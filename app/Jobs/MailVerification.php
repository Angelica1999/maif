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
    protected $type;
    protected $userid;

    public function __construct($registration, $type, $userid)
    {
        $this->registration = $registration;
        $this->type = $type;
        $this->userid = $userid;
    }

    public function handle()
    {
        $registration = $this->registration;
        $type = $this->type;
        $userid = $this->userid;
   
        $password = $this->generateUniquePassword();
        $username = $registration->lname.$registration->identity_type.$registration->id;

        if($type == 'verify'){
            $user = new OnlineUser();
            $user->fname = $registration->fname;
            $user->lname = $registration->lname;
            $user->email = $registration->email;
            $user->verified_by = $userid;
            $user->type_identity = $registration->identity_type;
            $user->user_type = $registration->user_type;
            $user->contact_no = $registration->contact_no;
            $user->gender = $registration->gender;
            $user->birthdate = $registration->birthdate;
            $user->roles = 0;
            $user->username = $username;
        }else{
            $user = OnlineUser::where('username', $registration->username)->first();
            $username = $user->username;
        }

        $user->pass_change = 0;
        $user->password =  Hash::make($password);

        try {
            if($this->sendMail($registration, $password, $username, $type)){
                $user->save();
                if ($type == 'verify') {
                    $registration->delete();
                }
                session()->flash('email_sent', true);
            }
        } catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
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

    private function sendMail($registration, $password, $username, $type)
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
            $mail->isHTML(true);    
            $mail->Subject = 'System Account Credentials';
            $mail->addEmbeddedImage('public\images\maipp_banner.png', 'unique_cid_for_image', 'image.jpg');
            // $link = "https://gletter.cvchd7.com/guaranteeletter";                              //Set email format to HTML
            $link = "http://localhost/guaranteeletter";                              //Set email format to HTML

            $mail->Body =
                '<html><body>
                <span style="text-align:right;">Username: '.$username.'<span><br>
                <span style="text-align:right;">Password: '.$password.'<span><br>
                <a href="https://gletter.cvchd7.com/guaranteeletter" style="text-align:right;">'.$link.'</a><br>
                    <div align="center">
                        <div align="center">
                            <img src="cid:unique_cid_for_image" width="100%"> 
                        </div>
                    </div>
                </body></html>
                ';
        
            return $mail->send();

        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }   
    }
}

