<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patients;
use App\Models\Facility;
use App\Models\Province;
use App\Models\Muncity;
use App\Models\Barangay;
use App\Models\Fundsource;
use App\Models\Proponent;
use App\Models\Group;
use App\Models\ProponentInfo;
use App\Models\User;
use App\Models\Transfer;
use App\Models\Dv;
use App\Models\Utilization;
use App\Models\TrackingDetails;
use App\Models\PatientLogs;
use App\Models\MailHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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

class ReportController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('block.secure.nonadmin');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */


     public function reportSaa(){

        $fundsources = ProponentInfo::orderBy('fundsource_id', 'ASC')
            ->with([
                'main_pro:id,proponent',
                'facility:id,name', 
                'proponent:id,proponent', 
                'fundsource:id,saa',
                'utilizations' => function($query) {
                    $query->where('status', 0)
                        ->select('proponentinfo_id', DB::raw('SUM(REPLACE(utilize_amount, ",", "")) as totalAmount'))
                        ->groupBy('proponentinfo_id');
                }
            ])
            ->get();
        $data = [];
        $facilityCache = []; 
        $transfers = Utilization::where('status', 3)->whereHas('transfer', function ($q) {
            $q->where('owed', 1);
        })->get();

        foreach ($fundsources as $row) {
            $transfer = $transfers
                ->where('proponentinfo_id', $row->id)
                ->sum(function ($item) {
                    return (float) str_replace(',', '', $item->utilize_amount);
                });
            $allocatedFunds = (float) str_replace(',', '', $row->alocated_funds);
            $remainingBalance = (float) str_replace(',', '', $row->remaining_balance);
            $adminCost = (float) str_replace(',', '', $row->admin_cost);
            
            $totalWithAdmin = $allocatedFunds - $remainingBalance;
            
            $utilizationRate = $allocatedFunds > 0 ? round(($totalWithAdmin / $allocatedFunds) * 100) : 0;
            
            if($row->facility == null) {
                $facilityIds = json_decode($row->facility_id, true);
                $cacheKey = implode(',', $facilityIds);
                
                if (!isset($facilityCache[$cacheKey])) {
                    $facilityCache[$cacheKey] = Facility::whereIn('id', $facilityIds)
                        ->pluck('name')
                        ->implode(', ');
                }
                $facilityName = $facilityCache[$cacheKey];
            } else {
                $facilityName = $row->facility->name;
            }
            
            $data[] = [
                $row->fundsource->saa,
                $row->main_pro ? $row->main_pro->proponent: '',
                $row->proponent->proponent,
                $facilityName,
                str_replace(',','',$allocatedFunds),
                $transfer,
                str_replace(',','',$totalWithAdmin),
                str_replace(',','',$remainingBalance),
                $utilizationRate . "%",
            ];
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $columnWidths = [
            'A' => 2, 'B' => 30,  'C' => 40, 'D' => 40, 
            'E' => 55, 'F' => 20, 'G' => 20, 'H' => 30, 
            'I' => 20, 'J' => 20
        ];

        foreach($columnWidths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        $headers = [
            'B1' => ['text' => 'SAA', 'size' => 20, 'height' => 30],
            'B3' => ['text' => 'SAA NO'],
            'C3' => ['text' => 'PRINCIPAL'],
            'D3' => ['text' => 'PROPONENT'],
            'E3' => ['text' => 'FACILITY'],
            'F3' => ['text' => 'ALLOCATED FUNDS'],
            'G3' => ['text' => 'PAYABLES TO DOH'],
            'H3' => ['text' => 'UTILIZATION (DV + Admin Cost)'],
            'I3' => ['text' => 'BALANCE'],
            'J3' => ['text' => 'UTILIZATION RATE'],
        ];

        foreach($headers as $cell => $config) {
            $richText = new RichText();
            $textRun = $richText->createTextRun($config['text']);
            $font = $textRun->getFont()->setBold(true);
            
            if(isset($config['size'])) {
                $font->setSize($config['size']);
            }
            
            $sheet->setCellValue($cell, $richText);
            $sheet->getStyle($cell)->getAlignment()->setWrapText(true);
            
            if(isset($config['height'])) {
                $sheet->getRowDimension(substr($cell, -1))->setRowHeight($config['height']);
            }
        }

        $sheet->getRowDimension(3)->setRowHeight(50);
        $sheet->getStyle('B3:J3')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->fromArray($data, null, 'B4');

        $dataRange = 'B3:J' . (count($data) + 3);
        $numberRange = 'F4:H' . (count($data) + 3);

        $sheet->getStyle($numberRange)->getNumberFormat()->setFormatCode('#,##0.00');

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

        $sheet->getStyle($dataRange)->applyFromArray($styleArray);

        $sheet->getStyle('B4:E' . (count($data) + 3))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('F4:J' . (count($data) + 3))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $filename = 'SAA_Report_' . date('Ymd') . '.xlsx';

        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=$filename");
        header("Pragma: no-cache");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        exit;
    }
}
