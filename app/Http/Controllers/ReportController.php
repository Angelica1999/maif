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
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */


     public function reportSaa(){

        $fundsources = ProponentInfo::orderBy('fundsource_id', 'ASC')
            ->with([
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

        foreach($fundsources as $row) {
            $allocatedFunds = (float) str_replace(',', '', $row->alocated_funds);
            $remainingBalance = (float) str_replace(',', '', $row->remaining_balance);
            $adminCost = (float) str_replace(',', '', $row->admin_cost);
            
            // $utilizationTotal = $row->utilizations->first()->totalAmount ?? 0;
            $totalWithAdmin = $allocatedFunds - $remainingBalance;
            // $totalWithAdmin = $utilizationTotal + $adminCost;
            
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
                $row->proponent->proponent,
                $facilityName,
                str_replace(',','',$allocatedFunds),
                str_replace(',','',$totalWithAdmin),
                str_replace(',','',$remainingBalance),
                $utilizationRate . "%",
            ];
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $columnWidths = [
            'A' => 2, 'B' => 30, 'C' => 40, 'D' => 55,
            'E' => 20, 'F' => 30, 'G' => 20, 'H' => 20
        ];

        foreach($columnWidths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        $headers = [
            'B1' => ['text' => 'SAA', 'size' => 20, 'height' => 30],
            'B3' => ['text' => 'SAA NO'],
            'C3' => ['text' => 'PROPONENT'],
            'D3' => ['text' => 'FACILITY'],
            'E3' => ['text' => 'ALLOCATED FUNDS'],
            'F3' => ['text' => 'UTILIZATION (DV + Admin Cost)'],
            'G3' => ['text' => 'BALANCE'],
            'H3' => ['text' => 'UTILIZATION RATE'],
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
        $sheet->getStyle('B3:H3')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->fromArray($data, null, 'B4');

        $dataRange = 'B3:H' . (count($data) + 3);
        $numberRange = 'E4:G' . (count($data) + 3);

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

        $sheet->getStyle('B4:D' . (count($data) + 3))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('E4:H' . (count($data) + 3))
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
        //previous code
        // $spreadsheet = new Spreadsheet();
        // $sheet = $spreadsheet->getActiveSheet();

        // // Adjust column widths
        // $sheet->getColumnDimension('A')->setWidth(2);  
        // $sheet->getColumnDimension('B')->setWidth(30); 
        // $sheet->getColumnDimension('C')->setWidth(40); 
        // $sheet->getColumnDimension('D')->setWidth(55);
        // $sheet->getColumnDimension('E')->setWidth(20);
        // $sheet->getColumnDimension('F')->setWidth(30); 
        // $sheet->getColumnDimension('G')->setWidth(20); 
        // $sheet->getColumnDimension('H')->setWidth(20); 

        // $richText1 = new RichText();
        // $normalText = $richText1->createTextRun("SAA");
        // $normalText->getFont()->setBold(true)->setSize(20); 
        // $sheet->setCellValue('B1', $richText1);
        // $sheet->getRowDimension(1)->setRowHeight(30);
        // $sheet->getStyle('B1')->getAlignment()->setWrapText(true);

        // $richText1 = new RichText();
        // $normalText = $richText1->createTextRun("SAA NO");
        // $normalText->getFont()->setBold(true); 
        // $sheet->setCellValue('B3', $richText1);
        // $sheet->getStyle('B3')->getAlignment()->setWrapText(true);

        // $richText1 = new RichText();
        // $normalText = $richText1->createTextRun("PROPONENT");
        // $normalText->getFont()->setBold(true); 
        // $sheet->setCellValue('C3', $richText1);
        // $sheet->getStyle('C3')->getAlignment()->setWrapText(true);

        // $richText1 = new RichText();
        // $normalText = $richText1->createTextRun("FACILITY");
        // $normalText->getFont()->setBold(true); 
        // $sheet->setCellValue('D3', $richText1);
        // $sheet->getStyle('D3')->getAlignment()->setWrapText(true);

        // $richText1 = new RichText();
        // $normalText = $richText1->createTextRun("ALLOCATED FUNDS");
        // $normalText->getFont()->setBold(true); 
        // $sheet->setCellValue('E3', $richText1);
        // $sheet->getStyle('E3')->getAlignment()->setWrapText(true);

        // $richText1 = new RichText();
        // $normalText = $richText1->createTextRun("UTILIZATION (DV + Admin Cost)");
        // $normalText->getFont()->setBold(true); 
        // $sheet->setCellValue('F3', $richText1);
        // $sheet->getStyle('F3')->getAlignment()->setWrapText(true);

        // $richText1 = new RichText();
        // $normalText = $richText1->createTextRun("BALANCE");
        // $normalText->getFont()->setBold(true); 
        // $sheet->setCellValue('G3', $richText1);
        // $sheet->getStyle('G3')->getAlignment()->setWrapText(true);

        // $richText1 = new RichText();
        // $normalText = $richText1->createTextRun("UTILIZATION RATE");
        // $normalText->getFont()->setBold(true); 
        // $sheet->setCellValue('H3', $richText1);
        // $sheet->getStyle('H3')->getAlignment()->setWrapText(true);

        // $sheet->getStyle('B3:H3')
        //     ->getAlignment()
        //     ->setHorizontal(Alignment::HORIZONTAL_CENTER)
        //     ->setVertical(Alignment::VERTICAL_CENTER);
        // $sheet->getRowDimension(3)->setRowHeight(50); 

        // $fundsources = ProponentInfo::orderBy('fundsource_id', 'ASC')->with('facility:id,name', 'proponent:id,proponent', 'fundsource:id,saa')->get();

        // $data = [];
        // if($fundsources){

        //     foreach($fundsources as $row) {

        //         $saa = $row->fundsource->saa;
        //         $proponent = $row->proponent->proponent;
        //         $facility = $row->facility;
        //         $fundsource = number_format((double) str_replace(',', '',$row->alocated_funds), 2,'.',',');
        //         $rem = number_format((double) str_replace(',', '',$row->remaining_balance), 2,'.',',');
        //         $utilization = (double) str_replace(',', '',$row->alocated_funds) - (double) str_replace(',', '',$row->remaining_balance);
        //         $utilization = number_format((double) str_replace(',', '',$utilization), 2,'.',',');

        //         $total = Utilization::where('proponentinfo_id', $row->id)
        //                 ->where('status', 0)
        //                 ->select(DB::raw('SUM(REPLACE(utilize_amount, ",", "")) as totalAmount'))
        //                 ->first()->totalAmount;   
        //         $total_am = number_format($total + (double) str_replace(',', '',$row->admin_cost), 2,'.',',');
        //         if($rem == 0 ){
        //             $per = 100;
        //         }else if($utilization == 0){
        //             $per = 0;
        //         }else{
        //             $per = round((double) str_replace(',', '',$total_am) / (double) str_replace(',', '',$row->alocated_funds) * 100);
        //         }

        //         if($facility == null){
        //             $id = $row->facility_id;
        //             $array = json_decode($id, true);
        //             $int = array_map('intval', $array);
        //             $name = Facility::whereIn('id', $int)->pluck('name');
        //             $name = str_replace(['[', ']', '"'], '', $name); 
        //         }else{
        //             $name = $row->facility->name;
        //         }
                
        //         $data[] = [
        //             $saa,
        //             $proponent,
        //             $name,
        //             str_replace(',','',$fundsource),
        //             str_replace(',','',$utilization),
        //             str_replace(',','',$rem),
        //             $per."%",
        //         ];
        //     }
        // }else{
        // }

        // $sheet->fromArray($data, null, 'B4');
        // $sheet->getStyle('E4:G' . (count($data) + 3))
        // ->getNumberFormat()->setFormatCode('#,##0.00');

        // $styleArray = [
        //     'borders' => [
        //         'allBorders' => [
        //             'borderStyle' => Border::BORDER_THIN,
        //         ],
        //     ],
        //     'alignment' => [
        //         'vertical' => Alignment::VERTICAL_CENTER, 
        //     ],
        // ];
        
        // $sheet->getStyle('B3:H' . (count($data) + 3))->applyFromArray($styleArray);
        
        // $sheet->getStyle('B4:E' . (count($data) + 3))
        //     ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        
        // $sheet->getStyle('E4:H' . (count($data) + 3))
        //     ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        
        // // Output preparation
        // ob_start();
        // $writer = new Xlsx($spreadsheet);
        // $writer->save('php://output');
        // $xlsData = ob_get_contents();
        // ob_end_clean();

        // // Filename
        // $filename = 'SAA_Report_' . date('Ymd') . '.xlsx';

        // // Set headers
        // header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        // header("Content-Disposition: attachment; filename=$filename");
        // header("Pragma: no-cache");
        // header("Expires: 0");

        // // Output the file
        // return $xlsData;
        // exit;
    }
}
