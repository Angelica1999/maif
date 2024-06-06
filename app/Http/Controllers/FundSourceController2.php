<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utilization;
use App\Models\Proponent;
use App\Models\ProponentInfo;
use App\Models\Fundsource;
use App\Models\User;
use App\Models\Dv;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FundSourceController2 extends Controller{

    public function __construct(){
       $this->middleware('auth');
    }

    public function sample(Request $request) {
      return view('sample');
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

                if((double)str_replace(',','',$fund) >= 1000000){
                    $admin_cost = (double) str_replace(',','',$fund) * ($cost_value[$index]/100);
                    $fundsource->admin_cost = $admin_cost;
                    $fundsource->remaining_balance = (double)str_replace(',','',$fund) - $admin_cost ;
                }else{
                    $fundsource->admin_cost = 0;
                    $fundsource->remaining_balance = str_replace(',','',$fund);
                }
                
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
        if($type == 'obligate'){
          // $dv->dv_no = $dv_no;
          // $dv->save();
          // Utilization::where('div_id', $route_no)->update(['dv_no' => $dv_no]);
        }
        
        $all= array_map('intval', json_decode($dv->fundsource_id));
        $fund_source = [];
        foreach($all as $id){
            $fund_source []= Fundsource::where('id', $id)->first();
        }
        // $fund_source = Fundsource::whereIn('id', $all)->get();
      }
      return view('fundsource_budget.obligate_dv', [ 
        'dv' =>$dv, 
        'section' => $section,
        'fund_source' => $fund_source,
        'type' => $type]);
    }

}
