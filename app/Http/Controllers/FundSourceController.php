<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fundsource;
use App\Models\Facility;
use App\Models\Proponent;
use App\Models\ProponentInfo;
use App\Models\Utilization;
use App\Models\Transfer;
use App\Models\Patients;
use App\Models\Admin_Cost;
use App\Models\Fundsource_Files;
use App\Models\Dv2;
use App\Models\NewDV;
use App\Models\PreDV;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FundSourceController extends Controller
{
    public function __construct()
    {
       $this->middleware('auth');
    }

    public function fundSource(Request $request) {
        
        $fundsources = Fundsource::              
            with([
                'proponents' => function ($query) {
                    $query->with([
                        'proponentInfo' => function ($query) {
                            $query->with('facility','main_pro:id,proponent');
                        }
                    ]);
                },
                'encoded_by' => function ($query) {
                    $query->select(
                        'userid',
                        'fname'
                    );
                }
            ]);
            
        $utilizations = Utilization::with(['fundSourcedata', 'proponentdata'])->first();
                  
        if($request->viewAll) {
            $request->keyword = '';
        }
        else if($request->keyword) {
            
            $check = $fundsources->where('saa', 'LIKE', "%$request->keyword%")->get();
            if($check->isEmpty()){
                //search through proponent but display only the proponent searched even if there are multiple proponent within the fundsource
                $fundsources = Fundsource::with([
                    'proponents' => function ($query) use ($request) {
                        $query->whereHas('proponentInfo', function ($subquery) use ($request) {
                            $subquery->where('proponent', 'LIKE', "%$request->keyword%");
                        });
                    },
                    'encoded_by' => function ($query) {
                        $query->select('userid', 'fname');
                    }
                ])
                ->orWhereHas('proponents', function ($query) use ($request) {
                    $query->whereHas('proponentInfo', function ($subquery) use ($request) {
                        $subquery->where('proponent', 'LIKE', "%$request->keyword%");
                    });
                });

                if($fundsources->get()->isEmpty()){
                    $f_list = Facility::where('name', 'LIKE', "%$request->keyword%")->pluck('id')->toArray();
                    $fundsources = Fundsource::with([
                        'proponents' => function ($query) use ($f_list) {
                            $query->whereHas('proponentInfo', function ($query) use ($f_list) {
                                $query->where(function ($query) use ($f_list) {
                                    $query->whereIn('facility_id', $f_list)
                                          ->orWhere(function ($query) use ($f_list) {
                                              foreach ($f_list as $value) {
                                                  $stringValue = (string) $value; // Convert $value to string
                                                  $query->orWhereJsonContains('facility_id', $stringValue);
                                              }
                                          });
                                });
                            })->with(['proponentInfo' => function ($query) use ($f_list) {
                                $query->where(function ($query) use ($f_list) {
                                    $query->whereIn('facility_id', $f_list)
                                          ->orWhere(function ($query) use ($f_list) {
                                              foreach ($f_list as $value) {
                                                  $stringValue = (string) $value; // Convert $value to string
                                                  $query->orWhereJsonContains('facility_id', $stringValue);
                                              }
                                          });
                                });
                            }]);
                        },
                        'encoded_by' => function ($query) {
                            $query->select(
                                'userid',
                                'fname'
                            );
                        }
                    ])
                    ->orWhereHas('proponents.proponentInfo', function ($query) use ($f_list) {
                        $query->where(function ($query) use ($f_list) {
                            $query->whereIn('facility_id', $f_list)
                                  ->orWhere(function ($query) use ($f_list) {
                                      foreach ($f_list as $value) {
                                          $stringValue = (string) $value; // Convert $value to string
                                          $query->orWhereJsonContains('facility_id', $stringValue);
                                      }
                                  });
                        });
                    });
                }
            }else{
                //search through fundsource
                $fundsources = $fundsources->where('saa', 'LIKE', "%$request->keyword%");
            }
        } 
        
        $fundsources = $fundsources->orderByRaw("CASE WHEN saa LIKE 'conap%' THEN 0 ELSE 1 END, saa ASC")->paginate(15);

        $user = DB::connection('dohdtr')->table('users')->leftJoin('dts.users', 'users.userid', '=', 'dts.users.username')
                ->where('users.userid', '=', Auth::user()->userid)
                ->select('users.section')
                ->first();
        return view('fundsource.fundsource',[
            'fundsources' => $fundsources,
            'keyword' => $request->keyword,
            'utilizations' => $utilizations,
            'facilities' => Facility::get(),
            'user' => $user
        ]);
    }

    public function fundSource2(Request $request) {
        $fundsources = Fundsource:: orderBy('id', 'asc')->paginate(15);
        return view('fundsource_budget.fundsource2',[
            'fundsources' => $fundsources
        ]);
    }

    public function getFundsource($type, $fundsource_id, Request $request){

        if($type == 'display'){
            return Fundsource::where('id', $fundsource_id)->first();
        }else if($type == 'save'){
            // return abs(0-3);
            $fundsource =  Fundsource::where('id', $fundsource_id)->first();
            
            if($fundsource){

                $fundsource->saa = $request->input('saa');
                $fundsource->alocated_funds = str_replace(',','',  $request->input('allocated_funds'));
                $fundsource->cost_value = $request->input('admin_cost');
                $admin_cost = (double)str_replace(',','',  $request->input('allocated_funds')) * ($request->input('admin_cost')/100);
                $fundsource->admin_cost = $admin_cost;
                $fundsource->remaining_balance = (double)str_replace(',','',  $request->input('allocated_funds')) -  $admin_cost;
                $fundsource->created_by = Auth::user()->userid;
                $fundsource->save();

                $funds_util = Utilization::where('fundsource_id', $fundsource->id)->where('status', 0)->where('obligated', 1)->get();
                if($funds_util){
                    foreach($funds_util as $item){
                        $amount = (double) str_replace(',', '', $fundsource->remaining_balance);
                        $util_balance = (double) str_replace(',', '', $item->budget_bbalance);
                        $util_utilize = (double) str_replace(',', '', $item->budget_utilize);
                        $fundsource->remaining_balance = $amount - $util_utilize;
                        $fundsource->save();
                    }
                }
                $info = ProponentInfo::where('fundsource_id', $fundsource->id)->get();
                foreach($info as $row){
                    $allocated = (double)str_replace(',','',  $row->alocated_funds);
                    $rem = (double)str_replace(',','',  $row->remaining_balance);
                    $r_cost = $allocated * ($request->input('admin_cost')/100);
                    $existing_cost = (double)str_replace(',','',  $row->admin_cost) + $rem;

                    if($allocated == $existing_cost){
                        $row->admin_cost = $r_cost;
                        $row->remaining_balance =  $allocated  - $r_cost;
                        $row->save();
                    }else{
                        $a_cost = (double)str_replace(',','',  $row->admin_cost);
                        // return $r_cost;
                        if($r_cost != $a_cost){
                            $rmng = $a_cost - $r_cost ;
                            $row->admin_cost = $r_cost;
                            $row->remaining_balance =  $rem  + $rmng;
                            $util = Utilization::where('proponentinfo_id', $row->id)->get();
                            foreach($util as $u){
                                $u->beginning_balance = (double)str_replace(',','',  $u->beginning_balance) + $rmng;
                                $u->save();
                            }
                            $row->save();
                        }
                    }
                }

                return redirect()->back()->with('fundsource_update', true);
            }
        }
    }


    public function createFundSource() {
    
        $user = Auth::user();
        $fundsources = Fundsource::get();
        $proponent = Proponent::select( DB::raw('MAX(id) as id'), DB::raw('MAX(proponent) as proponent'))
            ->groupBy('proponent_code')
            ->get();    
        
        return view('fundsource.create_fundsource',[
            'facilities' => Facility::where('hospital_type','private')->get(),
            'user' => $user,
            'fundsources' => $fundsources,
            'proponent' => $proponent
        ]);
    }

    public function fetchProponent($proponent_id){
        return Proponent::where('id', $proponent_id)->value('proponent_code');
    }

    public function createFundSourceSave(Request $request) {
        // $user = Auth::user();
        return $user;
        if(isset($request->saa_exist)) {
            $fundsource = Fundsource::find($request->saa_exist);
        } else {
            $fundsource = new Fundsource();
            $fundsource->saa = $request->saa;
            $fundsource->created_by = $user->userid;
            $fundsource->save();
        }
        $proponent = Proponent::where('fundsource_id', $fundsource->id)->where('proponent_code',  $request->proponent_code)->first();

        if($proponent){
            // return $proponent;
        }else{
            // return 1;
            $check = Proponent::where('proponent_code', $request->proponent_code)->first();
            $proponent = new Proponent();
            $proponent->fundsource_id = $fundsource->id;
            $proponent->pro_group= 0;
            $proponent->proponent = $request->proponent;
            $proponent->proponent_code = $request->proponent_code;
            $proponent->created_by = $user->userid;
            $proponent->save();
            if($check){
                $proponent->pro_group= $check->pro_group;
            }else{
                $proponent->pro_group= $proponent->id;
            }
            $proponent->save();
        }

        $index = 0;
        foreach ($request->facility_id as $facilityId) {
            $proponentInfo = new ProponentInfo();
            $proponentInfo->fundsource_id = $fundsource->id;
            $proponentInfo->proponent_id = $proponent->id;
            $proponentInfo->facility_id = $request->facility_id[$index];
            $proponentInfo->alocated_funds = $request->alocated_funds[$index];
            $proponentInfo->remaining_balance = $request->alocated_funds[$index];
            $proponentInfo->created_by = $user->userid;
            $proponentInfo->save();
            $index++;
        }

        session()->flash('fundsource_save', true);
        return redirect()->back();
    }

    public function Editfundsource(Request $request){

        $fundsources = Fundsource::where('id', $request->fundsourceId)              
            ->with([
                'proponents' => function ($query) use ($request) {
                    $query->where('id', '=', $request->proponent_id)->with([
                        'proponentInfo' => function ($query) {
                            $query->with('facility');
                        }
                    ]);
                },
                'encoded_by' => function ($query) {
                    $query->select('id', 'fname');
                }
            ])->first();
    
        $specificProponent = $fundsources->proponents->first();
        return view('fundsource.update_fundsource', [
            'fundsource' => $fundsources,
            'fundsources' => Fundsource::get(),
            'facility' => Facility::get(),
            'proponent_spec' => $specificProponent,
        ]);
    }

    public function createBDowns($fundsourceId){

        $fundsource = Fundsource::where('id', $fundsourceId)-> with([
            'proponents' => function ($query) {
                $query->with('proponentInfo');}
            ])->get();

        $randomBytes = random_bytes(16); 
        $proponents = Proponent::select( DB::raw('MAX(id) as id'), DB::raw('MAX(proponent) as proponent'),
                            DB::raw('MAX(proponent_code) as proponent_code'))
                        ->groupBy('proponent')
                        ->get(); 
                        // return $proponents;
        $proponent_info  =   ProponentInfo::where('fundsource_id', $fundsourceId)->get();             
        $sum = $proponent_info->sum(function ($info) {
                    return (float) str_replace(',', '', $info->alocated_funds);
                });
                // return $fundsource;
        return view('fundsource.breakdowns', [
            'fundsource' => $fundsource,
            'pro_count' => $proponent_info->count(),
            'facilities' => Facility::get(),
            'proponents' => $proponents,
            'uniqueCode' => bin2hex($randomBytes),
            'sum' => $sum,
            'util' => Utilization::where('fundsource_id', $fundsourceId)->where('status', 0)->get()
        ]);

    }

    public function removeInfo($infoId){
        if($infoId){
            $util = Utilization::where('proponentinfo_id', $infoId)->get();
            if(!$util){
                ProponentInfo::where('id', $infoId)->delete();
            }
        }
    }
    
    public function saveBDowns(Request $request){

        $breakdowns = $request->input('breakdowns');
        $fund_id = $request->input('fundsource_id');
        $get_fundsource = Fundsource::where('id', $fund_id)->first();
       
        if($breakdowns){
            foreach($breakdowns as $breakdown){
                // return $breakdown['proponent_main'];
                $pro_exists = Proponent::where('proponent', $breakdown['proponent'])->where('fundsource_id', $breakdown['fundsource_id'])->where('proponent_code', $breakdown['proponent_code'])->first();
                if(!$pro_exists){
                    $check = Proponent::where('proponent', $breakdown['proponent'])->where('proponent_code', $breakdown['proponent_code'])->first();
                    $proponent = new Proponent();
                    $proponent->fundsource_id = $breakdown['fundsource_id'];
                    $proponent->proponent = $breakdown['proponent'];
                    $proponent->proponent_code = $breakdown['proponent_code'];
                    $proponent->pro_group = 0;
                    $proponent->created_by = Auth::user()->userid;
                    $proponent->save();
                    if($check){
                        $proponent->pro_group = $check->id;
                    }else{
                        $proponent->pro_group = $proponent->id;
                    }
                    $proponent->save();
                    $proponentId = $proponent->id;
                }else{
                    $proponentId = $pro_exists->id;
                }
                // $info = ProponentInfo::where('proponent_id', $proponentId)->where('facility_id', $breakdown['facility_id'])->where('fundsource_id', $breakdown['fundsource_id'])->first();
                if( $breakdown['info_id'] !== "0" && $breakdown['info_id'] !== "undefined" ){
                    
                    $info = ProponentInfo::where('id',  $breakdown['info_id'])->first();

                    if(str_replace(',','',$breakdown['alocated_funds']) != str_replace(',','',$info->alocated_funds)){
                        $info->facility_id = json_encode($breakdown['facility_id']);
                        $info->main_proponent = json_encode($breakdown['proponent_main']);
                        $info->proponent_id = $proponentId;
                        $info->alocated_funds = $breakdown['alocated_funds'];
                        // if((double)str_replace(',','',$get_fundsource->alocated_funds) >= 1000000){
                        $info->admin_cost =number_format( (double)str_replace(',','',$breakdown['alocated_funds']) * ($get_fundsource->cost_value/100) , 2,'.', ',');

                        $rem =  (double)str_replace(',','',$breakdown['alocated_funds']) - (double)str_replace(',','', $info->admin_cost);
                        $info->remaining_balance = $rem;
                        $info->facility_funds = $rem;
                        $info->proponent_funds = $rem;

                        // }else{
                        //     $info->admin_cost = 0;
                        //     $info->remaining_balance = $breakdown['alocated_funds'];
                        // }
                            
                        $info->created_by = Auth::user()->userid;
                        $info->save();
                        $utilization = Utilization::where('proponentinfo_id', $info->id)->where('status', '!=', 1)->get();
                        $new_beginning = number_format((double)str_replace(',','',$info->remaining_balance), 2, '.', ',');

                        if($utilization){
                            foreach($utilization as $u){
                                if($u->status == 3){
                                    $u->beginning_balance = $new_beginning;
                                    $u->save();
                                    $new_beginning = number_format((double)str_replace(',','',$u->beginning_balance) + (double)str_replace(',','',$u->utilize_amount),2,'.',',');
                                    $info->alocated_funds = number_format((double)str_replace(',','',$info->alocated_funds) + (double)str_replace(',','',$u->utilize_amount),2,'.',',');
                                    $saa_am = (double)str_replace(',','',$get_fundsource->alocated_funds) + (double)str_replace(',','',$u->utilize_amount);
                                    $get_fundsource->alocated_funds = $saa_am;
                                    $get_fundsource->remaining_balance = $saa_am;
                                    $get_fundsource->save();
                                }else{
                                    if($u->status == 2){
                                        $info->alocated_funds = number_format((double)str_replace(',','',$info->alocated_funds) - (double)str_replace(',','',$u->utilize_amount),2,'.',',');
                                    }
                                    $u->beginning_balance = $new_beginning;
                                    $u->save();
                                    $new_beginning = number_format((double)str_replace(',','',$u->beginning_balance) - (double)str_replace(',','',$u->utilize_amount),2,'.',',');
                                }
                                
                            }
                            $info->remaining_balance = $new_beginning;
                            $info->save();
                        }
                    }
                }else{
                    $p_info = new ProponentInfo();
                    $p_info->main_proponent = $breakdown['proponent_main'];
                    $p_info->fundsource_id = $breakdown['fundsource_id'];
                    $p_info->proponent_id = $proponentId;
                    $p_info->facility_id = json_encode($breakdown['facility_id']);
                    $p_info->alocated_funds = $breakdown['alocated_funds'];
                    // if((double)str_replace(',','',$get_fundsource->alocated_funds) >= 1000000){
                    $p_info->admin_cost =number_format((double)str_replace(',','',$breakdown['alocated_funds']) * ($get_fundsource->cost_value/100) , 2,'.', ',');

                    $rem = (double)str_replace(',','',$breakdown['alocated_funds']) - (double)str_replace(',','',$p_info->admin_cost);
                    $p_info->remaining_balance = $rem;
                    $p_info->facility_funds = $rem;
                    $p_info->proponent_funds = $rem;
                    // }else{
                    //     $p_info->admin_cost = 0;
                    //     $p_info->remaining_balance = $breakdown['alocated_funds'];
                    // }
                    $p_info->created_by = Auth::user()->userid;
                    $p_info->save();
                }
            }
        }
    }

    public function updatefundsource(Request $request) {
    
        $fundsourceData = $request->input('fundsource');
    
        foreach ($fundsourceData as $fundsourceId => $fundsources){
        $fundsourceModel = Fundsource::find($fundsourceId);

        if($fundsourceModel){
            $fundsourceModel->update([
                'saa' => $fundsources['saa_exist'],
            ]);
        }
        }
        $proponentsData = $request->input('proponents');

        foreach ($proponentsData as $proponentId => $proponent) {
            $proponentModel = Proponent::find($proponentId);

            if ($proponentModel) {
                $proponentModel->update([
                    'proponent' => $proponent['proponent'],
                    'proponent_code' => $proponent['proponent_code'],
                ]);
            }
        }
        
        $proponentInfoData = $request->input('proponentInfo');
        $keys = array_keys($proponentInfoData);
        foreach ($proponentInfoData as $proponentInfoId => $proponentInfo) {
            $proponentInfoModel = ProponentInfo::find($proponentInfoId);
        
            if ($proponentInfoModel) {
                $proponentInfoModel->update([
                    'facility_id' => $proponentInfo['facility_id'], 
                    'alocated_funds' => $proponentInfo['alocated_funds'], 
                ]);
            }
        }
        $get_pInfo = ProponentInfo::find(end($keys));
        $facility_id = $request->input('facility_id');
        $amount = $request->input('alocated_funds');
        if($facility_id !==null){
            for($i=0;$i<count($facility_id); $i++){
                $new_pInfo = new ProponentInfo();
                $new_pInfo->fundsource_id = $get_pInfo->fundsource_id;
                $new_pInfo->proponent_id = $get_pInfo->proponent_id;
                $new_pInfo->facility_id = $facility_id[$i];
                $new_pInfo->alocated_funds = $amount[$i];
                $new_pInfo->remaining_balance = $amount[$i];
                $new_pInfo->created_by= Auth::user()->userid;
                $new_pInfo->save();
            }
        }
              
        session()->flash('fundsource_update', true);
        return redirect()->back();
    }
    
    public function proponentGet(Request $request) {
        return Proponent::where('fundsource_id',$request->fundsource_id)->get();
    }

    public function transferFunds($info_id){
        if($info_id){
            $from_info = ProponentInfo::with(['proponent', 'fundsource', 'facility'])
                    ->where('id', $info_id)
                    ->first();
            $all_pro = Proponent::where('pro_group', $from_info->proponent->pro_group)->pluck('id')->toArray();
            $to_info = ProponentInfo::with(['proponent', 'fundsource', 'facility'])
                ->whereIn('proponent_id', $all_pro)
                ->get();
        }
        $fundsources = Fundsource::get();
        return view('fundsource.transfer_funds', [
            'from_info' => $from_info,
            'to_info' => $to_info,
            'fundsources' => $fundsources,
            'facilities' => Facility::get()
        ]);
    }

    public function saveTransferFunds(Request $request){
        // return $request->input('to_saa');
        $identifier = $request->input('fac');
        $from_id = $request->input('from_info');
        $to_id = $request->input('to_info');

        $from = ProponentInfo::where('id', $from_id)->first();

        if($identifier == "new_fac"){
            $fund = Fundsource::where('id', $request->input('to_saa'))->first();
            $proponent = Proponent::where('id', $request->input('to_proponent'))->first();
            $pro_exists = Proponent::where('fundsource_id', $request->input('to_saa'))->first();
            if($pro_exists == null || $pro_exists == ""){
                $new_pro = new Proponent();
                $new_pro->fundsource_id = $request->input('to_saa');
                $new_pro->proponent = $proponent->proponent;
                $new_pro->proponent_code = $proponent->proponent_code;
                $new_pro->created_by = Auth::user()->userid;
                $new_pro->pro_group = $proponent->id;
                $new_pro->save();

                $proponent_id = $new_pro->id;
            }else{
                $proponent_id = $pro_exists->id;

            }
            $to = new ProponentInfo();
            $to->alocated_funds = $request->input('to_amount');
            $to->remaining_balance = $request->input('to_amount');
            $to->proponent_id = $proponent_id;
            $to->fundsource_id = $request->input('to_saa');
            $to->facility_id = $request->input('to_info');
            $to->remaining_balance = $request->input('to_amount');
            $to->admin_cost = "";
            // if($fund->cost_value == 0){
            //     $to->admin_cost = 0;
            //     $to->remaining_balance = $request->input('to_amount');
            // }else{
            //     $to->admin_cost = (double) str_replace(',','', $request->input('to_amount')) * $fund->cost_value / 100;
            //     $to->remaining_balance = (double) str_replace(',','', $request->input('to_amount')) - ((double) str_replace(',','', $request->input('to_amount')) * $fund->cost_value / 100);
            // }
            $to->created_by = Auth::user()->userid;
            $to->save();

        }else{

            if($from_id != null && $to_id != null){
                $to = ProponentInfo::where('id', $to_id)->first();
            }
        }

        $transfer = new Transfer();
        $transfer->from_proponent = $from->proponent_id;
        $transfer->from_saa = $from->fundsource_id;
        $transfer->from_facility = $from->facility_id;
        $transfer->from_amount = $request->input('to_amount');
        $transfer->to_proponent = $to->proponent_id;
        $transfer->to_saa = $to->fundsource_id;
        $transfer->to_facility = $to->facility_id;
        $transfer->to_amount = $request->input('to_amount');

        $transfer->from_beginning_balance = $from->remaining_balance;
        $from_balance = (double) str_replace(',', '', $from->remaining_balance) - (double) str_replace(',', '', $request->input('to_amount'));
        $transfer->from_balance_after = $from_balance;

        $transfer->to_beginning_balance = $to->remaining_balance;
        $to_balance = (double) str_replace(',', '', $to->remaining_balance) + (double) str_replace(',', '', $request->input('to_amount'));
        $transfer->to_balance_after = $to_balance;

        $transfer->remarks = $request->input('transfer_remarks');
        $transfer->status = 0;
        $transfer->transfer_by = Auth::user()->userid;
        $transfer->save();    

        if($from){
                
            $from_utilize = new Utilization();
            $from_utilize->fundsource_id = $from->fundsource_id;
            $from_utilize->proponentinfo_id = $from->id;
            $from_utilize->proponent_id = $from->proponent_id;
            $from_utilize->div_id = 0;
            $from_utilize->utilize_amount = $request->input('to_amount');
            $from_utilize->beginning_balance = $from->remaining_balance;
            $from_utilize->created_by= Auth::user()->userid;
            $from_utilize->facility_id = $from->facility_id;
            $from_utilize->status = 2;
            $from_utilize->transfer_id = $transfer->id;
            $from_utilize->save();

            $from->alocated_funds = str_replace(',', '',$from->alocated_funds) - str_replace(',', '',$request->input('to_amount'));
            $from->remaining_balance = str_replace(',', '',$from->remaining_balance) - str_replace(',', '',$request->input('to_amount'));
            $from->save();

            $fundsource = Fundsource ::where('id', $from->fundsource_id)->first();
            // $fundsource->admin_cost = ($fundsource->cost_value == 0)?0:(double) str_replace(',','',$fundsource->remaining_balance) 
            //     - str_replace(',', '',$request->input('to_amount')) * $fundsource->cost_value / 100;
            $fundsource->alocated_funds = (double) str_replace(',','',$fundsource->alocated_funds) - str_replace(',', '',$request->input('to_amount'));
            $fundsource->remaining_balance = (double) str_replace(',','',$fundsource->remaining_balance) - str_replace(',', '',$request->input('to_amount'));
            $fundsource->save();
        }
        
        if($to){

            $to_utilize = new Utilization();
            $to_utilize->fundsource_id = $to->fundsource_id;
            $to_utilize->proponentinfo_id = $to->id;
            $to_utilize->proponent_id = $to->proponent_id;
            $to_utilize->div_id = 0;
            $to_utilize->utilize_amount = $request->input('to_amount');
            $to_utilize->beginning_balance = $to->remaining_balance;
            $to_utilize->created_by= Auth::user()->userid;
            $to_utilize->facility_id = $to->facility_id;
            $to_utilize->status = 3;
            $to_utilize->transfer_id = $transfer->id;
            $to_utilize->save();

            $to->alocated_funds = str_replace(',', '',$to->alocated_funds) + str_replace(',', '',$request->input('to_amount')); 
            $to->remaining_balance = str_replace(',', '',$to->remaining_balance) + str_replace(',', '',$request->input('to_amount')); 
            $to->save();

            $to_fundsource = Fundsource ::where('id', $to->fundsource_id)->first();
            // $to_fundsource->admin_cost = ($to_fundsource->cost_value == 0)?0:(double) str_replace(',','',$to_fundsource->remaining_balance) 
            //     - str_replace(',', '',$request->input('to_amount'))* $to_fundsource->cost_value / 100;
            $to_fundsource->alocated_funds = (double) str_replace(',','',$to_fundsource->alocated_funds) + str_replace(',', '',$request->input('to_amount'));
            $to_fundsource->remaining_balance = (double) str_replace(',','',$to_fundsource->remaining_balance) + str_replace(',', '',$request->input('to_amount'));
            $to_fundsource->save();
        }
        session()->flash('fund_transfer', true);
        return redirect()->back();
        
    }

    public function facilityProponentGet($facility_id) {
        // $ids = ProponentInfo::where('facility_id', $facility_id)
        //         ->orWhereJsonContains('facility_id', $facility_id)
        //         ->pluck('proponent_id')->toArray();

        $ids = ProponentInfo::where(function ($query) use ($facility_id) {
                        $query->whereJsonContains('proponent_info.facility_id', '702')
                            ->orWhereJsonContains('proponent_info.facility_id', [$facility_id]);
                    })
                    ->orWhereIn('proponent_info.facility_id', [$facility_id, '702'])
                    ->pluck('proponent_id')->toArray();

        $proponents = Proponent::select( DB::raw('MAX(proponent) as proponent'), DB::raw('MAX(id) as id'))
            ->groupBy('proponent_code') ->whereIn('id', $ids)
            ->whereNull('status')
            ->get();
        return $proponents;
    }

    public function getAcronym($str) {
        $words = explode(' ', $str); 
        $acronym = '';
        
        foreach ($words as $word) {
            $acronym .= strtoupper(substr($word, 0, 1)); 
        }
        
        return $acronym;
    }

    public function forPatientCode($proponent_id, $facility_id) {
        $user = Auth::user();
        $proponent= Proponent::where('id', $proponent_id)->first();
        $proponent_ids= Proponent::where('proponent', $proponent->proponent)->pluck('id')->toArray();
        $facility = Facility::find($facility_id);
        $patient_code = $proponent->proponent_code.'-'.$this->getAcronym($facility->name).date('YmdHi').$user->id;
        $proponent_info = ProponentInfo::where(function ($query) use ($facility_id, $proponent_ids) {
                                $query->where(function ($subquery) use ($facility_id) {
                                    $subquery->whereJsonContains('proponent_info.facility_id', '702')
                                            ->orWhereJsonContains('proponent_info.facility_id', [$facility_id]);
                                })
                                ->orWhereIn('proponent_info.facility_id', [$facility_id, '702']);
                            })
                            ->whereIn('proponent_id', $proponent_ids)
                            ->with('fundsource')
                            ->get();
        $sum = $proponent_info->sum(function ($info) {
                    return (float) str_replace(',', '', $info->remaining_balance);
                });                                

        return [
            'patient_code' => $patient_code,
            'proponent_info' => $proponent_info,
            'balance' => $sum,
        ];
    }

    public function forPatientFacilityCode($fundsource_id) {

        $proponentInfo = ProponentInfo::where('fundsource_id', $fundsource_id)->first();
        
        if($proponentInfo){
            $facility = Facility::find($proponentInfo->facility_id);

            $proponent = Proponent::find($proponentInfo->proponent_id);
            $proponentName = $proponent ? $proponent->proponent : null;
            return response()->json([

                'proponent' => $proponentName,
                'proponent_id' => $proponentInfo? $proponentInfo->proponent_id : null,
                'facility' => $facility ? $facility->name : null,
                'facility_id' => $proponentInfo ? $proponentInfo->facility_id : null,
            ]);
        }else{
            return response()->json(['error' => 'Proponent Info not found'], 404);
        }
    }

    public function facilityGet(Request $request){
        return Facility::where('id', $request->facilityId)->get();
    }

    public function transactionGet() {
        $randomBytes = random_bytes(16); 
        $uniqueCode = bin2hex($randomBytes);
        $facilities = Facility::where('hospital_type','private')->get();
        return view('fundsource.transaction',[
            'facilities' => $facilities,
            'uniqueCode' => $uniqueCode
        ]);
    }
    public function facilitiesGet($type) {
        $randomBytes = random_bytes(16); 
        if($type== 'fac'){
            return view('fundsource.facilities_select',[
                'facilities' => Facility::get(),
                'uniqueCode' => bin2hex($randomBytes)
            ]);
        }else{
            $proponents = Proponent::select( DB::raw('MAX(id) as id'), DB::raw('MAX(proponent) as proponent'),
                            DB::raw('MAX(proponent_code) as proponent_code'))
                        ->groupBy('proponent_code')
                        ->get(); 
            return view('fundsource.clone_prodiv',[
                'facilities' => Facility::get(),
                'proponents' => $proponents,
                'uniqueCode' => bin2hex($randomBytes)
            ]);
        }
        
    }
    
    public function fundSourceGet() {
        $result = Fundsource::with([
            'proponents' => function ($query) {
                $query->with([
                    'proponentInfo' => function ($query) {
                        $query->with('facility');
                    }
                ]);
            }
        ])->get();
        return $result;
    }

    public function adminCost(Request $req){

        $fundsources = Fundsource::with('encoded_by','cost_usage');

        if($req->viewAll){
            $req->keyword = '';
         }else if($req->keyword){
             $fundsources->where('saa', 'LIKE', "%$req->keyword%");
         }
        return view('admin_cost', [
            'fundsources' => $fundsources->orderBy('id', 'desc')->paginate(50),
            'keyword' => $req->keyword
        ]);

    }

    public function costBalance($fundsource_id){

        $usage = Admin_Cost::where('fundsource_id', $fundsource_id)->orderBy('id', 'desc')->first();
        if($usage){
            return number_format($usage->balance, 2,'.',',');
        }else{

            $fund = Fundsource::where('id', $fundsource_id)->first();
            return number_format($fund->admin_cost, 2,'.',',');
        }

    }

    public function addUsage(Request $req){
        $usage = new Admin_Cost();

        $not_new = Admin_Cost::where('fundsource_id', $req->input('saa'))->orderBy('id', 'desc')->first();
        if($not_new){
            $cost = $not_new->balance;
        }else{
            $new = Fundsource::where('id', $req->input('saa'))->first();
            $cost = $new->admin_cost;
        }
        $usage->admin_cost = $cost;
        $usage->fundsource_id = $req->input('saa');
        $usage->deductions = str_replace(',','',$req->input('deductions'));
        $usage->event = $req->input('event');
        $usage->balance = str_replace(',','', $cost) - str_replace(',','', $req->input('deductions'));
        $usage->remarks = $req->input('remarks');
        $usage->created_by = Auth::user()->userid;
        $usage->save();
        
        return redirect()->back()->with('add_deductions', true);
    }

    public function fileUpload(Request $req){
        $files = Fundsource_Files::orderBy('id', 'desc');
        if($req->viewAll) {
            $req->keyword = '';
        }
        else if($req->keyword) {
            $files = $files->where('saa_no', 'LIKE', "%$req->keyword%");
        }
        $files = $files->paginate(50);
        return view('fundsource.upload_file',[
            'files'=>$files,
            'keyword'=>$req->keyword
            ]);
    }

    public function uploadFiles(Request $req){
        
        $req->validate([
            'files.*' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2408',
        ]);
        foreach($req->file('files') as $upload){
            $filename = $upload->getClientOriginalName();
            $path = $upload->storeAs('uploads',$filename);
            $imagePath = storage_path('app/' . $path);

            $exif = exif_read_data($imagePath);
            $orientation = isset($exif['Orientation']) ? $exif['Orientation'] : null;
            $img = imagecreatefromstring(file_get_contents($imagePath));

            switch ($orientation) {
                case 3:
                    $img = imagerotate($img, 180, 0);
                    break;
                case 6:
                    $img = imagerotate($img, -90, 0);
                    break;
                case 8:
                    $img = imagerotate($img, 90, 0);
                    break;
            }

            imagejpeg($img, $imagePath);
            imagedestroy($img);

            $ffiles = new Fundsource_Files();
            $ffiles->saa_no = pathinfo($filename, PATHINFO_FILENAME);
            $ffiles->path = $path;
            $ffiles->uploaded_by = Auth::user()->userid;
            $ffiles->save();
        }
        return redirect()->back()->with('upload_files', true);
    }

    public function removeImage($id){
        Fundsource_Files::where('id', $id)->delete();
    }

    public function fetchFundsource($fundsource_id){
        if($fundsource_id == "all"){

            $proponents = Proponent::all()->groupBy('pro_group');
            $firstEntries = $proponents->map(function ($group) {
                return $group->first();
            });
            return $firstEntries;

        }else{
            return Proponent::where('fundsource_id', $fundsource_id)->get();
        }
    }
    
    public function fetchInfo($fundsource_id,$proponent_id){
        return ProponentInfo::where('fundsource_id', $fundsource_id)->where('proponent_id', $proponent_id)->get();
    }

    public function fetchFacility($facility_id){
        if($facility_id == "others"){
            return Facility::get();
        }else{
            return Facility::where('id', $facility_id)->first();
        }
    }

    public function generateExcel(){
        $proponents = Proponent::select( DB::raw('MAX(id) as id'), DB::raw('MAX(proponent) as proponent'), 
                        DB::raw('MAX(proponent_code) as proponent_code'))
                        ->groupBy('proponent_code')
                        ->orderBy('id', 'desc')
                        ->get();
        $title = "List of Proponent";
        $filename = $title.'.xls';
        header("Content-Type: application/xls");
        header("Content-Disposition: attachment; filename=$filename");
        header("Pragma: no-cache");
        header("Expires: 0");
        $table_body = "<tr>
                <th>Proponent</th>
                <th>Proponent Code</th>
            </tr>";
            // return $utilization;
        foreach($proponents as $row){
            $table_body .= "<tr>
                <td style='vertical-align:top;'>$row->proponent</td>
                <td style='vertical-align:top;'>$row->proponent_code</td>
            </tr>";
        }
        $display =
            '<h1>'.$title.'</h1>'.
            '<table cellspacing="1" cellpadding="5" border="1">'.$table_body.'</table>';

        return $display;
    }

    public function version2($route_no){

        $dv2 = Dv2::where('route_no', $route_no)->get();
        $new_dv = NewDV::where('route_no', $route_no)->first();

        if(count($dv2) != 0){
            $dv2 =  Dv2::where('route_no', $route_no)->leftJoin('patients as p1', 'dv2.lname', '=', 'p1.id')
                    ->leftJoin('patients as p2', 'dv2.lname2', '=', 'p2.id')
                    ->select('dv2.*', 'p1.lname as lname1', 'p2.lname as lname_2')
                    ->get();
                
            $total = Dv2::where('route_no', $route_no)
                    ->select(DB::raw('SUM(REPLACE(amount, ",", "")) as totalAmount'))
                    ->first()->totalAmount;   

            return view('maif.dv2', [
                'dv2'=> $dv2,
                'total' => $total
            ]);
        }else if($new_dv){

            $pre_dv = PreDV::where('id', $new_dv->predv_id)->with(
                [
                    'facility:id,name',
                    'new_dv',
                    'extension' => function ($query) {
                        $query->with(
                            [
                                'proponent:id,proponent',
                                'controls',
                                'saas' => function ($query) {
                                    $query->with([
                                        'saa:id,saa'
                                    ]);
                                }
                            ]
                        );
                    }
                ]
            )->first();
        
            return view('maif.predv', [
                'result' => $pre_dv
            ]);
        }else{
            return "No disbursement version 2 found on this dv!";
        }
    }
}
