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


class Dv2Controller extends Controller
{
    public function __construct(){
       $this->middleware('auth');
    }

    public function dv2(Request $request) {
        $dv2_list = Dv2::whereIn('id', function ($query) {
            $query->select(\DB::raw('MAX(id)'))
                ->from('dv2')
                ->groupBy('route_no');
        })
        ->with('user')
        ->orderBy('id', 'desc');
        
        if ($request->viewAll) {
            $request->keyword = "";
        } elseif ($request->keyword) {
            $dv2_list = $dv2_list->where('route_no','LIKE', "%$request->keyword%")
                ->orWhere('ref_no', 'LIKE', "%$request->keyword%")
                ->orWhere('amount', 'LIKE', "%$request->keyword%");                       
        }
        
        $dv2_list = $dv2_list->paginate(50);
        $section = DB::connection('dohdtr')
            ->table('users')
            ->leftJoin('dts.users', 'users.userid', '=', 'dts.users.username')
            ->where('users.userid', '=', Auth::user()->userid)
            ->value('users.section');
        return view('dv2.dv2',[
            'dv2_list' => $dv2_list,
            'keyword' => $request->keyword,
            'section' => $section
        ]);
    }

    public function getGroup($facility_id, $proponentId){
        $pro_g = Proponent::where('id', $proponentId)->value('pro_group');
        return Group::where('facility_id', $facility_id)->where('proponent_id', $pro_g)->where('status', 1)->get();
    }
    
    public function getProponentInfo($facility_id, $pro_group){
        // return $pro_group;
        $ids = Proponent::where('pro_group', $pro_group)->pluck('id')->toArray();
        return ProponentInfo::where(function ($query) use ($facility_id, $ids) {
                    $query->where('facility_id', $facility_id)
                        ->whereIn('proponent_id', $ids);
                    })
                    ->orWhere(function ($query) use ($ids) {
                        $query->where('facility_id', 702)
                            ->whereIn('proponent_id', $ids);
                    })
                ->with(['proponent', 'fundsource', 'facility'])
                ->get();
    }

    public function createDv2($route_no){
        $dv = Dv::where('route_no', $route_no)->first();
        $searchValue = Facility::where('id', $dv->facility_id)->value('name');

        $dv2_exist = Dv2::where('route_no', $route_no)->first();
        
        if($dv2_exist){
            
            $dv_amount = Dv::where('route_no', $route_no)->value('total_amount');
            $dv2 = Dv2::where('route_no', $route_no)
                        ->leftJoin('patients as p1', 'dv2.lname', '=', 'p1.id')
                        ->leftJoin('patients as p2', 'dv2.lname2', '=', 'p2.id')
                        ->select('dv2.*', 'p1.lname as lname1', 'p2.lname as lname_2')
                        ->get();
            $total = Dv2::where('route_no', $route_no)
                        ->select(DB::raw('SUM(REPLACE(amount, ",", "")) as totalAmount'))
                        ->first()->totalAmount;   
            return view('dv2.update_dv2', [
                'dv2'=> $dv2,
                'dv_amount' => $dv_amount,
                'total' => $total,
                'control_nos' => Dv2::whereRaw("facility REGEXP ?", ["^" . preg_quote($searchValue, '/')])->pluck('ref_no')
            ]);

        }else{
            
            $dv_1 = Dv::where('route_no', $route_no)->first();
            $groupIdArray = explode(',', $dv_1->group_id);
            $proponentArray = json_decode($dv_1->proponent_id, true);
            if($proponentArray){
                $dv = Dv::where('route_no', $route_no)
                    ->leftJoin('proponent', function ($join) use ($proponentArray) {
                        $join->on('proponent.id', '=', \DB::raw($proponentArray[0]));
                    })
                    ->with('facility')->first();
                $group = Group::whereIn('id', $groupIdArray)->with('patient')->get();
                $total = Group::whereIn('id', $groupIdArray)
                            ->select(DB::raw('SUM(REPLACE(amount, ",", "")) as totalAmount'))
                            ->first()->totalAmount;
                return view('dv.create_dv2', [
                    'dv'=> $dv,
                    'group'=>$group, 
                    'total'=>$total,
                    'control_nos' => Dv2::whereRaw("facility REGEXP ?", ["^" . preg_quote($searchValue, '/')])->pluck('ref_no')
                ]);
            }else{
                return "No proponent being recorded, please contact system administrator!";
            }
            
        }
    }

    public function updateDv2(Request $request){
        $route_no = $request->input('route_no');
        $amount = $request->input('amount');
        $control_no = $request->input('ref_no');
        $lname = $request->input('g_lname1');
        $lname2 = $request->input('g_lname2');
        $facility = $request->input('facility');

        Dv2::where('route_no', $route_no)->delete();

        foreach($control_no as $index => $ref){
            if($lname[$index] !== null){
                $dv2 = new Dv2();
                $dv2->route_no = $request->input('route_no');
                $dv2->ref_no = $ref;
                $dv2->lname = $lname[$index];
                if($lname2[$index] !== null || $lname2[$index]){
                    $dv2->lname2 = $lname2[$index];
                }else{
                    $dv2->lname2 = 0;
                }
                $dv2->amount = $amount[$index];
                $dv2->facility = $facility;
                $dv2->created_by = Auth::user()->userid;
                $dv2->save();
            }
           
        }
        return redirect()->route('dv2')->with('update_dv2', true);

    }

    public function saveDv2(Request $request){
        // return  $request->input('g_lname1');
               
        $facility = $request->input('facility');
        $lname = $request->input('g_lname1');
        // $lname = array_filter($lname, function($value) {
        //     return $value !== null;
        // });
        
        // return $lname;
        $lname2 = $request->input('g_lname2');
        $amount = $request->input('amount');
        $control_no = $request->input('ref_no');
        // return $lname ;
        foreach($control_no as $index => $ref){
            if($lname[$index] !== null){
                $dv2 = new Dv2();
                $dv2->route_no = $request->input('route_no');
                $dv2->ref_no = $ref;
                $dv2->lname = $lname[$index];
                if($lname2[$index] !== 0 || $lname2[$index]){
                    $dv2->lname2 = $lname2[$index];
                }
                $dv2->amount = $amount[$index];
                $dv2->facility = $facility;
                $dv2->created_by = Auth::user()->userid;
                $dv2->save();
            }
           
        }
        return redirect()->route('dv2')->with('create_dv2', true);
    }

    public function removeDv2($route_no){
        Dv2::where('route_no', $route_no)->delete();
        return redirect()->route('dv2')->with('dv2_remove', true);

    }
}
