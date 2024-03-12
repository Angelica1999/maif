<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fundsource;
use App\Models\Facility;
use App\Models\Proponent;
use App\Models\ProponentInfo;
use App\Models\Utilization;
use App\Models\Transfer;

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
                            $query->with('facility');
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
            // $fundsources = $fundsources->where('saa', 'LIKE', "%$request->keyword%");
            $fundsources = $fundsources->where('saa', 'LIKE', "%$request->keyword%")
            ->orWhereHas('proponents', function ($query) use ($request) {
                $query->whereHas('proponentInfo', function ($subquery) use ($request) {
                    $subquery->where('proponent', 'LIKE', "%$request->keyword%");
                });
            });
        } 
        $fundsources = $fundsources
                        ->orderBy('id','desc')
                        ->paginate(15);
                        
        $user = DB::connection('dohdtr')->table('users')->leftJoin('dts.users', 'users.userid', '=', 'dts.users.username')
                ->where('users.userid', '=', Auth::user()->userid)
                ->select('users.section')
                ->first();
               
        return view('fundsource.fundsource',[
            'fundsources' => $fundsources,
            'keyword' => $request->keyword,
            'utilizations' => $utilizations,
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
            $fundsource =  Fundsource::where('id', $fundsource_id)->first();
            
            if($fundsource){
                $fundsource->saa = $request->input('saa');
                $fundsource->alocated_funds = str_replace(',','',  $request->input('allocated_funds'));
                if((double)str_replace(',','',  $request->input('allocated_funds')) >= 1000000){
                    $admin_cost = (double)str_replace(',','',  $request->input('allocated_funds')) * .01;
                    $fundsource->admin_cost = $admin_cost;
                    $fundsource->remaining_balance = (double)str_replace(',','',  $request->input('allocated_funds')) -  $admin_cost;
                }else{
                    $fundsource->admin_cost = 0;
                    $fundsource->remaining_balance = str_replace(',','',  $request->input('allocated_funds'));
                }
                
                $fundsource->created_by = Auth::user()->userid;
                $fundsource->save();

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
                        ->groupBy('proponent_code')
                        ->get(); 

        return view('fundsource.breakdowns', [
            'fundsource' => $fundsource,
            'pro_count' => ProponentInfo::where('fundsource_id', $fundsourceId)->count(),
            'facilities' => Facility::get(),
            'proponents' => $proponents,
            'uniqueCode' => bin2hex($randomBytes)
        ]);

    }

    public function removeInfo($infoId){
        if($infoId){
            ProponentInfo::where('id', $infoId)->delete();
        }
    }
    
    public function saveBDowns(Request $request){

        $breakdowns = $request->input('breakdowns');
        $fund_id = $request->input('fundsource_id');
        $get_fundsource = Fundsource::where('id', $fund_id)->first();
        // if($fund_id){

        // }
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
                    $compare = (double) str_replace(',','',$info->remaining_balance) + (double) str_replace(',','',$info->admin_cost);

                    if(str_replace(',','', $info->alocated_funds) == $compare ){
                        $info->facility_id = json_encode($breakdown['facility_id']);
                        $info->proponent_id = $proponentId;
                        $info->alocated_funds = $breakdown['alocated_funds'];
                        if((double)str_replace(',','',$get_fundsource->alocated_funds) >= 1000000){
                            $info->admin_cost =number_format( (double)str_replace(',','',$breakdown['alocated_funds']) * .01 , 2,'.', ',');
                            $info->remaining_balance = (double)str_replace(',','',$breakdown['alocated_funds']) - (double)str_replace(',','', $info->admin_cost);
                        }else{
                            $info->admin_cost = 0;
                            $info->remaining_balance = $breakdown['alocated_funds'];
                        }
                           
                        $info->created_by = Auth::user()->userid;
                        $info->save();
                    }else{
                        $info->facility_id = json_encode($breakdown['facility_id']);
                        $info->proponent_id = $proponentId;
                        $info->created_by = Auth::user()->userid;
                        $info->save();
                    }
                }else{
                    $p_info = new ProponentInfo();
                    $p_info->fundsource_id = $breakdown['fundsource_id'];
                    $p_info->proponent_id = $proponentId;
                    $p_info->facility_id = json_encode($breakdown['facility_id']);
                    $p_info->alocated_funds = $breakdown['alocated_funds'];
                    if((double)str_replace(',','',$get_fundsource->alocated_funds) >= 1000000){
                        $p_info->admin_cost =number_format((double)str_replace(',','',$breakdown['alocated_funds']) * .01 , 2,'.', ',');
                        $p_info->remaining_balance = (double)str_replace(',','',$breakdown['alocated_funds']) - (double)str_replace(',','',$p_info->admin_cost);
                    }else{
                        $p_info->admin_cost = 0;
                        $p_info->remaining_balance = $breakdown['alocated_funds'];
                    }
                    $p_info->created_by = Auth::user()->userid;
                    $p_info->save();
                }
            }
        }
        // return redirect()->back()->with('breakdowns_created', true);
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
        return view('fundsource.transfer_funds', [
            'from_info' => $from_info,
            'to_info' => $to_info
        ]);
    }

    public function saveTransferFunds(Request $request){
        // return $request->input('to_saa');

        $from_id = $request->input('from_info');
        $to_id = $request->input('to_info');

        if($from_id != null && $to_id != null){
            $from = ProponentInfo::where('id', $from_id)->first();
            $to = ProponentInfo::where('id', $to_id)->first();

            $transfer = new Transfer();
            $transfer->from_proponent = $from->proponent_id;
            $transfer->from_saa = $from->fundsource_id;
            $transfer->from_facility = $from->facility_id;
            $transfer->from_amount = $request->input('to_amount');
            $transfer->to_proponent = $to->proponent_id;
            $transfer->to_saa = $to->fundsource_id;
            $transfer->to_facility = $to->facility_id;
            $transfer->to_amount = $request->input('to_amount');
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
    
                $from->alocated_funds = str_replace(',', '',$from->remaining_balance) - str_replace(',', '',$request->input('to_amount'));
                $from->remaining_balance = str_replace(',', '',$from->remaining_balance) - str_replace(',', '',$request->input('to_amount'));
                $from->save();

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
    
                $to->alocated_funds = str_replace(',', '',$to->remaining_balance) + str_replace(',', '',$request->input('to_amount')); 
                $to->remaining_balance = str_replace(',', '',$to->remaining_balance) + str_replace(',', '',$request->input('to_amount')); 
                $to->save();
            }

            session()->flash('fund_transfer', true);
            return redirect()->back();
        }
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

        $proponents = Proponent::select( DB::raw('MAX(proponent) as proponent'), DB::raw('MAX(pro_group) as id'))
            ->groupBy('pro_group') ->whereIn('id', $ids)
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
        $proponent= Proponent::where('pro_group', $proponent_id)->first();
        $proponent_ids= Proponent::where('pro_group', $proponent_id)->pluck('id')->toArray();
        // return $proponent_ids;
        $facility = Facility::find($facility_id);
        $patient_code = $proponent->proponent_code.'-'.$this->getAcronym($facility->name).date('YmdHi').$user->id;

        //previous code
        
        // $proponent_info = ProponentInfo::where(function ($query) use ($facility_id) {
        //                         $query->where('facility_id', $facility_id)
        //                             ->orWhereJsonContains('facility_id', $facility_id);
        //                     })
        //                     ->whereIn('proponent_id', $proponent_ids)
        //                     ->with('fundsource')
        //                     ->get();
        
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

}
