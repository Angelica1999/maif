<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Utilization;
use App\Models\Proponent;
use App\Models\Fundsource;
use App\Models\User;
use App\Models\Patients;
use App\Models\Facility;
use App\Models\Dv;
use App\Models\Dv2;
use App\Models\Group;
use App\Models\ProponentInfo;


class Dv3Controller extends Controller
{
    public function __construct(){
       $this->middleware('auth');
    }

    public function dv3(Request $request) {
        
        return view('dv3.dv3',[
            ]);
        }
    

}
