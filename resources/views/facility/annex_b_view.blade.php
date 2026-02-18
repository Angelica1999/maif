<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f5f5f5;
        padding: 20px;
    }

    .container {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        max-width: 100%;
        margin: 0 auto;
    }

    .header-section {
        background: linear-gradient(135deg, #e0e0e0 0%, #757575 100%);
        padding: 20px 30px;
        color: white;
    }

    .header-section h1 {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .header-section p {
        opacity: 0.95;
        font-size: 13px;
    }

    .controls-section {
        padding: 20px 30px;
        background: #fafafa;
        border-bottom: 1px solid #e0e0e0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .btn {
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }

    .btn-success { background: #28a745; color: white; }
    .btn-info { background: #17a2b8; color: white; }
    .btn-warning { background: #ffc107; color: #333; }
    .btn:hover { opacity: 0.9; transform: translateY(-1px); }

    #patient_table_container {
        max-height: 600px;
        overflow: auto;
        position: relative;
    }

    /* Custom scrollbar */
    #patient_table_container::-webkit-scrollbar {
        width: 10px;
        height: 10px;
    }

    #patient_table_container::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    #patient_table_container::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 5px;
    }

    #patient_table_container::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }

    .table thead {
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .table th {
        background: gray;
        color: white;
        font-weight: 600;
        padding: 12px 8px;
        text-align: center;
        border: 1px solid rgba(255, 255, 255, 0.2);
        font-size: 11px;
        line-height: 1.3;
        vertical-align: middle;
        position: relative;
    }

    .table thead tr:first-child th {
        height: 50px;
        vertical-align:middle;
    }

    .table thead tr:nth-child(2) th {
        height: 50px;
    }

    .table thead tr:nth-child(3) th {
        height: 50px;
    }

    .table th[rowspan="2"] {
        height: 100px;
    }

    .table th[rowspan="3"] {
        height: 150px;
    }

    .table tbody td {
        padding: 10px 8px;
        border: 1px solid #ddd;
        background: white;
        transition: all 0.2s;
        min-width: 100px;
    }

    .table tbody td[contenteditable="true"] {
        cursor: text;
        position: relative;
    }

    .table tbody td[contenteditable="true"]:hover {
        background-color: #fffacd;
        box-shadow: inset 0 0 0 2px #667eea;
    }

    .table tbody td[contenteditable="true"]:focus {
        background-color: #fff8dc;
        outline: 2px solid #667eea;
        outline-offset: -2px;
    }

    .table tbody tr:hover td {
        background-color: #f8f9ff;
    }

    .table tbody tr:nth-child(even) td {
        background-color: #fafafa;
    }

    .table tbody tr:nth-child(even):hover td {
        background-color: #f0f2ff;
    }

    /* Numeric columns alignment */
    .table td.numeric {
        text-align: right;
        font-family: 'Courier New', monospace;
        font-weight: 500;
    }

    .table th i {
        font-style: italic;
        font-weight: 400;
        opacity: 0.9;
        font-size: 10px;
    }

    .edit-indicator {
        position: absolute;
        top: 2px;
        right: 2px;
        font-size: 10px;
        color: #667eea;
        opacity: 0;
        transition: opacity 0.2s;
    }

    .table tbody td[contenteditable="true"]:hover .edit-indicator {
        opacity: 1;
    }

    td[contenteditable="true"]::after {
        content: "✎";
        position: absolute;
        top: 2px;
        right: 4px;
        font-size: 10px;
        color: #667eea;
        opacity: 0;
        pointer-events: none;
    }

    td[contenteditable="true"]:hover::after,
        td[contenteditable="true"]:focus::after {
            opacity: 1;
    }


    .save-notice {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: #28a745;
        color: white;
        padding: 12px 20px;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        display: none;
        align-items: center;
        gap: 8px;
        z-index: 1000;
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from { transform: translateY(100px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .save-notice.show {
        display: flex;
    }
    td input[type="checkbox"] {
        cursor: pointer;
        height:30px;
        width:20px;    }

    
</style>
@extends('layouts.app')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <div class="float-right">
                <div class="input-group">
                    <form method="GET" action="{{ route('fur.submission') }}">
                        <div class="input-group">
                            <input type="hidden" name="month" value="{{ $month }}">
                            <input type="hidden" name="year" value="{{ $year }}">
                            <input type="hidden" name="facility_id" value="{{ $facility_id }}">
                            <input type="text" class="form-control" name="keyword" value="{{ $keyword }}" placeholder="Search..." style="width:350px;">
                            <div class="input-group-append">
                                <button class="btn btn-sm btn-info" type="submit"><img src="\maif\public\images\icons8_search_16.png">Search</button> 
                            </div>  
                            <select name="data_type" class="form-control" style="width: 120px; border:1px solid green" onchange="this.form.submit()">
                                <option value="" {{ $type == '' ? 'selected' :'' }}>All</option>
                                <option value="1" {{ $type == 1 ? 'selected' :'' }}>SERVICE WARD</option>
                                <option value="2" {{ $type == 2 ? 'selected' :'' }}>PAYWARD</option>
                                <option value="3" {{ $type == 3 ? 'selected' :'' }}>OPD</option>
                            </select>
                            <button class="btn btn-sm btn-warning text-white" style="border-radius:0px"type="submit" name="viewAll" value="viewAll"><img src="\maif\public\images\icons8_eye_16.png">View All</button>
                            <a href="{{ url()->previous() }}" class="btn btn-sm d-flex align-items-center" style="border-radius:0px; background-color:#5DADE2">
                                <i class="fa fa-arrow-left me-1"></i> Previous View
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            <h4 class="card-title">ANNEX B <span class="text-info">({{ \Carbon\Carbon::create()->month($month)->format('F') .' '. $year }})</span></h4>
            <span class="card-description">
                MAIF-IPP
            </span>
            <div class="position-relative">
                <button 
                    id="scrollLeft" 
                    class="btn btn-light shadow-sm position-absolute"
                    style="display: none; z-index: 100; left: 10px; top: 50%; transform: translateY(-50%); height: 60px; width: 40px; padding: 0; font-size: 28px; line-height: 1; border: 1px solid #ccc; background: rgba(255, 255, 255, 0.95);"
                    title="Scroll Left">
                    ‹
                </button>
                <div id="patient_table_container" class="table-responsive" style="overflow-x: auto; scroll-behavior: smooth; flex-grow: 1;">
                    <table class="table" id="patient_table">
                        <thead>
                            <tr>
                                <th rowspan="3">OPD</th>
                                <th style="min-width:200px;" rowspan="3">Name of Patient<br>(Last Name, First Name,<br>Middle Name)</th>
                                <th style="min-width:200px;" rowspan="3">MAIP Code<br>(Generated from MAIS)</th>
                                <th style="min-width:200px;" rowspan="3">Type of Medical<br>Assistance Provided</th>
                                <th rowspan="2" style="min-width:180px">Total Actual Charges<br><i>(without any deductions)</i></th>
                                <th colspan="6" style="min-width:200px">Hospital Bill/Medical Assistance Deductions</th>
                                <th rowspan="2" style="min-width:180px">Total Actual Charges<br><i>(with deductions of the following:<br>PhilHealth, PCS, Senior Citizen,<br>PWD, DSWD, LGU, HMOs,<br>Insurance & others)</i></th>
                                <th rowspan="2" style="min-width:180px">Assistance to<br>Professional Fee<br>through MAIP<br><i>(not more than 50%<br>of the approved assistance)</i></th>
                                <th rowspan="2" style="min-width:180px">Hospital Bill/<br>Medical Assistance<br>through MAIP</th>
                                <th rowspan="2" style="min-width:180px">Total Actual<br>Approved Assistance<br>through MAIP<br><i>(Utilized Amount)</i></th>
                                <th rowspan="2" style="min-width:150px">Percent of Excess<br>Net Bill/Charges<br>covered by MAIP</th>
                            </tr>
                            <tr>
                                <th style="min-width:140px">Senior Citizen/<br>PWD</th>
                                <th style="min-width:140px">PhilHealth<br>(case rate)</th>
                                <th style="min-width:120px">PCSO</th>
                                <th style="min-width:120px">DSWD</th>
                                <th colspan="2" style="min-width:160px">Others<br>(please specify)</th>
                            </tr>
                            <tr>
                                <th>A</th>
                                <th>B</th>
                                <th>C</th>
                                <th>D</th>
                                <th>E</th>
                                <th colspan="2">F</th>
                                <th>A - SUM(B:F) = G</th>
                                <th>H</th>
                                <th>I</th>
                                <th>H + I = J</th>
                                <th>J / G = K</th>
                            </tr>
                        </thead>
                        <tbody id="list_body">
                            @if(count($data) > 0)
                                @foreach($data as $row)
                                    @php
                                        $fullName = $row->patient->lname . ', ' . $row->patient->fname;
                                        if($row->patient->mname && $row->patient->mname != 'N/A') {
                                            $fullName .= ' ' . $row->patient->mname;
                                        }
                                        $transTotal = $row->trans?->final_bill ?? 0;
                                        $senior = $row->senior ?? 0;
                                        $phic = $row->phic ?? 0;
                                        $pcso = $row->pcso ?? 0;
                                        $dswd = $row->dswd ?? 0;
                                        $o_amount = $row->o_amount ?? 0;
                                        $approved_assistance = $row->trans?->total ?? 0;
                                        $actual_charges = ($row->trans?->final_bill ?? 0 ) - ($senior + $phic + $pcso + $dswd + $o_amount);
                                        $ratio = ($actual_charges > 0 && $approved_assistance > 0)
                                            ? ($approved_assistance / $actual_charges) * 100
                                            : 0;
                                        $p_fee = $row->trans?->p_fee ?? 0;
                                        $h_bill = $row->trans?->h_bill ?? 0;
                                        $total = $row->trans?->total ?? 0;
                                    @endphp

                                    <tr data-id="{{ $row->patient_id }}">
                                        <td style="text-align: center;"><input type="checkbox" {{ $row->opd ? 'checked' : '' }} disabled></td>
                                        <td>{{ $fullName }}</td>
                                        <td>{{ $row->patient->patient_code ?? '' }}</td>
                                        <td>Hospital Bill</td>
                                        <td id="trans_total">{{ number_format($transTotal, 2, '.', ',') }}</td>
                                        <td @if($row->opd == 0) @else style="background-color:lightgray" @endif id="senior" data-type="number">
                                            {{ $row->opd == 0 ? number_format($senior, 2, '.', ',') : '' }}
                                        </td>
                                        <td id="phic" data-type="number" @if($row->opd == 0 ) @else style="background-color:lightgray" @endif>
                                            {{ $row->opd == 0 ? number_format($phic, 2, '.', ',') : '' }}
                                        </td>
                                        <td @if($row->opd == 0 )  @else style="background-color:lightgray" @endif id="pcso" data-type="number">
                                            {{ $row->opd == 0 ? number_format($pcso, 2, '.', ',') : '' }}
                                        </td>
                                        <td @if($row->opd == 0 )  @else style="background-color:lightgray" @endif id="dswd" data-type="number">
                                            {{ $row->opd == 0 ? number_format($dswd, 2, '.', ',') : '' }}
                                        </td>
                                        <td @if($row->opd == 0 )  @else style="background-color:lightgray" @endif id="o_amount" data-type="number">
                                            {{ $row->opd == 0 ? number_format($o_amount, 2, '.', ',') : '' }}
                                        </td>
                                        <td @if($row->opd == 0 )  @else style="background-color:lightgray" @endif id="others">
                                            {{ $row->opd == 0 ? $row->others ?? '' : '' }}
                                        </td>
                                        <td @if($row->opd == 1) style="background-color:lightgray" @endif id="actual_charges">
                                            {{ $row->opd == 0 ? number_format($actual_charges, 2, '.', ',') : '' }}
                                        </td>
                                        <td @if($row->opd == 1) style="background-color:lightgray" @endif >
                                            {{ $row->opd == 0 ? number_format($p_fee, 2, '.', ',') : '' }}
                                        </td>
                                        <td @if($row->opd == 1) style="background-color:lightgray" @endif >
                                            {{ $row->opd == 0 ? number_format($h_bill, 2, '.', ',') : '' }}
                                        </td>
                                        <td id="approved_assistance">{{ number_format($total, 2, '.', ',') }}</td>
                                        <td @if($row->opd == 1) style="background-color:lightgray" @endif id="ratio">
                                            {{ $row->opd == 0 ? number_format($ratio, 2).'%' : '' }}
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr><td style="color:#850000; background-color:#ffcccc; border-color:#ffb8b8;" colspan="16">No data Found</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <button 
                        id="scrollRight" 
                        class="btn btn-light shadow-sm position-absolute"
                        style="z-index: 100; right: 10px; top: 50%; transform: translateY(-50%); height: 60px; width: 40px; padding: 0; font-size: 28px; line-height: 1; border: 1px solid #ccc; background: rgba(255, 255, 255, 0.95);"
                        title="Scroll Right">
                    ›
                </button>
            </div>
            <div class="pl-5 pr-5 mt-5" id ="pagination_links">
                {!! $data->appends(request()->query())->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</div>
<div class="save-notice" id="saveNotice">
    <span>✓</span>
    <span>Changes saved successfully!</span>
</div>
@endsection
@section('js')
<script>
    (function() {
        'use strict';
        
        document.addEventListener('DOMContentLoaded', function() {
            var tableContainer = document.getElementById('patient_table_container');
            var scrollLeftBtn = document.getElementById('scrollLeft');
            var scrollRightBtn = document.getElementById('scrollRight');
            
            if (!tableContainer || !scrollLeftBtn || !scrollRightBtn) {
                return;
            }
            
            var scrollAmount = 300;

            scrollLeftBtn.addEventListener('click', function() {
                tableContainer.scrollBy({
                    left: -scrollAmount,
                    behavior: 'smooth'
                });
            });

            scrollRightBtn.addEventListener('click', function() {
                tableContainer.scrollBy({
                    left: scrollAmount,
                    behavior: 'smooth'
                });
            });

            function updateButtonVisibility() {
                var { scrollLeft, scrollWidth, clientWidth } = tableContainer;
        
                if (scrollLeft <= 0) {
                    scrollLeftBtn.style.display = 'none';
                } else {
                    scrollLeftBtn.style.display = 'block';
                }
                
                if (scrollLeft + clientWidth >= scrollWidth - 1) {
                    scrollRightBtn.style.display = 'none';
                } else {
                    scrollRightBtn.style.display = 'block';
                }
            }

            tableContainer.addEventListener('scroll', updateButtonVisibility);
            
            window.addEventListener('resize', updateButtonVisibility);
            
            setTimeout(updateButtonVisibility, 100);
        });
    })();
</script>
@endsection