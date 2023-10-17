<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fundsource;
use App\Models\Facility;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FundSourceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function fundSource(Request $request) {
        $fundsources = Fundsource::
                        with([
                            'facility' => function ($query) {
                                $query->select(
                                    'id',
                                    DB::raw('name as description')
                                );
                            },
                            'encoded_by' => function ($query) {
                                $query->select(
                                    'id',
                                    'name'
                                );
                            }
                        ]);

        if($request->viewAll) {
            $request->keyword = '';
        }
        else if($request->keyword) {
            $fundsources = $fundsources->where('saa', 'LIKE', "%$request->keyword%");
        } 
        
        $fundsources = $fundsources
                        ->orderBy('id','desc')
                        ->paginate(15);

        return view('fundsource.fundsource',[
            'fundsources' => $fundsources,
            'keyword' => $request->keyword
        ]);
    }

    public function createFundSource() {
        $user = Auth::user();
        return view('fundsource.create_fundsource',[
            'facilities' => Facility::where('hospital_type','private')->get(),
            'user' => $user
        ]);
    }

    public function createFundSourceSave(Request $request) {
        session()->flash('fundsource_save', true);
        $data = $request->all();
        Fundsource::create($request->all());

        return redirect()->back();
    }

    public function fundsourceGet(Request $request) {
        return Fundsource::where('id',$request->fundsource_id)->with([
            'facility' => function ($query) {
                $query->select(
                    'id',
                    DB::raw('name as description')
                );
            }
        ])->first();
    }

}
