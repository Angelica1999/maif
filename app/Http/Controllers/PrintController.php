<?php

namespace App\Http\Controllers;
use App\Models\Patients;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Dv;
use App\Models\MailHistory;
use App\Models\AddFacilityInfo;
use App\Models\Dv2;
use App\Models\Facility;
use App\Models\Fundsource_Files;
use App\Models\Fundsource;
use App\Models\Proponent;
use App\Models\Dv3;
use Illuminate\Support\Facades\Mail;
use App\Mail\PdfEmail;
use Illuminate\Support\Facades\DB;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use \Mpdf\Mpdf;
use DNS1D;

// use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;

// use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Image;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\PreDV;
use App\Models\PreDVControl;
use App\Models\PreDVSAA;
use App\Models\PreDVExtension;
use App\Models\NewDV;
use PDF;

use App\Jobs\SendMultipleEmails;

class PrintController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function calculateAge($dob) {

        $dob = Carbon::parse($dob);
        $currentDate = Carbon::now();
        $age = $currentDate->diffInYears($dob);

        if ($age >= 1) {
            if ($dob->diffInMonths($currentDate) > 0) {
                return $age . ' y/o';
            } else {
                return $age . ' y/o';
            }
        } else {
            return $dob->diffInMonths($currentDate) . ' month' . ($dob->diffInMonths($currentDate) != 1 ? 's' : '');
        }
    }
    
    public function patientPdf(Request $request, $patientid) {
        $patient = Patients::where('id',$patientid)->with('encoded_by', 'province', 'muncity', 'barangay')->first();
        if(!$patient){
            return redirect()->route('Home.index')->with('error', 'Patient not found.');
        }

        $data = [
            'title' => 'Welcome to MAIF',
            'date' => date('m/d/Y'),
            'patient' => $patient,
            'age' => $this->calculateAge($patient->dob)
        ];

        $html = view('maif.print_patient', $data)->render();
        $mpdf = new Mpdf([
            'format' => 'A4'
        ]);

        $mpdf->WriteHTML($html);
        return $mpdf->Output('document.pdf', 'I');
        
        // $options = [];
    
        // $pdf = PDF::loadView('maif.print_patient', $data, $options);
        // return $pdf->stream('patient.pdf');
    }

    public function sendPatientPdf($patientId) {

        $ids = array($patientId);
        set_time_limit(0);

        if ($ids !== null || $ids !== '') {
            SendMultipleEmails::dispatch($ids);
            return redirect()->route('home')->with('status', 'Emails are being sent in the background.');
        }

        return redirect()->route('home')->with('status', 'No emails selected.');

    }

    public function sendMultiple(Request $request)
    {
        $ids = $request->input('send_mails');
        $ids = array_map('intval', explode(',', $ids[0]));
        set_time_limit(0);

        if($request->sent_type == 0){
            if ($ids !== null || $ids !== '') {
                SendMultipleEmails::dispatch($ids);
                return redirect()->route('home')->with('status', 'Emails are being sent in the background.');
            }
    
            return redirect()->route('home')->with('status', 'No emails selected.');
        }elseif($request->sent_type == 1){
            foreach($ids as $id){
                Patients::where('id', $id)->update([
                    'fc_status' => 'referred',
                    'sent_type' => 3
                ]);
                return redirect()->back()->with('process_gl', true);
            }
        }
    }

    public function dvPDF(Request $request, $dvId) {

        $dv = Dv::find($dvId);
        $facility = Facility::find($dv->facility_id);
        $saa = explode(',', $dv->fundsource_id);
        $saa = str_replace(['[', ']', '"'],'',$saa);
        $fund_source = [];
        foreach($saa as $id){
            $fund_source []= Fundsource::where('id', $id)->with('image')->first();
        }
        // return $fund_source;
        if(!$dv){
            return redirect()->route('Home.index')->with('error', 'Patient not found.');
        }

        $saa_source = [$fund_source[0]->saa, !Empty($fund_source[1]->saa)?$fund_source[1]->saa : '',  
                        !Empty($fund_source[2]->saa)?$fund_source[2]->saa : ''];
        $saa_amount = array_values(array_filter([$dv->amount1, !Empty($dv->amount2)?$dv->amount2 : 0,  
                        !Empty($dv->amount3)?$dv->amount3: 0], function($value){ return $value !== 0 && $value!== null;}));

        $total_overall = (float)str_replace(',', '', $dv->total_amount);
        if($dv->deduction1>3){
            $total = $total_overall/1.12;
        }else{
            $total = $total_overall;
        }

        $vatFormatted = number_format(str_replace(',','',$dv->deduction_amount1), 2, '.', '');
        $ewtFormatted = number_format(str_replace(',','',$dv->deduction_amount2), 2, '.', '');
        $result = number_format($vatFormatted + $ewtFormatted, 2, '.', ',');

        $imageData = base64_encode(file_get_contents(public_path('images/doh-logo.png')));
        $barcodePNG = DNS1D::getBarcodePNG($dv->route_no, 'C39E', 1, 28);

        $data = [
            'dv'=> $dv,
            'facility' => $facility,
            'fund_source' => $fund_source,
            'imageData' =>  $imageData,
            'total' => $total,
            'saa_source' => $saa_source,
            'saa_amount' => $saa_amount,
            'result' => $result,
            'barcodePNG' => $barcodePNG
        ];
        
        $html = view('dv.dv_pdf', $data)->render();
        $mpdf = new Mpdf([
            'format' => 'Folio',
            'strictVariables' => false
        ]);

        $mpdf->WriteHTML($html);
        $output = $mpdf->Output('dv.pdf', 'I');
        return $output;
    
        // $pdf = PDF::loadView('dv.dv_pdf', $data);
        // $pdf->setPaper('Folio');
        // return $pdf->stream('dv.pdf');
    }

    public function dv2Pdf($route_no) {

        $dv2 = Dv2::where('route_no', $route_no)
                ->leftJoin('patients as p1', 'dv2.lname', '=', 'p1.id')
                ->leftJoin('patients as p2', 'dv2.lname2', '=', 'p2.id')
                ->select('dv2.*', 'p1.lname as lname1', 'p2.lname as lname_2')
                ->get();
        $total = Dv2::where('route_no', $route_no)
                ->select(DB::raw('SUM(REPLACE(amount, ",", "")) as totalAmount'))
                ->first()->totalAmount;   
        if(!$dv2){
            return redirect()->route('dv2.dv2')->with('error', 'Disbursement Voucher Version 2 not found!');
        }
        $data = [
            'dv2'=> $dv2,
            'total' => $total
        ];
        $pdf = PDF::loadView('dv2.print_dv2', $data);
        $pdf->setPaper('A4');
        $filename = $dv2[0]->facility.' - '.number_format(str_replace(',','',$total), 2, '.', ',').' - '.$route_no . '.pdf';
        return $pdf->stream($filename);
    }

    public function dv2Image($route_no) {
   
        try {
            $dv2 = Dv2::where('route_no', $route_no)
                ->leftJoin('patients as p1', 'dv2.lname', '=', 'p1.id')
                ->leftJoin('patients as p2', 'dv2.lname2', '=', 'p2.id')
                ->select('dv2.*', 'p1.lname as lname1', 'p2.lname as lname_2')
                ->get();
            $total = Dv2::where('route_no', $route_no)
                ->select(DB::raw('SUM(REPLACE(amount, ",", "")) as totalAmount'))
                ->first()->totalAmount;
            
            $imageWidth = 600; 
            if(count($dv2) >= 95){
                $imageHeight = 620 + (count($dv2) * 85); 
            }else if(count($dv2) >= 10){
                $imageHeight = 620 + (count($dv2) * 75); 
            }else if(count($dv2) <= 10 && count($dv2) >= 5){
                $imageHeight = 620 + (count($dv2) * 65); 
            }else if(count($dv2) >= 5){
                $imageHeight = 620 + (count($dv2) * 40); 
            }else{
                $imageHeight = 620 + (count($dv2) * 30); 
            }
            $image = imagecreate($imageWidth, $imageHeight);
    
            $backgroundColor = imagecolorallocate($image, 255, 255, 255); // white
            $textColor = imagecolorallocate($image, 0, 0, 0); // black
            $fontpath = 'public\admin\fonts\Karla\Karla-Bold.ttf';
            $fontSize = 12;
            $fontHeight = imagettfbbox($fontSize, 0, $fontpath, 'Sample')['1'];

            $y = 70;
            imagettftext($image, $fontSize + 4, 0, 160, $y, $textColor, $fontpath, 'Disbursement Voucher V2');
            $y += $fontSize + 10;
            imagettftext($image, $fontSize, 0, 250, $y, $textColor, $fontpath, 'MAIF-IPP');

            $y += $fontSize + 30;
            $boxWidth = 455;
            $boxHeight = 60;

            imagefilledrectangle($image, 55, $y, 55 + $boxWidth, $y + $boxHeight, $backgroundColor);
            imagerectangle($image, 70, $y, 70 + $boxWidth, $y + $boxHeight, $textColor);
            $text = htmlspecialchars($dv2[0]->facility);
            $maxWidth = 50; 
            $wrappedText = wordwrap($text, $maxWidth, "\n", true);
            $lines = explode("\n", $wrappedText);

            foreach ($lines as $lineNumber => $line) {
                $yPosition = $y + ($boxHeight + $fontHeight) / 2 + ($lineNumber * $fontHeight);
                imagettftext($image, $fontSize, 0, 95, $yPosition, $textColor, $fontpath, $line);
                $y += 10;
            }
            
            $y += $fontSize + 50;
            $boxWidth = 500;
            $boxHeight = 70;

            foreach ($dv2 as $data) {

                if(strlen(htmlspecialchars($data->ref_no)) >= 40){
                    $boxHeight = 100;
                    imagefilledrectangle($image, 45, $y, 50 + $boxWidth, $y + $boxHeight, $backgroundColor);
                    imagerectangle($image, 45, $y, 50 + $boxWidth, $y + $boxHeight, $textColor);

                    $text = htmlspecialchars($data->ref_no);
                    $maxWidth = 37; 
                    $wrappedText = wordwrap($text, $maxWidth, "\n", true);
                    $lines = explode("\n", $wrappedText);

                    foreach ($lines as $lineNumber => $line) {
                         $yPosition = $y + ($boxHeight + $fontHeight) / 2 - 15 + ($lineNumber * $fontHeight);
                        imagettftext($image, $fontSize, 0, 190, $yPosition, $textColor, $fontpath, $line);
                        $y += 10;
                    }

                    $y += (count($lines) * $fontHeight) - 10;

                    imagettftext($image, $fontSize, 0, 70, $y + ($boxHeight + $fontHeight) / 2 -10, $textColor, $fontpath, (!empty($data->lname1) ? htmlspecialchars($data->lname1) : $data->lname));
                    imagettftext($image, $fontSize, 0, 400, $y + ($boxHeight + $fontHeight) / 2 -10, $textColor, $fontpath, (!empty($data->amount) ? $data->amount : 0.00));
                    $y += $fontSize + 5;

                    imagettftext($image, $fontSize, 0, 70, $y + ($boxHeight + $fontHeight) / 2-10, $textColor, $fontpath, (isset($data->lname_2) ? htmlspecialchars($data->lname_2) : ($data->lname2 != 0 ? $data->lname2 : '')));

                    $y += $boxHeight - 20;
                    $imageWidth = $imageWidth + 20; 
                    $imageHeight = $imageHeight + 50; 
                }else{
                    $boxHeight = 70;
                    imagefilledrectangle($image, 45, $y, 50 + $boxWidth, $y + $boxHeight, $backgroundColor);
                    imagerectangle($image, 45, $y, 50 + $boxWidth, $y + $boxHeight, $textColor);

                    imagettftext($image, $fontSize, 0, 190, $y + ($boxHeight + $fontHeight) / 2 - 15, $textColor, $fontpath, (!empty($data->ref_no) ? htmlspecialchars($data->ref_no) : ""));
                    $y += $fontSize + 5;

                    imagettftext($image, $fontSize, 0, 70, $y + ($boxHeight + $fontHeight) / 2 -10, $textColor, $fontpath, (!empty($data->lname1) ? htmlspecialchars($data->lname1) : $data->lname));
                    imagettftext($image, $fontSize, 0, 400, $y + ($boxHeight + $fontHeight) / 2 -10, $textColor, $fontpath, (!empty($data->amount) ? $data->amount : 0.00));
                    $y += $fontSize + 5;

                    imagettftext($image, $fontSize, 0, 70, $y + ($boxHeight + $fontHeight) / 2-10, $textColor, $fontpath, (isset($data->lname_2) ? htmlspecialchars($data->lname_2) : ($data->lname2 != 0 ? $data->lname2 : '')));

                    $y += $boxHeight - 20;
                    $imageWidth = $imageWidth + 20; 
                    $imageHeight = $imageHeight + 50; 
                }
                
            }

            $y += $fontSize + 15;

            $boxWidth = 300;
            $boxHeight = 50;

            $verticalCenter = $y + ($boxHeight + $fontHeight) / 2;

            imagefilledrectangle($image, 250, $y, 250 + $boxWidth, $y + $boxHeight, $backgroundColor);
            imagerectangle($image, 250, $y, 250 + $boxWidth, $y + $boxHeight, $textColor);

            imagettftext($image, $fontSize, 0, 260, $verticalCenter, $textColor, $fontpath, 'Total Amount: PHP ' . number_format($total, 2, '.', ','));
            $y += $boxHeight + 5;
            $filename = $dv2[0]->facility.' - '.number_format(str_replace(',','',$total), 2, '.', ',').' - '.$route_no . '.png';
            header('Content-Type: image/jpeg');
            // header('Content-Disposition: attachment; filename="'. $filename.'"');

            imagejpeg($image);
            imagedestroy($image);
        } catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function  dv3Pdf($route_no){
        $dv3 = Dv3::where('route_no', $route_no)             
                ->with([
                    'extension' => function ($query) {
                        $query->with([
                            'proponentInfo' => function ($query) {
                                $query->with(['proponent', 
                                'fundsource' => function ($query){
                                    $query->with('image');
                                }
                            ]);
                            }
                        ]);
                    },
                    'facility' => function ($query) {
                        $query->select(
                            'id',
                            'name',
                            'address'
                        );
                    },
                    'user' => function ($query) {
                        $query->select(
                            'userid',
                            'fname',
                            'lname'
                        );
                    }
                ])->first();
        $data = [
            'dv3'=> $dv3,
            'total' => $dv3->total
        ];
        $pdf = PDF::loadView('dv3.dv3_pdf', $data);
        $pdf->setPaper('Folio');
        return $pdf->stream('dv3.pdf');
    }

    private function route_image($route_no){
        $text_width = strlen($route_no);
        $width = 11.5 * $text_width;
        $height = 35;
    
        $image = imagecreatetruecolor($width, $height);
    
        imagealphablending($image, false);
        imagesavealpha($image, true);
    
        $transparent = imagecolorallocatealpha($image, 255, 255, 255, 127); 
        imagefilledrectangle($image, 0, 0, $width, $height, $transparent); 
        $text_color = imagecolorallocate($image, 0, 0, 0); 
        imagestring($image, 5, 10, 10, $route_no, $text_color);
    
        // Save the image
        $image_path = 'public/images/' .$route_no.'.png';
        imagepng($image, $image_path);
    
        // Destroy the image to free up memory
        imagedestroy($image);
    
        return $image_path; // Return the image path so it can be used later
    }    

    public function newDVPDF($id) {
        $this->genPreImage($id);
        set_time_limit(0);  // No time limit, will run indefinitely (or until the task finishes)
       
        $new = NewDV::where('predv_id', $id)->first();
        if($new){
            $pre_dv = PreDV::where('id', $id)->with(
                [
                    'user:userid,fname,lname,mname',
                    'facility:id,name,address', 
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
            $extension = PreDVExtension::where('pre_dv_id', $pre_dv->id)->pluck('id');
            $saas = PreDVSAA::whereIn('predv_extension_id', $extension)->with(
                ['saa'=> function ($query){
                    $query->with('image');
                }]
            )->get();

            $info = AddFacilityInfo::where('facility_id', $pre_dv->facility_id)->first();
            $controls = PreDVControl::whereIn('predv_extension_id', $extension)->get();  
            // return $saas;
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
                    'path' => ($group->first()->saa->image)?$group->first()->saa->image->path: '', 
                    'fundsource_id' => $group->first()->saa->id,
                    'vat' => ($info && $info->vat != null)? (float) $info->vat *  $group->sum('amount') / 100: 0,
                    'ewt' => ($info && $info->Ewt != null)? (float) $info->Ewt *  $group->sum('amount') / 100: 0
                ];
            });

            $barcodePNG = DNS1D::getBarcodePNG($new->route_no, 'C39E', 1, 28);
            $image_path = $this->route_image($new->route_no); 
            $data = [
                'result'=> $new,
                'pre_dv'=> $pre_dv,
                'fundsources' => $grouped,
                'control' => $control,
                'info' => $info,
                'amount' => $grouped->sum('amount'),
                'barcodePNG' => $barcodePNG,
                'route_no' => $new->route_no,
                'total_count' => count($grouped)
            ];
            // Determine which view to use based on grouped count
            $file = (count($grouped) > 16) ? 'pre_dv.new_pdf1' : 'pre_dv.new_pdf';
            $html = view($file, $data)->render();

            // Create the PDF
            $mpdf = new Mpdf([
                'format' => 'Folio',
                'strictVariables' => false,
                'externalImages' => true
            ]);
            $mpdf->WriteHTML($html);

            // Output the PDF
            $pdf_output = $mpdf->Output($new->route_no.'.pdf', 'I');

            // Delete the image after PDF generation
            if (file_exists($image_path)) {
                unlink($image_path); // Delete the file
            }

            return $pdf_output;
        }
    }
    
    public function prePDF($id) {

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

        if($pre_dv){

            $data = [
                'result'=> $pre_dv
            ];

            $pdf = PDF::loadView('pre_dv.pre_pdf', $data);
            $pdf->setPaper('Folio');
            
            $route = $pre_dv->new_dv->route_no;

            return $pdf->stream($route.'.pdf');
            
        }else{
            return 'no data found!';
        }
    }

    private function genPreImage($id) {
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
                                $query->with(['saa:id,saa']);
                            }
                        ]
                    );
                }
            ]
        )->first();
    
        $route_no = $pre_dv->new_dv->route_no;
    
        $data = [
            'result' => $pre_dv
        ];
    
        $height = 600;
        $width = 500;
        foreach ($pre_dv->extension as $row) {
            foreach ($row->controls as $row1) {
                $height += 160;
                $width += 20;
            }
            foreach ($row->saas as $row2) {
                $height += 25;
                $width += 5;
            }
        }
    
        $pdf = PDF::loadView('pre_dv.pre_pdf', $data);
        $pdf->setPaper([0, 0, 595.28, $height]);
    
        $pdfPath = storage_path("app/new_dv.pdf");
        $pdf->save($pdfPath);
    
        // $imageDir = storage_path('app/pre_dv'); 
        $imageDir = 'C:/Apache24/htdocs/guaranteeletter/storage/app/pre_dv';

        if (!file_exists($imageDir)) {
            mkdir($imageDir, 0755, true);
        }
    
        $pdftoppmPath = 'C:\\poppler-24.07.0\\Library\\bin\\pdftoppm.exe';
        $imageName = "{$route_no}"; 
        $command = "{$pdftoppmPath} -png -f 1 -l 1 {$pdfPath} " . escapeshellarg("{$imageDir}/{$imageName}") . " 2>&1";
        exec($command, $output, $returnVar);
    
        $filePath = "{$imageDir}/{$imageName}";
        // return $filePath;
    }

    public function preImage($id) {
        // $this->genPreImage($id); 
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
        
        if($pre_dv){

            $data = [
                'result' => $pre_dv
            ];

            $height = 600;
            $width = 500;
            foreach($pre_dv->extension as $row){
                foreach($row->controls as $row1){
                    $height = $height + 160;
                    $width = $width + 20;
                }
                foreach($row->saas as $row2){
                    $height = $height + 25;
                    $width = $width + 5;
                }
            }
            $pdf = PDF::loadView('pre_dv.pre_pdf', $data);
            // $pdf->setPaper('Folio');
            $pdf->setPaper([0, 0, 595.28, $height]); 

            $pdfPath = storage_path("app/new_dv.pdf");
            $pdf->save($pdfPath);

            $imagePath = storage_path('app/new_dv'); // Directory to save individual images
            $pdftoppmPath = 'C:\\poppler-24.07.0\\Library\\bin\\pdftoppm.exe';
            $command = "{$pdftoppmPath} -png -f 1 -l 1 {$pdfPath} " . escapeshellarg($imagePath) . " 2>&1";
            exec($command, $output, $returnVar);

            $filePath = storage_path('app/new_dv-1.png');

            if (!File::exists($filePath)) {
                abort(404);
            }

            $file = File::get($filePath);
            $type = File::mimeType($filePath);

            return Response::make($file, 200, [
                'Content-Type' => $type,
                'Content-Disposition' => 'inline; filename="' . $pre_dv->new_dv->route_no . '.png"',
            ]);  
        }else{
            return 'no data found!';
        }
    }

    public function roImage(){
        set_time_limit(0);  // No time limit, will run indefinitely (or until the task finishes)
        $funds = Fundsource_Files::get();
        foreach ($funds as $row) {
            // Get the original image path
            $imagePath = storage_path('app/' . $row->path);
            // Set the rotated image path, preserving the original filename
            $rotatedImagePath = storage_path('app/rotate/' . $row->path);
        
            // Ensure the directory for rotated images exists
            if (!file_exists(storage_path('app/rotate'))) {
                mkdir(storage_path('app/rotate'), 0777, true);
            }
        
            // Open the image from the uploads directory
            $image = imagecreatefromjpeg($imagePath);
        
            // Rotate the image 270 degrees
            $rotatedImage = imagerotate($image, 270, 0);
        
            // Save the rotated image to the rotate directory
            imagejpeg($rotatedImage, $rotatedImagePath);
        
            // Free memory
            imagedestroy($image);
            imagedestroy($rotatedImage);
        }
        
    }

    public function sampleMe($id) {
        $this->genPreImage($id);
        set_time_limit(0);  // No time limit, will run indefinitely (or until the task finishes)
       
        $new = NewDV::where('predv_id', $id)->first();
        if($new){
            $pre_dv = PreDV::where('id', $id)->with(
                [
                    'user:userid,fname,lname,mname',
                    'facility:id,name,address', 
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
            $extension = PreDVExtension::where('pre_dv_id', $pre_dv->id)->pluck('id');
            $saas = PreDVSAA::whereIn('predv_extension_id', $extension)->with(
                ['saa'=> function ($query){
                    $query->with('image');
                }]
            )->get();

            $info = AddFacilityInfo::where('facility_id', $pre_dv->facility_id)->first();
            $controls = PreDVControl::whereIn('predv_extension_id', $extension)->get();  
            // return $saas;
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
                    'path' => ($group->first()->saa->image)?$group->first()->saa->image->path: '', 
                    'fundsource_id' => $group->first()->saa->id,
                    'vat' => ($info && $info->vat != null)? (float) $info->vat *  $group->sum('amount') / 100: 0,
                    'ewt' => ($info && $info->Ewt != null)? (float) $info->Ewt *  $group->sum('amount') / 100: 0
                ];
            });

           
            $data = [
                'result'=> $new,
                'pre_dv'=> $pre_dv,
                'fundsources' => $grouped,
                'control' => $control,
                'info' => $info,
                'amount' => $grouped->sum('amount'),
                'route_no' => $new->route_no,
                'total_count' => count($grouped)
            ];
            // Determine which view to use based on grouped count
           
            $pdf = PDF::loadView('pre_dv.new_pdf1', $data);

            $pdf->setPaper('Folio');
            return $pdf->stream('dv.pdf');
        }
    }
    
}
