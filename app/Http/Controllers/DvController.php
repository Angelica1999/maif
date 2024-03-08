<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Fundsource;
use App\Models\User;
use App\Models\TrackingMaster;
use App\Models\TrackingDetails;
use App\Models\Facility;
use App\Models\Utilization;
use App\Models\Proponent;
use App\Models\ProponentInfo;
use App\Models\Dv;
use App\Models\AddFacilityInfo;
use App\Models\Group;
use App\Models\Section;
use App\Models\Tracking_Releasev2;
use Illuminate\Support\Facades\Http;

class DvController extends Controller
{
    public function __construct()
    {
       $this->middleware('auth');
    }

    public function dv(Request $request){

        $dv_update = Dv::whereNotNull('dv_no')->with(['fundsource','facility', 'user'])->get();
        $dv= Dv::whereNull('dv_no')->get();
        // return $dv;
        
        foreach($dv as $d){
            $master = TrackingMaster::where('route_no', $d->route_no)->first();
            $dv_here = Dv::where('route_no',  $d->route_no)->first();
            if($master->dv_no !== null){
                $dv_here->dv_no = $master->dv_no;
                $dv_here->save();
            }
        }

        foreach ($dv_update as $result) {
            if($result->master->div_no != null){
                $result->update(['dv_no' => $result->master->div_no]);
            }
        }
                
        if($request->viewAll){
            $request->keyword = '';
        }
        
        $results = Dv::with(['fundsource', 'facility', 'user', 'master'])
                    ->when($request->keyword, function ($query) use ($request) {
                        $query->where('route_no', 'LIKE', "%$request->keyword%");
                    })
                ->orderby('id', 'desc')
                ->paginate(50);
        
        if(Auth::user()->userid == 1027){
            return view('dv.acc_dv', [
                'disbursement' => $results,
                'keyword' => $request->keyword ?: '',
                'user' => Auth::user()->userid
            ]);
        }else{
            return view('dv.dv', [
                'disbursement' => $results,
                'keyword' => $request->keyword ?: '',
                'user' => Auth::user()->userid
            ]);
        }
    }

    public function getFundsource(Request $request){
       
        $info = ProponentInfo::with('facility', 'fundsource', 'proponent')
                    ->whereJsonContains('proponent_info.facility_id', $request->facility_id)
                    ->orWhere('proponent_info.facility_id', $request->facility_id)
                    ->orWhere('proponent_info.facility_id', 702)
                    ->get();
        $facility = Facility::where('id', $request->facility_id)->first();
        return response()->json(['info' => $info, 'facility' => $facility]);
    }

    public function saveUpdateDV(Request $request){
        return $request->input('amount1');
        $check = $request->input('div_id');
        return redirect()->back();
    }

    public function updateDV(Request $request){
        $dv = Dv::where('id', $request->dv_id)->first();
        if($dv){
            $saa = explode(',', $dv->fundsource_id);
            $saa = str_replace(['[', ']', '"'],'',$saa);
            $all = [];
            foreach($saa as $id){
                $all []= $id;
           }
        $fund_source = Fundsource::whereIn('id', $all)->get();
        $facility = Facility::where('id', $dv->facility_id)->first();
        $facilityIds = ProponentInfo::pluck('facility_id')->toArray();
        $facilities = Facility::whereIn('id', $facilityIds)->get();

        $saaIds = ProponentInfo::where('facility_id', $dv->facility_id)->pluck('fundsource_id')->toArray();
        $saa = Fundsource::whereIn('id', $saaIds)->get();
        }
    
        return view('dv.edit_dv', 
        [
        'dv' =>$dv,
        'fund_source' => $fund_source,
        'facility' => $facility,
        'facilities' => $facilities,
        'saa' => $saa]);
    }

    public function addRelease(Request $req) {
        $routes = explode(',',$req->route_no);
        $doc_id = explode(',',$req->currentID);

        foreach($routes as $index => $route_no){

            // return $route;
            $user = Auth::user();
            $release_to_datein = date('Y-m-d H:i:s');

            if($req->op != 0) {
                $id = $req->op;
                TrackingDetails::where('id',$id)->update(array(
                    'code' => 'temp;' . $req->section,
                    'action' => ($req->remarks == null || $req->remarks == '')? '' : $req->remarks,
                    'date_in' => $release_to_datein,
                    'status' => 0
                ));
                $status='releaseUpdated';
            } else {
                if($req->currentID!=0){

                    $table = TrackingDetails::where('id',$doc_id[$index])->orderBy('id', 'DESC');
                    $code = isset($table->first()->code) ? $table->first()->code:null;

                    $tracking_release = new Tracking_Releasev2();
                    $tracking_release->released_by = $user->id;
                    $tracking_release->released_section_to = $req->section;
                    $tracking_release->released_date = $release_to_datein;
                    $tracking_release->remarks = ($req->remarks == null || $req->remarks == '')? '' : $req->remarks;
                    $tracking_release->document_id = $table->first()->id;
                    $tracking_release->route_no = $route_no;
                    $tracking_release->status = "waiting";
                    $tracking_release->save();

                    $update = array(
                        'code' => ''
                    );

                    $table->update($update);
                    $tmp = explode(';',$code);
                    $code = $tmp[0];
                    if($code=='return')
                    {
                        $table->delete();
                    }
                }else{
                    $tracking_details_info = TrackingDetails::where('route_no',$route_no)
                            ->orderBy('id','desc')
                            ->first();
                    $tracking_details_id = $tracking_details_info->id;
                    $tracking_details_id = $tracking_details_info->id;

                    $update = array(
                        'code' => ''
                    );
                    $table = TrackingDetails::where('id',$tracking_details_id);
                    $table->update($update);
                }

                $q = new TrackingDetails();
                $q->route_no = $route_no;
                $q->date_in = $release_to_datein;
                $q->action = ($req->remarks == null || $req->remarks == '')? '' : $req->remarks;
                $q->delivered_by = $user->id;
                $q->code= 'temp;' . $req->section;
                $q->alert= 0;
                $q->received_by= 0;
                $q->status= 0;
                $q->save();

                // Session::put("releaseAdded",[
                //     "route_no" => $req->route_no,
                //     "section_released_to_id" => $req->section,
                //     "user_released_name" => $user->fname.' '.$user->lname,
                //     "section_released_by_id" => $user->section,
                //     "section_released_by_name" => Section::find($user->section)->description,
                //     "remarks" => $req->remarks,
                //     "status" => "released"
                // ]);
            }
        }
        return redirect()->back()->with('releaseAdded', true);
    }

    public function createDv(Request $request)
    {
        $user = Auth::user();
        $dvs = Dv::get();
         
        // $facilityId = ProponentInfo::where('facility_id','=', $request->facilityId)->get();
        // $facilityIds = ProponentInfo::pluck('facility_id')->toArray();
        // $facilities = Facility::whereIn('id', $facilityIds)->get();

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
                                    'id',
                                    'fname'
                                );
                            }
                        ])->get();
            // return  $facilities;

        $VatFacility = AddFacilityInfo::Select('id','vat')->distinct()->get();
        $ewtFacility = AddFacilityInfo::Select('id','Ewt')->distinct()->get();

        return view('dv.create_dv', [
            'user' => $user,
            'dvs' => $dvs,
            'FundSources' =>  $fundsources,
            'fundsources' => Fundsource::get(), 
            'facilities' => Facility::get(),
            'VatFacility' => $VatFacility,
            'ewtFacility' => $ewtFacility
            // 'facilityId' => $facilityId
        ]);
    }

    function getUser(Request $request){
        $data = User::where('userid', $request->userid)->first();
        return response()->json($data);
    }

    function createDvSave(Request $request){
        $check = $request->input('dv');       
        $facility_used = array_values(array_filter([$request->input('fac_id1'), $request->input('fac_id2'), $request->input('fac_id3')],
                            function($value){return $value !== '0' && $value !==null;}));
        $info = array_values(array_filter([$request->input('saa1_infoId'), $request->input('saa2_infoId'), $request->input('saa3_infoId')],
                            function($value){return  $value !=='0' && $value !==null;}));
        $per_amount = [$request->input('amount1'),$request->input('amount2'),$request->input('amount3')];
        $utilize_amount = [$request->input('saa1_utilize'),$request->input('saa2_utilize'),$request->input('saa3_utilize')];
        $discount = [$request->input('saa1_discount'),$request->input('saa2_discount'),$request->input('saa3_discount')];
        $dv= Dv::where('id', $check)->first();
        $all_pro = array_values(array_filter([$request->input('pro_id1'), $request->input('pro_id2'), $request->input('pro_id3')],
                        function($value){return $value !== '0' && $value !==null;}));
        if($dv) {

            Utilization::where('div_id', $dv->route_no)->update(['status'=>1]);
            $dv->modified_by = Auth::user()->userid;
            $dv->dv_no = $request->input('dv_no');
            $saa = explode(',', $dv->fundsource_id);
            $saa = str_replace(['[', ']', '"'],'',$saa);

            $amount = [$dv->amount1, !empty($dv->amount2)?$dv->amount2: 0 , !empty($dv->amount3)?$dv->amount3: 0];
            $index = 0;

            foreach($saa as $index=>$id){
                $p_if = ProponentInfo::where('id', $info[$index])->first();

                if($dv->deduction1 >= 3){
                    $total =((double)str_replace(',', '',$amount[$index]) / 1.12);
                }else{
                    $total =((double)str_replace(',', '',$amount[$index]));
                }
                
                $return = (double)$p_if -> remaining_balance + (double)str_replace(',', '',$amount[$index]);
                $p_if->remaining_balance = $return;
                $index = $index + 1;
                $p_if->save();
            }
        }else{
            $dv = new Dv();
            $dv->created_by = Auth::user()->userid;
            $dv->route_no = date('Y-') . Auth::user()->userid . date('mdHis');
            
        } 

        $dv->date = $request->input('datefield');
        $dv->facility_used = json_encode($facility_used);
        $dv->info_id = json_encode($info);
        $dv->group_id = $request->input('group_id');
        $dv->facility_id = $request->input('facilityname');
        $dv->address = $request->input('facilityAddress');
        $dv->month_year_from = $request->input('billingMonth1');
        $dv->month_year_to = $request->input('billingMonth2');
        if($request->input('control_no') !=null){
            $dv->control_no = $request->input('control_no');
        }
        $saaNumbers =array_values(array_filter([
            $request->input('fundsource_id'),
            $request->input('fundsource_id_2'),
            $request->input('fundsource_id_3'),
            ], function($value){return $value !==0 && $value!==null;}));

            // return $saaNumbers;
        $dv->fundsource_id = json_encode($saaNumbers);
        $dv->amount1 = $request->input('amount1');
        $dv->amount2 = $request->input('amount2');
        $dv->amount3 = $request->input('amount3');
        $dv->total_amount = $request->input('total');
        $dv->deduction1 = $request->input('vat');
        $dv->deduction2 = $request->input('ewt');
        $dv->deduction_amount1 = $request->input('deductionAmount1');
        $dv->deduction_amount2 = $request->input('deductionAmount2');
        $dv->total_deduction_amount = $request->input('totalDeduction');
        $dv->overall_total_amount = $request->input('overallTotal1');
        $dv->accumulated = $request->input('accumulated');
        $dv->proponent_id = json_encode($all_pro);
        $dv->save();
        
        if($check == null || $check == '' ){
        
            $dts_user = DB::connection('dts')->select("SELECT id FROM users WHERE username = ? LIMIT 1",array($dv->created_by));
            $desc = "Disbursement voucher for ". Facility::where('id', $dv->facility_id)->value('name');
            $data = [$dv->route_no,"DV",$dv->created_at,$dts_user[0]->id,0,  $desc, 0.00,"", "", "", "", "", "", "", "", "", "", "0000-00-00 00:00:00",
                        "", "", "", 0, "", "", "", "", "", "", ];
            DB::connection('dts')->insert(
                "INSERT INTO TRACKING_MASTER(route_no, doc_type, prepared_date, prepared_by, division_head, description, amount, pr_no, po_no, pr_date, purpose, po_date, 
                    source_fund, requested_by, route_to, route_from, supplier, event_date, event_location, event_participant, cdo_applicant, cdo_day, event_daterange, 
                    payee, item, dv_no, ors_no, fund_source_budget, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, now(), now())",$data);
            $tracking_master = TrackingMaster::where('route_no', $dv->route_no)->first();
            $updated_route = date('Y-').$tracking_master->id;
            $tracking_master->route_no = $updated_route;
            $tracking_master->save();  
            $dv->route_no = $updated_route;
            $dv->save();
            Group::whereIn('id',explode(',',$request->input('group_id')))->update(['route_no'=>$updated_route, 'status'=>0]);
            //creating tracking_details
            $data_details = [$updated_route, "", 0,$dv->created_at, $dts_user[0]->id, $dts_user[0]->id,  $desc, 0];
            DB::connection('dts')->insert("INSERT INTO TRACKING_DETAILS(route_no, code, alert, date_in, received_by, delivered_by, action, status,created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, now(), now())",$data_details);
        }
       

        if ($dv->fundsource_id) {
            $saaNumbersArray= array_map('intval', json_decode($dv->fundsource_id));
            $id = array_values(array_filter([
                $request->input('pro_id1'),
                $request->input('pro_id2'),
                $request->input('pro_id3')
            ], function ($value) {
                return $value !== '0';
            }));
            // return $proponent_id;

                foreach ($saaNumbersArray as $index=>$saa) {
                    
                    $cleanedSaa = str_replace(['[', ']', '"'], '', $saa);
                    $utilize = new Utilization();
                    $utilize->status = 0;
                    $utilize->fundsource_id = trim($cleanedSaa);
                    $utilize->proponentinfo_id = $info[$index];
                    $utilize->proponent_id = $all_pro[$index];
                    $utilize->facility_id = $facility_used[$index];
                    $utilize->facility_id = $dv->facility_id;

                    $proponent_info = ProponentInfo::where('id', $info[$index])->first();

                    $utilize->div_id = $dv->route_no;
                    $utilize->beginning_balance = $proponent_info->remaining_balance;
                    $utilize->discount = $discount[$index];
                    $utilize->utilize_amount = $utilize_amount[$index];
                    $utilize->created_by = Auth::user()->userid;

                    if($proponent_info && $proponent_info != null){
                        $cleanedValue = str_replace(',', '', $proponent_info->remaining_balance);
                        $proponent_info->remaining_balance = (float)$cleanedValue - (float)str_replace(',', '',$per_amount[$index]);
                    }else{
                        return "contact system administrator" ;
                    }
                
                    $utilize->save();
                    $proponent_info->save();
                }
            }
            return redirect()->back()->with('dv_create', true);
    }
    
    public function getDv(Request $request){
       
        $dv = Dv::where('id', $request->dvId)->with('facility')->first();

        if($dv){
            $info = array_map('intval', json_decode($dv->info_id));
            $proponentInfo = ProponentInfo::whereIn('id', $info)->with(['proponent', 'fundsource', 'facility'])->get();
            return response()->json(['dv' =>$dv,'proponentInfo' => $proponentInfo]);
        }

        
        // if($dv){
        //     $all_saa = array_map('intval', json_decode($dv->fundsource_id));
        //     $all_proponent = array_map('intval', json_decode($dv->proponent_id));
        //     $all_fac = array_map('intval', json_decode($dv->facility_used));

        //     $fund_source = Fundsource::whereIn('id', $all_saa)->get();
        //     $proponent = Proponent::whereIn('id', $all_proponent)->get();
        //     $facilities = Facility::whereIn('id', $all_fac)->get();

        //     $orderMapping = array_flip($all_saa);
        //     $fund_source = $fund_source->sortBy(function ($item) use ($orderMapping) {
        //         return $orderMapping[$item->id];
        //     })->values();

        //     $orderProponent = array_flip($all_proponent);
        //     $proponent = $proponent->sortBy(function ($item) use ($orderProponent) {
        //         return $orderProponent[$item->id];
        //     })->values();
                        
        //     $facility = Facility::where('id', $dv->facility_id)->first();
        
        // }
        // return response()->json(['dv' =>$dv,'fund_source' => $fund_source,'facility' => $facility, 'facilities' => $facilities,'proponent' => $proponent]);
    }

    function getSections($id){
        $sections = Section::where('division',$id)->orderBy('description','asc')->get();
        return $sections;
    }

    function obligate(Request $request){
        $dv= Dv::where('id', $request->input('dv_id'))->first();
        $gg = [];
        if($dv){
            $saa= array_map('intval', json_decode($dv->fundsource_id));
            $proponent= array_map('intval', json_decode($dv->proponent_id));
            $amount = array_values(array_filter([$dv->amount1, $dv->amount2, $dv->amount3],
                function($value){return $value !== '' && $value !==null;}));
            foreach($saa as $index => $saa_list){
                $info = Fundsource::where('id', $saa_list)->first();
                $info->remaining_balance = $info->remaining_balance - floatval(str_replace(',','', $amount[$index]));
                $info->save();
                $utilization = Utilization::where('div_id', $dv->route_no)->where('fundsource_id', $saa_list)
                    ->where('proponent_id', $proponent[$index])->orderBy('id', 'desc')->latest()->first();
                    $gg[]=$utilization;

                $utilization->budget_bbalance = $info->remaining_balance + floatval(str_replace(',','', $amount[$index]));
                $utilization->budget_utilize = $amount[$index];
                $utilization->obligated = 1;
                $utilization->obligated_by = Auth::user()->userid;
                $utilization->save();
            }
            $dv->ors_no = $request->ors_no;
            $dv->obligated = 1;
            $dv->save();
            $response = Http::withoutVerifying()->get('https://mis.cvchd7.com/dts/document/ors_no/' . $dv->ors_no . '/' . $dv->route_no . '/' .Auth::user()->userid);
            if($response){
                if($response == '0'){
                     return redirect()->route('fundsource_budget.pendingDv', ['type' => 'pending'])->with('', true);
                }
            }else{
                return redirect()->route('fundsource_budget.pendingDv', ['type' => 'pending'])->with('', true);
            }
        }
        // return redirect()->route('fundsource_budget.pendingDv', ['type' => 'pending'])->with('', true);

    }

    function payDv(Request $request){
        $dv= Dv::where('id', $request->input('dv_id'))->first();
        if($dv){
            $util = Utilization::where('div_id', $dv->route_no)
            ->where('status', '=', 0)
            ->update([
                'paid' => 1,
                'paid_by' => Auth::user()->userid,
            ]);
        
            $dv->paid = 1;
            $dv->paid_by = Auth::user()->userid;
            $dv->save();

            $response = Http::withoutVerifying()->get('https://mis.cvchd7.com/dts/document/paid/'. $dv->route_no . '/' .Auth::user()->userid);
            if($response){
                if($response == '0'){
                    return redirect()->back()->with(
                        'pay_dv', true        
                        );
                }
            }else{
                return redirect()->back()->with([
                    'pay_dv' => true        
                ]);
            }
        }
        // return redirect()->back()->with('pay_dv', true);

    }

    function addDvNo(Request $request){
        $dv= Dv::where('id', $request->input('dv_id'))->first();
        if($dv){
            $dv->dv_no = $request->input('dv_no');
            $dv->save();

            $response = Http::withoutVerifying()->get('https://mis.cvchd7.com/dts/document/dv_no/' . $dv->dv_no . '/' . $dv->route_no . '/' .Auth::user()->userid);
            if($response){
                // $res = $response;
                if($response == "0"){

                    return redirect()->back()->with(
                        'add_dvno', true        
                        );
                }
            }else{
                return redirect()->back()->with([
                    'add_dvno' => true        
                ]);
            }
          
        }
    }

    function facilityGet(Request $request){
  
          ProponentInfo::where('fundsource_id', $request->fundsource_id)->get();
          $proponentInfo = ProponentInfo::with('facility')
                         ->where('fundsource_id', $request->fundsource_id)
                         ->get();
                   
         return $proponentInfo;
      }

      function dvfacility(Request $request){
         $proponentInfo = ProponentInfo::with('facility')
         ->where('facility_id',  $request->facility_id)->first();
        // return $proponentInfo;
         if($proponentInfo){
           $facilityAddress = $proponentInfo->facility->address;
           return response()->json(['facilityAddress' => $facilityAddress]);
         }else{
          return "facility not found";
         }
      }

    public function getFund (Request $request) {
        $facilityId = ProponentInfo::with([
            'fundsource' => function ($query) {
                $query->select('id', 'saa');
            },
        ])->where('facility_id', '=', $request->facilityId)
            ->where('fundsource_id', '!=', $request->fund_source)
            ->get();

        $fund_source = ProponentInfo::with([
            'fundsource' => function ($query) {
                $query->select(
                    'id',
                    'saa'
                );
            }])->where('fundsource_id','=', $request->fund_source)
            ->first();
             
        $beginning_balances = session()->put('balance', $fund_source->alocated_funds);
        return $facilityId;
    }

    public function getvatEwt(Request $request)
    {
        $facilityVatEwt = AddFacilityInfo::where('facility_id',$request->facilityId)->first();
        return $facilityVatEwt;
    }
    
    public function getAlocated(Request $request){
        $allocatedFunds = ProponentInfo::where('facility_id', $request->facilityId)
       // ->where('fundsource_id', $request->fund_source)
        ->select('alocated_funds','fundsource_id', 'id', 'remaining_balance', 'proponent_id', 'facility_id')
        ->get();
        return response()->json(['allocated_funds' => $allocatedFunds]);
    }

    public function getAllInfo(Request $request){
        $allocatedFunds = ProponentInfo::select('alocated_funds','fundsource_id', 'id', 'remaining_balance', 'proponent_id', 'facility_id')->get();
        return response()->json(['allocated_funds' => $allocatedFunds]);
    }

    

//     public function createFundSourceSave(Request $request) {
//         $user = Auth::user();
//         //return $request->all();
//         if(isset($request->saa_exist)) {
//             $fundsource = Fundsource::find($request->saa_exist);
//         } else {
//             $fundsource = new Fundsource();
//             $fundsource->saa = $request->saa;
//             $fundsource->created_by = $user->id;
//             $fundsource->save();
//         }

//         $proponent = new Proponent();
//         $proponent->fundsource_id = $fundsource->id;
//         $proponent->proponent = $request->proponent;
//         $proponent->proponent_code = $request->proponent_code;
//         $proponent->created_by = $user->id;
//         $proponent->save();

//         $index = 0;
//         foreach ($request->facility_id as $facilityId) {
//             $proponentInfo = new ProponentInfo();
//             $proponentInfo->fundsource_id = $fundsource->id;
//             $proponentInfo->proponent_id = $proponent->id;
//             $proponentInfo->facility_id = $request->facility_id[$index];
//             $proponentInfo->alocated_funds = $request->alocated_funds[$index];
//             $proponentInfo->created_by = $user->id;
//             $proponentInfo->save();
//             $index++;
//         }

    

//         session()->flash('fundsource_save', true);
//         return redirect()->back();
//     }

//     public function Editfundsource($proponentId)
//     {
//         $fundsource = Fundsource::
//                 with([
//                 'proponents' => function ($query) use($proponentId){
//                     $query->select('id', 'fundsource_id', 'proponent', 'proponent_code')
//                     ->where('id', $proponentId);
//                 },
//                 'proponents.proponentInfo' => function ($query) {
//                     $query->select('id', 'fundsource_id', 'proponent_id', 'alocated_funds');
//                 },
//                 'encoded_by' => function ($query) {
//                     $query->select('id', 'name');
//                 }
//             ])->first();    
    
//         $specificProponent = $fundsource->proponents->first();
    
//         return view('fundsource.update_fundsource', [
//             'fundsource' => $fundsource,
//             'facility' => Facility::get(),
//             'fundsourcess' => Fundsource::get(),
//             'proponent' => $specificProponent,
//         ]);
//     }
    
    
//     public function proponentGet(Request $request) {
//         return Proponent::where('fundsource_id',$request->fundsource_id)->get();
//     }

//     public function facilityProponentGet(Request $request) {
//         return ProponentInfo::where('proponent_id',$request->proponent_id)->with([
//             'facility' => function ($query) {
//                 $query->select(
//                     'id',
//                     DB::raw('name as description')
//                 );
//             }
//         ])
//         ->get();
//     }

//     public function getAcronym($str) {
//         $words = explode(' ', $str); // Split the string into words
//         $acronym = '';
        
//         foreach ($words as $word) {
//             $acronym .= strtoupper(substr($word, 0, 1)); // Take the first letter of each word and convert to uppercase
//         }
        
//         return $acronym;
//     }

//     public function forPatientCode(Request $request) {
//         $user = Auth::user();
//         $proponent = Proponent::find($request->proponent_id);
//         $facility = Facility::find($request->facility_id);
//         $patient_code = $proponent->proponent_code.'-'.$this->getAcronym($facility->name).date('YmdHi').$user->id;
//         return $patient_code;
//     }



// public function forPatientFacilityCode($fundsource_id) {

//     $proponentInfo = ProponentInfo::where('fundsource_id', $fundsource_id)->first();
    
//     if($proponentInfo){
//         $facility = Facility::find($proponentInfo->facility_id);

//         $proponent = Proponent::find($proponentInfo->proponent_id);
//         $proponentName = $proponent ? $proponent->proponent : null;
//        // return $proponent->id . '' . $facility->id;
//         return response()->json([

//             'proponent' => $proponentName,
//             'proponent_id' => $proponentInfo? $proponentInfo->proponent_id : null,
//             'facility' => $facility ? $facility->name : null,
//             'facility_id' => $proponentInfo ? $proponentInfo->facility_id : null,
//         ]);
//     }else{
//         return response()->json(['error' => 'Proponent Info not found'], 404);
//     }
// }



//     public function transactionGet() {
//         $randomBytes = random_bytes(16); // 16 bytes (128 bits) for a reasonably long random code
//         $uniqueCode = bin2hex($randomBytes);
//         $facilities = Facility::where('hospital_type','private')->get();
//         return view('fundsource.transaction',[
//             'facilities' => $facilities,
//             'uniqueCode' => $uniqueCode
//         ]);
//     }

//     public function fundSourceGet() {
//         $result = Fundsource::with([
//             'proponents' => function ($query) {
//                 $query->with([
//                     'proponentInfo' => function ($query) {
//                         $query->with('facility');
//                     }
//                 ]);
//             }
//         ])->get();
//         return $result;
//     }

}
