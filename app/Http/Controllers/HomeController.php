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
use App\Models\Utilization;
use App\Models\TrackingDetails;
use App\Models\PatientLogs;
use App\Models\MailHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
        $patients = Patients::with([
            'province' => function ($query) {
                $query->select('id', 'description');
            },
            'muncity' => function ($query) {
                $query->select('id', 'description');
            },
            'barangay' => function ($query) {
                $query->select('id', 'description');
            },
            'encoded_by' => function ($query) {
                $query->select('userid', 'fname', 'lname');
            },
            'facility' => function ($query) {
                $query->select('id','name');
            },
            'proponentData' => function ($query) {
                $query->select('id','proponent');
            }
        ]);
        //for search
        if ($request->viewAll) {
            $request->keyword = '';
        } elseif ($request->keyword) {
            $patients = $patients->where('fname', 'LIKE', "%$request->keyword%")
                ->orWhere('lname', 'LIKE', "%$request->keyword%")
                ->orWhere('mname', 'LIKE', "%$request->keyword%");
        }
        //sort table header
        if($request->key == 'fname'){
            $patients = $patients->orderBy('fname', 'asc')->get();
        // }else if($request->key == 'mname'){
        //     $patients = $patients->orderBy('mname', 'asc')->get();
        // }else if($request->key == 'lname'){
        //     $patients = $patients->orderBy('lname', 'asc')->get();
        // }else if($request->key == 'region'){
        //     $patients = $patients->orderBy('region', 'asc')->get();
        // }else if($request->key == 'province'){
        //     $patients = $patients->orderBy('province_id', 'asc')->get();
        // }else if($request->key == 'municipality'){
        //     $patients = $patients
        //                 ->orderBy(
        //                     \DB::connection('cloud_mysql')
        //                         ->table('muncity')
        //                         ->select('description')
        //                         ->whereColumn('muncity.id', 'patients.muncity_id')
        //                 )->get();
        // }else if($request->key == 'barangay'){
        //     $patients = $patients
        //                 ->orderBy(
        //                     \DB::connection('cloud_mysql')
        //                         ->table('barangay')
        //                         ->select('description')
        //                         ->whereColumn('barangay.id', 'patients.barangay_id')
        //                 )->get();
        }else{
            $patients = $patients->orderBy('id', 'desc')->get();
        }

        return view('home', [
            'patients' => $patients,
            'keyword' => $request->keyword,
            'provinces' => Province::get(),
            'municipalities' => Muncity::get(),
            'barangays' => Barangay::get(),
            'fundsources' => Fundsource::get(),
            'facilities' => Facility::get(),
            'user' => Auth::user(),
            'all_pat' => Patients::get(),
            'proponents' => Proponent::get(),
            'history' => MailHistory::with('patient', 'sent', 'modified')->get(),
            'logs' => PatientLogs::with('modified', 'facility', 'province', 'muncity', 'barangay', 'proponent')->get()

        ]);
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
            ->with('facilitydata')
            ->with('user')
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
        return $patientsArray;
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
            // 'provinces' => Province::get(),
            // 'fundsources' => Fundsource::get(),
            // 'proponents' => Proponent::get(),
            // 'facility' => Facility::get(),
            'patient' => $patient
            // 'municipal' => $municipal,
            // 'barangay' => $barangay,
        ];        
    }

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
 
   public function updatePatient($id, Request $request){
        // $patient_id = $request->input('patient_id');
        $patient_id = $id;
        $patient = Patients::where('id', $patient_id)->first();

        if(!$patient){
            return redirect()->back()->with('error', 'Patient not found');
        }
        
        $patientLogs = new PatientLogs();
        $patientLogs->patient_id = $patient->id;
        $patientLogs->fill($patient->toArray());
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

        $patient->save();
        return redirect()->back();
   }

   public function removePatient($id){
        if($id){
            Patients::where('id', $id)->delete();
        }
        return redirect()->back()->with('remove_patient', true);
   }

    // public function facilityGet(Request $request) {
    //     return Facility::where('province',$request->province_id)->where('hospital_type','private')->get();
    // }

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
}
