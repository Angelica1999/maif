<?php

namespace App\Http\Controllers;
use App\Models\Patients;
use Illuminate\Http\Request;
use Carbon\Carbon;

use PDF; 


class PrintController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function calculateAge($dob) {
        $dob = Carbon::parse($dob);
        $currentDate = Carbon::now();
        $age = $currentDate->diffInYears($dob);

        if ($age >= 1) {
            if ($dob->diffInMonths($currentDate) > 0) {
                return $age . ' y/o';
            } else {
                return $age . ' y/o';
            }
        } else {
            return $dob->diffInMonths($currentDate) . ' month' . ($dob->diffInMonths($currentDate) != 1 ? 's' : '');
        }
    }
    
    public function patientPdf(Request $request, $patientid) {
        $patient = Patients::find($patientid);

        if(!$patient){
            return redirect()->route('Home.index')->with('error', 'Patient not found.');
        }

        $data = [
            'title' => 'Welcome to MAIF',
            'date' => date('m/d/Y'),
            'patient' => $patient,
            'age' => $this->calculateAge($patient->dob)
        ];
    
        // Set the paper size to A4 in the options array
        $options = [
        //     'defaultFont' => 'helvetica',
        //     'isHtml5ParserEnabled' => true,
        //     'isPhpEnabled' => true,
        //     'isRemoteEnabled' => true,
        //     'isFontSubsettingEnabled' => true,
        //    // 'format' => 'folio',
        //     'size' => 'folio'
        ];
    
        $pdf = PDF::loadView('maif.print_patient', $data, $options);

        // Set the response headers to open the PDF in a new tab
        return $pdf->stream('patient.pdf');
    }
}
