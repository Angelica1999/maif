<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PreDV;
use App\Models\PreDVControl;
use App\Models\PreDVSAA;
use App\Models\PreDVExtension;
use App\Models\Proponent;
use App\Models\Fundsource;
use App\Models\Facility;

class PreDvController extends Controller
{
    //

    public function pre_dv(){
        $pre_dv = PreDV::with('user','facility')->orderBy('id', 'desc')->paginate(50);
        $saas = Fundsource::get();
        $proponents = Proponent::select('proponent')->groupBy('proponent')->get();
        $facilities = Facility::get();
        return view('pre_dv.pre_dv', [
            'results' => $pre_dv,
            'proponents' => $proponents,
            'saas' => $saas,
            'facilities' => $facilities
        ]);
    }

    public function cloneProponent(){
        $proponents = Proponent::select('proponent')->groupBy('proponent')->get();
        $saas = Fundsource::get();

        return view('pre_dv.proponent_clone',[
            'proponents' => $proponents,
            'saas' => $saas
        ]);
    }

    public function cloneSAA(){
        $saas = Fundsource::get();
        return view('pre_dv.saa_clone',[
            'saas' => $saas
        ]);
    }

    public function cloneControl(){
        return view('pre_dv.control_clone');
    }

    public function savePreDV($data,Request $request){
        // return $data;
        $decodedData = urldecode($data);
        $all_data = json_decode($decodedData, true);
        $grand_total = $request->grand_total;
        $facility_id = $request->facility_id;

        $pre_dv = new PreDV();
        $pre_dv->facility_id = $facility_id;
        $pre_dv->grand_total = (float)str_replace(',','',$grand_total);
        $pre_dv->created_by = Auth::user()->userid;
        $pre_dv->save();

        foreach($all_data as $value){
            $proponent_id = $value['proponent'];
            $control_nos = $value['pro_clone'];
            $fundsources = $value['fundsource_clone'];
            $proponent = Proponent::where('proponent', $proponent_id)->value('id');

            $pre_extension = new PreDVExtension();
            $pre_extension->pre_dv_id = $pre_dv->id;
            $pre_extension->proponent_id = $proponent;
            $pre_extension->total_amount = (float)str_replace(',','',$value['total_amount']);
            $pre_extension->save();

            foreach($control_nos as $row){
                $controls = new PreDVControl();
                $controls->predv_extension_id = $pre_extension->id;
                $controls->control_no = $row['control_no'];
                $controls->patient_1 = $row['patient_1'];
                $controls->patient_2 = $row['patient_2'];
                $controls->amount = (float)str_replace(',','',$row['amount']);
                $controls->save();
            }

            foreach($fundsources as $saa){
                $pre_saa = new PreDVSAA();
                $pre_saa->predv_extension_id = $pre_extension->id;
                $pre_saa->fundsource_id = $saa['saa_id'];
                $pre_saa->amount = (float)str_replace(',','',$saa['saa_amount']);
                $pre_saa->save();
            }
        }
        return redirect()->back();
    }

    public function displayPreDV($id){
        $pre_dv = PreDV::where('id', $id)->with(
                [
                    'user:userid,fname,lname,mname',
                    'facility:id,name', 
                    'extension' => function ($query){
                        $query->with(
                            [
                                'proponent:id,proponent',
                                'controls',
                                'saas' => function($query){
                                    $query->with([
                                        'saa:id,saa'
                                    ]);
                                }
                            ]
                        );
                    }
                ])->first();
        $saas = Fundsource::get();
        $proponents = Proponent::select('proponent')->groupBy('proponent')->get();
        $facilities = Facility::get();
        return view('pre_dv.update_predv', [
            'result' => $pre_dv,
            'proponents' => $proponents,
            'saas' => $saas,
            'facilities' => $facilities
        ]);
    }

    public function updatePreDV($data, Request $request){

        return $data;

        $decodedData = urldecode($data);
        $all_data = json_decode($decodedData, true);
        $id = $request->pre_id;
        $pre_dv = PreDV::where('id', $id)->first();
        if($pre_dv){
            $pre_dv->facility_id = $request->facility_id;
            $pre_dv->grand_total = str_replace(',','',$request->grand_total);
            $pre_dv->save();
            foreach($all_data as $data){
                $extension = PreDVExtension::where('pre_dv_id')->get();
                $ex_control = PreDVControl::where('predv_extension_id', $extension[0]->id)->delete();
                $ex_saa = PreDVSAA::where('predv_extension_id', $extension[0]->id)->delete();
                $extension->delete();

                $new_extension = new PreDVExtension();
                $new_extension->proponent_id = Proponent::where('id', $extension->id)->first();

                // to be continued ... 
            }
        }
    }

}
