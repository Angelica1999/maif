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
use App\Models\NewDV;
use App\Models\Dv2;
use App\Models\Group;
use App\Models\ProponentInfo;
use App\Models\Dv3Fundsource;
use App\Models\ProponentUtilizationV1;
use App\Models\SupplementalFunds;
use App\Models\SubtractedFunds;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

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
        try {
            $keyword = $request->viewAll ? '' : $request->keyword;
            $perPage = 51;

            $proponentGroups = Proponent::when($keyword, function ($query) use ($keyword) {
                    return $query->where('proponent', 'LIKE', "%{$keyword}%");
                })
                ->select('id', 'proponent')
                ->orderBy('proponent')
                ->get()
                ->groupBy('proponent');

            if ($proponentGroups->isEmpty()) {
                return view('maif.pro_fundsource', [
                    'data' => [],
                    'keyword' => $keyword,
                    'facilities' => Facility::select('id', 'name')->get(),
                    'user' => Auth::user()->user_type
                ]);
            }

            $allProponentIds = $proponentGroups->map(function ($group) {
                return $group->pluck('id')->toArray();
            });

            $fundsData = ProponentInfo::whereIn('proponent_id', $allProponentIds->flatten()->toArray())
                ->selectRaw('
                    proponent_id,
                    SUM(CAST(NULLIF(REPLACE(COALESCE(alocated_funds, "0"), ",", ""), "") AS DECIMAL(20,2))) as total_funds,
                    SUM(CAST(NULLIF(REPLACE(COALESCE(admin_cost, "0"), ",", ""), "") AS DECIMAL(20,2))) as admin_cost
                ')
                ->groupBy('proponent_id')
                ->get()
                ->groupBy('proponent_id');

            $utilizationData = Patients::whereIn('proponent_id', $allProponentIds->flatten()->toArray())
                ->where(function ($query) {
                    $query->where('expired', '!=', 1)
                        ->orWhereNull('expired');
                })
                ->selectRaw('
                    proponent_id,
                    SUM(
                        CASE 
                            WHEN actual_amount IS NOT NULL AND actual_amount != "" 
                            THEN CAST(REPLACE(actual_amount, ",", "") AS DECIMAL(20, 2))
                            ELSE CAST(REPLACE(COALESCE(guaranteed_amount, "0"), ",", "") AS DECIMAL(20, 2))
                        END
                    ) as total_utilized
                ')
                ->groupBy('proponent_id')
                ->get()
                ->groupBy('proponent_id');

            $supplementalFunds = SupplementalFunds::whereIn('proponent', $proponentGroups->keys())
                ->selectRaw('
                    proponent,
                    SUM(CAST(NULLIF(REPLACE(COALESCE(amount, "0"), ",", ""), "") AS DECIMAL(20,2))) as total_amount
                ')
                ->groupBy('proponent')
                ->get()
                ->keyBy('proponent');

            $subtractedFunds = DB::table('subtracted_funds')
                ->whereIn('proponent', $proponentGroups->keys())
                ->selectRaw('
                    proponent,
                    SUM(CAST(NULLIF(REPLACE(COALESCE(amount, "0"), ",", ""), "") AS DECIMAL(20,2))) as total_amount
                ')
                ->groupBy('proponent')
                ->get()
                ->keyBy('proponent');

            $dv1Data = Utilization::whereIn('proponent_id', $allProponentIds->flatten()->toArray())
                ->where('status', 0)
                ->where('facility_id', 837)
                ->where(function ($query) {
                    $query->whereHas('dv', function ($q) {
                        $q->whereColumn('div_id', 'route_no');
                    })->orWhereHas('newDv', function ($q) {
                        $q->whereColumn('div_id', 'route_no');
                    });
                })
                ->selectRaw('
                    proponent_id,
                    SUM(CAST(NULLIF(REPLACE(COALESCE(utilize_amount, "0"), ",", ""), "") AS DECIMAL(20,2))) as total_amount
                ')
                ->groupBy('proponent_id')
                ->get()
                ->groupBy('proponent_id');

            $dv3Data= Utilization::whereIn('proponent_id', $allProponentIds->flatten()->toArray())
                ->where('status', 0)
                ->where(function ($query) {
                    $query->whereHas('dv3', function ($q) {
                        $q->whereColumn('div_id', 'route_no');
                    });
                })
                ->selectRaw('
                    proponent_id,
                    SUM(CAST(NULLIF(REPLACE(COALESCE(utilize_amount, "0"), ",", ""), "") AS DECIMAL(20,2))) as total_amount
                ')
                ->groupBy('proponent_id')
                ->get()
                ->groupBy('proponent_id');

            $allData = $proponentGroups->map(function ($proponentGroup, $proponentName) use (
                $fundsData,
                $utilizationData,
                $supplementalFunds,
                $subtractedFunds,
                $dv1Data,
                $dv3Data
            ) {
                $proponentIds = $proponentGroup->pluck('id');
                
                $totalFunds = 0;
                $totalAdminCost = 0;
                $totalUtilized = 0;
                $totalDv1Amount = 0;
                $totalDv3Amount = 0;

                foreach ($proponentIds as $id) {
                    if ($fundsData->has($id)) {
                        $fundInfo = $fundsData->get($id)->first();
                        $totalFunds += $fundInfo->total_funds ?? 0;
                        $totalAdminCost += $fundInfo->admin_cost ?? 0;
                    }

                    if ($utilizationData->has($id)) {
                        $totalUtilized += $utilizationData->get($id)->sum('total_utilized');
                    }

                    if ($dv1Data->has($id)) {
                        $totalDv1Amount += $dv1Data->get($id)->sum('total_amount');
                    }
                    if ($dv3Data->has($id)) {
                        $totalDv3Amount += $dv3Data->get($id)->sum('total_amount');
                    }
                }

                $supp = $supplementalFunds->get($proponentName)?->total_amount ?? 0;
                $sub = $subtractedFunds->get($proponentName)?->total_amount ?? 0;

                $netFunds = $totalFunds - $totalAdminCost;
                $remaining = $netFunds - $totalUtilized;
                $finalRemaining = $remaining + $supp - ($totalDv1Amount + $sub);

                return [
                    'proponent' => $proponentGroup->first(),
                    'sum' => round($netFunds, 2),
                    'rem' => round($finalRemaining - $totalDv3Amount , 2),
                    'supp' => round($supp, 2),
                    'sub' => round($sub, 2),
                    'disbursement' => round($totalDv1Amount + $totalDv3Amount, 2),
                    'allocated_cost' => round($totalAdminCost, 2),
                    'totalUtilized' => round($totalUtilized, 2),
                    'admin_cost' => round($totalAdminCost, 2),
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
                'keyword' => $keyword,
                'facilities' => Facility::select('id', 'name')->get(),
                'user' => Auth::user()->user_type
            ]);

        } catch (\Exception $e) {
            \Log::error('ProFunds Error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while processing the data. Please try again.');
        }
    }

    public function delGL($id){
        $patient = Patients::where('id', $id)->first();

        if (!$patient) {
            Log::warning("Patient with ID {$id} not found for deletion.");
            return response()->json(['success' => false]);
        }

        Log::info("Deleting patient record: ", $patient->toArray());
        $patient->delete();
        return response()->json(['success' => true]);
      }

    public function tracking($code){
        $ids = Proponent::where('proponent', $code)->pluck('id')->toArray();
        $tracking = Patients::whereIn('proponent_id', $ids)->with('facility:id,name','encoded_by:userid,fname,lname,mname', 'gl_user:username,fname,lname')->paginate(20);
        $facilities = Facility::whereIn('id', Patients::whereIn('proponent_id', $ids)->pluck('facility_id')->toArray())->select('id', 'name')->get(); 
        $info = ProponentInfo::whereIn('proponent_id', $ids)->pluck('id')->toArray();
        
        $dv1 = Utilization::whereIn('proponent_id', $ids)
            ->where('status', 0)
            ->where('facility_id', 837)
            ->where(function ($query) {
                $query->whereHas('dv', function ($query) {
                    $query->whereColumn('div_id', 'route_no');
                })->orWhereHas('newDv', function ($query) {
                    $query->whereColumn('div_id', 'route_no');
                });
            })
            ->with([
                'fundSourcedata:id,saa',
                'user:userid,fname,lname',
            ])
            ->orderBy('id', 'desc')
            ->get();

        $dv3_fundsources = Dv3Fundsource::whereIn('info_id', $info)
            ->with([
                'dv3' => function ($query){
                    $query->with([
                        'facility:id,name',
                        'user:userid,fname,lname'
                    ]);
                },
                'fundsource:id,saa'
            ]);

        if(count($tracking) > 0){
            return view('proponents.proponent_util',[
                'data' => $tracking,
                'facilities' => $facilities,
                'dv3' => $dv3_fundsources->orderBy('id', 'desc')->get(),
                'dv1' => $dv1
            ]);
        }else{
            return 0;
        }
      
    }

    public function filterData(Request $request){
        $f_ids = $request->f_id;
        $pro_code = $request->pro_code;
        $ids = Proponent::where('proponent', $pro_code)->pluck('id')->toArray();
        $info = ProponentInfo::whereIn('proponent_id', $ids)->pluck('id')->toArray();
        
        $dv1 = Utilization::whereIn('proponent_id', $ids)
            ->where('status', 0)
            ->where('facility_id', 837)
            ->where(function ($query) {
                $query->whereHas('dv', function ($query) {
                    $query->whereColumn('div_id', 'route_no');
                })->orWhereHas('newDv', function ($query) {
                    $query->whereColumn('div_id', 'route_no');
                });
            })
            ->with([
                'fundSourcedata:id,saa',
                'user:userid,fname,lname',
            ])
            ->orderBy('id', 'desc')
            ->get();

        $dv3_fundsources = Dv3Fundsource::whereIn('info_id', $info)
            ->with([
                'dv3' => function ($query){
                    $query->with([
                        'facility:id,name',
                        'user:userid,fname,lname'
                    ]);
                },
                'fundsource:id,saa'
            ]);
        if (in_array("all", $f_ids)) {
            $tracking = Patients::whereIn('proponent_id', $ids)
                ->with('facility:id,name','encoded_by:userid,fname,lname,mname', 'gl_user:username,fname,lname')->paginate(20);
        }else{
            $tracking = Patients::whereIn('proponent_id', $ids)->whereIn('facility_id', $f_ids)
                ->with('facility:id,name','encoded_by:userid,fname,lname,mname', 'gl_user:username,fname,lname')->paginate(20);
        }
        $facilities = Facility::whereIn('id', Patients::whereIn('proponent_id', $ids)->pluck('facility_id')->toArray())->select('id', 'name')->get(); 
        return view('proponents.proponent_util',[
            'data' => $tracking,
            'facilities' => $facilities,
            'dv3' => $dv3_fundsources->orderBy('id', 'desc')->get(),
            'dv1' => $dv1
        ]);
    }
    
    public function manageFunds(Request $request){
        if($request->funds_type == 1){
            $supplemental = new SupplementalFunds();
            $supplemental->proponent = $request->proponent;
            $supplemental->amount = (float) str_replace(',', '', $request->amount);
            $supplemental->added_by = Auth::user()->userid;
            $supplemental->remarks = $request->remarks;
            $supplemental->save();
        }else if($request->funds_type == 2){
            $subtracted = new SubtractedFunds();
            $subtracted->proponent = $request->proponent;
            $subtracted->amount = (float) str_replace(',', '', $request->amount);
            $subtracted->subtracted_by = Auth::user()->userid;
            $subtracted->remarks = $request->remarks;
            $subtracted->save();
        }
        return redirect()->back()->with('manage_funds', true);
    }

    public function supplemental($proponent, $amount)
    {
        $supplemental = new SupplementalFunds();
        $supplemental->proponent = $proponent;
        $supplemental->amount = (float) str_replace(',', '', $amount);
        $supplemental->added_by = Auth::user()->userid;
        $supplemental->save();

        return response()->json([
            'message' => 'Supplemental fund added successfully',
            'data' => $supplemental,
        ], 200);
    }

    

    public function subtracted($proponent, $amount)
    {
        $subtracted = new SubtractedFunds();
        $subtracted->proponent = $proponent;
        $subtracted->amount = (float) str_replace(',', '', $amount);
        $subtracted->subtracted_by = Auth::user()->userid;
        $subtracted->save();

        return response()->json([
            'message' => 'Funds was successfully deducted!',
            'data' => $subtracted,
        ], 200);
    }

    public function supDetails($proponent){
        $supp = SupplementalFunds::where('proponent', $proponent)->with('user:userid,fname,lname')->get();
        return view('proponents.proponent_supplemental', [
            'data' => $supp
        ]);
    }

    public function subDetails($proponent){
        $sub = SubtractedFunds::where('proponent', $proponent)->with('user:userid,fname,lname')->get();
        return view('proponents.proponent_subtracted', [
            'data' => $sub
        ]);
    }

    public function supUpdate($id, $amount){
        $supplemental = SupplementalFunds::where('id', $id)->first();
    
        if($supplemental){
            $supplemental->amount = (float) str_replace(',', '', $amount);
            $supplemental->added_by = Auth::user()->userid;
            $supplemental->save();

            return response()->json([
                'message' => 'Supplemental fund added successfully'
            ], 200);
        }
    }

    public function subUpdate($id, $amount){
        $subtracted = SubtractedFunds::where('id', $id)->first();
    
        if($subtracted){
            $subtracted->amount = (float) str_replace(',', '', $amount);
            $subtracted->subtracted_by = Auth::user()->userid;
            $subtracted->save();

            return response()->json([
                'message' => 'success'
            ], 200);
        }
    }

    public function excelData($code, $ids){
        $pro = Proponent::where('proponent', $code)->get();
        $id = $pro->pluck('id')->toArray();

        if($ids == 0){
            $patients = Patients::whereIn('proponent_id', $id)->with([
                'facility:id,name',
                'encoded_by:userid,fname,lname,mname',
                'gl_user:username,fname,lname'
            ])->get();
        }else{
            $patients = Patients::whereIn('proponent_id', $id)
            ->whereIn('facility_id', $ids)->with([
                'facility:id,name',
                'encoded_by:userid,fname,lname,mname',
                'gl_user:username,fname,lname'
            ])->get();
        }

        $title = $pro[0]->proponent;
        $filename = $title.'.xls';
        header("Content-Type: application/xls");
        header("Content-Disposition: attachment; filename=$filename");
        header("Pragma: no-cache");
        header("Expires: 0");
        $table_body = "<tr>
                <th>Patient Code</th>
                <th>Name</th>
                <th>Guaranteed Amount</th>
                <th>Actual Amount</th>
                <th>Facility</th>
                <th>Created By</th>
                <th>Created On</th>
            </tr>";

        if(count($patients) > 0){
            foreach($patients as $row){
                $name = $row->lname .', '.$row->fname.' '.$row->mname;
                $guaranteed = number_format(str_replace(',','',$row->guaranteed_amount), 2,'.',',');
                $actual = number_format($row->actual_amount, 2,'.',',');
                $facility = $row->facility->name;
                $user = !Empty($row->encoded_by) ? $row->encoded_by->lname .', '.$row->encoded_by->fname : 
                        (!Empty($row->gl_user) ? $row->gl_user->lname.', '.$row->gl_user->fname : '');
                $on = date('F j, Y', strtotime($row->created_at));
                $table_body .= "<tr>
                    <td style='vertical-align:top;'>$row->patient_code</td>
                    <td style='vertical-align:top;'>$name</td>
                    <td style='vertical-align:top;'>$guaranteed</td>
                    <td style='vertical-align:top;'>$actual</td>
                    <td style='vertical-align:top;'>$facility</td>
                    <td style='vertical-align:top;'>$user</td>
                    <td style='vertical-align:top;'>$on</td>
                </tr>";
            }
        }else{
            $table_body .= "<tr>
                <td colspan=7 style='vertical-align:top;'>No Data Available</td>
            </tr>";
        }
        $display =
            '<h1>'.$title.'</h1>'.
            '<table cellspacing="1" cellpadding="5" border="1">'.$table_body.'</table>';

        return $display;
    }
    
}
