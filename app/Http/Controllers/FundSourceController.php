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
use App\Models\Dv;
use App\Models\NewDV;
use App\Models\PreDV;
use App\Models\Dv3;
use App\Models\Dv3Fundsource;
use App\Models\ProponentUtilizationV1;
use App\Models\SupplementalFunds;
use App\Models\SubtractedFunds;
use App\Models\IncludedFacility;
use App\Models\UsageTransfer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class FundSourceController extends Controller
{
    public function __construct()
    {
       $this->middleware('auth');
       $this->middleware('block.secure.nonadmin');
    }

    public function fundSource(Request $request) {

        if(Auth::user()->userid == "2760"){
            $fundsources = Fundsource::with('proponentInfo')->get();
            $result = $fundsources->filter(function ($fundsource) {
                $totalProponentFunds = $fundsource->proponentInfo->sum(function ($info) {
                    return floatval(str_replace(',', '', $info->alocated_funds));
                });
                $fundsourceAllocated = floatval(str_replace(',', '', $fundsource->alocated_funds));
                $totalProponentFunds = round($totalProponentFunds, 2);
                $fundsourceAllocated = round($fundsourceAllocated, 2);
                return $totalProponentFunds > $fundsourceAllocated;
            })->pluck('id');
            // return $result;
        }
        
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
                $fundsources = $fundsources->where('saa', 'LIKE', "%$request->keyword%");
            }
        }  
        $fundsources = $fundsources->get();  
        $fundsources = $fundsources->map(function ($fund) {
            $totalRemaining = $fund->proponents->sum(function ($proponent) {
                return $proponent->proponentInfo->sum(function ($info) {
                    return (float) str_replace(',', '', $info->remaining_balance);
                });
            });
            $fund->total_remaining = $totalRemaining;
            return $fund;
        });
        
        $fundsources = $fundsources->sortBy(function ($fund) {
            $isConap = Str::contains($fund->saa, 'CONAP');
            $hasPositive = $fund->total_remaining > 0;
        
            if ($isConap && $hasPositive) {
                $group = 0; // CONAP positive
            } elseif (!$isConap && $hasPositive) {
                $group = 1; // Non-CONAP positive
            } elseif ($isConap && !$hasPositive) {
                $group = 2; // CONAP 0 or less
            } else {
                $group = 3; // Non-CONAP 0 or less
            }
        
            return [$group, -$fund->total_remaining];
        })->values();
        $page = request()->get('page', 1);
        $perPage = 15;
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $fundsources->forPage($page, $perPage),
            $fundsources->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
        
        $fundsources = $paginated;
        
        $user = DB::connection('dohdtr')->table('users')->leftJoin('dts.users', 'users.userid', '=', 'dts.users.username')
                ->where('users.userid', '=', Auth::user()->userid)
                ->select('users.section')
                ->first();

        $proponents = Proponent::select( DB::raw('MAX(id) as id'), DB::raw('MAX(proponent) as proponent'),
            DB::raw('MAX(proponent_code) as proponent_code'))
            ->groupBy('proponent')
            ->get(); 

        return view('fundsource.fundsource',[
            'fundsources' => $fundsources,
            'keyword' => $request->keyword,
            'utilizations' => $utilizations,
            'proponents' => $proponents,
            'facilities' => Facility::get(),
            'user' => $user,
            'funds_list' => Fundsource::select('id', 'saa')->get(),
            'transferred' => Utilization::where('status', 3)->whereHas('transfer', function ($q) {
                $q->where('owed', 1);
            })->get()
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
            $fundsource =  Fundsource::where('id', $fundsource_id)->first();
            
            if($fundsource){

                $funds_balance = str_replace(',','', $fundsource->alocated_funds);
                $infos_balance = ProponentInfo::where('fundsource_id', $fundsource_id)
                    ->get()
                    ->sum(function ($info) {
                        return floatval(str_replace(',', '', $info->alocated_funds));
                    });
            
                if($infos_balance > str_replace(',','',  $request->input('allocated_funds'))){
                    return redirect()->back()->with('invalid_update', true);
                }

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

    // public function createBDowns($fundsourceId){

    //     $fundsource = Fundsource::where('id', $fundsourceId)-> with([
    //         'proponents' => function ($query) {
    //             $query->with('proponentInfo');}
    //         ])->get();

    //     $randomBytes = random_bytes(16); 
    //     $proponents = Proponent::select( DB::raw('MAX(id) as id'), DB::raw('MAX(proponent) as proponent'),
    //             DB::raw('MAX(proponent_code) as proponent_code'))
    //         ->groupBy('proponent')
    //         ->get(); 
    //     $proponent_info  =   ProponentInfo::where('fundsource_id', $fundsourceId)->get();             
    //     $sum = $proponent_info->sum(function ($info) {
    //                 return (float) str_replace(',', '', $info->alocated_funds);
    //             });
                 
    //     return view('fundsource.breakdowns', [
    //         'fundsource' => $fundsource,
    //         'pro_count' => $proponent_info->count(),
    //         'facilities' => Facility::get(),
    //         'proponents' => $proponents,
    //         'uniqueCode' => bin2hex($randomBytes),
    //         'sum' => $sum,
    //         'util' => Utilization::where('fundsource_id', $fundsourceId)->where('status', 0)->get()
    //     ]);

    // }

    public function createBDowns($fundsourceId){
        $fundsource = Fundsource::where('id', $fundsourceId)->get();
    
        $randomBytes = random_bytes(16); 
        $proponents = Proponent::select( 
                DB::raw('MAX(id) as id'), 
                DB::raw('MAX(proponent) as proponent'),
                DB::raw('MAX(proponent_code) as proponent_code')
            )
            ->groupBy('proponent')
            ->get(); 
        
        $pro_count = Fundsource::find($fundsourceId)->proponents()->count();
        $proponent_info = ProponentInfo::where('fundsource_id', $fundsourceId)->get();             
        $sum = $proponent_info->sum(function ($info) {
            return (float) str_replace(',', '', $info->alocated_funds);
        });
        
        return view('fundsource.breakdowns', [
            'fundsource' => $fundsource,
            'pro_count' => $pro_count,
            'facilities' => Facility::get(),
            'proponents' => $proponents,
            'uniqueCode' => bin2hex($randomBytes),
            'sum' => $sum,
            'util' => Utilization::where('fundsource_id', $fundsourceId)->where('status', 0)->get(),
            'fundsourceId' => $fundsourceId
        ]);
    }
    
    public function getProponentInfoBatch(Request $request, $fundsourceId) {
        $page = $request->get('page', 1);
        $perPage = 20; 
        
        $proponents = Fundsource::find($fundsourceId)
            ->proponents()
            ->with(['proponentInfo' => function($query) {
                $query->select('id', 'proponent_id', 'facility_id', 'alocated_funds', 'remaining_balance', 'main_proponent', 'fundsource_id');
            }])
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();
        
        $totalProponents = Fundsource::find($fundsourceId)
            ->proponents()
            ->count();
        
        $loaded = min($page * $perPage, $totalProponents);
        $hasMore = $totalProponents > ($page * $perPage);
        
        return response()->json([
            'proponents' => $proponents,
            'hasMore' => $hasMore,
            'page' => $page,
            'total' => $totalProponents,
            'loaded' => $loaded
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
                        $info->main_proponent = (int) $breakdown['proponent_main'];
                        $info->proponent_id = $proponentId;
                        $info->alocated_funds = $breakdown['alocated_funds'];
                        // if((double)str_replace(',','',$get_fundsource->alocated_funds) >= 1000000){
                        $info->admin_cost =number_format( (double)str_replace(',','',$breakdown['alocated_funds']) * ($get_fundsource->cost_value/100) , 2,'.', ',');
                        $rem =  (double)str_replace(',','',$breakdown['alocated_funds']) - (double)str_replace(',','', $info->admin_cost);
                        $info->remaining_balance = $rem;
                        $info->facility_funds = $rem;
                        $info->proponent_funds = $rem;
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
                    $p_info->admin_cost =number_format((double)str_replace(',','',$breakdown['alocated_funds']) * ($get_fundsource->cost_value/100) , 2,'.', ',');
                    $rem = (double)str_replace(',','',$breakdown['alocated_funds']) - (double)str_replace(',','',$p_info->admin_cost);
                    $p_info->remaining_balance = $rem;
                    $p_info->facility_funds = $rem;
                    $p_info->proponent_funds = $rem;
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
        $identifier = $request->input('fac');
        $from_id = $request->input('from_info');
        $to_id = $request->input('to_info');
        $sb_from = $request->input('sb_from');
        $sb_to = $request->input('to_source');

        $from = ProponentInfo::where('id', $from_id)->first();

        if($identifier == "new_fac"){
            $fund = Fundsource::where('id', $request->input('to_saa'))->first();
            $proponent = Proponent::where('id', $request->input('to_proponent'))->first();
            // $pro_exists = Proponent::where('fundsource_id', $request->input('to_saa'))->first();
            $pro_exists = Proponent::where('fundsource_id', $request->input('to_saa'))->where('proponent', $proponent->proponent)->first();

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
            $to->created_by = Auth::user()->userid;
            $to->save();
        }else{
            if($from_id != null && $to_id != null){
                $to = ProponentInfo::where('id', $to_id)->first();
            }
        }

        $transfer = new Transfer();
        $transfer->owed = $request->input('owed') == "ON" || $request->input('owed') == "on" ? 1 : 0;
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
            $from_utilize->transfer_type = $sb_from == 1 ? 1 : ($sb_from == 2 ? 2 : ($sb_from == 3 ? 3 : ''));
            $from_utilize->transfer_to = $sb_to == 1 ? 1 : ($sb_to == 2 ? 2 : ($sb_to == 3 ? 3 : ''));
            $from_utilize->save();

            $fundsource = Fundsource ::where('id', $from->fundsource_id)->first();

            if($sb_from == 1){ // allocated funds deduction
                $fundsource->alocated_funds = (double) str_replace(',','',$fundsource->alocated_funds) - str_replace(',', '',$request->input('to_amount'));
                $fundsource->remaining_balance = (double) str_replace(',','',$fundsource->remaining_balance) - str_replace(',', '',$request->input('to_amount'));
                $from->alocated_funds = str_replace(',', '',$from->alocated_funds) - str_replace(',', '',$request->input('to_amount'));
                $from->remaining_balance = str_replace(',', '',$from->remaining_balance) - str_replace(',', '',$request->input('to_amount'));
                
            }else if($sb_from == 2){ // remaining balance deduction
                $fundsource->remaining_balance = (double) str_replace(',','',$fundsource->remaining_balance) - str_replace(',', '',$request->input('to_amount'));
                $from->remaining_balance = str_replace(',', '',$from->remaining_balance) - str_replace(',', '',$request->input('to_amount'));

            }else if($sb_from == 3){ //admin cost deduction
                $from->admin_cost = str_replace(',', '',$from->admin_cost) - str_replace(',', '',$request->input('to_amount'));
                $fundsource->admin_cost = (double) str_replace(',','',$fundsource->admin_cost) - str_replace(',', '',$request->input('to_amount'));
            }
           
            $from->save();
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
            $to_utilize->transfer_type = $sb_from == 1 ? 1 : ($sb_from == 2 ? 2 : ($sb_from == 3 ? 3 : ''));
            $to_utilize->transfer_to = $sb_to == 1 ? 1 : ($sb_to == 2 ? 2 : ($sb_to == 3 ? 3 : ''));
            $to_utilize->save();

            $to_fundsource = Fundsource ::where('id', $to->fundsource_id)->first();

            if($sb_to == 1){ // allocated funds deduction
                $to_fundsource->alocated_funds = (double) str_replace(',','',$to_fundsource->alocated_funds) + str_replace(',', '',$request->input('to_amount'));
                $to_fundsource->remaining_balance = (double) str_replace(',','',$to_fundsource->remaining_balance) + str_replace(',', '',$request->input('to_amount'));
                $to->alocated_funds = str_replace(',', '',$to->alocated_funds) + str_replace(',', '',$request->input('to_amount')); 
                $to->remaining_balance = str_replace(',', '',$to->remaining_balance) + str_replace(',', '',$request->input('to_amount')); 
                
            }else if($sb_to == 2){ // remaining balance deduction
                $to_fundsource->remaining_balance = (double) str_replace(',','',$to_fundsource->remaining_balance) + str_replace(',', '',$request->input('to_amount'));
                $to->remaining_balance = str_replace(',', '',$to->remaining_balance) + str_replace(',', '',$request->input('to_amount')); 

            }else if($sb_to == 3){ //admin cost deduction
                $to->admin_cost = str_replace(',', '',$to->admin_cost) + str_replace(',', '',$request->input('to_amount')); 
                $to_fundsource->admin_cost = (double) str_replace(',','',$to_fundsource->admin_cost) + str_replace(',', '',$request->input('to_amount'));
            }
            $to->save();
            $to_fundsource->save();
        }
        session()->flash('fund_transfer', true);
        return redirect()->back();
    }

    public function facilityProponentGet($facility_id) {

        $ids = ProponentInfo::where(function ($query) use ($facility_id) {
                $query->whereJsonContains('proponent_info.facility_id', '702')
                    ->orWhereJsonContains('proponent_info.facility_id', [$facility_id]);
            })
            ->orWhereIn('proponent_info.facility_id', [$facility_id, '702'])
            ->pluck('proponent_id')->toArray();

        $proponents = Proponent::select( DB::raw('MAX(proponent) as proponent'), DB::raw('MAX(id) as id'))
            ->groupBy('proponent') ->whereIn('id', $ids)
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

    public function forPatientCode1($proponent_id, $facility_id) {
        $included_ids = IncludedFacility::pluck('facility_id')->toArray();
       

        $user = Auth::user();
        $proponent= Proponent::where('id', $proponent_id)->first();
        $proponent_ids= Proponent::where('proponent', $proponent->proponent)->pluck('id')->toArray();
        $sl = ProponentInfo::whereIn('proponent_id', $proponent_ids)->where('facility_id', 'LIKE', "%851%")->get();
        if(count($sl) != 0) {
            $included_ids[] = 775; // add 775 if 851 exists
        }
        // $info_sum = ProponentInfo::whereIn('proponent_id', $proponent_ids)
        //     ->selectRaw('SUM(CAST(REPLACE(alocated_funds, ",", "") AS DECIMAL(10,2)) - CAST(REPLACE(admin_cost, ",", "") AS DECIMAL(10,2))) as total_amount')
        //     ->value('total_amount');
        $saaa = ProponentInfo::whereIn('proponent_id', $proponent_ids)
        ->where(function ($query) use ($included_ids) {
            foreach ($included_ids as $id) {
                $query->orWhere('facility_id', $id) // Match facility_id as integer
                    ->orWhereJsonContains('facility_id', (string) $id); // Match facility_id in JSON array
            }
        })->get();
        $info_sum = ProponentInfo::whereIn('proponent_id', $proponent_ids)
            ->where(function ($query) use ($included_ids) {
                foreach ($included_ids as $id) {
                    $query->orWhere('facility_id', $id) // Match facility_id as integer
                        ->orWhereJsonContains('facility_id', (string) $id); // Match facility_id in JSON array
                }
            })
            ->selectRaw('SUM(CAST(REPLACE(alocated_funds, ",", "") AS DECIMAL(10,2)) - CAST(REPLACE(admin_cost, ",", "") AS DECIMAL(10,2))) as total_amount')
            ->value('total_amount');
        $pat_sum = Patients::whereIn('proponent_id', $proponent_ids)
            // ->where(function ($query) {
            //     $query->where('expired', '!=', 1)
            //         ->orWhereNull('expired')
            //         ->orWhere('expired', 0); // Include NULL values
            // })
            ->sum(DB::raw("
                IFNULL(actual_amount, CAST(REPLACE(guaranteed_amount, ',', '') AS DECIMAL(20, 2)))
            ")); 
        $supplemental = SupplementalFunds::where('proponent',  $proponent->proponent)
            ->sum(DB::raw("
                CAST(REPLACE(IFNULL(amount, '0'), ',', '') AS DECIMAL(20, 2))
            "));

        $subtracted = DB::table('subtracted_funds')
            ->where('proponent',  $proponent->proponent)
            ->sum(DB::raw("
                CAST(REPLACE(IFNULL(amount, '0'), ',', '') AS DECIMAL(20, 2))
            "));   
        $info = ProponentInfo::whereIn('proponent_id', $proponent_ids)->pluck('id')->toArray();

        $info = ProponentInfo::whereIn('proponent_id', $proponent_ids)
            ->where(function ($query) use ($included_ids) {
                $query->whereIn('facility_id', $included_ids);
                foreach ($included_ids as $id) {
                    $query->orWhereJsonContains('facility_id', (string) $id);
                }
            })
            ->where(function ($query) {
                $query->where('facility_id', '!=', 702);
                $query->whereNot(function ($sub) {
                    $sub->whereJsonContains('facility_id', '702');
                });
            })
            ->pluck('id')
            ->toArray();

        $dv3_sum = Dv3Fundsource::whereIn('info_id', $info)
        ->with([
            'dv3' => function ($query){
                $query->with([
                    'facility:id,name',
                    'user:userid,fname,lname'
                ]);
            },
            'fundsource:id,saa'
        ])->sum('amount');

        $dv3_sumsd = Dv3Fundsource::whereIn('info_id', $info)
        // ->where('facility_id')
        ->with([
            'dv3' => function ($query){
                $query->with([
                    'facility:id,name',
                    'user:userid,fname,lname'
                ]);
            },
            'fundsource:id,saa'
        ])->sum('amount');

        // $dv3_sum = Dv3Fundsource::whereIn('info_id', $info)
        //     ->with([
        //         'dv3' => function ($query){
        //             $query->with([
        //                 'facility:id,name',
        //                 'user:userid,fname,lname'
        //             ]);
        //         },
        //         'fundsource:id,saa'
        //     ])->sum('amount');

        $dv1 = Utilization::whereIn('proponent_id', $proponent_ids)
            ->where('status', 0)
            ->where('facility_id', 837)
            ->where(function ($query) {
                $query->whereHas('dv', function ($query) {
                    $query->whereColumn('div_id', 'route_no');
                })->orWhereHas('newDv', function ($query) {
                    $query->whereColumn('div_id', 'route_no');
                });
            })
            ->with([
                'fundSourcedata:id,saa',
                'user:userid,fname,lname',
            ])
            ->orderBy('id', 'desc')
            ->get();

        $dv_sum = $dv1->sum(function ($item) {
            return floatval(str_replace(',', '', $item->utilize_amount));
        });
        
        if (count($sl) != 0) {
            $balance = ($info_sum + $supplemental) - ($subtracted + $dv3_sum + $dv_sum + $pat_sum) ;
            $sampp = $dv3_sum;
        }else{
            $balance = ($info_sum + $supplemental) - ($subtracted + $pat_sum + $dv_sum) ;
            $sampp = 2;
        }

      
        // $balance = ($info_sum + $supplemental) - ($subtracted + $pat_sum) ;
        $timestamp = str_replace('.', '', microtime(true));
        $random = mt_rand(100, 999);

        $facility = Facility::find($facility_id);
        $patient_code = $proponent->proponent_code.'-'.$this->getAcronym($facility->name).$timestamp.$random.$user->id;
        $total = ProponentInfo::whereIn('proponent_id', $proponent_ids)
                ->sum(DB::raw('CAST(REPLACE(alocated_funds, ",", "") AS DECIMAL(10,2)) - CAST(REPLACE(admin_cost, ",", "") AS DECIMAL(10,2))'));

        return [
            'patient_code' => $patient_code,
            'balance' => round($balance, 2),
            'total_funds' => $info_sum,
            'supplemental' => $supplemental,
            'subtracted' => $subtracted,
            'disbursement' => count($sl) != 0 ? $dv3_sum + $dv_sum : $dv_sum ,
            // 'disbursement' => $dv_sum,
            'gl_sum' => round($pat_sum, 2),
            'dv3'=> $dv3_sum
        ];
    }

    private function overallBalance1($proponent_id){
        $list = Proponent::where('id', $proponent_id)->pluck('proponent')->toArray();

        $proponentGroups = Proponent::when($list, function ($query) use ($list) {
                return $query->whereIn('proponent', $list);
            })
            ->select('id', 'proponent')
            ->orderBy('proponent')
            ->get()
            ->groupBy('proponent');

        if ($proponentGroups->isEmpty()) {
            return view('maif.pro_fundsource', [
                'data' => [],
                'keyword' => $keyword,
                'facilities' => Facility::select('id', 'name')->get(),
                'user' => Auth::user()->user_type
            ]);
        }

        $allProponentIds = $proponentGroups->map(function ($group) {
            return $group->pluck('id')->toArray();
        });

        $fundsData = ProponentInfo::whereIn('proponent_id', $allProponentIds->flatten()->toArray())
            ->selectRaw('
                proponent_id,
                SUM(CAST(NULLIF(REPLACE(COALESCE(alocated_funds, "0"), ",", ""), "") AS DECIMAL(20,2))) as total_funds,
                SUM(CAST(NULLIF(REPLACE(COALESCE(admin_cost, "0"), ",", ""), "") AS DECIMAL(20,2))) as admin_cost
            ')
            ->groupBy('proponent_id')
            ->get()
            ->groupBy('proponent_id');

        $utilizationData = Patients::whereIn('proponent_id', $allProponentIds->flatten()->toArray())
            ->selectRaw('
                proponent_id,
                SUM(
                    CASE 
                        WHEN actual_amount IS NOT NULL AND actual_amount != "" 
                        THEN CAST(REPLACE(actual_amount, ",", "") AS DECIMAL(20, 2))
                        ELSE CAST(REPLACE(COALESCE(guaranteed_amount, "0"), ",", "") AS DECIMAL(20, 2))
                    END
                ) as total_utilized
            ')
            ->groupBy('proponent_id')
            ->get()
            ->groupBy('proponent_id');

        $supplementalFunds = SupplementalFunds::whereIn('proponent', $proponentGroups->keys())
            ->selectRaw('
                proponent,
                SUM(CAST(NULLIF(REPLACE(COALESCE(amount, "0"), ",", ""), "") AS DECIMAL(20,2))) as total_amount
            ')
            ->groupBy('proponent')
            ->get()
            ->keyBy('proponent');

        $subtractedFunds = DB::table('subtracted_funds')
            ->whereIn('proponent', $proponentGroups->keys())
            ->selectRaw('
                proponent,
                SUM(CAST(NULLIF(REPLACE(COALESCE(amount, "0"), ",", ""), "") AS DECIMAL(20,2))) as total_amount
            ')
            ->groupBy('proponent')
            ->get()
            ->keyBy('proponent');

        $dv1Data = Utilization::whereIn('proponent_id', $allProponentIds->flatten()->toArray())
            ->where('status', 0)
            ->where('facility_id', 837)
            ->where(function ($query) {
                $query->whereHas('dv', function ($q) {
                    $q->whereColumn('div_id', 'route_no');
                })->orWhereHas('newDv', function ($q) {
                    $q->whereColumn('div_id', 'route_no');
                });
            })
            ->selectRaw('
                proponent_id,
                SUM(CAST(NULLIF(REPLACE(COALESCE(utilize_amount, "0"), ",", ""), "") AS DECIMAL(20,2))) as total_amount
            ')
            ->groupBy('proponent_id')
            ->get()
            ->groupBy('proponent_id');

        $dv3Data= Utilization::whereIn('proponent_id', $allProponentIds->flatten()->toArray())
            ->where('status', 0)
            ->where(function ($query) {
                $query->whereHas('dv3', function ($q) {
                    $q->whereColumn('div_id', 'route_no');
                });
            })
            ->selectRaw('
                proponent_id,
                SUM(CAST(NULLIF(REPLACE(COALESCE(utilize_amount, "0"), ",", ""), "") AS DECIMAL(20,2))) as total_amount
            ')
            ->groupBy('proponent_id')
            ->get()
            ->groupBy('proponent_id');

        $allData = $proponentGroups->map(function ($proponentGroup, $proponentName) use (
            $fundsData,
            $utilizationData,
            $supplementalFunds,
            $subtractedFunds,
            $dv1Data,
            $dv3Data
        ) {
            $proponentIds = $proponentGroup->pluck('id');
            
            $totalFunds = 0;
            $totalAdminCost = 0;
            $totalUtilized = 0;
            $totalDv1Amount = 0;
            $totalDv3Amount = 0;

            foreach ($proponentIds as $id) {
                if ($fundsData->has($id)) {
                    $fundInfo = $fundsData->get($id)->first();
                    $totalFunds += $fundInfo->total_funds ?? 0;
                    $totalAdminCost += $fundInfo->admin_cost ?? 0;
                }

                if ($utilizationData->has($id)) {
                    $totalUtilized += $utilizationData->get($id)->sum('total_utilized');
                }

                if ($dv1Data->has($id)) {
                    $totalDv1Amount += $dv1Data->get($id)->sum('total_amount');
                }
                if ($dv3Data->has($id)) {
                    $totalDv3Amount += $dv3Data->get($id)->sum('total_amount');
                }
            }

            $supp = $supplementalFunds->get($proponentName)?->total_amount ?? 0;
            $sub = $subtractedFunds->get($proponentName)?->total_amount ?? 0;

            $netFunds = $totalFunds - $totalAdminCost;
            $remaining = $netFunds - $totalUtilized;
            $finalRemaining = $remaining + $supp - ($totalDv1Amount + $sub);

            return [
                'proponent' => $proponentGroup->first(),
                'sum' => round($netFunds, 2),
                'rem' => round($finalRemaining - $totalDv3Amount , 2),
                'supp' => round($supp, 2),
                'sub' => round($sub, 2),
                'disbursement' => round($totalDv1Amount + $totalDv3Amount, 2),
                'allocated_cost' => round($totalAdminCost, 2),
                'totalUtilized' => round($totalUtilized, 2),
                'admin_cost' => round($totalAdminCost, 2),
            ];
        });

        return $allData;
    }

    public function forPatientCode($proponent_id, $facility_id) {

        

        // $overall = $this->overallBalance1($proponent_id);
        // $included = $this->forPatientCode1($proponent_id, $facility_id);
        // $firstKey = $overall->keys()->first();
        // $sub = $overall[$firstKey]['sub'];
        // $supp = $overall[$firstKey]['supp'];
        // $first_bal = $overall[$firstKey]['rem'];
        // $sec_bal = $included['balance'];
        // $patient_code = $included['patient_code'];

        // $included_ids = IncludedFacility::pluck('facility_id')->toArray();
        // $user = Auth::user();
        // $proponent= Proponent::where('id', $proponent_id)->first();
        // $proponent_ids= Proponent::where('proponent', $proponent->proponent)->pluck('id')->toArray();

        // $main_balance = 0;
        // $excess_balance = 0;
        // $used_info = [];
        // $main_facility = [];
        // $rem_facility = [];

        // //for selected facility

        // $selected_patients = Patients::whereIn('proponent_id', $proponent_ids)->where('facility_id', $facility_id)->get();
        // $selected_total = $selected_patients->sum(function ($patient) {
        //     $amount = $patient->actual_amount ?? $patient->guaranteed_amount;
        //     return floatval(str_replace(',', '', $amount));
        // });
        
        // $selected_info = ProponentInfo::whereIn('proponent_id', $proponent_ids)
        //     ->where(function($query) use ($facility_id) {
        //         $query->where('facility_id', $facility_id)
        //             ->orWhereJsonContains('facility_id', (string) $facility_id);
        //     })
        //     ->get();

        // $selected_info_amount = $selected_info->sum(function($item) {
        //     $amount = floatval(str_replace(',', '', $item->alocated_funds ?? 0)) 
        //                 - floatval(str_replace(',', '', $item->admin_cost ?? 0));
        //     return floatval($amount);
        // });

        // $facility_allocated = $selected_info->sum(function($item) {
        //     $amount = floatval(str_replace(',', '', $item->alocated_funds ?? 0));
        //     return floatval($amount);
        // });

        // $facility_admin = $selected_info->sum(function($item) {
        //     $amount = floatval(str_replace(',', '', $item->admin_cost ?? 0));
        //     return floatval($amount);
        // });

        // $selected_util_sum = Utilization::whereIn('proponentinfo_id', $selected_info->pluck('id')->toArray())
        //     ->where('status', 0)
        //     ->where(function ($query) {
        //         $query->whereHas('dv', function ($q) {
        //             $q->whereColumn('div_id', 'route_no')
        //             ->where('facility_id', 837);
        //         })->orWhereHas('newDv', function ($q) {
        //             $q->whereColumn('div_id', 'route_no')
        //             ->where('facility_id', 837);
        //         })->orWhereHas('dv3', function ($q) {
        //             $q->whereColumn('div_id', 'route_no');
        //         });
        //     })
        //     // ->get();
        //     ->sum(DB::raw('CAST(NULLIF(REPLACE(COALESCE(utilize_amount, "0"), ",", ""), "") AS DECIMAL(20,2))'));
        //     $check_all = 0 ;
        // $overall_selected_usage = $selected_util_sum + $selected_total;
        // $cut = 0;

        // $rem_all_fac = [];

        // if($overall_selected_usage > $selected_info_amount){
        //     $excess_balance = $excess_balance + ($overall_selected_usage - $selected_info_amount);
        // }else{
        //     $main_balance = $selected_info_amount - $overall_selected_usage;
        //     $cut =  $selected_info_amount - $overall_selected_usage;
        //     if($selected_info){
        //         $selected_facility = array_unique(array_map(fn($id) => is_array(json_decode($id, true)) ? json_decode($id, true)[0] : $id, $selected_info->pluck('facility_id')->toArray()));
        //         $rem_all_fac[] = [
        //             "amount" => $main_balance,
        //             "facilities" => Facility::whereIn("id", $selected_facility)->pluck("name")->toArray()
        //         ];
        //         $main_facility[] =  [
        //             "amount" => $main_balance,
        //             "facilities" => Facility::whereIn("id", $selected_facility)->pluck("name")->toArray()
        //         ];
        //     }else{
        //         $main_facility[] =  [
        //             "amount" => 0,
        //             "facilities" => ["No Allocation"]
        //         ];
        //     }
        // }

        // $used_info[] = $selected_info->pluck('id')->toArray();
        // $used_info = Arr::flatten($used_info);
        // $check_all=    $check_all + $overall_selected_usage;
        
        // //other facility same proponent

        // $other_facility = Patients::whereNotIn('id', $selected_patients->pluck('id')->toArray())
        //     ->whereIn('proponent_id', $proponent_ids)
        //     ->select('facility_id')
        //     ->selectRaw("
        //         SUM(
        //             IFNULL(
        //                 CAST(REPLACE(actual_amount, ',', '') AS DECIMAL(20, 2)),
        //                 CAST(REPLACE(guaranteed_amount, ',', '') AS DECIMAL(20, 2))
        //             )
        //         ) AS total_amount
        //     ")
        //     ->groupBy('facility_id')
        //     ->get();

        // foreach($other_facility as $other){
            
        //     $used_info = Arr::flatten($used_info);

        //     $other_id = $other->facility_id;
        //     $patient_amount = $other->total_amount;

        //     $other_info = ProponentInfo::whereIn('proponent_id', $proponent_ids)
        //         ->whereNotIn('id', $used_info)
        //         ->where(function($query) use ($other_id) {
        //             $query->where('facility_id', $other_id)
        //                 ->orWhereJsonContains('facility_id', (string) $other_id);
        //         })
        //         ->get();

        //     $other_amount = $other_info->sum(function($item) {
        //         $amount = floatval(str_replace(',', '', $item->alocated_funds ?? 0)) 
        //                 - floatval(str_replace(',', '', $item->admin_cost ?? 0));
        //         return floatval($amount);
        //     });

        //     $other_util_sum = Utilization::whereIn('proponentinfo_id', $other_info->pluck('id')->toArray())
        //         ->whereNotIn('proponentinfo_id', $used_info)
        //         ->where('status', 0)
        //         ->where(function ($query) {
        //             $query->whereHas('dv', function ($q) {
        //                 $q->whereColumn('div_id', 'route_no')
        //                 ->where('facility_id', 837);
        //             })->orWhereHas('newDv', function ($q) {
        //                 $q->whereColumn('div_id', 'route_no')
        //                 ->where('facility_id', 837);
        //             })->orWhereHas('dv3', function ($q) {
        //                 $q->whereColumn('div_id', 'route_no');
        //             });
        //         })
        //         ->sum(DB::raw('CAST(NULLIF(REPLACE(COALESCE(utilize_amount, "0"), ",", ""), "") AS DECIMAL(20,2))'));

        //     $overall_other_usage = $patient_amount +  $other_util_sum;
        //     $check_all=$check_all + $overall_other_usage;

        //     if($overall_other_usage > $other_amount){
        //         $excess_balance = $excess_balance + $overall_other_usage - $other_amount;
        //     }else{
        //         $cut = $cut + $other_amount - $overall_other_usage;
        //         if($other_info){
        //             $other_facility = array_unique(array_map(fn($id) => is_array(json_decode($id, true)) ? json_decode($id, true)[0] : $id, $other_info->pluck('facility_id')->toArray()));
        //             $rem_all_fac[] = [
        //                 "amount" => $other_amount - $overall_other_usage,
        //                 "facilities" => Facility::whereIn("id", $other_facility)->pluck("name")->toArray()
        //             ];
        //         }
        //     }
        //     $used_info[] = $other_info->pluck('id')->toArray();
        // }
        
        // //cvchd
        // $used_info = Arr::flatten($used_info);
        // $cvchd_id = 702;
        // $cvchd_info = ProponentInfo::whereIn('proponent_id', $proponent_ids)
        //     ->whereNotIn('id', $used_info)
        //     ->where(function($query) use ($cvchd_id) {
        //         $query->where('facility_id', $cvchd_id)
        //             ->orWhereJsonContains('facility_id', (string) $cvchd_id);
        //     })
        //     ->get();

        // $cvchd_amount = $cvchd_info->sum(function ($item) {
        //     $amount = floatval(str_replace(',', '', $item->alocated_funds ?? 0)) 
        //                 - floatval(str_replace(',', '', $item->admin_cost ?? 0));
        //     return floatval($amount);
        // });

        // $cvchd_funds = $cvchd_info->sum(function ($item) {
        //     $amount = floatval(str_replace(',', '', $item->alocated_funds ?? 0));
        //     return floatval($amount);
        // });

        // $cvchd_admin = $cvchd_info->sum(function ($item) {
        //     $amount = floatval(str_replace(',', '', $item->admin_cost ?? 0));
        //     return floatval($amount);
        // });
        
        // $cvchd_util_sum = Utilization::whereIn('proponentinfo_id', $cvchd_info->pluck('id')->toArray())
        //     ->whereNotIn('proponentinfo_id', $used_info)
        //     ->where('status', 0)
        //     ->where(function ($query) {
        //         $query->whereHas('dv', function ($q) {
        //             $q->whereColumn('div_id', 'route_no')
        //             ->where('facility_id', 837);
        //         })->orWhereHas('newDv', function ($q) {
        //             $q->whereColumn('div_id', 'route_no')
        //             ->where('facility_id', 837);
        //         })->orWhereHas('dv3', function ($q) {
        //             $q->whereColumn('div_id', 'route_no');
        //         });
        //     })
        //     ->sum(DB::raw('CAST(NULLIF(REPLACE(COALESCE(utilize_amount, "0"), ",", ""), "") AS DECIMAL(20,2))'));
        
        // $check_all=$check_all + $cvchd_util_sum;

        // $rem_balance = $cvchd_amount - $excess_balance;

        // $used_info[] = $cvchd_info->pluck('id')->toArray();

        // //excluded
        // $used_info = Arr::flatten($used_info);

        // $excluded_info = ProponentInfo::whereIn('proponent_id', $proponent_ids)->whereNotIn('id', $used_info)->get();
        // $excluded_amount = $excluded_info->sum(function ($item) {
        //     $amount = floatval(str_replace(',', '', $item->alocated_funds ?? 0)) 
        //                 - floatval(str_replace(',', '', $item->admin_cost ?? 0));
        //     return floatval($amount);
        // });

        // $excluded_util = Utilization::whereIn('proponentinfo_id', $excluded_info->pluck('id'))
        //     ->whereNotIn('proponentinfo_id', $used_info)
        //     ->where('status', 0)
        //     ->where(function ($query) {

        //         $query->whereHas('dv', function ($q) {
        //             $q->whereColumn('div_id', 'route_no')
        //             ->where('facility_id', 837);
        //         })

        //         ->orWhereHas('newDv', function ($q) {
        //             $q->whereColumn('div_id', 'route_no')
        //             ->where('facility_id', 837);
        //         })

        //         ->orWhereHas('dv3', function ($q) {
        //             $q->whereColumn('div_id', 'route_no');
        //         });

        //     })
        //     ->select(
        //         'proponentinfo_id',
        //         DB::raw('SUM(CAST(NULLIF(REPLACE(COALESCE(utilize_amount, "0"), ",", ""), "") AS DECIMAL(20,2))) as total_util')
        //     )
        //     ->groupBy('proponentinfo_id')
        //     ->pluck('total_util', 'proponentinfo_id');

        // foreach ($excluded_info as $excluded) {
        //     $ex_util_sum = $excluded_util[$excluded->id] ?? 0;

        //     $ex_amount = floatval(str_replace(',', '', $excluded->alocated_funds ?? 0)) 
        //             - floatval(str_replace(',', '', $excluded->admin_cost ?? 0));

        //     if ($ex_amount > $ex_util_sum) {
        //         $fac_ids = json_decode($excluded->facility_id, true);
        //         $fac_ids = is_array($fac_ids) ? $fac_ids : [$excluded->facility_id];
        //         $facilities = Facility::whereIn('id', $fac_ids)->pluck('name')->toArray();

        //         $rem_all_fac[] = [
        //             "amount" => $ex_amount - $ex_util_sum,
        //             "facilities" => $facilities
        //         ];
        //     }
        // }

        // $excluded_util_sum = Utilization::whereIn('proponentinfo_id', $excluded_info->pluck('id')->toArray())
        //     ->whereNotIn('proponentinfo_id', $used_info)
        //     ->where('status', 0)
        //     ->where(function ($query) {
        //         $query->whereHas('dv', function ($q) {
        //             $q->whereColumn('div_id', 'route_no')
        //                 ->where('facility_id', 837);
        //         })->orWhereHas('newDv', function ($q) {
        //             $q->whereColumn('div_id', 'route_no')
        //             ->where('facility_id', 837);
        //         })->orWhereHas('dv3', function ($q) {
        //             $q->whereColumn('div_id', 'route_no');
        //         })
        //         ;
        //     })
        //     ->sum(DB::raw('CAST(NULLIF(REPLACE(COALESCE(utilize_amount, "0"), ",", ""), "") AS DECIMAL(20,2))'));

        // $check_all = $check_all+$excluded_util_sum;

        // $excluded_excess = 0;
        // if($excluded_util_sum > $excluded_amount){
        //     $excluded_excess = $excluded_util_sum - $excluded_amount;
        // }
         
        // $cvchd_overall_balance = ($cvchd_amount + $supp) - ($sub + $excluded_excess + $excess_balance + $cvchd_util_sum);

        // if($cvchd_overall_balance > 0){
        //     $rem_all_fac[] = [
        //             "amount" => $cvchd_overall_balance,
        //             "facilities" => Facility::whereIn("id", [702])->pluck("name")->toArray()
        //         ];
        // }

        // $used_info[] = $excluded_info->pluck('id')->toArray();
        // $used_info = Arr::flatten($used_info);
        // $fourth_rem = 0;
        // $check_rem = 0;
        // if($cvchd_overall_balance <=0 ){
        //     $fourth_rem = $main_balance;
        // }else{
        //     $fourth_rem = $cvchd_overall_balance + $main_balance;
        //     $check_rem = 1;
        // }
        // // return $main_balance;
        // // return $included;
        // return [
        //     'cvchd' => $cvchd_amount,
        //     'excess' => $excess_balance,
        //     'cvchd_util' => $cvchd_util_sum,
        //     'cut' => $cut,
        //     'excluded' => $excluded_amount,
        //     'excluded_util' => $excluded_util_sum,
        //     'first_rem' => $first_bal,
        //     'second_rem' => $sec_bal,
        //     'third_rem' => ($cvchd_amount - ($excess_balance + $cvchd_util_sum + $sub)) + $supp + $main_balance,
        //     'rem_all_fac' => $rem_all_fac,
        //     'fourth_rem' => $fourth_rem,
        //     'selected_info' => $selected_info,
        //     'selected_fund' => $facility_allocated,
        //     'selected_cost' => $facility_admin,
        //     'selected_gl' => $selected_total,
        //     'selected_dv' => $selected_util_sum,
        //     'check_rem' => $check_rem,
        //     'main_balance' => $main_balance,
        //     'cvchd_funds' => $cvchd_funds,  
        //     'cvchd_admin' => $cvchd_admin,
        //     'cvchd_usage' => $excluded_excess + $excess_balance + $cvchd_util_sum,
        //     'cvchd_overall_balance' => $cvchd_overall_balance,
        //     'supp' => $supp,
        //     'sub' => $sub,
        //     'patient_code' => $patient_code
        // ];

        $overall = $this->overallBalance1($proponent_id);
        $included = $this->forPatientCode1($proponent_id, $facility_id);
        $firstKey = $overall->keys()->first();
        $sub = $overall[$firstKey]['sub'];
        $supp = $overall[$firstKey]['supp'];
        $first_bal = $overall[$firstKey]['rem'];
        $sec_bal = $included['balance'];
        $patient_code = $included['patient_code'];

        $included_ids = IncludedFacility::pluck('facility_id')->toArray();
        $user = Auth::user();
        $proponent= Proponent::where('id', $proponent_id)->first();
        $proponent_ids= Proponent::where('proponent', $proponent->proponent)->pluck('id')->toArray();

        $usage_ids = [];
        $usage_to_ids = [];

        $usage_transfer = UsageTransfer::whereJsonContains('facility_id', (string)$facility_id)->where('proponent', $proponent->proponent)->get();
        $usage_amount = $usage_transfer->sum('amount');
        $usage_ids = array_merge($usage_ids, $usage_transfer->pluck('id')->toArray());

        $usage_to_transfer = UsageTransfer::whereJsonContains('to_fid', (string)$facility_id)->where('proponent', $proponent->proponent)->get();
        $usage_to_amount = $usage_to_transfer->sum('amount');
        $usage_to_ids = array_merge($usage_to_ids, $usage_to_transfer->pluck('id')->toArray());

        $main_balance = 0;
        $excess_balance = 0;
        $used_info = [];
        $main_facility = [];
        $rem_facility = [];

        //for selected facility

        $selected_patients = Patients::whereIn('proponent_id', $proponent_ids)->where('facility_id', $facility_id)->get();
        $selected_total = $selected_patients->sum(function ($patient) {
            $amount = $patient->actual_amount ?? $patient->guaranteed_amount;
            return floatval(str_replace(',', '', $amount));
        });

        $usage_amount = max(0, min($selected_total, $usage_amount));
        
        $selected_total = $selected_total + $usage_to_amount - $usage_amount;
        
        $selected_info = ProponentInfo::whereIn('proponent_id', $proponent_ids)
            ->where(function($query) use ($facility_id) {
                $query->where('facility_id', $facility_id)
                    ->orWhereJsonContains('facility_id', (string) $facility_id);
            })
            ->get();

        $selected_info_amount = $selected_info->sum(function($item) {
            $amount = floatval(str_replace(',', '', $item->alocated_funds ?? 0)) 
                        - floatval(str_replace(',', '', $item->admin_cost ?? 0));
            return floatval($amount);
        });

        $facility_allocated = $selected_info->sum(function($item) {
            $amount = floatval(str_replace(',', '', $item->alocated_funds ?? 0));
            return floatval($amount);
        });

        $facility_admin = $selected_info->sum(function($item) {
            $amount = floatval(str_replace(',', '', $item->admin_cost ?? 0));
            return floatval($amount);
        });

        $selected_util_sum = Utilization::whereIn('proponentinfo_id', $selected_info->pluck('id')->toArray())
            ->where('status', 0)
            ->where(function ($query) {
                $query->whereHas('dv', function ($q) {
                    $q->whereColumn('div_id', 'route_no')
                    ->where('facility_id', 837);
                })->orWhereHas('newDv', function ($q) {
                    $q->whereColumn('div_id', 'route_no')
                    ->where('facility_id', 837);
                })->orWhereHas('dv3', function ($q) {
                    $q->whereColumn('div_id', 'route_no');
                });
            })
            // ->get();
            ->sum(DB::raw('CAST(NULLIF(REPLACE(COALESCE(utilize_amount, "0"), ",", ""), "") AS DECIMAL(20,2))'));
        $check_all = 0 ;
        $overall_selected_usage = $selected_util_sum + $selected_total;
        $cut = 0;
        
        $rem_all_fac = [];

        if($overall_selected_usage > $selected_info_amount){
            $excess_balance = $excess_balance + ($overall_selected_usage - $selected_info_amount);
        }else{

            $main_balance = $selected_info_amount - $overall_selected_usage;
            $cut =  $selected_info_amount - $overall_selected_usage;
            if($selected_info){

                $selected_facility = array_unique(array_map(fn($id) => is_array(json_decode($id, true)) ? json_decode($id, true)[0] : $id, $selected_info->pluck('facility_id')->toArray()));
                $rem_all_fac[] = [
                    "amount" => $main_balance,
                    "facilities" => Facility::whereIn("id", $selected_facility)->pluck("name")->toArray()
                ];
                $main_facility[] =  [
                    "amount" => $main_balance,
                    "facilities" => Facility::whereIn("id", $selected_facility)->pluck("name")->toArray()
                ];
            }else{
    
                $main_facility[] =  [
                    "amount" => 0,
                    "facilities" => ["No Allocation"]
                ];
            }
        }

        $used_info[] = $selected_info->pluck('id')->toArray();
        $used_info = Arr::flatten($used_info);
        $check_all=    $check_all + $overall_selected_usage;
        
        //other facility same proponent

        $other_facility = Patients::whereNotIn('id', $selected_patients->pluck('id')->toArray())
            ->whereIn('proponent_id', $proponent_ids)
            ->select('facility_id')
            ->selectRaw("
                SUM(
                    IFNULL(
                        CAST(REPLACE(actual_amount, ',', '') AS DECIMAL(20, 2)),
                        CAST(REPLACE(guaranteed_amount, ',', '') AS DECIMAL(20, 2))
                    )
                ) AS total_amount
            ")
            ->groupBy('facility_id')
            ->get();
        
        $additional_cvchd_usage = 0;
        foreach($other_facility as $other){
            
            $used_info = Arr::flatten($used_info);

            $other_id = $other->facility_id;
            $patient_amount = $other->total_amount;

            $other_info = ProponentInfo::whereIn('proponent_id', $proponent_ids)
                ->whereNotIn('id', $used_info)
                ->where(function($query) use ($other_id) {
                    $query->where('facility_id', $other_id)
                        ->orWhereJsonContains('facility_id', (string) $other_id);
                })
                ->get();

            $other_fc = $other_info->pluck('facility_id')
                ->flatMap(function ($id) {
                    // If it's a JSON string like ["239"], decode it
                    if (is_string($id)) {
                        $decoded = json_decode($id, true);
                        return is_array($decoded) ? $decoded : [$id];
                    }
            
                    return is_array($id) ? $id : [$id];
                })
                ->map(fn ($id) => (string) $id)
                ->unique()
                ->values()
                ->toArray();
            
            $other_usage = 0;
            $other_to_usage = 0;

            if($other_fc){
                $usage_transfer = UsageTransfer::whereJsonContains('facility_id', $other_fc)->where('proponent', $proponent->proponent)->whereNotIn('id', $usage_ids)->get();
                $other_usage = $usage_transfer->sum('amount');
                
                $usage_ids = array_merge($usage_ids, $usage_transfer->pluck('id')->toArray());

                $usage_to_transfer = UsageTransfer::whereJsonContains('to_fid', $other_fc)->where('proponent', $proponent->proponent)->whereNotIn('id', $usage_to_ids)->get();
                $other_to_usage = $usage_to_transfer->sum('amount');

                $usage_to_ids = array_merge($usage_to_ids, $usage_to_transfer->pluck('id')->toArray());
            }
                

            $other_amount = $other_info->sum(function($item) {
                $amount = floatval(str_replace(',', '', $item->alocated_funds ?? 0)) 
                        - floatval(str_replace(',', '', $item->admin_cost ?? 0));
                return floatval($amount);
            });

            $other_util_sum = Utilization::whereIn('proponentinfo_id', $other_info->pluck('id')->toArray())
                ->whereNotIn('proponentinfo_id', $used_info)
                ->where('status', 0)
                ->where(function ($query) {
                    $query->whereHas('dv', function ($q) {
                        $q->whereColumn('div_id', 'route_no')
                        ->where('facility_id', 837);
                    })->orWhereHas('newDv', function ($q) {
                        $q->whereColumn('div_id', 'route_no')
                        ->where('facility_id', 837);
                    })->orWhereHas('dv3', function ($q) {
                        $q->whereColumn('div_id', 'route_no');
                    });
                })
                ->sum(DB::raw('CAST(NULLIF(REPLACE(COALESCE(utilize_amount, "0"), ",", ""), "") AS DECIMAL(20,2))'));


            if($other_fc){
                $overall_other_usage = $patient_amount +  $other_util_sum - $other_usage + $other_to_usage;
                // $additional_cvchd_usage = $additional_cvchd_usage + $other_usage;
                $additional_cvchd_usage = $additional_cvchd_usage;
            }else{
                $overall_other_usage = $patient_amount +  $other_util_sum;
            }

            $check_all = $check_all + $overall_other_usage;

            if($overall_other_usage > $other_amount){
                $excess_balance = $excess_balance + $overall_other_usage - $other_amount;
            }else{
                $cut = $cut + $other_amount - $overall_other_usage;
                if($other_info){
                    $other_facility = array_unique(array_map(fn($id) => is_array(json_decode($id, true)) ? json_decode($id, true)[0] : $id, $other_info->pluck('facility_id')->toArray()));
                    $rem_all_fac[] = [
                        "amount" => $other_amount - $overall_other_usage,
                        "facilities" => Facility::whereIn("id", $other_facility)->pluck("name")->toArray()
                    ];
                }
            }

            $used_info[] = $other_info->pluck('id')->toArray();
        }
        
        //cvchd
        $used_info = Arr::flatten($used_info);
        $cvchd_id = 702;
        $cvchd_info = ProponentInfo::whereIn('proponent_id', $proponent_ids)
            ->whereNotIn('id', $used_info)
            ->where(function($query) use ($cvchd_id) {
                $query->where('facility_id', $cvchd_id)
                    ->orWhereJsonContains('facility_id', (string) $cvchd_id);
            })
            ->get();

        $cvchd_amount = $cvchd_info->sum(function ($item) {
            $amount = floatval(str_replace(',', '', $item->alocated_funds ?? 0)) 
                        - floatval(str_replace(',', '', $item->admin_cost ?? 0));
            return floatval($amount);
        });

        $cvchd_funds = $cvchd_info->sum(function ($item) {
            $amount = floatval(str_replace(',', '', $item->alocated_funds ?? 0));
            return floatval($amount);
        });

        $cvchd_admin = $cvchd_info->sum(function ($item) {
            $amount = floatval(str_replace(',', '', $item->admin_cost ?? 0));
            return floatval($amount);
        });
        
        $cvchd_util_sum = Utilization::whereIn('proponentinfo_id', $cvchd_info->pluck('id')->toArray())
            ->whereNotIn('proponentinfo_id', $used_info)
            ->where('status', 0)
            ->where(function ($query) {
                $query->whereHas('dv', function ($q) {
                    $q->whereColumn('div_id', 'route_no')
                    ->where('facility_id', 837);
                })->orWhereHas('newDv', function ($q) {
                    $q->whereColumn('div_id', 'route_no')
                    ->where('facility_id', 837);
                })->orWhereHas('dv3', function ($q) {
                    $q->whereColumn('div_id', 'route_no');
                });
            })
            ->sum(DB::raw('CAST(NULLIF(REPLACE(COALESCE(utilize_amount, "0"), ",", ""), "") AS DECIMAL(20,2))'));

        // $cvchd_util_sum = $cvchd_util_sum + $usage_amount + $additional_cvchd_usage;

        $cvchd_transfer_usage = UsageTransfer::where(function($query) use ($proponent) {
                $query->whereJsonContains('to_fid', "702") // matches ["702"]
                    ->orWhereNull('to_fid');            // also allow null
            })
            ->whereNotIn('id', $usage_to_ids)
            ->where('proponent', $proponent->proponent)
            ->sum('amount');
            
        $cvchd_util_sum = $cvchd_util_sum + $additional_cvchd_usage + $cvchd_transfer_usage;

        $check_all = $check_all + $cvchd_util_sum;

        $rem_balance = $cvchd_amount - $excess_balance;

        $used_info[] = $cvchd_info->pluck('id')->toArray();

        //excluded
        $used_info = Arr::flatten($used_info);

        $excluded_info = ProponentInfo::whereIn('proponent_id', $proponent_ids)->whereNotIn('id', $used_info)->get();
        $excluded_amount = $excluded_info->sum(function ($item) {
            $amount = floatval(str_replace(',', '', $item->alocated_funds ?? 0)) 
                        - floatval(str_replace(',', '', $item->admin_cost ?? 0));
            return floatval($amount);
        });

        $excluded_util = Utilization::whereIn('proponentinfo_id', $excluded_info->pluck('id'))
            ->whereNotIn('proponentinfo_id', $used_info)
            ->where('status', 0)
            ->where(function ($query) {

                $query->whereHas('dv', function ($q) {
                    $q->whereColumn('div_id', 'route_no')
                    ->where('facility_id', 837);
                })

                ->orWhereHas('newDv', function ($q) {
                    $q->whereColumn('div_id', 'route_no')
                    ->where('facility_id', 837);
                })

                ->orWhereHas('dv3', function ($q) {
                    $q->whereColumn('div_id', 'route_no');
                });

            })
            ->select(
                'proponentinfo_id',
                DB::raw('SUM(CAST(NULLIF(REPLACE(COALESCE(utilize_amount, "0"), ",", ""), "") AS DECIMAL(20,2))) as total_util')
            )
            ->groupBy('proponentinfo_id')
            ->pluck('total_util', 'proponentinfo_id');

        foreach ($excluded_info as $excluded) {
            $ex_util_sum = $excluded_util[$excluded->id] ?? 0;

            $ex_amount = floatval(str_replace(',', '', $excluded->alocated_funds ?? 0)) 
                    - floatval(str_replace(',', '', $excluded->admin_cost ?? 0));

            if ($ex_amount > $ex_util_sum) {
                $fac_ids = json_decode($excluded->facility_id, true);
                $fac_ids = is_array($fac_ids) ? $fac_ids : [$excluded->facility_id];
                $facilities = Facility::whereIn('id', $fac_ids)->pluck('name')->toArray();

                $rem_all_fac[] = [
                    "amount" => $ex_amount - $ex_util_sum,
                    "facilities" => $facilities
                ];
            }
        }

        $excluded_util_sum = Utilization::whereIn('proponentinfo_id', $excluded_info->pluck('id')->toArray())
            ->whereNotIn('proponentinfo_id', $used_info)
            ->where('status', 0)
            ->where(function ($query) {
                $query->whereHas('dv', function ($q) {
                    $q->whereColumn('div_id', 'route_no')
                        ->where('facility_id', 837);
                })->orWhereHas('newDv', function ($q) {
                    $q->whereColumn('div_id', 'route_no')
                    ->where('facility_id', 837);
                })->orWhereHas('dv3', function ($q) {
                    $q->whereColumn('div_id', 'route_no');
                })
                ;
            })
            ->sum(DB::raw('CAST(NULLIF(REPLACE(COALESCE(utilize_amount, "0"), ",", ""), "") AS DECIMAL(20,2))'));

        $check_all = $check_all+$excluded_util_sum;

        $excluded_excess = 0;
        if($excluded_util_sum > $excluded_amount){
            $excluded_excess = $excluded_util_sum - $excluded_amount;
        }
        
        $cvchd_overall_balance = ($cvchd_amount + $supp) - ($sub + $excluded_excess + $excess_balance + $cvchd_util_sum);

        if($cvchd_overall_balance > 0){
            $rem_all_fac[] = [
                    "amount" => $cvchd_overall_balance,
                    "facilities" => Facility::whereIn("id", [702])->pluck("name")->toArray()
                ];
        }

        $used_info[] = $excluded_info->pluck('id')->toArray();
        $used_info = Arr::flatten($used_info);
        $fourth_rem = 0;
        $check_rem = 0;
        if($cvchd_overall_balance <=0 ){
            $fourth_rem = $main_balance;
        }else{
            $fourth_rem = $cvchd_overall_balance + $main_balance;
            $check_rem = 1;
        }
        // return $main_balance;
        // return $included;

        
        return [
            'cvchd' => $cvchd_amount,
            'excess' => $excess_balance,
            'cvchd_util' => $cvchd_util_sum,
            'cut' => $cut,
            'excluded' => $excluded_amount,
            'excluded_util' => $excluded_util_sum,
            'first_rem' => $first_bal,
            'second_rem' => $sec_bal,
            'third_rem' => ($cvchd_amount - ($excess_balance + $cvchd_util_sum + $sub)) + $supp + $main_balance,
            'rem_all_fac' => $rem_all_fac,
            'fourth_rem' => $fourth_rem,
            'selected_info' => $selected_info,
            'selected_fund' => $facility_allocated,
            'selected_cost' => $facility_admin,
            'selected_gl' => $selected_total,
            'selected_dv' => $selected_util_sum,
            'check_rem' => $check_rem,
            'main_balance' => $main_balance,
            'cvchd_funds' => $cvchd_funds,  
            'cvchd_admin' => $cvchd_admin,
            'cvchd_usage' => $excluded_excess + $excess_balance + $cvchd_util_sum,
            'cvchd_overall_balance' => $cvchd_overall_balance,
            'supp' => $supp,
            'sub' => $sub,
            'patient_code' => $patient_code
        ];
    }

    

    // public function forPatientCode($proponent_id, $facility_id) {
    //     $included_ids = IncludedFacility::pluck('facility_id')->toArray();
       

    //     $user = Auth::user();
    //     $proponent= Proponent::where('id', $proponent_id)->first();
    //     $proponent_ids= Proponent::where('proponent', $proponent->proponent)->pluck('id')->toArray();
    //     $sl = ProponentInfo::whereIn('proponent_id', $proponent_ids)->where('facility_id', 'LIKE', "%851%")->get();
    //     if(count($sl) != 0) {
    //         $included_ids[] = 775; // add 775 if 851 exists
    //     }
    //     // $info_sum = ProponentInfo::whereIn('proponent_id', $proponent_ids)
    //     //     ->selectRaw('SUM(CAST(REPLACE(alocated_funds, ",", "") AS DECIMAL(10,2)) - CAST(REPLACE(admin_cost, ",", "") AS DECIMAL(10,2))) as total_amount')
    //     //     ->value('total_amount');
    //     $saaa = ProponentInfo::whereIn('proponent_id', $proponent_ids)
    //     ->where(function ($query) use ($included_ids) {
    //         foreach ($included_ids as $id) {
    //             $query->orWhere('facility_id', $id) // Match facility_id as integer
    //                 ->orWhereJsonContains('facility_id', (string) $id); // Match facility_id in JSON array
    //         }
    //     })->get();
    //     $info_sum = ProponentInfo::whereIn('proponent_id', $proponent_ids)
    //         ->where(function ($query) use ($included_ids) {
    //             foreach ($included_ids as $id) {
    //                 $query->orWhere('facility_id', $id) // Match facility_id as integer
    //                     ->orWhereJsonContains('facility_id', (string) $id); // Match facility_id in JSON array
    //             }
    //         })
    //         ->selectRaw('SUM(CAST(REPLACE(alocated_funds, ",", "") AS DECIMAL(10,2)) - CAST(REPLACE(admin_cost, ",", "") AS DECIMAL(10,2))) as total_amount')
    //         ->value('total_amount');
    //     $pat_sum = Patients::whereIn('proponent_id', $proponent_ids)
    //         ->where(function ($query) {
    //             $query->where('expired', '!=', 1)
    //                 ->orWhereNull('expired')
    //                 ->orWhere('expired', 0); // Include NULL values
    //         })
    //         ->sum(DB::raw("
    //             IFNULL(actual_amount, CAST(REPLACE(guaranteed_amount, ',', '') AS DECIMAL(20, 2)))
    //         ")); 
    //     $supplemental = SupplementalFunds::where('proponent',  $proponent->proponent)
    //         ->sum(DB::raw("
    //             CAST(REPLACE(IFNULL(amount, '0'), ',', '') AS DECIMAL(20, 2))
    //         "));

    //     $subtracted = DB::table('subtracted_funds')
    //         ->where('proponent',  $proponent->proponent)
    //         ->sum(DB::raw("
    //             CAST(REPLACE(IFNULL(amount, '0'), ',', '') AS DECIMAL(20, 2))
    //         "));   
    //     $info = ProponentInfo::whereIn('proponent_id', $proponent_ids)->pluck('id')->toArray();

    //     $info = ProponentInfo::whereIn('proponent_id', $proponent_ids)
    //         ->where(function ($query) use ($included_ids) {
    //             $query->whereIn('facility_id', $included_ids);
    //             foreach ($included_ids as $id) {
    //                 $query->orWhereJsonContains('facility_id', (string) $id);
    //             }
    //         })
    //         ->where(function ($query) {
    //             $query->where('facility_id', '!=', 702);
    //             $query->whereNot(function ($sub) {
    //                 $sub->whereJsonContains('facility_id', '702');
    //             });
    //         })
    //         ->pluck('id')
    //         ->toArray();

    //     $dv3_sum = Dv3Fundsource::whereIn('info_id', $info)
    //     ->with([
    //         'dv3' => function ($query){
    //             $query->with([
    //                 'facility:id,name',
    //                 'user:userid,fname,lname'
    //             ]);
    //         },
    //         'fundsource:id,saa'
    //     ])->sum('amount');

    //     $dv3_sumsd = Dv3Fundsource::whereIn('info_id', $info)
    //     // ->where('facility_id')
    //     ->with([
    //         'dv3' => function ($query){
    //             $query->with([
    //                 'facility:id,name',
    //                 'user:userid,fname,lname'
    //             ]);
    //         },
    //         'fundsource:id,saa'
    //     ])->sum('amount');

    //     // $dv3_sum = Dv3Fundsource::whereIn('info_id', $info)
    //     //     ->with([
    //     //         'dv3' => function ($query){
    //     //             $query->with([
    //     //                 'facility:id,name',
    //     //                 'user:userid,fname,lname'
    //     //             ]);
    //     //         },
    //     //         'fundsource:id,saa'
    //     //     ])->sum('amount');

    //     $dv1 = Utilization::whereIn('proponent_id', $proponent_ids)
    //         ->where('status', 0)
    //         ->where('facility_id', 837)
    //         ->where(function ($query) {
    //             $query->whereHas('dv', function ($query) {
    //                 $query->whereColumn('div_id', 'route_no');
    //             })->orWhereHas('newDv', function ($query) {
    //                 $query->whereColumn('div_id', 'route_no');
    //             });
    //         })
    //         ->with([
    //             'fundSourcedata:id,saa',
    //             'user:userid,fname,lname',
    //         ])
    //         ->orderBy('id', 'desc')
    //         ->get();

    //     $dv_sum = $dv1->sum(function ($item) {
    //         return floatval(str_replace(',', '', $item->utilize_amount));
    //     });
        
    //     if (count($sl) != 0) {
    //         $balance = ($info_sum + $supplemental) - ($subtracted + $dv3_sum + $dv_sum + $pat_sum) ;
    //         $sampp = $dv3_sum;
    //     }else{
    //         $balance = ($info_sum + $supplemental) - ($subtracted + $pat_sum + $dv_sum) ;
    //         $sampp = 2;
    //     }

      
    //     // $balance = ($info_sum + $supplemental) - ($subtracted + $pat_sum) ;

    //     $facility = Facility::find($facility_id);
    //     $patient_code = $proponent->proponent_code.'-'.$this->getAcronym($facility->name).date('YmdHis').$user->id;
    //     $total = ProponentInfo::whereIn('proponent_id', $proponent_ids)
    //             ->sum(DB::raw('CAST(REPLACE(alocated_funds, ",", "") AS DECIMAL(10,2)) - CAST(REPLACE(admin_cost, ",", "") AS DECIMAL(10,2))'));

    //     return [
    //         'patient_code' => $patient_code,
    //         'balance' => round($balance, 2),
    //         'total_funds' => $info_sum,
    //         'supplemental' => $supplemental,
    //         'subtracted' => $subtracted,
    //         'disbursement' => count($sl) != 0 ? $dv3_sum + $dv_sum : $dv_sum ,
    //         // 'disbursement' => $dv_sum,
    //         'gl_sum' => round($pat_sum, 2),
    //         'dv3'=> $dv3_sum,
    //         'overall_balance' => $this->overallBalance($proponent_id)
    //     ];
    // }
    
    private function overallBalance($id){
        $keyword = $id ? $id : 0;
        $list = Proponent::where('id', $keyword)->pluck('proponent')->toArray();

        $proponentGroups = Proponent::when($list, function ($query) use ($list) {
                return $query->whereIn('proponent', $list);
            })
            ->select('id', 'proponent')
            ->orderBy('proponent')
            ->get()
            ->groupBy('proponent');

        $allProponentIds = $proponentGroups->map(function ($group) {
            return $group->pluck('id')->toArray();
        });

        $fundsData = ProponentInfo::whereIn('proponent_id', $allProponentIds->flatten()->toArray())
            ->selectRaw('
                proponent_id,
                SUM(CAST(NULLIF(REPLACE(COALESCE(alocated_funds, "0"), ",", ""), "") AS DECIMAL(20,2))) as total_funds,
                SUM(CAST(NULLIF(REPLACE(COALESCE(admin_cost, "0"), ",", ""), "") AS DECIMAL(20,2))) as admin_cost
            ')
            ->groupBy('proponent_id')
            ->get()
            ->groupBy('proponent_id');

        $utilizationData = Patients::whereIn('proponent_id', $allProponentIds->flatten()->toArray())
            ->selectRaw('
                proponent_id,
                SUM(
                    CASE 
                        WHEN actual_amount IS NOT NULL AND actual_amount != "" 
                        THEN CAST(REPLACE(actual_amount, ",", "") AS DECIMAL(20, 2))
                        ELSE CAST(REPLACE(COALESCE(guaranteed_amount, "0"), ",", "") AS DECIMAL(20, 2))
                    END
                ) as total_utilized
            ')
            ->groupBy('proponent_id')
            ->get()
            ->groupBy('proponent_id');

        $supplementalFunds = SupplementalFunds::whereIn('proponent', $proponentGroups->keys())
            ->selectRaw('
                proponent,
                SUM(CAST(NULLIF(REPLACE(COALESCE(amount, "0"), ",", ""), "") AS DECIMAL(20,2))) as total_amount
            ')
            ->groupBy('proponent')
            ->get()
            ->keyBy('proponent');

        $subtractedFunds = DB::table('subtracted_funds')
            ->whereIn('proponent', $proponentGroups->keys())
            ->selectRaw('
                proponent,
                SUM(CAST(NULLIF(REPLACE(COALESCE(amount, "0"), ",", ""), "") AS DECIMAL(20,2))) as total_amount
            ')
            ->groupBy('proponent')
            ->get()
            ->keyBy('proponent');

        $dv1Data = Utilization::whereIn('proponent_id', $allProponentIds->flatten()->toArray())
            ->where('status', 0)
            ->where('facility_id', 837)
            ->where(function ($query) {
                $query->whereHas('dv', function ($q) {
                    $q->whereColumn('div_id', 'route_no');
                })->orWhereHas('newDv', function ($q) {
                    $q->whereColumn('div_id', 'route_no');
                });
            })
            ->selectRaw('
                proponent_id,
                SUM(CAST(NULLIF(REPLACE(COALESCE(utilize_amount, "0"), ",", ""), "") AS DECIMAL(20,2))) as total_amount
            ')
            ->groupBy('proponent_id')
            ->get()
            ->groupBy('proponent_id');

        $dv3Data= Utilization::whereIn('proponent_id', $allProponentIds->flatten()->toArray())
            ->where('status', 0)
            ->where(function ($query) {
                $query->whereHas('dv3', function ($q) {
                    $q->whereColumn('div_id', 'route_no');
                });
            })
            ->selectRaw('
                proponent_id,
                SUM(CAST(NULLIF(REPLACE(COALESCE(utilize_amount, "0"), ",", ""), "") AS DECIMAL(20,2))) as total_amount
            ')
            ->groupBy('proponent_id')
            ->get()
            ->groupBy('proponent_id');

        $allData = $proponentGroups->map(function ($proponentGroup, $proponentName) use (
            $fundsData,
            $utilizationData,
            $supplementalFunds,
            $subtractedFunds,
            $dv1Data,
            $dv3Data
        ) {
            $proponentIds = $proponentGroup->pluck('id');
            
            $totalFunds = 0;
            $totalAdminCost = 0;
            $totalUtilized = 0;
            $totalDv1Amount = 0;
            $totalDv3Amount = 0;

            foreach ($proponentIds as $id) {
                if ($fundsData->has($id)) {
                    $fundInfo = $fundsData->get($id)->first();
                    $totalFunds += $fundInfo->total_funds ?? 0;
                    $totalAdminCost += $fundInfo->admin_cost ?? 0;
                }

                if ($utilizationData->has($id)) {
                    $totalUtilized += $utilizationData->get($id)->sum('total_utilized');
                }

                if ($dv1Data->has($id)) {
                    $totalDv1Amount += $dv1Data->get($id)->sum('total_amount');
                }
                if ($dv3Data->has($id)) {
                    $totalDv3Amount += $dv3Data->get($id)->sum('total_amount');
                }
            }

            $supp = $supplementalFunds->get($proponentName)?->total_amount ?? 0;
            $sub = $subtractedFunds->get($proponentName)?->total_amount ?? 0;

            $netFunds = $totalFunds - $totalAdminCost;
            $remaining = $netFunds - $totalUtilized;
            $finalRemaining = $remaining + $supp - ($totalDv1Amount + $sub);

            return [
                'proponent' => $proponentGroup->first(),
                'sum' => round($netFunds, 2),
                'rem' => round($finalRemaining - $totalDv3Amount , 2),
                'supp' => round($supp, 2),
                'sub' => round($sub, 2),
                'disbursement' => round($totalDv1Amount + $totalDv3Amount, 2),
                'allocated_cost' => round($totalAdminCost, 2),
                'totalUtilized' => round($totalUtilized, 2),
                'admin_cost' => round($totalAdminCost, 2),
                'totalDv1Amount' => round($totalDv1Amount, 2),
                'totalDv3Amount' => round($totalDv3Amount, 2),
            ];
        });

        return $allData->sum('rem');
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

            if (exif_imagetype($imagePath) == IMAGETYPE_JPEG || exif_imagetype($imagePath) == IMAGETYPE_TIFF_II || exif_imagetype($imagePath) == IMAGETYPE_TIFF_MM) {
                $exif = exif_read_data($imagePath);
            } else {
                return "Please upload jpeg file.";
            }

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

            //for rotation
            $imagePath1 = storage_path('app/' . $path);
            $rotatedImagePath = storage_path('app/rotate/' . $path);
        
            if (!file_exists(storage_path('app/rotate'))) {
                mkdir(storage_path('app/rotate'), 0777, true);
            }
            $image1 = imagecreatefromjpeg($imagePath1);
        
            $rotatedImage = imagerotate($image1, 270, 0);
        
            imagejpeg($rotatedImage, $rotatedImagePath);
        
            imagedestroy($image1);
            imagedestroy($rotatedImage);
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

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Adjust column widths
        $sheet->getColumnDimension('A')->setWidth(2);  
        $sheet->getColumnDimension('B')->setWidth(50); 
        $sheet->getColumnDimension('C')->setWidth(20); 

        $sheet->mergeCells("B1:C1");
        $richText1 = new RichText();
        $normalText = $richText1->createTextRun($title);
        $normalText->getFont()->setBold(true)->setSize(20); 
        $sheet->setCellValue('B1', $richText1);
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getStyle('B1')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("PROPONENT");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('B3', $richText1);
        $sheet->getStyle('B3')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("PROPONENT CODE");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('C3', $richText1);
        $sheet->getStyle('C3')->getAlignment()->setWrapText(true);

        $sheet->getStyle('B3:C3')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(3)->setRowHeight(50); 

        $data = [];
        // $filename = $title.'.xls';
        // header("Content-Type: application/xls");
        // header("Content-Disposition: attachment; filename=$filename");
        // header("Pragma: no-cache");
        // header("Expires: 0");
        // $table_body = "<tr>
        //         <th>Proponent</th>
        //         <th>Proponent Code</th>
        //     </tr>";


        foreach($proponents as $row){
            // $table_body .= "<tr>
            //     <td style='vertical-align:top;'>$row->proponent</td>
            //     <td style='vertical-align:top;'>$row->proponent_code</td>
            // </tr>";
            $data [] = [
                $row->proponent,
                $row->proponent_code
            ];
        }
        // $display =
        //     '<h1>'.$title.'</h1>'.
        //     '<table cellspacing="1" cellpadding="5" border="1">'.$table_body.'</table>';

        // return $display;

        $sheet->fromArray($data, null, 'B4');

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER, 
            ],
        ];
        
        $sheet->getStyle('B3:C' . (count($data) + 3))->applyFromArray($styleArray);
        
        $sheet->getStyle('B4:C' . (count($data) + 3))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            
        // Output preparation
        ob_start();
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();

        // Filename
        $filename = $title . date('Ymd') . '.xlsx';

        // Set headers
        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=$filename");
        header("Pragma: no-cache");
        header("Expires: 0");

        // Output the file
        return $xlsData;
        exit;
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

    public function getAmount($id){
        $utilization = Utilization::where('proponentinfo_id', 832)->get();
        $initial = 9900000;
        foreach($utilization as $row){
            $row->beginning_balance = $initial;
            $row->save();
            $initial = $initial -  str_replace(',','',$row->utilize_amount);
        }
    }

    public function budgetCost($id, $amount){
        Fundsource::where('id', $id)->update([
            'budget_cost' => str_replace(',','', $amount),
            'added_by' => Auth::user()->userid
        ]);

        return 'success';
    }
}
