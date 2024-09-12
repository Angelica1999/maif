<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registration;
use App\Models\OnlineUser;
use App\Jobs\MailVerification;
use Illuminate\Support\Facades\Mail;
use App\Mail\PdfEmail;
use Illuminate\Support\Facades\DB;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller{

    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function users(){
        $registration = Registration::with('facility','proponent')->orderBy('id', 'desc')->paginate(50);
        $users = OnlineUser::with('facility','proponent')->orderBy('id', 'desc')->paginate(50);
        return view('users',[
            'registrations' => $registration,
            'users' => $users
        ]);
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

    public function verifyuser($id){
        $registration = Registration::where('id', $id)->first();
        if($registration){
            // MailVerification::dispatch($registration);
            try {
                $password = $this->generateUniquePassword();
                $randomNumber1 = rand(100, 999); 
                $username = $registration->lname.$registration->identity_type.$registration->id;
                // return $username;
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
                $mail->addAddress($recipientEmail);   
                $mail->addReplyTo($email_doh);
                // if ($cc_mails !== null && $cc_mails !== "") {
                //     foreach ($cc_mails as $ccEmail) {
                //         if ($ccEmail !== "") {
                //             $mail->addCC($ccEmail);
                //         }
                //     }
                // }
                //Content
                $mail->isHTML(true);                                  //Set email format to HTML
                $mail->Subject = 'System Account Activation';
                $mail->addEmbeddedImage('public\images\maipp_banner.png', 'unique_cid_for_image', 'image.jpg');
                $mail->Body =
                    '<html><body>
                    <span style="text-align:right;">Account Credentials:<span><br>
                    <span style="text-align:right;">Username: '.$username.'<span><br>
                    <span style="text-align:right;">Password: '.$password.'<span><br>
                    <a href="http://localhost/guaranteeletter/">http://localhost/guaranteeletter/</a>
                        <div align="center">
                            <div align="center">
                                <img src="cid:unique_cid_for_image" width="100%"> 
                            </div>
                        </div>
                    </body></html>
                    ';
                if($mail->send()){
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
                    $user->password = $password;
                    $user->roles = 0;
                    $user->save();
                }               
            } catch (Exception $e) {
                echo 'Error: ' . $e->getMessage();
            }
        }
    }
}
