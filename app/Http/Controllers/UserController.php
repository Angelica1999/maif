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
        $this->middleware('block.secure.nonadmin');
    }
    
    public function viewAccount($id){
        if(Auth::user()->userid == "2760" || "2680"){
            $user = OnlineUser::find($id);
            $data = [
                'type_identity' => $user->type_identity,
                'user_type' => $user->user_type,
                'expires_at' => now()->addMinutes(5)->timestamp,
            ];
            
            $secret = 'tJ,KU(Ef913kX@Jd.Wx+[9KW8#Mc\6o3kEYt#Zo]Gl/3\]Ch=A).9w@mEr*JsE?9';
            
            $payload = json_encode($data);
            $token = base64_encode($payload);
            
            $signature = hash_hmac('sha256', $payload, $secret);

            $url = "https://gletter.cvchd7.com/guaranteeletter/impersonate?token=$token&sig=$signature";
            return redirect()->away($url);
        }
    }

    public function users(Request $req){
        $users = OnlineUser::with('facility:id,name','proponent:id,proponent')->sortable();
        if($req->viewAll){
            $req->keyword = '';
        }else if($req->keyword){
            $users->where(function ($query) use ($req) {
                $query->where('lname', 'LIKE', "%{$req->keyword}%")
                    ->orWhere('fname', 'LIKE', "%{$req->keyword}%")
                    ->orWhereHas('facility', function ($subquery) use ($req) {
                    $subquery->where('name', 'LIKE', "%$req->keyword%");
                })->orWhereHas('proponent', function ($subquery) use ($req) {
                    $subquery->where('proponent', 'LIKE', "%$req->keyword%");
                });
            });
        }

        if($req->account_type){
            if($req->account_type == 1){
                $users->where('user_type', 1);
            }else if($req->account_type == 2){
                $users->where('user_type', 2);
            }else if($req->account_type == 3){
                $users->where('user_type', 3);
            }
        }

        return view('users',[
            'users' => $users->orderBy('id', 'desc')->paginate(50),
            'keyword' => $req->keyword,
            'type' => [1,2,3],
            'account_type' => $req->account_type
        ]);
    }

    public function usersActivation(Request $req){
        $registration = Registration::with('facility','proponent')->sortable();
      
        if($req->viewAll){
            $req->keyword = '';
        }else if($req->keyword){
            $registration->where(function ($query) use ($req) {
                $query->where('lname', 'LIKE', "%{$req->keyword}%")
                    ->orWhere('fname', 'LIKE', "%{$req->keyword}%")
                    ->orWhereHas('facility', function ($subquery) use ($req) {
                    $subquery->where('name', 'LIKE', "%$req->keyword%");
                })->orWhereHas('proponent', function ($subquery) use ($req) {
                    $subquery->where('proponent', 'LIKE', "%$req->keyword%");
                });
            });
        }

        if($req->account_type){
            if($req->account_type == 1){
                $registration->where('user_type', 1);
            }else if($req->account_type == 2){
                $registration->where('user_type', 2);
            }else if($req->account_type == 3){
                $registration->where('user_type', 3);
            }
        }

        return view('user_activation',[
            'registrations' => $registration->orderBy('id', 'desc')->paginate(50),
            'keyword' => $req->keyword,
            'type' => [1,2,3],
            'account_type' => $req->account_type
        ]);
    }

    public function deactivate($id){
        OnlineUser::where('id', $id)->update(['status' => 1]);
        return redirect()->back()->with('user_deactivation', true);
    }

    public function activate($id){
        OnlineUser::where('id', $id)->update(['status' => 0]);
        return redirect()->back()->with('user_activation', true);
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
