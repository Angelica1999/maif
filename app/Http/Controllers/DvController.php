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


    public function dv(Request $request){

       $result = Dv::with(['fundsource','facility'])->orderby('id', 'desc');

        if($request->viewAll){
            $request->keyword = '';
        }else if($request->keyword){
            $result->where('name', 'LIKE', "%$request->keyword%");
        }

       $results = $result->paginate(5);
        return view('dv.dv', [
            'disbursement' => $results,

        ]);
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

        $user = User::where('id', Auth::user()->id)->first();

        $check = $request->input('dv');        
        $facility_id = array_values(array_filter([$request->input('fac_id1'), $request->input('fac_id2'), $request->input('fac_id3')],
        function($value){return $value !== '0' && $value !==null;}));
        $proponent_id = array_values(array_filter([$request->input('saa1_infoId'), $request->input('saa2_infoId'), $request->input('saa3_infoId')],
        function($value){return  $value !=='0' && $value !==null;}));
        // return $facility_id;
        $per_amount = [$request->input('amount1'),$request->input('amount2'),$request->input('amount3')];
        $utilize_amount = [$request->input('saa1_utilize'),$request->input('saa2_utilize'),$request->input('saa3_utilize')];
        $discount = [$request->input('saa1_discount'),$request->input('saa2_discount'),$request->input('saa3_discount')];
        
        if($check == null){
          
            $dv = new Dv();
            $dv->date = $request->input('datefield');
            $dv->facility_id = $request->input('facilityname');
            $dv->address = $request->input('facilityAddress');
            $dv->month_year_from = $request->input('billingMonth1');
            $dv->month_year_to = $request->input('billingMonth2');
            $dv->control_no = $request->input('control_no');
            $saaNumbers =array_values(array_filter([
            $request->input('fundsource_id'),
            $request->input('fundsource_id_2'),
            $request->input('fundsource_id_3'),
            ], function($value){return $value !==0 && $value!==null;}));
            $dv->fundsource_id = json_encode($saaNumbers);
            $dv->amount1 = $request->input('amount1');
            $dv->amount2 = $request->input('amount2');
            $dv->amount3 = $request->input('amount3');
            $dv->total_amount = $request->input('total');
            $dv->deduction1 = $request->input('vat');
            $dv->deduction2 = $request->input('ewt');
            $dv->deduction_amount1 = $request->input('deductionAmount1');
            $dv->deduction_amount2 = $request->input('deductionAmount2');
            $dv->total_deduction_amount = $request->input('totalDeduction');
            $dv->overall_total_amount = $request->input('overallTotal1');
            $dv->proponent_id = json_encode($proponent_id);
            $dv->save();

            if ($dv->fundsource_id) {
            $saaNumbersArray = is_array($dv->fundsource_id)
                ? $dv->fundsource_id
                : explode(',', $dv->fundsource_id);
            
            $i= 0;
            $id = array_values(array_filter([
                $request->input('pro_id1'),
                $request->input('pro_id2'),
                $request->input('pro_id3')
            ], function ($value) {
                return $value !== '0';
            }));
            
                foreach ($saaNumbersArray as $saa) {
                    $cleanedSaa = str_replace(['[', ']', '"'], '', $saa);
                    $utilize = new Utilization();
                    $utilize->status = 0;
                    $utilize->fundsource_id = trim($cleanedSaa);
                    $utilize->proponentinfo_id = $proponent_id[$i];
                    $utilize->facility_id = $facility_id[$i];

                    $proponent_info = ProponentInfo::where('fundsource_id', trim($cleanedSaa))->where('proponent_id', $proponent_id[$i])->where('id', $id[$i] )->first();
                    // return  $proponent_info;
                    $utilize->div_id = $dv->id;
                    $utilize->beginning_balance = $proponent_info->remaining_balance;
                    $utilize->discount = $discount[$i];
                    $utilize->utilize_amount = $utilize_amount[$i];
                    $utilize->created_by = $user->name;
                    if($proponent_info && $proponent_info != null){
                        $cleanedValue = str_replace(',', '', $proponent_info->remaining_balance);
                        // return  $proponent_info->remaining_balance = (float)$cleanedValue - (float)str_replace(',', '',$per_amount[$i]);
                        $proponent_info->remaining_balance = (float)$cleanedValue - (float)str_replace(',', '',$per_amount[$i]);
                    }else{
                        // return $proponent_id[$i];
                        return "contact system administrator" ;
                    }
                    $utilize->save();
                    $proponent_info->save();
                    $i = $i + 1;
                }
            }
            session()->flash('dv_create', true);
        }else{ // for update
            $update_utilize = Utilization::where('div_id', $check)->update(['status'=>1]);
            $dv = DV::where('id', $check)->first();
            $dv->control_no = $request->input('control_no');
            $saa = explode(',', $dv->fundsource_id);
            $saa = str_replace(['[', ']', '"'],'',$saa);
            $pro_id = explode(',', $dv->proponent_id);
            $pro_id = str_replace(['[', ']', '"'],'',$pro_id);
            $amount = [$dv->amount1, !empty($dv->amount2)?$dv->amount2: 0 , !empty($dv->amount3)?$dv->amount3: 0];
            $index = 0;
            
                foreach($saa as $id){
                   $p_if = ProponentInfo::where('fundsource_id', $id)->where('facility_id', $dv->facility_id)->where('proponent_id',$pro_id[$index])->first();
                //    return $pro_id;
                   if($dv->deduction1 >= 3){
                        $total =((double)str_replace(',', '',$amount[$index]) / 1.12);
                   }else{
                        $total =((double)str_replace(',', '',$amount[$index]));
                   }
                   
                   $return = $p_if -> remaining_balance + (double)str_replace(',', '',$amount[$index]);
                   $p_if->remaining_balance = $return;
                   $index = $index + 1;
                   $p_if->save();
               }

            $dv->date = $request->input('datefield');
            $dv->facility_id = $request->input('facilityname');
            $dv->address = $request->input('facilityAddress');
            $dv->month_year_from = $request->input('billingMonth1');
            $dv->month_year_to = $request->input('billingMonth2');
            $saaNumbers =array_values(array_filter([
                $request->input('fundsource_id'),
                $request->input('fundsource_id_2'),
                $request->input('fundsource_id_3'),
                ], function($value){return $value !==0 && $value!==null;}));
            $dv->fundsource_id = json_encode($saaNumbers); 
            // return json_encode($saaNumbers);
            // $dv->fundsource_id =  implode(', ', array_values($saaNumbers));
            $dv->amount1 = $request->input('amount1');
            $dv->amount2 = $request->input('amount2');
            $dv->amount3 = $request->input('amount3');
            $dv->total_amount = $request->input('total');
            $dv->deduction1 = $request->input('vat');
            $dv->deduction2 = $request->input('ewt');
            $dv->deduction_amount1 = $request->input('deductionAmount1');
            $dv->deduction_amount2 = $request->input('deductionAmount2');
            $dv->total_deduction_amount = $request->input('totalDeduction');
            $dv->overall_total_amount = $request->input('overallTotal1');
            $dv->save();

            if ($dv->fundsource_id) {
            $saaNumbersArray = is_array($dv->fundsource_id)
                ? $dv->fundsource_id
                : explode(',', $dv->fundsource_id);
            
            $i= 0;
            $id = array_values(array_filter([
                $request->input('pro_id1'),
                $request->input('pro_id2'),
                $request->input('pro_id3')
            ], function ($value) {
                return $value !== '0';
            }));
            // return $saaNumbersArray;
                $check = [];
                
                foreach ($saaNumbersArray as $saa) {

                    $cleanedSaa = str_replace(['[', ']', '"'], '', $saa);
                    
                    $utilize = new Utilization();
                    $utilize->status = 0;
                    $utilize->fundsource_id = trim($cleanedSaa);
                    $utilize->proponentinfo_id = $proponent_id[$i];
                    $utilize->facility_id = $facility_id[$i];

                    $proponent_info = ProponentInfo::where('fundsource_id', trim($cleanedSaa))->where('proponent_id', $proponent_id[$i])->where('id', $id[$i] )->first();
                    // return trim($cleanedSaa);
                    
                    $utilize->div_id = $dv->id;
                    $utilize->beginning_balance = $proponent_info->remaining_balance;
                    $utilize->discount = $discount[$i];
                    $utilize->utilize_amount = $utilize_amount[$i];
                    $utilize->created_by = $user->name;
                    if($proponent_info && $proponent_info != null){
                        $cleanedValue = str_replace(',', '', $proponent_info->remaining_balance);
                        $proponent_info->remaining_balance = (float)$cleanedValue - (float)str_replace(',', '', $per_amount[$i]);
                    }else{
                        return "contact system administrator" ;
                    }
                    
                    $utilize->save();
                    $proponent_info->save();
                    $i = $i + 1;
                    $check[]=$utilize->id;
                }
                //  return $check;
            }
            
            session()->flash('dv_update', true);
        }
            
        return redirect()->back();
    }

    public function getDv(Request $request){
       
        $dv = Dv::where('id', $request->dvId)->first();
        
        if($dv){
            $saa = explode(',', $dv->fundsource_id);
            $saa = str_replace(['[', ']', '"'],'',$saa);
            $all = [];
            foreach($saa as $id){
                $all []= $id;
           }
            $fund_source = Fundsource::whereIn('id', $all)->get();
            $facility = Facility::where('id', $dv->facility_id)->first();
            // return $fund_source;
        }

        $data = [
            'dv' =>$dv,
            'fund_source' => $fund_source,
            'facility' => $facility
        ];
        return $data;
       
        return response()->json(['data' => $data]);
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
        ->select('alocated_funds','fundsource_id', 'id', 'remaining_balance', 'proponent_id', 'facility_id')
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
