<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Fundsource;
use App\Models\Utilization;
use App\Models\Proponent;
use App\Models\ProponentInfo;
use App\Models\Facility;

class DashboardController extends Controller{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('block.secure.nonadmin');
    }
    
    public function dashboard(Request $req){
        $funds = Fundsource::selectRaw('SUM(CAST(REPLACE(alocated_funds, ",", "") AS DECIMAL(10,2))) as total_amount')
        ->selectRaw('SUM(CAST(REPLACE(admin_cost, ",", "") AS DECIMAL(10,2))) as total_cost')
        ->first();

        $utilization = Utilization::where('status', 0)
        ->selectRaw('
            IFNULL(SUM(CAST(REPLACE(utilize_amount, ",", "") AS DECIMAL(10,2))), 0) as total_utilize,
            IFNULL(SUM(CASE WHEN paid is null and obligated = 1 THEN CAST(REPLACE(utilize_amount, ",", "") AS DECIMAL(10,2)) ELSE 0 END), 0) as total_obligated,
            IFNULL(SUM(CASE WHEN paid = 1 THEN CAST(REPLACE(utilize_amount, ",", "") AS DECIMAL(10,2)) ELSE 0 END), 0) as total_paid,
            IFNULL(SUM(CASE WHEN obligated IS NULL AND paid IS NULL THEN CAST(REPLACE(utilize_amount, ",", "") AS DECIMAL(10,2)) ELSE 0 END), 0) as total_pending
        ')
        ->first();  

        $total_amount = $funds->total_amount ?? 0;
        $total_cost = $funds->total_cost ?? 0;
        $total_utilization = $utilization->total_utilize ?? 0;
        $remaining_balance = $total_amount - $total_cost - $total_utilization;
        $utilization_rate = $total_utilization / ($total_amount - $total_cost) * 100;

        if($req->stat){ 
            $dateRange = explode(' - ', $req->status_filtered);
            $start_date = date('Y-m-d', strtotime($dateRange[0]));
            $end_date = date('Y-m-d', strtotime($dateRange[1]));
            $utilization = Utilization::where('status', 0)
            ->whereBetween('created_at', [$start_date, $end_date . ' 23:59:59'])
            ->selectRaw('
                IFNULL(SUM(CAST(REPLACE(utilize_amount, ",", "") AS DECIMAL(10,2))), 0) as total_utilize,
                IFNULL(SUM(CASE WHEN obligated = 1 THEN CAST(REPLACE(utilize_amount, ",", "") AS DECIMAL(10,2)) ELSE 0 END), 0) as total_obligated,
                IFNULL(SUM(CASE WHEN paid = 1 THEN CAST(REPLACE(utilize_amount, ",", "") AS DECIMAL(10,2)) ELSE 0 END), 0) as total_paid,
                IFNULL(SUM(CASE WHEN obligated IS NULL AND paid IS NULL THEN CAST(REPLACE(utilize_amount, ",", "") AS DECIMAL(10,2)) ELSE 0 END), 0) as total_pending
            ')
            ->first();
        }

        $total_utilization1 = $utilization->total_utilize ?? 0;
        $total_pending = $utilization->total_pending ?? 0;
        $total_paid = $utilization->total_paid ?? 0;
        $total_obligated = $utilization->total_obligated ?? 0;

        $proponentGroups = Proponent::select('id', 'proponent')->orderBy('proponent')->get()->groupBy('proponent');
        $allProponentIds = $proponentGroups->map(function ($group) {
            return $group->pluck('id')->toArray();
        });

        $fundsData = ProponentInfo::whereIn('proponent_id', $allProponentIds->flatten()->toArray())
                ->selectRaw('
                    proponent_id,
                    SUM(CAST(NULLIF(REPLACE(COALESCE(alocated_funds, "0"), ",", ""), "") AS DECIMAL(20,2))) as total_funds
                ')
                ->groupBy('proponent_id')
                ->get()
                ->groupBy('proponent_id');

        $allData = $proponentGroups->map(function ($proponentGroup, $proponentName) use (
            $fundsData
        ) {
            $proponentIds = $proponentGroup->pluck('id');
            
            $totalFunds = 0;

            foreach ($proponentIds as $id) {
                if ($fundsData->has($id)) {
                    $fundInfo = $fundsData->get($id)->first();
                    $totalFunds += $fundInfo->total_funds ?? 0;
                }
            }

            return [
                'proponent' => $proponentGroup->first(),
                'sum' => round($totalFunds, 2)
            ];
        });

        $facilities = ProponentInfo::all()->groupBy(function ($item) {
            $facilityIds = is_array($item->facility_id) ? $item->facility_id : json_decode($item->facility_id, true);
            
            if (!is_array($facilityIds)) {
                $facilityIds = [$facilityIds];
            }
        
            sort($facilityIds);
            return implode(',', $facilityIds);
        })->map(function ($group, $key) {
            $facilityIds = explode(',', $key);
        
            $facilityNames = Facility::whereIn('id', $facilityIds)->pluck('name')->filter()->implode(', ');
        
            return [
                'total_allocated_funds' => $group->sum(fn($item) => (float) str_replace(',', '', $item->alocated_funds)),
                'facility_names' => $facilityNames
            ];
        });

        $utilization_disbursed = Utilization::with('facilitydata:id,name')
            ->where('status', 0)
            ->whereNotNull('paid')
            ->get()
            ->groupBy('facility_id')
            ->map(function ($items) {
                return [
                    'facility_name' => $items->first()->facilitydata->name,
                    'total_utilize_amount' => str_replace(',', '', $items->sum('utilize_amount')),
                ];
            })
            ->sortBy('facility_name') 
            ->values(); 

        $utilization_trend = Utilization::where('status', 0)
            ->whereNotNull('paid')
            ->get()
            ->groupBy(function ($item) {
                return \Carbon\Carbon::parse($item->created_at)->format('M'); 
            })
            ->map(function ($items, $month) {
                return [
                    'month' => strtoupper($month), 
                    'total_utilize_amount' => number_format($items->sum('utilize_amount'), 2, '.', ''), 
                ];
            })
            ->sortBy(function ($item) {
                $months = ['JAN' => 1, 'FEB' => 2, 'MAR' => 3, 'APR' => 4, 'MAY' => 5, 'JUN' => 6, 
                        'JUL' => 7, 'AUG' => 8, 'SEP' => 9, 'OCT' => 10, 'NOV' => 11, 'DEC' => 12];
        
                return $months[$item['month']] ?? 999; 
            })
            ->values();  

        if($req->year){
            $year = $req->year;
        }else{
            $year = date('Y');
        }

        $utilization_trend = Utilization::where('status', 0)
            ->whereNotNull('paid')
            ->whereYear('created_at', $year) 
            ->get()
            ->groupBy(function ($item) {
                return \Carbon\Carbon::parse($item->created_at)->format('M'); 
            })
            ->map(function ($items, $month) {
                return [
                    'month' => strtoupper($month), 
                    'total_utilize_amount' => number_format($items->sum('utilize_amount'), 2, '.', ''), 
                ];
            })
            ->sortBy(function ($item) {
                $months = ['JAN' => 1, 'FEB' => 2, 'MAR' => 3, 'APR' => 4, 'MAY' => 5, 'JUN' => 6, 
                        'JUL' => 7, 'AUG' => 8, 'SEP' => 9, 'OCT' => 10, 'NOV' => 11, 'DEC' => 12];

                return $months[$item['month']] ?? 999; 
            })
            ->values();

        return view('dashboard', [
            'total_amount' => $total_amount, 
            'total_cost' => $total_cost,
            'total_utilization' => $total_utilization,
            'total_utilization1' => $total_utilization1,
            'total_pending' => $total_pending,
            'total_obligated' => $total_obligated,
            'total_paid' => $total_paid,
            'remaining_balance' => $remaining_balance,
            'utilization_rate' => $utilization_rate,
            'proponents' => $allData,
            'facilities' => $facilities,
            'disbursed' => $utilization_disbursed,
            'trend' => $utilization_trend,
            'year' => $year
        ]);
    }
}
