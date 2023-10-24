<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facility;
use App\Models\Patients;
use App\Models\AddFacilityInfo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;   


class FacilityController extends Controller
{
    public function index(Request $request) {
        // $Facility = (new Facility())->getConnection()->getDatabaseName();
        // $AddFacilityInfo = (new AddFacilityInfo())->getConnection()->getDatabaseName();
        // $result = DB::table($Facility.'.facility')
        //     ->select(
        //         DB::raw($Facility.'.facility.name'),
        //         DB::raw($AddFacilityInfo.'.addfacilityinfo.*'))
        //     ->join($AddFacilityInfo.'.addfacilityinfo', $Facility.'.facility.id', '=', $AddFacilityInfo.'.addfacilityinfo.facility_id')
        //     ->where($Facility.'.facility.hospital_type','private')
        //     ->paginate(15);

        
         $result = DB::table('doh_referral.facility')
            ->select(
                'doh_referral.facility.id as main_id',
                'doh_referral.facility.name',
                'doh_referral.facility.address',
                'maif.addfacilityinfo.*'
            )
            ->leftJoin('maif.addfacilityinfo', 'doh_referral.facility.id', '=', 'maif.addfacilityinfo.facility_id')
            ->where('doh_referral.facility.hospital_type','private')
            ->paginate(15);

        return view('facility.facility',[
            'facilities' => $result,
            'keyword' => $request->keyword
        ]);
    }

    public function facilityEdit($main_id)
    {
    
       // $facility = AddFacilityInfo::find($main_id);
       $facility = AddFacilityInfo::where('facility_id', $main_id)->first();
        if (!$facility) {
           $facility = new AddFacilityInfo();
        }

        $data = [
                 'facility' => $facility,
                 'main_id' => $main_id,
                ];
    
        return view('facility.edit', $data);
     }//end of function
    

     public function facilityUpdate(Request $request)
     {

          $rules =[
                  'social_worker_contact' => 'required|digits:11',
                 'finance_officer_contact' => 'required|digits:11',
                 ];
         $message = [
                    'social_worker_contact' => 'The social worker contact number must be 11 digits.',
                    'finance_officer_contact' => 'The finance officer contact number must be 11 digits.',
                    ];

        $validator = Validator::make($request->all(), $rules, $message);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
    
        $main_id = $request->input('main_id');    
        // Retrieve the facility record based on the main_id
        $facility = AddFacilityInfo::where('facility_id', $main_id)->first();

        if (!$facility) {
            // If the facility doesn't exist, you can create a new instance.
            $facility = new AddFacilityInfo();
            $facility->facility_id = $main_id;
        }
            // Update the fields regardless of whether it's a new record or an existing one
            $facility->social_worker = $request->input('social_worker');
            $facility->social_worker_email = $request->input('social_worker_email');
            $facility->social_worker_contact = $request->input('social_worker_contact');
            $facility->finance_officer = $request->input('finance_officer');
            $facility->finance_officer_email = $request->input('finance_officer_email');
            $facility->finance_officer_contact = $request->input('finance_officer_contact');
            $facility->vat = $request->input('vat');
            $facility->Ewt = $request->input('Ewt');
            $facility->save();   
            return redirect()->route('facility', ['main_id' => $main_id])->with('success', 'Facility updated successfully');
        }//end of function

      

}



