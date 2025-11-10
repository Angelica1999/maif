<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facility;
use App\Models\Patients;
use App\Models\Proponent;
use App\Models\AddFacilityInfo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;   
use App\Models\Muncity;
use App\Models\Barangay;
use App\Models\Bills;
use App\Models\BillExtension;
use App\Models\BillTracking;
use App\Models\Transmittal;
use App\Models\ReturnDetails;
use App\Models\Logbook;
use App\Models\IncludedFacility;

class FacilityController extends Controller
{
    public function __construct()
    {
       $this->middleware('auth');
       $this->middleware('block.secure.nonadmin');
    }
    
    public function index(Request $request) {

        $brgy = Barangay::pluck('muncity_id')->toArray();
        $result = Facility::with('addFacilityInfo')
                ->select(
                    'facility.id',
                    'facility.name',
                    'facility.address',
                );
     
        if($request->viewAll){
            $request->keyword = '';
        }else if($request->keyword){
            $result->where('name', 'LIKE', "%$request->keyword%");
        }

        $results = $result->orderBy('name', 'asc')->paginate(20);

        return view('facility.facility',[
            'results' => $results,
            'keyword' => $request->keyword
        ]);
    }

    public function includedFacility(Request $request) {
        $ids = IncludedFacility::pluck('facility_id')->toArray();
        $brgy = Barangay::pluck('muncity_id')->toArray();
        $result = Facility::whereIn('id', $ids)->with('addFacilityInfo')
                ->select(
                    'facility.id',
                    'facility.name',
                    'facility.address',
                );
     
        if($request->viewAll){
            $request->keyword = '';
        }else if($request->keyword){
            $result->where('name', 'LIKE', "%$request->keyword%");
        }

        $results = $result->paginate(20);
        $list = Facility::whereNotIn('id', $ids)->get();

        return view('facility.included_facility',[
            'results' => $results,
            'keyword' => $request->keyword,
            'list' => $list
        ]);
    }

    public function facilityEdit($main_id)
    {
    
        $facility = AddFacilityInfo::where('facility_id', $main_id)->first();
        if (!$facility) {
           $facility = new AddFacilityInfo();
        }

        $data = [
                 'facility' => $facility,
                 'main_id' => $main_id,
                ];
    
        return view('facility.edit', $data);
    }
    
     public function facilityUpdate(Request $request)
     {
    
        $main_id = $request->input('main_id');    
        $facility = AddFacilityInfo::where('facility_id', $main_id)->orderBy('updated_at', 'desc')->first();

        if (!$facility) {
            $facility = new AddFacilityInfo();
            $facility->facility_id = $main_id;
        }

        $facility->social_worker = $request->input('social_worker');
        $facility->social_worker_email = $request->input('social_worker_email');
        $facility->social_worker_contact = $request->input('social_worker_contact');
        $facility->finance_officer = $request->input('finance_officer');
        $facility->finance_officer_email = $request->input('finance_officer_email');
        $facility->finance_officer_contact = $request->input('finance_officer_contact');
        $facility->official_mail = $request->input('official_mail');
        $facility->cc = $request->input('cc');
        $facility->vat = $request->input('vat');
        $facility->Ewt = $request->input('Ewt');
        $facility->ewt_pf = $request->input('ewt_pf');

        $facility->save();  
        session()->flash('facility_save', true); 
        return redirect()->route('facility', ['main_id' => $main_id]);
    }   

    public function getVatEwt()
    {
        $facility = AddFacilityInfo::all();
        if($facility){
            return $facility;
        }else{
            return 0;
        }
    }
    
    public function updateData(){

        $response = Http::get('http://cvchd7.com/iMkiW5YcHA6D9Gd7BuTteeQPVx4a1UxK');
        set_time_limit(0);
        if ($response->successful()) { // Check if the request was successful
            Facility::truncate();
            $facilities = $response->json(); // Get the response as an array
        
            foreach ($facilities as $fac) {
                $f = new Facility();
                $f->fill($fac); // Use the array directly
                $f->save();
            }
        }
        Facility::where('id', 238)->update(['name' => 'Mactan Doctors Hospital']);

        Facility::where('id', 246)->update(['name' => 'Allied Care Experts (ACE) Medical Center-Bohol, Inc.']);
        Facility::where('id', 251)->update(['name' => 'Allied Care Experts (ACE) Medical Center-Dumaguete Doctors, Inc']);
        Facility::where('id', 678)->update(['name' => 'Allied Care Experts (ACE) Medical Center-Cebu, Inc.']);
        Facility::where('id', 776)->update(['name' => 'Allied Care Experts(ACE) Medical Center - Bayawan Inc.']);
        Facility::where('id', 746)->update(['address' => '2049-D Tagbilaran East Road, Tagbilaran City']);
        Facility::where('id', 849)->update(['name' => 'Tan Chay Duan Renal Center']);
        Facility::where('id', 864)->update(['address' => 'Barili, Cebu']);

        return redirect()->back()->with('update_fac', true);
    }

    public function bills(){

        $bills = Bills::with([
            'extension' => function ($query){
                $query->with('proponent');
            },
            'user' => function ($query){
                $query->with('facility1:id,name,address');
            },
        ])->orderBy('id', 'desc')->paginate(50);
        // return $bills;
        return view('facility.bills',[
            'results' => $bills
        ]);
    }

    public function viewBills($id){
        // $data = Bills::where('id', $id)->with('extension')->first();
        $data = Bills::with([
            'extension' => function ($query){
                $query->with('proponent');
            }
        ])->first();
        return view('facility.view_bills', [
            'result' => $data
        ]);
    }

    public function tracking($id)
    {
        return view('facility.tracking', [
            'results' => BillTracking::where('bills_id', $id)->with([
                'user:username,lname,fname', 
                'accepted_dtr:userid,fname,lname,mname',
                'accepted_gl:username,fname,lname',
                'dtr_user:userid,fname,lname,mname'
            ])->orderBy('id', 'asc')->get()
        ]);
    }

    public function processBills($type, $id, Request $request){

        $billTracking = BillTracking::where('bills_id', $id)->orderBy('id', 'desc')->first();
        $bills = Bills::where('id', $id)->with('user:username,type_identity')->first();

        if($type == 'accept'){
            Bills::where('id', $id)->update(['status' => 3, 'remarks'=> $request->remarks]);
            
        }else if($type == 'return'){
            Bills::where('id', $id)->update(['status' => 2, 'remarks'=> $request->remarks]);

            $tracking = new BillTracking();
            $tracking->bills_id = $id;
            $tracking->remarks = $request->remarks;
            $tracking->status = 2;
            $tracking->released_by = Auth::user()->userid;
            $tracking->released_to = $bills->user->type_identity;
            $tracking->released_on = now();
            $tracking->save();

            $billTracking->status = 3;

        }
        if ($billTracking) {
            $billTracking->accepted_by = Auth::user()->userid;
            $billTracking->accepted_on = now();
            $billTracking->save();
        }

        return redirect()->back()->with('process_bills', true);
    }

    public function incoming(Request $req){
        $transmittal = Transmittal::where('status', 1)
            ->with([
                'user.facility' => function ($query) {
                    $query->select('id', 'name');
                }
            ])
            ->orderBy('id', 'desc')
            ->paginate(50);
         
        $trans = Transmittal::pluck('control_no')->toArray();

        return view('facility.incoming',[
            'transmittal' => $transmittal,
            'control_no' => $trans
        ]);
    }

    public function getTrans($id){
        // $response = Http::get('http://localhost/guaranteeletter/transmittal/summary/'.$id);
        $token = $this->getToken();

        if ($token != 1) {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token
            ])->get('http://192.168.110.7/guaranteeletter/api/transmittal/summary/'.$id);            
        } else {
            return "Authentication failed.";
        }

        // $response = Http::get('http://192.168.110.7/guaranteeletter/transmittal/summary/'.$id);
        return $response;
    }

    public function logbook(Request $request){
        // $group = Logbook::with('r_by:fname,lname,mname,userid')->get()->groupBy('received_by');
        $group = Logbook::whereIn('id', function ($query) {
            $query->select(\DB::raw('MAX(id)'))
                ->from('logbook')
                ->groupBy('received_by');
        })
        ->with('r_by:fname,lname,mname,userid')->get();
        $logbook = Logbook::with('r_by:fname,lname,mname,userid');
        $trans = Transmittal::pluck('control_no')->toArray();
        $keyword = '';
        if($request->keyword && !$request->viewAll && !$request->filter){
            $keyword = $request->keyword;
            $logbook->where('control_no', 'LIKE', "%$keyword%");
        }else if($request->filter){
            $logbook->whereIn('received_by', array_map('intval', explode(',', $request->filter)));
        }

        return view('maif.logbook',[
            'logbook' => $logbook->orderBy('id', 'desc')->paginate(30),
            'control_no' => $trans,
            'keyword' => $keyword,
            'list' => $group,
            'selected' => array_map('intval', explode(',', $request->filter))
        ]);
    }

    public function logbookSave(Request $req){
        $controls = $req->control_no;
        if($controls){
            foreach($controls as $item){

                $trans = Transmittal::where('control_no', $item)->first();
                $trans->remarks = 2;
                $trans->save();

                $log = new Logbook();
                $log->received_on = date('Y-m-d',strtotime($req->received_on));
                $log->received_by = Auth::user()->userid;
                $log->delivered_by = $req->delivered_by;
                $log->control_no = $item;
                $log->save();
                
                // Http::get('http://localhost/guaranteeletter/transmittal/returned/'.$trans->id.'/'.Auth::user()->userid.'/received');
                // Http::get('http://192.168.110.7/guaranteeletter/transmittal/returned/'.$trans->id.'/'.Auth::user()->userid.'/received');
                $token = $this->getToken();

                if ($token != 1) {
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $token
                    ])->get('http://192.168.110.7/guaranteeletter/api/transmittal/returned/'.$trans->id.'/'.Auth::user()->userid.'/received');            
                } else {
                    return "Authentication failed.";
                }
            }
            return redirect()->back()->with('logbook', true);
        }
    }

    public function references($type, $id){
        // $response = Http::get('http://192.168.110.7/guaranteeletter/transmittal/references/'.$id);
        // $response = Http::get('http://localhost/guaranteeletter/transmittal/references/'.$id);
        $token = $this->getToken();
        if ($token != 1) {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token
            ])->get('http://192.168.110.7/guaranteeletter/api/transmittal/references/'.$id);            
        } else {
            return "Authentication failed.";
        }
        $randomBytes = random_bytes(16); 
        return view('facility.return_facility',[
            'references' => $response->json(),
            'type' => $type,
            'code' => $randomBytes
        ]);
    }

    public function returnTrans(Request $req){
        $ids = $_POST['ref_no'];
        foreach($ids as $index => $id){
            $return = new ReturnDetails();
            $return->transmittal_id = $req->id;
            $return->ref_id = $id;
            $return->remarks = $req->remarks[$index];
            $return->returned_by = Auth::user()->userid;
            $return->save();
        }
        // $reponse = Http::get('http://localhost/guaranteeletter/transmittal/returned/'.$req->id.'/'.Auth::user()->userid.'/returned');
        // $reponse = Http::get('http://192.168.110.7/guaranteeletter/transmittal/returned/'.$req->id.'/'.Auth::user()->userid.'/returned');
        $token = $this->getToken();
        if ($token != 1) {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token
            ])->get('http://192.168.110.7/guaranteeletter/api/transmittal/returned/'.$req->id.'/'.Auth::user()->userid.'/returned');            
        } else {
            return "Authentication failed.";
        }

        Transmittal::where('id', $req->id)->update(['status' => 3, 'remarks' => 3]);
        return redirect()->back()->with('trans_return', true);
    }

    public function returned(Request $req){
        $transmittal = Transmittal::where('status', 3)
            ->with([
                'user.facility' => function ($query) {
                    $query->select('id', 'name');
                }
            ])
            ->orderBy('id', 'desc')->paginate(50);
        return view('facility.returned',[
            'transmittal' => $transmittal
        ]);
    }

    public function returnedDetails($id){
        // return Http::get('http://localhost/guaranteeletter/transmittal/return-remarks/'.$id);
        // return Http::get('http://192.168.110.7/guaranteeletter/transmittal/return-remarks/'.$id);
        $token = $this->getToken();
        if ($token != 1) {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token
            ])->get('http://192.168.110.7/guaranteeletter/api/transmittal/return-remarks/'.$id);  
            return $response;      
        } else {
            return "Authentication failed.";
        }
    } 

    public function acceptTrans($id){
        Transmittal::where('id', $id)->update(['status' => 5, 'remarks' => 5]);
        // $reponse = Http::get('http://localhost/guaranteeletter/transmittal/returned/'.$id.'/'.Auth::user()->userid.'/accept');
        // $reponse = Http::get('http://192.168.110.7/guaranteeletter/transmittal/returned/'.$id.'/'.Auth::user()->userid.'/accept');
        $token = $this->getToken();
        if ($token != 1) {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token
            ])->get('http://192.168.110.7/guaranteeletter/api/transmittal/returned/'.$id.'/'.Auth::user()->userid.'/accept');            
        } else {
            return "Authentication failed.";
        }
        return 'success';
    }

    public function accepted(Request $req)
    {
        $keyword = $req->has('viewAll') ? '' : $req->keyword;
        $viewAll = $req->has('viewAll');
        $facs = $req->has('viewAll') ? [0] 
            : (
                $req->facility_data 
                ? array_map('intval', json_decode($req->facility_data[0] ?? '[]', true) ?: $req->facility_data) 
                : [0]
            );
    
        $transmittalQuery = Transmittal::where('status', 5)
            ->with([
                'user.facility' => function ($query) {
                    $query->select('id', 'name');
                }
            ]);
            
        $facilityIds = (clone $transmittalQuery)
            ->select('facility_id')
            ->distinct()
            ->pluck('facility_id');

        if (!$viewAll && !empty($keyword)) {
            $transmittalQuery->where('control_no', 'LIKE', "%{$keyword}%");
        }

        if ($req->facility_data) {
            $transmittalQuery->whereIn('facility_id', $facs);
        }

        $status = $req->has('viewAll') ? [0] 
        : (
            $req->status_data 
            ? array_map('intval', json_decode($req->status_data[0] ?? '[]', true) ?: $req->status_data) 
            : [0]
        );

        if ($req->status_data) {
            if($status != 0){
                $transmittalQuery->whereIn('remarks', $status);
            }
        }

        $facilities = Facility::whereIn('id', $facilityIds)->select('id', 'name')->get();

        $stats = (clone $transmittalQuery)
            ->selectRaw('COUNT(*) as total_count, SUM(total) as total_amount')
            ->first();

        $total = $stats->total_count ?? 0;
        $amount = $stats->total_amount ?? 0;

        if ($req->sort == "name" && $req->direction){
            $f_order = Facility::orderBy('name', $req->direction)->pluck('id')->toArray();    
            $transmittal = (clone $transmittalQuery)
                ->orderByRaw('FIELD(facility_id, ' . implode(',', $f_order) . ')')
                ->paginate(50);
        }elseif($req->sort == "remarks" && $req->direction){
            $transmittal = (clone $transmittalQuery)
            ->orderBy('remarks', $req->direction)
            ->paginate(50);
        }else{
            $transmittal = (clone $transmittalQuery)
            ->orderBy('id', 'desc')
            ->paginate(50);
        }

        $patients = DB::table('transmittal_patients as tp')
            ->join('transmittal_details as td', 'tp.transmittal_details', '=', 'td.id')
            ->whereIn('td.transmittal_id', $transmittal->pluck('id'))
            ->count();

        return view('facility.accepted', [
            'transmittal' => $transmittal,
            'facilities' => $facilities,
            'patients' => $patients,
            'keyword' => $keyword,
            'total' => $total,
            'amount' => $amount,
            'facs' => $facs ?? '',
            'status' => $status ?? '',
        ]);
    }

    public function transDetails($id, $facility_id){
        // $reponse = Http::get('http://localhost/guaranteeletter/transmittal/details/'.$id.'/'.$facility_id);
        // $reponse = Http::get('http://192.168.110.7/guaranteeletter/transmittal/details/'.$id.'/'.$facility_id);
        $token = $this->getToken();
        // return $token;
        if ($token != 1) {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token
            ])->get('http://192.168.110.7/guaranteeletter/api/transmittal/details/'.$id.'/'.$facility_id);            
        } else {
            return "Authentication failed.";
        }
        return $response;
    }

    public function transRem(Request $req) {
        $files = [];
        
        if ($req->trans_files) {
            try {
                $req->validate([
                    'trans_files.*' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2408',
                ]);
    
                foreach ($req->file('trans_files') as $upload) {
                    $filename = $upload->getClientOriginalName();
                    $path = $upload->storeAs('transmittal', $filename);
                    $files[] = $filename; 
                }
            } catch (\Illuminate\Validation\ValidationException $e) {
                return redirect()->back()->withErrors($e->validator)->withInput();
            }
        }

        $id = $req->rem_id;
        $trans = Transmittal::where('id', $id)->first();
        $trans->image = !empty($files) ? json_encode($files) : '';  
        $trans->link = $req->trans_link ? $req->trans_link : '';
        $trans->save();
    
        return redirect()->back()->with('update_remarks', true);
    }

    public function received($control_no, $name){
        $trans = Transmittal::where('id', $control_no)->first();

        $log = new Logbook();
        $log->received_on = date('Y-m-d',strtotime(now()));
        $log->received_by = Auth::user()->userid;
        $log->delivered_by = $name;
        $log->control_no = $trans->control_no;
        $log->save();
        
        $trans->remarks = 2;
        $trans->save();

        // Http::get('http://192.168.110.7/guaranteeletter/transmittal/returned/'.$trans->id.'/'.Auth::user()->userid.'/received');
        // Http::get('http://localhost/guaranteeletter/transmittal/returned/'.$trans->id.'/'.Auth::user()->userid.'/received');
        $token = $this->getToken();
        if ($token != 1) {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token
            ])->get('http://192.168.110.7/guaranteeletter/api/transmittal/returned/'.$trans->id.'/'.Auth::user()->userid.'/received');            
        } else {
            return "Authentication failed.";
        }

        return response()->json(['message' => 'Data submitted successfully']);
    }

    public function releaseFacility($id){
        $fc = IncludedFacility::where('facility_id', $id)->first();
        if($fc){
            $fc->delete();
        }
    }
    
    public function addFacility(Request $request){
        $ids = $request->ids;
        foreach($ids as $id){
            $fc = new IncludedFacility();
            $fc->facility_id = $id;
            $fc->added_by = Auth::user()->userid;
            $fc->save();
        }
       return redirect()->back()->with('added_facility', true);
    }

    public function getToken(){
        $user = Auth::user();
        $loginResponse = Http::post('http://192.168.110.7/guaranteeletter/api/login', [
            'userid' => 2760
        ]);
        if (isset($loginResponse['token'])) {
            $token = $loginResponse['token'];
        } else {
            "Authentication failed. Error: " . ($loginResponse['message'] ?? 'Unknown error');
        }
        return $token;
    }

    public function samsam(){

        $token = $this->getToken();

        if ($token != 1) {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token
            ])->get('http://192.168.110.7/guaranteeletter/api/test');
            
            $data = $response->json();
        } else {
            return "Authentication failed.";
        }

        return $data;

    }

        public function sendHold(Request $req){
            $facilities = AddFacilityInfo::select('id','facility_id')->with('facility:id,name')
                ->whereNotNull('sent_status');
            // return $facilities->get();
            $on_hold = Facility::with('addFacilityInfo')
                ->whereRelation('addFacilityInfo', 'sent_status', null)
                ->orderBy('name', 'asc')
                ->select('id', 'name')
                ->get();
            
            if ($req->viewAll) {
                $req->keyword = '';
            } else if ($req->keyword) {
                $facilities->where(function($query) use ($req) {
                    $query->where('name', 'LIKE', "%{$req->keyword}%");
                });
            }

            return view('facility.facility_hold_send', [
                'facilities' => $facilities->paginate(50),
                'keyword' => $req->keyword,
                'hold' => $on_hold
            ]);
        }

        public function holdSendFacility(Request $req){
            if($req->facility_id){
                foreach($req->facility_id as $id){
                    $info = AddFacilityInfo::where('facility_id', $id)->first();
                    if($info){
                        $info->sent_status = 1;
                        $info->save();
                    }else{
                        $new_info = new AddFacilityInfo();
                        $new_info->facility_id = $id;
                        $new_info->sent_status = 1;
                        $new_info->created_by = Auth::user()->userid;
                        $new_info->save();
                    }
                }
                return redirect()->back();
            }
        }
}