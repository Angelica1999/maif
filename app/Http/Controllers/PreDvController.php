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
use App\Models\ProponentInfo;
use App\Models\Utilization;
use App\Models\Dv2;
use PDF;

class PreDvController extends Controller
{
    //

    public function pre_dv(Request $request)
    {

        NewDV::whereNull('dv_no')
            ->orWhere('dv_no', '')
            ->with('dts')
            ->get()
            ->each(function ($dv) {
                if ($dv->dts && $dv->dts->dv_no) {
                    $dv->dv_no = $dv->dts->dv_no;
                    $dv->save();
                }
            });

            $pre_dv = PreDV::with(
                [
                    'user:userid,fname,lname,mname',
                    'facility:id,name',
                    'new_dv',
                    'extension' => function ($query) {
                        $query->with(
                            [
                                'proponent:id,proponent',
                                'controls',
                                'saas' => function ($query) {
                                    $query->with([
                                        'saa:id,saa'
                                    ]);
                                }
                            ]
                        );
                    }
                ]
            );
        $saas = Fundsource::get();
        $proponents = Proponent::select('proponent')->groupBy('proponent')->get();
        $facilities = Facility::with('addFacilityInfo')->get();

        if($request->viewAll){
            $request->keyword = '';
        }else if($request->keyword){
            $keyword = $request->keyword;
            $pre_dv->WhereHas('facility', function ($query) use ($keyword) {
                $query->where('name', 'LIKE', '%' . $keyword . '%');
            })->orWhereHas('extension.controls', function ($query) use ($keyword) {
                $query->where('control_no', 'LIKE', '%' . $keyword . '%');
            })->orWhereHas('new_dv', function ($query) use ($keyword) {
                $query->where('route_no', 'LIKE', '%' . $keyword . '%');
            });
        }
        $pre_dv = $pre_dv->orderBy('id', 'desc')->paginate(50);
        
        return view('pre_dv.pre_dv', [
            'results' => $pre_dv,
            'proponents' => $proponents,
            'saas' => $saas,
            'facilities' => $facilities,
            'keyword' => $request->keyword
        ]);
    }

    public function pre_dv1(Request $request)
    {
        $pre_dv = PreDV::with(
            [
                'user:userid,fname,lname,mname',
                'facility:id,name',
                'extension' => function ($query) {
                    $query->with(
                        [
                            'proponent:id,proponent',
                            'controls',
                            'saas' => function ($query) {
                                $query->with([
                                    'saa:id,saa'
                                ]);
                            }
                        ]
                    );
                }
            ]
        );
        $saas = Fundsource::get();
        $proponents = Proponent::select('proponent')->groupBy('proponent')->get();
        $facilities = Facility::get();

        if($request->viewAll){
            $request->keyword = '';
        }else if($request->keyword){
            $keyword = $request->keyword;
            $pre_dv->WhereHas('facility', function ($query) use ($keyword) {
                $query->where('name', 'LIKE', '%' . $keyword . '%');
            });
        }

        $pre_dv = $pre_dv->orderBy('id', 'desc')->paginate(50);

        return view('pre_dv.pre_dv1', [
            'results' => $pre_dv,
            'proponents' => $proponents,
            'saas' => $saas,
            'facilities' => $facilities,
            'keyword' => $request->keyword
        ]);
    }

    public function pre_dv2(Request $request)
    {
        $pre_dv = PreDV::with(
            [
                'user:userid,fname,lname,mname',
                'facility:id,name',
                'new_dv' => function ($query) {
                    $query->with(
                        [
                            'details'
                        ]
                    );
                },
                'extension' => function ($query) {
                    $query->with(
                        [
                            'proponent:id,proponent',
                            'controls',
                            'saas' => function ($query) {
                                $query->with([
                                    'saa:id,saa'
                                ]);
                            }
                        ]
                    );
                }
            ]
        );
        // return $pre_dv->get();
        $saas = Fundsource::get();
        $proponents = Proponent::select('proponent')->groupBy('proponent')->get();
        $facilities = Facility::get();

        if($request->viewAll){
            $request->keyword = '';
        }else if($request->keyword){
            $keyword = $request->keyword;
            $pre_dv->whereHas('new_dv', function ($query) use ($keyword) {
                $query->where('route_no', 'LIKE', '%' . $keyword . '%');
            })->orWhereHas('facility', function ($query) use ($keyword) {
                $query->where('name', 'LIKE', '%' . $keyword . '%');
            })->orWhereHas('extension.controls', function ($query) use ($keyword) {
                $query->where('control_no', 'LIKE', '%' . $keyword . '%');
            });
        
        }
        $pre_dv = $pre_dv->orderBy('id', 'desc')->paginate(50);

        return view('pre_dv.pre_dv2', [
            'results' => $pre_dv,
            'proponents' => $proponents,
            'saas' => $saas,
            'facilities' => $facilities,
            'keyword' => $request->keyword
        ]);
    }

    public function pre_dvBudget(Request $request, $type)
    {
        $pre_dv = PreDV::with(
            [
                'user:userid,fname,lname,mname',
                'facility:id,name',
                'new_dv',
                'extension' => function ($query) {
                    $query->with(
                        [
                            'proponent:id,proponent',
                            'controls',
                            'saas' => function ($query) {
                                $query->with([
                                    'saa:id,saa'
                                ]);
                            }
                        ]
                    );
                }
            ]
        );
        // return $pre_dv;
        $saas = Fundsource::get();
        $proponents = Proponent::select('proponent')->groupBy('proponent')->get();
        $facilities = Facility::get();

        if($request->viewAll){
            $request->keyword = '';
        }else if($request->keyword){
            $keyword = $request->keyword;
            $pre_dv->whereHas('new_dv', function ($query) use ($keyword) {
                $query->where('route_no', 'LIKE', '%' . $keyword . '%');
            })->orWhereHas('facility', function ($query) use ($keyword) {
                $query->where('name', 'LIKE', '%' . $keyword . '%');
            });
        }

        if($type == 'pending_new'){
            $pre_dv->whereHas('new_dv', function ($query) {
                $query->whereNull('ors_no'); 
            });  
        }else if($type== 'processed_new'){
            $pre_dv->whereHas('new_dv', function ($query) {
                $query->whereNotNull('ors_no'); 
            });  
        }else if($type== 'cashier_pending'){
            $pre_dv->whereHas('new_dv', function ($query) {
                $query->whereNull('paid_by')->whereNotNull('ors_no');
            });  
        }else if($type== 'cashier_paid'){
            $pre_dv->whereHas('new_dv', function ($query) {
                $query->whereNotNull('paid_by')->whereNotNull('ors_no'); 
            });  
        }

        $pre_dv = $pre_dv->orderBy('id', 'desc')->paginate(50);

        return view('fundsource_budget.new_dv_list', [
            'results' => $pre_dv,
            'proponents' => $proponents,
            'saas' => $saas,
            'facilities' => $facilities,
            'keyword' => $request->keyword,
            'type' => $type
        ]);
    }

    public function budgetV2(Request $request, $type, $id){
        $pre_dv = PreDV::where('id', $id)->with('facility')->first();
        $new_dv = NewDV::where('predv_id', $id)->with('dts')->first();
        $extension = PreDVExtension::where('pre_dv_id', $pre_dv->id)->pluck('id');
        $saas = PreDVSAA::whereIn('predv_extension_id', $extension)->with('saa:id,saa')->get();
        $info = AddFacilityInfo::where('facility_id', $pre_dv->facility_id)->first();
        $controls = PreDVControl::whereIn('predv_extension_id', $extension)->get();
        $i = 0;
        $control = '';
        foreach ($controls as $index => $c) {
            if ($i <= 3) {
                $control = ($control != '') ? $control . ', ' . $c->control_no : $control . ' ' . $c->control_no;
            }
            $i++;
        }
        $grouped = $saas->groupBy('fundsource_id')->map(function ($group) use ($info) {
            return [
                'amount' => $group->sum('amount'),
                'saa' => $group->first()->saa->saa,
                'fundsource_id' => $group->first()->saa->id,
                'vat' => ($info && $info->vat != null) ? (float) $info->vat *  $group->sum('amount') / 100 : 0,
                'ewt' => ($info && $info->Ewt != null) ? (float) $info->Ewt *  $group->sum('amount') / 100 : 0
            ];
        });
        if ($pre_dv) {
            return view('fundsource_budget.v2_view', [
                'result' => $pre_dv,
                'fundsources' => $grouped,
                'info' => $info,
                'control' => $control,
                'new_dv' => $new_dv,
                'type' => $type
            ]);
        }
    }

    public function v1View($id)
    {
        $pre_dv = PreDV::where('id', $id)->with(
            [
                'user:userid,fname,lname,mname',
                'facility:id,name',
                'extension' => function ($query) {
                    $query->with(
                        [
                            'proponent:id,proponent',
                            'controls',
                            'saas' => function ($query) {
                                $query->with([
                                    'saa:id,saa'
                                ]);
                            }
                        ]
                    );
                }
            ]
        )->first();
        // return $pre_dv;
        if ($pre_dv) {
            return view('pre_dv.v1_view', [
                'result' => $pre_dv
            ]);
        }
    }

    public function v2View($id)
    {
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
                $control = ($control != '') ? $control . ', ' . $c->control_no : $control . ' ' . $c->control_no;
            }
            $i++;
        }
        $grouped = $saas->groupBy('fundsource_id')->map(function ($group) use ($info) {
            return [
                'amount' => $group->sum('amount'),
                'saa' => $group->first()->saa->saa,
                'fundsource_id' => $group->first()->saa->id,
                'vat' => ($info && $info->vat != null) ? (float) $info->vat *  $group->sum('amount') / 100 : 0,
                'ewt' => ($info && $info->Ewt != null) ? (float) $info->Ewt *  $group->sum('amount') / 100 : 0
            ];
        });
        if ($pre_dv) {
            return view('pre_dv.v2_view', [
                'result' => $pre_dv,
                'fundsources' => $grouped,
                'info' => $info,
                'control' => $control,
                'new_dv' => $new_dv
            ]);
        }
    }

    public function cloneProponent($facility_id)
    {
        $proponents = Proponent::select('proponent')->groupBy('proponent')->get();
        $saas = Fundsource::get();

        $info = ProponentInfo::with('facility', 'fundsource', 'proponent')
                ->where(function ($query) use ($facility_id) {
                    $query->whereJsonContains('proponent_info.facility_id', '702')
                        ->orWhereJsonContains('proponent_info.facility_id', [$facility_id]);
                })
                ->orWhereIn('proponent_info.facility_id', [$facility_id, '702'])
                ->get()
                ->sortBy(function ($item) {
                    $facility_id = $item->facility_id;
                    $contains702 = is_string($facility_id) && strpos($facility_id, '702') !== false;
                    $is702 = $facility_id == 702;
                    $containsCONAP = isset($item->fundsource->saa) && strpos($item->fundsource->saa, 'CONAP') !== false;

                    if ($item->remaining_balance == 0) {
                        if ($containsCONAP) {
                            return 4; 
                        }else{
                            return 5;
                        }
                    }elseif($containsCONAP){
                        if ($contains702 || $is702) {
                            return 1; 
                        }else{
                            return 0;
                        }
                    }else{
                        if ($contains702 || $is702) {
                            return 3; 
                        }else{
                            return 2;
                        }
                    }
            
                });

        return view('pre_dv.proponent_clone', [
            'proponents' => $proponents,
            'saas' => $saas,
            'facility_id' => $facility_id,
            'info' => $info,
            'facility' => Facility::where('id', $facility_id)->first(),
        ]);
    }

    public function cloneSAA($facility_id)
    {
        $saas = Fundsource::get();

        $info = ProponentInfo::with('facility', 'fundsource', 'proponent')
                ->where(function ($query) use ($facility_id) {
                    $query->whereJsonContains('proponent_info.facility_id', '702')
                        ->orWhereJsonContains('proponent_info.facility_id', [$facility_id]);
                })
                ->orWhereIn('proponent_info.facility_id', [$facility_id, '702'])
                ->get()
                ->sortBy(function ($item) {
                    $facility_id = $item->facility_id;
                    $contains702 = is_string($facility_id) && strpos($facility_id, '702') !== false;
                    $is702 = $facility_id == 702;
                    $containsCONAP = isset($item->fundsource->saa) && strpos($item->fundsource->saa, 'CONAP') !== false;

                    if ($item->remaining_balance == 0) {
                        if ($containsCONAP) {
                            return 4; 
                        }else{
                            return 5;
                        }
                    }elseif($containsCONAP){
                        if ($contains702 || $is702) {
                            return 1; 
                        }else{
                            return 0;
                        }
                    }else{
                        if ($contains702 || $is702) {
                            return 3; 
                        }else{
                            return 2;
                        }
                    }
            
                });
                
        return view('pre_dv.saa_clone', [
            'saas' => $saas,
            'facility_id' => $facility_id,
            'facility' => Facility::where('id', $facility_id)->first(),
            'info' => $info
        ]);
    }

    public function cloneControl()
    {
        return view('pre_dv.control_clone');
    }

    public function savePreDV(Request $request)
    {

        $decodedData = urldecode($request->data);
        $all_data = json_decode($decodedData, true);
        $grand_total = $request->grand_total;
        $facility_id = $request->facility_id;

        $pre_dv = new PreDV();
        $pre_dv->facility_id = $facility_id;
        $pre_dv->grand_total = (float) str_replace(',', '', $grand_total);
        $pre_dv->created_by = Auth::user()->userid;
        $pre_dv->save();

        foreach ($all_data as $value) {
            $proponent_id = $value['proponent'];
            $control_nos = $value['pro_clone'];
            $fundsources = $value['fundsource_clone'];
            $proponent = Proponent::where('proponent', $proponent_id)->value('id');

            $pre_extension = new PreDVExtension();
            $pre_extension->pre_dv_id = $pre_dv->id;
            $pre_extension->proponent_id = $proponent;
            $pre_extension->total_amount = (float) str_replace(',', '', $value['total_amount']);
            $pre_extension->save();

            foreach ($control_nos as $row) {
                $controls = new PreDVControl();
                $controls->predv_extension_id = $pre_extension->id;
                $controls->control_no = $row['control_no'];
                $controls->patient_1 = $row['patient_1'];
                if($row['patient_2']){
                    $controls->patient_2 = $row['patient_2'];
                }
                $controls->amount = (float) str_replace(',', '', $row['amount']);
                $controls->save();
            }

            foreach ($fundsources as $saa) {
                $pre_saa = new PreDVSAA();
                $pre_saa->predv_extension_id = $pre_extension->id;
                $pre_saa->fundsource_id = $saa['saa_id'];
                $pre_saa->info_id = $saa['info_id'];
                $pre_saa->amount = (float) str_replace(',', '', $saa['saa_amount']);
                $pre_saa->save();
            }
        }
        return redirect()->back()->with('pre_dv', true);
    }

    public function displayPreDV($id)
    {

        $pre_dv = PreDV::where('id', $id)->with(
            [
                'user:userid,fname,lname,mname',
                'facility:id,name',
                'new_dv',
                'extension' => function ($query) {
                    $query->with(
                        [
                            'proponent:id,proponent',
                            'controls',
                            'saas' => function ($query) {
                                $query->with([
                                    'saa:id,saa'
                                ]);
                            }
                        ]
                    );
                }
            ]
        )->first();
        // return $pre_dv;

        $saas = Fundsource::get();
        $proponents = Proponent::select('proponent')->groupBy('proponent')->get();
        $facilities = Facility::get();
        $facility_id = (string) $pre_dv->facility_id;
        $info = ProponentInfo::with('facility', 'fundsource', 'proponent')
            ->where(function ($query) use ($facility_id) {
                $query->whereJsonContains('proponent_info.facility_id', '702')
                    ->orWhereJsonContains('proponent_info.facility_id', [$facility_id]);
            })
            ->orWhereIn('proponent_info.facility_id', [$facility_id, '702'])
            ->get();
        // return $pre_dv;
        return view('pre_dv.update_predv', [
            'result' => $pre_dv,
            'proponents' => $proponents,
            'saas' => $saas,
            'facilities' => $facilities,
            'info' => $info
        ]);
    }

    public function updatePreDV(Request $request)
    {

        $decodedData = urldecode($request->data);
        $all_data = json_decode($decodedData, true);
        $id = $request->pre_id;
        $grand_total = (float) str_replace(',','',$request->grand_total);
        $facility_id = $request->facility_id;
        $pre_dv = PreDV::where('id', $id)->first();

        if ($pre_dv) {
            $pre_dv->facility_id = $request->facility_id;
            $pre_dv->grand_total = str_replace(',', '', $request->grand_total);
            $pre_dv->save();
            
            $extension = PreDVExtension::where('pre_dv_id', $id)->pluck('id')->toArray();
            $ex_control = PreDVControl::whereIn('predv_extension_id', $extension)->delete();
            $ex_saa = PreDVSAA::whereIn('predv_extension_id', $extension)->delete();
            PreDVExtension::where('pre_dv_id', $id)->delete();

            foreach ($all_data as $value) {

                $proponent_id = $value['proponent'];
                $control_nos = $value['pro_clone'];
                $fundsources = $value['fundsource_clone'];
                $proponent = Proponent::where('proponent', $proponent_id)->value('id');

                $pre_extension = new PreDVExtension();
                $pre_extension->pre_dv_id = $pre_dv->id;
                $pre_extension->proponent_id = $proponent;
                $pre_extension->total_amount = (float) str_replace(',', '', $value['total_amount']);
                $pre_extension->save();

                foreach ($control_nos as $row) {
                    $controls = new PreDVControl();
                    $controls->predv_extension_id = $pre_extension->id;
                    $controls->control_no = $row['control_no'];
                    $controls->patient_1 = $row['patient_1'];
                    if($row['patient_2']){
                        $controls->patient_2 = $row['patient_2'];
                    }
                    $controls->amount = (float) str_replace(',', '', $row['amount']);
                    $controls->save();
                }
    
                foreach ($fundsources as $saa) {
                    $pre_saa = new PreDVSAA();
                    $pre_saa->predv_extension_id = $pre_extension->id;
                    $pre_saa->fundsource_id = $saa['saa_id'];
                    $pre_saa->info_id = $saa['info_id'];
                    $pre_saa->amount = (float) str_replace(',', '', $saa['saa_amount']);
                    $pre_saa->save();
                }

            }
            
            return redirect()->back()->with('pre_dv', true);
        } else {
            return redirect()->back()->with('pre_dv_error', true);
        }
    }

    public function deletePreDV($id)
    {
        $pre_dv = PreDV::where('id', $id)->first();
        if ($pre_dv) {
            $extension = PreDVExtension::where('pre_dv_id', $id)->get();
            PreDVSAA::where('predv_extension_id', $extension[0]->id)->delete();
            PreDVControl::where('predv_extension_id', $extension[0]->id)->delete();
            PreDVExtension::where('pre_dv_id', $id)->delete();
            $pre_dv->delete();
            return redirect()->back()->with('remove_pre_dv', true);
        } else {
            return redirect()->back()->with('pre_dv_error', true);
        }
    }

    public function newDV(Request $request)
    {
        $id = $request->id;

        $pre = PreDV::where('id', $id)->with(
            [
                'user:userid,fname,lname,mname',
                'facility' => function ($query) {
                    $query->with('addFacilityInfo');
                },
                'extension' => function ($query) {
                    $query->with(
                        [
                            'proponent:id,proponent',
                            'controls',
                            'saas' => function ($query) {
                                $query->with([
                                    'saa:id,saa'
                                ]);
                            }
                        ]
                    );
                }
            ]
        )->first();
        $existing = NewDV::where('predv_id', $id)->first();
        // return $pre;
        if ($existing) {

            $existing->date = $request->date;
            $existing->date_from = $request->date_from . '-1';
            if ($request->date_to) {
                $existing->date_to = $request->date_to . '-1';
            }
            $existing->total = $request->total_amount;
            $existing->accumulated = (float) str_replace(',','',$request->accumulated);
            $existing->created_by = Auth::user()->userid;
            $existing->save();

            return redirect()->back()->with('pre_dv_update', true);
        } else {

            $new_dv = new NewDV();
            $new_dv->route_no = date('Y-') . Auth::user()->userid . date('mdHis');
            $new_dv->predv_id = $pre->id;
            $new_dv->date = $request->date;
            $new_dv->date_from = $request->date_from . '-1';
            if ($request->date_to) {
                $new_dv->date_to = $request->date_to . '-1';
            }
            $new_dv->total = $request->total_amount;
            $new_dv->accumulated = (float) str_replace(',','',$request->accumulated);
            $new_dv->created_by = Auth::user()->userid;
            $new_dv->status = 0;
            $new_dv->save();

            $desc = "Disbursement voucher for " . $pre->facility->name . " amounting to Php " . number_format(str_replace(',', '', $request->total_amount), 2, '.', ',');
            $dts_user = DB::connection('dts')->select("SELECT id FROM users WHERE username = ? LIMIT 1", array(Auth::user()->userid));
            $data = [
                $new_dv->route_no, "DV", $new_dv->created_at, $dts_user[0]->id, 0,  $desc, 0.00, "", "", "", "", "", "", "", "", "", "", "0000-00-00 00:00:00",
                "", "", "", 0, "", !empty($dv->dv_no) ? $dv->dv_no : "", "", "", "", "",
            ];
            DB::connection('dts')->insert(
                "INSERT INTO TRACKING_MASTER(route_no, doc_type, prepared_date, prepared_by, division_head, description, amount, pr_no, po_no, pr_date, purpose, po_date, 
                    source_fund, requested_by, route_to, route_from, supplier, event_date, event_location, event_participant, cdo_applicant, cdo_day, event_daterange, 
                    payee, item, dv_no, ors_no, fund_source_budget, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, now(), now())",
                $data
            );

            $tracking_master = TrackingMaster::where('route_no', $new_dv->route_no)->first();
            $updated_route = date('Y-') . $tracking_master->id;
            $tracking_master->route_no = $updated_route;
            $tracking_master->save();
            $new_dv->route_no = $updated_route;
            $new_dv->save();
            //creating tracking_details
            $data_details = [$updated_route, "", 0, $new_dv->created_at, $dts_user[0]->id, $dts_user[0]->id,  $desc, 0];
            DB::connection('dts')->insert("INSERT INTO TRACKING_DETAILS(route_no, code, alert, date_in, received_by, delivered_by, action, status,created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, now(), now())", $data_details);

            //utilization

            foreach ($pre->extension as $data) {
                foreach ($data->saas as $row) {
                    $info = ProponentInfo::where('id', $row->info_id)->first();
                    if ($info) {

                        $vat = $pre->facility->addFacilityInfo->vat;
                        $ewt = $pre->facility->addFacilityInfo->Ewt;

                        $total = $row->amount;

                        if ($vat > 3) {
                            $total = ($total / 1.12 * $vat / 100) + ($total / 1.12 * $vat / 100);
                        } else {
                            $total = ($total * $vat / 100) + ($total * $vat / 100);
                        }

                        $util = new Utilization();
                        $util->proponent_id = $data->proponent_id;
                        $util->fundsource_id = $row->fundsource_id;
                        $util->proponentinfo_id = $info->id;
                        $util->div_id = $new_dv->route_no;
                        $util->beginning_balance = $info->remaining_balance;
                        $util->utilize_amount = $row->amount;
                        $util->discount = $total;
                        $util->created_by = Auth::user()->userid;
                        $util->status = 0;
                        $util->facility_id = $pre->facility_id;
                        $util->save();

                        $info->remaining_balance = (float) str_replace(',', '', $info->remaining_balance) - (float) str_replace(',', '', $row->amount);
                        $info->save();
                    }
                }
            }
            return redirect()->back()->with('pre_dv', true);
        }
    }

    public function v2Delete($route_no){
        $new_dv = NewDV::where('route_no', $route_no)->first();
        if($new_dv){
            $uzs = Utilization::where('div_id', $route_no)->get();
            if($uzs){
                foreach($uzs as $u){
                    $info = ProponentInfo::where('id', $u->proponentinfo_id)->first();
                    $info->remaining_balance = (float) str_replace(',','', $info->remaining_balance) + (float) str_replace(',','', $u->utilize_amount);
                    $info->save();
                    $u->delete();
                }
            }
            $new_dv->delete();
        }
        return redirect()->back()->with('pre_dv_remove', true);
    }

    public function processNew(Request $request){
        $new = NewDV::where('route_no', $request->new_dv_id)->first();
        $type = $request->type;
        if($type == 'pending_new'){
            $new->ors_no = $request->ors_no;
            $new->obligated_by = Auth::user()->userid;
            $new->obligated_on =  date('Y-m-d H:i:s');
            $new->status = 1;
            $new->save();

            $uts = Utilization::where('div_id', $new->route_no)->get();

            if($uts){
                foreach($uts as $u){
                    $fund = Fundsource::where('id', $u->fundsource_id)->first();

                    $u->budget_bbalance = $fund->remaining_balance;
                    $u->budget_utilize = $u->utilize_amount;
                    $u->obligated = 1;
                    $u->obligated_by = Auth::user()->userid;
                    $u->obligated_on = date('Y-m-d H:i:s');
                    $u->save();
                    
                    $fund->remaining_balance = (float) str_replace(',','', $fund->remaining_balance) + (float) str_replace(',','', $u->utilize_amount);
                    $fund->save();
                }
            }
            
            return redirect()->back()->with('pre_dv_update', true);
        }else if($type == 'cashier_pending'){
            $new->paid_on = date('Y-m-d H:i:s');
            $new->paid_by = Auth::user()->userid;
            $new->status = 2;
            $new->save();

            $uts = Utilization::where('div_id', $new->route_no)->get();

            if($uts){
                foreach($uts as $u){
                    $u->paid = 1;
                    $u->paid_by = Auth::user()->userid;
                    $u->paid_on = date('Y-m-d H:i:s');
                    $u->save();
                }
            }

            return redirect()->back()->with('pay_dv', true);
        }
    }

    public function controlList($facility_id){
        $searchValue = Facility::where('id', $facility_id)->value('name');
        $pre_dv = PreDV::where('facility_id', $facility_id)->pluck('id')->toArray();
        $extension = PreDVExtension::whereIn('pre_dv_id', $pre_dv)->pluck('id')->toArray();
        $controls = PreDVControl::whereIn('predv_extension_id', $extension)->pluck('control_no')->toArray();
        $dv2 = Dv2::whereRaw("facility REGEXP ?", ["^" . preg_quote($searchValue, '/')])->pluck('ref_no')->toArray();
        return response()->json(['controls'=> array_merge($controls, $dv2)]);
    }

    public function check(){
        // return 12;
        $utils = Utilization::whereIn('div_id', ['2024-303798','2024-303788','2024-303779','2024-303748', '2024-303718','2024-303514','2024-303453',
        '2024-303433', '2024-303398','2024-304064', '2024-304058', '2024-303839', '2024-303808', '2024-303802',
        '2024-303390', '2024-302977'])->get();
        
        // return $utils;
        foreach($utils as $row){
            $row->delete();
            // $info = ProponentInfo::where('id', $row->proponentinfo_id)->first();
            // $info->remaining_balance = (float) str_replace(',','', $info->remaining_balance) + (float) str_replace(',','', $row->utilize_amount);
            // $info->save();
        }
    }
}
