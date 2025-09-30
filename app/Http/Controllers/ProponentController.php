<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Utilization;
use App\Models\Proponent;
use App\Models\Fundsource;
use App\Models\User;
use App\Models\Transfer;
use App\Models\Patients;
use App\Models\Facility;
use App\Models\AddFacilityInfo;
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

class ProponentController extends Controller
{
    public function __construct(){
       $this->middleware('auth');
       $this->middleware('block.secure.nonadmin');
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

    public function sendHold(Request $req){
        $proponents = Proponent::select(
                DB::raw('MAX(id) as id'), 
                DB::raw('MAX(proponent) as proponent'), 
                DB::raw('MAX(proponent_code) as proponent_code')
            )
            ->groupBy('proponent_code')
            ->whereNotNull('sent_status')
            ->orderBy('id', 'desc');
        
        $on_hold = Proponent::select(
                DB::raw('MAX(id) as id'), 
                DB::raw('MAX(proponent) as proponent'), 
                DB::raw('MAX(proponent_code) as proponent_code')
            )
            ->groupBy('proponent_code')
            ->whereNull('sent_status')
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

        return view('proponents.proponent_hold_send', [
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

    public function release($type, $code){
        if($type == 1 ){
            Proponent::where('proponent_code', $code)->update(['status' => null]);
        }else if($type == 2){
            AddFacilityInfo::where('facility_id', $code)->update(['sent_status' => null]);
        }
    }

    public function fundsource(Request $request)
    {
        try {
            $keyword = $request->viewAll ? [] : $request->data_filtering;
            $keyword = $keyword ? $keyword : [];
            $list = Proponent::whereIn('id', $keyword)->pluck('proponent')->toArray();

            $perPage = 51;
            $proponentGroups = Proponent::when($list, function ($query) use ($list) {
                    return $query->whereIn('proponent', $list);
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

            $sort = $request->sorting_btn ? $request->sorting_btn : 'desc';
            $sortDirection = $sort == 'desc' ? 'sortByDesc' : 'sortBy';

            if ($request->sorting_btn) {
                switch ($request->data_sorting) {
                    case 1: // Sort by Proponent Name
                        $allData = $allData->$sortDirection('proponent.name');
                        break;
                    case 2: // Sort by Allocated Funds
                        $allData = $allData->$sortDirection('sum');
                        break;
                    case 3: // Sort by GL Total
                        $allData = $allData->$sortDirection('totalUtilized');
                        break;
                    case 4: // Sort by Disbursement Total
                        $allData = $allData->$sortDirection('disbursement');
                        break;
                    case 5: // Sort by Supplemental Funds
                        $allData = $allData->$sortDirection('supp');
                        break;
                    case 6: // Sort by Negative Amount
                        $allData = $allData->$sortDirection('sub');
                        break;
                    case 7: // Sort by Remaining Funds
                        $allData = $allData->$sortDirection('rem');
                        break;
                }
            }

            // return $allData;
            $paginatedData = new LengthAwarePaginator(
                $allData->forPage(LengthAwarePaginator::resolveCurrentPage(), $perPage),
                $allData->count(),
                $perPage,
                null,
                ['path' => LengthAwarePaginator::resolveCurrentPath()]
            );

            return view('proponents.fundsource', [
                'data' => $paginatedData,
                'keyword' => $keyword ? $keyword : [],
                'facilities' => Facility::select('id', 'name')->get(),
                'user' => Auth::user()->user_type,
                'proponents' => Proponent::select('id', 'proponent')->orderBy('proponent')->get()->groupBy('proponent'),
                'sort' => $sort == 'asc' ? 'desc' : 'asc',
                'filter_keyword' => $request->viewAll ? '' : $request->data_sorting
            ]);

        } catch (\Exception $e) {
            \Log::error('ProFunds Error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while processing the data. Please try again.');
        }
    }

    public function excelPSummary()
    {
        $proponentGroups = Proponent::select('id', 'proponent')
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
                'proponent' => $proponentName,
                'sum' => round($netFunds, 2) != 0 ? round($netFunds, 2) : '0.00',
                'totalUtilized' => round($totalUtilized, 2) != 0 ? round($totalUtilized, 2) : '0.00',
                'disbursement' => round($totalDv1Amount + $totalDv3Amount, 2) != 0 ? round($totalDv1Amount + $totalDv3Amount, 2) : '0.00',
                'supp' => round($supp, 2) != 0 ? round($supp, 2) : '0.00',
                'sub' => round($sub, 2) != 0 ? round($sub, 2) : '0.00',
                'rem' => round($finalRemaining - $totalDv3Amount , 2) != 0 ? round($finalRemaining - $totalDv3Amount , 2) : '0.00',
            ];

        })->values()->all();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Adjust column widths
        $sheet->getColumnDimension('A')->setWidth(50);  
        $sheet->getColumnDimension('B')->setWidth(30); 
        $sheet->getColumnDimension('C')->setWidth(30); 
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(30);
        $sheet->getColumnDimension('G')->setWidth(30);

        $sheet->getStyle('A1:G1')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("PROPONENT");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('A1', $richText1);
        $sheet->getStyle('A1')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("ALLOCATED FUNDS");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('B1', $richText1);
        $sheet->getStyle('B1')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("GL TOTAL");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('C1', $richText1);
        $sheet->getStyle('C1')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("DISBURSEMENT TOTAL");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('D1', $richText1);
        $sheet->getStyle('D1')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("SUPPLEMENTAL FUNDS");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('E1', $richText1);
        $sheet->getStyle('E1')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("NEGATIVE AMOUNT");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('F1', $richText1);
        $sheet->getStyle('F1')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("REMAINING FUNDS");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('G1', $richText1);
        $sheet->getStyle('G1')->getAlignment()->setWrapText(true);

        $data = $allData;
        $sheet->fromArray($data, null, 'A2');
        $sheet->getStyle('B2:G' . (count($data) + 2))
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
        
        $sheet->getStyle('A2:G' . (count($data) + 1))->applyFromArray($styleArray);
        $sheet->getStyle('A2:G' . (count($data) + 1))->getAlignment()->setWrapText(true);

        $sheet->getStyle('A2:A' . (count($data) + 1))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        
        $sheet->getStyle('B2:G' . (count($data) + 1))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            
        // Output preparation
        ob_start();
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();

        // Filename
        $filename = "Proponent Summary" . date('Ymd') . ".xlsx";
        // Set headers
        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=$filename");
        header("Pragma: no-cache");
        header("Expires: 0");

        // Output the file
        return $xlsData;
        exit;

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
        $code = urldecode($code);
        $code = str_replace('$', '/', $code);        
        $ids = Proponent::where('proponent', $code)->pluck('id')->toArray();

        $filter_patients = Patients::whereIn('proponent_id', $ids)
            ->orderBy('id', 'desc')
            ->get()
            ->groupBy(function ($patient) {
                return $patient->fname . '|' . $patient->lname . '|' . $patient->mname;
            })
            ->map(function ($group) {
                return $group->first(); // Select only the first record in each group
            });
    
        $tracking = Patients::whereIn('proponent_id', $ids)->with('facility:id,name','encoded_by:userid,fname,lname,mname', 'gl_user:username,fname,lname')
            ->orderBy('id', 'asc')->paginate(20);
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

            $proponents = Proponent::selectRaw('MIN(id) as id, proponent')
                ->groupBy('proponent')
                ->get();

        if(count($tracking) > 0){
            return view('proponents.proponent_util',[
                'data' => $tracking,
                'facilities' => $facilities,
                'dv3' => $dv3_fundsources->orderBy('id', 'desc')->get(),
                'dv1' => $dv1,
                'proponents' => $proponents,
                'ids' => $tracking->whereNull('pro_used')->pluck('id')->toArray(),
                'filter_patients' => $filter_patients,
                'pat1' => 'none',
                'ret_id' => 'none',
                'sort_type' => 'desc'
            ]);
        }else{
            return 0;
        }
      
    }

    public function filterData(Request $request){
        $f_ids = $request->f_id;
        $patient_id = $request->patient_id;
        $pat1 = Patients::where('id',$patient_id)->select('fname', 'mname', 'lname')->first();
        $pro_code = $request->pro_code;
        $ids = Proponent::where('proponent', $pro_code)->pluck('id')->toArray();
        $info = ProponentInfo::whereIn('proponent_id', $ids)->pluck('id')->toArray();
        $filter_patients = Patients::whereIn('proponent_id', $ids)
            ->orderBy('id', 'desc')
            ->get()
            ->groupBy(function ($patient) {
                return $patient->fname . '|' . $patient->lname . '|' . $patient->mname;
            })
            ->map(function ($group) {
                return $group->first(); // Select only the first record in each group
            });
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

        $f_ids = is_array($f_ids) ? $f_ids : explode(',', $f_ids); 

        if (in_array("all", $f_ids)) {
            $query = Patients::whereIn('proponent_id', $ids);
            $ret_id = 0;
        } else {
            $query = Patients::whereIn('proponent_id', $ids)->whereIn('facility_id', $f_ids);
            $ret_id = $f_ids;
        }

        $query->with('facility:id,name', 'encoded_by:userid,fname,lname,mname', 'gl_user:username,fname,lname');

        if ($patient_id !== null) {
            if ($patient_id !== "all" && isset($pat1)) {
                $query->where('fname', $pat1->fname)
                    ->where('mname', $pat1->mname)
                    ->where('lname', $pat1->lname);
            }
        }

        $tracking = $query->orderBy('id', 'asc')->paginate(20);

        
        $facilities = Facility::whereIn('id', Patients::whereIn('proponent_id', $ids)->pluck('facility_id')->toArray())->select('id', 'name')->get(); 
        $proponents = Proponent::selectRaw('MIN(id) as id, proponent')
                ->groupBy('proponent')
                ->get();
        return view('proponents.proponent_util',[
            'data' => $tracking,
            'proponents' => $proponents,
            'ret_id' => $ret_id,
            'facilities' => $facilities,
            'dv3' => $dv3_fundsources->orderBy('id', 'desc')->get(),
            'dv1' => $dv1,
            'ids' => $tracking->whereNull('pro_used')->pluck('id')->toArray(),
            'filter_patients' => $filter_patients,
            'pat1' => $patient_id == "all" ? 'none' :$pat1,
            'sort_type' => 'desc'

        ]);
    }

    public function sortData(Request $request){
        $f_ids = $request->f_id;
        $patient_id = $request->patient_id;
        $pat1 = Patients::where('id',$patient_id)->select('fname', 'mname', 'lname')->first();
        $sort_type = $request->sort_type;
        $pro_code = $request->pro_code;
        $ids = Proponent::where('proponent', $pro_code)->pluck('id')->toArray();
        $info = ProponentInfo::whereIn('proponent_id', $ids)->pluck('id')->toArray();

        $filter_patients = Patients::whereIn('proponent_id', $ids)
            ->orderBy('id', 'desc')
            ->get()
            ->groupBy(function ($patient) {
                return $patient->fname . '|' . $patient->lname . '|' . $patient->mname;
            })
            ->map(function ($group) {
                return $group->first(); // Select only the first record in each group
            });
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

            $f_ids = is_array($f_ids) ? $f_ids : explode(',', $f_ids); 

            if (in_array("all", $f_ids)) {
                $query = Patients::whereIn('proponent_id', $ids);
                $ret_id = 0;
            } else {
                $query = Patients::whereIn('proponent_id', $ids)->whereIn('facility_id', $f_ids);
                $ret_id = $f_ids;
            }

            $query->with('facility:id,name', 'encoded_by:userid,fname,lname,mname', 'gl_user:username,fname,lname');

            if ($patient_id !== null) {
                if ($patient_id !== "all" && isset($pat1)) {
                    $query->where('fname', $pat1->fname)
                        ->where('mname', $pat1->mname)
                        ->where('lname', $pat1->lname);
                }
            }

            $tracking = $query->orderBy('lname', $sort_type)->paginate(20);

        
        $facilities = Facility::whereIn('id', Patients::whereIn('proponent_id', $ids)->pluck('facility_id')->toArray())->select('id', 'name')->get(); 
        $proponents = Proponent::selectRaw('MIN(id) as id, proponent')
                ->groupBy('proponent')
                ->get();
        return view('proponents.proponent_util',[
            'data' => $tracking,
            'proponents' => $proponents,
            'ret_id' => $ret_id,
            'facilities' => $facilities,
            'dv3' => $dv3_fundsources->orderBy('id', 'desc')->get(),
            'dv1' => $dv1,
            'ids' => $tracking->whereNull('pro_used')->pluck('id')->toArray(),
            'filter_patients' => $filter_patients,
            'pat1' => $patient_id == "all" ? 'none' :$pat1,
            'sort_type' => $sort_type

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

    public function delSup($id){
        SupplementalFunds::where('id', $id)->delete();
        return true;
    }

    public function delSub($id){
        SubtractedFunds::where('id', $id)->delete();
        return true;
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

    public function excelData($code, $ids, $patient_id){
        $pro = Proponent::where('proponent', $code)->get();
        $id = $pro->pluck('id')->toArray();
        $pat1 = Patients::where('id', $patient_id)->select('fname','mname','lname')->first();
        $ids = array_map('intval', explode(',', $ids)); 

        if ($ids == 0 || (is_array($ids) && in_array(0, $ids))) {
            $query = Patients::whereIn('proponent_id', $id);
        }else{
            $query = Patients::whereIn('proponent_id', $id)
            ->whereIn('facility_id', $ids);
        }

        $query->with('facility:id,name', 'encoded_by:userid,fname,lname,mname', 'gl_user:username,fname,lname');

        if ($patient_id != 0) {
            if ($patient_id !== "all" && isset($pat1)) {
                $query->where('fname', $pat1->fname)
                    ->where('mname', $pat1->mname)
                    ->where('lname', $pat1->lname);
            }
        }

        $patients = $query->orderBy('id', 'asc')->get();
        $title = $pro[0]->proponent;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Adjust column widths
        $sheet->getColumnDimension('A')->setWidth(2);  
        $sheet->getColumnDimension('B')->setWidth(50); 
        $sheet->getColumnDimension('C')->setWidth(30); 
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(60); 
        $sheet->getColumnDimension('G')->setWidth(30); 
        $sheet->getColumnDimension('H')->setWidth(20);  
        $sheet->getColumnDimension('I')->setWidth(50);  

        $sheet->mergeCells("B1:D1");
        $richText1 = new RichText();
        $normalText = $richText1->createTextRun($title);
        $normalText->getFont()->setBold(true)->setSize(20); 
        $sheet->setCellValue('B1', $richText1);
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getStyle('B1')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("PATIENT CODE");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('B3', $richText1);
        $sheet->getStyle('B3')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("NAME");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('C3', $richText1);
        $sheet->getStyle('C3')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("GUARANTEED AMOUNT");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('D3', $richText1);
        $sheet->getStyle('D3')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("ACTUAL AMOUNT");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('E3', $richText1);
        $sheet->getStyle('E3')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("FACILITY");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('F3', $richText1);
        $sheet->getStyle('F3')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("CREATED BY");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('G3', $richText1);
        $sheet->getStyle('G3')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("CREATED ON");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('H3', $richText1);
        $sheet->getStyle('H3')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("REMARKS");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('I3', $richText1);
        $sheet->getStyle('I3')->getAlignment()->setWrapText(true);

        $sheet->getStyle('B3:I3')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(3)->setRowHeight(50); 

        $data = [];

        if(count($patients) > 0){
            foreach($patients as $row){
                $name = $row->lname .', '.$row->fname.' '.$row->mname;
                $guaranteed = str_replace(',','',$row->guaranteed_amount);
                $actual = str_replace(',','',$row->actual_amount);
                $facility = $row->facility->name;
                $user = !Empty($row->encoded_by) ? $row->encoded_by->lname .', '.$row->encoded_by->fname : 
                        (!Empty($row->gl_user) ? $row->gl_user->lname.', '.$row->gl_user->fname : '');
                $on = date('F j, Y', strtotime($row->created_at));
                $data [] = [
                    $row->patient_code,
                    $name,
                    $guaranteed,
                    $actual,
                    $facility,
                    $user,
                    $on,
                    $row->pat_rem
                ];
            }
        }else{
            // $table_body .= "<tr>
            //     <td colspan=7 style='vertical-align:top;'>No Data Available</td>
            // </tr>";
        }
        // $display =
        //     '<h1>'.$title.'</h1>'.
        //     '<table cellspacing="1" cellpadding="5" border="1">'.$table_body.'</table>';

        // return $display;

        $sheet->fromArray($data, null, 'B4');
        $sheet->getStyle('D4:E' . (count($data) + 3))
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
        
        $sheet->getStyle('B3:I' . (count($data) + 3))->applyFromArray($styleArray);
        $sheet->getStyle('B4:I' . (count($data) + 3))->getAlignment()->setWrapText(true);

        $sheet->getStyle('B4:C' . (count($data) + 3))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        
        $sheet->getStyle('D4:E' . (count($data) + 3))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $sheet->getStyle('F4:H' . (count($data) + 3))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            
        // Output preparation
        ob_start();
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();

        // Filename
        $filename = $title . date('Ymd') . '.xlsx';

        // Set headers
        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=$filename");
        header("Pragma: no-cache");
        header("Expires: 0");

        // Output the file
        return $xlsData;
        exit;
    }
    
}
