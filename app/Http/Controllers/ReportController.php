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
        
        // if(Auth::user()->userid == 2760){
        //     $info = ProponentInfo::get();
        //     foreach($info as $item){
        //         $get = Utilization::where('proponentinfo_id', $item->id)->first();
        //         $check = (float)str_replace(',','', $item->alocated_funds)- (float)(($item->admin_cost != null)?(float)str_replace(',','', $item->admin_cost):0);
        //         // return $get;
        //         if($get){
        //             $bal = ($get->beginning_balance != null )? (float)str_replace(',','', $get->beginning_balance):0;
        //             if($check != $bal && !in_array($item->id, [56, 57,176,181,186, 194,291,299,332,333,334,335, 369,393,394, 406, 427,432,433,434,435,437,
        //             438,439,440,441,447,448,449,450,451,452,454,455,456,457,458,472, 481,482,506,507,509,510,520,521,522,537,543,548,549,550,551,590,
        //             595,601,602,603,604,605,606,607,608,609,632,636,645,646,647,648,649,666,667,668,669,690])){
        //                 // return $get->beginning_balance;
        //                 // return $check; 2,493,092.02
        //                 return $item->id;
        //             }
        //         }else{
        //             // return 'else';
        //             // return $get; 359686.09
        //         }
               
        //     }
        // }
        // $info = Utilization::where('proponentinfo_id',181)->where('status', 0)->get();
        // $bal = 150000;

        // // return $info;
        // foreach($info as $info){
        //     $info->beginning_balance = $bal;
        //     $info->save();
        //     $bal =  $info->beginning_balance - $info->utilize_amount ;
        // }
       
        // $inf = ProponentInfo::where('id', 181)->first();
        // $inf->remaining_balance = number_format((double) str_replace(',', '',$bal), 2,'.',',');
        // $inf->save();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Adjust column widths
        $sheet->getColumnDimension('A')->setWidth(2);  
        $sheet->getColumnDimension('B')->setWidth(30); 
        $sheet->getColumnDimension('C')->setWidth(40); 
        $sheet->getColumnDimension('D')->setWidth(55);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(30); 
        $sheet->getColumnDimension('G')->setWidth(20); 
        $sheet->getColumnDimension('H')->setWidth(20); 

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("SAA");
        $normalText->getFont()->setBold(true)->setSize(20); 
        $sheet->setCellValue('B1', $richText1);
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getStyle('B1')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("SAA NO");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('B3', $richText1);
        $sheet->getStyle('B3')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("PROPONENT");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('C3', $richText1);
        $sheet->getStyle('C3')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("FACILITY");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('D3', $richText1);
        $sheet->getStyle('D3')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("ALLOCATED FUNDS");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('E3', $richText1);
        $sheet->getStyle('E3')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("UTILIZATION (DV + Admin Cost)");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('F3', $richText1);
        $sheet->getStyle('F3')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("BALANCE");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('G3', $richText1);
        $sheet->getStyle('G3')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("UTILIZATION RATE");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('H3', $richText1);
        $sheet->getStyle('H3')->getAlignment()->setWrapText(true);

        $sheet->getStyle('B3:H3')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(3)->setRowHeight(50); 

        $fundsources = ProponentInfo::orderBy('fundsource_id', 'ASC')->with('facility', 'proponent', 'fundsource')->get();
        $data = [];
        if($fundsources){

            foreach($fundsources as $row) {

                $saa = $row->fundsource->saa;
                $proponent = $row->proponent->proponent;
                $facility = $row->facility;
                $fundsource = number_format((double) str_replace(',', '',$row->alocated_funds), 2,'.',',');
                $rem = number_format((double) str_replace(',', '',$row->remaining_balance), 2,'.',',');
                $utilization = (double) str_replace(',', '',$row->alocated_funds) - (double) str_replace(',', '',$row->remaining_balance);
                $utilization = number_format((double) str_replace(',', '',$utilization), 2,'.',',');

                $total = Utilization::where('proponentinfo_id', $row->id)
                        ->where('status', 0)
                        ->select(DB::raw('SUM(REPLACE(utilize_amount, ",", "")) as totalAmount'))
                        ->first()->totalAmount;   
                $total_am = number_format($total + (double) str_replace(',', '',$row->admin_cost), 2,'.',',');
                if($rem == 0 ){
                    $per = 100;
                }else if($utilization == 0){
                    $per = 0;
                }else{
                    $per = round((double) str_replace(',', '',$total_am) / (double) str_replace(',', '',$row->alocated_funds) * 100);
                }

                if($facility == null){
                    $id = $row->facility_id;
                    $array = json_decode($id, true);
                    $int = array_map('intval', $array);
                    $name = Facility::whereIn('id', $int)->pluck('name');
                    $name = str_replace(['[', ']', '"'], '', $name); 
                }else{
                    $name = $row->facility->name;
                }
                
                $data[] = [
                    $saa,
                    $proponent,
                    $name,
                    $fundsource,
                    $utilization,
                    $rem,
                    $per."%",
                ];
            }
        }else{
        }

        $sheet->fromArray($data, null, 'B4');

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
        
        $sheet->getStyle('B3:H' . (count($data) + 3))->applyFromArray($styleArray);
        
        $sheet->getStyle('B4:E' . (count($data) + 3))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        
        $sheet->getStyle('E4:H' . (count($data) + 3))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        
        // Output preparation
        ob_start();
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();

        // Filename
        $filename = 'SAA_Report_' . date('Ymd') . '.xlsx';

        // Set headers
        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=$filename");
        header("Pragma: no-cache");
        header("Expires: 0");

        // Output the file
        return $xlsData;
        exit;

        //excel file before

        $fundsources = ProponentInfo::orderBy('fundsource_id', 'ASC')->with('facility', 'proponent', 'fundsource')->get();
            
        $filename = 'SAA.xls';
        header("Content-Type: application/xls");
        header("Content-Disposition: attachment; filename=$filename");
        header("Pragma: no-cache");
        header("Expires: 0");
        $table_body = "<tr>
                <th>SAA NUMBER</th>
                <th>PROPONENT</th>
                <th>FACILITY</th>
                <th>ALLOCATED FUNDS</th>
                <th>UTILIZATION (DV + Admin cost)</th>
                <th>BALANCE</th>
                <th>UTILIZATION RATE</th>
            </tr>";
      
        if($fundsources){

            foreach($fundsources as $row) {

                $saa = $row->fundsource->saa;
                $proponent = $row->proponent->proponent;
                $facility = $row->facility;
                $fundsource = number_format((double) str_replace(',', '',$row->alocated_funds), 2,'.',',');
                $rem = number_format((double) str_replace(',', '',$row->remaining_balance), 2,'.',',');
                $utilization = (double) str_replace(',', '',$row->alocated_funds) - (double) str_replace(',', '',$row->remaining_balance);
                $utilization = number_format((double) str_replace(',', '',$utilization), 2,'.',',');

                $total = Utilization::where('proponentinfo_id', $row->id)
                        ->where('status', 0)
                        ->select(DB::raw('SUM(REPLACE(utilize_amount, ",", "")) as totalAmount'))
                        ->first()->totalAmount;   
                $total_am = number_format($total + (double) str_replace(',', '',$row->admin_cost), 2,'.',',');
                if($rem == 0 ){
                    $per = 100;
                }else if($utilization == 0){
                    $per = 0;
                }else{
                    $per = round((double) str_replace(',', '',$total_am) / (double) str_replace(',', '',$row->alocated_funds) * 100);
                }

                if($facility == null){
                    $id = $row->facility_id;
                    $array = json_decode($id, true);
                    $int = array_map('intval', $array);
                    $name = Facility::whereIn('id', $int)->pluck('name');
                    $name = str_replace(['[', ']', '"'], '', $name); 
                }else{
                    $name = $row->facility->name;
                }
                $table_body .= "<tr>
                    <td style='vertical-align:top;'>$saa</td>
                    <td style='vertical-align:top;'>$proponent</td>
                    <td style='vertical-align:top;'>$name</td>
                    <td style='vertical-align:top;'>$fundsource</td>
                    <td style='vertical-align:top;'>$utilization</td>
                    <td style='vertical-align:top;'>$rem</td>
                    <td style='vertical-align:top;'>$per%</td>
                </tr>";

            }
        }else{
            $table_body .= "<tr>
                <td colspan=6 style='vertical-align:top;'>No Data Available</td>
            </tr>";
        }
        $display =
            '<h1>SAA</h1>'.
            '<table cellspacing="1" cellpadding="5" border="1">'.$table_body.'</table>';

        return $display;
    }
}
