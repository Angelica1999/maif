<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facility;
use App\Models\Patients;
use App\Models\Proponent;
use App\Models\AddFacilityInfo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;   
use App\Models\Muncity;
use App\Models\Barangay;
use App\Models\Bills;
use App\Models\BillExtension;
use App\Models\BillTracking;
use App\Models\Transmittal;
use App\Models\ReturnDetails;
use App\Models\Logbook;
use Illuminate\Support\Facades\Http;

class FacilityController extends Controller
{
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

        $results = $result->paginate(50);

        return view('facility.facility',[
            'results' => $results,
            'keyword' => $request->keyword
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
        Facility::truncate();
        $main_facility = DB::connection('cloud_mysql')
            ->table('facility')
            ->get();   
        foreach ($main_facility as $fac) {
            $f = new Facility();
            $f->fill(get_object_vars($fac));
            $f->save();
        }

        Facility::where('id', 238)->update(['name' => 'Mactan Doctors Hospital']);

        Facility::where('id', 246)->update(['name' => 'Allied Care Experts (ACE) Medical Center-Bohol, Inc.']);
        Facility::where('id', 251)->update(['name' => 'Allied Care Experts (ACE) Medical Center-Dumaguete Doctors, Inc']);
        Facility::where('id', 678)->update(['name' => 'Allied Care Experts (ACE) Medical Center-Cebu, Inc.']);
        Facility::where('id', 776)->update(['name' => 'Allied Care Experts(ACE) Medical Center - Bayawan Inc.']);

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
        
        $transmittal = Transmittal::where('status', 1)->with('user')->orderBy('id', 'desc')->paginate(50);
        $trans = Transmittal::pluck('control_no')->toArray();

        return view('facility.incoming',[
            'transmittal' => $transmittal,
            'control_no' => $trans
        ]);
    }

    public function getTrans($id){

        $response = Http::get('http://192.168.110.148/guaranteeletter/transmittal/summary/'.$id);
        return $response;
    }

    public function logbook(Request $req){
        $logbook = Logbook::orderBy('id', 'desc')->paginate(100);
        $trans = Transmittal::pluck('control_no')->toArray();
        return view('maif.logbook',[
            'logbook' => $logbook,
            'control_no' => $trans
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
                

                Http::get('http://192.168.110.148/guaranteeletter/transmittal/returned/'.$trans->id.'/'.Auth::user()->userid.'/received');
            }
            return redirect()->back()->with('logbook', true);
        }
    }

    public function references($type, $id){
        $response = Http::get('http://192.168.110.148/guaranteeletter/transmittal/references/'.$id);
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

        $reponse = Http::get('http://192.168.110.148/guaranteeletter/transmittal/returned/'.$req->id.'/'.Auth::user()->userid.'/returned');
        Transmittal::where('id', $req->id)->update(['status' => 3, 'remarks' => 3]);
        return redirect()->back()->with('trans_return', true);
    }

    public function returned(Request $req){
        $transmittal = Transmittal::where('status', 3)->with('user')->orderBy('id', 'desc')->paginate(50);
        return view('facility.returned',[
            'transmittal' => $transmittal
        ]);
    }

    public function returnedDetails($id){
        return Http::get('http://192.168.110.148/guaranteeletter/transmittal/return-remarks/'.$id);
    } 

    public function acceptTrans($id){
        Transmittal::where('id', $id)->update(['status' => 5, 'remarks' => 5]);
        $reponse = Http::get('http://192.168.110.148/guaranteeletter/transmittal/returned/'.$id.'/'.Auth::user()->userid.'/accept');
        return 'success';
    }

    public function accepted(Request $req){
        $transmittal = Transmittal::where('status', 5)->with('user')->orderBy('id', 'desc')->paginate(50);
        return view('facility.accepted',[
            'transmittal' => $transmittal
        ]);
    }

    public function transDetails($id, $facility_id){
        $reponse = Http::get('http://192.168.110.148/guaranteeletter/transmittal/details/'.$id.'/'.$facility_id);
        return $reponse;
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
    
        return redirect()->back();
    }
    
}