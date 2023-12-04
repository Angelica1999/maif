<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fundsource;
use App\Models\User;
use App\Models\Facility;
use App\Models\Utilization;
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

        $dv = Dv::with('fundsource')->get();
        
        return view('dv.dv', ['disbursement' => $dv]);
        // $dv = Dv::get();
        // return view('dv.dv', [
        // 'disbursement' => $dv,
        // ]);
    }
    

    public function createDv(Request $request)
    {
        $user = Auth::user();
        $dvs = Dv::get();
         
            $facilityId = ProponentInfo::where('facility_id','=', $request->facilityId)->get();
            //  dd($facilityId);

            $fundsources = Fundsource::              
                            with([
                                'proponents' => function ($query) {
                                    $query->with([
                                        'proponentInfo' => function ($query) {
                                            $query->with('facility');
                                        }
                                    ]);
                                },
                                'encoded_by' => function ($query) {
                                    $query->select(
                                        'id',
                                        'name'
                                    );
                                }
                            ])->get();

        $facilities = Facility::all();
        $VatFacility = AddFacilityInfo::Select('id','vat')->distinct()->get();
        $ewtFacility = AddFacilityInfo::Select('id','Ewt')->distinct()->get();


        return view('dv.create_dv', [
            'user' => $user,
            'dvs' => $dvs,
            'FundSources' =>  $fundsources,
            'fundsources' => Fundsource::get(), // Pass the fundsource data to the view
            'facilities' => $facilities, // Pass the facility data to the view
            'VatFacility' => $VatFacility,
            'ewtFacility' => $ewtFacility,
            'facilityId' => $facilityId
        ]);
    }

    
    
    function createDvSave(Request $request){
        // return Auth::user()->id;
        $request->input('saa1_infoId');
        $user = User::where('id', Auth::user()->id)->first();
        // return $user;
        $dv = new Dv();
       $dv->date = $request->input('datefield');
       $dv->payee = $request->input('facilityname');
       $dv->address = $request->input('facilityAddress');
       $dv->month_year_from = $request->input('billingMonth1');
       $dv->month_year_to = $request->input('billingMonth2');
       $saaNumbers = [
        $request->input('fundsource_id'),
        $request->input('fundsource_id_2'),
        $request->input('fundsource_id_3'),
       ];
       $saaNumbers = array_filter($saaNumbers, function($value) {
          return $value !== null;
       });
      
       $dv->saa_number = json_encode($saaNumbers);
       $dv->amount1 = $request->input('amount1');
       $dv->amount2 = $request->input('amount2');
       $dv->amount3 = $request->input('amount3');
       $dv->total_amount = $request->input('total');
       $dv->deduction1 = $request->input('vat');
       $dv->deduction2 = $request->input('ewt');
       $dv->deduction_amount1 = $request->input('deductionAmount1');
       $dv->deduction_amount2 = $request->input('deductionAmount2');
       $dv->total_deduction_amount = $request->input('totalDeduction');
       $dv->overall_total_amount = $request->input('overallTotal');
       $dv->save();

       if ($dv->saa_number) {
        $saaNumbersArray = is_array($dv->saa_number)
            ? $dv->saa_number
            : explode(',', $dv->saa_number);
        $proponent_id = [$request->input('saa1_infoId'), $request->input('saa2_infoId'), $request->input('saa3_infoId')];
        $beg_balance = [$request->input('saa1_beg'),$request->input('saa2_beg'),$request->input('saa3_beg')];
        $utilize_amount = [$request->input('saa1_utilize'),$request->input('saa2_utilize'),$request->input('saa3_utilize')];
        $discount = [$request->input('saa1_discount'),$request->input('saa2_discount'),$request->input('saa3_discount')];
        $i= 0;
        // return $utilize ;

      
        // return $discount;
        foreach ($saaNumbersArray as $saa) {
            $cleanedSaa = str_replace(['[', ']', '"'], '', $saa);
            $utilize = new Utilization();
            $utilize->fundsource_id = trim($cleanedSaa);
            $utilize->proponentinfo_id = $proponent_id[$i];

            $proponent_info = ProponentInfo::where('fundsource_id', trim($cleanedSaa))->where('proponent_id', $proponent_id[$i])->first();
            if($proponent_info && $proponent_info != null){
                $proponent_info->remaining_balance = $proponent_info->alocated_funds - $utilize_amount[$i];

            }else{
                $proponent_info->remaining_balance = $proponent_info->remaining_balance - $utilize_amount[$i]; // 100 - 10 = 90

            }
            $proponent_info->save();

            $utilize->div_id = $dv->id;
            $utilize->beginning_balance = $beg_balance[$i];
            $utilize->discount = $discount[$i];
            $utilize->utilize_amount = $utilize_amount[$i];
            $utilize->created_by = $user->name;
            $utilize->save();
            $i = $i + 1;
        }
    }

       session()->flash('dv_create', true);
       return redirect()->back();
    }

    function facilityGet(Request $request){
        //  \Log::info('Request received. Fundsource ID: ' . $request->fundsource_id);
  
          ProponentInfo::where('fundsource_id', $request->fundsource_id)->get();
          $proponentInfo = ProponentInfo::with('facility')
                         ->where('fundsource_id', $request->fundsource_id)
                         ->get();
                   
         return $proponentInfo;
      }
  
      function dvfacility(Request $request){
         $proponentInfo = ProponentInfo::with('facility')
         ->where('facility_id',  $request->facility_id)->first();
        // return $proponentInfo;
         if($proponentInfo){
           $facilityAddress = $proponentInfo->facility->address;
           return response()->json(['facilityAddress' => $facilityAddress]);
         }else{
          return "facility not found";
         }
      }

    public function getFund (Request $request) {
        $facilityId = ProponentInfo::with([
            'fundsource' => function ($query) {
                $query->select('id', 'saa');
            },
        ])->where('facility_id', '=', $request->facilityId)
            ->where('fundsource_id', '!=', $request->fund_source)
            ->get();

        $fund_source = ProponentInfo::with([
            'fundsource' => function ($query) {
                $query->select(
                    'id',
                    'saa'
                );
            }])->where('fundsource_id','=', $request->fund_source)
            ->first();
             
        $beginning_balances = session()->put('balance', $fund_source->alocated_funds);
        return $facilityId;
    }

    public function getvatEwt(Request $request)
    {
        $facilityVatEwt = AddFacilityInfo::where('facility_id',$request->facilityId)->first();
    

        return $facilityVatEwt;
    }
    
    public function getAlocated(Request $request){
        $allocatedFunds = ProponentInfo::where('facility_id', $request->facilityId)
       // ->where('fundsource_id', $request->fund_source)
        ->select('alocated_funds','fundsource_id', 'id', 'remaining_balance')
        ->get();
        return response()->json(['allocated_funds' => $allocatedFunds]);
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
