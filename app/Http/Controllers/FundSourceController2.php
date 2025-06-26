<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utilization;
use App\Models\Proponent;
use App\Models\Facility;
use App\Models\ProponentInfo;
use App\Models\Fundsource;
use App\Models\User;
use App\Models\Dv;
use App\Models\Dv3;
use App\Models\NewDV;
use App\Models\PreDv;
use App\Models\AdminCostUtilization;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Kyslik\ColumnSortable\Sortable;

class FundSourceController2 extends Controller{

    public function __construct(){
        $this->middleware('auth');
    }

    public function sample(Request $request) {
        $all = ProponentInfo::get();
        foreach($all as $row){
            $row->in_balance = (float) str_replace(',','', $row->remaining_balance);
            $row->save();
        }
    }

    public function fundSource2(Request $request) {
        // if(Auth::user()->userid != 2163){
        //     return "under development";
        // }
        $section = DB::connection('dohdtr')
            ->table('users')
            ->leftJoin('dts.users', 'users.userid', '=', 'dts.users.username')
            ->where('users.userid', '=', Auth::user()->userid)
            ->value('users.section');

        $fundsources = Fundsource::orderByRaw("CASE WHEN saa LIKE 'conap%' THEN 0 ELSE 1 END, saa ASC")
            ->with(['proponentInfo' => function($query) {
                    $query->selectRaw('
                        fundsource_id, 
                        sum(CAST(REPLACE(alocated_funds, ",", "") AS DECIMAL(18, 2))) - sum(CAST(REPLACE(admin_cost, ",", "") AS DECIMAL(18, 2))) as total_allocated_funds,
                        sum(CAST(REPLACE(admin_cost, ",", "") AS DECIMAL(18, 2))) as total_admin_cost
                        ')
                    ->groupBy('fundsource_id'); 
                },
                'utilization' => function($query) {
                    $query->selectRaw('
                        fundsource_id,
                        sum(CASE WHEN status = 0 AND obligated = 1 THEN CAST(REPLACE(budget_utilize, ",", "") AS DECIMAL(18, 2)) ELSE 0 END) as total_bbudget_utilize
                        ')
                    ->groupBy('fundsource_id');
                },
                'a_cost' => function($query) {
                    $query->selectRaw('
                        fundsource_id, 
                        SUM(CAST(REPLACE(admin_cost, ",", "") AS DECIMAL(18, 2))) as total_admin_cost
                    ')
                    ->groupBy('fundsource_id');
                }
            ])
            ->paginate(15);

        if($request->viewAll) {
            $request->keyword = '';
        }
        else if($request->keyword) {
            $fundsources = Fundsource::where('saa', 'LIKE', "%$request->keyword%")->orderByRaw("CASE WHEN saa LIKE 'conap%' THEN 0 ELSE 1 END, saa ASC")
            ->with([
                'proponentInfo' => function($query) {
                $query->selectRaw('
                    fundsource_id, 
                    sum(CAST(REPLACE(alocated_funds, ",", "") AS DECIMAL(18, 2))) - sum(CAST(REPLACE(admin_cost, ",", "") AS DECIMAL(18, 2))) as total_allocated_funds,
                    sum(CAST(REPLACE(admin_cost, ",", "") AS DECIMAL(18, 2))) as total_admin_cost
                ')
                ->groupBy('fundsource_id'); 
            },
            'utilization' => function($query) {
                $query->selectRaw('
                    fundsource_id,
                    sum(CASE WHEN status = 0 AND obligated = 1 THEN CAST(REPLACE(budget_utilize, ",", "") AS DECIMAL(18, 2)) ELSE 0 END) as total_bbudget_utilize,
                    sum(CASE WHEN status = 2 AND transfer_type = 2 THEN CAST(REPLACE(utilize_amount, ",", "") AS DECIMAL(18, 2)) ELSE 0 END) as transfer_from_rem
                ')
                ->groupBy('fundsource_id');
            },
            'a_cost' => function($query) {
                $query->selectRaw('
                    fundsource_id, 
                    SUM(CAST(REPLACE(admin_cost, ",", "") AS DECIMAL(18, 2))) as total_admin_cost
                ')
                ->groupBy('fundsource_id');
            }])
            ->paginate(15);
        } 
        // return $fundsources;
        return view('fundsource_budget.fundsource2',[
            'fundsources' => $fundsources,
            'keyword' => $request->keyword,
            'section' => $section
        ]);
    }

    public function createfundSource2(Request $request){
        // $funds = $request->input('allocated_funds');
        // $saas = $request->input('saa');  
        // $cost_value = $request->input('admin_cost');
        // foreach($funds as $index => $fund){
        //     $saa_ex = Fundsource::where('saa', $saas[$index])->first();

        //     if($saa_ex){
        //         session()->flash('saa_exist', true);
        //     }else{
        //         $fundsource = new Fundsource();
        //         $fundsource->saa = $saas[$index];
        //         $fundsource->alocated_funds = str_replace(',','',$fund);
        //         $fundsource->cost_value = $cost_value[$index];

        //         $admin_cost = (double) str_replace(',','',$fund) * ($cost_value[$index]/100);
        //         $fundsource->admin_cost = $admin_cost;
        //         $fundsource->remaining_balance = (double)str_replace(',','',$fund) - $admin_cost ;
                
        //         $fundsource->created_by = Auth::user()->userid;
        //         $fundsource->save();
        //         session()->flash('fundsource_save', true);
        //     }
        // }
        // return redirect()->back();

        $data = $request->input('breakdowns');

        if($data){
            foreach($data as $fundsource){
                $allocated = str_replace(',','', $fundsource['allocated_fund']);
                $admin_cost = (double) str_replace(',','', $fundsource['allocated_fund']) * ($fundsource['admin_cost']/100);

                $funds = new Fundsource();
                $funds->saa = $fundsource['saa'];
                $funds->alocated_funds = $allocated;
                $funds->remaining_balance = $allocated - $admin_cost;
                $funds->admin_cost = $admin_cost;
                $funds->cost_value = $fundsource['admin_cost'];
                $funds->created_by = Auth::user()->userid;
                $funds->save();

                if (isset($fundsource['break_data']) && is_array($fundsource['break_data'])) {
                        foreach($fundsource['break_data'] as $breakdown){
                        $pro_exists = Proponent::where('proponent', $breakdown['proponent'])
                            ->where('fundsource_id', $funds->id)->where('proponent_code', $breakdown['proponent_code'])->first();
                        if(!$pro_exists){
                            $check = Proponent::where('proponent', $breakdown['proponent'])->where('proponent_code', $breakdown['proponent_code'])->first();
                            $proponent = new Proponent();
                            $proponent->fundsource_id = $funds->id;
                            $proponent->proponent = $breakdown['proponent'];
                            $proponent->proponent_code = $breakdown['proponent_code'];
                            $proponent->pro_group = 0;
                            $proponent->created_by = Auth::user()->userid;
                            $proponent->save();
                            if($check){
                                $proponent->pro_group = $check->id;
                            }else{
                                $proponent->pro_group = $proponent->id;
                            }
                            $proponent->save();
                            $proponentId = $proponent->id;
                        }else{
                            $proponentId = $pro_exists->id;
                        }
                        $p_info = new ProponentInfo();
                        $p_info->main_proponent = $breakdown['proponent_main'];
                        $p_info->fundsource_id = $funds->id;
                        $p_info->proponent_id = $proponentId;
                        $p_info->facility_id = json_encode($breakdown['facility_id']);
                        $p_info->alocated_funds = $breakdown['alocated_funds'];
                        $p_info->admin_cost = number_format((double)str_replace(',','',$breakdown['alocated_funds']) * ($funds->cost_value/100) , 2,'.', ',');
                        $rem = (double)str_replace(',','',$breakdown['alocated_funds']) - (double)str_replace(',','',$p_info->admin_cost);
                        $p_info->remaining_balance = $rem;
                        $p_info->facility_funds = $rem;
                        $p_info->proponent_funds = $rem;
                        $p_info->created_by = Auth::user()->userid;
                        $p_info->save();
                    }
                }
            }
        }
    }

    public function pendingDv(Request $request, $type){

        if($type == 'pending'){
            $result = Dv::whereNull('obligated')->with(['fundsource','facility', 'master'])->orderby('id', 'desc');
          // $result = Dv::whereNull('obligated')->whereNotNull('dv_no')->where('dv_no', '!=', '')->with(['fundsource','facility', 'master'])->orderby('id', 'desc');
        }else if($type == 'obligated'){
            $result = Dv::whereNotNull('obligated')->with(['fundsource','facility', 'master'])->orderby('id', 'desc');
          // $result = Dv::whereNotNull('obligated')->whereNotNull('dv_no')->with(['fundsource','facility', 'master'])->orderby('id', 'desc');
        }

        if($request->viewAll){
            $request->keyword = '';
        }else if($request->keyword){
            $result->where('route_no', 'LIKE', "%$request->keyword%");
        }
        $id = $result->pluck('created_by')->unique();
        $name = User::whereIn('userid', $id)->get()->keyBy('userid'); 
        $results = $result->paginate(50);
        
        return view('fundsource_budget.dv_list', [
            'disbursement' => $results,
            'name'=> $name,
            'type' => $type,
            'keyword' => $request->keyword,
            'proponents' => Proponent::get(),
            'proponentInfo' => ProponentInfo::get()
        ]);
    }

    public function cashierPending(Request $request, $type){

        if($type == 'pending'){
            // $result = Dv::whereNotNull('obligated')->whereNotNull('dv_no')->whereNull('paid')->with(['fundsource','facility', 'master'])->orderby('id', 'desc');
            $result = Dv::whereNotNull('obligated')->whereNull('paid')->with(['fundsource','facility', 'master'])->orderby('id', 'desc');
        }else{
            // $result = Dv::whereNotNull('obligated')->whereNotNull('dv_no')->whereNotNull('paid')->with(['fundsource','facility', 'master'])->orderby('id', 'desc');
            $result = Dv::whereNotNull('obligated')->whereNotNull('paid')->with(['fundsource','facility', 'master'])->orderby('id', 'desc');
        }

        if($request->viewAll){
            $request->keyword = '';
        }else if($request->keyword){
            $result->where('route_no', 'LIKE', "%$request->keyword%");
        }
        $id = $result->pluck('created_by')->unique();
        $name = User::whereIn('userid', $id)->get()->keyBy('userid'); 
        $results = $result->paginate(50);
        return view('cashier.pending_dv', [
            'disbursement' => $results,
            'name'=> $name,
            'type' => $type,
            'keyword' => $request->keyword,
            'proponents' => Proponent::get(),
            'proponentInfo' => ProponentInfo::get()
        ]);
    }

    public function cashierPaid(Request $request){

        $result = Dv::whereNotNull('obligated')
                    ->whereNotNull('dv_no')
                    ->whereNotNull('paid')
                    ->with(['fundsource', 'facility', 'master'])
                    ->orderBy('id', 'desc');
        if($request->viewAll){
            $request->keyword = '';
        }else if($request->keyword){
            $result->where('route_no', 'LIKE', "%$request->keyword%");
        }
        $id = $result->pluck('created_by')->unique();
        $name = User::whereIn('userid', $id)->get()->keyBy('userid'); 
        $results = $result->paginate(50);
        return view('cashier.paid_dv', [
            'disbursement' => $results,
            'name'=> $name,
            'keyword' => $request->keyword
        ]);
    }
    
    public function dv_display($route_no, $type){

        $section = DB::connection('dohdtr')
                        ->table('users')
                        ->leftJoin('dts.users', 'users.userid', '=', 'dts.users.username')
                        ->where('users.userid', '=', Auth::user()->userid)
                        ->value('users.section');

        $dv = Dv::where('route_no', $route_no)->with('facility')->first();
        
        if($dv){

            $all= array_map('intval', json_decode($dv->fundsource_id));
            $fund_source = [];
            foreach($all as $id){
                $fund_source []= Fundsource::where('id', $id)->first();
            }

            return view('fundsource_budget.obligate_dv', [ 
            'dv' =>$dv, 
            'section' => $section,
            'fund_source' => $fund_source,
            'type' => $type
            ]);

        }else{
            return redirect()->route('dv3.update', ['route_no' => $route_no]);
        }
    }

    public function budgetTracking($id){
        $saa = Fundsource::where('id', $id)->first();
        if($saa){
            $util = Utilization::where('fundsource_id', $saa->id)
                    ->with([
                    'fundSourcedata:id,saa,remaining_balance,alocated_funds,admin_cost',
                    'proponentdata:id,proponent',
                    'infoData:id,facility_id',
                    'facilitydata:id,name',
                    'dv' => function($query){
                        $query->with('facility:id,name');
                    },
                    'dv3' => function($query){
                        $query->with('facility:id,name');
                    },
                    'newDv' => function($query){    
                        $query->with([
                            'preDv' => function($query){
                                $query->with('facility:id,name');
                            }
                        ]);
                    }
                    ])->where('status', 0)->get();
            $admin_c = AdminCostUtilization::with('fundSourcedata:id,saa')->where('fundsource_id', $saa->id)->get();
            $combi = $util->merge($admin_c);
            $combi = $combi->sortByDesc('updated_at'); // Change to 'updated_by' if needed
            // return $combi;
                    // return $util;
            $perPage = 10;
            $data = new LengthAwarePaginator(
                $combi->forPage(LengthAwarePaginator::resolveCurrentPage(), $perPage),
                $combi->count(),
                $perPage,
                null,
                ['path' => LengthAwarePaginator::resolveCurrentPath()]
            );


            // if(count($combi) > 0){
                return view('fundsource_budget.budget_tracking',[
                    'result' => $data,
                    'facilities' => Facility::get(),
                    'last' => $combi->last(),
                    'confirm' => 0
                ]);
            // }else{
            //     return 'No data available!';
            // }
          
        }else{
            return 'No data found';
        }
    }

    public function orsNo(Request $request){
        if($request->id){
            Utilization::where('id', $request->id)->update(['ors_no' => $request->ors_no]);
        }
        return true;
    }

    public function orsNo2(Request $request){
        $ids = array_map('intval', explode(',', $request->id));
        if($request->id){
            Utilization::whereIn('id', $ids)->update(['ors_no' => $request->ors_no]);
        }
        return true;
    }


    public function uacs(Request $request){
        if($request->id){
            Utilization::where('id', $request->id)->update(['uacs' => $request->uacs]);
        }
        return true;
    }

    public function fundsTracking($id){
        $saa = Fundsource::where('id', $id)
            ->with(['a_cost' => function($query) {
                $query->selectRaw('
                    fundsource_id, 
                    SUM(CAST(REPLACE(admin_cost, ",", "") AS DECIMAL(18, 2))) as total_admin_cost
                ')
                ->groupBy('fundsource_id');
            }])
            ->first();

        // ->with(['proponentInfo' => function($query) {
        //     $query->selectRaw('
        //         fundsource_id, 
        //         sum(CAST(REPLACE(alocated_funds, ",", "") AS DECIMAL(18, 2))) - sum(IFNULL(admin_cost, 0)) as total_allocated_funds,
        //         sum(IFNULL(admin_cost, 0)) as total_admin_cost
        //     ')
        //     ->groupBy('fundsource_id'); 
        // }
        // return $saa;
        if($saa){
            $infos = ProponentInfo::where('fundsource_id', $saa->id)
                ->with('proponent:id,proponent', 'main_pro:id,proponent')->get();

            $data = [];

            foreach($infos as $info){
                $sum = Utilization::where('proponentinfo_id', $info->id)->where('obligated', 1)
                    ->sum('utilize_amount');
                $data[] = [
                    'obligated' => $sum,
                    'info' => $info
                ];
            }
            $perPage = 10;
            $dataCollection = collect($data); 
            $all_data = new LengthAwarePaginator(
                $dataCollection->forPage(LengthAwarePaginator::resolveCurrentPage(), $perPage),
                $dataCollection->count(),
                $perPage,
                null,
                ['path' => LengthAwarePaginator::resolveCurrentPath()]
            );
            
            return view('fundsource_budget.funds_tracking',[
                'result' => $all_data,
                'facilities' => Facility::get()->select('id','name'),
                'saa' => $saa
            ]);
        }
    }

    public function costTracking($id){
        $saa = Fundsource::where('id', $id)
            ->with('a_cost')
            ->first();
        
        return view('fundsource_budget.cost_tracking',[
            'saa' => $saa
        ]);
        
    }

    public function confirmDV($route_no, Request $request){
        $dv = NewDV::where('route_no', $route_no)->first();

        if (empty($dv)) {
            $dv = Dv3::where('route_no', $route_no)->first();
        }

        if($dv){
            $util = Utilization::where('div_id', $dv->route_no)->where('status', 0)
                    ->with([
                        'infoData:id,facility_id',
                        'saaData:id,saa',
                        'proponentdata:id,proponent',
                        'facilitydata:id,name'
                    ]);

            if ($request->sort) {

               
                $util = $util->get(); 
                $direction = $request->input('direction', 'asc') == 'desc'; 
                $direct = $request->input('direction'); 
                if($request->input('sort') === 'proponent'){
                    $util = $util->sortBy(function ($utilization) {
                        return $utilization->proponentdata->proponent; 
                    }, SORT_REGULAR, $direction); 
                }else if($request->input('sort') === 'saa'){
                    $util = $util->sortBy(function ($utilization) {
                        return $utilization->saaData->saa; 
                    }, SORT_REGULAR, $direction); 
                }else if($request->input('sort') === 'payee'){
                    $facility_ids = Facility::orderBy('name', $direct)->pluck('id')->toArray(); 
                    $util = $util->sortBy(function ($item) use ($facility_ids) {
                        $ids = json_decode($item->infoData->facility_id, true); 
                        if (!is_array($ids)) {
                            $ids = [$ids];
                        }
                        $firstFacilityId = $ids[0] ?? null;
                        return array_search($firstFacilityId, $facility_ids) !== false
                            ? array_search($firstFacilityId, $facility_ids)
                            : PHP_INT_MAX;
                    });

                    $util = $util->values();
                }
                
            }else{
                $util = $util->get();
                $direct = 'sd';
            }

            // if ($request->ajax()) {
            //     // Return only the updated table rows for AJAX requests
            //     return response()->json([
            //         'html' => view('fundsource_budget.confirmation_table_rows', ['data' => $util])->render()
            //     ]);
            // }
    
            // return $util->groupBy('fundsource_id');
            return view('fundsource_budget.confirmation',[
                'facilities' =>Facility::get(),
                'data' => $util->groupBy('fundsource_id'),
                'dv' => $dv,
                'direction' => $direct
            ]);        
        }
    }

    public function confirm($id){
        if($id){
            $dv = NewDv::where('id', $id)->first();
            $dv->confirm = "yes";
            $dv->save();
            return $dv;

            Utilization::where('div_id', $dv->route_no)->where('status', 0)->update(['confirm' => 'yes']);
        }
    }

    public function saveCost(Request $request){
        $data = $request->data;
        if($data){
            $util = new AdminCostUtilization();
            $util->util_id = $data['l_id'];
            $util->fundsource_id = $data['saa_id'];
            $util->proponent = $data['pro'];
            $util->dv_no = $data['dv_no'];
            $util->payee = $data['payee'];
            $util->ors_no = $data['ors'];
            $util->recipient = $data['fc'];
            $util->admin_uacs = $data['uacs'];
            $util->admin_cost = (float) str_replace(',','',$data['cost']);
            $util->created_by = Auth::user()->userid;
            $util->save();
            return 'success';
        }
    }

    public function confirmBudget($id){
        $ids = array_map('intval', explode(',', $id));
        $util = Utilization::whereIn('id', $ids)
            ->with([
                'fundSourcedata:id,saa,remaining_balance,alocated_funds,admin_cost',
                'proponentdata:id,proponent',
                'infoData:id,facility_id',
                'facilitydata:id,name',
                'dv' => function($query){
                    $query->with('facility:id,name');
                },
                'dv3' => function($query){
                    $query->with('facility:id,name');
                },
                'newDv' => function($query){    
                    $query->with([
                        'preDv' => function($query){
                            $query->with('facility:id,name');
                        }
                    ]);
                }
            ])->where('status', 0)->paginate(10);

            // return $util;

        // $admin_c = AdminCostUtilization::with('fundSourcedata:id,saa')->get();
        // $combi = $util->merge($admin_c);
        // $combi = $combi->sortByDesc('updated_at'); // Change to 'updated_by' if needed
        // return $combi;
                // return $util;
        // $perPage = 10;
        // $data = new LengthAwarePaginator(
        //     $util->forPage(LengthAwarePaginator::resolveCurrentPage(), $perPage),
        //     $combi->count(),
        //     $perPage,
        //     null,
        //     ['path' => LengthAwarePaginator::resolveCurrentPath()]
        // );


        if(count($util) > 0){
            return view('fundsource_budget.budget_tracking',[
                'result' => $util,
                'facilities' => Facility::get(),
                'last' => $util->last(),
                'confirm' => 1
            ]);
        }else{
            return 'No data available!';
        }
       
    }

    public function saveDate($id, $date){
        Utilization::where('id', $id)->update(['obligated_on' => date('Y-m-d', strtotime($date))]);
        return 'success';
    }
}
