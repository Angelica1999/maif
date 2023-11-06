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
use App\Models\ProponentInfo;
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
    public function index(Request $request)
    {
        $patients = Patients::
                        with(
                            [
                                // 'facility' => function ($query) {
                                //     $query->select(
                                //         'id',
                                //         DB::raw('name as description')
                                //     );
                                // },
                                'province' => function ($query) {
                                    $query->select(
                                        'id',
                                        'description'
                                    );
                                },
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
                                'encoded_by' => function ($query) {
                                    $query->select(
                                        'id',
                                        'name'
                                    );
                                }
                            ]);              
                        

        if($request->viewAll) {
            $request->keyword = '';
        }
        else if($request->keyword) {
            $patients = $patients->where('fname', 'LIKE', "%$request->keyword%")
                                ->orWhere('lname', 'LIKE', "%$request->keyword%")
                                ->orWhere('mname', 'LIKE', "%$request->keyword%");
        }

        $patients = $patients->orderBy('id','desc')
                            ->paginate(15);
                           
                            
        return view('home',[
            'patients' => $patients,
            'keyword' => $request->keyword,
            'provinces' => Province::get()
        ]);
    }

    public function createPatient() {
        $user = Auth::user();
        return view('maif.create_patient',[
            'provinces' => Province::get(),
            'fundsources' => Fundsource::get(),
            'user' => $user
        ]);
    }

    public function createPatientSave(Request $request) {
        session()->flash('patient_save', true);
        $data = $request->all();
        Patients::create($request->all());

        return redirect()->back();
    }

    public function editPatient(Request $request) {
        $patient = Patients::where('id',$request->patient_id)
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
                                ])
                                ->first();

                                $municipal = Muncity::select('id', 'description')->get();
                                $barangay = Barangay::select('id', 'description')->get();
                               // $Proponent = Proponent::find($patient->proponent_id);
                                //$Facility = Facility::find($patient->facility_id);
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
 
   public function updatePatient(Request $request)
   {
      $patient_id = $request->input('patient_id');
       
      $patient = Patients::where('id', $patient_id)->first();
      if(!$patient){
        return redirect()->back()->with('error', 'Patient not found');
      }
      session()->flash('patient_update', true);
      $patient->fname = $request->input('fname');
      $patient->lname = $request->input('lname');
      $patient->mname = $request->input('mname');
      $patient->dob   = $request->input('dob');
      $patient->region = $request->input('region');
      $patient->province_id = $request->input('province_id');
      $patient->muncity_id  = $request->input('muncity_id');
      $patient->barangay_id = $request->input('barangay_id');
      $patient->fundsource_id = $request->input('fundsource_id');
      $patient->proponent_id = $request->input('proponent_id');
      $patient->facility_id = $request->input('facility_id');
      $patient->guaranteed_amount = $request->input('guaranteed_amount');
      $patient->actual_amount = $request->input('actual_amount');
      $patient->remaining_balance = $request->input('remaining_balance');


        $patient->save();
        return redirect()->back();

        
    //   $patient = Patients::where('id', $patient_id)
    //                     ->with(
    //                     [
    //                       'muncity' => function ($query){
    //                         $query->select(
    //                             'id',
    //                             'description'
    //                         );
    //                       },                 
    //                      'barangay' => function ($query){
    //                         $query->select(
    //                            'id',
    //                            'description'
    //                         );
    //                      },
    //                      'fundsource'
    //                     ])
    //                     ->first();
   }  



    public function facilityGet(Request $request) {
        return Facility::where('province',$request->province_id)->where('hospital_type','private')->get();
    }

    public function muncityGet(Request $request) {
        return Muncity::where('province_id',$request->province_id)->get();
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
