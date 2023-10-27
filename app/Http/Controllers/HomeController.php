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
                                    'fundsource'
                                ])
                                ->first();
        return view('maif.update_patient',[
            'provinces' => Province::get(),
            'fundsources' => Fundsource::get(),
            'proponents' => Proponent::get(),
            'patient' => $patient,
        ]);
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
