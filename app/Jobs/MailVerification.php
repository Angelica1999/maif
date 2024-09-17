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

class MailVerification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $registration;

    public function __construct($registration)
    {
        $this->registration = $registration;
    }

    public function handle()
    {
        $registration = $this->registration;

        $password = $this->generateUniquePassword();
        $randomNumber1 = rand(100, 999); 
        $username = $registration->lname.$registration->identity_type.$registration->id;
        $user = new OnlineUser();
        $user->fname = $registration->fname;
        $user->lname = $registration->lname;
        $user->email = $registration->email;
        $user->pass_change = 0;
        $user->verified_by = Auth::user()->userid;
        $user->username = $username;
        $user->type_identity = $registration->identity_type;
        $user->user_type = $registration->user_type;
        $user->contact_no = $registration->contact_no;
        $user->gender = $registration->gender;
        $user->birthdate = $registration->birthdate;
        $user->password =  Hash::make($password);
        $user->roles = 0;
        try {
            if($this->sendMail($registration, $password, $username)){
                $user->save();
                session()->flash('email_sent', true);
            }
        } catch (\Exception $e) {
            return "Mail sending failed: " . $e->getMessage();
        }
        
    }

    private function generateUniquePassword($length = 12) {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        $charactersLength = strlen($characters);
    
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[rand(0, $charactersLength - 1)];
        }
    
        return $password;
    }

    private function sendMail($registration, $password, $username)
    {
        try {
           
            $recipientEmail = $registration->email;
            $email_doh = 'maipp@ro7.doh.gov.ph';
            $email_password = 'ezfdilafwbdoutit';
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
            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'System Account Activation Credentials';
            $mail->addEmbeddedImage('public\images\maipp_banner.png', 'unique_cid_for_image', 'image.jpg');
            $link = "http://localhost/guaranteeletter";
            $mail->Body =
                '<html><body>
                <span style="text-align:right;">Username: '.$username.'<span><br>
                <span style="text-align:right;">Password: '.$password.'<span><br>
                <a href="http://localhost/guaranteeletter" style="text-align:right;">'.$link.'</a><br>
                    <div align="center">
                        <div align="center">
                            <img src="cid:unique_cid_for_image" width="100%"> 
                        </div>
                    </div>
                </body></html>
                ';
            if($mail->send()){
                return true;
            }else{
                return false;
            }
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
        
    }
}

