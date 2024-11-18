<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Utilization;
use App\Models\Proponent;
use App\Models\Fundsource;
use App\Models\User;
use App\Models\Patients;
use App\Models\Facility;
use App\Models\Dv;
use App\Models\Dv2;
use App\Models\Group;
use App\Models\ProponentInfo;
use App\Models\ProponentUtilizationV1;
use Illuminate\Pagination\LengthAwarePaginator;


class ProponentController extends Controller
{
    public function __construct(){
       $this->middleware('auth');
    }

    public function proponentList(Request $req){
        $proponents = Proponent::select( DB::raw('MAX(id) as id'), DB::raw('MAX(proponent) as proponent'), 
                        DB::raw('MAX(proponent_code) as proponent_code'))
                        ->groupBy('proponent_code')
                        ->orderBy('id', 'desc'); 
        if($req->viewAll){
            $req->keyword = '';
        }else if($req->keyword){
            $proponents->where('proponent', 'LIKE', "%$req->keyword%")->orWhere('proponent_code', 'LIKE', "%$req->keyword%");
        } 
        return view('proponents.proponents', [
            'proponents' => $proponents->paginate(50),
            'keyword' => $req->keyword,
            'all_proponents' => Proponent::get()
        ]);
    }

    public function updateProponent(Request $req){
        if($req->id){
            $pro = Proponent::where('id', $req->id)->first();
            $all = Proponent::where('proponent_code', $pro->proponent_code)->get();

            $exists = Proponent::where('proponent_code', $req->proponent_code)->get();
            
            foreach($all as $p){
                $p->proponent = $req->proponent;
                $p->proponent_code = $req->proponent_code;
                $p->save();
            }
            
            return redirect()->back()->with('update_proponent', true);
        }else{
            return redirect()->back()->with('unreachable', true);
        }
    }

    public function onHold(Request $req){
        $proponents = Proponent::select(
                DB::raw('MAX(id) as id'), 
                DB::raw('MAX(proponent) as proponent'), 
                DB::raw('MAX(proponent_code) as proponent_code')
            )
            ->groupBy('proponent_code')
            ->whereNotNull('status')
            ->orderBy('id', 'desc');
        
        $on_hold = Proponent::select(
                DB::raw('MAX(id) as id'), 
                DB::raw('MAX(proponent) as proponent'), 
                DB::raw('MAX(proponent_code) as proponent_code')
            )
            ->groupBy('proponent_code')
            ->whereNull('status')
            ->orderBy('id', 'desc')
            ->get();
        
        if ($req->viewAll) {
            $req->keyword = '';
        } else if ($req->keyword) {
            $proponents->where(function($query) use ($req) {
                $query->where('proponent', 'LIKE', "%{$req->keyword}%")
                    ->orWhere('proponent_code', 'LIKE', "%{$req->keyword}%");
            });
        }

        return view('proponents.proponent_hold', [
            'proponents' => $proponents->paginate(50),
            'keyword' => $req->keyword,
            'hold' => $on_hold
        ]);
    }

    public function holdPro(Request $req){
        if($req->proponent_id){
            Proponent::whereIn('proponent_code', $req->proponent_id)->update(['status' => 1]);
            return redirect()->back();
        }
    }

    public function release($code){
        Proponent::where('proponent_code', $code)->update(['status' => null]);
    }
//     public function fundsource(Request $req)
// {

//     if(Auth::user()->userid != 2760){
//         return 'ongoing updates';
//     }
//     $keyword = $req->viewAll ? '' : $req->keyword;

//     // Build base query with aggregations
//     $proponentsQuery = Proponent::query()
//         ->leftJoin('proponent_info', 'proponent.id', '=', 'proponent_info.proponent_id')
//         ->leftJoin('patients', 'proponent.id', '=', 'patients.proponent_id')
//         ->select(
//             'proponent.proponent_code',
//             DB::raw('MAX(proponent.id) as id'),
//             DB::raw('MAX(proponent.proponent) as proponent'),
//             DB::raw('SUM(COALESCE(CAST(REPLACE(proponent_info.alocated_funds, ",", "") AS DECIMAL(10,2)), 0) 
//                       - COALESCE(CAST(REPLACE(proponent_info.admin_cost, ",", "") AS DECIMAL(10,2)), 0)) as total_sum'),
//             DB::raw('SUM(COALESCE(CAST(REPLACE(patients.guaranteed_amount, ",", "") AS DECIMAL(10,2)), 0)) as total_util_sum')
//         )
//         ->groupBy('proponent.proponent_code');

//     // Apply keyword filter if provided
//     if (!empty($keyword)) {
//         $proponentsQuery->where('proponent.proponent', 'LIKE', "%$keyword%");
//     }

//     // Paginate results
//     $perPage = 51;
//     $proponents = $proponentsQuery->paginate($perPage);

//     // Calculate remaining funds and format the result
//     $all_data = $proponents->map(function ($row) {
//         $total_sum = (float) $row->total_sum;
//         $util_sum = (float) $row->total_util_sum;
//         return [
//             'proponent' => $row,
//             'sum' => $total_sum,
//             'rem' => $total_sum - $util_sum,
//         ];
//     });
    
//     // Pass data to the view
//     return view('proponents.fundsource', [
//         'data' => $all_data,
//         'keyword' => $keyword,
//     ]);
// }


    // public function fundsource(Request $req){

    //     if(Auth::user()->userid != 2760){
    //         return 'ongoing updates';
    //     }
    //     $keyword = $req->keyword;

    //     if($req->viewAll){
    //         $keyword = '';
    //     }

    //     if($keyword){
    //         $proponents = Proponent::where('proponent', 'LIKE', "%$keyword%")
    //         ->select(
    //             DB::raw('MAX(id) as id'), 
    //             DB::raw('MAX(proponent) as proponent'), 
    //             DB::raw('MAX(proponent_code) as proponent_code')
    //         )
    //         ->groupBy('proponent_code')
    //         ->get();
    //     }else{
    //         $proponents = Proponent::select(
    //             DB::raw('MAX(id) as id'), 
    //             DB::raw('MAX(proponent) as proponent'), 
    //             DB::raw('MAX(proponent_code) as proponent_code')
    //         )
    //         ->groupBy('proponent_code')
    //         ->get();
    //     }
    //     $all_data = [];
    //     foreach($proponents as $row){
    //         $ids = Proponent::where('proponent_code', $row->proponent_code)->pluck('id')->toArray();
           
    //         $sum = ProponentInfo::whereIn('proponent_id', $ids)
    //         ->sum(DB::raw('CAST(REPLACE(alocated_funds, ",", "") AS DECIMAL(10,2)) - CAST(REPLACE(admin_cost, ",", "") AS DECIMAL(10,2))'));

    //         $util_sum = Patients::whereIn('proponent_id', $ids)
    //             ->sum(DB::raw('CAST(REPLACE(guaranteed_amount, ",", "") AS DECIMAL(10,2))'));
        
    //         // $sum = ProponentInfo::whereIn('proponent_id', $ids) ->sum('in_balance');
    //         // $util_sum = ProponentUtilizationV1::where('proponent_code', $row->proponent_code)->sum('amount');
    //         $all_data[] =[
    //             'proponent' => $row,
    //             'sum' => $sum,
    //             'rem' => $sum - $util_sum
    //         ];
    //     }

    //     $page = LengthAwarePaginator::resolveCurrentPage();
    //     $perPage = 51; 
    //     $currentPageData = array_slice($all_data, ($page - 1) * $perPage, $perPage); 
    //     $all_data_paginated = new LengthAwarePaginator(
    //         $currentPageData, 
    //         count($all_data), 
    //         $perPage, 
    //         $page, 
    //         ['path' => LengthAwarePaginator::resolveCurrentPath()] 
    //     );

    //     return view('proponents.fundsource', [
    //         'data' => $all_data_paginated,
    //         'keyword' => $keyword
    //     ]);
    // }

    public function fundsource(Request $request)
    {
        // if (Auth::user()->userid != 2760) {
        //     return 'ongoing updates';
        // }

        $keyword = $request->viewAll ? '' : $request->keyword;
        $perPage = 51;

        $query = Proponent::select([
            DB::raw('MAX(id) as id'),
            DB::raw('MAX(proponent) as proponent'),
            'proponent_code'
        ])
        ->when($keyword, function ($query) use ($keyword) {
            return $query->where('proponent', 'LIKE', "%{$keyword}%");
        })
        ->groupBy('proponent_code');

        $proponents = $query->get();

        $proponentIdMap = Proponent::whereIn('proponent_code', $proponents->pluck('proponent_code'))
            ->select('id', 'proponent_code')
            ->get()
            ->groupBy('proponent_code');

        $fundsData = ProponentInfo::whereIn('proponent_id', $proponentIdMap->flatten()->pluck('id'))
            ->select(
                'proponent_id',
                DB::raw('SUM(CAST(REPLACE(alocated_funds, ",", "") AS DECIMAL(10,2)) - CAST(REPLACE(admin_cost, ",", "") AS DECIMAL(10,2))) as total_amount')
            )
            ->groupBy('proponent_id')
            ->get()
            ->groupBy('proponent_id');

        $utilizationData = Patients::whereIn('proponent_id', $proponentIdMap->flatten()->pluck('id'))
            ->select(
                'proponent_id',
                DB::raw('SUM(CAST(REPLACE(guaranteed_amount, ",", "") AS DECIMAL(10,2))) as total_utilized')
            )
            ->groupBy('proponent_id')
            ->get()
            ->groupBy('proponent_id');

        $allData = $proponents->map(function ($proponent) use ($proponentIdMap, $fundsData, $utilizationData) {
            $proponentIds = $proponentIdMap->get($proponent->proponent_code)->pluck('id');
            
            $totalFunds = $proponentIds->sum(function ($id) use ($fundsData) {
                return $fundsData->get($id)?->first()?->total_amount ?? 0;
            });
            
            $totalUtilized = $proponentIds->sum(function ($id) use ($utilizationData) {
                return $utilizationData->get($id)?->first()?->total_utilized ?? 0;
            });

            return [
                'proponent' => $proponent,
                'sum' => $totalFunds,
                'rem' => $totalFunds - $totalUtilized
            ];
        });

        $paginatedData = new LengthAwarePaginator(
            $allData->forPage(LengthAwarePaginator::resolveCurrentPage(), $perPage),
            $allData->count(),
            $perPage,
            null,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        return view('proponents.fundsource', [
            'data' => $paginatedData,
            'keyword' => $keyword
        ]);
    }

    public function tracking($code){
        // $tracking = ProponentUtilizationV1::where('proponent_code', $code)
        //     ->with([
        //         'proponent:id,proponent',
        //         'patient' => function($query){
        //             $query->with([
        //                 'facility:id,name',
        //                 'encoded_by:userid,fname,lname'
        //             ]);
        //         }
        //     ])->get();
        $proponent = Proponent::where('proponent_code', $code)->first();
        $ids = Proponent::where('proponent', $proponent->proponent)->pluck('id')->toArray();
        $tracking = Patients::whereIn('proponent_id', $ids)->with('facility:id,name','encoded_by:userid,fname,lname,mname', 'gl_user:username,fname,lname')->get();

        if(count($tracking) > 0){
            return view('proponents.proponent_util',[
                'data' => $tracking
            ]);
        }else{
            return 0;
        }
      
    }
}
