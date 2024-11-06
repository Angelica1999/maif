<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patients;
use App\Models\Facility;
use App\Models\Province;
use App\Models\Muncity;
use App\Models\Barangay;
use App\Models\Fundsource;
use App\Models\Proponent;
use App\Models\Group;
use App\Models\ProponentInfo;
use App\Models\User;
use App\Models\Transfer;
use App\Models\Dv;
use App\Models\Dv2;
use App\Models\Dv3;
use App\Models\NewDV;
use App\Models\Utilization;
use App\Models\TrackingDetails;
use App\Models\PatientLogs;
use App\Models\MailHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use DataTables;
use Kyslik\ColumnSortable\Sortable;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */


    public function index(Request $request){
        // return $request->input('sort');
        // return explode(',',$request->filter_fname);

        $filter_date = $request->input('filter_dates');
        $order = $request->input('order', 'asc');


        $patients = Patients::with([
            'province:id,description',
            'muncity:id,description',
            'barangay:id,description',
            'encoded_by:userid,fname,lname,mname',
            'gl_user:username,fname,lname',
            'facility:id,name',
            'proponentData:id,proponent',
            'pat_remarks:patient_id,remarks'
        ]);
       
        //  -- for date range
        if($request->gen){
            $dateRange = explode(' - ', $filter_date);
            $start_date = date('Y-m-d', strtotime($dateRange[0]));
            $end_date = date('Y-m-d', strtotime($dateRange[1]));
            $patients = $patients ->whereBetween('created_at', [$start_date, $end_date . ' 23:59:59']);
        }

        // -- for search

        if($request->viewAll){

            $request->keyword = '';
            $request->filter_date = '';
            $request->filter_fname = '';
            $request->filter_mname = '';
            $request->filter_lname = '';
            $request->filter_facility = '';
            $request->filter_proponent = '';
            $request->filter_code = '';
            $request->filter_region = '';
            $request->filter_province = '';
            $request->filter_muncity = '';
            $request->filter_barangay = '';
            $request->filter_on = '';
            $request->filter_by = '';
            $filter_date = '';
            $request->gen = '';


        }else if($request->keyword){
            $keyword = $request->keyword;
            $patients = $patients->where(function ($query) use ($keyword) {
                $query->where('fname', 'LIKE', "%$keyword%")
                      ->orWhere('lname', 'LIKE', "%$keyword%")
                      ->orWhere('mname', 'LIKE', "%$keyword%")
                      ->orWhere('region', 'LIKE', "%$keyword%")
                      ->orWhere('other_province', 'LIKE', "%$keyword%")
                      ->orWhere('other_muncity', 'LIKE', "%$keyword%")
                      ->orWhere('other_barangay', 'LIKE', "%$keyword%")
                      ->orWhere('patient_code', 'LIKE', "%$keyword%");
            })
            ->orWhereHas('facility', function ($query) use ($keyword) {
                $query->where('name', 'LIKE', "%$keyword%");
            })
            ->orWhereHas('proponentData', function ($query) use ($keyword) {
                $query->where('proponent', 'LIKE', "%$keyword%");
            })
            ->orWhereHas('barangay', function ($query) use ($keyword) {
                $query->where('description', 'LIKE', "%$keyword%");
            })
            ->orWhereHas('muncity', function ($query) use ($keyword) {
                $query->where('description', 'LIKE', "%$keyword%");
            })
            ->orWhereHas('province', function ($query) use ($keyword) {
                $query->where('description', 'LIKE', "%$keyword%");
            });
        }

        // -- for table header sorting

        if ($request->sort && $request->input('sort') == 'facility') {
            $patients = $patients->sortable(['facility.name' => 'asc']);
        }else if ($request->sort && $request->input('sort') == 'proponent') {
            $patients = $patients->sortable(['proponentData.proponent' => 'asc']);
        }else if ($request->sort && $request->input('sort') == 'province') {
            
            $patients = $patients->leftJoin('province', 'province.id', '=', 'patients.province_id')
                            ->orderBy('patients.other_province', $request->input('order'))
                            ->orderBy('province.description', $request->input('order')) 
                            ->select('patients.*');

        }else if ($request->sort && $request->input('sort') == 'municipality') {
            
            $patients = $patients->leftJoin('muncity', 'muncity.id', '=', 'patients.muncity_id')
                            ->orderBy('patients.other_muncity', $request->input('order'))
                            ->orderBy('muncity.description', $request->input('order')) 
                            ->select('patients.*');

        }else if ($request->sort && $request->input('sort') == 'barangay') {
            
            $patients = $patients->leftJoin('barangay', 'barangay.id', '=', 'patients.barangay_id')
                            ->orderBy('patients.other_barangay', $request->input('order'))
                            ->orderBy('barangay.description', $request->input('order')) 
                            ->select('patients.*');

        }else if ($request->sort && $request->input('sort') == 'encoded_by') {
        
            $patients = $patients
                        ->orderBy(
                            \DB::connection('dohdtr')
                                ->table('users')
                                ->select('lname')
                                ->whereColumn('users.userid', 'patients.created_by'),
                                $request->input('order')
                        );
        }else{
            $patients->sortable(['id' => 'desc']);
        }
        // for filtering column

        // if($request->filter_col){
            if($request->filter_date){
                $patients = $patients->whereIn('date_guarantee_letter', explode(',',$request->filter_date));
            }
            if($request->filter_fname){
                $patients = $patients->whereIn('fname', explode(',',$request->filter_fname));
            }
            if($request->filter_mname){
                $patients = $patients->whereIn('mname', explode(',',$request->filter_date));
            }
            if($request->filter_lname){
                $patients = $patients->whereIn('lname', explode(',',$request->filter_lname));
            }
            if($request->filter_facility){
                $patients = $patients->whereIn('facility_id', explode(',',$request->filter_facility));
            }
            if($request->filter_proponent){
                $patients = $patients->whereIn('proponent_id', explode(',',$request->filter_proponent));
            }
            if($request->filter_code){
                $patients = $patients->whereIn('patient_code', explode(',',$request->filter_code));
            }
            if($request->filter_region){
                $patients = $patients->whereIn('region', explode(',',$request->filter_region));
            }
            if($request->filter_province){
                $patients = $patients->whereIn('province_id', explode(',',$request->filter_province))
                            ->orWhereIn('other_province', explode(',',$request->filter_province));
            }
            if($request->filter_municipality){
                $patients = $patients->whereIn('muncity_id', explode(',',$request->filter_municipality))
                            ->orWhereIn('other_muncity', explode(',',$request->filter_municipality));
            }
            if($request->filter_barangay){
                $patients = $patients->whereIn('barangay_id', explode(',',$request->filter_barangay))
                            ->orWhereIn('other_barangay', explode(',',$request->filter_barangay));
            }
            if($request->filter_on){
                $patients = $patients->whereIn(DB::raw('DATE(created_at)'), explode(',',$request->filter_on));
                // return  $request->filter_on;
            }
            if($request->filter_by){
                // return explode(',',$request->filter_by);
                $patients = $patients->whereIn('created_by', explode(',',$request->filter_by));
            }
        // }

        $date = clone ($patients);
        $fname = clone ($patients);
        $mname = clone ($patients);
        $lname = clone ($patients);
        $facs = clone ($patients);
        $code = clone ($patients);
        $proponent = clone ($patients);
        $region = clone ($patients);
        $province = clone ($patients);
        $muncity = clone ($patients);
        $barangay = clone ($patients);
        $on = clone ($patients);
        $by = clone ($patients);

        $fc_list = Facility::whereIn('id', $facs->groupBy('facility_id')->pluck('facility_id'))->select('id','name')->get();;
        $pros = Proponent::whereIn('id', $proponent->groupBy('proponent_id')->pluck('proponent_id'))->select('id','proponent')->get();
        $users = User::whereIn('userid', $by->groupBy('created_by')->pluck('created_by'))->select('userid','lname', 'fname')->get();
        $brgy = Barangay::whereIn('id', $barangay->groupBy('barangay_id')->pluck('barangay_id'))->select('id','description')->get();
        $mncty = Muncity::whereIn('id', $muncity->groupBy('muncity_id')->pluck('muncity_id'))->select('id','description')->get();
        $prvnc = Province::whereIn('id', $province->groupBy('province_id')->pluck('province_id'))->select('id','description')->get();
        $on = $on->groupBy(DB::raw('DATE(created_at)'))->pluck(DB::raw('MAX(DATE(created_at))'));
        $all_pat = clone ($patients);
        $proponents_code = Proponent::groupBy('proponent_code')->select(DB::raw('MAX(proponent) as proponent'), DB::raw('MAX(proponent_code) as proponent_code'),DB::raw('MAX(id) as id') )->get();
        // return $patients->paginate(10);
        return view('home', [
            'patients' => $patients->paginate(50),
            'keyword' => $request->keyword,
            'provinces' => Province::get(),
            'municipalities' => Muncity::get(),
            'proponents' => $proponents_code,
            'barangays' => Barangay::get(),
            'facilities' => Facility::get(),
            'user' => Auth::user(),
            'date' =>  $date->groupBy('date_guarantee_letter')->pluck('date_guarantee_letter'),
            'fname' => $fname->groupBy('fname')->pluck('fname'),
            'mname' => $mname->groupBy('mname')->pluck('mname'),
            'lname' => $lname->groupBy('lname')->pluck('lname'),
            'fc_list' => $fc_list,
            'pros' => $pros,
            'code' => $code->groupBy('patient_code')->pluck('patient_code'),
            'region' => $region->groupBy('region')->pluck('region'),
            'pro1' => $province->groupBy('other_province')->pluck('other_province'),
            'prvnc' => $prvnc,
            'muncity' => $province->groupBy('other_muncity')->pluck('other_muncity'),
            'mncty' => $mncty,
            'barangay' => $barangay->groupBy('other_barangay')->pluck('other_barangay'),
            'brgy' => $brgy,
            'on' => $on,
            'by' => $users,
            'filter_date' => explode(',',$request->filter_date),
            'filter_fname' => explode(',',$request->filter_fname),
            'filter_mname' => explode(',',$request->filter_mname),
            'filter_lname' => explode(',',$request->filter_lname),
            'filter_facility' => explode(',',$request->filter_facility),
            'filter_proponent' => explode(',',$request->filter_proponent),
            'filter_code' => explode(',',$request->filter_code),
            'filter_region' => explode(',',$request->filter_region),
            'filter_province' => explode(',',$request->filter_province),
            'filter_municipality' => explode(',',$request->filter_municipality),
            'filter_barangay' => explode(',',$request->filter_barangay),
            'filter_on' => explode(',',$request->filter_on),
            'filter_by' => explode(',',$request->filter_by),
            'generate_dates' => $filter_date,
            'gen' => $request->gen,
            'order' => $order,
            'id_pat' => ''
        ]);
     }

    public function fetchAdditionalData(){
        return [
            'all_pat' => Patients::get(),
            'proponents' => Proponent::get()
        ];
    }

    public function updateGl($id){
        return Patients::where('id', $id)->first();
    }

    public function report(Request $request){

        $proponents = Proponent::groupBy('pro_group')->select(DB::raw('MAX(proponent) as proponent'), DB::raw('MAX(pro_group) as pro_group'),DB::raw('MAX(id) as id') );
        
        if($request->viewAll){
            $request->keyword = '';
        }else if($request->keyword){
            $proponents->where('proponent', 'LIKE', "%$request->keyword%");
        }
        $proponents = $proponents->orderBy('id', 'desc')->paginate(15);

        return view('report', ['proponents'=> $proponents, 'keyword'=>$request->keyword]);
    }

    public function reportFacility(Request $request){
        // $facilities = ProponentInfo::groupBy('facility_id')
        //     ->select(DB::raw('MAX(facility_id) as facility_id'))
        //     ->with(['facility' => function ($query) use ($request) {
        //         if (!$request->viewAll && $request->keyword) {
        //             $query->where('name', 'LIKE', "%$request->keyword%");
        //         }
        //     }])
        //     ->paginate(15);
        $facilities = Facility::when(!$request->viewAll && $request->keyword, function ($query) use ($request) {
            $query->where('name', 'LIKE', "%$request->keyword%");
        })
        ->paginate(15);

        if($request->viewAll){
            $request->keyword = '';
        }

        return view('report.facility_report', ['facilities'=>$facilities, 'keyword' => $request->keyword]);
    }

    public function getProponentReport($pro_group){
        $proponentIds = Proponent::where('pro_group', $pro_group)->pluck('id')->toArray();
        $utilization = Utilization::whereIn('proponent_id', $proponentIds)
            ->select( DB::raw('MAX(utilization.div_id) as route_no'), DB::raw('MAX(utilization.utilize_amount) as utilize_amount'),  
                DB::raw('MAX(proponent.proponent) as proponent_name'), DB::raw('MAX(utilization.created_at) as created_at'),
                DB::raw('MAX(utilization.created_by) as created_by'), DB::raw('MAX(utilization.facility_id) as facility_id'),
                DB::raw('MAX(utilization.fundsource_id) as fundsource_id'), DB::raw('MAX(utilization.transfer_id) as transfer_id'),
                DB::raw('MAX(utilization.status) as status'), DB::raw('MAX(utilization.id) as id'))
            ->groupBy(DB::raw('CASE WHEN utilization.div_id = 0 THEN utilization.id ELSE utilization.div_id END'))
            ->leftJoin('proponent', 'proponent.id', '=', 'utilization.proponentinfo_id')
            ->with('fundSourcedata')
            ->with('facilitydata:id,name,address')
            ->with('user:id,userid,fname,lname,mname')
            ->orderBy('id', 'asc')
            ->get();
        $proponent = Proponent::where('pro_group', $pro_group)->first();
        $title = $proponent->proponent;
        $filename = $title.'.xls';
        header("Content-Type: application/xls");
        header("Content-Disposition: attachment; filename=$filename");
        header("Pragma: no-cache");
        header("Expires: 0");
        $table_body = "<tr>
                <th>Route No</th>
                <th>SAA</th>
                <th>Facility</th>
                <th>Allocation</th>
                <th>Utilize Amount</th>
                <th>Percentage</th>
                <th>Discount</th>
                <th>Balance</th>
                <th>Patients</th>
                <th>Created On</th>
            </tr>";
            // return $utilization;
        $all = ProponentInfo::whereIn('proponent_id', $proponentIds)->get();
        $all_id = ProponentInfo::whereIn('proponent_id', $proponentIds)->pluck('id')->toArray();

        $allocation_funds = $all->sum(function ($info) {
            return (float) str_replace(',', '', $info->alocated_funds);
        });  

        $deduct = 0;
        if($utilization){

            foreach($utilization as $row) {

                if($row->status !== 1){
                    $user = $row->user->lname .', '. $row->user->fname .' '. $row->user->mname;
                    $created_on = date('F j, Y', strtotime($row->created_at));
                    $saa = $row->fundSourcedata->saa;

                    if($row->route_no == 0){
                        // to be open and recalculated once it is finalize to transfer funds to another proponent
                        // $transfer = Transfer::where('id', $row->transfer_id)->with('to_fundsource')->with('from_fundsource')->with('to_facilityInfo')->with('from_facilityInfo')->first();
                        
                        // $from_fac = array_map(fn($value) => (int)$value, json_decode($transfer->from_facility));
                        // $from_facilities =  Facility::whereIn('id', $from_fac)->pluck('name')->toArray();
                        // $to_fac = array_map(fn($value) => (int)$value, json_decode($transfer->to_facility));
                        // $to_facilities =  Facility::whereIn('id', $to_fac)->pluck('name')->toArray();

                        // if($row->status == 2){ //2-deducted 
                        //     $allocation_funds = $allocation_funds + str_replace(',','', $transfer->from_amount);
                        //     $rem_bal = $allocation_funds - str_replace(',','', $transfer->from_amount);
                        //     $transfer_rem = 'Transfer (deducted)';
                        //     // $allocation_funds = 
                        // }else if($row->status == 3){ // 3 -added
                        //     $allocated = $allocation_funds - str_replace(',','', $transfer->to_amount);
                        //     $rem_bal = $allocation_funds + str_replace(',','', $transfer->to_amount);
                        //     $transfer_rem = 'Transfer (added)';
                        // }
                        // // return $from_facilities;

                        // $facility_new = 'from '. $transfer->from_fundsource->saa.' - '.  implode(',', $from_facilities).' to '. $transfer->to_fundsource->saa.' - '.  implode(',', $to_facilities);
                        // $table_body .= "<tr>
                        //     <td style='vertical-align:top;'>$transfer_rem</td>
                        //     <td style='vertical-align:top;'>$saa</td>
                        //     <td style='vertical-align:top;'>$facility_new</td>
                        //     <td style='vertical-align:top;'>$allocated</td>
                        //     <td style='vertical-align:top;'>$row->utilize_amount</td>
                        //     <td style='vertical-align:top;'></td>
                        //     <td style='vertical-align:top;'></td>
                        //     <td style='vertical-align:top;'>$rem_bal</td>
                        //     <td style='vertical-align:top;'></td>
                        //     <td style='vertical-align:top;'>$created_on</td>
                        // </tr>";
                    }else{
                        $facility = $row->facilitydata->name;
                        $dv = Dv::where('route_no', $row->route_no)->first();
                        $dv3 = Dv3::where('route_no', $row->route_no)->with('extension')->first();
                        $new_dv = NewDV::where('route_no', $row->route_no)->first();

                        if($dv){
                            $saa_Ids = json_decode($dv->fundsource_id);
                            $saa_name = Fundsource::whereIn('id',$saa_Ids)->pluck('saa')->toArray();
                            $saaString = implode('<br>', $saa_name);
                            $groupIdArray = explode(',', $dv->group_id);
                            $patients = Patients::whereIn('group_id', $groupIdArray)->get();
                            $patient_list = [];
                            foreach($patients as $patient){
                                $patient_list[] = $patient->lname.', '. $patient->fname .' '. $patient->mname;
                            }
                            $string_patient =  implode('<br>', $patient_list);
                            $trap = 1;
                            if($dv->deduction1 >3){
                                $trap = 1.12;
                            }
                            $amount1 = str_replace(',', '', $dv->amount1);
                            $amount2 = str_replace(',', '', $dv->amount2);
                            $amount3 = str_replace(',', '', $dv->amount3);
                
                            $discount1 = !empty($dv->amount1)? floatval($amount1/$trap * $dv->deduction1/100) + floatval($amount1/$trap * $dv->deduction2/100) :'';
                            $discount2 = !empty($dv->amount2)? floatval($amount2/$trap * $dv->deduction1/100) + floatval($amount1/$trap * $dv->deduction2/100) :'';
                            $discount3 = !empty($dv->amount3)? floatval($amount3/$trap * $dv->deduction1/100) + floatval($amount1/$trap * $dv->deduction2/100) :'';
                
                            $amounts = array_filter([
                                $dv->amount1 !== null ? $dv->amount1 : null,
                                $dv->amount2 !== null ? $dv->amount2 : null,
                                $dv->amount3 !== null ? $dv->amount3 : null,
                            ]);
                            $discounts = array_filter([
                                $discount1 !== null ? $discount1 : null,
                                $discount2 !== null ? $discount2 : null,
                                $discount3 !== null ? $discount3 : null,
                            ]);
                            $all_amount = implode('<br>', $amounts);
                            $rem_bal =  $allocation_funds - str_replace(',','', $dv->total_amount);
                            $discount_all = implode('<br>', $discounts);
                            $percentage = number_format((str_replace(',', '', $dv->total_amount) / $allocation_funds) * 100, 2);
                            $al_disp = number_format($allocation_funds, 2);
                            $rem_disp = number_format($rem_bal, 2);
                        }else if($dv3){

                            $saa_ids = [];
                            $amounts = [];
                            $amount_total = 0;

                            foreach($dv3->extension as $row){

                                if (in_array($row->info_id, $all_id)) {
                                    $saa_ids [] = $row->fundsource_id;
                                    $amounts [] = $row->amount;
                                    $amount_total = $amount_total + $row->amount;
                                }
                            }

                            $saa_name = Fundsource::whereIn('id',$saa_ids)->pluck('saa')->toArray();
                            $saaString = implode('<br>', $saa_name);
                            $all_amount = implode('<br>', $amounts);
                            $rem_bal =  $allocation_funds - str_replace(',','', $amount_total);
                            $percentage = number_format((str_replace(',', '', $amount_total) / $allocation_funds) * 100, 2);
                            $al_disp = number_format($allocation_funds, 2);
                            $rem_disp = number_format($rem_bal, 2);
                            $discount_all = 0;
                            $string_patient = '';
                        }elseif($new_dv){

                            $saa_ids = [];
                            $amounts = [];
                            $amount_total = 0;

                            $util = Utilization::where('div_id', $row->route_no)->where('status', 0)->whereIn('proponent_id', $proponentIds)->get();
                       
                            foreach($util as $u){
                                $saa_ids [] = $u->fundsource_id;
                                $amounts [] = $u->utilize_amount;
                                $amount_total = $amount_total +str_replace(',','', $u->utilize_amount);
                            }
                            
                           
                            $saa_name = Fundsource::whereIn('id',$saa_ids)->pluck('saa')->toArray();
                            $saaString = implode('<br>', $saa_name);
                            $all_amount = implode('<br>', $amounts);
                            $rem_bal =  $allocation_funds - str_replace(',','', $amount_total);
                            $percentage = number_format((str_replace(',', '', $amount_total) / $allocation_funds) * 100, 2);
                            $al_disp = number_format($allocation_funds, 2);
                            $rem_disp = number_format($rem_bal, 2);
                            $discount_all = 0;
                            $string_patient = '';
                        }
                        
                        $table_body .= "<tr>
                            <td style='vertical-align:top;'>$row->route_no</td>
                            <td style='vertical-align:top;'>$saaString</td>
                            <td style='vertical-align:top;'>$facility</td>
                            <td style='vertical-align:top;'>$al_disp</td>
                            <td style='vertical-align:top;'>$all_amount</td>
                            <td style='vertical-align:top;'>$percentage %</td>
                            <td style='vertical-align:top;'>$discount_all</td>
                            <td style='vertical-align:top;'>$rem_disp</td>
                            <td style='vertical-align:top;'>$string_patient</td>
                            <td style='vertical-align:top;'>$created_on</td>
                        </tr>";
                        $allocation_funds = $rem_bal;
                    }
                }
            }
        }else{
            $table_body .= "<tr>
                <td colspan=6 style='vertical-align:top;'>No Data Available</td>
            </tr>";
        }
        $display =
            '<h1>'.$title.'</h1>'.
            '<table cellspacing="1" cellpadding="5" border="1">'.$table_body.'</table>';

        return $display;
    }
    public function getFacilityReport($facility_id){
        $utilize = Utilization::
                        where('facility_id', $facility_id)
                    ->orWhereJsonContains('facility_id', $facility_id)
                    ->with('facilitydata')->with('fundSourcedata')->with('proponentdata')->where('status', '<>', '1')->get();
        // return count($utilize);
        $title = Facility::where('id', $facility_id)->value('name');
        $filename = $title.'.xls';
        header("Content-Type: application/xls");
        header("Content-Disposition: attachment; filename=$filename");
        header("Pragma: no-cache");
        header("Expires: 0");
        $table_body = "<tr>
                <th>Fundsource</th>
                <th>Proponent</th>
                <th>Utilize Amount</th>
                <th>Discount</th>
                <th>Remarks</th>
                <th>Created On</th>
            </tr>";
        if($utilize){
            foreach($utilize as $row){
                $created_on = date('F j, Y', strtotime($row->created_at));
                // return $row->created_at;
                $saa = $row->fundSourcedata->saa;
                $proponent = $row->proponentdata->proponent;
                $discount = $row->discount;
                $utilize = $row->utilize_amount;
                $remarks = "processed";
                if($row->status ==2){ //from

                    $transfer = Transfer::where('id', $row->transfer_id)->with('to_fundsource')->with('to_facilityInfo')->with('to_proponentInfo')->first();
                          if(is_string($transfer->from_facility)){
                            $facility_n = Facility::whereIn('id',array_map('intval', json_decode($transfer->from_facility)))->pluck('name')->toArray();
                            $facility_n = implode(', ', $facility_n);
                          }else{
                            $facility_n = Facility::where('id', $transfer->from_facility)->value('name');
                          }
                    $remarks = 'transferred to '.$transfer->to_proponentInfo->proponent.' - '.$transfer->to_fundsource->saa.' - '. $facility_n;

                }else if($row->status ==3){ //to

                    $transfer = Transfer::where('id', $row->transfer_id)->with('from_fundsource')->with('from_facilityInfo')->with('from_proponentInfo')->first();
                    if(is_string($transfer->to_facility)){
                        $facility_n = Facility::whereIn('id',array_map('intval', json_decode($transfer->to_facility)))->pluck('name')->toArray();
                        $facility_n = implode(', ', $facility_n);
                      }else{
                        $facility_n = Facility::where('id', $transfer->to_facility)->value('name');
                      }
                    $remarks = 'transferred from '.$transfer->from_proponentInfo->proponent.' - '.$transfer->from_fundsource->saa.' - '. $facility_n;
                }
                $table_body .= "<tr>
                    <td style='vertical-align:top;'>$saa</td>
                    <td style='vertical-align:top;'>$proponent</td>
                    <td style='vertical-align:top;'>$utilize</td>
                    <td style='vertical-align:top;'>$discount</td>
                    <td style='vertical-align:top;'>$remarks</td>
                    <td style='vertical-align:top;'>$created_on</td>
                    </tr>";
            }
        }
        
        $display =
            '<h1>'.$title.'</h1>'.
            '<table cellspacing="1" cellpadding="5" border="1">'.$table_body.'</table>';

        return $display;
    }

    public function updateAmount($patientId, $amount){

        $patient = Patients::find($patientId);
        $newAmount = str_replace(',', '',$amount);

        if (!$patient) {
            return response()->json(['error' => 'Patient not found'], 404);
        }else{
            if($patient->group_id !== null && $patient->group_id !== ""){
                $group = Group::where('id', $patient->group_id)->first();
                $updated_a = floatval(str_replace(',', '', $group->amount)) - floatval($patient->actual_amount) + floatval($newAmount);
                $stat = $group->status;
                $group->status = 1;
                $group->amount = number_format($updated_a, 2, '.',',');
                $group->save();
            }
            $patient->actual_amount = $newAmount;
            $patient->save();
            // session()->flash('actual_amount', true);
        }
    }
    
    public function group(Request $request){ 
        $groups = Group::with('proponent', 'facility', 'user', 'patient')
            ->when(
            !$request->viewAll && $request->filled('keyword'),
            function ($query) use ($request) {
                $query->where(function ($subquery) use ($request) {
                    $subquery->whereHas('proponent', function ($proponentQuery) use ($request) {
                        $proponentQuery->where('proponent', 'LIKE', "%{$request->keyword}%");
                    })
                    ->orWhereHas('patient', function ($patientQuery) use ($request) {
                        $patientQuery->where('lname', 'LIKE', "%{$request->keyword}%");
                    });
                });
            }
        )
        ->withCount('patient')
        ->orderBy('id', 'desc')
        ->paginate(50); 
        
        if($request->viewAll){
            $request->keyword ='';
        } 
        return view('group.group', ['groups'=>$groups, 'keyword'=>$request->keyword]);
    }
  
    public function getPatientGroup($group_id){
        $patient_list = Patients::where('group_id', $group_id)->with('muncity')->with('province')->with('barangay')->get();
        return view('group.patients_group', ['patient_list'=>$patient_list, 'group'=>Group::where('id', $group_id)->first()]);
    }

    public function getPatient($patient_id){
        $patient = Patients::where('id', $patient_id)->first();
        $group = Group::where('id', $patient->group_id)->first();
        $amount = str_replace(',','',$group->amount) - str_replace(',','', $patient->actual_amount);
        $stat = $group->status;
        $group->status = 1;
        $group->amount = $amount;
        $group->save();
        $patient->group_id = null;
        $patient->save();   
        if($stat == 0){
            session()->flash('update_group', true);
        }
          session()->flash('remove_patientgroup', true); 
    }
    public function getPatients($facility_id, $proponent_id){
        return Patients::where(function($query) {
            $query->whereNull('group_id')
                  ->orWhere('group_id', '=', '');
        })
        ->whereNotNull('actual_amount')
        ->where('facility_id', $facility_id)
        ->where('proponent_id', $proponent_id)
        ->where('actual_amount', '!=', 0)
        ->get();

    }

    public function updateGroupList(Request $request){
        
        $patient = Patients::where('id', $request->input('fac_id'))->first();
        $group = Group::where('id', $request->input('group_id'))->first();
        $amount = str_replace(',','',$group->amount) + str_replace(',','', $patient->actual_amount);
        $group->amount = $amount;
        $stat = $group->status;
        $group->status = 1;
        $group->save();
        $patient->group_id = $request->input('group_id');
        $patient->save();
        return redirect()->back()->with('save_patientgroup', true);
        if($stat == 0){
            session()->flash('update_group', true);
        }
    }

    public function saveGroup(Request $request){

        $patients = $request->input('group_patients');
        $patientsArray = explode(',', $patients);
        $group = new Group();
        $group->facility_id = $request->input('group_facility');
        $group->proponent_id = $request->input('group_proponent');
        $group->grouped_by = Auth::user()->userid;
        $group->amount = $request->input('group_amountT');
        $group->status = 1;
        $group->save();
        Patients::whereIn('id', $patientsArray)->update(['group_id' => $group->id]);
        return redirect()->back()->with('save_group', true);
    }

    public function createPatientSave(Request $request) {
        $data = $request->all();
        Patients::create($request->all());
        $patientCount = Patients::where('fname', $request->fname)
            ->where('lname', $request->lname)
            ->where('mname', $request->mname)
            ->where('region', $request->region)
            ->where('province_id', $request->province_id)
            ->where('muncity_id', $request->muncity_id)
            ->where('barangay_id', $request->barangay_id)
            ->count();
        if($patientCount>0){
            session()->flash('patient_exist', $patientCount);
        }else{
            session()->flash('patient_save', true);
        }

        return redirect()->back();
    }

    public function fetchPatient($id){
        $patient =  Patients::where('id',$id)
                        ->with(
                            [
                                'muncity' => function ($query) {
                                    $query->select(
                                        'id',
                                        'description'
                                    );
                                },
                                'barangay' => function ($query) {
                                    $query->select(
                                        'id',
                                        'description'
                                    );
                                },
                                'fundsource',
                            ])->orderBy('updated_at', 'desc')
                        ->first();

        $municipal = Muncity::select('id', 'description')->get();
        $barangay = Barangay::select('id', 'description')->get();
        return [
            'patient' => $patient
        ];        
    }

    //sir jondy unused
    public function editPatient(Request $request) {
        $patient =  Patients::where('id',$request->patient_id)
                        ->with(
                            [
                                'muncity' => function ($query) {
                                    $query->select(
                                        'id',
                                        'description'
                                    );
                                },
                                'barangay' => function ($query) {
                                    $query->select(
                                        'id',
                                        'description'
                                    );
                                },
                                'fundsource',
                            ])->orderBy('updated_at', 'desc')
                        ->first();

        $municipal = Muncity::select('id', 'description')->get();
        $barangay = Barangay::select('id', 'description')->get();
        return view('maif.update_patient',[
            'provinces' => Province::get(),
            'fundsources' => Fundsource::get(),
            'proponents' => Proponent::get(),
            'facility' => Facility::get(),
            'patient' => $patient,
            'municipal' => $municipal,
            'barangay' => $barangay,
        ]);
    }

    public function mailHistory($id){
        return view('maif.mail_history',[
            'history' => MailHistory::where('patient_id', $id)->with('patient', 'sent', 'modified')->get()
        ]);
    }

    public function patientHistory($id){
        return view('maif.patient_history',[
            'logs' => PatientLogs::where('patient_id', $id)->with('modified', 'facility', 'province', 'muncity', 'barangay', 'proponent')->get()
        ]);
    }
 
    public function updatePatient($id, Request $request){
        $val = $request->input('update_send');
        
        $patient_id = $id;
        $patient = Patients::where('id', $patient_id)->first();

        if(!$patient){
            return redirect()->back()->with('error', 'Patient not found');
        }

        DB::beginTransaction();

        $patientLogs = new PatientLogs();
        $patientLogs->patient_id = $patient->id;
        $patientLogs->fill(Arr::except($patient->toArray(), ['status', 'sent_type', 'user_type', 'transd_id']));
        unset($patientLogs->id);
        $patientLogs->save();
        
        session()->flash('patient_update', true);
        $patient->fname = $request->input('fname');
        $patient->lname = $request->input('lname');
        $patient->mname = $request->input('mname');
        $patient->dob   = $request->input('dob');
        $patient->region = $request->input('region');

        if($patient->region !== "Region 7"){
            $patient->other_province = $request->input('other_province');
            $patient->other_muncity = $request->input('other_muncity');
            $patient->other_barangay = $request->input('other_barangay');
        }

        $patient->date_guarantee_letter = $request->input('date_guarantee_letter');
        $patient->province_id = $request->input('province_id');
        $patient->muncity_id  = $request->input('muncity_id');
        $patient->barangay_id = $request->input('barangay_id');
        // $patient->fundsource_id = $request->input('fundsource_id');
        $patient->proponent_id = $request->input('proponent_id');
        $patient->facility_id = $request->input('facility_id');
        $patient->patient_code = $request->input('patient_code');
        $patient->guaranteed_amount = $request->input('guaranteed_amount');
        $patient->actual_amount = $request->input('actual_amount');
        $patient->remaining_balance = $request->input('remaining_balance');
        $patient->pat_rem = $request->input('pat_rem');
        $patient->sent_type = $request->input('sent_type');
        $patient->save();
        DB::commit();
        if($val == "upsend"){
            // return Patients::where('id', $patient->id)->first();
            return redirect()->route('patient.sendpdf', ['patientid' => $patient->id]);
        }else{
            return redirect()->back();
        }

        return redirect()->back()->with('patient_update', true);

    }

    public function removePatient($id){
        if($id){
            Patients::where('id', $id)->delete();
        }
        return redirect()->back()->with('remove_patient', true);
    }

    public function groupRemovePatient($id){
        $pat = Patients::where('id', $id)->first();
        if($pat){
            $gr = Group::where('id', $pat->group_id)->first();
            $gr->amount = (double)str_replace(',', '', $gr->amount) - (double)str_replace(',', '', $pat->actual_amount);
            $gr->save();
            $pat->group_id = null;
            $pat->save();
        }
    }

    public function muncityGet(Request $request) {
        return Muncity::where('province_id',$request->province_id)->whereNull('vaccine_used')->get();
    }

    public function barangayGet(Request $request) {
        return Barangay::where('muncity_id',$request->muncity_id)->get();
    }

    public function transactionGet() {
        $facilities = Facility::where('hospital_type','private')->get();
        return view('fundsource.transaction',[
            'facilities' => $facilities
        ]);
    }

    public function facilitySend($id){

        Patients::where('id', $id)->update(['status' => 1]);
        return redirect()->back()->with('facility_send',true);
        
    }   

    public function returnPatient($id, Request $request){

        $val = $request->input('update_send');
        
        $patient_id = $id;
        $patient = Patients::where('id', $patient_id)->first();

        if(!$patient){
            return redirect()->back()->with('error', 'Patient not found');
        }

        DB::beginTransaction();

        $patientLogs = new PatientLogs();
        $patientLogs->patient_id = $patient->id;
        $patientLogs->fill(Arr::except($patient->toArray(), ['status', 'sent_type', 'user_type']));
        unset($patientLogs->id);
        $patientLogs->save();
        
        $patient->pat_rem = $request->input('pat_rem');
        $patient->sent_type = $request->input('sent_type');
        $patient->save();
        DB::commit();
        
        return redirect()->back()->with('return_gl', true);
    }

    public function patients(Request $request){

        $filter_date = $request->input('filter_dates');
        $order = $request->input('order', 'asc');


        $patients = Patients::where('user_type', 1)
            ->where('sent_type','!=', 1)->with([
            'province:id,description',
            'muncity:id,description',
            'barangay:id,description',
            'encoded_by:userid,fname,lname,mname',
            'gl_user:username,fname,lname',
            'facility:id,name',
            'proponentData:id,proponent',
            'pat_remarks:patient_id,remarks'
        ]);
       
        //  -- for date range
        if($request->gen){
            $dateRange = explode(' - ', $filter_date);
            $start_date = date('Y-m-d', strtotime($dateRange[0]));
            $end_date = date('Y-m-d', strtotime($dateRange[1]));
            $patients = $patients ->whereBetween('created_at', [$start_date, $end_date . ' 23:59:59']);
        }

        // -- for search

        if($request->viewAll){

            $request->keyword = '';
            $request->filter_date = '';
            $request->filter_fname = '';
            $request->filter_mname = '';
            $request->filter_lname = '';
            $request->filter_facility = '';
            $request->filter_proponent = '';
            $request->filter_code = '';
            $request->filter_region = '';
            $request->filter_province = '';
            $request->filter_muncity = '';
            $request->filter_barangay = '';
            $request->filter_on = '';
            $request->filter_by = '';
            $filter_date = '';
            $request->gen = '';


        }else if($request->keyword){
            $keyword = $request->keyword;
            
            $patients = $patients->where('user_type', 1)->where('sent_type','!=', 1)->where(function ($query) use ($keyword) {
                $query->where('fname', 'LIKE', "%$keyword%")
                      ->orWhere('lname', 'LIKE', "%$keyword%")
                      ->orWhere('mname', 'LIKE', "%$keyword%")
                      ->orWhere('region', 'LIKE', "%$keyword%")
                      ->orWhere('other_province', 'LIKE', "%$keyword%")
                      ->orWhere('other_muncity', 'LIKE', "%$keyword%")
                      ->orWhere('other_barangay', 'LIKE', "%$keyword%")
                      ->orWhere('patient_code', 'LIKE', "%$keyword%");
            })
            ->orWhere(function ($query) use ($keyword) {
                $query->whereHas('facility', function ($query) use ($keyword) {
                    $query->where('name', 'LIKE', "%$keyword%");
                })
                ->orWhereHas('proponentData', function ($query) use ($keyword) {
                    $query->where('proponent', 'LIKE', "%$keyword%");
                })
                ->orWhereHas('barangay', function ($query) use ($keyword) {
                    $query->where('description', 'LIKE', "%$keyword%");
                })
                ->orWhereHas('muncity', function ($query) use ($keyword) {
                    $query->where('description', 'LIKE', "%$keyword%");
                })
                ->orWhereHas('province', function ($query) use ($keyword) {
                    $query->where('description', 'LIKE', "%$keyword%");
                });
            });
        }

        // -- for table header sorting

        if ($request->sort && $request->input('sort') == 'facility') {
            $patients = $patients->sortable(['facility.name' => 'asc']);
        }else if ($request->sort && $request->input('sort') == 'proponent') {
            $patients = $patients->sortable(['proponentData.proponent' => 'asc']);
        }else if ($request->sort && $request->input('sort') == 'province') {
            
            $patients = $patients->leftJoin('province', 'province.id', '=', 'patients.province_id')
                            ->orderBy('patients.other_province', $request->input('order'))
                            ->orderBy('province.description', $request->input('order')) 
                            ->select('patients.*');

        }else if ($request->sort && $request->input('sort') == 'municipality') {
            
            $patients = $patients->leftJoin('muncity', 'muncity.id', '=', 'patients.muncity_id')
                            ->orderBy('patients.other_muncity', $request->input('order'))
                            ->orderBy('muncity.description', $request->input('order')) 
                            ->select('patients.*');

        }else if ($request->sort && $request->input('sort') == 'barangay') {
            
            $patients = $patients->leftJoin('barangay', 'barangay.id', '=', 'patients.barangay_id')
                            ->orderBy('patients.other_barangay', $request->input('order'))
                            ->orderBy('barangay.description', $request->input('order')) 
                            ->select('patients.*');

        }else if ($request->sort && $request->input('sort') == 'encoded_by') {
        
            $patients = $patients
                        ->orderBy(
                            \DB::connection('dohdtr')
                                ->table('users')
                                ->select('lname')
                                ->whereColumn('users.userid', 'patients.created_by'),
                                $request->input('order')
                        );
        }else{
            $patients->sortable(['id' => 'desc']);
        }
        // for filtering column

        // if($request->filter_col){
            if($request->filter_date){
                $patients = $patients->whereIn('date_guarantee_letter', explode(',',$request->filter_date));
            }
            if($request->filter_fname){
                $patients = $patients->whereIn('fname', explode(',',$request->filter_fname));
            }
            if($request->filter_mname){
                $patients = $patients->whereIn('mname', explode(',',$request->filter_date));
            }
            if($request->filter_lname){
                $patients = $patients->whereIn('lname', explode(',',$request->filter_lname));
            }
            if($request->filter_facility){
                $patients = $patients->whereIn('facility_id', explode(',',$request->filter_facility));
            }
            if($request->filter_proponent){
                $patients = $patients->whereIn('proponent_id', explode(',',$request->filter_proponent));
            }
            if($request->filter_code){
                $patients = $patients->whereIn('patient_code', explode(',',$request->filter_code));
            }
            if($request->filter_region){
                $patients = $patients->whereIn('region', explode(',',$request->filter_region));
            }
            if($request->filter_province){
                $patients = $patients->whereIn('province_id', explode(',',$request->filter_province))
                            ->orWhereIn('other_province', explode(',',$request->filter_province));
            }
            if($request->filter_municipality){
                $patients = $patients->whereIn('muncity_id', explode(',',$request->filter_municipality))
                            ->orWhereIn('other_muncity', explode(',',$request->filter_municipality));
            }
            if($request->filter_barangay){
                $patients = $patients->whereIn('barangay_id', explode(',',$request->filter_barangay))
                            ->orWhereIn('other_barangay', explode(',',$request->filter_barangay));
            }
            if($request->filter_on){
                $patients = $patients->whereIn(DB::raw('DATE(created_at)'), explode(',',$request->filter_on));
                // return  $request->filter_on;
            }
            if($request->filter_by){
                // return explode(',',$request->filter_by);
                $patients = $patients->whereIn('created_by', explode(',',$request->filter_by));
            }
        // }

        $date = clone ($patients);
        $fname = clone ($patients);
        $mname = clone ($patients);
        $lname = clone ($patients);
        $facs = clone ($patients);
        $code = clone ($patients);
        $proponent = clone ($patients);
        $region = clone ($patients);
        $province = clone ($patients);
        $muncity = clone ($patients);
        $barangay = clone ($patients);
        $on = clone ($patients);
        $by = clone ($patients);

        $fc_list = Facility::whereIn('id', $facs->groupBy('facility_id')->pluck('facility_id'))->select('id','name')->get();;
        $pros = Proponent::whereIn('id', $proponent->groupBy('proponent_id')->pluck('proponent_id'))->select('id','proponent')->get();
        $users = User::whereIn('userid', $by->groupBy('created_by')->pluck('created_by'))->select('userid','lname', 'fname')->get();
        $brgy = Barangay::whereIn('id', $barangay->groupBy('barangay_id')->pluck('barangay_id'))->select('id','description')->get();
        $mncty = Muncity::whereIn('id', $muncity->groupBy('muncity_id')->pluck('muncity_id'))->select('id','description')->get();
        $prvnc = Province::whereIn('id', $province->groupBy('province_id')->pluck('province_id'))->select('id','description')->get();
        $on = $on->groupBy(DB::raw('DATE(created_at)'))->pluck(DB::raw('MAX(DATE(created_at))'));
        $all_pat = clone ($patients);
        $proponents_code = Proponent::groupBy('proponent_code')->select(DB::raw('MAX(proponent) as proponent'), DB::raw('MAX(proponent_code) as proponent_code'),
            DB::raw('MAX(id) as id') )->get();
     
            // return $patients;   
        return view('maif.proponent_patient', [
            'patients' => $patients->where('patients.user_type', 1)->where('sent_type','!=', 1)->paginate(50),
            'keyword' => $request->keyword,
            'provinces' => Province::get(),
            'municipalities' => Muncity::get(),
            'proponents' => $proponents_code,
            'barangays' => Barangay::get(),
            'facilities' => Facility::get(),
            'user' => Auth::user(),
            'date' =>  $date->groupBy('date_guarantee_letter')->pluck('date_guarantee_letter'),
            'fname' => $fname->groupBy('fname')->pluck('fname'),
            'mname' => $mname->groupBy('mname')->pluck('mname'),
            'lname' => $lname->groupBy('lname')->pluck('lname'),
            'fc_list' => $fc_list,
            'pros' => $pros,
            'code' => $code->groupBy('patient_code')->pluck('patient_code'),
            'region' => $region->groupBy('region')->pluck('region'),
            'pro1' => $province->groupBy('other_province')->pluck('other_province'),
            'prvnc' => $prvnc,
            'muncity' => $province->groupBy('other_muncity')->pluck('other_muncity'),
            'mncty' => $mncty,
            'barangay' => $barangay->groupBy('other_barangay')->pluck('other_barangay'),
            'brgy' => $brgy,
            'on' => $on,
            'by' => $users,
            'filter_date' => explode(',',$request->filter_date),
            'filter_fname' => explode(',',$request->filter_fname),
            'filter_mname' => explode(',',$request->filter_mname),
            'filter_lname' => explode(',',$request->filter_lname),
            'filter_facility' => explode(',',$request->filter_facility),
            'filter_proponent' => explode(',',$request->filter_proponent),
            'filter_code' => explode(',',$request->filter_code),
            'filter_region' => explode(',',$request->filter_region),
            'filter_province' => explode(',',$request->filter_province),
            'filter_municipality' => explode(',',$request->filter_municipality),
            'filter_barangay' => explode(',',$request->filter_barangay),
            'filter_on' => explode(',',$request->filter_on),
            'filter_by' => explode(',',$request->filter_by),
            'generate_dates' => $filter_date,
            'gen' => $request->gen,
            'order' => $order,
            'id_pat' => ''
        ]);
     }
}
