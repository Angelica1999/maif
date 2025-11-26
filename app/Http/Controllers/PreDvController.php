<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
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
use App\Models\Transmittal;
use App\Models\TrackingDetails;
use App\Models\ProponentInfo;
use App\Models\Utilization;
use App\Models\Dv2;
use App\Models\User;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PDF;

class PreDvController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('block.secure.nonadmin');
    }

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
                'new_dv:id,predv_id,route_no',
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
        $gen_date = $request->dates_filter;
        if ($request->generate && !$request->viewAll) {
            $dateRange = explode(' - ', $request->dates_filter);
            $start_date = date('Y-m-d', strtotime($dateRange[0]));
            $end_date = date('Y-m-d', strtotime($dateRange[1]));
            $pre_dv ->whereBetween('created_at', [$start_date, $end_date . ' 23:59:59']);
        }else{
            $gen_date = '';
        }

        $saas = Fundsource::get();
        $proponents = Proponent::select('proponent')->groupBy('proponent')->get();
        $facilities = Facility::select('id', 'name') 
            ->with(['addFacilityInfo:id,facility_id,vat,ewt'])
            ->get();
    
        if($request->viewAll){
            $request->keyword = '';
            $request->f_id = '';
            $request->b_id = '';
            $request->generate = '';
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

        if($request->f_id){
            $pre_dv->whereIn('facility_id', explode(',', $request->f_id));
        }elseif($request->b_id){
            $pre_dv->whereIn('created_by', explode(',', $request->b_id));
        }

        $pre_dv = PreDV::with([
            'user:userid,fname,lname,mname',
            'facility:id,name',
            'new_dv:id,predv_id,route_no,edit_status',
            'extension' => function ($query) {
                $query->with([
                    'proponent:id,proponent',
                    'controls',
                    'saas.saa:id,saa'
                ]);
            }
        ])->withCount('extension as controls_count');

        
        $sortOrder = $request->input('sort_order'); 

        if ($sortOrder) {
            $pre_dv = $pre_dv->orderBy('controls_count', $sortOrder);
        } else {
            $pre_dv = $pre_dv->orderBy('id', 'desc');
        }

        $gen_date = $request->dates_filter;
        
        if ($request->generate && !$request->viewAll) {
            $dateRange = explode(' - ', $gen_date);
            $start_date = date('Y-m-d', strtotime($dateRange[0]));
            $end_date = date('Y-m-d', strtotime($dateRange[1])) . ' 23:59:59';
            $pre_dv->whereBetween('created_at', [$start_date, $end_date]);
        } else {
            $gen_date = '';
        }
        
        // Load related data efficiently
        $saas = Fundsource::all();
        $proponents = Proponent::select('proponent')->distinct()->get();
        $facilities = Facility::select('id', 'name')
            ->with('addFacilityInfo:id,facility_id,vat,ewt')
            ->get();
        
        if ($request->viewAll) {
            $request->merge(['keyword' => '', 'f_id' => '', 'b_id' => '', 'generate' => '']);
        } elseif ($request->keyword) {
            $keyword = $request->keyword;
            $pre_dv->where(function ($query) use ($keyword) {
                $query->whereHas('facility', function ($query) use ($keyword) {
                    $query->where('name', 'LIKE', '%' . $keyword . '%');
                })->orWhereHas('extension.controls', function ($query) use ($keyword) {
                    $query->where('control_no', 'LIKE', '%' . $keyword . '%');
                })->orWhereHas('new_dv', function ($query) use ($keyword) {
                    $query->where('route_no', 'LIKE', '%' . $keyword . '%');
                });
            });
        }
        
        if ($request->f_id) {
            $pre_dv->whereIn('facility_id', explode(',', $request->f_id));
        } elseif ($request->b_id) {
            $pre_dv->whereIn('created_by', explode(',', $request->b_id));
        }

        $pre_dv_ids = $pre_dv->pluck('id')->toArray();

        $totalControls = DB::table('pre_dv_control')
            ->whereIn('predv_extension_id', function ($query) use ($pre_dv_ids) {
                $query->select('id')
                    ->from('pre_dv_extension')
                    ->whereIn('pre_dv_id', $pre_dv_ids);
            })
            ->count();


            
        // $pre_dv_ids = $pre_dv->pluck('id')->toArray();
        // $totalControls = PreDVControl::whereIn('predv_extension_id',
        //     PreDVExtension::whereIn('pre_dv_id', $pre_dv_ids)->pluck('id')
        // )->count();
        
        // $totalControls = $pre_dv->get()->sum(function ($preDv) {
        //     return $preDv->extension->sum(function ($extension) {
        //         return $extension->controls->count();
        //     });
        // });

        $total = $pre_dv->take(1)->count();        
        $grand_amount = $pre_dv->sum('grand_total');
        $grand_fee = $pre_dv->sum('prof_fee');
        $pre_dv = $pre_dv->orderBy('id', 'desc')->paginate(20);
        // return $pre_dv;

        $pre_ids = PreDv::pluck('facility_id')->unique()->values()->toArray();
        $user_ids = PreDv::pluck('created_by')->unique()->values()->toArray();
        $f_list = Facility::whereIn('id', $pre_ids)->select('id','name')->get();
        $user_data = User::whereIn('userid', $user_ids)->select('userid','fname','lname','mname')->get();

        return view('pre_dv.pre_dv', [
            'facility_data' => $f_list,
            'user_data' => $user_data,
            'results' => $pre_dv,
            'proponents' => $proponents,
            'saas' => $saas,
            'facilities' => $facilities,
            'keyword' => $request->keyword,
            'f_id' => explode(',', $request->f_id),
            'b_id' => explode(',', $request->b_id),
            'generate' => $request->generate,
            'num_generated' => $total,
            'grand_amount' => $grand_amount,
            'generated_dates' => $gen_date,
            'total_control' => $totalControls,
            'grand_fee' => $grand_fee,
            'sortOrder'       => $sortOrder
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

        if ($request->generate && !$request->viewAll) {

            $dateRange = explode(' - ', $request->dates_filter);
            $start_date = date('Y-m-d', strtotime($dateRange[0]));
            $end_date = date('Y-m-d', strtotime($dateRange[1]));
            $pre_dv ->whereBetween('created_at', [$start_date, $end_date . ' 23:59:59']);
        
        }

        $saas = Fundsource::get();
        $proponents = Proponent::select('proponent')->groupBy('proponent')->get();
        $facilities = Facility::get();

        if($request->viewAll){
            $request->keyword = '';
            $request->f_id = '';
            $request->b_id = '';
            $request->p_id = '';
            $request->generate = '';
        }else if($request->keyword){
            $keyword = $request->keyword;
            $pre_dv->WhereHas('facility', function ($query) use ($keyword) {
                $query->where('name', 'LIKE', '%' . $keyword . '%');
                 })->orWhereHas('extension.proponent', function ($query) use ($keyword) {
                $query->where('proponent', 'LIKE', '%' . $keyword . '%'); 
            });
            
        }

        if($request->f_id){
            $pre_dv->whereIn('facility_id', explode(',', $request->f_id));
        }elseif($request->b_id){
            $pre_dv->whereIn('created_by', explode(',', $request->b_id));
        }elseif($request->p_id){
            $pre_dv->whereHas('extension', function ($query) use ($request) {
                $query->whereIn('proponent_id', explode(',', $request->p_id));
            });
        }

        $pre_dv = $pre_dv->orderBy('id', 'desc')->paginate(50);

        return view('pre_dv.pre_dv1', [
            'results' => $pre_dv,
            'proponents' => $proponents,
            'saas' => $saas,
            'facilities' => $facilities,
            'keyword' => $request->keyword,
            'f_id' => explode(',', $request->f_id),
            'b_id' => explode(',', $request->b_id),
            'p_id' => explode(',', $request->p_id),
            'generate' => $request->generate,
            'proponents' => Proponent::whereIn('id', PreDVExtension::pluck('proponent_id')->toArray())->get()
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

        if ($request->generate && !$request->viewAll) {

            $dateRange = explode(' - ', $request->dates_filter);
            $start_date = date('Y-m-d', strtotime($dateRange[0]));
            $end_date = date('Y-m-d', strtotime($dateRange[1]));
            $pre_dv ->whereBetween('created_at', [$start_date, $end_date . ' 23:59:59']);
        
        }

        $saas = Fundsource::get();
        $proponents = Proponent::select('proponent')->groupBy('proponent')->get();
        $facilities = Facility::get();

        if($request->viewAll){
            $request->keyword = '';
            $request->f_id = '';
            $request->p_id = '';
            $request->b_id = '';
            $request->s_id = '';
        }else if($request->keyword){
            $keyword = $request->keyword;
            $pre_dv->whereHas('new_dv', function ($query) use ($keyword) {
                $query->where('route_no', 'LIKE', '%' . $keyword . '%');
            })->orWhereHas('facility', function ($query) use ($keyword) {
                $query->where('name', 'LIKE', '%' . $keyword . '%');
            })->orWhereHas('extension.proponent', function ($query) use ($keyword) {
                $query->where('proponent', 'LIKE', '%' . $keyword . '%');    
            })->orWhereHas('extension.controls', function ($query) use ($keyword) {
                $query->where('control_no', 'LIKE', '%' . $keyword . '%');
            });
        
        }

        if($request->f_id){
            $pre_dv->whereIn('facility_id', explode(',', $request->f_id));
        }elseif($request->b_id){
            $pre_dv->whereIn('created_by', explode(',', $request->b_id));
        }elseif($request->p_id){
            $pre_dv->whereHas('extension', function ($query) use ($request) {
                $query->whereIn('proponent_id', explode(',', $request->p_id));
            });
        }elseif($request->s_id){
            $pre_dv->whereHas('new_dv', function ($query) use ($request) {
                $query->whereIn('status', explode(',', $request->s_id));
            });
        }

        if($request->stat_select){
            $pre_dv->whereHas('new_dv', function ($query) use ($request) {
                $query->whereIn('status', $request->stat_select);
            });
        }

        $pre_dv = $pre_dv->orderBy('id', 'desc')->paginate(50);

        $pre_ids = PreDv::pluck('facility_id')->unique()->values()->toArray();
        $f_list = Facility::whereIn('id', $pre_ids)->select('id','name')->get();

        return view('pre_dv.pre_dv2', [
            'facility_data' => $f_list,
            'results' => $pre_dv,
            'proponents' => $proponents,
            'saas' => $saas,
            'facilities' => $facilities,
            'keyword' => $request->keyword,
            'generate' => $request->generate,
            'dates_generated' => $request->input('dates_filter'),
            'f_id' => explode(',', $request->f_id),
            'p_id' => explode(',', $request->p_id),
            'b_id' => explode(',', $request->b_id),
            's_id' => explode(',', $request->s_id),
            'pros' => Proponent::whereIn('id', PreDVExtension::pluck('proponent_id')->toArray())->get(),
            'status' => $request->stat_select
        ]);
    }

    public function exclude($ids){
        $ids = array_map('intval', explode(',', $ids));
        foreach($ids as $id){
            $new_dv = NewDV::where('id', $id)->first();
            if($new_dv->saa_exclusion == 1){
                $new_dv->saa_exclusion = false;
                $result = 0;
            }else{
                $new_dv->saa_exclusion = true;
                $result = 1;
            }
            $new_dv->save();
        }
        return response()->json($result);
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
        }

        if (in_array($type, ['awaiting', 'accomplished', 'deferred', 'disbursed'])) {
            $pre_dv->whereHas('new_dv', function ($query) use ($type) {
                if ($type == 'awaiting') {
                    $query->whereNull('obligated_by');
                } elseif ($type == 'accomplished') {
                    $query->whereNotNull('obligated_by');
                } elseif ($type == 'deferred') {
                    $query->whereNull('paid_by')->whereNotNull('ors_no');
                } elseif ($type == 'disbursed') {
                    $query->whereNotNull('paid_by')->whereNotNull('ors_no');
                }
            });
        
            if ($request->keyword) {
                $keyword = $request->keyword;
                $pre_dv->where(function ($query) use ($keyword) {
                    $query->whereHas('new_dv', function ($subQuery) use ($keyword) {
                        $subQuery->where('route_no', 'LIKE', '%' . $keyword . '%');
                    })->orWhereHas('facility', function ($subQuery) use ($keyword) {
                        $subQuery->where('name', 'LIKE', '%' . $keyword . '%');
                    });
                });
            }
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
        $ors = Utilization::where('div_id', $new_dv->route_no)->pluck('ors_no')->filter()->unique()->implode(', ');
        // return $ors;
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
                'type' => $type,
                'ors' =>  $ors,
                'amount' => $grouped->sum('amount')
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
        // return $new_dv;
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
                'new_dv' => $new_dv,
                'amount' => $grouped->sum('amount'),
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
        // return 1;
        if($request->all_transmittal){
            Transmittal::whereIn('id', $request->all_transmittal)->update(['used'=>1]);
        }

        $decodedData = urldecode($request->data);
        $all_data = json_decode($decodedData, true);
        $grand_total = $request->grand_total;
        $grand_fee = $request->grand_fee;
        $facility_id = $request->facility_id;

        $pre_dv = new PreDV();
        $pre_dv->facility_id = $facility_id;
        $pre_dv->grand_total = (float) str_replace(',', '', $grand_total);
        $pre_dv->prof_fee = (float) str_replace(',', '', $grand_fee);
        $pre_dv->created_by = Auth::user()->userid;
        $pre_dv->trans_id =  $request->all_transmittal ? implode(',', $request->all_transmittal) : null;
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
                $controls->prof_fee = (float) str_replace(',', '', $row['prof_fee']);
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

        $saas = Fundsource::get();
        $proponents = Proponent::select('proponent')->groupBy('proponent')->get();
        $facilities = Facility::select('id','name')->get();
        $facility_id = (string) $pre_dv->facility_id;
        $info = ProponentInfo::with('facility:id,name', 'fundsource', 'proponent:id,proponent')
            ->where(function ($query) use ($facility_id) {
                $query->whereJsonContains('proponent_info.facility_id', '702')
                    ->orWhereJsonContains('proponent_info.facility_id', [$facility_id]);
            })
            ->orWhereIn('proponent_info.facility_id', [$facility_id, '702'])
            ->get();
        return view('pre_dv.update_predv', [
            'result' => $pre_dv,
            'proponents' => $proponents,
            'saas' => $saas,
            'facilities' => $facilities,
            'info' => $info
        ]);
    }

    public function preExcel($id){
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

        $saas = Fundsource::get();
        $proponents = Proponent::select('proponent')->groupBy('proponent')->get();
        $facilities = Facility::select('id','name')->get();
        $facility_id = (string) $pre_dv->facility_id;
        $info = ProponentInfo::with('facility:id,name', 'fundsource', 'proponent:id,proponent')
            ->where(function ($query) use ($facility_id) {
                $query->whereJsonContains('proponent_info.facility_id', '702')
                    ->orWhereJsonContains('proponent_info.facility_id', [$facility_id]);
            })
            ->orWhereIn('proponent_info.facility_id', [$facility_id, '702'])
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Adjust column widths
        $sheet->getColumnDimension('A')->setWidth(40);  
        $sheet->getColumnDimension('B')->setWidth(50); 
        $sheet->getColumnDimension('C')->setWidth(30); 
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);

        $sheet->mergeCells("A1:E1");
        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("Facility: " . $pre_dv->facility->name);
        $normalText->getFont()->setBold(true)->setSize(20); 
        $sheet->setCellValue('A1', $richText1);
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getStyle('A1')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A1:E1')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("Proponent");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('A2', $richText1);
        $sheet->getStyle('A2')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("Control Number");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('B2', $richText1);
        $sheet->getStyle('B2')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("Patient 1");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('C2', $richText1);
        $sheet->getStyle('C2')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("Patient 2");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('D2', $richText1);
        $sheet->getStyle('D2')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("Professional Fee");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('E2', $richText1);
        $sheet->getStyle('E2')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("Amount");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('F2', $richText1);
        $sheet->getStyle('F2')->getAlignment()->setWrapText(true);

        $sheet->getStyle('A2:F2')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $data = [];

        foreach($pre_dv->extension as $index=>$row){
            foreach($row->controls as $index1 => $row2){
                $data[] = [
                    $row->proponent->proponent,
                    $row2->control_no,
                    $row2->patient_1,
                    $row2->patient_2,
                    $row2->prof_fee == null ? (float) 0.00 : (float) $row2->prof_fee,
                    $row2->amount
                ];
            }
        }

        $sheet->fromArray($data, null, 'A3');
        $sheet->getStyle('E3:F' . (count($data) + 2))
        ->getNumberFormat()->setFormatCode('#,##0.00');

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER, 
            ],
        ];
        
        $sheet->getStyle('A2:F' . (count($data) + 2))->applyFromArray($styleArray);
        $sheet->getStyle('A3:F' . (count($data) + 2))->getAlignment()->setWrapText(true);

        $sheet->getStyle('A3:D' . (count($data) + 2))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        
        $sheet->getStyle('E3:F' . (count($data) + 2))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            
        // Output preparation
        ob_start();
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();

        // Filename
        $facilityName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $pre_dv->facility->name ?? 'Unknown');
        $filename = "Pre-DV_" . $facilityName . "_" . date('Ymd') . ".xlsx";
        // Set headers
        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=$filename");
        header("Pragma: no-cache");
        header("Expires: 0");

        // Output the file
        return $xlsData;
        exit;
        
    }

    public function updatePreDV(Request $request)
    {

        $decodedData = urldecode($request->data);
        $all_data = json_decode($decodedData, true);
        $id = $request->pre_id;
        $grand_total = (float) str_replace(',','',$request->grand_total);
        $grand_fee = (float) str_replace(',','',$request->grand_fee);
        $facility_id = $request->facility_id;
        $pre_dv = PreDV::where('id', $id)->first();

        if ($pre_dv) {
            $pre_dv->facility_id = $facility_id;
            $pre_dv->grand_total = $grand_total;
            $pre_dv->prof_fee = $grand_fee;
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
                    $controls->prof_fee = (float) str_replace(',', '', $row['prof_fee']);
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

            $check_latest = PreDVExtension::where('pre_dv_id', $pre_dv->id)->get();

            $fees = PreDVControl::whereIn('predv_extension_id', $check_latest->pluck('id')->toArray())->get();
            $pre_dv->grand_total = $check_latest ? $check_latest->sum('total_amount') : $pre_dv->grand_total;
            $pre_dv->prof_fee = $fees ? $fees->sum('prof_fee') : $pre_dv->prof_fee;
            $pre_dv->save();

            return redirect()->back()->with('pre_dv', true);
        } else {
            return redirect()->back()->with('pre_dv_error', true);
        }
    }

    public function modifyPreDV(Request $request){
        try{

            $decodedData = urldecode($request->data);
            $all_data = json_decode($decodedData, true);
            $id = $request->pre_id;
            $grand_total = (float) str_replace(',','',$request->grand_total);
            $grand_fee = (float) str_replace(',','',$request->grand_fee);
            $facility_id = $request->facility_id;
            $pre_dv = PreDV::where('id', $id)->first();

            $new_dv = NewDV::where('predv_id', $id)->first();

            if ($pre_dv) {
                $pre_dv->facility_id = $request->facility_id;
                $pre_dv->grand_total = $grand_total;
                $pre_dv->prof_fee = $grand_fee;
                $pre_dv->save();
                
                $extension = PreDVExtension::where('pre_dv_id', $id)->pluck('id')->toArray();
                $ex_saa = PreDVSAA::whereIn('predv_extension_id', $extension)->get();
                foreach($ex_saa as $row){
                    $info_here = ProponentInfo::where('id', $row->info_id)->first();
                    $info_here->remaining_balance = str_replace(',', '', $info_here->remaining_balance) + $row->amount;
                    $info_here->save();
                }

                $ex_control = PreDVControl::whereIn('predv_extension_id', $extension)->delete();
                $ex_saa1 = PreDVSAA::whereIn('predv_extension_id', $extension)->delete();
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
                        $controls->prof_fee = (float) str_replace(',', '', $row['prof_fee']);
                        $controls->save();
                    }
        
                    foreach ($fundsources as $saa) {
                        $pre_saa = new PreDVSAA();
                        $pre_saa->predv_extension_id = $pre_extension->id;
                        $pre_saa->fundsource_id = $saa['saa_id'];
                        $pre_saa->info_id = $saa['info_id'];
                        $pre_saa->amount = (float) str_replace(',', '', $saa['saa_amount']);
                        $pre_saa->save();

                        $utilization_here = Utilization::where('div_id', $new_dv->route_no)->delete();

                        $info = ProponentInfo::where('id', $saa['info_id'])->first();
                        if ($info) {

                            $facility_info = AddFacilityInfo::where('facility_id', $pre_dv->facility_id)->first();

                            $total = (float) str_replace(',', '', $saa['saa_amount']);

                            if($facility_info){
                                $vat = $facility_info->vat;
                                $ewt = $facility_info->Ewt;

                                if ($vat > 3) {
                                    $total = ($total / 1.12 * $vat / 100) + ($total / 1.12 * $vat / 100);
                                } else {
                                    $total = ($total * $vat / 100) + ($total * $vat / 100);
                                }
                            }

                            $util = new Utilization();
                            $util->proponent_id = $info->proponent_id;
                            $util->fundsource_id = $info->fundsource_id;
                            $util->proponentinfo_id = $pre_saa->info_id;
                            $util->div_id = $new_dv->route_no;
                            $util->beginning_balance = $info->remaining_balance;
                            $util->utilize_amount = (float) str_replace(',', '', $saa['saa_amount']);
                            $util->discount = $total;
                            $util->created_by = Auth::user()->userid;
                            $util->status = 0;
                            $util->facility_id = $pre_dv->facility_id;
                            $util->save();

                            $info->remaining_balance = (float) str_replace(',', '', $info->remaining_balance) - (float) str_replace(',', '', $saa['saa_amount']);
                            $info->save();
                        }
                    }
                }
                
                $check_latest = PreDVExtension::where('pre_dv_id', $pre_dv->id)->get();

                $fees = PreDVControl::whereIn('predv_extension_id', $check_latest->pluck('id')->toArray())->get();
                $pre_dv->grand_total = $check_latest ? $check_latest->sum('total_amount') : $pre_dv->grand_total;
                $pre_dv->prof_fee = $fees ? $fees->sum('prof_fee') : $pre_dv->prof_fee;
                $pre_dv->save();

                NewDV::where('predv_id', $id)->update(
                    [
                        'total' => $pre_dv->grand_total,
                        'edit_status' => 0
                    ]
                );

                $name = Facility::where('id', $pre_dv->facility_id)->value('name');
                $route_no = $new_dv->route_no;

                TrackingMaster::where('route_no', $route_no)->update([
                    'description' => "Disbursement Voucher for". $name . "amoung to PhP" . number_format($pre_dv->grand_total, 2, '.', ',')
                ]);

                $details = TrackingDetails::where('route_no', $route_no)->first();
                if($details){
                    TrackingDetails::where('route_no', $route_no)->where('id', $details->id)->update([
                        'action' => "Disbursement Voucher for". $name . "amoung to PhP" . number_format($pre_dv->grand_total, 2, '.', ',')
                    ]);
                }

                return redirect()->back()->with('pre_dv', true);
            } else {
                return redirect()->back()->with('pre_dv_error', true);
            }
        }catch (Exception $e) {
            return $e->getMessage();
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
        
        $trans_ids = array_map('intval', explode(',', $pre->trans_id));

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
            if($pre->trans_id){
                Transmittal::whereIn('id', $trans_ids)->update([
                    'route_no' => $updated_route,
                    'remarks' => 6
                ]);
                // Http::get('http://localhost/guaranteeletter/transmittal/returned/'.$pre->trans_id.'/'.Auth::user()->userid.'/dv');
                // Http::get('http://192.168.110.7/guaranteeletter/transmittal/returned/'.$pre->trans_id.'/'.Auth::user()->userid.'/dv');
                $token = $this->getToken();
                if ($token != 1) {
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $token
                    ])->get('http://192.168.110.7/guaranteeletter/api/transmittal/returned/'.$pre->trans_id.'/'.Auth::user()->userid.'/dv');            
                } else {
                    return "Authentication failed.";
                }
            }
        }
        return redirect()->back()->with('pre_dv', true);
    }

    public function getToken(){
        $loginResponse = Http::post('http://192.168.110.7/guaranteeletter/api/login', [
            'userid' => 2760
        ]);
        if (isset($loginResponse['token'])) {
            $token = $loginResponse['token'];
        } else {
            "Authentication failed. Error: " . ($loginResponse['message'] ?? 'Unknown error');
        }
        return $token;
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
            TrackingDetails::where('route_no', $route_no)->delete();
            TrackingMaster::where('route_no', $route_no)->delete();
            $transmittal = Transmittal::where('route_no', $route_no)->get();
            if($transmittal){
                foreach($transmittal as $row){
                    $row->route_no = null;
                    $row->remarks = 5;
                    $row->status = 5;
                    $row->save();
                }
            }
        }

        return redirect()->back()->with('pre_dv_remove', true);
    }

    public function angelica($route_no){
        $uts = Utilization::where('div_id', $route_no)->get();

        if($uts){
            foreach($uts as $u){
                $fund = Fundsource::where('id', $u->fundsource_id)->first();
                $u->budget_bbalance = $fund->remaining_balance;
                $u->budget_utilize = $u->utilize_amount;
                $u->obligated = 1;
                $u->obligated_by = '2147';
                $u->obligated_on = date('Y-m-d H:i:s');
                $u->save();
                
                $fund->remaining_balance = (float) str_replace(',','', $fund->remaining_balance) - (float) str_replace(',','', $u->utilize_amount);
                $fund->save();
            }
        }
    }

    public function processNew(Request $request){
        $new = NewDV::where('route_no', $request->new_dv_id)->first();
        $pre = PreDv::where('id', $new->predv_id)->first();
        $trans_ids = array_map('intval', explode(',', $pre->trans_id));
        $trans_ids = array_filter($trans_ids, function($value) {
            return $value !== 0 && $value !== ''; 
        });

        $type = $request->type;
        if($type == 'awaiting'){
            $new->ors_no = $request->ors_no;
            $new->obligated_by = Auth::user()->userid;
            $new->obligated_on = date('Y-m-d H:i:s', strtotime($request->obligated_on));
            $new->status = 1;
            $new->confirm = 1;
            $new->save();

            $uts = Utilization::where('div_id', $new->route_no)->get();

            if($uts){
                foreach($uts as $u){
                    $fund = Fundsource::where('id', $u->fundsource_id)->first();

                    $u->budget_bbalance = $fund->remaining_balance;
                    $u->budget_utilize = $u->utilize_amount;
                    $u->obligated = 1;
                    $u->obligated_by = Auth::user()->userid;
                    $u->obligated_on = date('Y-m-d H:i:s', strtotime($request->obligated_on));
                    $u->save();
                    
                    $fund->remaining_balance = (float) str_replace(',','', $fund->remaining_balance) - (float) str_replace(',','', $u->utilize_amount);
                    $fund->save();
                }
            }
            if(count($trans_ids) > 0){
                Transmittal::whereIn('id', $trans_ids)->update([
                    'remarks' => 7
                ]);

                // Http::get('http://localhost/guaranteeletter/transmittal/returned/'.$pre->trans_id.'/'.Auth::user()->userid.'/obligate');
                // Http::get('http://192.168.110.7/guaranteeletter/transmittal/returned/'.$pre->trans_id.'/'.Auth::user()->userid.'/obligate');

                $token = $this->getToken();
                if ($token != 1) {
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $token
                    ])->get('http://192.168.110.7/guaranteeletter/api/transmittal/returned/'.$pre->trans_id.'/'.Auth::user()->userid.'/obligate');            
                } else {
                    return "Authentication failed.";
                }
            }
           
            return redirect()->back()->with('pre_dv_update', true);
        }else if($type == 'deferred'){
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
            if(count($trans_ids) > 0){
                Transmittal::whereIn('id', $trans_ids)->update([
                    'remarks' => 8
                ]);

                // Http::get('http://localhost/guaranteeletter/transmittal/returned/'.$pre->trans_id.'/'.Auth::user()->userid.'/paid');
                // Http::get('http://192.168.110.7/guaranteeletter/transmittal/returned/'.$pre->trans_id.'/'.Auth::user()->userid.'/paid');
                $token = $this->getToken();
                if ($token != 1) {
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $token
                    ])->get('http://192.168.110.7/guaranteeletter/api/transmittal/returned/'.$pre->trans_id.'/'.Auth::user()->userid.'/paid');            
                } else {
                    return "Authentication failed.";
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
        $transmittal = Transmittal::where('remarks', 5)->where('facility_id', $facility_id)->get();
        return response()->json(['controls'=> array_merge($controls, $dv2), 'transmittal' => $transmittal]);
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

    public function dv2Remarks(Request $req){
        $dv2 = NewDV::where('route_no', $req->route_no)->first();
        if($dv2){
            $dv2->remarks = $req->text_remarks;
            $dv2->save();

            return redirect()->back()->with('update_remarks', true);
        }
    }
}
