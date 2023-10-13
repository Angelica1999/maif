<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patients;
use App\Models\Facility;
use App\Models\Province;
use App\Models\Muncity;
use App\Models\Barangay;
use Illuminate\Support\Facades\DB;

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
                                'facility' => function ($query) {
                                    $query->select(
                                        'id',
                                        DB::raw('name as description')
                                    );
                                },
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
            'keyword' => $request->keyword
        ]);
    }

    public function createPatient() {
        return view('maif.create_patient',[
            'facilities' => Facility::where('hospital_type','private')->get(),
            'provinces' => Province::get(),
            'muncities' => Muncity::get(),
            'barangays' => Barangay::get()
        ]);
    }

    public function createPatientSave(Request $request) {
        session()->flash('patient_save', true);
        $data = $request->all();
        unset($data);
        Patients::create($request->all());

        return redirect()->back();
    }

}
