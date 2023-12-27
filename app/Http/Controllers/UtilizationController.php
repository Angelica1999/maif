<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utilization;
use App\Models\Proponent;
use App\Models\Fundsource;

class UtilizationController extends Controller
{
    //
    public function tracking(Request $request)
    {
      
        // $utilize = Utilization::with(['fundSource', 'proponentInfo'])
        // ->where('fundsource_id', $request->fundsourceId)
        // ->where('proponentinfo_id', $request->proponentInfoId)->get();

        $data = Utilization::with(['proponentdata', 'fundSourcedata'])
        ->where('fundsource_id', $request->fundsourceId)
        ->where('proponentinfo_id', $request->proponentInfoId)
        ->where('facility_id', $request->facilityId)
        ->get();
        // return $request->fundsourceId;
    
    return response()->json($data);
    
    }



    
    // public function index(){

    //     $utilizations = Utilization::with(['fundSource', 'proponentInfo'])->get();
    //     return view('fundsource.fundsource', [
    //         'utilizations' => $utilizations,
    //     ]);
    // }

}
