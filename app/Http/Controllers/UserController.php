<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registration;


class UserController extends Controller{

    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function users(){
        $registration = Registration::with('facility','proponent')->paginate(50);
        return view('users',[
            'registrations' => $registration
        ]);
    }
}
