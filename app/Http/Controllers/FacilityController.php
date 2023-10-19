<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facility;
use App\Models\Patients;
use App\Models\AddFacilityInfo;
use Illuminate\Support\Facades\DB;

class FacilityController extends Controller
{
    public function index(Request $request) {
        // $Facility = (new Facility())->getConnection()->getDatabaseName();
        // $AddFacilityInfo = (new AddFacilityInfo())->getConnection()->getDatabaseName();
        // $result = DB::table($Facility.'.facility')
        //     ->select(
        //         DB::raw($Facility.'.facility.name'),
        //         DB::raw($AddFacilityInfo.'.addfacilityinfo.*'))
        //     ->join($AddFacilityInfo.'.addfacilityinfo', $Facility.'.facility.id', '=', $AddFacilityInfo.'.addfacilityinfo.facility_id')
        //     ->where($Facility.'.facility.hospital_type','private')
        //     ->paginate(15);

        
        $result = DB::table('doh_referral.facility')
            ->select(
                'doh_referral.facility.name',
                'doh_referral.facility.address',
                'maif.addfacilityinfo.*'
            )
            ->leftJoin('maif.addfacilityinfo', 'doh_referral.facility.id', '=', 'maif.addfacilityinfo.facility_id')
            ->where('doh_referral.facility.hospital_type','private')
            ->paginate(15);

        return view('facility.facility',[
            'facilities' => $result,
            'keyword' => $request->keyword
        ]);
    }
}
