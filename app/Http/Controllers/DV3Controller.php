<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Utilization;
use App\Models\Proponent;
use App\Models\Fundsource;
use App\Models\User;
use App\Models\Patients;
use App\Models\Facility;
use App\Models\AddFacilityInfo;
use App\Models\Dv;
use App\Models\Dv2;
use App\Models\Group;
use App\Models\ProponentInfo;
use App\Models\Dv3;
use App\Models\Dv3Fundsource;
use App\Models\TrackingMaster;
use App\Models\TrackingDetails;

class Dv3Controller extends Controller
{
    public function __construct(){
       $this->middleware('auth');
    }

    public function dv3(Request $request) {

        $filter_date = $request->input('filter_dates');
        Dv3::whereNull('dv_no')
            ->orWhere('dv_no', '')
            ->with('master')
            ->get()
            ->each(function ($dv) {
                if ($dv->master && $dv->master->dv_no) {
                    $dv->dv_no = $dv->master->dv_no;
                    $dv->save();
                }
            });
        $dv3 = Dv3::              
                with([
                    'extension' => function ($query) {
                        $query->with([
                            'proponentInfo' => function ($query) {
                                $query->with('proponent', 'fundsource');
                            }
                        ]);
                    },
                    'facility' => function ($query) {
                        $query->select(
                            'id',
                            'name'
                        );
                    },
                    'user' => function ($query) {
                        $query->select(
                            'userid',
                            'fname',
                            'lname'
                        );
                    }
                ]);

        if($request->gen_key){
            $dateRange = explode(' - ', $filter_date);
            $start_date = date('Y-m-d', strtotime($dateRange[0]));
            $end_date = date('Y-m-d', strtotime($dateRange[1]));
            $dv3->whereBetween('created_at', [$start_date, $end_date . ' 23:59:59']);
        }

        $user_clone = clone($dv3);
        $pro_clone = clone($dv3);

        if($request->viewAll){
            
            $request->keyword = '';
            $request->filter_rem3 = '';
            $request->filter_fac3 = '';
            $request->filter_saa3 = '';
            $request->filter_pro3 = '';
            $request->filter_date3 = '';
            $request->filter_on3 = '';
            $request->filter_by3 = '';
            $request->gen_key = '';
            $filter_date = '';

        }else if($request->keyword){
            $keyword = $request->keyword;
            $dv3->where(function ($query) use ($keyword) {
                $query->where('route_no', 'LIKE', "%$keyword%")
                    ->orWhereHas('facility', function ($q) use ($keyword) {
                        $q->where('name', 'LIKE', "%$keyword%");
                    })
                    ->orWhereHas('extension.proponentInfo.fundsource', function ($q) use ($keyword) {
                        $q->where('saa', 'LIKE', "%$keyword%");
                    })
                    ->orWhereHas('extension.proponentInfo.proponent', function ($q) use ($keyword) {
                        $q->where('proponent', 'LIKE', "%$keyword%"); 
                    });
            });
            if ($dv3->count() === 0) {
                $user = User::where('lname', 'LIKE', "%$keyword%")->orWhere('fname', 'LIKE', "%$keyword%")->pluck('userid');
                if($user != null){
                    $query = $user_clone;
                    $query->whereIn('created_by',$user);
                }
            }
        }

        //header sorting
        if ($sort = $request->sort) {
            if($sort == 'status'){
                $dv3->orderBy('status', $request->input('order'));
            }else if($sort == 'remarks'){
                $dv3->orderBy('remarks', $request->input('order'));
            }else if($sort == 'facility'){
                $dv3->leftJoin('facility', 'facility.id', '=', 'dv3.facility_id')
                    ->orderBy('facility.name', $request->input('order')) 
                    ->select('dv3.*');
            }else if($sort == 'saa'){
                $dv3 ->leftJoin('dv3_fundsources', function($join) {
                    $join->on('dv3.route_no', '=', 'dv3_fundsources.route_no')
                         ->whereRaw('dv3_fundsources.id = (SELECT MIN(id) FROM dv3_fundsources WHERE dv3_fundsources.route_no = dv3.route_no)');
                })
                ->leftJoin('fundsource', 'fundsource.id', '=', 'dv3_fundsources.fundsource_id')
                ->orderBy('fundsource.saa', $request->input('order', 'asc'))
                ->select('dv3.*');
            }else if($sort == 'proponent'){
                $dv3 ->leftJoin('dv3_fundsources', function($join) {
                    $join->on('dv3.route_no', '=', 'dv3_fundsources.route_no')
                         ->whereRaw('dv3_fundsources.id = (SELECT MIN(id) FROM dv3_fundsources WHERE dv3_fundsources.route_no = dv3.route_no)');
                })
                ->leftJoin('proponent_info', 'proponent_info.id', '=', 'dv3_fundsources.info_id')
                ->leftJoin('proponent', 'proponent.id', '=', 'proponent_info.proponent_id')
                ->orderBy('proponent.proponent', $request->input('order', 'asc'))
                ->select('dv3.*');
            }else if($sort == 'date'){
                $dv3->orderBy('date', $request->input('order'));
            }else if($sort == 'total'){
                $dv3->orderBy('total', $request->input('order'));
            }else if($sort == 'on'){
                $dv3->orderBy(
                    \DB::connection('dohdtr')->table('users')->select('lname')
                        ->whereColumn('users.userid', 'dv3.created_by'),$request->input('order'));
            }
        }else{
            $dv3->orderBy('created_at', 'desc');
        }

        $facility = Facility::whereIn('id', Dv3::pluck('facility_id'))->select('id', 'name')->get();
        $saa = Fundsource::whereIn('id', Dv3Fundsource::pluck('fundsource_id'))->select('id', 'saa')->get();
        $proponents = Dv3Fundsource::leftJoin('proponent_info', 'proponent_info.id', '=', 'dv3_fundsources.info_id')->pluck('proponent_info.proponent_id');
        $proponents = Proponent::whereIn('id', $proponents)->select('id', 'proponent')->get();
        $dates = Dv3::groupBy(DB::raw('DATE(date)'))->pluck(DB::raw('MAX(date)'));
        $on =  Dv3::groupBy(DB::raw('DATE(created_at)'))->pluck(DB::raw('MAX(created_at)'));
        $by =  User::whereIn('userid', Dv3::pluck('created_by'))->select('userid', 'lname', 'fname')->get();

        //header filtering 
        // if($request->filt3_dv){
            if($request->filter_rem3 != null){
                $dv3->whereIn('remarks', explode(',', $request->filter_rem3));
            }
            if($request->filter_fac3 != null){
                $dv3->whereIn('facility_id', explode(',', $request->filter_fac3));
            }
            if($request->filter_saa3 != null){
                $dv3->where(function ($query) use ($request) {
                    $query->orWhereHas('extension.proponentInfo.fundsource', function ($q) use ($request) {
                        $q->whereIn('id', explode(',', $request->filter_saa3)); 
                    });
                });
            }
            if($request->filter_pro3 != null){
                $dv3->where(function ($query) use ($request) {
                    $query->orWhereHas('extension.proponentInfo.proponent', function ($q) use ($request) {
                        $q->whereIn('id', explode(',', $request->filter_pro3)); 
                    });
                });
            }
            if($request->filter_date3 != null){
                $dv3->whereIn('date', explode(',', $request->filter_date3));
            }
            if($request->filter_on3 != null){
                $dv3->whereIn(DB::raw('DATE(created_at)'), explode(',',$request->filter_on3));
            }
            if($request->filter_by3 != null){
                $dv3->whereIn('created_by', explode(',', $request->filter_by3));
            }
        // }

        return view('dv3.dv3',[
            'dv3' => $dv3->paginate(50),
            'keyword' => $request->keyword,
            'order' => $request->input('order'),
            'filter_rem3' =>  explode(',', $request->filter_rem3),
            'filter_fac3' =>  explode(',', $request->filter_fac3),
            'filter_saa3' =>  explode(',', $request->filter_saa3),
            'filter_pro3' =>  explode(',', $request->filter_pro3),
            'filter_date3' =>  explode(',', $request->filter_date3),
            'filter_on3' =>  explode(',', $request->filter_on3),
            'filter_by3' =>  explode(',', $request->filter_by3),
            'facilities' => $facility,
            'saa' => $saa,
            'proponents' => $proponents,
            'dates' => $dates,
            'on' => $on,
            'by' => $by,
            'gen_key' => $request->gen_key,
            'generated_dates' => $filter_date
            ]);
    }

    function dv3Save(Request $request){

        $userid = Auth::user()->userid;
        $initial_route = date('Y-') . $userid . date('mdHis');

        $dv3 = new Dv3();
        $dv3->route_no = $initial_route;
        $dv3->date = $request->dv3_date;
        $dv3->facility_id = $request->dv3_facility;
        $dv3->total = (float)str_replace(',','',$request->total_amount);
        $dv3->created_by = Auth::user()->userid;
        $dv3->remarks = 0;
        $dv3->status = 0;
        $dv3->save();

        $desc = "Disbursement voucher for " . Facility::where('id', $request->dv3_facility)->value('name') . " amounting to Php " . number_format(str_replace(',', '', $request->total_amount), 2, '.', ',');
        
        $dts_user = DB::connection('dts')->select("SELECT id FROM users WHERE username = ? LIMIT 1",array($userid));
        $data = [$initial_route,"DV",$dv3->created_at,$dts_user[0]->id,0,  $desc, 0.00,"", "", "", "", "", "", "", "", "", "", "0000-00-00 00:00:00",
                    "", "", "", 0, "", "", "", "", "", "", ];
        DB::connection('dts')->insert(
            "INSERT INTO TRACKING_MASTER(route_no, doc_type, prepared_date, prepared_by, division_head, description, amount, pr_no, po_no, pr_date, purpose, po_date, 
                source_fund, requested_by, route_to, route_from, supplier, event_date, event_location, event_participant, cdo_applicant, cdo_day, event_daterange, 
                payee, item, dv_no, ors_no, fund_source_budget, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, now(), now())",$data);
        $tracking_master = TrackingMaster::where('route_no', $initial_route)->first();
        $updated_route = date('Y-').$tracking_master->id;
        $tracking_master->route_no = $updated_route;
        $tracking_master->save();  
        $dv3->route_no = $updated_route;
        $dv3->save();

        $data_details = [$updated_route, "", 0,$dv3->created_at, $dts_user[0]->id, $dts_user[0]->id,  $desc, 0];
        DB::connection('dts')->insert("INSERT INTO TRACKING_DETAILS(route_no, code, alert, date_in, received_by, delivered_by, action, status,created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, now(), now())",$data_details);
        
        $amount = $request->amount;
        $saa = $request->fundsource_id;
        // $vat = $request->vat_amount;
        // $ewt = $request->ewt_amount;

        foreach($request->info_id as $index => $id){
            
            $each = (float)str_replace(',','',$amount[$index]);
            $info = ProponentInfo::where('id', $id)->first();

            $util = new Utilization();
            $util->div_id = $dv3->route_no;
            $util->beginning_balance = $info->remaining_balance;
            $util->discount = 0;
            $util->utilize_amount = (float)str_replace(',','',$amount[$index]);
            $util->facility_id = $request->dv3_facility;
            $util->proponent_id = $info->proponent_id;
            $util->facility_used = $info->facility_id;
            $util->fundsource_id = $info->fundsource_id;
            $util->proponentinfo_id = $info->id;
            $util->status = 0;
            $util->created_by = $userid;
            $util->save();

            $info->remaining_balance = (float)str_replace(',','',$info->remaining_balance) - $each;
            $info->save();

            $dv3_funds = new Dv3Fundsource();
            $dv3_funds->route_no = $dv3->route_no;
            $dv3_funds->fundsource_id = $saa[$index];
            $dv3_funds->info_id = $id;
            $dv3_funds->amount = (float)str_replace(',','',$amount[$index]);
            $dv3_funds->vat = 0;
            $dv3_funds->ewt = 0;
            $dv3_funds->save();
        }
        return redirect()->back()->with('dv3', true);
    }

    public function dv3Update($route_no){
        if($route_no){
            $dv3 = Dv3::              
                with([
                    'extension' => function ($query) {
                        $query->with([
                            'proponentInfo' => function ($query) {
                                $query->with('proponent', 'fundsource');
                            }
                        ]);
                    },
                    'facility' => function ($query) {
                        $query->select(
                            'id',
                            'name',
                            'address'
                        );
                    }
                ])->where('route_no', $route_no)->first();
            
            $facility_id = (string) $dv3->facility_id;

            $info = ProponentInfo::with('facility', 'fundsource', 'proponent')
                ->where(function ($query) use ($facility_id) {
                    $query->whereJsonContains('proponent_info.facility_id', '702')
                        ->orWhereJsonContains('proponent_info.facility_id', [$facility_id]);
                })
                ->orWhereIn('proponent_info.facility_id', [$facility_id, '702'])
                ->get();
            $f_info = AddFacilityInfo::where('id', $facility_id)->select('vat', 'ewt')->first();

            $section = DB::connection('dohdtr')
                ->table('users')
                ->leftJoin('dts.users', 'users.userid', '=', 'dts.users.username')
                ->where('users.userid', '=', Auth::user()->userid)
                ->value('users.section');

            return view('dv3.update_dv3', [
                'facilities' => Facility::get(),
                'info' => $info,
                'dv3' => $dv3,
                'f_info' => $f_info,
                'section' => $section
            ]);
        }
    }

    public function saveUpdate($route_no, Request $request){

        $userid = Auth::user()->userid;
        $amount = $request->amount;
        $saa = $request->fundsource_id;
        // $vat = $request->vat_amount;
        // $ewt = $request->ewt_amount;
      
        $dv3 = Dv3::where('route_no',$route_no)->with('extension')->first();
        foreach($dv3->extension as $item){
            $info = ProponentInfo::where('id', $item->info_id)->first();
            $info->remaining_balance = str_replace(',','',$info->remaining_balance) + $item->amount;
            $info->save();
            Utilization::where('div_id', $route_no)->where('proponentinfo_id', $item->info_id)->update(['status' => 1]);
            $get = Utilization::where('div_id', $route_no)->where('proponentinfo_id', $item->info_id)->orderBy('id', 'desc')->first();
            $all = Utilization::where('proponentinfo_id', $item->info_id)->where('id', '>', $get->id)->orderBy('id', 'asc')->get();

            foreach($all as $row){
                // return str_replace(',','',$row->beginning_balance) + $item->amount;
                $row->beginning_balance = str_replace(',','',$row->beginning_balance) + $item->amount;
                $row->save();
            }
        }

        $dv3->date = $request->dv3_date;
        $dv3->facility_id = $request->dv3_facility;
        $dv3->total = (float)str_replace(',','',$request->total_amount);
        $dv3->modified_by = $userid;
        if($userid == 1027 || $userid == 2660){
            $dv3->dv_no = $request->dv_no;
            TrackingMaster::where('route_no',$route_no)->update(['dv_no'=>$request->dv_no]);
        }
        $dv3->save();        

        Dv3Fundsource::where('route_no', $route_no)->delete();

        foreach($request->info_id as $index => $id){
      
            $each = (float)str_replace(',','',$amount[$index]);

            $info = ProponentInfo::where('id', $id)->first();

            $util = new Utilization();
            $util->div_id = $dv3->route_no;
            $util->beginning_balance = $info->remaining_balance;
            $util->discount = 0;
            $util->utilize_amount = (float)str_replace(',','',$amount[$index]);
            $util->facility_id = $request->dv3_facility;
            $util->proponent_id = $info->proponent_id;
            $util->facility_used = $info->facility_id;
            $util->fundsource_id = $info->fundsource_id;
            $util->proponentinfo_id = $info->id;
            $util->status = 0;
            $util->created_by = $userid;
            $util->save();
            
            $info->remaining_balance = (float)str_replace(',','',$info->remaining_balance) - $each;
            $info->save();

            $dv3_funds = new Dv3Fundsource();
            $dv3_funds->route_no = $dv3->route_no;
            $dv3_funds->fundsource_id = $saa[$index];
            $dv3_funds->info_id = $id;
            $dv3_funds->amount = (float)str_replace(',','',$amount[$index]);
            $dv3_funds->vat = 0;
            $dv3_funds->ewt = 0;
            $dv3_funds->save();
        }
        // return $i;

        $desc = "Disbursement voucher for " . Facility::where('id', $request->dv3_facility)->value('name') . " amounting to Php " . number_format(str_replace(',', '', $request->total_amount), 2, '.', ',');
        $tracking = TrackingMaster::where('route_no', $route_no)->first();
        $tracking->description = $desc;
        $tracking->save();
        $details = TrackingDetails::where('route_no', $route_no)->first();
        $details->action = $desc;
        $details->save();

        return redirect()->back()->with('dv3_update', true);

    }

    public function createDv3(Request $request)
    {
        return view('dv3.create_dv3', [
            'facilities' => Facility::get()
        ]);
    }

    public function cloneSaa($id) {
        $randomBytes = random_bytes(16); 
        return view('dv3.saa_clone',[
            'uniqueCode' => bin2hex($randomBytes),
            'id' => $id
        ]);
    }

    public function pendingDv3(Request $request, $type){ 
        if($type == 'unsettled'){
            $dv3 = Dv3::              
            with([
                'extension' => function ($query) {
                    $query->with([
                        'proponentInfo' => function ($query) {
                            $query->with('proponent', 'fundsource');
                        }
                    ]);
                },
                'facility' => function ($query) {
                    $query->select('id','name');},
                'user' => function ($query) {
                    $query->select('userid','fname','lname');}
            ])
            ->whereNull('ors_no')
            ->orderBy('created_at', 'desc');

        }else if($type == 'processed'){
            $dv3 = Dv3::              
            with([
                'extension' => function ($query) {
                    $query->with([
                        'proponentInfo' => function ($query) {
                            $query->with('proponent', 'fundsource');
                        }
                    ]);
                },
                'facility' => function ($query) {
                    $query->select('id','name');},
                'user' => function ($query) {
                    $query->select('userid','fname','lname');}
            ])
            ->whereNotNull('ors_no')
            ->orderBy('created_at', 'desc');

        }else if($type == 'unpaid'){

            $dv3 = Dv3::              
            with([
                'extension' => function ($query) {
                    $query->with([
                        'proponentInfo' => function ($query) {
                            $query->with('proponent', 'fundsource');
                        }
                    ]);
                },
                'facility' => function ($query) {
                    $query->select('id','name');},
                'user' => function ($query) {
                    $query->select('userid','fname','lname');}
            ])
            ->whereNotNull('ors_no')
            ->whereNull('paid_by')
            ->orderBy('created_at', 'desc');

        }else if($type == 'done'){

            $dv3 = Dv3::              
            with([
                'extension' => function ($query) {
                    $query->with([
                        'proponentInfo' => function ($query) {
                            $query->with('proponent', 'fundsource');
                        }
                    ]);
                },
                'facility' => function ($query) {
                    $query->select('id','name');},
                'user' => function ($query) {
                    $query->select('userid','fname','lname');}
            ])
            ->whereNotNull('paid_by')
            ->orderBy('created_at', 'desc');

        }
        
        return view('fundsource_budget.dv3_list', [
          'dv3' => $dv3->paginate(50),
          'type' => $type
        ]);
    }

    public function processDv3($type, Request $request){

        $route_no = $request->route_no;
        $user = Auth::user()->userid;

        if($type == 'obligate'){

            $ors_no = $request->ors_no;

            $dv3 = Dv3::where('route_no', $route_no)->first();
            $dv3->obligated_by = $user;
            $dv3->ors_no = $ors_no;
            $dv3->remarks = 1;
            $dv3->save();

            $util = Utilization::where('div_id', $route_no)->where('status', 0)->get();
            foreach($util as $u){
                $amount = (float) str_replace(',','', $u->utilize_amount);
                $id = $u->fundsource_id;
                $fundsource = Fundsource::where('id', $id)->first();
                $u->budget_bbalance = $fundsource->remaining_balance;
                $u->budget_utilize = $amount;
                $u->obligated = 1;
                $u->obligated_by = Auth::user()->userid;
                $u->obligated_on = date('Y-m-d');
                $u->save();
                $fundsource->remaining_balance = (float) str_replace(',','', $fundsource->remaining_balance) - $amount;
                $fundsource->save();
            }
            return redirect()->back()->with('dv3_obligate', true);
        }else if($type == 'pay'){

            $dv3 = Dv3::where('route_no', $route_no)->first();
            $dv3->paid_by = $user;
            $dv3->remarks = 2;
            $dv3->save();

            $util = Utilization::where('div_id', $route_no)->where('status', 0)->get();
            foreach($util as $u){    
                $u->paid = 1;
                $u->paid_by = Auth::user()->userid;
                $u->paid_on = date('Y-m-d');
                $u->save();
            }
            return redirect()->back()->with('dv3_paid', true);
        }
    }
}
