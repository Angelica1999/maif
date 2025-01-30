<?php

namespace App\Jobs;

use App\Models\OnlineUser;
use App\Models\Registration;
use App\Models\AddFacilityInfo;
use App\Models\MailHistory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use PDF;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AccountCancellation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $registration;
    protected $remarks;

    public function __construct($registration, $remarks)
    {
        $this->registration = $registration;
        $this->remarks = $remarks;

    }

    public function handle()
    {
        $registration = $this->registration;
        $remarks = $this->remarks;

        try {

            $recipientEmail = $registration->email;
            $email_doh = 'maipp@ro7.doh.gov.ph';
            $email_password = 'ngpxbtkftobporiw';
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $email_doh;
            $mail->Password   = $email_password;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            $mail->SMTPDebug = 0; // Set to 0 to disable debug output
            
            // Recipients
            $mail->setFrom($email_doh, 'DOH-CVCHD MAIFIP');
            $mail->addAddress($recipientEmail);
            $mail->addReplyTo($email_doh);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Account Verification Failed';
            $mail->addEmbeddedImage(public_path('images/maipp_banner.png'), 'unique_cid_for_image', 'image.jpg');
            $mail->Body = '
            <html><body>
                <span style="text-align:right;">Account verification failed with the reason: '.$remarks.'</span><br>
                <div align="center">
                    <div align="center">
                        <img src="cid:unique_cid_for_image" width="100%"> 
                    </div>
                </div>
            </body></html>
            ';
            
            if ($mail->send()) {
                $registration->delete();  // Call the delete method properly
            }

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

}

