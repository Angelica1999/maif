<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registration;
use App\Models\OnlineUser;
use App\Models\User;
use App\Jobs\MailVerification;
use App\Jobs\AccountCancellation;
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

    public function verifyuser($id)
    {
        $registration = Registration::find($id);

        if ($registration) {
            try {
                if (Auth::check()) {
                    MailVerification::dispatch($registration, 'verify', Auth::user()->userid);
                    return redirect()->back()->with('success', 'Verification email has been sent.');
                } else {
                    return redirect()->back()->with('error', 'Unauthorized action.');
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Failed to send verification email.');
            }
        } else {
            return redirect()->back()->with('error', 'User not found.');
        }
    }

    public function reset($id)
    {
        $registration = OnlineUser::find($id);

        if ($registration) {
            try {
                if (Auth::check()) {
                    MailVerification::dispatch($registration, 'reset', Auth::user()->userid);
                    return redirect()->back()->with('success', 'Verification email has been sent.');
                } else {
                    return redirect()->back()->with('error', 'Unauthorized action.');
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Failed to send verification email.');
            }
        } else {
            return redirect()->back()->with('not_found', true);
        }
    }

    public function cancel($id, Request $request){
        $registration = Registration::where('id', $id)->first();
        
        if($registration){
            AccountCancellation::dispatch($registration, $request->remarks);
            return redirect()->back();
        }else{
            return redirect()->back()->with('error', 'User not found.');
        }
    }

    public function save(){
        // $registration = OnlineUser::where('id', 1)->first();
        $user = new OnlineUser();
            $user->fname = 'Oronan';
            $user->lname = 'Angel';
            $user->email = 'lenga@gmail.com';
            $user->pass_change = 0;
            $user->verified_by = '2760';
            $user->username = "angel7";
            $user->type_identity = 3;
            $user->user_type = 0;
            $user->contact_no = '12325469875';
            $user->gender = 'F';
            $user->birthdate = '2024-09-18';
            $user->password =  Hash::make("angel7.");
            $user->roles = 0;
            $user->save();
    }

    public function mpu(Request $req){

        $check = User::where('userid', $req->user_id)->first();
        if($check){
            $user = new OnlineUser();
            $user->fname = $check->fname;
            $user->lname = $check->lname;
            $user->email = $req->email_add;
            $user->pass_change = 0;
            $user->verified_by = Auth::user()->userid;
            $user->username = $check->userid;
            $user->type_identity = 3;
            $user->user_type = 3;
            $user->contact_no = $req->contact_no;
            $user->gender = $req->gender;
            $user->birthdate = $req->dob;
            $user->password =  Hash::make("P@ssword77.");
            $user->roles = 0;
            $user->save();
            return redirect()->back()->with('activate_user', true);
        }else{
            return redirect()->back()->with('not_found', true);
        }
    }
}
