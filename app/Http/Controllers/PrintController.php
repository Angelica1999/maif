<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;

class PrintController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function patientPdf(Request $request) {
        $data = [
            'title' => 'Welcome to MAIF',
            'date' => date('m/d/Y')
        ];
    
        // Set the paper size to A4 in the options array
        $options = [
            'defaultFont' => 'helvetica',
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'isRemoteEnabled' => true,
            'isFontSubsettingEnabled' => true,
            'paper' => 'A4',
        ];
    
        $pdf = PDF::loadView('maif.print_patient', $data, $options);
    
        // Set the response headers to open the PDF in a new tab
        return $pdf->stream('patient.pdf');
    }
}
