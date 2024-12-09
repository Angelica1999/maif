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
      
        $section = DB::connection('dohdtr')
                        ->table('users')
                        ->leftJoin('dts.users', 'users.userid', '=', 'dts.users.username')
                        ->where('users.userid', '=', Auth::user()->userid)
                        ->value('users.section');
        $fundsources = Fundsource::orderByRaw("CASE WHEN saa LIKE 'conap%' THEN 0 ELSE 1 END, saa ASC")->paginate(15);
        if($request->viewAll) {
            $request->keyword = '';
        }
        else if($request->keyword) {
            $fundsources = Fundsource::where('saa', 'LIKE', "%$request->keyword%")->orderByRaw("CASE WHEN saa LIKE 'conap%' THEN 0 ELSE 1 END, saa ASC")->paginate(15);
        } 
        return view('fundsource_budget.fundsource2',[
            'fundsources' => $fundsources,
            'keyword' => $request->keyword,
            'section' => $section
        ]);
    }

    public function createfundSource2(Request $request){

        $funds = $request->input('allocated_funds');
        $saas = $request->input('saa');
        $cost_value = $request->input('admin_cost');
        foreach($funds as $index => $fund){
            $saa_ex = Fundsource::where('saa', $saas[$index])->first();
          // return $fund ; 
            if($saa_ex){
                session()->flash('saa_exist', true);
            }else{
                $fundsource = new Fundsource();
                $fundsource->saa = $saas[$index];
                $fundsource->alocated_funds = str_replace(',','',$fund);
                $fundsource->cost_value = $cost_value[$index];

                $admin_cost = (double) str_replace(',','',$fund) * ($cost_value[$index]/100);
                $fundsource->admin_cost = $admin_cost;
                $fundsource->remaining_balance = (double)str_replace(',','',$fund) - $admin_cost ;
                
                $fundsource->created_by = Auth::user()->userid;
                $fundsource->save();
                session()->flash('fundsource_save', true);
            }
        }
        return redirect()->back();
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

            $admin_c = AdminCostUtilization::with('fundSourcedata:id,saa')->get();
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


            if(count($combi) > 0){
                return view('fundsource_budget.budget_tracking',[
                    'result' => $data,
                    'facilities' => Facility::get(),
                    'last' => $combi->last(),
                    'confirm' => 0
                ]);
            }else{
                return 'No data available!';
            }
          
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

    public function uacs(Request $request){
        if($request->id){
            Utilization::where('id', $request->id)->update(['uacs' => $request->uacs]);
        }
        return true;
    }

    public function fundsTracking($id){
        $saa = Fundsource::where('id', $id)->first();
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

    public function confirmDV($id){
        $dv = NewDV::where('id', $id)->first();

        if (empty($dv)) {
            $dv = Dv3::where('route_no', $id)->first();
        }

        if($dv){
            $util = Utilization::where('div_id', $dv->route_no)->where('status', 0)
                    ->with([
                        'infoData:id,facility_id',
                        'saaData:id,saa',
                        'proponentdata:id,proponent',
                        'facilitydata:id,name'
                    ])->get();

            return view('fundsource_budget.confirmation',[
                'facilities' =>Facility::get(),
                'data' => $util,
                'dv' => $dv
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
       
        $util = Utilization::where('id', $id)
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
}
