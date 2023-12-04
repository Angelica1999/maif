<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utilization;

class UtilizationController extends Controller
{
    //
    public function indextracking(Request $request)
    {
        $utilize = Utilization::where('fundsource_id', $request->fundsourceId)
                                ->where('proponentinfo_id', $request->proponentInfoId)->first();
            // $data = [
            //     'fundSource' => $utilize->fundsource_id, // Replace with the actual column name
            //     'proponent' => $utilize->proponentinfo_id, // Replace with the actual column name
            //     'beginningBalance' => $utilize->beginning_balance, // Replace with the actual column name
            //     'discount' => $utilize->discount, // Replace with the actual column name
            //     'utilizeAmount' => $utilize->utilizeAmount, // Replace with the actual column name
            //     'createdBy' => $utilize->createdBy, // Replace with the actual column name
            //     'createdAt' => $utilize->createdAt, // Replace with the actual column name
            // ];
            return response()->json($data);
    }

    public function index(){

        $utilizations = Utilization::with(['fundSource', 'proponentInfo'])->get();
        return view('fundsource.fundsource', [
            'utilizations' => $utilizations,
        ]);
    }

}
