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
use App\Models\Dv2;
use App\Models\AddFacilityInfo;
use App\Models\Group;
use App\Models\Section;
use App\Models\Tracking_Releasev2;
use App\Models\Dv3;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Kyslik\ColumnSortable\Sortable;
use App\Models\NewDV;

class DvController extends Controller
{
    public function __construct()
    {
       $this->middleware('auth');
    }

    public function dv(Request $request)
    {
        $order = $request->input('order', 'asc');
        Dv::whereNull('dv_no')
            ->orWhere('dv_no', '')
            ->with('master')
            ->get()
            ->each(function ($dv) {
                if ($dv->master && $dv->master->dv_no) {
                    $dv->dv_no = $dv->master->dv_no;
                    $dv->save();
                }
            });

        $query = Dv::with([
            'facility:id,name',
            'user:userid,fname,lname,mname',
            'master', 
            'dv2',
        ])->orderBy('created_at', 'desc');

        // Handle keyword searching
        $saa_cl = clone($query);
        $pro_cl = clone($query);
        $user_cl = clone($query);

        if ($request->viewAll) {
            $request->merge([
                'keyword' => '',
                'filter_rem' => '',
                'filter_saa' => '',
                'filter_fac' => '',
                'filter_pro' => '',
                'filter_date' => '',
                'filter_created' => '',
                'generate' => '',
                'dates_filter' => ''
            ]);
        }

        $filter_dates = $request->input('dates_filter');

        $keyword = $request->keyword;
        if ($keyword) {
            
            $query->where(function ($query) use ($keyword) {
                $query->where('route_no', 'LIKE', "%$keyword%")
                        ->orWhereHas('facility', fn($q) => $q->where('name', 'LIKE', "%$keyword%"));
            });
            if ($query->count() === 0) {
                $saa = Fundsource::where('saa', 'LIKE', "%$keyword%")->pluck('id')->map(function ($item) {
                    return (string) $item;
                })->toArray();
                if($saa != null){
                    $query = $saa_cl;
                    $query->where(function($subQuery) use ($saa) {
                        foreach ($saa as $id) {
                            $subQuery->orWhereJsonContains('fundsource_id', $id);
                        }
                    });
                }
                
                if ($query->count() === 0) {
                    $pros = ProponentInfo::whereIn('proponent_id',Proponent::where('proponent', 'LIKE', "%$keyword%")->pluck('id'))->pluck('id')->map(function ($item) {
                        return (string) $item;
                    })->toArray();
                    if($pros != null){
                        $query = $pro_cl;
                        $query->where(function($subQuery) use ($pros) {
                            foreach ($pros as $id) {
                                $subQuery->orWhereJsonContains('info_id', $id);
                            }
                        });
                    }

                    if ($query->count() === 0) {
                        $user = User::where('lname', 'LIKE', "%$keyword%")->orWhere('fname', 'LIKE', "%$keyword%")->pluck('userid');
                        if($user != null){
                            $query = $user_cl;
                            $query->whereIn('created_by',$user);
                        }
                    }
                }
            }
        }

        // Date filtering
        if ($request->generate) {

            $dateRange = explode(' - ', $filter_dates);
            $start_date = date('Y-m-d', strtotime($dateRange[0]));
            $end_date = date('Y-m-d', strtotime($dateRange[1]));
            $query ->whereBetween('dv.created_at', [$start_date, $end_date . ' 23:59:59']);
     
        }

        // Sorting
        if ($sort = $request->sort) {
            if($sort == 'facility'){
                $query->leftJoin('facility', 'facility.id', '=', 'dv.facility_id')
                    ->orderBy('facility.name', $request->input('order')) 
                    ->select('dv.*');
            }else if($sort == 'saa'){
                $query = Dv::leftJoin('fundsource', 'fundsource.id', '=', DB::raw("JSON_UNQUOTE(JSON_EXTRACT(dv.fundsource_id, '$[0]'))"))
                    ->orderBy('fundsource.saa', $request->input('order'))
                    ->select('dv.*');
            }else if($sort == 'proponent'){
                $query = Dv::leftJoin('proponent_info', function($join) {
                            $join->on('proponent_info.id', '=', DB::raw("JSON_UNQUOTE(JSON_EXTRACT(dv.info_id, '$[0]'))"));
                        })
                        ->leftJoin('proponent', 'proponent_info.proponent_id', '=', 'proponent.id')
                        ->orderBy('proponent.proponent', $request->input('order'))
                        ->select('dv.*');
            }else if($sort == 'date'){
                $query->orderBy(DB::raw('DATE(created_at)'), $request->input('order'));
            }else if($sort == 'user'){
                $query->orderBy(
                        \DB::connection('dohdtr')->table('users')->select('lname')
                            ->whereColumn('users.userid', 'dv.created_by'),$request->input('order'));
            }
        }

        // // Filtering table header column
        // if($request->filt_dv){
            if($request->filter_rem){
                $rem = explode(',',$request->filter_rem);
                if(in_array('pending', $rem)){
                    $query->where('obligated', null);
                }
                if(in_array('obligated', $rem)){
                    $query->where('obligated', 1)->where('paid', null);
                }
                if(in_array('processed', $rem)){
                    $query->where('paid', 1);
                }
            }
            if($request->filter_saa){
                $saa_l = explode(',',$request->filter_saa);
                $query->where(function($subQuery) use ($saa_l) {
                    foreach ($saa_l as $id) {
                        $subQuery->orWhereJsonContains('fundsource_id', $id);
                    }
                }); 
            }
            if ($request->filter_fac) {
                $query->whereIn('dv.facility_id', explode(',', $request->filter_fac));
            }
            
            if ($request->filter_pro) {
                $query->leftJoin('proponent_info', function($join) {
                        $join->on('proponent_info.id', '=', DB::raw("JSON_UNQUOTE(JSON_EXTRACT(dv.info_id, '$[0]'))"));
                    })
                    ->leftJoin('proponent', 'proponent_info.proponent_id', '=', 'proponent.id')
                    ->whereIn('proponent.proponent', explode(',', $request->filter_pro))
                    ->select('dv.*');
            }
            if($request->filter_date){
                $query->whereIn(DB::raw('DATE(date)'), explode(',',$request->filter_date));
            }
            if($request->filter_created){
                $query->whereIn('dv.created_by', explode(',',$request->filter_created));
            }
        // }            

        $results = $query->paginate(50);

        $date = Dv::groupBy(DB::raw('DATE(date)'))->pluck(DB::raw('MAX(date)'));
        $pros = Proponent::groupBy('pro_group')->pluck(DB::raw('MAX(proponent) as proponent'));
        $ids = Dv::groupBy('created_by')->pluck(DB::raw('MAX(created_by)'));
        $users = User::whereIn('userid', $ids)->select('userid', 'fname', 'lname')->get();
        $facility = Facility::whereIn('id', Dv::pluck('facility_id'))->select('id', 'name')->get();

        if (in_array(Auth::user()->userid, [1027, 2660])) {
            return view('dv.acc_dv', [
                'disbursement' => $results,
                'keyword' => $request->keyword ?: '',
                'user' => Auth::user()->userid,
                'order' => $order
            ]);
        } else {
            return view('dv.dv', [
                'disbursement' => $results,
                'keyword' => $request->keyword ?: '',
                'user' => Auth::user()->userid,
                'proponents' => Proponent::get(),
                'proponentInfo' => ProponentInfo::get(),
                'order' => $order,
                'filter_rem' => explode(',', $request->filter_rem),
                'filter_fac' => explode(',', $request->filter_fac),
                'filter_saa' => explode(',', $request->filter_saa),
                'filter_pro' => explode(',', $request->filter_pro),
                'filter_date' => explode(',', $request->filter_date),
                'filter_created' => explode(',', $request->filter_created),
                'generate' => $request->generate,
                'dates_generated' => $filter_dates,
                'pros' => $pros,
                'date' => $date,
                'users' => $users,
                'facility' => $facility,
                'fundsources' => Fundsource::select('saa', 'id')->get()
            ]);
        }
    }

    public function getFundsource(Request $request){
        $currentYear = date("Y");
        $info = ProponentInfo::with(['facility', 'fundsource', 'proponent'])
            ->where(function ($query) use ($request, $currentYear) {
                $query->where(function ($q) use ($currentYear) {
                    $q->whereJsonContains('proponent_info.facility_id', '702')
                    ->whereYear('created_at', $currentYear);
                })->orWhereJsonContains('proponent_info.facility_id', [$request->facility_id]);
            })
            ->whereYear('created_at', $currentYear) // Ensures filtering by year for all conditions
            ->orWhere(function ($query) use ($request, $currentYear) {
                $query->whereIn('proponent_info.facility_id', [$request->facility_id, '702'])
                    ->whereYear('created_at', $currentYear);
            })
            ->get();

        // $info = ProponentInfo::with('facility', 'fundsource', 'proponent')
        //     ->where(function ($query) use ($request) {
        //         $query->whereJsonContains('proponent_info.facility_id', '702')
        //             ->orWhereJsonContains('proponent_info.facility_id', [$request->facility_id]);
        //     })
        //     ->orWhereIn('proponent_info.facility_id', [$request->facility_id, '702'])
        //     ->get();
        // return count($info);
                    
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
            $user = DB::connection('dohdtr')
                        ->table('users')
                        ->leftJoin('dts.users', 'users.userid', '=', 'dts.users.username')
                        ->where('users.userid', '=', Auth::user()->userid)
                        ->first();
                        
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
            }

            $dv3 = Dv3::where('route_no', $route_no)->first();
            if($dv3){
                $dv3->status = 1;
                $dv3->save();
            }else{
            //    NewDV::where('route_no', $route_no)->update(['status' =>]);
            }

        }

        return redirect()->back()->with('releaseAdded', true);
    }

    public function createDv(Request $request)
    {
        $user = Auth::user();
        $dvs = Dv::get();

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
        
        if(Auth::user()->userid == 1027 || Auth::user()->userid == 2660){
            $dv->dv_no = $request->input('dv_no');
        }

        if($dv) {
            
            // Utilization::where('div_id', $dv->route_no)->update(['status'=>1]);
            $dv->modified_by = Auth::user()->userid;
            $dv->dv_no = $request->input('dv_no');
            $saa = explode(',', $dv->fundsource_id);
            $saa = str_replace(['[', ']', '"'],'',$saa);

            $amount = [$dv->amount1, !empty($dv->amount2)?$dv->amount2: 0 , !empty($dv->amount3)?$dv->amount3: 0];
            $index = 0;
            $infos = array_map('intval', json_decode($dv->info_id));

            foreach($infos as $index=>$id){
                $p_if = ProponentInfo::where('id', $id)->first();

                if($dv->deduction1 >= 3){
                    $total =((double)str_replace(',', '',$amount[$index]) / 1.12);
                }else{
                    $total =((double)str_replace(',', '',$amount[$index]));
                }

                Utilization::where('div_id', $dv->route_no)->where('proponentinfo_id', $p_if->id)->update(['status' => 1]);
                $get = Utilization::where('div_id', $dv->route_no)->where('proponentinfo_id', $p_if->id)->orderBy('id', 'desc')->first();
                $all = Utilization::where('proponentinfo_id', $p_if->id)->where('id', '>', $get->id)->orderBy('id', 'asc')->get();
                // return $get;
                foreach($all as $row){
                    // return (float)str_replace(',','',$row->beginning_balance) + (float)str_replace(',','',$amount[$index]);
                    $row->beginning_balance = (float)str_replace(',','',$row->beginning_balance) + (float)str_replace(',','',$amount[$index]);
                    $row->save();
                }
                
                $return = (double)str_replace(',','',$p_if -> remaining_balance) + (double)str_replace(',', '',$amount[$index]);
                $p_if->remaining_balance = $return;
                $index = $index + 1;
                $p_if->save();
            }

            $utilization = Utilization::where('div_id', $dv->route_no)->get();
            $util = $utilization->sortByDesc('id')->first();
            // $get_util = $util->where('id', '>', $util->id)->get();
            $get_util = Utilization::where('id', '>',  $util->id)->where('proponentinfo_id', $util->proponentinfo_id)->get();
            $bal =  $util->beginning_balance;
            if($get_util){
                foreach($get_util as $u){
                    if($u->status == 0){
                        $u->beginning_balance = $bal;
                        $bal = number_format( (double)str_replace(',','',$u->beginning_balance) - (double)str_replace(',','',$u->utilize_amount) , 2,'.', ',');
                    }else{
                        $u->beginning_balance = $bal;
                        $bal = $u->beginning_balance;
                    }
                    // return $u;
                    $u->save();
                }
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

        $desc = "Disbursement voucher for " . Facility::where('id', $dv->facility_id)->value('name') . " amounting to Php " . number_format(str_replace(',', '', $dv->total_amount), 2, '.', ',');

        if($check == null || $check == '' ){
            $dts_user = DB::connection('dts')->select("SELECT id FROM users WHERE username = ? LIMIT 1",array($dv->created_by));
            $data = [$dv->route_no,"DV",$dv->created_at,$dts_user[0]->id,0,  $desc, 0.00,"", "", "", "", "", "", "", "", "", "", "0000-00-00 00:00:00",
                        "", "", "", 0, "", !Empty($dv->dv_no)? $dv->dv_no:"", "", "", "", "", ];
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
        }else{
            
            // Update 
            $trackingMaster = TrackingMaster::where('route_no', $dv->route_no)->first();

            if ($trackingMaster) {
                $trackingMaster->description = $desc;
                $trackingMaster->save();
            }

            $trackingDetails = TrackingDetails::where('route_no', $dv->route_no)->first();

            if ($trackingDetails) {
                $trackingDetails->action = $desc;
                $trackingDetails->save();
            }

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

                    $proponent_info = ProponentInfo::where('id', $info[$index])->first();

                    $cleanedSaa = str_replace(['[', ']', '"'], '', $saa);
                    $utilize = new Utilization();
                    $utilize->status = 0;
                    $utilize->fundsource_id = trim($cleanedSaa);
                    $utilize->proponentinfo_id = $info[$index];
                    $utilize->proponent_id = $all_pro[$index];
                    $utilize->facility_used = $facility_used[$index];
                    $utilize->facility_id = $dv->facility_id;
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
                    $proponent_info->save();
                    $utilize->save();
                }
            }
            return redirect()->back()->with('dv_create', true);
    }
    
    public function getDv(Request $request){
       
        $dv = Dv::where('id', $request->dvId)->with('facility')->first();

        if($dv){
            $info = array_map('intval', json_decode($dv->info_id));
            $idsString = implode(',', $info);
            
            $proponentInfo = ProponentInfo::whereIn('id', $info)
                ->with(['proponent', 'fundsource', 'facility'])
                ->orderByRaw("FIELD(id, $idsString)")
                ->get();
            
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
        // return $dv;
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
                    ->where('proponent_id', $proponent[$index])->where('status', 0)->orderBy('id', 'desc')->first();
                    //->whereNull('obligated')
                if($utilization != null){

                    if($utilization->obligated == 1){
                        $info->remaining_balance = floatval(str_replace(',','', $info->remaining_balance)) + floatval(str_replace(',','', $utilization->budget_utilize));
                        $info->save();
                    }

                    $utilization->budget_bbalance = $info->remaining_balance + floatval(str_replace(',','', $amount[$index]));
                    $utilization->budget_utilize = $amount[$index];
                    $utilization->obligated = 1;
                    $utilization->obligated_by = Auth::user()->userid;
                    $utilization->save();
                }else{
                    return 'Please contact System Administrator!';
                }
            }
            $dv->ors_no = $request->ors_no;
            $dv->obligated = 1;
            $dv->save();
        }
        return redirect()->route('fundsource_budget.pendingDv', ['type' => 'pending'])->with('', true);

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

            // $response = Http::withoutVerifying()->get('https://mis.cvchd7.com/dts/document/paid/'. $dv->route_no . '/' .Auth::user()->userid);
            // if($response){
            //     if($response == '0'){
            //         return redirect()->back()->with(
            //             'pay_dv', true        
            //             );
            //     }
            // }else{
            //  return redirect()->back()->with(['pay_dv' => true]);
            // }
        }
        return redirect()->back()->with('pay_dv', true);
    }

    function addDvNo(Request $request){
        $dv= Dv::where('id', $request->input('dv_id'))->first();
        if($dv){
            $dv->dv_no = $request->input('dv_no');
            $dv->save();

            // $response = Http::withoutVerifying()->get('https://mis.cvchd7.com/dts/document/dv_no/' . $dv->dv_no . '/' . $dv->route_no . '/' .Auth::user()->userid);
        
                $response = Http::withoutVerifying()->get('http://192.168.110.17/dts/document/dv_no/' . $dv->dv_no . '/' . $dv->route_no . '/' .Auth::user()->userid);

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

    public function dvHistory($route_no){
        return view('dv.dv_md_history', [
            'utilizations' => Utilization::with('fundSourcedata', 'proponentdata', 'user')->where('div_id', $route_no)->get()
        ]);
    }

    public function removeDv($route_no){
        $dv = Dv::where('route_no', $route_no)->first();
        // $amount_list = 
        $int_list = array_map('intval', json_decode($dv->info_id));
        $string_list = implode(',', $int_list);
        $info_list = ProponentInfo::whereIn('id', $int_list)->orderByRaw("FIELD(id, $string_list)")->get();
        $amount_list = array_values(array_filter([$dv->amount1, !Empty($dv->amount2)?$dv->amount2 : 0,  
                        !Empty($dv->amount3)?$dv->amount3: 0], function($value){ return $value !== 0 && $value!== null;}));
                        // return $info_list;

        foreach($info_list as $index => $info){
            $rem = (double) str_replace(',','',$info->remaining_balance) + (double) str_replace(',','', $amount_list[$index]);
            $info->remaining_balance = $rem;
            $info->save();
            $u = Utilization::where('div_id', $route_no)->where('proponentinfo_id', $info->id)->where('status', 0)->first();
            $util = Utilization::where('proponentinfo_id', $info->id)->where('id','>',$u->id)->get();
            $u->delete();
            foreach($util as $item){
                $item->beginning_balance = (float) str_replace(',','', $item->beginning_balance) + (double) str_replace(',','', $amount_list[$index]);
                $item->save();
            }
        }

        $dv->delete();
        Utilization::where('div_id',$route_no)->delete();
        Dv2::where('route_no',$route_no)->delete();

        return redirect()->back()->with('dv_remove', true);
    }

    public function acceptDocument(Request $req){
        $user = DB::connection('dohdtr')
            ->table('users')
            ->leftJoin('dts.users', 'users.userid', '=', 'dts.users.username')
            ->where('users.userid', '=', Auth::user()->userid)
            ->first();
        $id = $req->id;
        $remarks = $req->remarks;
        $date_in = date('Y-m-d H:i:s');

        $tracking_details = TrackingDetails::where('id',$id)->where('route_no', $req->route_no)->orderBy('id', 'DESC');

        //RELEASED TO
        $this->releasedStatusChecker($tracking_details->first()->route_no,Auth::user()->section);

        $tracking_details->update(array(
            'code' => 'accept;' . $user->section,
            'date_in' => $date_in,
            'action' => $remarks,
            'received_by' => $user->id,
            'alert' => 0
        ));

        NewDV::where('route_no', $req->route_no)->update(['edit_status' => 1]);
        
    }

    public function releasedStatusChecker($route_no,$section){
        $release = Tracking_Releasev2::where("route_no","=",$route_no)
            ->where("released_section_to","=",$section)
            ->where(function ($query) {
                $query->where('status','=','waiting')
                    ->orWhere('status','=','return');
            })
            ->orderBy('id', 'DESC');

        if($release->first()){
            $minute = $this->checkMinutes($release->first()->released_date);
            if($minute <= 30 && ($release->first()->status == "waiting" || $release->first()->status == "return" )){
                $release->update([
                    "status" => "accept"
                ]);
            }
            elseif($minute > 30 && $release->first()->status == "waiting" || $release->first()->status == "return" ) {
                $release->update([
                    "status" => "report"
                ]);
            }
        }
    }

    static function checkMinutes($start_date)
    {
        /* $start_date = "2018-11-16 11:24:33";
         $end_date = "2018-11-16 14:43:00";*/
        $global_end_date = date("Y-m-d H:i:s");
        $end_date = $global_end_date;

        $start_checker = date("Y-m-d",strtotime($start_date));
        $end_checker = date("Y-m-d",strtotime($end_date));
        $fhour_checker = date("H",strtotime($start_date));
        $lhour_checker = date("H",strtotime($end_date));
        $minutesTemp = 0;


        if($start_checker != $end_checker) return 100;

        if($fhour_checker <= 7 && $lhour_checker >= 8){
            $fhour_checker = 8;
            $start_date = $start_checker.' '.'08:00:00';
        }
        elseif($fhour_checker == 11 && $lhour_checker >= 12){
            $start_date = new DateTime($start_date);
            $end_date = $start_date->diff(new DateTime($start_checker." 12:00:00"));

            $minutes = $end_date->days * 24 * 60;
            $minutes += $end_date->h * 60;
            $minutes += $end_date->i;

            $start_date = $start_checker.' '.'13:00:00';
            $minutesTemp = $minutes;
            $end_date = $global_end_date;
        }
        elseif($fhour_checker == 12 && $lhour_checker >= 13){
            $fhour_checker = 13;
            $start_date = $start_checker.' '.'13:00:00';
        }
        elseif($fhour_checker >= 17 && $lhour_checker >= 17){
            $start_date = $start_checker.' '.'17:00:00';
            $end_date = $end_checker.' '.'17:00:00';
        }
        elseif($lhour_checker >= 17){
            $end_date = $end_checker.' '.'17:00:00';
        }

        if(
            ($fhour_checker >= 8 && $fhour_checker < 12)
            || ($fhour_checker >= 13)

            && ($lhour_checker >= 8 && $lhour_checker < 12)
            || ($lhour_checker >= 13)
        )
        {
            $start_date = new DateTime($start_date);
            $end_date = $start_date->diff(new DateTime($end_date));

            $minutes = $end_date->days * 24 * 60;
            $minutes += $end_date->h * 60;
            $minutes += $end_date->i;

            if($minutesTemp){
                $minutes += $minutesTemp;
            }
            return $minutes;
        }
        return 100;
    }

}
