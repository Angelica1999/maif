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

    public function fundsource(Request $req){

        $keyword = $req->keyword;

        if($req->viewAll){
            $keyword = '';
        }

        if($keyword){
            $proponents = Proponent::where('proponent', 'LIKE', "%$keyword%")->get();
        }else{
            $proponents = Proponent::select(
                DB::raw('MAX(id) as id'), 
                DB::raw('MAX(proponent) as proponent'), 
                DB::raw('MAX(proponent_code) as proponent_code')
            )
            ->groupBy('proponent_code')
            ->get();
        }
        $all_data = [];
        foreach($proponents as $row){
            $ids = Proponent::where('proponent_code', $row->proponent_code)->pluck('id')->toArray();
            $sum = ProponentInfo::whereIn('proponent_id', $ids) ->sum('in_balance');
            $util_sum = ProponentUtilizationV1::where('proponent_code', $row->proponent_code)->sum('amount');
            $all_data[] =[
                'proponent' => $row,
                'sum' => $sum,
                'rem' => $sum - $util_sum
            ];
        }

        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 51; 
        $currentPageData = array_slice($all_data, ($page - 1) * $perPage, $perPage); 
        $all_data_paginated = new LengthAwarePaginator(
            $currentPageData, 
            count($all_data), 
            $perPage, 
            $page, 
            ['path' => LengthAwarePaginator::resolveCurrentPath()] 
        );

        return view('proponents.fundsource', [
            'data' => $all_data_paginated,
            'keyword' => $keyword
        ]);
    }

    public function tracking($code){
        $tracking = ProponentUtilizationV1::where('proponent_code', $code)
            ->with([
                'proponent:id,proponent',
                'patient' => function($query){
                    $query->with([
                        'facility:id,name',
                        'encoded_by:userid,fname,lname'
                    ]);
                }
            ])->get();

        if(count($tracking) > 0){
            return view('proponents.proponent_util',[
                'data' => $tracking
            ]);
        }else{
            return 0;
        }
      
    }
}
