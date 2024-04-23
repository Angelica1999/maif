<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utilization;
use App\Models\Proponent;
use App\Models\Fundsource;
use App\Models\User;
use PDF;

class UtilizationController extends Controller{
    //
    public function tracking($info_id){
        
        $utilization = Utilization::where('proponentinfo_id', $info_id)
                            ->with(['user', 'proponentdata', 'fundSourcedata', 'transfer'])
                            ->get();
                            // ->groupBy('div_id')
                            // ->map(function ($group) {
                            //     return $group->sortByDesc('created_at')->first();
                            // })
                            // ->values();    
        return response()->json($utilization);
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
