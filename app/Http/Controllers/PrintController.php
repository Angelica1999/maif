<?php

namespace App\Http\Controllers;
use App\Models\Patients;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Dv;
use App\Models\AddFacilityInfo;
use App\Models\Dv2;
use App\Models\Facility;
use App\Models\Fundsource;
use Illuminate\Support\Facades\Mail;
use App\Mail\PdfEmail;
use Illuminate\Support\Facades\DB;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Image;
use Illuminate\Support\Str;
use PDF;


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
        $patient = Patients::where('id',$patientid)->with('encoded_by')->first();
        if(!$patient){
            return redirect()->route('Home.index')->with('error', 'Patient not found.');
        }

        $data = [
            'title' => 'Welcome to MAIF',
            'date' => date('m/d/Y'),
            'patient' => $patient,
            'age' => $this->calculateAge($patient->dob)
        ];
        $options = [];
    
        $pdf = PDF::loadView('maif.print_patient', $data, $options);
        return $pdf->stream('patient.pdf');
    }

    public function sendPatientPdf($patientId) {

        $patient = Patients::where('id', $patientId)->with('facility')->first();
        if(!$patient){
            return redirect()->route('Home.index')->with('error', 'Patient not found.');
        }else{
            $facility = AddFacilityInfo::where('facility_id', $patient->facility_id)->first();
            if($facility->official_mail !== null || $facility->official_mail !== "" || $facility->official_mail !== "none"){
                $data = [
                    'title' => 'Welcome to MAIF',
                    'date' => date('m/d/Y'),
                    'patient' => $patient,
                    'age' => $this->calculateAge($patient->dob)
                ];
                $options = [];
                $recipientEmail = $facility->official_mail;
                $cc = str_replace(' ','',$facility->cc);
                $cc_mails = explode(',',  $cc);
                $pdf = PDF::loadView('maif.print_patient', $data, $options);
                $pdfFilePath = storage_path("app\pdfs");

                $pdf->save($pdfFilePath);
    
                try {
                    $this->sendMail($recipientEmail,$pdfFilePath,$cc_mails);
                    session()->flash('email_sent', true);

                } catch (Exception $e) {
                    session()->flash('email_unsent', true);
                    $chaki[] = $recipientEmail;
                }
            
            }
        } 
        return redirect()->route('home');

    }
    
    public function sendMultiple(Request $request){
        $ids = $request->input('send_mails');
        $ids = array_map('intval', explode(',', $ids[0]));
        $chaki = [];
        if($ids !== null || $ids !== ''){
            $patients = Patients::whereIn('id', $ids)->with('facility')->get();
            if($patients){
                foreach($patients as $patient){
                    $facility = AddFacilityInfo::where('facility_id', $patient->facility_id)->first();
                    if($facility->official_mail !== null || $facility->official_mail !== "" || $facility->official_mail !== "none"){
                        $data = [
                            'title' => 'Welcome to MAIF',
                            'date' => date('m/d/Y'),
                            'patient' => $patient,
                            'age' => $this->calculateAge($patient->dob)
                        ];
                        $options = [];
                        $recipientEmail = $facility->official_mail;
                        $cc = str_replace(' ','',$facility->cc);
                        $cc_mails = explode(',',  $cc);
                        
                        $pdf = PDF::loadView('maif.print_patient', $data, $options);
                        $pdfFilePath = storage_path("app\pdfs");
                        $pdf->save($pdfFilePath);
            
                        try {
                            $this->sendMail($recipientEmail,$pdfFilePath,$cc_mails);
                            session()->flash('email_sent', true);
    
                        } catch (Exception $e) {
                            session()->flash('email_unsent', true);
                            $chaki[] = $recipientEmail;
                        }
                    }
                }
            }
            return redirect()->route('home');
        }
        
    }

    private function sendMail($recipientEmail, $pdfFilePath, $cc_mails){
        try{
            $email_doh = 'maipp@ro7.doh.gov.ph';
            $email_password = 'betvdmvyribwcyba';
            $mail = new PHPMailer(true);
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = $email_doh;                             //SMTP username
            $mail->Password   = $email_password;                        //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
            $mail->SMTPDebug = 0; 
            //Recipients
            $mail->setFrom($email_doh, 'DOH-CVCHD MAIFIP');
            $mail->addAddress($recipientEmail);     //Add a recipient
            $mail->addReplyTo($email_doh);
            if($cc_mails !==null && $cc_mails !=="" ){
                foreach ($cc_mails as $ccEmail) {
                    if($ccEmail !== ""){
                        $mail->addCC($ccEmail);
                    }
                }
            }
            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Guarante Letters for the following patients';
            $mail->addEmbeddedImage('public\images\maipp_banner.png', 'unique_cid_for_image', 'image.jpg');
            $mail->Body    =
                '<html><body>
                <span style="text-align:right;">Please acknowledge receipt of this email. Thank you.<span>
                    <div align="center">
                        <div align="center">
                            <img src="cid:unique_cid_for_image" width="100%"> 
                        </div>
                    </div>
                </body></html>
                ';
            $mail->addAttachment($pdfFilePath, "guarantee_letter.pdf");
            $mail->send();
            unlink($pdfFilePath);
        }catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function dvPDF(Request $request, $dvId) {
        $dv = Dv::find($dvId);
        $facility = Facility::find($dv->facility_id);
        $saa = explode(',', $dv->fundsource_id);
        $saa = str_replace(['[', ']', '"'],'',$saa);
        $all = [];
        foreach($saa as $id){
            $all []= $id;
        }
        $fund_source = Fundsource::whereIn('id', $all)->get();
        if(!$dv){
            return redirect()->route('Home.index')->with('error', 'Patient not found.');
        }
        $data = [
            'dv'=> $dv,
            'facility' => $facility,
            'fund_source' => $fund_source
        ];
    
        $pdf = PDF::loadView('dv.dv_pdf', $data);
        $pdf->setPaper('Folio');
        return $pdf->stream('dv.pdf');
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
        return $pdf->stream('dv2.pdf');
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
    
            $imageWidth = 500; // Set your desired width
            $imageHeight = 600; // Set your desired height
            $image = imagecreate($imageWidth, $imageHeight);
    
            $backgroundColor = imagecolorallocate($image, 255, 255, 255); // white
            $textColor = imagecolorallocate($image, 0, 0, 0); // black
            $fontpath = 'public\admin\fonts\Karla\Karla-Bold.ttf';
            $fontSize = 12;
            $fontHeight = imagettfbbox($fontSize, 0, $fontpath, 'Sample')['1'];

            $y = 50;
            imagettftext($image, $fontSize + 4, 0, 100, $y, $textColor, $fontpath, 'Disbursement Voucher V2');
            $y += $fontSize + 5;
            imagettftext($image, $fontSize, 0, 190, $y, $textColor, $fontpath, 'MAIF-IPP');

            $y += $fontSize + 30;
            $boxWidth = 380;
            $boxHeight = 50;

            imagefilledrectangle($image, 60, $y, 65 + $boxWidth, $y + $boxHeight, $backgroundColor);
            imagerectangle($image, 60, $y, 65 + $boxWidth, $y + $boxHeight, $textColor);
            $text = htmlspecialchars($dv2[0]->facility);
            $maxWidth = 50; 
            $wrappedText = wordwrap($text, $maxWidth, "\n", true);
            $lines = explode("\n", $wrappedText);
            
            foreach ($lines as $lineNumber => $line) {
                $yPosition = $y + ($boxHeight + $fontHeight) / 2 + ($lineNumber * $fontHeight);
                imagettftext($image, $fontSize, 0, 70, $yPosition, $textColor, $fontpath, $line);
                $y += 10;

            }
            $y += $fontSize + 30;
            $boxWidth = 400;
            $boxHeight = 70;

            foreach ($dv2 as $data) {

                imagefilledrectangle($image, 45, $y, 50 + $boxWidth, $y + $boxHeight, $backgroundColor);
                imagerectangle($image, 45, $y, 50 + $boxWidth, $y + $boxHeight, $textColor);

                imagettftext($image, $fontSize, 0, 160, $y + ($boxHeight + $fontHeight) / 2 - 15, $textColor, $fontpath, (!empty($data->ref_no) ? htmlspecialchars($data->ref_no) : ""));
                $y += $fontSize + 5;

                imagettftext($image, $fontSize, 0, 70, $y + ($boxHeight + $fontHeight) / 2 -10, $textColor, $fontpath, (!empty($data->lname1) ? htmlspecialchars($data->lname1) : $data->lname));
                imagettftext($image, $fontSize, 0, 320, $y + ($boxHeight + $fontHeight) / 2 -10, $textColor, $fontpath, (!empty($data->amount) ? $data->amount : 0.00));
                $y += $fontSize + 5;

                imagettftext($image, $fontSize, 0, 70, $y + ($boxHeight + $fontHeight) / 2-10, $textColor, $fontpath, (isset($data->lname_2) ? htmlspecialchars($data->lname_2) : ($data->lname2 != 0 ? $data->lname2 : '')));

                $y += $boxHeight + 5;
            }

            $y += $fontSize + 15;

            $boxWidth = 270;
            $boxHeight = 30;

            $verticalCenter = $y + ($boxHeight + $fontHeight) / 2;

            imagefilledrectangle($image, 180, $y, 180 + $boxWidth, $y + $boxHeight, $backgroundColor);
            imagerectangle($image, 180, $y, 180 + $boxWidth, $y + $boxHeight, $textColor);

            imagettftext($image, $fontSize, 0, 185, $verticalCenter, $textColor, $fontpath, 'Total Amount: PHP ' . number_format($total, 2, '.', ','));
            $y += $boxHeight + 5;

            $filename = $route_no . '.png';
            header('Content-Type: image/jpeg');
            // header('Content-Disposition: attachment; filename="'. $filename.'"');

            imagejpeg($image);
            imagedestroy($image);
        } catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
        
}
