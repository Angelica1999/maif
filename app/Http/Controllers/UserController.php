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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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

    public function verifyuser($id){
        $registration = Registration::where('id', $id)->first();
        if($registration){
            MailVerification::dispatch($registration);
            return redirect()->back();
        }else{
            return redirect()->back()->with('error', 'User not found.');
        }
    }

    public function save(){
        $registration = Registration::where('id', 1)->first();
        $user = new OnlineUser();
            $user->fname = $registration->fname;
            $user->lname = $registration->lname;
            $user->email = $registration->email;
            $user->pass_change = 0;
            $user->verified_by = Auth::user()->userid;
            $user->username = "cadayday12";
            $user->type_identity = $registration->identity_type;
            $user->user_type = $registration->user_type;
            $user->contact_no = $registration->contact_no;
            $user->gender = $registration->gender;
            $user->birthdate = $registration->birthdate;
            $user->password =  Hash::make("password");
            $user->roles = 0;
            $user->save();
    }
}
