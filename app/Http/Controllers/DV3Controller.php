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
use App\Models\AddFacilityInfo;
use App\Models\Dv;
use App\Models\Dv2;
use App\Models\Group;
use App\Models\ProponentInfo;
use App\Models\Dv3;
use App\Models\Dv3Fundsource;
use App\Models\TrackingMaster;
use App\Models\TrackingDetails;

class Dv3Controller extends Controller
{
    public function __construct(){
       $this->middleware('auth');
    }

    public function dv3(Request $request) {
        $user = Auth::user();
        $dv3 = Dv3::              
                with([
                    'extension' => function ($query) {
                        $query->with([
                            'proponentInfo' => function ($query) {
                                $query->with('proponent', 'fundsource');
                            }
                        ]);
                    },
                    'facility' => function ($query) {
                        $query->select(
                            'id',
                            'name'
                        );
                    },
                    'user' => function ($query) {
                        $query->select(
                            'userid',
                            'fname',
                            'lname'
                        );
                    }
                ])->orderBy('created_at', 'desc');
        return view('dv3.dv3',[
            'dv3' => $dv3->paginate(50)
            ]);
    }
    function dv3Save(Request $request){

        $userid = Auth::user()->userid;
        $initial_route = date('Y-') . $userid . date('mdHis');

        $dv3 = new Dv3();
        $dv3->route_no = $initial_route;
        $dv3->date = $request->dv3_date;
        $dv3->facility_id = $request->dv3_facility;
        $dv3->total = (float)str_replace(',','',$request->total_amount);
        $dv3->created_by = Auth::user()->userid;
        $dv3->remarks = 0;
        $dv3->status = 0;
        $dv3->save();

        $desc = "Disbursement voucher for " . Facility::where('id', $request->dv3_facility)->value('name') . " amounting to Php " . number_format(str_replace(',', '', $request->total_amount), 2, '.', ',');
        
        $dts_user = DB::connection('dts')->select("SELECT id FROM users WHERE username = ? LIMIT 1",array($userid));
        $data = [$initial_route,"DV",$dv3->created_at,$dts_user[0]->id,0,  $desc, 0.00,"", "", "", "", "", "", "", "", "", "", "0000-00-00 00:00:00",
                    "", "", "", 0, "", "", "", "", "", "", ];
        DB::connection('dts')->insert(
            "INSERT INTO TRACKING_MASTER(route_no, doc_type, prepared_date, prepared_by, division_head, description, amount, pr_no, po_no, pr_date, purpose, po_date, 
                source_fund, requested_by, route_to, route_from, supplier, event_date, event_location, event_participant, cdo_applicant, cdo_day, event_daterange, 
                payee, item, dv_no, ors_no, fund_source_budget, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, now(), now())",$data);
        $tracking_master = TrackingMaster::where('route_no', $initial_route)->first();
        $updated_route = date('Y-').$tracking_master->id;
        $tracking_master->route_no = $updated_route;
        $tracking_master->save();  
        $dv3->route_no = $updated_route;
        $dv3->save();

        $data_details = [$updated_route, "", 0,$dv3->created_at, $dts_user[0]->id, $dts_user[0]->id,  $desc, 0];
        DB::connection('dts')->insert("INSERT INTO TRACKING_DETAILS(route_no, code, alert, date_in, received_by, delivered_by, action, status,created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, now(), now())",$data_details);
        
        $amount = $request->amount;
        $saa = $request->fundsource_id;
        $vat = $request->vat_amount;
        $ewt = $request->ewt_amount;

        foreach($request->info_id as $index => $id){
            
            $each = (float)str_replace(',','',$amount[$index]);
            $info = ProponentInfo::where('id', $id)->first();

            $util = new Utilization();
            $util->div_id = $dv3->route_no;
            $util->beginning_balance = $info->remaining_balance;
            $util->discount = (float)str_replace(',','',$vat[$index]) + (float)str_replace(',','',$ewt[$index]);
            $util->utilize_amount = (float)str_replace(',','',$amount[$index]);
            $util->facility_id = $request->dv3_facility;
            $util->proponent_id = $info->proponent_id;
            $util->facility_used = $info->facility_id;
            $util->fundsource_id = $info->fundsource_id;
            $util->proponentinfo_id = $info->id;
            $util->status = 0;
            $util->created_by = $userid;
            $util->save();

            $info->remaining_balance = (float)str_replace(',','',$info->remaining_balance) - $each;
            $info->save();

            $dv3_funds = new Dv3Fundsource();
            $dv3_funds->route_no = $dv3->route_no;
            $dv3_funds->fundsource_id = $saa[$index];
            $dv3_funds->info_id = $id;
            $dv3_funds->amount = (float)str_replace(',','',$amount[$index]);
            $dv3_funds->vat = (float)str_replace(',','',$vat[$index]);
            $dv3_funds->ewt = (float)str_replace(',','',$ewt[$index]);
            $dv3_funds->save();
            // $
        }
        return redirect()->back()->with('dv3', true);
    }

    public function dv3Update($route_no){
        if($route_no){
            $dv3 = Dv3::              
                with([
                    'extension' => function ($query) {
                        $query->with([
                            'proponentInfo' => function ($query) {
                                $query->with('proponent', 'fundsource');
                            }
                        ]);
                    },
                    'facility' => function ($query) {
                        $query->select(
                            'id',
                            'name',
                            'address'
                        );
                    }
                ])->where('route_no', $route_no)->first();
            
            $facility_id = (string) $dv3->facility_id;

            $info = ProponentInfo::with('facility', 'fundsource', 'proponent')
                ->where(function ($query) use ($facility_id) {
                    $query->whereJsonContains('proponent_info.facility_id', '702')
                        ->orWhereJsonContains('proponent_info.facility_id', [$facility_id]);
                })
                ->orWhereIn('proponent_info.facility_id', [$facility_id, '702'])
                ->get();
            $f_info = AddFacilityInfo::where('id', $facility_id)->select('vat', 'ewt')->first();

            return view('dv3.update_dv3', [
                'facilities' => Facility::get(),
                'info' => $info,
                'dv3' => $dv3,
                'f_info' => $f_info
            ]);
        }
    }

    public function saveUpdate($route_no, Request $request){
      
        $dv3 = Dv3::where('route_no',$route_no)->with('extension')->first();
        foreach($dv3->extension as $item){
            $info = ProponentInfo::where('id', $item->info_id)->first();
            $info->remaining_balance = str_replace(',','',$info->remaining_balance) + $item->amount;
            $info->save();
            Utilization::where('div_id', $route_no)->where('proponentinfo_id', $item->info_id)->update(['status' => 1]);
            $get = Utilization::where('div_id', $route_no)->where('proponentinfo_id', $item->info_id)->orderBy('id', 'desc')->first();
            $all = Utilization::where('proponentinfo_id', $item->info_id)->where('id', '>', $get->id)->orderBy('id', 'asc')->get();

            foreach($all as $row){
                return str_replace(',','',$row->beginning_balance) + $item->amount;
                $row->beginning_balance = str_replace(',','',$row->remaining_balance) + $item->amount;
                $row->save();
            }
        }

        $dv3->date = $request->dv3_date;
        $dv3->facility_id = $request->dv3_facility;
        $dv3->total = (float)str_replace(',','',$request->total_amount);
        $dv3->created_by = Auth::user()->userid;
        $dv3->save();        

        foreach($request->info_id as $index => $id){
            
            $each = (float)str_replace(',','',$amount[$index]);
            $info = ProponentInfo::where('id', $id)->first();

            $util = new Utilization();
            $util->div_id = $dv3->route_no;
            $util->beginning_balance = $info->remaining_balance;
            $util->discount = (float)str_replace(',','',$vat[$index]) + (float)str_replace(',','',$ewt[$index]);
            $util->utilize_amount = (float)str_replace(',','',$amount[$index]);
            $util->facility_id = $request->dv3_facility;
            $util->proponent_id = $info->proponent_id;
            $util->facility_used = $info->facility_id;
            $util->fundsource_id = $info->fundsource_id;
            $util->proponentinfo_id = $info->id;
            $util->status = 0;
            $util->created_by = $userid;
            $util->save();

            $info->remaining_balance = (float)str_replace(',','',$info->remaining_balance) - $each;
            $info->save();

            Dv3Fundsource::where('route_no', $route_no)->delete();

            $dv3_funds = new Dv3Fundsource();
            $dv3_funds->route_no = $dv3->route_no;
            $dv3_funds->fundsource_id = $saa[$index];
            $dv3_funds->info_id = $id;
            $dv3_funds->amount = (float)str_replace(',','',$amount[$index]);
            $dv3_funds->vat = (float)str_replace(',','',$vat[$index]);
            $dv3_funds->ewt = (float)str_replace(',','',$ewt[$index]);
            $dv3_funds->save();

        }

        $desc = "Disbursement voucher for " . Facility::where('id', $request->dv3_facility)->value('name') . " amounting to Php " . number_format(str_replace(',', '', $request->total_amount), 2, '.', ',');
        $tracking = TrackingMaster::where('route_no', $route_no)->first();
        $tracking->description = $desc;
        $tracking->save();
        $details = TrackingDetails::where('route_no', $route_no)->first();
        $details->action = $desc;
        $details->save();
    }

    public function createDv3(Request $request)
    {
        return view('dv3.create_dv3', [
            'facilities' => Facility::get()
        ]);
    }

    public function cloneSaa($id) {
        $randomBytes = random_bytes(16); 
        return view('dv3.saa_clone',[
            'uniqueCode' => bin2hex($randomBytes),
            'id' => $id
        ]);
    }

    public function pendingDv3(Request $request, $type){

        // if($type == 'pending'){
        //   $result = Dv3::whereNull('obligated')->with(['fundsource','facility', 'master'])->orderby('id', 'desc');
        //   // $result = Dv::whereNull('obligated')->whereNotNull('dv_no')->where('dv_no', '!=', '')->with(['fundsource','facility', 'master'])->orderby('id', 'desc');
        // }else if($type == 'obligated'){
        //   $result = Dv3::whereNotNull('obligated')->with(['fundsource','facility', 'master'])->orderby('id', 'desc');
        //   // $result = Dv::whereNotNull('obligated')->whereNotNull('dv_no')->with(['fundsource','facility', 'master'])->orderby('id', 'desc');
        // }

        // if($request->viewAll){
        //     $request->keyword = '';
        // }else if($request->keyword){
        //     $result->where('route_no', 'LIKE', "%$request->keyword%");
        // }
        // $id = $result->pluck('created_by')->unique();
        // $name = User::whereIn('userid', $id)->get()->keyBy('userid'); 
        // $results = $result->paginate(50);
        
        return view('fundsource_budget.dv3_list', [
        //   'disbursement' => $results,
        //   'name'=> $name,
        //   'type' => $type,
        //   'keyword' => $request->keyword,
        //   'proponents' => Proponent::get(),
        //   'proponentInfo' => ProponentInfo::get()
        ]);
    }
}
