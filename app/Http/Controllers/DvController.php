<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fundsource;
use App\Models\Facility;
use App\Models\Proponent;
use App\Models\ProponentInfo;
use App\Models\Dv;
use App\Models\AddFacilityInfo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DvController extends Controller
{
    public function __construct()
    {
       // $this->middleware('auth');
    }


    public function dv(){

        
        return view('dv.dv');
    }


    public function createDv()
    {
        $user = Auth::user();
        $dvs = Dv::get();

        // Fetch the fundsource data from the model
        $fundsources = Fundsource::all();
        $facilities = Facility::all();
        $VatFacility = AddFacilityInfo::Select('vat')->distinct()->get();
        $ewtFacility = AddFacilityInfo::Select('Ewt')->distinct()->get();
        //$ewtVatFacility = AddFacilityInfo::all();
        // $ewtVatFacility= Facility::with('addFacilityInfo')
        // ->select(
        //     'facility.id',
        //     'facility.name',
        //     'facility.address',
        //  )
        // ->where('hospital_type','private')->get();

        return view('dv.create_dv', [
            'user' => $user,
            'dvs' => $dvs,
            'fundsources' => $fundsources, // Pass the fundsource data to the view
            'facilities' => $facilities, // Pass the facility data to the view
            'VatFacility' => $VatFacility,
            'ewtFacility' => $ewtFacility,
        ]);
    }

    function facilityGet(Request $request){
         return Facility::where('id', $request->facility_id)->get();
    }


//     public function createFundSourceSave(Request $request) {
//         $user = Auth::user();
//         //return $request->all();
//         if(isset($request->saa_exist)) {
//             $fundsource = Fundsource::find($request->saa_exist);
//         } else {
//             $fundsource = new Fundsource();
//             $fundsource->saa = $request->saa;
//             $fundsource->created_by = $user->id;
//             $fundsource->save();
//         }

//         $proponent = new Proponent();
//         $proponent->fundsource_id = $fundsource->id;
//         $proponent->proponent = $request->proponent;
//         $proponent->proponent_code = $request->proponent_code;
//         $proponent->created_by = $user->id;
//         $proponent->save();

//         $index = 0;
//         foreach ($request->facility_id as $facilityId) {
//             $proponentInfo = new ProponentInfo();
//             $proponentInfo->fundsource_id = $fundsource->id;
//             $proponentInfo->proponent_id = $proponent->id;
//             $proponentInfo->facility_id = $request->facility_id[$index];
//             $proponentInfo->alocated_funds = $request->alocated_funds[$index];
//             $proponentInfo->created_by = $user->id;
//             $proponentInfo->save();
//             $index++;
//         }

    

//         session()->flash('fundsource_save', true);
//         return redirect()->back();
//     }

//     public function Editfundsource($proponentId)
//     {
//         $fundsource = Fundsource::
//                 with([
//                 'proponents' => function ($query) use($proponentId){
//                     $query->select('id', 'fundsource_id', 'proponent', 'proponent_code')
//                     ->where('id', $proponentId);
//                 },
//                 'proponents.proponentInfo' => function ($query) {
//                     $query->select('id', 'fundsource_id', 'proponent_id', 'alocated_funds');
//                 },
//                 'encoded_by' => function ($query) {
//                     $query->select('id', 'name');
//                 }
//             ])->first();    
    
//         $specificProponent = $fundsource->proponents->first();
    
//         return view('fundsource.update_fundsource', [
//             'fundsource' => $fundsource,
//             'facility' => Facility::get(),
//             'fundsourcess' => Fundsource::get(),
//             'proponent' => $specificProponent,
//         ]);
//     }
    
    
//     public function proponentGet(Request $request) {
//         return Proponent::where('fundsource_id',$request->fundsource_id)->get();
//     }

//     public function facilityProponentGet(Request $request) {
//         return ProponentInfo::where('proponent_id',$request->proponent_id)->with([
//             'facility' => function ($query) {
//                 $query->select(
//                     'id',
//                     DB::raw('name as description')
//                 );
//             }
//         ])
//         ->get();
//     }

//     public function getAcronym($str) {
//         $words = explode(' ', $str); // Split the string into words
//         $acronym = '';
        
//         foreach ($words as $word) {
//             $acronym .= strtoupper(substr($word, 0, 1)); // Take the first letter of each word and convert to uppercase
//         }
        
//         return $acronym;
//     }

//     public function forPatientCode(Request $request) {
//         $user = Auth::user();
//         $proponent = Proponent::find($request->proponent_id);
//         $facility = Facility::find($request->facility_id);
//         $patient_code = $proponent->proponent_code.'-'.$this->getAcronym($facility->name).date('YmdHi').$user->id;
//         return $patient_code;
//     }



// public function forPatientFacilityCode($fundsource_id) {

//     $proponentInfo = ProponentInfo::where('fundsource_id', $fundsource_id)->first();
    
//     if($proponentInfo){
//         $facility = Facility::find($proponentInfo->facility_id);

//         $proponent = Proponent::find($proponentInfo->proponent_id);
//         $proponentName = $proponent ? $proponent->proponent : null;
//        // return $proponent->id . '' . $facility->id;
//         return response()->json([

//             'proponent' => $proponentName,
//             'proponent_id' => $proponentInfo? $proponentInfo->proponent_id : null,
//             'facility' => $facility ? $facility->name : null,
//             'facility_id' => $proponentInfo ? $proponentInfo->facility_id : null,
//         ]);
//     }else{
//         return response()->json(['error' => 'Proponent Info not found'], 404);
//     }
// }



//     public function transactionGet() {
//         $randomBytes = random_bytes(16); // 16 bytes (128 bits) for a reasonably long random code
//         $uniqueCode = bin2hex($randomBytes);
//         $facilities = Facility::where('hospital_type','private')->get();
//         return view('fundsource.transaction',[
//             'facilities' => $facilities,
//             'uniqueCode' => $uniqueCode
//         ]);
//     }

//     public function fundSourceGet() {
//         $result = Fundsource::with([
//             'proponents' => function ($query) {
//                 $query->with([
//                     'proponentInfo' => function ($query) {
//                         $query->with('facility');
//                     }
//                 ]);
//             }
//         ])->get();
//         return $result;
//     }

}
