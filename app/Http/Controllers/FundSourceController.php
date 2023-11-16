<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fundsource;
use App\Models\Facility;
use App\Models\Proponent;
use App\Models\ProponentInfo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FundSourceController extends Controller
{
    public function __construct()
    {
       // $this->middleware('auth');
    }

    public function fundSource(Request $request) {
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
                        ]);

        if($request->viewAll) {
            $request->keyword = '';
        }
        else if($request->keyword) {
            $fundsources = $fundsources->where('saa', 'LIKE', "%$request->keyword%");
        } 

        //return $fundsources->toSql();
        
        $fundsources = $fundsources
                        ->orderBy('id','desc')
                        ->paginate(15);
                        
        return view('fundsource.fundsource',[
            'fundsources' => $fundsources,
            'keyword' => $request->keyword
        ]);
    }

    public function createFundSource() {
        $user = Auth::user();
        $fundsources = Fundsource::get();
        return view('fundsource.create_fundsource',[
            'facilities' => Facility::where('hospital_type','private')->get(),
            'user' => $user,
            'fundsources' => $fundsources
        ]);
    }


    public function createFundSourceSave(Request $request) {
        $user = Auth::user();
        //return $request->all();
        if(isset($request->saa_exist)) {
            $fundsource = Fundsource::find($request->saa_exist);
        } else {
            $fundsource = new Fundsource();
            $fundsource->saa = $request->saa;
            $fundsource->created_by = $user->id;
            $fundsource->save();
        }

        $proponent = new Proponent();
        $proponent->fundsource_id = $fundsource->id;
        $proponent->proponent = $request->proponent;
        $proponent->proponent_code = $request->proponent_code;
        $proponent->created_by = $user->id;
        $proponent->save();

        $index = 0;
        foreach ($request->facility_id as $facilityId) {
            $proponentInfo = new ProponentInfo();
            $proponentInfo->fundsource_id = $fundsource->id;
            $proponentInfo->proponent_id = $proponent->id;
            $proponentInfo->facility_id = $request->facility_id[$index];
            $proponentInfo->alocated_funds = $request->alocated_funds[$index];
            $proponentInfo->created_by = $user->id;
            $proponentInfo->save();
            $index++;
        }

        //return $request->all();
        // $data = $request->all();
        // Fundsource::create($request->all());
        // $fundsource = new Fundsource();
        // $fundsource->saa = $request->saa;
        // $fundsource->created_by = $request->created_by;
        // $fundsource->save();

        // $saaInfo = new SaaInfo();
        // $saaInfo->saaId_unique = $fundsouce->id;
        // $saaInfo->saaId = $fundsource->saa_exist;
        
        // $saaInfo->proponent = $saaInfo->proponent;
        // $saaInfo->code_proponent = $saaInfo->code_proponent;
        // $saaInfo->facility_id = $saaInfo->facility_id;
        // $saaInfo->alocated_funds = $saaInfo->alocated_funds;
        // $saaInfo->save();

        session()->flash('fundsource_save', true);
        return redirect()->back();
    }

    public function Editfundsource($fundsourceId)
    {
            // return($proponent_id);
            $fundsources = Fundsource::where('id', $fundsourceId)              
            ->with([
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
            ])->first();

        $specificProponent = $fundsources->proponents->first();
    
        return view('fundsource.update_fundsource', [
            'fundsource' => $fundsources,
            'fundsources' => Fundsource::get(),
            'facility' => Facility::get(),
            'proponent' => $specificProponent,
    
        ]);
    }

    public function updatefundsource(Request $request) {
            $user = Auth::user();
            $fundsource_id = $request->input('fundsourceId');
            $fundsource = Fundsource::find($fundsource_id);
        
                if ($fundsource) {
                    $fundsource->saa = $request->input('saa_exist');
                    $fundsource->save();
                } else {
            
                    return response()->json(['error' => 'Fundsource not found'], 404);
                }

                $proponentsData = $request->input('proponents');

                foreach ($proponentsData as $proponentId => $proponent) {
                    $proponentModel = Proponent::find($proponentId);
        
                    if ($proponentModel) {
                        $proponentModel->update([
                            'proponent' => $proponent['proponent'],
                            'proponent_code' => $proponent['proponent_code'],
                            // Add other fields as needed
                        ]);
                    }
                }

                
                $proponentInfoData = $request->input('proponentInfo');

                foreach ($proponentInfoData as $proponentInfoId => $proponentInfo) {
                    $proponentInfoModel = ProponentInfo::find($proponentInfoId);
                
                    if ($proponentInfoModel) {
                        $proponentInfoModel->update([
                            'facility_id' => $proponentInfo['facility_id'], // Updated from $proponent['facility_id']
                            'alocated_funds' => $proponentInfo['alocated_funds'], // Updated from $proponent['alocated_funds']
                            // Add other fields as needed
                        ]);
                    }
                }


                //  $proponentIds = $request->input('proponentId');
               
                // $proponent = Proponent::find($proponentIds);
                // if ($proponent) {
                //     $proponent->proponent = $request->input('proponent');
                //     $proponent->proponent_code = $request->input('proponent_code');
                //     $proponent->save();
                // } else {
                //     return response()->json(['error' => 'Proponent not found'], 404);
                // }
                // $index = 0;
                
                // if (is_array($proponentIds) || is_object($proponentIds)) {
                //     foreach ($proponentIds as $proponentId) {
                //         $proponent = Proponent::where('id', $proponentId);
                //         if ($proponent) {
                //             $proponent->proponent = $request->input('proponent')[$index];
                //             $proponent->proponent_code = $request->input('proponent_code')[$index];
                //             $proponent->save();
                //         }
                
                //         $index++;
                //     }
                // }
   
               
               
                // foreach ($request->input('facility_id') as $facilityId) {
                //     $proponentInfo = ProponentInfo::where('fundsource_id', $fundsource_id)
                //         ->where('proponent_id', $proponentIds)
                //         ->where('facility_id', $facilityId)
                //         ->first();
            
                //     if ($proponentInfo) {
                //         $proponentInfo->facility_id = $request->input('facility_id')[$index];
                //         $proponentInfo->alocated_funds = $request->input('alocated_funds')[$index];
                //         $proponentInfo->save();
                //     } 
            
                //     $index++;
                // }
        
          return redirect()->back();
    }//end of function
    
    
    public function proponentGet(Request $request) {
        return Proponent::where('fundsource_id',$request->fundsource_id)->get();
    }

    public function facilityProponentGet(Request $request) {
        return ProponentInfo::where('proponent_id',$request->proponent_id)->with([
            'facility' => function ($query) {
                $query->select(
                    'id',
                    DB::raw('name as description')
                );
            }
        ])
        ->get();
    }

    public function getAcronym($str) {
        $words = explode(' ', $str); // Split the string into words
        $acronym = '';
        
        foreach ($words as $word) {
            $acronym .= strtoupper(substr($word, 0, 1)); // Take the first letter of each word and convert to uppercase
        }
        
        return $acronym;
    }

    public function forPatientCode(Request $request) {
        $user = Auth::user();
        $proponent = Proponent::find($request->proponent_id);
        $facility = Facility::find($request->facility_id);
        $patient_code = $proponent->proponent_code.'-'.$this->getAcronym($facility->name).date('YmdHi').$user->id;
        return $patient_code;
    }

    
//  public function forPatientFacilityCode(Request $request) {
//         $user = Auth::user();
//         $proponent = Proponent::where('fundsource_id',$request->funsource_id);
//         $facility = Facility::where('facility_id',$request->facility_id); 
//         $patient_proponent = $proponent->proponent;
//         $facilityname = $facility->name;
//         $data = [
//             'patient_proponent' => $patient_proponent,
//             'facilityname' => $facilityname,
//         ];
//     return response()->json($data);
    
// }

public function forPatientFacilityCode($fundsource_id) {

    $proponentInfo = ProponentInfo::where('fundsource_id', $fundsource_id)->first();
    
    if($proponentInfo){
        $facility = Facility::find($proponentInfo->facility_id);

        $proponent = Proponent::find($proponentInfo->proponent_id);
        $proponentName = $proponent ? $proponent->proponent : null;
       // return $proponent->id . '' . $facility->id;
        return response()->json([

            'proponent' => $proponentName,
            'proponent_id' => $proponentInfo? $proponentInfo->proponent_id : null,
            'facility' => $facility ? $facility->name : null,
            'facility_id' => $proponentInfo ? $proponentInfo->facility_id : null,
        ]);
    }else{
        return response()->json(['error' => 'Proponent Info not found'], 404);
    }
}


  public function facilityGet(Request $request){
     return Facility::where('id', $request->facilityId)->get();
  }

// public function forPatientFacilityCode($fundsource_id) {

//     $fundsource = Fundsource::with('facility', 'proponents', 'proponent_info')
//     ->whereHas('proponents', function ($query) use ($fundsource_id){
//         $query->where('fundsource_id', $fundsource_id);
//     })->find($fundsource_id);
    
//     if($fundsource){
//         $facility_id = $fundsource->proponent_info->first()->facility_id;
//         $facilities = Facility::where('facility_id', $facility_id)->get();

//         $proponentName = $fundsource->proponents->first()->proponent;
//         $facilityName = $facilities->pluck('name')->all();
//         //$facilityName =  $fundsource->facility ?  $fundsource->facility->name : null;
//         $proponents = $fundsource->proponents->pluck('proponent')->all();
//         return response()->json([
//           'proponent' => $proponents,
//           'facility' => $facilityName,
//         ]);
//     }
   
// }


    public function transactionGet() {
        $randomBytes = random_bytes(16); // 16 bytes (128 bits) for a reasonably long random code
        $uniqueCode = bin2hex($randomBytes);
        $facilities = Facility::where('hospital_type','private')->get();
        return view('fundsource.transaction',[
            'facilities' => $facilities,
            'uniqueCode' => $uniqueCode
        ]);
    }

    public function fundSourceGet() {
        $result = Fundsource::with([
            'proponents' => function ($query) {
                $query->with([
                    'proponentInfo' => function ($query) {
                        $query->with('facility');
                    }
                ]);
            }
        ])->get();
        return $result;
    }


}
