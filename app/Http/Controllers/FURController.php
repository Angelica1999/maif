<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Patients;
use App\Models\Facility;
use App\Models\Transmittal;
use App\Models\TransmittalDetails;
use App\Models\TransmittalPatients;
use App\Models\ProponentUtilization;
use App\Models\AnnexB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
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

class FURController extends Controller
{
    //

    public function incomingFUR(Request $request){
        // $year = $request->year ? $request->year : now()->year; 

        $data = AnnexB::join('transmittal_patients', 'annex_b.patient_id', '=', 'transmittal_patients.patient_id')
            ->join('facility', 'annex_b.facility_id', '=', 'facility.id')
            ->where('annex_b.status', '!=', 0)
            ->groupByRaw('YEAR(annex_b.month_year), MONTH(annex_b.month_year)')
            ->selectRaw('
                YEAR(annex_b.month_year) as year,
                MONTH(annex_b.month_year) as month,
                MAX(annex_b.facility_id) as facility_id,
                MAX(facility.name) as name,
                MAX(annex_b.status) as status,
                MAX(annex_b.remarks) as remarks,
                COUNT(DISTINCT annex_b.patient_id) as patients,
                SUM(transmittal_patients.total) as total,
                MAX(annex_b.updated_at) as last_update
            ');

        if($request->viewAll){
            $request->year = '';
            $request->type = '';
        }
        
        if($request->type && $request->type != 'all'){
            $data->where('annex_b.status', $request->type);
        }

        if($request->year){
            $data->whereYear('annex_b.month_year', $request->year);
        }

        if($request->facility_id){
            $month = $request->month;
            $year = $request->year;
            $facility_id = $request->facility_id;
            $monthNumber = str_pad($month, 2, '0', STR_PAD_LEFT);
            $data = AnnexB::whereMonth('month_year', $monthNumber)
                ->whereYear('month_year', $year)
                ->where('facility_id', $facility_id)
                ->with([
                    'trans',
                    'patient'
                ]);

            if ($request->data_type) {

                $type = $request->data_type;
            
                if ($type == 3) {

                    $data->where(function ($q) {
                        $q->where('opd', 1);
                    });

                } elseif ($type == 2) { 

                    $data->where(function ($q) {
                        $q->where('excess', '!=', 1)->where('opd', 0);
                    });
                }elseif ($type == 1) {

                    $data->where(function ($q) {
                        $q->where('excess', 1)->where('opd', 0);
                    });
                }
            }        
                
            $keyword = $request->keyword;

            if($request->viewAll){
                $keyword = '';
            }

            if($keyword){
                $data->whereHas('patient', function ($query) use ($keyword) {
                    $query->where('lname', 'LIKE', "%{$keyword}%")
                        ->orWhere('fname', 'LIKE', "%{$keyword}%")
                        ->orWhere('mname', 'LIKE', "%{$keyword}%");
                });
            }
            return view('facility.annex_b_view',[
                'data' => $data->paginate(50),
                'keyword' => $keyword,
                'type' => $request->data_type,
                'month' => $month,
                'year' => $year,
                'facility_id' => $facility_id
            ]);
        }

        return view('facility.fur_annex_b',[
            'data' => $data->orderBy('year', 'asc')
                ->orderBy('month', 'asc')
                ->paginate(50),
            'type' => $request->type ?? '',
            'year' => $request->year ?? '',
        ]);
    }

    public function annexAExcel($id, $year){

        $facility_id = $id;

        $data = AnnexB::join('transmittal_patients', 'annex_b.patient_id', '=', 'transmittal_patients.patient_id')
            ->where('annex_b.facility_id', $facility_id)
            ->whereYear('annex_b.month_year', $year)
            ->groupByRaw('MONTH(annex_b.month_year)')
            ->selectRaw('
                MONTH(annex_b.month_year) as month,
                COUNT(DISTINCT annex_b.patient_id) as patients,
                SUM(transmittal_patients.total) as total
            ')
            ->get();

        $allMonths = [];

        for ($m = 1; $m <= 12; $m++) {
            $monthData = $data->firstWhere('month', $m);

            $allMonths[] = [
                'month' => Carbon::create()->month($m)->format('F'), 
                'patients' => $monthData->patients ?? 0,
                'total' => (float) ($monthData->total ?? 0)
            ];
        }

        $overallPatients = collect($allMonths)->sum('patients');
        $overallTotal    = collect($allMonths)->sum('total');

        $result = [
            'year' => $year,
            'monthly' => $allMonths,
            'overall' => [
                'patients' => $overallPatients,
                'total' => $overallTotal
            ]
        ];

        $facility = Facility::where('id', $facility_id)->value('name');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getColumnDimension('A')->setWidth(2);  
        $sheet->getColumnDimension('B')->setWidth(40); 
        $sheet->getColumnDimension('C')->setWidth(20); 
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20); 
        $sheet->getColumnDimension('G')->setWidth(20); 
        $sheet->getColumnDimension('H')->setWidth(20); 

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('DOH Logo');
        $drawing->setDescription('Department of Health Logo');
        $drawing->setPath(public_path('images/doh-logo.png')); 

        $drawing->setWidth($drawing->getWidth() * 0.21);
        $drawing->setHeight($drawing->getHeight() * 0.21);

        $drawing->setCoordinates('B2');
        $drawing->setOffsetX(5);  
        $drawing->setOffsetY(10); 

        $drawing->setWorksheet($sheet);

        $richText = new RichText();

        $normalText1 = $richText->createTextRun("Republic of the Philippines\nDepartment of Health\n");

        $boldText = $richText->createTextRun("Medical Assistance to Indigent Patients (MAIP) Program - List of All Patients Served\n");
        $boldText->getFont()->setBold(true); 

        $italicText = $richText->createTextRun("As of ");
        $italicText->getFont()->setItalic(true); 
        
        $underlinedDate = $richText->createTextRun($year);
        $underlinedDate->getFont()->setUnderline(Font::UNDERLINE_SINGLE); 
        $sheet->setCellValue('B2', $richText);

        $sheet->mergeCells('B2:H2');
        $sheet->getStyle('B2')->getAlignment()->setWrapText(true);
        $sheet->getStyle('B2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B2')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(2)->setRowHeight(100); 

        $sheet->getStyle('B31:H32')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B31:H32')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $drawing1 = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing1->setName('Malasakit Logo');
        $drawing1->setDescription('MPU Logo');
        $drawing1->setPath(public_path('images/malasakit.png')); 

        $drawing1->setCoordinates('H2');
        $drawing1->setOffsetX(5); 
        $drawing1->setOffsetY(5); 

        $drawing1->setWorksheet($sheet);

        $sheet->setCellValue('B4', 'Name of Hospital:');
        $sheet->setCellValue('C4', $facility);
        $sheet->setCellValue('B5', 'Region:');
        $sheet->setCellValue('C5', 'VII');

        $range = "B9:H9"; 
        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);
        $sheet->getStyle($range)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('B7B7B7');

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("SAA No. and Date of Issuance of SAA");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('B9', $richText1);
        $sheet->getRowDimension(9)->setRowHeight(70); 
        $sheet->getStyle('B9')->getAlignment()->setWrapText(true);
        $sheet->getStyle('B9:H9')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("Amount of SAA");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('C9', $richText1);
        $sheet->getStyle('C9')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("Total Fund Allocation");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('D9', $richText1);
        $sheet->getStyle('D9')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("Month Utilized");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('E9', $richText1);
        $sheet->getStyle('E9')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("Total Number of Patients Served");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('F9', $richText1);
        $sheet->getStyle('F9')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("Total Actual Approved Assistance through MAIPP (Utilized Amount)");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('G9', $richText1);
        $sheet->getStyle('G9')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("Balance");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('H9', $richText1);
        $sheet->getStyle('H9')->getAlignment()->setWrapText(true);

        $annexa1 = [];

        foreach($result['monthly'] as $index=>$row){
            $annexa1[] = [
                '',
                '',
                '',
                $row['month'],
                $row['patients'],
                $row['total'],
                '',
            ];
        }

        $startRow = 10; 
        $sheet->fromArray($annexa1, null, "B" . $startRow);

        $lastRow = $startRow + count($annexa1) - 1;

        $sheet->getStyle('C10:D22')
            ->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('G10:H22')
            ->getNumberFormat()->setFormatCode('#,##0.00');

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];

        $rangeV3 = "B$startRow:H$lastRow";
        $sheet->getStyle($rangeV3)->applyFromArray($styleArray);

        for ($row = $startRow; $row <= $lastRow; $row++) {
            $mergeRange = "D10:D21";
            $sheet->mergeCells($mergeRange);
        }

        $sheet->setCellValue("B22", "TOTAL");
        $sheet->setCellValue("C22", '');
        $sheet->setCellValue("D22", '');
        $sheet->setCellValue("F22", $result['overall']['patients']);
        $sheet->setCellValue("G22", $result['overall']['total']);
        $sheet->setCellValue("H22", '');
        $sheet->getStyle("C10:H22")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("B10:B22")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->getStyle("C22")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE);
        $sheet->getStyle("F22:H22")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE);

        $sheet->setCellValue("B24", "Note:");
        $sheet->getStyle("B24")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $sheet->mergeCells("D24:F24");
        $sheet->setCellValue("D24", "*Put page numbers in the lower part of the file");
        $sheet->getStyle("D24")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue("D25", "*Affix initials per page of report except for the last page which includes complete signatories");
        $sheet->getStyle("D25")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->setCellValue("B28", "Prepared by:");
        $sheet->getStyle("B30")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
        $sheet->setCellValue("B31", "Signature Over Printed Name");
        $richText3 = new RichText();
        $normalText2 = $richText3->createTextRun("Designation");
        $sheet->setCellValue("B32", $richText3);
        $normalText2->getFont()->setBold(true);
        $sheet->getStyle("B32", $richText3)->getAlignment()->setWrapText(true);
        $sheet->setCellValue("B33", "Date:");

        $sheet->mergeCells("D28:E28");
        $sheet->setCellValue("D28", "Certified correct:");
        $sheet->mergeCells("D30:E30");
        $sheet->getStyle("D30:E30")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
        $sheet->mergeCells("D31:E31");
        $sheet->setCellValue("D31", "Signature Over Printed Name");
        $sheet->mergeCells("D32:E32");
        $richText3 = new RichText();
        $normalText2 = $richText3->createTextRun("Chief Accountant");
        $sheet->setCellValue("D32", $richText3);
        $normalText2->getFont()->setBold(true);
        $sheet->getStyle("D32")->getAlignment()->setWrapText(true);
        $sheet->mergeCells("D33:E33");
        $sheet->setCellValue("D33", "Date:");

        $sheet->mergeCells("G28:H28");
        $sheet->setCellValue("G28", "Approved by:");
        $sheet->mergeCells("G30:H30");
        $sheet->getStyle("G30:H30")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
        $sheet->mergeCells("G31:H31");
        $sheet->setCellValue("G31", "Signature Over Printed Name");
        $sheet->mergeCells("G32:H32");
        $richText3 = new RichText();
        $normalText2 = $richText3->createTextRun("Medical Center Chief");
        $sheet->setCellValue("G32", $richText3);
        $normalText2->getFont()->setBold(true);
        $sheet->getStyle("G32")->getAlignment()->setWrapText(true);
        $sheet->mergeCells("G33:H33");
        $sheet->setCellValue("G33", "Date:");
        
        // Output preparation
        ob_start();
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();

        // Filename
        $filename = 'Annex-A-'.$this->getAcronym($facility).'-'. $year . '.xlsx';

        // Set headers
        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=$filename");
        header("Pragma: no-cache");
        header("Expires: 0");
        
        // Output the file
        return $xlsData;
        exit;
    }

    private function getAcronym($str) {
        $words = explode(' ', $str); 
        $acronym = '';
        
        foreach ($words as $word) {
            $acronym .= strtoupper(substr($word, 0, 1)); 
        }
        
        return $acronym;
    }

    public function returnFUR(Request $request){
        AnnexB::whereMonth('month_year', $request->month)->whereYear('month_year', $request->year)->where('facility_id', $request->id)->update([
            'status' => 3,
            'remarks' => $request->remarks,
        ]);
    }

    public function acceptFUR(Request $request){
        AnnexB::whereMonth('month_year', $request->month)->whereYear('month_year', $request->year)->where('facility_id', $request->id)->update([
            'status' => 2,
        ]);
    }

    public function annexBExcel($id, $month, $year){
        $facility = Facility::where('id', $id)->value('name');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Adjust column widths
        $sheet->getColumnDimension('A')->setWidth(2);  
        $sheet->getColumnDimension('B')->setWidth(5); 
        $sheet->getColumnDimension('C')->setWidth(20); 
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(20); 
        $sheet->getColumnDimension('G')->setWidth(12); 
        $sheet->getColumnDimension('H')->setWidth(12); 
        $sheet->getColumnDimension('I')->setWidth(12); 
        $sheet->getColumnDimension('J')->setWidth(12); 
        $sheet->getColumnDimension('K')->setWidth(12); 
        $sheet->getColumnDimension('L')->setWidth(12); 
        $sheet->getColumnDimension('M')->setWidth(25);
        $sheet->getColumnDimension('N')->setWidth(20);
        $sheet->getColumnDimension('O')->setWidth(15);
        $sheet->getColumnDimension('P')->setWidth(15);
        $sheet->getColumnDimension('Q')->setWidth(15);
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('DOH Logo');
        $drawing->setDescription('Department of Health Logo');
        $drawing->setPath(public_path('images/doh-logo.png')); 

        $drawing->setWidth($drawing->getWidth() * 0.21);
        $drawing->setHeight($drawing->getHeight() * 0.21);

        $drawing->setCoordinates('D2');
        $drawing->setOffsetX(5);  
        $drawing->setOffsetY(10); 

        $drawing->setWorksheet($sheet);

        $richText = new RichText();

        $normalText1 = $richText->createTextRun("Republic of the Philippines\nDepartment of Health\n");

        $boldText = $richText->createTextRun("Medical Assistance to Indigent Patients (MAIP) Program - List of All Patients Served\n");
        $boldText->getFont()->setBold(true); 

        $italicText = $richText->createTextRun("For the Month of ");
        $italicText->getFont()->setItalic(true); 
        
        $underlinedDate = $richText->createTextRun(date('F', mktime(0, 0, 0, $month, 10)).' '.$year);
        $underlinedDate->getFont()->setUnderline(Font::UNDERLINE_SINGLE); 
        $sheet->setCellValue('C2', $richText);

        $sheet->mergeCells('C2:Q2');
        $sheet->getStyle('C2')->getAlignment()->setWrapText(true);
        $sheet->getStyle('C2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C2')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(2)->setRowHeight(80); 

        $drawing1 = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing1->setName('Malasakit Logo');
        $drawing1->setDescription('MPU Logo');
        $drawing1->setPath(public_path('images/malasakit.png')); 

        $drawing1->setCoordinates('P2');
        $drawing1->setOffsetX(5); 
        $drawing1->setOffsetY(5); 

        $drawing1->setWorksheet($sheet);

        $sheet->setCellValue('C4', 'Name of Hospital:');
        $sheet->setCellValue('D4', $facility);
        // $sheet->getStyle($sheet->calculateWorksheetDimension())->getFont()->setSize(12);

        $range = "B8:Q8"; 
        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);
        $sheet->getStyle($range)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('B7B7B7');

        $range2 = "B9:Q9"; 
        $sheet->getStyle($range2)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);
        $sheet->getStyle($range2)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('B7B7B7');

        $range3 = "B10:Q10"; 
        $sheet->getStyle($range3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);
        $sheet->getStyle($range3)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('B7B7B7');

        $range4 = "B11:Q11"; 
        $sheet->getStyle($range4)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);
        $sheet->getStyle($range4)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('B7B7B7');

        $sheet->setCellValue('C5', 'Region:');
        $sheet->setCellValue('D5', 'VII');

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("SERVICE WARD IN-PATIENTS");
        $normalText->getFont()->setBold(false); 

        $sheet->setCellValue('C8', $richText1);
        $sheet->getRowDimension(8)->setRowHeight(25); 

        $sheet->mergeCells('C8:Q8');

        $sheet->getStyle('C8')->getAlignment()->setWrapText(true);
        $sheet->getStyle('C8')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C8')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->mergeCells('B9:B11');
        $sheet->setCellValue('B9', 'No.');
        $sheet->getStyle('B9:B11')->getFont()->setBold(true);

        $sheet->mergeCells('C9:C11');
        $richText3 = new RichText();
        $normalText2 = $richText3->createTextRun("Name of Patient (Last Name, First Name, Middle Name)");
        $normalText2->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue('C9', $richText3);
        $sheet->getStyle('C9')->getAlignment()->setWrapText(true); 
        $sheet->getStyle($sheet->calculateWorksheetDimension())->getFont()->setSize(9);

        $sheet->mergeCells('D9:D11');
        $richText4 = new RichText();
        $normalText3 = $richText4->createTextRun("MAIP CODE (Generated from MAIS)");
        $normalText3->getFont()->setBold(true)->setSize(9);
        $sheet->setCellValue('D9', $richText4);

        $sheet->mergeCells('E9:E11');
        $richText6 = new RichText();
        $normalText5 = $richText6->createTextRun("Type of Medical Assistance Provided");
        $normalText5->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue('E9', $richText6);

        $sheet->mergeCells('F9:F10'); 
        $richText7 = new RichText();
        $normalText6 = $richText7->createTextRun("Total Actual Charges ");
        $normalText6->getFont()->setBold(true)->setSize(9);
        $normalText7 = $richText7->createTextRun("(without any medications)");
        $normalText7->getFont()->setBold(false)->setSize(9); 

        $sheet->setCellValue('F9', $richText7);
        $sheet->getStyle('F9')->getAlignment()->setWrapText(true);

        $sheet->mergeCells('G9:L9');
        $sheet->setCellValue('G9', 'Hospital Bill/Medical Assistance');
        $sheet->getStyle('G9')->getFont()->setBold(true)->setSize(9);
        $sheet->getRowDimension(9)->setRowHeight(45); 

        $sheet->getStyle('F9:G9')->getAlignment()->setWrapText(true);
        $sheet->getStyle('F9:G9')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F9:G9')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('F11', 'A');
        $sheet->getStyle('F11')->getFont()->setBold(true)->setSize(9);

        $richText8 = new RichText();
        $normalText8 = $richText8->createTextRun("Senior Citizen/PWD");
        $normalText8->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue('G10', $richText8);
        $sheet->getStyle('G10')->getAlignment()->setWrapText(true);

        $richText9 = new RichText();
        $normalText9 = $richText9->createTextRun("PHILHEALTH (case rate)");
        $normalText9->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue('H10', $richText9);
        $sheet->getStyle('H10')->getAlignment()->setWrapText(true);

        $sheet->setCellValue('I10', 'PCSO');
        $sheet->getStyle('I10')->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle('I10')->getAlignment()->setIndent(1); 

        $sheet->setCellValue('J10', 'DSWD');
        $sheet->getStyle('J10')->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle('J10')->getAlignment()->setIndent(1); 

        $sheet->mergeCells('K10:L10');
        $richText10 = new RichText();
        $normalText10 = $richText10->createTextRun("Others (please specify)");
        $normalText10->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue('K10', $richText10);
        $sheet->getStyle('K10')->getAlignment()->setWrapText(true);

        $sheet->getStyle('G10:L10')->getAlignment()->setWrapText(true);
        $sheet->getStyle('G10:L10')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G10:L10')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->getStyle('F11:L11')->getAlignment()->setWrapText(true);
        $sheet->getStyle('F11:L11')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F11:L11')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('G11', 'B');
        $sheet->getStyle('G11')->getFont()->setBold(true)->setSize(9);

        $sheet->setCellValue('H11', 'C');
        $sheet->getStyle('H11')->getFont()->setBold(true)->setSize(9);

        $sheet->setCellValue('I11', 'D');
        $sheet->getStyle('I11')->getFont()->setBold(true)->setSize(9);

        $sheet->setCellValue('J11', 'E');
        $sheet->getStyle('J11')->getFont()->setBold(true)->setSize(9);

        $sheet->mergeCells('K11:L11');
        $sheet->setCellValue('K11', 'F');
        $sheet->getStyle('K11')->getFont()->setBold(true)->setSize(9);

        $sheet->mergeCells('M9:M10');
        $richText11 = new RichText();
        $normalText11 = $richText11->createTextRun("Total Actual Charges");
        $normalText11->getFont()->setBold(true)->setSize(9); 
        $italicText11 = $richText11->createTextRun("(with deductions of the following: Philhealth, PCSO, Senior Citizen, PWD, DSWD, LGU,HMOs, Insurance & others)");
        $italicText11->getFont()->setItalic(true)->setSize(9); // ✅ Set font size to 10
        $sheet->setCellValue('M9', $richText11);
        $sheet->getStyle('M9')->getAlignment()->setWrapText(true); 

        $sheet->setCellValue('M11', 'A -  SUM(B:F) = G');
        $sheet->getStyle('M11')->getFont()->setBold(true)->setSize(9);

        $sheet->mergeCells('N9:N10');
        $richText12 = new RichText();
        $normalText12 = $richText12->createTextRun("Assistance to Professional Fee through MAIP (not more than 50% of the approved assistance)");
        $normalText12->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue('N9', $richText12);
        $sheet->getStyle('N9')->getAlignment()->setWrapText(true); 

        $sheet->setCellValue('N11', 'H');
        $sheet->getStyle('N11')->getFont()->setBold(true)->setSize(9);

        $sheet->mergeCells('O9:O10');
        $richText13 = new RichText();
        $normalText13 = $richText13->createTextRun("Hospital Bill/Medical Assistance through MAIP");
        $normalText13->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue('O9', $richText13);
        $sheet->getStyle('O9')->getAlignment()->setWrapText(true); 

        $sheet->setCellValue('O11', 'I');
        $sheet->getStyle('O11')->getFont()->setBold(true)->setSize(9);
        
        $sheet->mergeCells('P9:P10');
        $richText14 = new RichText();
        $normalText14 = $richText14->createTextRun("Total Actual Approved Assistance through MAIP (Utilized Amount)");
        $normalText14->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue('P9', $richText14);
        $sheet->getStyle('P9')->getAlignment()->setWrapText(true); 

        $sheet->setCellValue('P11', 'H + I = J');
        $sheet->getStyle('P11')->getFont()->setBold(true)->setSize(9);

        $sheet->mergeCells('Q9:Q10');
        $richText15 = new RichText();
        $normalText15 = $richText15->createTextRun("Percent of Excess Net Bill/Charges covered by MAIP");
        $normalText15->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue('Q9', $richText15);
        $sheet->getStyle('Q9')->getAlignment()->setWrapText(true); 

        $sheet->setCellValue('Q11', 'J / G = K');
        $sheet->getStyle('Q11')->getFont()->setBold(true)->setSize(9);

        $sheet->getStyle('B9:F9')->getAlignment()->setWrapText(true);
        $sheet->getStyle('B9:F9')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B9:F9')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
    
        //service-ward
        $data = AnnexB::whereMonth('month_year', $month)
            ->whereYear('month_year', $year)
            ->where('facility_id', $id)
            ->where('excess', 1)->where('opd', 0)
            ->with([
                'trans',
                'patient'
            ])->get();



        $pfee_service = 0;
        $hbill_service = 0;
        $total_actual_service = 0;

        $annexb1 = [];

        foreach($data as $index => $row){
            
            $fullName = $row->patient->lname . ', ' . $row->patient->fname;

            if($row->patient->mname && $row->patient->mname != 'N/A') {
                $fullName .= ' ' . $row->patient->mname;
            }

            $transTotal = $row->trans?->final_bill ?? 0;
            $senior = $row->senior ?? 0;
            $phic = $row->phic ?? 0;
            $pcso = $row->pcso ?? 0;
            $dswd = $row->dswd ?? 0;
            $o_amount = $row->o_amount ?? 0;
            $approved_assistance = $row->trans?->total ?? 0;
            $actual_charges = ($row->trans?->final_bill ?? 0 ) - ($senior + $phic + $pcso + $dswd + $o_amount);
            $ratio = ($actual_charges > 0 && $approved_assistance > 0)
                ? ($approved_assistance / $actual_charges) * 100
                : 0;
            $p_fee = $row->trans?->p_fee ?? 0;
            $h_bill = $row->trans?->h_bill ?? 0;

            $pfee_service = $pfee_service + $p_fee;
            $hbill_service = $hbill_service + $h_bill;
            $total_actual_service = $total_actual_service + $transTotal;

            $annexb1[] = [
                $index + 1, 
                $fullName,
                $row->patient->patient_code,
                $row->type,
                $transTotal,
                $senior,
                $phic,
                $pcso,
                $dswd,
                $o_amount,
                $row->others,
                $actual_charges,
                $p_fee,
                $h_bill, 
                $approved_assistance,
                $ratio .'%',
            ];
        }

        $sheet->fromArray($annexb1, null, 'B12');

        $sheet->getStyle('F12:K'. (count($annexb1) + 12))
            ->getNumberFormat()->setFormatCode('#,##0.00');

        $sheet->getStyle('M12:Q'. (count($annexb1) + 12))
            ->getNumberFormat()->setFormatCode('#,##0.00');

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ]
        ];
        
        $sheet->getStyle('B12:Q' . (count($annexb1) + 11))->applyFromArray($styleArray);
        
        $sheet->getStyle('C12:E' . (count($annexb1) + 11))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        
        $sheet->getStyle('F12:Q' . (count($annexb1) + 11))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        
        $number = count($annexb1) + 15;
        $sheet->mergeCells("C" . ($number - 3) . ":D" . ($number - 3));

        $richText3 = new RichText();
        $normalText2 = $richText3->createTextRun("TOTAL SERVICE WARD IN-PATIENTS");
        $normalText2->getFont()->setBold(true)->setSize(9);
        
        $sheet->setCellValue("C" . ($number - 3), $richText3);
        
        $sheet->getStyle("C" . ($number - 3) . ":D" . ($number - 3))
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);        
                $sheet->getStyle("F" . ($number - 3) . ":Q" . ($number - 3))
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);  

        $deduction = $data->sum('phic') + $data->sum('pcso') + $data->sum('dswd') + $data->sum('o_amount');
        $actual = $total_actual_service - $deduction;

        $sheet->setCellValue("F" . ($number-3), $total_actual_service);
        $sheet->setCellValue("G" . ($number-3), $data->sum('senior'));
        $sheet->setCellValue("H" . ($number-3), $data->sum('phic'));
        $sheet->setCellValue("I" . ($number-3), $data->sum('pcso'));
        $sheet->setCellValue("J" . ($number-3), $data->sum('dswd'));
        $sheet->setCellValue("K" . ($number-3), $data->sum('o_amount'));
        $sheet->setCellValue("M" . ($number-3), $actual);
        $sheet->setCellValue("N" . ($number-3), $pfee_service);
        $sheet->setCellValue("O" . ($number-3), $hbill_service);
        $sheet->setCellValue("P" . ($number-3), $pfee_service + $hbill_service);

        // payward

        $range = "B" . $number . ":Q" . $number; 
        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);
        $sheet->getStyle($range)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('B7B7B7');

        $range2 = "B" . ($number + 1). ":Q" .  ($number + 1); 
        $sheet->getStyle($range2)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);
        $sheet->getStyle($range2)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('B7B7B7');

        $range3 = "B" . ($number + 2). ":Q" .  ($number + 2); 
        $sheet->getStyle($range3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);
        $sheet->getStyle($range3)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('B7B7B7');

        $range4 = "B" . ($number + 3). ":Q" .  ($number + 3); 
        $sheet->getStyle($range4)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);
        $sheet->getStyle($range4)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('B7B7B7');

        $richText11 = new RichText();
        $normalText = $richText11->createTextRun("PAYWARD IN-PATIENTS");
        $normalText->getFont()->setBold(false);
        $sheet->getRowDimension($number)->setRowHeight(25); 

        $cell1 = 'C' . $number; 
        $sheet->setCellValue($cell1, $richText11);

        $sheet->mergeCells("C{$number}:Q{$number}");
        $sheet->getStyle("C{$number}:Q{$number}")->getAlignment()->setWrapText(true);
        $sheet->getStyle("C{$number}:Q{$number}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("C{$number}:Q{$number}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->getStyle("B" . ($number + 1) . ":F" . ($number + 1))->getAlignment()->setWrapText(true);
        $sheet->getStyle("B" . ($number + 1) . ":F" . ($number + 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("B" . ($number + 1) . ":F" . ($number + 1))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->mergeCells("B" . ($number + 1) . ":B" . ($number + 3));
        $sheet->setCellValue("B" . ($number + 1), 'No.');
        $sheet->getStyle("B" . ($number + 1) . ":B" . ($number + 3))->getFont()->setBold(true)->setSize(9);

        $sheet->mergeCells("C" . ($number + 1) . ":C" . ($number + 3));
        $richText3 = new RichText();
        $normalText2 = $richText3->createTextRun("Name of Patient (Last Name, First Name,Middle Name)");
        $normalText2->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue("C" . ($number + 1), $richText3);
        $sheet->getStyle("C" . ($number + 1))->getAlignment()->setWrapText(true); 

        $sheet->mergeCells("D" . ($number + 1) . ":D" . ($number + 3));
        $richText4 = new RichText();
        $normalText3 = $richText4->createTextRun("MAIP CODE (Generated from MAIS)");
        $normalText3->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue("D" . ($number + 1), $richText4);

        $sheet->mergeCells("E" . ($number + 1) . ":E" . ($number + 3));
        $richText6 = new RichText();
        $normalText5 = $richText6->createTextRun("Type of Medical Assistance Provided");
        $normalText5->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue("E" . ($number + 1), $richText6);

        $sheet->mergeCells("F" . ($number + 1) . ":F" . ($number + 2));
        $richText7 = new RichText();
        $normalText6 = $richText7->createTextRun("Total Actual Charges ");
        $normalText6->getFont()->setBold(true)->setSize(9);
        $normalText7 = $richText7->createTextRun("(without any medications)");
        $normalText7->getFont()->setBold(false)->setSize(9);

        $sheet->setCellValue("F" . ($number + 1), $richText7);
        $sheet->getStyle("F" . ($number + 1))->getAlignment()->setWrapText(true);

        $sheet->mergeCells("G" . ($number + 1) . ":L" . ($number + 1));
        $sheet->setCellValue("G" . ($number + 1), 'Hospital Bill/Medical Assistance');
        $sheet->getStyle("G" . ($number + 1))->getFont()->setBold(true)->setSize(9);
        $sheet->getRowDimension($number + 1)->setRowHeight(45); 

        $sheet->getStyle("G" . ($number + 1) . ":L" . ($number + 1))->getAlignment()->setWrapText(true);
        $sheet->getStyle("G" . ($number + 1) . ":L" . ($number + 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("G" . ($number + 1) . ":L" . ($number + 1))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->setCellValue("F" . ($number + 3), 'A');
        $sheet->getStyle("F" . ($number + 3))->getFont()->setBold(true)->setSize(9);

        $richText8 = new RichText();
        $normalText8 = $richText8->createTextRun("Senior Citizen/PWD");
        $normalText8->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue("G" . ($number + 2), $richText8);
        $sheet->getStyle("G" . ($number + 2))->getAlignment()->setWrapText(true);

        $richText9 = new RichText();
        $normalText9 = $richText9->createTextRun("PHILHEALTH (case rate)");
        $normalText9->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue("H" . ($number + 2), $richText9);
        $sheet->getStyle("H" . ($number + 2))->getAlignment()->setWrapText(true);

        $sheet->setCellValue("I" . ($number + 2), 'PCSO');
        $sheet->getStyle("I" . ($number + 2))->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle("I" . ($number + 2))->getAlignment()->setIndent(1); 

        $sheet->setCellValue("J" . ($number + 2), 'DSWD');
        $sheet->getStyle("J" . ($number + 2))->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle("J" . ($number + 2))->getAlignment()->setIndent(1); 

        $sheet->mergeCells("K" . ($number + 2) . ":L" . ($number + 2));
        $richText10 = new RichText();
        $normalText10 = $richText10->createTextRun("Others (please specify)");
        $normalText10->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue("K" . ($number + 2), $richText10);
        $sheet->getStyle("K" . ($number + 2))->getAlignment()->setWrapText(true);

        $sheet->getStyle("G" . ($number + 2) . ":L" . ($number + 2))->getAlignment()->setWrapText(true);
        $sheet->getStyle("G" . ($number + 2) . ":L" . ($number + 2))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("G" . ($number + 2) . ":L" . ($number + 2))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->getStyle("F" . ($number + 3) . ":L" . ($number + 3))->getAlignment()->setWrapText(true);
        $sheet->getStyle("F" . ($number + 3) . ":L" . ($number + 3))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("F" . ($number + 3) . ":L" . ($number + 3))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->setCellValue("G" . ($number + 3), 'B');
        $sheet->getStyle("G" . ($number + 3))->getFont()->setBold(true)->setSize(9);

        $sheet->setCellValue("H" . ($number + 3), 'C');
        $sheet->getStyle("H" . ($number + 3))->getFont()->setBold(true)->setSize(9);

        $sheet->setCellValue("I" . ($number + 3), 'D');
        $sheet->getStyle("I" . ($number + 3))->getFont()->setBold(true)->setSize(9);

        $sheet->setCellValue("J" . ($number + 3), 'E');
        $sheet->getStyle("J" . ($number + 3))->getFont()->setBold(true)->setSize(9);

        $sheet->mergeCells("K" . ($number + 3) . ":K" . ($number + 3));
        $sheet->setCellValue("K" . ($number + 3), 'F');
        $sheet->getStyle("K" . ($number + 3))->getFont()->setBold(true)->setSize(9);

        $sheet->mergeCells("M" . ($number + 1) . ":M" . ($number + 2));
        $richText11 = new RichText();
        $normalText11 = $richText11->createTextRun("Total Actual Charges ");
        $normalText11->getFont()->setBold(true)->setSize(9); 
        $italicText11 = $richText11->createTextRun("(with deductions of the following: Philhealth, PCSO, Senior Citizen, PWD, DSWD, LGU,HMOs, Insurance & others)");
        $italicText11->getFont()->setItalic(true)->setSize(9); // ✅ Set font size to 10
        $sheet->setCellValue("M" . ($number + 1), $richText11);
        $sheet->getStyle("M" . ($number + 1))->getAlignment()->setWrapText(true); 

        $sheet->setCellValue("M" . ($number + 3), 'A -  SUM(B:F) = G');
        $sheet->getStyle("M" . ($number + 3))->getFont()->setBold(true)->setSize(9);

        $sheet->mergeCells("N" . ($number + 1) . ":N" . ($number + 2));
        $richText12 = new RichText();
        $normalText12 = $richText12->createTextRun("Assistance to Professional Fee through MAIP (not more than 50% of the approved assistance)");
        $normalText12->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue("N" . ($number + 1), $richText12);
        $sheet->getStyle("N" . ($number + 1))->getAlignment()->setWrapText(true); 

        $sheet->setCellValue("N" . ($number + 3), 'H');
        $sheet->getStyle("N" . ($number + 3))->getFont()->setBold(true)->setSize(9);

        $sheet->mergeCells("O" . ($number + 1) . ":O" . ($number + 2));
        $richText13 = new RichText();
        $normalText13 = $richText13->createTextRun("Hospital Bill/Medical Assistance through MAIP");
        $normalText13->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue("O" . ($number + 1), $richText13);
        $sheet->getStyle("O" . ($number + 1))->getAlignment()->setWrapText(true); 
        // return 1;
        $sheet->setCellValue("O" . ($number + 3), 'I');
        $sheet->getStyle("O" . ($number + 3))->getFont()->setBold(true)->setSize(9);
        
        $sheet->mergeCells("P" . ($number + 1) . ":P" . ($number + 2));
        $richText14 = new RichText();
        $normalText14 = $richText14->createTextRun("Total Actual Approved Assistance through MAIP (Utilized Amount)");
        $normalText14->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue("P" . ($number + 1), $richText14);
        $sheet->getStyle("P" . ($number + 1))->getAlignment()->setWrapText(true); 

        $sheet->setCellValue("P" . ($number + 3), 'H + I = J');
        $sheet->getStyle("P" . ($number + 3))->getFont()->setBold(true)->setSize(9);

        $sheet->mergeCells("Q" . ($number + 1) . ":Q" . ($number + 2));
        $richText15 = new RichText();
        $normalText15 = $richText15->createTextRun("Percent of Excess Net Bill/Charges covered by MAIP");
        $normalText15->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue("Q" . ($number + 1), $richText15);
        $sheet->getStyle("Q" . ($number + 1))->getAlignment()->setWrapText(true); 

        $sheet->setCellValue("Q" . ($number + 3), 'J / G = K');
        $sheet->getStyle("Q" . ($number + 3))->getFont()->setBold(true)->setSize(9);

        $sheet->getStyle('M9:Q11')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('M9:Q11')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        
        $range = "M" . ($number + 1) . ":Q" . ($number + 3);
        $sheet->getStyle($range)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($range)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $data2 = AnnexB::whereMonth('month_year', $month)
            ->whereYear('month_year', $year)
            ->where('facility_id', $id)
            ->where('excess', '!=', 1)->where('opd', 0)
            ->with([
                'trans',
                'patient'
            ])->get();

        $pfee_payward = 0;
        $hbill_payward = 0;
        $total_actual_payward = 0;

        $annexb2 = [];
                
        foreach($data2 as $index => $row){
            
            $fullName = $row->patient->lname . ', ' . $row->patient->fname;

            if($row->patient->mname && $row->patient->mname != 'N/A') {
                $fullName .= ' ' . $row->patient->mname;
            }

            $transTotal = $row->trans?->final_bill ?? 0;
            $senior = $row->senior ?? 0;
            $phic = $row->phic ?? 0;
            $pcso = $row->pcso ?? 0;
            $dswd = $row->dswd ?? 0;
            $o_amount = $row->o_amount ?? 0;
            $approved_assistance = $row->trans?->total ?? 0;
            $actual_charges = ($row->trans?->final_bill ?? 0 ) - ($senior + $phic + $pcso + $dswd + $o_amount);
            $ratio = ($actual_charges > 0 && $approved_assistance > 0)
                ? ($approved_assistance / $actual_charges) * 100
                : 0;
            $p_fee = $row->trans?->p_fee ?? 0;
            $h_bill = $row->trans?->h_bill ?? 0;

            $pfee_payward = $pfee_payward + $p_fee;
            $hbill_payward = $hbill_payward + $h_bill;
            $total_actual_payward = $total_actual_payward + $transTotal;

            $annexb2[] = [
                $index + 1, 
                $fullName,
                $row->patient->patient_code,
                $row->type,
                $transTotal,
                $senior,
                $phic,
                $pcso,
                $dswd,
                $o_amount,
                $row->others,
                $actual_charges,
                $p_fee,
                $h_bill, 
                $approved_assistance,
                $ratio .'%',
            ];
        }

        $sheet->fromArray($annexb2, null, "B" . ($number + 4));

        $styleArray2 = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER, 
            ],
        ];
        
        $number2 = count($annexb2) + $number + 7;

        $sheet->getStyle('F12:K'. ($number2 + 1))
            ->getNumberFormat()->setFormatCode('#,##0.00');

        $sheet->getStyle('M12:Q'. ($number2 + 1))
            ->getNumberFormat()->setFormatCode('#,##0.00');

        $rangeV2 = "B" . ($number + 4) . ":Q" . ($number + 3 + count($annexb2));
        
        $sheet->getStyle($rangeV2)->applyFromArray($styleArray2);
        
        $sheet->getStyle("C" . ($number + 4) . ":E" . ($number + 3 + count($annexb2)))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        
        $sheet->getStyle("F" . ($number + 4) . ":Q" . ($number + 3 + count($annexb2)))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        

        $set_data = $number + count($annexb2) + 6;
        
        $sheet->mergeCells("C" . ($set_data -2) . ":D" . ($set_data - 2));
        $richText3 = new RichText();
        $normalText2 = $richText3->createTextRun("TOTAL PAYWARD IN-PATIENTS");
        $normalText2->getFont()->setBold(true)->setSize(9);

        $sheet->setCellValue("C" . ($set_data-2), $richText3);
        $sheet->getStyle("C" . ($set_data-2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->getStyle("F" . ($set_data -2) . ":Q" . ($set_data - 2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $deduction = $data2->sum('phic') + $data2->sum('pcso') + $data2->sum('dswd') + $data2->sum('o_amount');
        $actual = $total_actual_payward - $deduction;

        $sheet->setCellValue("F" . ($set_data-2), $total_actual_payward);
        $sheet->setCellValue("G" . ($set_data-2), $data2->sum('senior'));
        $sheet->setCellValue("H" . ($set_data-2), $data2->sum('phic'));
        $sheet->setCellValue("I" . ($set_data-2), $data2->sum('pcso'));
        $sheet->setCellValue("J" . ($set_data-2), $data2->sum('dswd'));
        $sheet->setCellValue("K" . ($set_data-2), $data2->sum('o_amount'));
        $sheet->setCellValue("M" . ($set_data-2), $actual);
        $sheet->setCellValue("N" . ($set_data-2), $pfee_payward);
        $sheet->setCellValue("O" . ($set_data-2), $hbill_payward);
        $sheet->setCellValue("P" . ($set_data-2), $pfee_payward + $hbill_payward);

        $range = "B" . $number2 . ":Q" . $number2; 
        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);
        $sheet->getStyle($range)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('B7B7B7');

        $range2 = "B" . ($number2 + 1). ":Q" .  ($number2 + 1); 
        $sheet->getStyle($range2)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);
        $sheet->getStyle($range2)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('B7B7B7');

        $range3 = "B" . ($number2 + 2). ":Q" .  ($number2 + 2); 
        $sheet->getStyle($range3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);
        $sheet->getStyle($range3)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('B7B7B7');

        $range4 = "B" . ($number2 + 3). ":Q" .  ($number2 + 3);
        $sheet->getStyle($range4)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);
        $sheet->getStyle($range4)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('B7B7B7');

        $sheet->getStyle("B" . ($number2 + 1) . ":Q" . ($number2 + 1))->getAlignment()->setWrapText(true);
        $sheet->getStyle("B" . ($number2 + 1) . ":Q" . ($number2 + 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("B" . ($number2 + 1) . ":Q" . ($number2 + 1))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $range = "M" . ($number2 + 1) . ":Q" . ($number2 + 3);
        $sheet->getStyle($range)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($range)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $richText11 = new RichText();
        $normalText = $richText11->createTextRun("OPD PATIENTS");
        $normalText->getFont()->setBold(false);

        $sheet->getRowDimension($number2)->setRowHeight(25); 

        $cell = 'C' . $number2; 

        $sheet->setCellValue($cell, $richText11);
        $sheet->mergeCells("C{$number2}:Q{$number2}");

        $sheet->getStyle("C{$number2}:Q{$number2}")->getAlignment()->setWrapText(true);
        $sheet->getStyle("C{$number2}:Q{$number2}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("C{$number2}:Q{$number2}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->mergeCells("B" . ($number2 + 1) . ":B" . ($number2 + 3));
        $sheet->setCellValue("B" . ($number2 + 1), 'No.');
        $sheet->getStyle("B" . ($number2 + 1) . ":B" . ($number2 + 3))->getFont()->setBold(true)->setSize(9);

        $sheet->mergeCells("C" . ($number2 + 1) . ":C" . ($number2 + 3));
        $richText3 = new RichText();
        $normalText2 = $richText3->createTextRun("Name of Patient (Last Name, First Name, Middle Name)");
        $normalText2->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue("C" . ($number2 + 1), $richText3);
        $sheet->getStyle("C" . ($number2 + 1))->getAlignment()->setWrapText(true); 
        $sheet->getRowDimension($number2 + 1)->setRowHeight(55); 

        $sheet->mergeCells("D" . ($number2 + 1) . ":D" . ($number2 + 3));
        $richText4 = new RichText();
        $normalText3 = $richText4->createTextRun("MAIP CODE (Generated from MAIS)");
        $normalText3->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue("D" . ($number2 + 1), $richText4);

        $sheet->mergeCells("E" . ($number2 + 1) . ":E" . ($number2 + 3));
        $richText6 = new RichText();
        $normalText5 = $richText6->createTextRun("Type of Medical Assistance Provided");
        $normalText5->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue("E" . ($number2 + 1), $richText6);

        $sheet->mergeCells("F" . ($number2 + 1) . ":F" . ($number2 + 2));
        $richText7 = new RichText();
        $normalText6 = $richText7->createTextRun("Total Actual Charges with Professional Fee (without any deduction )");
        $normalText6->getFont()->setBold(true)->setSize(9); 

        $sheet->setCellValue("F" . ($number2 + 1), $richText7);
        $sheet->getStyle("F" . ($number2 + 1))->getAlignment()->setWrapText(true);

        $mergeRange = "G" . ($number2 + 1) . ":O" . ($number2 + 3); 
        $sheet->mergeCells($mergeRange);

        $sheet->getStyle($mergeRange)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '000000'], 
            ],
            'font' => [
                'color' => ['rgb' => 'FFFFFF'], 
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $mergeRange1 = "Q" . ($number2 + 1) . ":Q" . ($number2 + 3); 
        $sheet->mergeCells($mergeRange);

        $sheet->getStyle($mergeRange1)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '000000'], 
            ],
            'font' => [
                'color' => ['rgb' => 'FFFFFF'], 
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->mergeCells("P" . ($number2 + 1) . ":P" . ($number2 + 2));
        $richText14 = new RichText();
        $normalText14 = $richText14->createTextRun("Total Actual Approved Assistance through MAIP (Utilized Amount)");
        $normalText14->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue("P" . ($number2 + 1), $richText14);
        $sheet->getStyle("P" . ($number2 + 1))->getAlignment()->setWrapText(true); 

        $sheet->setCellValue("P" . ($number2 + 3), 'B');
        $sheet->getStyle("P" . ($number2 + 3))->getFont()->setBold(true)->setSize(9);

        $data3 = AnnexB::whereMonth('month_year', $month)
            ->whereYear('month_year', $year)
            ->where('facility_id', $id)
            ->where('opd', 1)
            ->with([
                'trans',
                'patient'
            ])->get();

        $pfee_opd = 0;
        $hbill_opd = 0;
        $total_actual_opd = 0;

        $annexb3 = [];
                
        foreach($data3 as $index => $row){
            
            $fullName = $row->patient->lname . ', ' . $row->patient->fname;

            if($row->patient->mname && $row->patient->mname != 'N/A') {
                $fullName .= ' ' . $row->patient->mname;
            }

            $transTotal = $row->trans?->final_bill ?? 0;
            $approved_assistance = $row->trans?->total ?? 0;
            $p_fee = $row->trans?->p_fee ?? 0;
            $h_bill = $row->trans?->h_bill ?? 0;

            $pfee_opd = $pfee_opd + $p_fee;
            $hbill_opd = $hbill_opd + $h_bill;
            $total_actual_opd = $total_actual_opd + $transTotal;

            $annexb3[] = [
                $index + 1, 
                $fullName,
                $row->patient->patient_code,
                $row->type,
                $transTotal,
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '', 
                $approved_assistance,
            ];
        }

        $startRow = $number2 + 4;
        $sheet->fromArray($annexb3, null, "B" . $startRow);

        $lastRow = $startRow + count($annexb3) - 1;

        $styleArray3 = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER, 
            ],
        ];

        $rangeV3 = "B$startRow:Q$lastRow";

        $sheet->getStyle($rangeV3)->applyFromArray($styleArray3);

        $sheet->getStyle("C$startRow:E$lastRow")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->getStyle("F$startRow:Q$lastRow")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);


        for ($row = $startRow; $row <= $lastRow; $row++) {
            $mergeRange = "G$row:O$row";
            $sheet->mergeCells($mergeRange);
            $sheet->getStyle($mergeRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('000000');
            $sheet->getStyle($mergeRange)->getFont()->getColor()->setARGB('FFFFFF');
        }

        for ($row = $startRow; $row <= $lastRow; $row++) {
            $mergeRange = "Q$row:Q$row";
            $sheet->mergeCells($mergeRange);
            $sheet->getStyle($mergeRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('000000');
            $sheet->getStyle($mergeRange)->getFont()->getColor()->setARGB('FFFFFF');
        }
        
        $number3 = $number2 + count($annexb3) + 3;
        
        $sheet->mergeCells("C" . ($number3 +1) . ":D" . ($number3 + 1));
        $richText3 = new RichText();
        $normalText2 = $richText3->createTextRun("TOTAL OPD PATIENTS");
        $normalText2->getFont()->setBold(true)->setSize(9);
        $sheet->setCellValue("C" . ($number3 +1), $richText3);
        $sheet->getStyle("C" . ($number3 +1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->setCellValue("F" . ($number3 +1), $total_actual_opd);
        $sheet->setCellValue("P" . ($number3 +1), $pfee_opd + $hbill_opd);

        $sheet->fromArray($annexb3, null, "B" . ($number2 + 4));

        $styleArray3 = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];

        $sheet->getStyle($rangeV3)->applyFromArray($styleArray);

        $number4 = $number3 + 3;

        $sheet->mergeCells("C" . ($number4) . ":D" . ($number4));
        $richText3 = new RichText();
        $normalText2 = $richText3->createTextRun("GRAND TOTAL");
        $normalText2->getFont()->setBold(true)->setSize(9);
        $sheet->setCellValue("C" . ($number4), $richText3);

        $grandTotal = $total_actual_service + $total_actual_payward + $total_actual_opd;
        $sheet->setCellValue("F" . ($number4), $grandTotal);
        $sheet->getStyle("F" . ($number4))->getFont()->setBold(true)->setSize(9);

        $range = "F{$number4}:Q{$number4}";
        $sheet->getStyle($range)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE);

        $totalApproved = $pfee_opd + $pfee_payward + $pfee_service + $hbill_opd + $hbill_payward + $hbill_service;
        $sheet->setCellValue("P" . ($number4), $totalApproved);
        $sheet->getStyle("P" . ($number4))->getFont()->setBold(true)->setSize(9);

        $sheet->getStyle('F12:Q' . ($number4))
            ->getNumberFormat()->setFormatCode('#,##0.00');

        $richText3 = new RichText();
        $normalText2 = $richText3->createTextRun('Summary');
        $normalText2->getFont()->setBold(true)->setSize(9);
        $sheet->setCellValue("C" . ($number4 + 4), $richText3);

        $richText3 = new RichText();
        $normalText2 = $richText3->createTextRun("Number of Patients Served");
        $normalText2->getFont()->setSize(9); 
        $sheet->setCellValue("D" . ($number4 + 5), $richText3);
        $sheet->getStyle("D" . ($number4 + 5), $richText3)->getAlignment()->setWrapText(true);
        $sheet->getStyle("D" . ($number4 + 5))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $richText3 = new RichText();
        $normalText2 = $richText3->createTextRun("Total Actual Charges");
        $normalText2->getFont()->setSize(9); 
        $sheet->setCellValue("E" . ($number4 + 5), $richText3);
        $sheet->getStyle("E" . ($number4 + 5), $richText3)->getAlignment()->setWrapText(true);
        $sheet->getStyle("E" . ($number4 + 5))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $richText3 = new RichText();
        $normalText2 = $richText3->createTextRun("Total Actual Approved Assistance through MAIP (Utilized Amount)");
        $normalText2->getFont()->setSize(9);
        $sheet->setCellValue("F" . ($number4 + 5), $richText3);
        $sheet->getStyle("F" . ($number4 + 5), $richText3)->getAlignment()->setWrapText(true);

        $sheet->getStyle("D" . ($number4 + 5) . ":F" . ($number4 + 5))
        ->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
        ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle('E12:F' . ($number4 + 9))
            ->getNumberFormat()->setFormatCode('#,##0.00');

        $sheet->getStyle("D" . ($number4 + 5) . ":F" . ($number4 + 5))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("D" . ($number4 + 6) . ":F" . ($number4 + 9))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $sheet->setCellValue("C" . ($number4 + 6), "Service ward In-patients");
        $sheet->setCellValue("C" . ($number4 + 7), "Pay ward In-patients");
        $sheet->setCellValue("C" . ($number4 + 8), "OPD Patients");

        $sheet->setCellValue("D" . ($number4 + 6), count($data));
        $sheet->setCellValue("D" . ($number4 + 7), count($data2));
        $sheet->setCellValue("D" . ($number4 + 8), count($data3));

        $sheet->setCellValue("E" . ($number4 + 6), $total_actual_service);
        $sheet->setCellValue("E" . ($number4 + 7), $total_actual_payward);
        $sheet->setCellValue("E" . ($number4 + 8), $total_actual_opd);

        $sheet->setCellValue("F" . ($number4 + 6), $pfee_service + $hbill_service);
        $sheet->setCellValue("F" . ($number4 + 7), $pfee_payward + $hbill_payward);
        $sheet->setCellValue("F" . ($number4 + 8), $pfee_opd + $hbill_opd);

        $sheet->getRowDimension(($number4 + 5))->setRowHeight(60); 
        $range = "C" . ($number4 + 5) . ":F" . $number4 + 8; 
        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);

        $sheet->setCellValue("C" . ($number4 + 9), "TOTAL");
        $sheet->setCellValue("D" . ($number4 + 9), count($data) + count($data2) + count($data3));
        $sheet->setCellValue("E" . ($number4 + 9), $total_actual_service + $total_actual_payward + $total_actual_opd);
        $sheet->setCellValue("F" . ($number4 + 9), $totalApproved);
        $sheet->getStyle("D" . ($number4 + 9). ":F" . ($number4 + 9))->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE);

        $sheet->setCellValue("C" . ($number4 + 11), "Note:");
        $sheet->getStyle("C" . ($number4 + 11))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $sheet->mergeCells("D" . ($number4 + 11) . ":F" . ($number4 + 11));
        $sheet->setCellValue("D" . ($number4 + 11), "*Put page numbers in the lower part of the file");
        $sheet->getStyle("D" . ($number4 + 11))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue("D" . ($number4 + 12), "*Affix initials per page of report except for the last page which includes complete signatories");
        $sheet->getStyle("D" . ($number4 + 12))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->setCellValue("C" . ($number4 + 14), "Prepared by:");
        $sheet->getStyle("C" . ($number4 + 16))->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
        $sheet->setCellValue("C" . ($number4 + 17), "Signature Over Printed Name");
        $richText3 = new RichText();
        $normalText2 = $richText3->createTextRun("Designation");
        $sheet->setCellValue("C" . ($number4 + 18), $richText3);
        $normalText2->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle("C" . ($number4 + 18), $richText3)->getAlignment()->setWrapText(true);
        $sheet->setCellValue("C" . ($number4 + 19), "Date:");

        $sheet->mergeCells("G" . ($number4 + 14) . ":H" . ($number4 + 14));
        $sheet->setCellValue("G" . ($number4 + 14), "Certified correct:");
        $sheet->mergeCells("G" . ($number4 + 16) . ":H" . ($number4 + 16));
        $sheet->getStyle("G" . ($number4 + 16) . ":H" . ($number4 + 16))->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
        $sheet->mergeCells("G" . ($number4 + 17) . ":H" . ($number4 + 17));
        $sheet->setCellValue("G" . ($number4 + 17), "Signature Over Printed Name");
        $sheet->mergeCells("G" . ($number4 + 18) . ":H" . ($number4 + 18));
        $richText3 = new RichText();
        $normalText2 = $richText3->createTextRun("Chief Accountant");
        $sheet->setCellValue("G" . ($number4 + 18), $richText3);
        $normalText2->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle("G" . ($number4 + 18), $richText3)->getAlignment()->setWrapText(true);
        $sheet->mergeCells("G" . ($number4 + 19) . ":H" . ($number4 + 19));
        $sheet->setCellValue("G" . ($number4 + 19), "Date:");

        $sheet->setCellValue("M" . ($number4 + 14), "Approved by:");
        $sheet->getStyle("M" . ($number4 + 16))->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
        $sheet->setCellValue("M" . ($number4 + 17), "Signature Over Printed Name");
        $richText3 = new RichText();
        $normalText2 = $richText3->createTextRun("Hospital Head");
        $sheet->setCellValue("M" . ($number4 + 18), $richText3);
        $normalText2->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle("M" . ($number4 + 18), $richText3)->getAlignment()->setWrapText(true);
        $sheet->setCellValue("M" . ($number4 + 19), "Date:");
        $sheet->getStyle("M" . ($number4 + 14) . ":M" . ($number4 + 19))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle($sheet->calculateWorksheetDimension())->getFont()->setSize(9);

        // Output preparation
        ob_start();
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();

        // Filename
        $filename = 'ANNEX-B-'.$this->getAcronym($facility).'-'. date('F', mktime(0, 0, 0, $month, 10)).$year. '.xlsx';

        // Set headers
        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=$filename");
        header("Pragma: no-cache");
        header("Expires: 0");

        // Output the file
        return $xlsData;
        exit;    
    }

    public function furFacilities(Request $request){

        $data = AnnexB::select('facility_id', DB::raw('MAX(id) as latest_id'))
            ->groupBy('facility_id')
            ->with('facility:id,name');
        return view('facility.fur_facilities',[
            'data' => $data->paginate(50)
        ]);
    }

    public function facilityView(Request $request){
        $id = $request->id;
        $facility = Facility::where('id', $id)->value('name');
        $year = $request->year ?? now()->year;
        
        return view('facility.facility_view',[
            'facility' => $facility,
            'id' => $id,
            'year' => $year,
            'tab_type' => $request->tab_type ?? 1,
            'viewAll' => $request->viewAll ?? '',
        ]);
    }

    public function annexAView(Request $request, $id, $year){
    
        $facility_id = $id;
        $year = $year == '' ? now()->year : $year;
        $data = AnnexB::join('transmittal_patients', 'annex_b.patient_id', '=', 'transmittal_patients.patient_id')
            ->where('annex_b.facility_id', $facility_id)
            ->whereYear('annex_b.month_year', $year)
            ->groupByRaw('MONTH(annex_b.month_year)')
            ->selectRaw('
                MONTH(annex_b.month_year) as month,
                COUNT(DISTINCT annex_b.patient_id) as patients,
                SUM(transmittal_patients.total) as total
            ')
            ->get();


        $allMonths = [];

        for ($m = 1; $m <= 12; $m++) {
            $monthData = $data->firstWhere('month', $m);

            $allMonths[] = [
                'month' => Carbon::create()->month($m)->format('F'), 
                'patients' => $monthData->patients ?? 0,
                'total' => (float) ($monthData->total ?? 0)
            ];
        }

        $overallPatients = collect($allMonths)->sum('patients');
        $overallTotal    = collect($allMonths)->sum('total');

        $result = [
            'year' => $year,
            'monthly' => $allMonths,
            'overall' => [
                'patients' => $overallPatients,
                'total' => $overallTotal
            ]
        ];
    
        return view('facility.fur_annex_a',
            [
                'data' => $result,
                'year' => $year,
                'id' => $id
            ]
        );
    }

    public function fcAnnex(Request $request, $id, $year){

        $data = AnnexB::join('transmittal_patients', 'annex_b.patient_id', '=', 'transmittal_patients.patient_id')
            ->join('facility', 'annex_b.facility_id', '=', 'facility.id')
            ->where('annex_b.status', '!=', 0)
            ->where('annex_b.facility_id', $id)
            ->groupByRaw('YEAR(annex_b.month_year), MONTH(annex_b.month_year)')
            ->selectRaw('
                YEAR(annex_b.month_year) as year,
                MONTH(annex_b.month_year) as month,
                MAX(annex_b.facility_id) as facility_id,
                MAX(facility.name) as name,
                MAX(annex_b.status) as status,
                MAX(annex_b.remarks) as remarks,
                COUNT(DISTINCT annex_b.patient_id) as patients,
                SUM(transmittal_patients.total) as total,
                MAX(annex_b.updated_at) as last_update
            ');

        if($request->viewAll){
            $year = '';
            $type = '';
        }

        if($year){
            $data = $data->when($year, function ($query) use ($year) {
                $query->whereYear('month_year', $year);
            });
        }
        
        if($request->type && $request->type != 'all'){
            $data->where('annex_b.status', $request->type);
        }

        if($request->facility_id){
            $month = $request->month;
            $year = $request->year;
            $facility_id = $request->facility_id;

            $monthNumber = str_pad($month, 2, '0', STR_PAD_LEFT);
            $data = AnnexB::whereMonth('month_year', $monthNumber)
                ->whereYear('month_year', $year)
                ->where('facility_id', $facility_id)
                ->with([
                    'trans',
                    'patient'
                ]);

            if ($request->data_type) {
                $type = $request->data_type;
            
                if ($type == 3) {

                    $data->where(function ($q) {
                        $q->where('opd', 1);
                    });

                } elseif ($type == 2) { 

                    $data->where(function ($q) {
                        $q->where('excess', '!=', 1)->where('opd', 0);
                    });
                }elseif ($type == 1) {

                    $data->where(function ($q) {
                        $q->where('excess', 1)->where('opd', 0);
                    });
                }
            }        
                
            $keyword = $request->keyword;

            if($request->viewAll){
                $keyword = '';
            }

            if($keyword){

                $data->whereHas('patient', function ($query) use ($keyword) {
                    $query->where('lname', 'LIKE', "%{$keyword}%")
                        ->orWhere('fname', 'LIKE', "%{$keyword}%")
                        ->orWhere('mname', 'LIKE', "%{$keyword}%");
                });
            }

            return view('facility.annex_b_view',[
                'data' => $data->paginate(50),
                'keyword' => $keyword,
                'type' => $request->data_type,
                'month' => $month,
                'year' => $year,
                'facility_id' => $facility_id
            ]);
        }
        return view('facility.fc_annex_b',[
            'data' => $data->orderBy('year', 'asc')
                ->orderBy('month', 'asc')
                ->paginate(50),
            'type' => $request->type ?? '',
            'year' => $year,
            'id' => $id,
            'viewAll' => $request->viewAll
        ]);
    }

    public function consoA(Request $request){
        
        $year = $request->year ?? now()->year;

        $data = AnnexB::join('transmittal_patients', 'annex_b.patient_id', '=', 'transmittal_patients.patient_id')
            ->where('annex_b.status', 2)
            ->whereYear('annex_b.month_year', $year)
            ->groupByRaw('MONTH(annex_b.month_year)')
            ->selectRaw('
                MONTH(annex_b.month_year) as month,
                COUNT(DISTINCT annex_b.patient_id) as patients,
                SUM(transmittal_patients.total) as total
            ')
            ->get();

        $allMonths = [];

        for ($m = 1; $m <= 12; $m++) {
            $monthData = $data->firstWhere('month', $m);

            $allMonths[] = [
                'month' => Carbon::create()->month($m)->format('F'), 
                'patients' => $monthData->patients ?? 0,
                'total' => (float) ($monthData->total ?? 0)
            ];
        }

        $overallPatients = collect($allMonths)->sum('patients');
        $overallTotal    = collect($allMonths)->sum('total');

        $main_result = [
            'year' => $year,
            'monthly' => $allMonths,
            'overall' => [
                'patients' => $overallPatients,
                'total' => $overallTotal
            ]
        ];

        $ids = AnnexB::where('status', 2)
            ->distinct()
            ->pluck('facility_id')
            ->toArray();   

        if($request->excel){
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->getParent()->getDefaultStyle()->getFont()->setName('Times New Roman');
            $sheet->getParent()->getDefaultStyle()->getFont()->setSize(10);

            $sheet->getColumnDimension('A')->setWidth(30);  
            $sheet->getColumnDimension('B')->setWidth(20); 
            $sheet->getColumnDimension('C')->setWidth(20); 
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(20);
            $sheet->getColumnDimension('F')->setWidth(20); 
            $sheet->getColumnDimension('G')->setWidth(20); 
            $sheet->getColumnDimension('H')->setWidth(20); 
            $sheet->getColumnDimension('I')->setWidth(15); 

            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setName('DOH Logo');
            $drawing->setDescription('Department of Health Logo');
            $drawing->setPath(public_path('images/doh-logo.png')); 
            $drawing->setWidth($drawing->getWidth() * 0.20);
            $drawing->setHeight($drawing->getHeight() * 0.20);
            $drawing->setCoordinates('A2');
            $drawing->setOffsetX(45);  
            $drawing->setOffsetY(5); 
            $drawing->setWorksheet($sheet);

            $sheet->setCellValue('H1', 'ANNEX A2');
            $sheet->getStyle('H1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

            // Header content
            $richText = new RichText();
            $rt = $richText->createTextRun("Republic of the Philippines");
            $rt->getFont()->setBold(true)->setName('Times New Roman')->setSize(10);
            $sheet->setCellValue('B2', $richText);

            $richText = new RichText();
            $rt = $richText->createTextRun("Department of Health");
            $rt->getFont()->setBold(true)->setName('Times New Roman')->setSize(10);
            $sheet->setCellValue('B3', $richText);

            $richText = new RichText();
            $rt = $richText->createTextRun("Medical Assistance to Indigent and Financially Incapacitated Patients (MAIFIPP) Program - Fund Utilization Report Summary CY " . $year);
            $rt->getFont()->setBold(true)->setName('Times New Roman')->setSize(10);
            $sheet->setCellValue('B4', $richText);

            $richText = new RichText();
            $rt = $richText->createTextRun("As of " . $year);
            $rt->getFont()->setItalic(true)->setName('Times New Roman')->setSize(10);
            $sheet->setCellValue('B5', $richText);

            $sheet->mergeCells('B2:H2');
            $sheet->mergeCells('B3:H3');
            $sheet->mergeCells('B4:H4');
            $sheet->mergeCells('B5:H5');
            $sheet->getStyle('A2:H5')->getAlignment()->setWrapText(true);
            $sheet->getStyle('A2:H5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A2:H5')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            
            // Add Malasakit Logo
            $drawing1 = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing1->setName('Malasakit Logo');
            $drawing1->setDescription('MPU Logo');
            $drawing1->setPath(public_path('images/malasakit.png')); 
            $drawing1->setCoordinates('I2');
            $drawing1->setOffsetX(25); 
            $drawing1->setOffsetY(5); 
            $drawing1->setWorksheet($sheet);

            $number = 0;
            foreach($ids as $id){
                
                $number = $number + 9;
                $facility_id = $id;
                $data = AnnexB::join('transmittal_patients', 'annex_b.patient_id', '=', 'transmittal_patients.patient_id')
                    ->where('annex_b.status', 2)
                    ->where('annex_b.facility_id', $facility_id)
                    ->whereYear('annex_b.month_year', $year)
                    ->groupByRaw('MONTH(annex_b.month_year)')
                    ->selectRaw('
                        MONTH(annex_b.month_year) as month,
                        COUNT(DISTINCT annex_b.patient_id) as patients,
                        SUM(transmittal_patients.total) as total
                    ')
                    ->get();

                $facility = Facility::where('id', $id)->value('name');
                $allMonths = [];
    
                for ($m = 1; $m <= 12; $m++) {
                    $monthData = $data->firstWhere('month', $m);
    
                    $allMonths[] = [
                        'month' => Carbon::create()->month($m)->format('F'), 
                        'patients' => $monthData->patients ?? 0,
                        'total' => (float) ($monthData->total ?? 0)
                    ];
                }
    
                $overallPatients = collect($allMonths)->sum('patients');
                $overallTotal    = collect($allMonths)->sum('total');
    
                $result = [
                    'year' => $year,
                    'monthly' => $allMonths,
                    'overall' => [
                        'patients' => $overallPatients,
                        'total' => $overallTotal
                    ]
                ];

                $headers = [
                    'A'.($number) => 'Name of Hospital',
                    'B'.($number) => 'Classification',
                    'C'.($number) => 'SAA No. and Date of Issuance of SAA',
                    'D'.($number) => 'Amount of SAA',
                    'E'.($number) => 'Total Fund Allocation',
                    'F'.($number) => 'Month Utilized',
                    'G'.($number) => 'Total Number of Patients Served',
                    'H'.($number) => 'Total Actual Approved Assistance through MAIPP (Utilized Amount)',
                    'I'.($number) => 'Balance'
                ];

                $range = "A".$number.":I". $number; 
                $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);
                $sheet->getStyle($range)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('E7E6E6');
                $sheet->getRowDimension($number)->setRowHeight(70); 

                foreach ($headers as $cell => $text) {
                    $richText = new RichText();
                    $normalText = $richText->createTextRun($text);
                    $normalText->getFont()->setBold(true)->setName('Times New Roman')->setSize(10);
                    $sheet->setCellValue($cell, $richText);
                    $sheet->getStyle($cell)->getAlignment()->setWrapText(true);
                    $sheet->getStyle($cell)->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setVertical(Alignment::VERTICAL_CENTER);
                }

                // Add facility name and classification (merged for rows 10-21)
                $sheet->setCellValue('A'. ($number + 1), $facility);
                $sheet->mergeCells('A'. ($number + 1). ':A'. ($number + 12));
                $sheet->getStyle('A'. ($number + 1))->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->setCellValue('B'. ($number + 1), 'Private Hospital'); 
                $sheet->mergeCells('B'. ($number + 1). ':B'. ($number + 12));
                $sheet->getStyle('B'. ($number + 1))->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);

                // Add monthly data (rows 10-21)
                $startRow = $number + 1;
                foreach($result['monthly'] as $index => $row) {
                    $currentRow = $startRow + $index;
                    $sheet->setCellValue("F{$currentRow}", $row['month']);
                    $sheet->setCellValue("G{$currentRow}", $row['patients']);
                    $sheet->setCellValue("H{$currentRow}", $row['total']);
                    $sheet->setCellValue("I{$currentRow}", '-'); 
                }

                // Total row
                $sheet->setCellValue("A". ($number + 13), "TOTAL");
                $sheet->setCellValue("F". ($number + 13), '');
                $sheet->setCellValue("G" . ($number + 13), "=SUM(G" . ($number + 1) . ":G" . ($number + 12) . ")");
                $sheet->setCellValue("H" . ($number + 13), "=SUM(H" . ($number + 1) . ":H" . ($number + 12) . ")");
                $sheet->setCellValue("I". $number + 13, '0'); 

                // Apply borders to data area
                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                        ],
                    ],
                ];

                $sheet->mergeCells('E'. ($number + 1).':E'. ($number + 12));

                $sheet->getStyle('A'. ($number + 1). ':I'. ($number + 12))->applyFromArray($styleArray);
                // Number formatting
                $sheet->getStyle('D'.($number + 1).':E'. ($number + 13))->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle('H'.($number + 1).':I'.($number + 13))->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle('G'.($number + 1).':I'.($number + 13))->getNumberFormat()->setFormatCode('#,##0');

                // Center align specific columns
                $sheet->getStyle('F'.($number + 1).':F'.($number + 13))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('F'.($number + 1).':F'.($number + 13))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                // Right align number columns
                $sheet->getStyle('H'.($number + 1).':I'.($number + 12))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $sheet->setCellValue("D".($number + 13), '-'); 
                $sheet->setCellValue("E".($number + 13), '-');
                $sheet->getStyle('D'.($number + 13).':E'.($number + 13))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Bold and double underline total row
                $sheet->getStyle("A".($number + 13).":I".($number + 13))->getFont()->setBold(true);
                $sheet->getStyle("D".($number + 13).":D".($number + 13))->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE);
                $sheet->getStyle("G".($number + 13).":H".($number + 13))->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE);
                $sheet->getStyle("I".($number + 13).":I".($number + 13))->getBorders()->getRight()->setBorderStyle(Border::BORDER_MEDIUM);
                $sheet->getStyle("I".($number + 13).":I".($number + 13))->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("I".($number + 13).":I".($number + 13))->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);

                // end every loop
                $number = $number + 6;
            }

            $number= $number + 9;
            $sheet->mergeCells('E'.($number).':F'.($number));
            $range = "A".$number.":I". $number; 
            $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);
            $sheet->getStyle($range)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('D3D3D3');
            $sheet->getRowDimension($number); 

            $range = "A".($number + 1).":I". ($number + 1); 
            $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);
            $sheet->getRowDimension($number + 1); 

            $richText = new RichText();
            $rt = $richText->createTextRun("Total Fund Allocation");
            $rt->getFont()->setName('Times New Roman')->setSize(10);
            $sheet->setCellValue('E'.$number, $richText);

            $richText = new RichText();
            $rt = $richText->createTextRun("Patients Served");
            $rt->getFont()->setName('Times New Roman')->setSize(10);
            $sheet->setCellValue('G'.$number, $richText);

            $richText = new RichText();
            $rt = $richText->createTextRun("Total Actual Approved");
            $rt->getFont()->setName('Times New Roman')->setSize(10);
            $sheet->setCellValue('H'.$number, $richText);

            $richText = new RichText();
            $rt = $richText->createTextRun("Balance");
            $rt->getFont()->setName('Times New Roman')->setSize(10);
            $sheet->setCellValue('I'.$number, $richText);

            $richText = new RichText();
            $rt = $richText->createTextRun("GRAND TOTAL");
            $rt->getFont()->setBold(true)->setName('Times New Roman')->setSize(10);
            $sheet->setCellValue('A'.$number + 1, $richText);

            $sheet->setCellValue("G".($number + 1), $main_result['overall']['patients']); 
            $sheet->setCellValue("H".($number + 1), $main_result['overall']['total']);
            $sheet->getStyle('G'.($number + 1).':H'.($number + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('H'.($number + 1).':H'. ($number + 1))->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('G'.($number + 1).':G'. ($number + 1))->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('A'.($number + 4).':H'.($number + 6))->getFont()->setBold(true);
            $sheet->getStyle('A'.($number + 4).':H'.($number + 6))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

            // Signatures section (if needed)
            $sheet->setCellValue("A".($number + 4), "Prepared by:");
            $sheet->setCellValue("A".($number + 6), "MICHAEL JEI E. WAMAR, LPT");
            $sheet->setCellValue("A".($number + 7), "Health Program Officer II");
            // $sheet->setCellValue("A28", "Designation:");
            // $sheet->setCellValue("A29", "Date:");

            $sheet->setCellValue("C".($number + 4), "Noted by:");
            $sheet->setCellValue("C".($number + 6), "CHERYL B. OBELLO");
            $sheet->setCellValue("C".($number + 7), "MAIPP Coordinator");
            // $sheet->setCellValue("D28", "Chief Accountant");
            // $sheet->setCellValue("D29", "Date:");

            $sheet->setCellValue("E".($number + 4), "Certified correct:");
            $sheet->setCellValue("E".($number + 6), "ANGIELINE T. ADLAON, MBA, CPA");
            $sheet->setCellValue("E".($number + 7), "Accountant III");
            // $sheet->setCellValue("G28", "Medical Center Chief");
            // $sheet->setCellValue("G29", "Date:");

            $sheet->setCellValue("H".($number + 4), "Approved by:");
            $sheet->setCellValue("H".($number + 6), "JOSHUA G. BRILLANTES, MD, MPH, CESO IV");
            $sheet->setCellValue("H".($number + 7), "Director IV");
            // $sheet->setCellValue("G28", "Medical Center Chief");
            // $sheet->setCellValue("G29", "Date:");

            // Output preparation
            ob_start();
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            $xlsData = ob_get_contents();
            ob_end_clean();

            // Filename
            $filename = 'Annex-A2-'. $year . '.xlsx';

            // Set headers
            header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
            header("Content-Disposition: attachment; filename=$filename");
            header("Pragma: no-cache");
            header("Expires: 0");

            // Output the file
            return $xlsData;
            exit;
        }

        return view('facility.fur_consolidated_a',[
            'data' => $main_result,
            'type' => $request->data_type,
            'year' => $year
        ]);
    }

    public function consoB(Request $request){

        $data = AnnexB::where('status', 2)
            ->with([
                'trans',
                'patient'
            ]);
        if($request->excel){
            [$year, $month] = explode('-', $request->date_selection);

            return $this->consoBExcel($month, $year);
        }    

        if ($request->date_selection) {
            [$year, $month] = explode('-', $request->date_selection);
        
            $data->whereYear('month_year', $year)
                ->whereMonth('month_year', $month);
        }

        if ($request->data_type) {
            $type = $request->data_type;
        
            if ($type == 1) {
                $data->where('opd', '!=', 1);
            } elseif ($type == 2) {
                $data->where('opd', 1);
            }
        }        
            
        $keyword = $request->keyword;

        if($request->viewAll){
            $keyword = '';
        }

        if($keyword){
            $data->where(function ($query) use ($keyword) {
                $query->where('lname', 'LIKE', "%{$keyword}%")
                      ->orWhere('fname', 'LIKE', "%{$keyword}%")
                      ->orWhere('mname', 'LIKE', "%{$keyword}%");
            });
        }
        
        return view('facility.fur_consolidated_b',[
            'data' => $data->paginate(50),
            'keyword' => $keyword,
            'date' => $request->date_selection,
            'type' => $request->data_type
        ]);
    }

    private function consoBExcel($month, $year){
        $facility = "CENTRAL VISAYAS CENTER FOR HEALTH DEVELOPMENT ";

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Adjust column widths
        $sheet->getColumnDimension('A')->setWidth(2);  
        $sheet->getColumnDimension('B')->setWidth(5); 
        $sheet->getColumnDimension('C')->setWidth(30); 
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20); 
        $sheet->getColumnDimension('G')->setWidth(12); 
        $sheet->getColumnDimension('H')->setWidth(12); 
        $sheet->getColumnDimension('I')->setWidth(12); 
        $sheet->getColumnDimension('J')->setWidth(12); 
        $sheet->getColumnDimension('K')->setWidth(12); 
        $sheet->getColumnDimension('L')->setWidth(12); 
        $sheet->getColumnDimension('M')->setWidth(25);
        $sheet->getColumnDimension('N')->setWidth(20);
        $sheet->getColumnDimension('O')->setWidth(15);
        $sheet->getColumnDimension('P')->setWidth(15);
        $sheet->getColumnDimension('Q')->setWidth(15);
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('DOH Logo');
        $drawing->setDescription('Department of Health Logo');
        $drawing->setPath(public_path('images/doh-logo.png')); 

        $drawing->setWidth($drawing->getWidth() * 0.21);
        $drawing->setHeight($drawing->getHeight() * 0.21);

        $drawing->setCoordinates('D2');
        $drawing->setOffsetX(5);  
        $drawing->setOffsetY(10); 

        $drawing->setWorksheet($sheet);

        $richText = new RichText();

        $normalText1 = $richText->createTextRun("Republic of the Philippines\nDepartment of Health\n");

        $boldText = $richText->createTextRun("Medical Assistance to Indigent Patients (MAIP) Program - List of All Patients Served\n");
        $boldText->getFont()->setBold(true); 

        $italicText = $richText->createTextRun("For the Month of ");
        $italicText->getFont()->setItalic(true); 
        
        $underlinedDate = $richText->createTextRun(date('F', mktime(0, 0, 0, $month, 10)).' '.$year);
        $underlinedDate->getFont()->setUnderline(Font::UNDERLINE_SINGLE); 
        $sheet->setCellValue('C2', $richText);

        $sheet->mergeCells('C2:Q2');
        $sheet->getStyle('C2')->getAlignment()->setWrapText(true);
        $sheet->getStyle('C2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C2')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(2)->setRowHeight(80); 

        $drawing1 = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing1->setName('Malasakit Logo');
        $drawing1->setDescription('MPU Logo');
        $drawing1->setPath(public_path('images/malasakit.png')); 

        $drawing1->setCoordinates('P2');
        $drawing1->setOffsetX(5); 
        $drawing1->setOffsetY(5); 

        $drawing1->setWorksheet($sheet);

        $sheet->setCellValue('C4', 'Name of Hospital:');
        $sheet->setCellValue('D4', $facility);

        $range = "B8:Q8"; 
        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);
        $sheet->getStyle($range)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('B7B7B7');

        $range2 = "B9:Q9"; 
        $sheet->getStyle($range2)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);
        $sheet->getStyle($range2)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('B7B7B7');

        $range3 = "B10:Q10"; 
        $sheet->getStyle($range3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);
        $sheet->getStyle($range3)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('B7B7B7');

        $range4 = "B11:Q11"; 
        $sheet->getStyle($range4)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);
        $sheet->getStyle($range4)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('B7B7B7');

        $sheet->setCellValue('C5', 'Region:');
        $sheet->setCellValue('D5', 'VII');

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("SERVICE WARD IN-PATIENTS");
        $normalText->getFont()->setBold(false); 

        $sheet->setCellValue('C8', $richText1);
        $sheet->getRowDimension(8)->setRowHeight(25); 

        $sheet->mergeCells('C8:Q8');

        $sheet->getStyle('C8')->getAlignment()->setWrapText(true);
        $sheet->getStyle('C8')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C8')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->mergeCells('B9:B11');
        $sheet->setCellValue('B9', 'No.');
        $sheet->getStyle('B9:B11')->getFont()->setBold(true);

        $sheet->mergeCells('C9:C11');
        $richText3 = new RichText();
        $normalText2 = $richText3->createTextRun("Name of Patient (Last Name, First Name, Middle Name)");
        $normalText2->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue('C9', $richText3);
        $sheet->getStyle('C9')->getAlignment()->setWrapText(true); 
        $sheet->getStyle($sheet->calculateWorksheetDimension())->getFont()->setSize(9);

        $sheet->mergeCells('D9:D11');
        $richText4 = new RichText();
        $normalText3 = $richText4->createTextRun("MAIP CODE (Generated from MAIS)");
        $normalText3->getFont()->setBold(true)->setSize(9);
        $sheet->setCellValue('D9', $richText4);

        $sheet->mergeCells('E9:E11');
        $richText6 = new RichText();
        $normalText5 = $richText6->createTextRun("Type of Medical Assistance Provided");
        $normalText5->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue('E9', $richText6);

        $sheet->mergeCells('F9:F10'); 
        $richText7 = new RichText();
        $normalText6 = $richText7->createTextRun("Total Actual Charges ");
        $normalText6->getFont()->setBold(true)->setSize(9);
        $normalText7 = $richText7->createTextRun("(without any medications)");
        $normalText7->getFont()->setBold(false)->setSize(9); 

        $sheet->setCellValue('F9', $richText7);
        $sheet->getStyle('F9')->getAlignment()->setWrapText(true);

        $sheet->mergeCells('G9:L9');
        $sheet->setCellValue('G9', 'Hospital Bill/Medical Assistance');
        $sheet->getStyle('G9')->getFont()->setBold(true)->setSize(9);
        $sheet->getRowDimension(9)->setRowHeight(45); 

        $sheet->getStyle('F9:G9')->getAlignment()->setWrapText(true);
        $sheet->getStyle('F9:G9')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F9:G9')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('F11', 'A');
        $sheet->getStyle('F11')->getFont()->setBold(true)->setSize(9);

        $richText8 = new RichText();
        $normalText8 = $richText8->createTextRun("Senior Citizen/PWD");
        $normalText8->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue('G10', $richText8);
        $sheet->getStyle('G10')->getAlignment()->setWrapText(true);

        $richText9 = new RichText();
        $normalText9 = $richText9->createTextRun("PHILHEALTH (case rate)");
        $normalText9->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue('H10', $richText9);
        $sheet->getStyle('H10')->getAlignment()->setWrapText(true);

        $sheet->setCellValue('I10', 'PCSO');
        $sheet->getStyle('I10')->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle('I10')->getAlignment()->setIndent(1); 

        $sheet->setCellValue('J10', 'DSWD');
        $sheet->getStyle('J10')->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle('J10')->getAlignment()->setIndent(1); 

        $sheet->mergeCells('K10:L10');
        $richText10 = new RichText();
        $normalText10 = $richText10->createTextRun("Others (please specify)");
        $normalText10->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue('K10', $richText10);
        $sheet->getStyle('K10')->getAlignment()->setWrapText(true);

        $sheet->getStyle('G10:L10')->getAlignment()->setWrapText(true);
        $sheet->getStyle('G10:L10')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G10:L10')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->getStyle('F11:L11')->getAlignment()->setWrapText(true);
        $sheet->getStyle('F11:L11')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F11:L11')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('G11', 'B');
        $sheet->getStyle('G11')->getFont()->setBold(true)->setSize(9);

        $sheet->setCellValue('H11', 'C');
        $sheet->getStyle('H11')->getFont()->setBold(true)->setSize(9);

        $sheet->setCellValue('I11', 'D');
        $sheet->getStyle('I11')->getFont()->setBold(true)->setSize(9);

        $sheet->setCellValue('J11', 'E');
        $sheet->getStyle('J11')->getFont()->setBold(true)->setSize(9);

        $sheet->mergeCells('K11:L11');
        $sheet->setCellValue('K11', 'F');
        $sheet->getStyle('K11')->getFont()->setBold(true)->setSize(9);

        $sheet->mergeCells('M9:M10');
        $richText11 = new RichText();
        $normalText11 = $richText11->createTextRun("Total Actual Charges");
        $normalText11->getFont()->setBold(true)->setSize(9); 
        $italicText11 = $richText11->createTextRun("(with deductions of the following: Philhealth, PCSO, Senior Citizen, PWD, DSWD, LGU,HMOs, Insurance & others)");
        $italicText11->getFont()->setItalic(true)->setSize(9); // ✅ Set font size to 10
        $sheet->setCellValue('M9', $richText11);
        $sheet->getStyle('M9')->getAlignment()->setWrapText(true); 

        $sheet->setCellValue('M11', 'A -  SUM(B:F) = G');
        $sheet->getStyle('M11')->getFont()->setBold(true)->setSize(9);

        $sheet->mergeCells('N9:N10');
        $richText12 = new RichText();
        $normalText12 = $richText12->createTextRun("Assistance to Professional Fee through MAIP (not more than 50% of the approved assistance)");
        $normalText12->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue('N9', $richText12);
        $sheet->getStyle('N9')->getAlignment()->setWrapText(true); 

        $sheet->setCellValue('N11', 'H');
        $sheet->getStyle('N11')->getFont()->setBold(true)->setSize(9);

        $sheet->mergeCells('O9:O10');
        $richText13 = new RichText();
        $normalText13 = $richText13->createTextRun("Hospital Bill/Medical Assistance through MAIP");
        $normalText13->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue('O9', $richText13);
        $sheet->getStyle('O9')->getAlignment()->setWrapText(true); 

        $sheet->setCellValue('O11', 'I');
        $sheet->getStyle('O11')->getFont()->setBold(true)->setSize(9);
        
        $sheet->mergeCells('P9:P10');
        $richText14 = new RichText();
        $normalText14 = $richText14->createTextRun("Total Actual Approved Assistance through MAIP (Utilized Amount)");
        $normalText14->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue('P9', $richText14);
        $sheet->getStyle('P9')->getAlignment()->setWrapText(true); 

        $sheet->setCellValue('P11', 'H + I = J');
        $sheet->getStyle('P11')->getFont()->setBold(true)->setSize(9);

        $sheet->mergeCells('Q9:Q10');
        $richText15 = new RichText();
        $normalText15 = $richText15->createTextRun("Percent of Excess Net Bill/Charges covered by MAIP");
        $normalText15->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue('Q9', $richText15);
        $sheet->getStyle('Q9')->getAlignment()->setWrapText(true); 

        $sheet->setCellValue('Q11', 'J / G = K');
        $sheet->getStyle('Q11')->getFont()->setBold(true)->setSize(9);

        $sheet->getStyle('B9:F9')->getAlignment()->setWrapText(true);
        $sheet->getStyle('B9:F9')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B9:F9')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
    
        //service-ward
        $data = AnnexB::whereMonth('month_year', $month)
            ->where('status', 2)
            ->whereYear('month_year', $year)
            ->where('excess', 1)->where('opd', 0)
            ->with([
                'trans',
                'patient'
            ])->get();



        $pfee_service = 0;
        $hbill_service = 0;
        $total_actual_service = 0;

        $annexb1 = [];

        foreach($data as $index => $row){
            
            $fullName = $row->patient->lname . ', ' . $row->patient->fname;

            if($row->patient->mname && $row->patient->mname != 'N/A') {
                $fullName .= ' ' . $row->patient->mname;
            }

            $transTotal = $row->trans?->final_bill ?? 0;
            $senior = $row->senior ?? 0;
            $phic = $row->phic ?? 0;
            $pcso = $row->pcso ?? 0;
            $dswd = $row->dswd ?? 0;
            $o_amount = $row->o_amount ?? 0;
            $approved_assistance = $row->trans?->total ?? 0;
            $actual_charges = ($row->trans?->final_bill ?? 0 ) - ($senior + $phic + $pcso + $dswd + $o_amount);
            $ratio = ($actual_charges > 0 && $approved_assistance > 0)
                ? ($approved_assistance / $actual_charges) * 100
                : 0;
            $p_fee = $row->trans?->p_fee ?? 0;
            $h_bill = $row->trans?->h_bill ?? 0;

            $pfee_service = $pfee_service + $p_fee;
            $hbill_service = $hbill_service + $h_bill;
            $total_actual_service = $total_actual_service + $transTotal;

            $annexb1[] = [
                $index + 1, 
                $fullName,
                $row->patient->patient_code,
                $row->type,
                $transTotal,
                $senior,
                $phic,
                $pcso,
                $dswd,
                $o_amount,
                $row->others,
                $actual_charges,
                $p_fee,
                $h_bill, 
                $approved_assistance,
                $ratio .'%',
            ];
        }

        $sheet->fromArray($annexb1, null, 'B12');

        $sheet->getStyle('F12:K'. (count($annexb1) + 12))
            ->getNumberFormat()->setFormatCode('#,##0.00');

        $sheet->getStyle('M12:Q'. (count($annexb1) + 12))
            ->getNumberFormat()->setFormatCode('#,##0.00');

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'font' => [
                'name' => 'Times New Roman',
                'size' => 10,
            ],
        ];
        
        $sheet->getStyle('B12:Q' . (count($annexb1) + 11))->applyFromArray($styleArray);
        
        $sheet->getStyle('C12:E' . (count($annexb1) + 11))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        
        $sheet->getStyle('F12:Q' . (count($annexb1) + 11))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        
        $number = count($annexb1) + 15;
        $sheet->mergeCells("C" . ($number - 3) . ":D" . ($number - 3));

        $richText3 = new RichText();
        $normalText2 = $richText3->createTextRun("TOTAL SERVICE WARD IN-PATIENTS");
        $normalText2->getFont()->setBold(true)->setSize(9);
        
        $sheet->setCellValue("C" . ($number - 3), $richText3);
        
        $sheet->getStyle("C" . ($number - 3) . ":D" . ($number - 3))
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);        
                $sheet->getStyle("F" . ($number - 3) . ":Q" . ($number - 3))
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);  

        $deduction = $data->sum('phic') + $data->sum('pcso') + $data->sum('dswd') + $data->sum('o_amount');
        $actual = $total_actual_service - $deduction;

        $sheet->setCellValue("F" . ($number-3), $total_actual_service);
        $sheet->setCellValue("G" . ($number-3), $data->sum('senior'));
        $sheet->setCellValue("H" . ($number-3), $data->sum('phic'));
        $sheet->setCellValue("I" . ($number-3), $data->sum('pcso'));
        $sheet->setCellValue("J" . ($number-3), $data->sum('dswd'));
        $sheet->setCellValue("K" . ($number-3), $data->sum('o_amount'));
        $sheet->setCellValue("M" . ($number-3), $actual);
        $sheet->setCellValue("N" . ($number-3), $pfee_service);
        $sheet->setCellValue("O" . ($number-3), $hbill_service);
        $sheet->setCellValue("P" . ($number-3), $pfee_service + $hbill_service);

        // payward

        $range = "B" . $number . ":Q" . $number; 
        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);
        $sheet->getStyle($range)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('B7B7B7');

        $range2 = "B" . ($number + 1). ":Q" .  ($number + 1); 
        $sheet->getStyle($range2)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);
        $sheet->getStyle($range2)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('B7B7B7');

        $range3 = "B" . ($number + 2). ":Q" .  ($number + 2); 
        $sheet->getStyle($range3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);
        $sheet->getStyle($range3)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('B7B7B7');

        $range4 = "B" . ($number + 3). ":Q" .  ($number + 3); 
        $sheet->getStyle($range4)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);
        $sheet->getStyle($range4)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('B7B7B7');

        $richText11 = new RichText();
        $normalText = $richText11->createTextRun("PAYWARD IN-PATIENTS");
        $normalText->getFont()->setBold(false);
        $sheet->getRowDimension($number)->setRowHeight(25); 

        $cell1 = 'C' . $number; 
        $sheet->setCellValue($cell1, $richText11);

        $sheet->mergeCells("C{$number}:Q{$number}");
        $sheet->getStyle("C{$number}:Q{$number}")->getAlignment()->setWrapText(true);
        $sheet->getStyle("C{$number}:Q{$number}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("C{$number}:Q{$number}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->getStyle("B" . ($number + 1) . ":F" . ($number + 1))->getAlignment()->setWrapText(true);
        $sheet->getStyle("B" . ($number + 1) . ":F" . ($number + 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("B" . ($number + 1) . ":F" . ($number + 1))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->mergeCells("B" . ($number + 1) . ":B" . ($number + 3));
        $sheet->setCellValue("B" . ($number + 1), 'No.');
        $sheet->getStyle("B" . ($number + 1) . ":B" . ($number + 3))->getFont()->setBold(true)->setSize(9);

        $sheet->mergeCells("C" . ($number + 1) . ":C" . ($number + 3));
        $richText3 = new RichText();
        $normalText2 = $richText3->createTextRun("Name of Patient (Last Name, First Name,Middle Name)");
        $normalText2->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue("C" . ($number + 1), $richText3);
        $sheet->getStyle("C" . ($number + 1))->getAlignment()->setWrapText(true); 

        $sheet->mergeCells("D" . ($number + 1) . ":D" . ($number + 3));
        $richText4 = new RichText();
        $normalText3 = $richText4->createTextRun("MAIP CODE (Generated from MAIS)");
        $normalText3->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue("D" . ($number + 1), $richText4);

        $sheet->mergeCells("E" . ($number + 1) . ":E" . ($number + 3));
        $richText6 = new RichText();
        $normalText5 = $richText6->createTextRun("Type of Medical Assistance Provided");
        $normalText5->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue("E" . ($number + 1), $richText6);

        $sheet->mergeCells("F" . ($number + 1) . ":F" . ($number + 2));
        $richText7 = new RichText();
        $normalText6 = $richText7->createTextRun("Total Actual Charges ");
        $normalText6->getFont()->setBold(true)->setSize(9);
        $normalText7 = $richText7->createTextRun("(without any medications)");
        $normalText7->getFont()->setBold(false)->setSize(9);

        $sheet->setCellValue("F" . ($number + 1), $richText7);
        $sheet->getStyle("F" . ($number + 1))->getAlignment()->setWrapText(true);

        $sheet->mergeCells("G" . ($number + 1) . ":L" . ($number + 1));
        $sheet->setCellValue("G" . ($number + 1), 'Hospital Bill/Medical Assistance');
        $sheet->getStyle("G" . ($number + 1))->getFont()->setBold(true)->setSize(9);
        $sheet->getRowDimension($number + 1)->setRowHeight(45); 

        $sheet->getStyle("G" . ($number + 1) . ":L" . ($number + 1))->getAlignment()->setWrapText(true);
        $sheet->getStyle("G" . ($number + 1) . ":L" . ($number + 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("G" . ($number + 1) . ":L" . ($number + 1))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->setCellValue("F" . ($number + 3), 'A');
        $sheet->getStyle("F" . ($number + 3))->getFont()->setBold(true)->setSize(9);

        $richText8 = new RichText();
        $normalText8 = $richText8->createTextRun("Senior Citizen/PWD");
        $normalText8->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue("G" . ($number + 2), $richText8);
        $sheet->getStyle("G" . ($number + 2))->getAlignment()->setWrapText(true);

        $richText9 = new RichText();
        $normalText9 = $richText9->createTextRun("PHILHEALTH (case rate)");
        $normalText9->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue("H" . ($number + 2), $richText9);
        $sheet->getStyle("H" . ($number + 2))->getAlignment()->setWrapText(true);

        $sheet->setCellValue("I" . ($number + 2), 'PCSO');
        $sheet->getStyle("I" . ($number + 2))->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle("I" . ($number + 2))->getAlignment()->setIndent(1); 

        $sheet->setCellValue("J" . ($number + 2), 'DSWD');
        $sheet->getStyle("J" . ($number + 2))->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle("J" . ($number + 2))->getAlignment()->setIndent(1); 

        $sheet->mergeCells("K" . ($number + 2) . ":L" . ($number + 2));
        $richText10 = new RichText();
        $normalText10 = $richText10->createTextRun("Others (please specify)");
        $normalText10->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue("K" . ($number + 2), $richText10);
        $sheet->getStyle("K" . ($number + 2))->getAlignment()->setWrapText(true);

        $sheet->getStyle("G" . ($number + 2) . ":L" . ($number + 2))->getAlignment()->setWrapText(true);
        $sheet->getStyle("G" . ($number + 2) . ":L" . ($number + 2))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("G" . ($number + 2) . ":L" . ($number + 2))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->getStyle("F" . ($number + 3) . ":L" . ($number + 3))->getAlignment()->setWrapText(true);
        $sheet->getStyle("F" . ($number + 3) . ":L" . ($number + 3))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("F" . ($number + 3) . ":L" . ($number + 3))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->setCellValue("G" . ($number + 3), 'B');
        $sheet->getStyle("G" . ($number + 3))->getFont()->setBold(true)->setSize(9);

        $sheet->setCellValue("H" . ($number + 3), 'C');
        $sheet->getStyle("H" . ($number + 3))->getFont()->setBold(true)->setSize(9);

        $sheet->setCellValue("I" . ($number + 3), 'D');
        $sheet->getStyle("I" . ($number + 3))->getFont()->setBold(true)->setSize(9);

        $sheet->setCellValue("J" . ($number + 3), 'E');
        $sheet->getStyle("J" . ($number + 3))->getFont()->setBold(true)->setSize(9);

        $sheet->mergeCells("K" . ($number + 3) . ":K" . ($number + 3));
        $sheet->setCellValue("K" . ($number + 3), 'F');
        $sheet->getStyle("K" . ($number + 3))->getFont()->setBold(true)->setSize(9);

        $sheet->mergeCells("M" . ($number + 1) . ":M" . ($number + 2));
        $richText11 = new RichText();
        $normalText11 = $richText11->createTextRun("Total Actual Charges ");
        $normalText11->getFont()->setBold(true)->setSize(9); 
        $italicText11 = $richText11->createTextRun("(with deductions of the following: Philhealth, PCSO, Senior Citizen, PWD, DSWD, LGU,HMOs, Insurance & others)");
        $italicText11->getFont()->setItalic(true)->setSize(9); // ✅ Set font size to 10
        $sheet->setCellValue("M" . ($number + 1), $richText11);
        $sheet->getStyle("M" . ($number + 1))->getAlignment()->setWrapText(true); 

        $sheet->setCellValue("M" . ($number + 3), 'A -  SUM(B:F) = G');
        $sheet->getStyle("M" . ($number + 3))->getFont()->setBold(true)->setSize(9);

        $sheet->mergeCells("N" . ($number + 1) . ":N" . ($number + 2));
        $richText12 = new RichText();
        $normalText12 = $richText12->createTextRun("Assistance to Professional Fee through MAIP (not more than 50% of the approved assistance)");
        $normalText12->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue("N" . ($number + 1), $richText12);
        $sheet->getStyle("N" . ($number + 1))->getAlignment()->setWrapText(true); 

        $sheet->setCellValue("N" . ($number + 3), 'H');
        $sheet->getStyle("N" . ($number + 3))->getFont()->setBold(true)->setSize(9);

        $sheet->mergeCells("O" . ($number + 1) . ":O" . ($number + 2));
        $richText13 = new RichText();
        $normalText13 = $richText13->createTextRun("Hospital Bill/Medical Assistance through MAIP");
        $normalText13->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue("O" . ($number + 1), $richText13);
        $sheet->getStyle("O" . ($number + 1))->getAlignment()->setWrapText(true); 
        // return 1;
        $sheet->setCellValue("O" . ($number + 3), 'I');
        $sheet->getStyle("O" . ($number + 3))->getFont()->setBold(true)->setSize(9);
        
        $sheet->mergeCells("P" . ($number + 1) . ":P" . ($number + 2));
        $richText14 = new RichText();
        $normalText14 = $richText14->createTextRun("Total Actual Approved Assistance through MAIP (Utilized Amount)");
        $normalText14->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue("P" . ($number + 1), $richText14);
        $sheet->getStyle("P" . ($number + 1))->getAlignment()->setWrapText(true); 

        $sheet->setCellValue("P" . ($number + 3), 'H + I = J');
        $sheet->getStyle("P" . ($number + 3))->getFont()->setBold(true)->setSize(9);

        $sheet->mergeCells("Q" . ($number + 1) . ":Q" . ($number + 2));
        $richText15 = new RichText();
        $normalText15 = $richText15->createTextRun("Percent of Excess Net Bill/Charges covered by MAIP");
        $normalText15->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue("Q" . ($number + 1), $richText15);
        $sheet->getStyle("Q" . ($number + 1))->getAlignment()->setWrapText(true); 

        $sheet->setCellValue("Q" . ($number + 3), 'J / G = K');
        $sheet->getStyle("Q" . ($number + 3))->getFont()->setBold(true)->setSize(9);

        $sheet->getStyle('M9:Q11')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('M9:Q11')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        
        $range = "M" . ($number + 1) . ":Q" . ($number + 3);
        $sheet->getStyle($range)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($range)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $data2 = AnnexB::where('status', 2)
            ->whereMonth('month_year', $month)
            ->whereYear('month_year', $year)
            ->where('excess', '!=', 1)->where('opd', 0)
            ->with([
                'trans',
                'patient'
            ])->get();

        $pfee_payward = 0;
        $hbill_payward = 0;
        $total_actual_payward = 0;

        $annexb2 = [];
                
        foreach($data2 as $index => $row){
            
            $fullName = $row->patient->lname . ', ' . $row->patient->fname;

            if($row->patient->mname && $row->patient->mname != 'N/A') {
                $fullName .= ' ' . $row->patient->mname;
            }

            $transTotal = $row->trans?->final_bill ?? 0;
            $senior = $row->senior ?? 0;
            $phic = $row->phic ?? 0;
            $pcso = $row->pcso ?? 0;
            $dswd = $row->dswd ?? 0;
            $o_amount = $row->o_amount ?? 0;
            $approved_assistance = $row->trans?->total ?? 0;
            $actual_charges = ($row->trans?->final_bill ?? 0 ) - ($senior + $phic + $pcso + $dswd + $o_amount);
            $ratio = ($actual_charges > 0 && $approved_assistance > 0)
                ? ($approved_assistance / $actual_charges) * 100
                : 0;
            $p_fee = $row->trans?->p_fee ?? 0;
            $h_bill = $row->trans?->h_bill ?? 0;

            $pfee_payward = $pfee_payward + $p_fee;
            $hbill_payward = $hbill_payward + $h_bill;
            $total_actual_payward = $total_actual_payward + $transTotal;

            $annexb2[] = [
                $index + 1, 
                $fullName,
                $row->patient->patient_code,
                $row->type,
                $transTotal,
                $senior,
                $phic,
                $pcso,
                $dswd,
                $o_amount,
                $row->others,
                $actual_charges,
                $p_fee,
                $h_bill, 
                $approved_assistance,
                $ratio .'%',
            ];
        }

        $sheet->fromArray($annexb2, null, "B" . ($number + 4));

        $styleArray2 = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER, 
            ],
            'font' => [
                'name' => 'Times New Roman',
                'size' => 10,
            ],
        ];
        
        $number2 = count($annexb2) + $number + 7;

        $sheet->getStyle('F12:K'. ($number2 + 1))
            ->getNumberFormat()->setFormatCode('#,##0.00');

        $sheet->getStyle('M12:Q'. ($number2 + 1))
            ->getNumberFormat()->setFormatCode('#,##0.00');

        $rangeV2 = "B" . ($number + 4) . ":Q" . ($number + 3 + count($annexb2));
        
        $sheet->getStyle($rangeV2)->applyFromArray($styleArray2);
        
        $sheet->getStyle("C" . ($number + 4) . ":E" . ($number + 3 + count($annexb2)))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        
        $sheet->getStyle("F" . ($number + 4) . ":Q" . ($number + 3 + count($annexb2)))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        

        $set_data = $number + count($annexb2) + 6;
        
        $sheet->mergeCells("C" . ($set_data -2) . ":D" . ($set_data - 2));
        $richText3 = new RichText();
        $normalText2 = $richText3->createTextRun("TOTAL PAYWARD IN-PATIENTS");
        $normalText2->getFont()->setBold(true)->setSize(9);

        $sheet->setCellValue("C" . ($set_data-2), $richText3);
        $sheet->getStyle("C" . ($set_data-2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->getStyle("F" . ($set_data -2) . ":Q" . ($set_data - 2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $deduction = $data2->sum('phic') + $data2->sum('pcso') + $data2->sum('dswd') + $data2->sum('o_amount');
        $actual = $total_actual_payward - $deduction;

        $sheet->setCellValue("F" . ($set_data-2), $total_actual_payward);
        $sheet->setCellValue("G" . ($set_data-2), $data2->sum('senior'));
        $sheet->setCellValue("H" . ($set_data-2), $data2->sum('phic'));
        $sheet->setCellValue("I" . ($set_data-2), $data2->sum('pcso'));
        $sheet->setCellValue("J" . ($set_data-2), $data2->sum('dswd'));
        $sheet->setCellValue("K" . ($set_data-2), $data2->sum('o_amount'));
        $sheet->setCellValue("M" . ($set_data-2), $actual);
        $sheet->setCellValue("N" . ($set_data-2), $pfee_payward);
        $sheet->setCellValue("O" . ($set_data-2), $hbill_payward);
        $sheet->setCellValue("P" . ($set_data-2), $pfee_payward + $hbill_payward);

        $range = "B" . $number2 . ":Q" . $number2; 
        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);
        $sheet->getStyle($range)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('B7B7B7');

        $range2 = "B" . ($number2 + 1). ":Q" .  ($number2 + 1); 
        $sheet->getStyle($range2)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);
        $sheet->getStyle($range2)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('B7B7B7');

        $range3 = "B" . ($number2 + 2). ":Q" .  ($number2 + 2); 
        $sheet->getStyle($range3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);
        $sheet->getStyle($range3)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('B7B7B7');

        $range4 = "B" . ($number2 + 3). ":Q" .  ($number2 + 3);
        $sheet->getStyle($range4)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);
        $sheet->getStyle($range4)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('B7B7B7');

        $sheet->getStyle("B" . ($number2 + 1) . ":Q" . ($number2 + 1))->getAlignment()->setWrapText(true);
        $sheet->getStyle("B" . ($number2 + 1) . ":Q" . ($number2 + 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("B" . ($number2 + 1) . ":Q" . ($number2 + 1))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $range = "M" . ($number2 + 1) . ":Q" . ($number2 + 3);
        $sheet->getStyle($range)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($range)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $richText11 = new RichText();
        $normalText = $richText11->createTextRun("OPD PATIENTS");
        $normalText->getFont()->setBold(false);

        $sheet->getRowDimension($number2)->setRowHeight(25); 

        $cell = 'C' . $number2; 

        $sheet->setCellValue($cell, $richText11);
        $sheet->mergeCells("C{$number2}:Q{$number2}");

        $sheet->getStyle("C{$number2}:Q{$number2}")->getAlignment()->setWrapText(true);
        $sheet->getStyle("C{$number2}:Q{$number2}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("C{$number2}:Q{$number2}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->mergeCells("B" . ($number2 + 1) . ":B" . ($number2 + 3));
        $sheet->setCellValue("B" . ($number2 + 1), 'No.');
        $sheet->getStyle("B" . ($number2 + 1) . ":B" . ($number2 + 3))->getFont()->setBold(true)->setSize(9);

        $sheet->mergeCells("C" . ($number2 + 1) . ":C" . ($number2 + 3));
        $richText3 = new RichText();
        $normalText2 = $richText3->createTextRun("Name of Patient (Last Name, First Name, Middle Name)");
        $normalText2->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue("C" . ($number2 + 1), $richText3);
        $sheet->getStyle("C" . ($number2 + 1))->getAlignment()->setWrapText(true); 
        $sheet->getRowDimension($number2 + 1)->setRowHeight(55); 

        $sheet->mergeCells("D" . ($number2 + 1) . ":D" . ($number2 + 3));
        $richText4 = new RichText();
        $normalText3 = $richText4->createTextRun("MAIP CODE (Generated from MAIS)");
        $normalText3->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue("D" . ($number2 + 1), $richText4);

        $sheet->mergeCells("E" . ($number2 + 1) . ":E" . ($number2 + 3));
        $richText6 = new RichText();
        $normalText5 = $richText6->createTextRun("Type of Medical Assistance Provided");
        $normalText5->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue("E" . ($number2 + 1), $richText6);

        $sheet->mergeCells("F" . ($number2 + 1) . ":F" . ($number2 + 2));
        $richText7 = new RichText();
        $normalText6 = $richText7->createTextRun("Total Actual Charges with Professional Fee (without any deduction )");
        $normalText6->getFont()->setBold(true)->setSize(9); 

        $sheet->setCellValue("F" . ($number2 + 1), $richText7);
        $sheet->getStyle("F" . ($number2 + 1))->getAlignment()->setWrapText(true);

        $mergeRange = "G" . ($number2 + 1) . ":O" . ($number2 + 3); 
        $sheet->mergeCells($mergeRange);

        $sheet->getStyle($mergeRange)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '000000'], 
            ],
            'font' => [
                'color' => ['rgb' => 'FFFFFF'], 
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $mergeRange1 = "Q" . ($number2 + 1) . ":Q" . ($number2 + 3); 
        $sheet->mergeCells($mergeRange);

        $sheet->getStyle($mergeRange1)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '000000'], 
            ],
            'font' => [
                'color' => ['rgb' => 'FFFFFF'], 
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->mergeCells("P" . ($number2 + 1) . ":P" . ($number2 + 2));
        $richText14 = new RichText();
        $normalText14 = $richText14->createTextRun("Total Actual Approved Assistance through MAIP (Utilized Amount)");
        $normalText14->getFont()->setBold(true)->setSize(9); 
        $sheet->setCellValue("P" . ($number2 + 1), $richText14);
        $sheet->getStyle("P" . ($number2 + 1))->getAlignment()->setWrapText(true); 

        $sheet->setCellValue("P" . ($number2 + 3), 'B');
        $sheet->getStyle("P" . ($number2 + 3))->getFont()->setBold(true)->setSize(9);

        $data3 = AnnexB::where('status', 2)
            ->whereMonth('month_year', $month)
            ->whereYear('month_year', $year)
            ->where('opd', 1)
            ->with([
                'trans',
                'patient'
            ])->get();

        $pfee_opd = 0;
        $hbill_opd = 0;
        $total_actual_opd = 0;

        $annexb3 = [];
                
        foreach($data3 as $index => $row){
            
            $fullName = $row->patient->lname . ', ' . $row->patient->fname;

            if($row->patient->mname && $row->patient->mname != 'N/A') {
                $fullName .= ' ' . $row->patient->mname;
            }

            $transTotal = $row->trans?->final_bill ?? 0;
            $approved_assistance = $row->trans?->total ?? 0;
            $p_fee = $row->trans?->p_fee ?? 0;
            $h_bill = $row->trans?->h_bill ?? 0;

            $pfee_opd = $pfee_opd + $p_fee;
            $hbill_opd = $hbill_opd + $h_bill;
            $total_actual_opd = $total_actual_opd + $transTotal;

            $annexb3[] = [
                $index + 1, 
                $fullName,
                $row->patient->patient_code,
                $row->type,
                $transTotal,
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '', 
                $approved_assistance,
            ];
        }

        $startRow = $number2 + 4;
        $sheet->fromArray($annexb3, null, "B" . $startRow);

        $lastRow = $startRow + count($annexb3) - 1;

        $styleArray3 = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER, 
            ],
            'font' => [
                'name' => 'Times New Roman',
                'size' => 10,
            ],
        ];

        $rangeV3 = "B$startRow:Q$lastRow";

        $sheet->getStyle($rangeV3)->applyFromArray($styleArray3);

        $sheet->getStyle("C$startRow:E$lastRow")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->getStyle("F$startRow:Q$lastRow")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);


        for ($row = $startRow; $row <= $lastRow; $row++) {
            $mergeRange = "G$row:O$row";
            $sheet->mergeCells($mergeRange);
            $sheet->getStyle($mergeRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('000000');
            $sheet->getStyle($mergeRange)->getFont()->getColor()->setARGB('FFFFFF');
        }

        for ($row = $startRow; $row <= $lastRow; $row++) {
            $mergeRange = "Q$row:Q$row";
            $sheet->mergeCells($mergeRange);
            $sheet->getStyle($mergeRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('000000');
            $sheet->getStyle($mergeRange)->getFont()->getColor()->setARGB('FFFFFF');
        }
        
        $number3 = $number2 + count($annexb3) + 3;
        
        $sheet->mergeCells("C" . ($number3 +1) . ":D" . ($number3 + 1));
        $richText3 = new RichText();
        $normalText2 = $richText3->createTextRun("TOTAL OPD PATIENTS");
        $normalText2->getFont()->setBold(true)->setSize(9);
        $sheet->setCellValue("C" . ($number3 +1), $richText3);
        $sheet->getStyle("C" . ($number3 +1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->setCellValue("F" . ($number3 +1), $total_actual_opd);
        $sheet->setCellValue("P" . ($number3 +1), $pfee_opd + $hbill_opd);

        $sheet->fromArray($annexb3, null, "B" . ($number2 + 4));

        $styleArray3 = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'font' => [
                'name' => 'Times New Roman',
                'size' => 10,
            ],
        ];

        $sheet->getStyle($rangeV3)->applyFromArray($styleArray);

        $number4 = $number3 + 3;

        $sheet->mergeCells("C" . ($number4) . ":D" . ($number4));
        $richText3 = new RichText();
        $normalText2 = $richText3->createTextRun("GRAND TOTAL");
        $normalText2->getFont()->setBold(true)->setSize(9);
        $sheet->setCellValue("C" . ($number4), $richText3);

        $grandTotal = $total_actual_service + $total_actual_payward + $total_actual_opd;
        $sheet->setCellValue("F" . ($number4), $grandTotal);
        $sheet->getStyle("F" . ($number4))->getFont()->setBold(true)->setSize(9);

        $range = "F{$number4}:Q{$number4}";
        $sheet->getStyle($range)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE);

        $totalApproved = $pfee_opd + $pfee_payward + $pfee_service + $hbill_opd + $hbill_payward + $hbill_service;
        $sheet->setCellValue("P" . ($number4), $totalApproved);
        $sheet->getStyle("P" . ($number4))->getFont()->setBold(true)->setSize(9);

        $sheet->getStyle('F12:Q' . ($number4))
            ->getNumberFormat()->setFormatCode('#,##0.00');

        $richText3 = new RichText();
        $normalText2 = $richText3->createTextRun('Summary');
        $normalText2->getFont()->setBold(true)->setSize(9);
        $sheet->setCellValue("C" . ($number4 + 4), $richText3);

        $richText3 = new RichText();
        $normalText2 = $richText3->createTextRun("Number of Patients Served");
        $normalText2->getFont()->setSize(9); 
        $sheet->setCellValue("D" . ($number4 + 5), $richText3);
        $sheet->getStyle("D" . ($number4 + 5), $richText3)->getAlignment()->setWrapText(true);
        $sheet->getStyle("D" . ($number4 + 5))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $richText3 = new RichText();
        $normalText2 = $richText3->createTextRun("Total Actual Charges");
        $normalText2->getFont()->setSize(9); 
        $sheet->setCellValue("E" . ($number4 + 5), $richText3);
        $sheet->getStyle("E" . ($number4 + 5), $richText3)->getAlignment()->setWrapText(true);
        $sheet->getStyle("E" . ($number4 + 5))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $richText3 = new RichText();
        $normalText2 = $richText3->createTextRun("Total Actual Approved Assistance through MAIP (Utilized Amount)");
        $normalText2->getFont()->setSize(9);
        $sheet->setCellValue("F" . ($number4 + 5), $richText3);
        $sheet->getStyle("F" . ($number4 + 5), $richText3)->getAlignment()->setWrapText(true);

        $sheet->getStyle("D" . ($number4 + 5) . ":F" . ($number4 + 5))
        ->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
        ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle('E12:F' . ($number4 + 9))
            ->getNumberFormat()->setFormatCode('#,##0.00');

        $sheet->getStyle("D" . ($number4 + 5) . ":F" . ($number4 + 5))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("D" . ($number4 + 6) . ":F" . ($number4 + 9))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $sheet->setCellValue("C" . ($number4 + 6), "Service ward In-patients");
        $sheet->setCellValue("C" . ($number4 + 7), "Pay ward In-patients");
        $sheet->setCellValue("C" . ($number4 + 8), "OPD Patients");

        $sheet->setCellValue("D" . ($number4 + 6), count($data));
        $sheet->setCellValue("D" . ($number4 + 7), count($data2));
        $sheet->setCellValue("D" . ($number4 + 8), count($data3));

        $sheet->setCellValue("E" . ($number4 + 6), $total_actual_service);
        $sheet->setCellValue("E" . ($number4 + 7), $total_actual_payward);
        $sheet->setCellValue("E" . ($number4 + 8), $total_actual_opd);

        $sheet->setCellValue("F" . ($number4 + 6), $pfee_service + $hbill_service);
        $sheet->setCellValue("F" . ($number4 + 7), $pfee_payward + $hbill_payward);
        $sheet->setCellValue("F" . ($number4 + 8), $pfee_opd + $hbill_opd);

        $sheet->getRowDimension(($number4 + 5))->setRowHeight(60); 
        $range = "C" . ($number4 + 5) . ":F" . $number4 + 8; 
        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);

        $sheet->setCellValue("C" . ($number4 + 9), "TOTAL");
        $sheet->setCellValue("D" . ($number4 + 9), count($data) + count($data2) + count($data3));
        $sheet->setCellValue("E" . ($number4 + 9), $total_actual_service + $total_actual_payward + $total_actual_opd);
        $sheet->setCellValue("F" . ($number4 + 9), $totalApproved);
        $sheet->getStyle("D" . ($number4 + 9). ":F" . ($number4 + 9))->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE);

        // Output preparation
        ob_start();
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();

        // Filename
        $filename = 'ANNEX-B-'. date('F', mktime(0, 0, 0, $month, 10)).$year. '.xlsx';

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
