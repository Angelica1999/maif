<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registration;
use App\Models\OnlineUser;
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
        $registration = Registration::find($id);

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
            return redirect()->back()->with('error', 'User not found.');
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
        $registration = OnlineUser::where('id', 1)->first();
        // $user = new OnlineUser();
        //     $user->fname = $registration->fname;
        //     $user->lname = $registration->lname;
        //     $user->email = $registration->email;
        //     $user->pass_change = 0;
        //     $user->verified_by = Auth::user()->userid;
            $registration->username = "Proponent7";
            // $user->type_identity = $registration->identity_type;
            // $user->user_type = $registration->user_type;
            // $user->contact_no = $registration->contact_no;
            // $user->gender = $registration->gender;
            // $user->birthdate = $registration->birthdate;
            $registration->password =  Hash::make("Proponent7.");
            // $user->roles = 0;
            $registration->save();
    }
}
