<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PreDV;
use App\Models\PreDVControl;
use App\Models\PreDVSAA;
use App\Models\PreDVExtension;
use App\Models\NewDV;
use App\Models\Proponent;
use App\Models\Fundsource;
use App\Models\Facility;
use App\Models\AddFacilityInfo;
use App\Models\TrackingMaster;
use App\Models\TrackingDetails;
use PDF;
class PreDvController extends Controller
{
    //

    public function pre_dv(){
        $pre_dv = PreDV::with('user','facility')->orderBy('id', 'desc')->paginate(50);
        $saas = Fundsource::get();
        $proponents = Proponent::select('proponent')->groupBy('proponent')->get();
        $facilities = Facility::get();
        return view('pre_dv.pre_dv', [
            'results' => $pre_dv,
            'proponents' => $proponents,
            'saas' => $saas,
            'facilities' => $facilities
        ]);
    }

    public function pre_dv1(){
        $pre_dv = PreDV::with(
            [
                'user:userid,fname,lname,mname',
                'facility:id,name', 
                'extension' => function ($query){
                    $query->with(
                        [
                            'proponent:id,proponent',
                            'controls',
                            'saas' => function($query){
                                $query->with([
                                    'saa:id,saa'
                                ]);
                            }
                        ]
                    );
                }
            ])
            ->orderBy('id', 'desc')->paginate(50);
        $saas = Fundsource::get();
        $proponents = Proponent::select('proponent')->groupBy('proponent')->get();
        $facilities = Facility::get();
        return view('pre_dv.pre_dv1', [
            'results' => $pre_dv,
            'proponents' => $proponents,
            'saas' => $saas,
            'facilities' => $facilities
        ]);
    }

    public function pre_dv2(){
        $pre_dv = PreDV::with(
            [
                'user:userid,fname,lname,mname',
                'facility:id,name', 
                'extension' => function ($query){
                    $query->with(
                        [
                            'proponent:id,proponent',
                            'controls',
                            'saas' => function($query){
                                $query->with([
                                    'saa:id,saa'
                                ]);
                            }
                        ]
                    );
                }
            ])
            ->orderBy('id', 'desc')->paginate(50);
        $saas = Fundsource::get();
        $proponents = Proponent::select('proponent')->groupBy('proponent')->get();
        $facilities = Facility::get();
        return view('pre_dv.pre_dv2', [
            'results' => $pre_dv,
            'proponents' => $proponents,
            'saas' => $saas,
            'facilities' => $facilities
        ]);
    }

    public function v1View($id){
        $pre_dv = PreDV::where('id', $id)->with(
            [
                'user:userid,fname,lname,mname',
                'facility:id,name', 
                'extension' => function ($query){
                    $query->with(
                        [
                            'proponent:id,proponent',
                            'controls',
                            'saas' => function($query){
                                $query->with([
                                    'saa:id,saa'
                                ]);
                            }
                        ]
                    );
                }
            ])->first();
        // return $pre_dv;
        if($pre_dv){
            return view('pre_dv.v1_view', [
                'result' => $pre_dv
            ]);
        }
    }

    public function v2View($id){
        $pre_dv = PreDV::where('id', $id)->with('facility')->first();
        $new_dv = NewDV::where('predv_id', $id)->first();
        $extension = PreDVExtension::where('pre_dv_id', $pre_dv->id)->pluck('id');
        $saas = PreDVSAA::whereIn('predv_extension_id', $extension)->with('saa:id,saa')->get();
        $info = AddFacilityInfo::where('facility_id', $pre_dv->facility_id)->first();
        $controls = PreDVControl::whereIn('predv_extension_id', $extension)->get();  
        $i = 0;   
        $control = '';   
        foreach ($controls as $index => $c) {
            if ($i <= 3) {
                $control = ($control != '')? $control .', '.$c->control_no : $control .' '. $c->control_no ;
            }
            $i++;
        }
        $grouped = $saas->groupBy('fundsource_id')->map(function ($group) use ($info){
            return [ 
                'amount' => $group->sum('amount'),
                'saa' => $group->first()->saa->saa, 
                'fundsource_id' => $group->first()->saa->id,
                'vat' => ($info && $info->vat != null)? (float) $info->vat *  $group->sum('amount') / 100: 0,
                'ewt' => ($info && $info->Ewt != null)? (float) $info->Ewt *  $group->sum('amount') / 100: 0
            ];
        });
        if($pre_dv){
            return view('pre_dv.v2_view', [
                'result' => $pre_dv,
                'fundsources' => $grouped,
                'info' => $info,
                'control' => $control,
                'new_dv' => $new_dv
            ]);
        }
    }

    public function cloneProponent(){
        $proponents = Proponent::select('proponent')->groupBy('proponent')->get();
        $saas = Fundsource::get();

        return view('pre_dv.proponent_clone',[
            'proponents' => $proponents,
            'saas' => $saas
        ]);
    }

    public function cloneSAA(){
        $saas = Fundsource::get();
        return view('pre_dv.saa_clone',[
            'saas' => $saas
        ]);
    }

    public function cloneControl(){
        return view('pre_dv.control_clone');
    }

    public function savePreDV($data,Request $request){
        // return $data;
        $decodedData = urldecode($data);
        $all_data = json_decode($decodedData, true);
        $grand_total = $request->grand_total;
        $facility_id = $request->facility_id;

        $pre_dv = new PreDV();
        $pre_dv->facility_id = $facility_id;
        $pre_dv->grand_total = (float)str_replace(',','',$grand_total);
        $pre_dv->created_by = Auth::user()->userid;
        $pre_dv->save();

        foreach($all_data as $value){
            $proponent_id = $value['proponent'];
            $control_nos = $value['pro_clone'];
            $fundsources = $value['fundsource_clone'];
            $proponent = Proponent::where('proponent', $proponent_id)->value('id');

            $pre_extension = new PreDVExtension();
            $pre_extension->pre_dv_id = $pre_dv->id;
            $pre_extension->proponent_id = $proponent;
            $pre_extension->total_amount = (float)str_replace(',','',$value['total_amount']);
            $pre_extension->save();

            foreach($control_nos as $row){
                $controls = new PreDVControl();
                $controls->predv_extension_id = $pre_extension->id;
                $controls->control_no = $row['control_no'];
                $controls->patient_1 = $row['patient_1'];
                $controls->patient_2 = $row['patient_2'];
                $controls->amount = (float)str_replace(',','',$row['amount']);
                $controls->save();
            }

            foreach($fundsources as $saa){
                $pre_saa = new PreDVSAA();
                $pre_saa->predv_extension_id = $pre_extension->id;
                $pre_saa->fundsource_id = $saa['saa_id'];
                $pre_saa->amount = (float)str_replace(',','',$saa['saa_amount']);
                $pre_saa->save();
            }
        }
        return redirect()->back()->with('pre_dv', true);
    }

    public function displayPreDV($id){
        $pre_dv = PreDV::where('id', $id)->with(
                [
                    'user:userid,fname,lname,mname',
                    'facility:id,name', 
                    'extension' => function ($query){
                        $query->with(
                            [
                                'proponent:id,proponent',
                                'controls',
                                'saas' => function($query){
                                    $query->with([
                                        'saa:id,saa'
                                    ]);
                                }
                            ]
                        );
                    }
                ])->first();
        $saas = Fundsource::get();
        $proponents = Proponent::select('proponent')->groupBy('proponent')->get();
        $facilities = Facility::get();
        return view('pre_dv.update_predv', [
            'result' => $pre_dv,
            'proponents' => $proponents,
            'saas' => $saas,
            'facilities' => $facilities
        ]);
    }

    public function updatePreDV($data, Request $request){

        $decodedData = urldecode($data);
        $all_data = json_decode($decodedData, true);
        $id = $request->pre_id;
        $grand_total = $request->grand_total;
        $facility_id = $request->facility_id;

        $pre_dv = PreDV::where('id', $id)->first();

        if($pre_dv){
            $pre_dv->facility_id = $request->facility_id;
            $pre_dv->grand_total = str_replace(',','',$request->grand_total);
            $pre_dv->save();
            foreach($all_data as $value){
                $extension = PreDVExtension::where('pre_dv_id', $id)->get();
                $ex_control = PreDVControl::where('predv_extension_id', $extension[0]->id)->delete();
                $ex_saa = PreDVSAA::where('predv_extension_id', $extension[0]->id)->delete();

                PreDVExtension::where('pre_dv_id', $id)->delete();

                $proponent_id = $value['proponent'];
                $control_nos = $value['pro_clone'];
                $fundsources = $value['fundsource_clone'];
                $proponent = Proponent::where('proponent', $proponent_id)->value('id');

                $pre_extension = new PreDVExtension();
                $pre_extension->pre_dv_id = $pre_dv->id;
                $pre_extension->proponent_id = $proponent;
                $pre_extension->total_amount = (float)str_replace(',','',$value['total_amount']);
                $pre_extension->save();
            }

            foreach($control_nos as $row){
                $controls = new PreDVControl();
                $controls->predv_extension_id = $pre_extension->id;
                $controls->control_no = $row['control_no'];
                $controls->patient_1 = $row['patient_1'];
                $controls->patient_2 = $row['patient_2'];
                $controls->amount = (float)str_replace(',','',$row['amount']);
                $controls->save();
            }

            foreach($fundsources as $saa){
                $pre_saa = new PreDVSAA();
                $pre_saa->predv_extension_id = $pre_extension->id;
                $pre_saa->fundsource_id = $saa['saa_id'];
                $pre_saa->amount = (float)str_replace(',','',$saa['saa_amount']);
                $pre_saa->save();
            }
            return redirect()->back()->with('pre_dv', true);
        }else{
            return redirect()->back()->with('pre_dv_error', true);
        }
    }

    public function deletePreDV($id){
        $pre_dv = PreDV::where('id', $id)->first();
        if($pre_dv){
            $extension = PreDVExtension::where('pre_dv_id', $id)->get();
            PreDVSAA::where('predv_extension_id', $extension[0]->id)->delete();
            PreDVControl::where('predv_extension_id', $extension[0]->id)->delete();
            PreDVExtension::where('pre_dv_id', $id)->delete();

            return redirect()->back()->with('remove_pre_dv', true);
        }else{
            return redirect()->back()->with('pre_dv_error', true);
        }
    }

    public function newDV(Request $request){
        $id = $request->id;
        $pre = PreDV::where('id', $id)->with('facility:id,name')->first();
        $existing = NewDV::where('predv_id', $id)->first();
        // return $request->total_amount;
        if($existing){

            $existing->date = $request->date;
            $existing->date_from = $request->date_from.'-1';
            if($request->date_to){
                $existing->date_to = $request->date_to.'-1';
            }
            $existing->total = $request->total_amount;
            $existing->accumulated = $request->accumulated;
            $existing->created_by = Auth::user()->userid;
            $existing->save();
            return redirect()->back()->with('pre_dv_update', true);

        }else{
            
            $new_dv = new NewDV();
            $new_dv->route_no = date('Y-') . Auth::user()->userid . date('mdHis');
            $new_dv->predv_id = $pre->id;
            $new_dv->date = $request->date;
            $new_dv->date_from = $request->date_from.'-1';
            if($request->date_to){
                $new_dv->date_to = $request->date_to.'-1';
            }
            $new_dv->total = $request->total_amount;
            $new_dv->accumulated = $request->accumulated;
            $new_dv->created_by = Auth::user()->userid;
            $new_dv->save();

            $desc = "Disbursement voucher for " . $pre->facility->name. " amounting to Php " . number_format(str_replace(',', '', $request->total_amount), 2, '.', ',');
            $dts_user = DB::connection('dts')->select("SELECT id FROM users WHERE username = ? LIMIT 1",array(Auth::user()->userid));
            $data = [$new_dv->route_no,"DV",$new_dv->created_at,$dts_user[0]->id,0,  $desc, 0.00,"", "", "", "", "", "", "", "", "", "", "0000-00-00 00:00:00",
                        "", "", "", 0, "", !Empty($dv->dv_no)? $dv->dv_no:"", "", "", "", "", ];
            DB::connection('dts')->insert(
                "INSERT INTO TRACKING_MASTER(route_no, doc_type, prepared_date, prepared_by, division_head, description, amount, pr_no, po_no, pr_date, purpose, po_date, 
                    source_fund, requested_by, route_to, route_from, supplier, event_date, event_location, event_participant, cdo_applicant, cdo_day, event_daterange, 
                    payee, item, dv_no, ors_no, fund_source_budget, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, now(), now())",$data);
            
            $tracking_master = TrackingMaster::where('route_no', $new_dv->route_no)->first();
            $updated_route = date('Y-').$tracking_master->id;
            $tracking_master->route_no = $updated_route;
            $tracking_master->save();  
            $new_dv->route_no = $updated_route;
            $new_dv->save();
            //creating tracking_details
            $data_details = [$updated_route, "", 0,$new_dv->created_at, $dts_user[0]->id, $dts_user[0]->id,  $desc, 0];
            DB::connection('dts')->insert("INSERT INTO TRACKING_DETAILS(route_no, code, alert, date_in, received_by, delivered_by, action, status,created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, now(), now())",$data_details);

            return redirect()->back()->with('pre_dv', true);
        }       
    }
}
