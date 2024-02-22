<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utilization;
use App\Models\Proponent;
use App\Models\Fundsource;
use App\Models\User;
use PDF;

class UtilizationController extends Controller
{
    //
    public function tracking(Request $request){

        $dv = Utilization::with(['proponentdata', 'fundSourcedata'])
        ->where('fundsource_id', $request->fundsourceId)
        ->where('proponentinfo_id', $request->proponentInfoId)
        ->where('facility_id', $request->facilityId)
        ->get();

        $userIds = $dv->pluck('created_by')->toArray();
        $user=[];
        foreach($userIds as $id){
            $user[] = User::where('userid', $id)->first();
        }
        // return $user;
        $data = ['dv' => $dv, 'user'=>$user];
        return response()->json($data);
    }
   
    public function trackingBudget($fundsourceId, $type){
        $utilization = Utilization::whereNotNull('obligated')->where('fundsource_id', $fundsourceId)
            ->with('proponentdata', 'fundSourcedata', 'facilitydata', 'user_budget')->orderBy('id', 'desc')->get();
        if($type == 'for_modal'){
            return $utilization;
        }else if ($type == 'pdf'){
            $pdf = PDF::loadView('fundsource_budget.budget_pdf', $data=['utilization'=>$utilization->sortBy('id')]);
            $pdf->setPaper('A4');
            return $pdf->stream('fundsource_budget.budget_pdf');
        }
    }
}
