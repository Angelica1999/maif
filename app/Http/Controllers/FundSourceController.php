<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fundsource;

class FundSourceController extends Controller
{
    public function fundSource(Request $request) {
        $fundsources = Fundsource::paginate(15);

        return view('fundsource.fundsource',[
            'fundsources' => $fundsources,
            'keyword' => $request->keyword
        ]);
    }
}
