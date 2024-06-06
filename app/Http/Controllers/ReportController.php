<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patients;
use App\Models\Facility;
use App\Models\Province;
use App\Models\Muncity;
use App\Models\Barangay;
use App\Models\Fundsource;
use App\Models\Proponent;
use App\Models\Group;
use App\Models\ProponentInfo;
use App\Models\User;
use App\Models\Transfer;
use App\Models\Dv;
use App\Models\Utilization;
use App\Models\TrackingDetails;
use App\Models\PatientLogs;
use App\Models\MailHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */


     public function reportSaa(){

        // $info = Utilization::where('proponentinfo_id',638)->where('status', 0)->get();
        // $bal = 1485000;
        // // return $info;
        // foreach($info as $info){
        //     $info->beginning_balance = $bal;
        //     $info->save();
        //     $bal =  $info->beginning_balance - $info->utilize_amount ;
        // }
        // $inf = ProponentInfo::where('id', 638)->first();
        // $inf->remaining_balance = number_format((double) str_replace(',', '',$bal), 2,'.',',');
        // $inf->save();

        $fundsources = ProponentInfo::orderBy('fundsource_id', 'ASC')->with('facility', 'proponent', 'fundsource')->get();
      
        $filename = 'SAA.xls';
        header("Content-Type: application/xls");
        header("Content-Disposition: attachment; filename=$filename");
        header("Pragma: no-cache");
        header("Expires: 0");
        $table_body = "<tr>
                <th>SAA NUMBER</th>
                <th>PROPONENT</th>
                <th>FACILITY</th>
                <th>ALLOCATED FUNDS</th>
                <th>UTILIZATION (DV + Admin cost)</th>
                <th>BALANCE</th>
                <th>UTILIZATION RATE</th>
            </tr>";
      
        if($fundsources){

            foreach($fundsources as $row) {

                $saa = $row->fundsource->saa;
                $proponent = $row->proponent->proponent;
                $facility = $row->facility;
                $fundsource = number_format((double) str_replace(',', '',$row->alocated_funds), 2,'.',',');
                $rem = number_format((double) str_replace(',', '',$row->remaining_balance), 2,'.',',');
                $utilization = (double) str_replace(',', '',$row->alocated_funds) - (double) str_replace(',', '',$row->remaining_balance);
                $utilization = number_format((double) str_replace(',', '',$utilization), 2,'.',',');

                $total = Utilization::where('proponentinfo_id', $row->id)
                        ->where('status', 0)
                        ->select(DB::raw('SUM(REPLACE(utilize_amount, ",", "")) as totalAmount'))
                        ->first()->totalAmount;   
                $total_am = number_format($total + (double) str_replace(',', '',$row->admin_cost), 2,'.',',');
                if($rem == 0 ){
                    $per = 100;
                }else if($utilization == 0){
                    $per = 0;
                }else{
                    $per = round((double) str_replace(',', '',$total_am) / (double) str_replace(',', '',$row->alocated_funds) * 100);
                }

                if($facility == null){
                    $id = $row->facility_id;
                    $array = json_decode($id, true);
                    $int = array_map('intval', $array);
                    $name = Facility::whereIn('id', $int)->pluck('name');
                    $name = str_replace(['[', ']', '"'], '', $name); 
                }else{
                    $name = $row->facility->name;
                }
                $table_body .= "<tr>
                    <td style='vertical-align:top;'>$saa</td>
                    <td style='vertical-align:top;'>$proponent</td>
                    <td style='vertical-align:top;'>$name</td>
                    <td style='vertical-align:top;'>$fundsource</td>
                    <td style='vertical-align:top;'>$utilization</td>
                    <td style='vertical-align:top;'>$rem</td>
                    <td style='vertical-align:top;'>$per%</td>
                </tr>";

            }
        }else{
            $table_body .= "<tr>
                <td colspan=6 style='vertical-align:top;'>No Data Available</td>
            </tr>";
        }
        $display =
            '<h1>SAA</h1>'.
            '<table cellspacing="1" cellpadding="5" border="1">'.$table_body.'</table>';

        return $display;
    }
}
