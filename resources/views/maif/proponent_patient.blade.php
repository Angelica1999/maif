@include('maif.editable_style')
<style>
  .loading-container {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1000;
        display: none; 
    }
    .loading-spinner {
        width: 100%; 
        height: 100%; 
    }

    #patient_table_length,
    #patient_table_filter {
        display: none;
    }
    #patient-code-container {
        position: relative;
    }
    #loading-image {
        position: absolute;
        margin-right: 50px;
        top: -8%;
        left: 50%;
        transform: translateY(-50%, -50%);
        width: 60px;
        height: 60px;
    }
    #province_id + .select2-container {
        width: 220px !important;
    }
    #muncity_id + .select2-container {
        width: 220px !important;
    }
    #barangay_id + .select2-container {
        width: 220px !important;
    }
    .select2-container--default .select2-results__option:hover {
        background-color: green !important; 
        color: white !important; 
    }
    #patient_table_container {
        max-height: 700px; 
        overflow-y: auto; 
    }
    .table th {
        position: sticky; 
        top: 0; 
        z-index: 2; 
        background-color: #fff; 
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4); 
    }
</style>
@extends('layouts.app')
@section('content')
<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <div class="float-right">
                <div class="input-group">
                    <form method="GET" action="">
                        <div class="input-group">
                            <input type="hidden" class="form-control" name="key">
                            <input type="text" class="form-control" name="keyword" id="search_patient" placeholder="Search..." value="{{$keyword}}" style="width:350px;">
                            <div class="input-group-append">
                                <button class="btn btn-sm btn-info" type="submit"><img src="\maif\public\images\icons8_search_16.png">Search</button> 
                                <button style="width:90px;" class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll"><img src="\maif\public\images\icons8_eye_16.png">View All</button>
                                <button type="submit" value="filt" style="display:none; background-color:00563B; color:white;" name="filter_col" id="filter_col" class="btn btn-success btn-md"><i class="typcn typcn-filter menu-icon"></i>&nbsp;&nbsp;&nbsp;Filter</button>
                            </div>  
                        </div>
                        <div class="input-group">
                            <input type="text" style="text-align:center" class="form-control" id="filter_dates" value="{{($generate_dates)?$generate_dates:''}}" name="filter_dates" />
                            <button type="submit" id="gen_btn" style="background-color:teal; color:white; width:90px; height:40px; border-radius:0; " class="btn"><i class="typcn typcn-calendar-outline menu-icon"></i>Filter</button>
                        </div>
                        <input type="hidden" name="filter_date" id="filter_date" value="{{implode(',', $filter_date)}}"></input>
                        <input type="hidden" name="filter_fname" id="filter_fname" value="{{implode(',', $filter_fname)}}"></input>
                        <input type="hidden" name="filter_mname" id="filter_mname" value="{{implode(',', $filter_mname)}}"></input>
                        <input type="hidden" name="filter_lname" id="filter_lname" value="{{implode(',', $filter_lname)}}"></input>
                        <input type="hidden" name="filter_facility" id="filter_facility" value="{{implode(',', $filter_facility)}}"></input>
                        <input type="hidden" name="filter_proponent" id="filter_proponent" value="{{implode(',', $filter_proponent)}}"></input>
                        <input type="hidden" name="filter_code" id="filter_code" value="{{implode(',', $filter_code)}}"></input>
                        <input type="hidden" name="filter_region" id="filter_region" value="{{implode(',', $filter_region)}}"></input>
                        <input type="hidden" name="filter_province" id="filter_province" value="{{implode(',', $filter_province)}}"></input>
                        <input type="hidden" name="filter_municipality" id="filter_municipality" value="{{implode(',', $filter_municipality)}}"></input>
                        <input type="hidden" name="filter_barangay" id="filter_barangay" value="{{implode(',', $filter_barangay)}}"></input>
                        <input type="hidden" name="filter_on" id="filter_on" value="{{implode(',', $filter_on)}}"></input>
                        <input type="hidden" name="filter_by" id="filter_by" value="{{implode(',', $filter_by)}}"></input>
                        <input type="hidden" name="gen" id="gen" value="{{$gen}}"></input>
                    </form>
                    <form method="POST" action="{{ route('sent.mails') }}" class="send_mailform">
                        @csrf
                        <input type="hidden" class="form-control idss" name="idss" id="idss" >
                        <input type="hidden" class="form-control sent_type" name="sent_type" id="sent_type" value="0">

                        <div class="input-group" >
                            <button class="btn btn-md send_mails" name="send_mails[]" id="email_sent" style="display:none; background-color:green; color:white; height:41px; border-radius:0px; width:130px">Send Mails <img src="\maif\public\images\email_16.png"></button>
                        </div>
                        <div class="input-group">
                            <button class="btn btn-md send_mails" name="send_mails[]" id="system_sent" style="display:none; background-color:darkgreen; color:white; height:40px; border-radius:0px; width:130px">Send GL to <img src="{{ asset('images/doh-logo.png') }}" style="width:20px"></button>
                        </div>
                    </form>
                    <form method="POST" action="{{ route('save.group') }}">
                        @csrf
                        <div style="display: flex; justify-content: flex-end;">
                            <label class="totalAmountLabel" style="display:none; height:30px;" ><b> Amount:</b></label>
                            <input style="display:none; vertical-align:center; width:150px" class="form-control group_amountT" name="group_amountT" id="group_amountT" readonly>
                            <button class=" btn-sm group-btn" style="display:none;background-color: green; color: white; height:48px; width:100px">Group</button>
                            <input type="hidden" class="form-control group_facility" name="group_facility" id="group_facility" >
                            <input type="hidden" class="form-control group_proponent" name="group_proponent" id="group_proponent" >
                            <input type="hidden" class="form-control group_patients" name="group_patients" id="group_patients" >
                        </div>
                    </form>
                </div>
            </div>
            <h4 class="card-title">PROPONENT PATIENT</h4>
            <span class="card-description">
                MAIF-IPP
            </span>
            @if(count($patients) > 0)
            <div class="table-responsive" id ="patient_table_container">
                <table class="table table-striped" id="patient_table">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th style="text-align:center">
                            <div style="display: flex; gap: 1px;">
                                <button class="btn-info select_all" style="width: 25px; display: flex; justify-content: center; align-items: center;">
                                    <i class="typcn typcn-input-checked"></i>
                                </button>
                                <button class="btn-danger unselect_all" style="width: 25px; display: flex; justify-content: center; align-items: center;">
                                    <i class="typcn typcn-times menu-icon"></i>
                                </button>
                            </div>
                        </th>
                        <th>@sortablelink('remarks', 'Status')</th>
                        <th>Remarks</th>
                        <th style="min-width:10px; text-align:center;">Group</th>
                        <th style="min-width:140px">Actual Amount</th>
                        <th style="min-width:100px">Guaranteed </th>
                        <th style="min-width:120px;">
                            <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                                <select class="form-control filter" style="display:none" id="date_select" name="date_select" multiple></select>
                                @sortablelink('date_guarantee_letter', '⇅')
                            </div>
                        </th>
                        <th style="min-width:120px; text-align:center;">
                            <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                                <select class="form-control filter" style="display:none;" id="fname_select" name="fname_select" multiple></select>
                                @sortablelink('fname', '⇅')
                            </div>
                        </th>
                        <th style="min-width:120px; text-align:center;">
                            <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                                <select class="form-control filter" style="display:none;" id="mname_select" name="mname_select" multiple></select>
                                @sortablelink('mname', '⇅')
                            </div>
                        </th>
                        <th style="min-width:120px; text-align:center;">
                            <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                                <select class="form-control filter" style="display:none;" id="lname_select" name="lname_select" multiple></select>
                                @sortablelink('lname', '⇅')
                            </div>
                        </th>
                        <th style="min-width:120px; text-align:center;">
                            <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                                <select class="form-control filter" style="display:none;" id="facility_select" name="facility_select" multiple></select>
                                <a href="{{ route('home', ['sort' => 'facility','order' => ($order == 'asc' ? 'desc' : 'asc')]) }}">⇅</a>
                            </div>
                        </th>
                        <th style="min-width:120px; text-align:center;">
                            <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                                <select class="form-control filter" style="display:none;" id="proponent_select" name="proponent_select" multiple></select>
                                <a href="{{ route('home', ['sort' => 'proponent','order' => ($order == 'asc' ? 'desc' : 'asc')]) }}">⇅</a>
                            </div>
                        </th>
                        <th style="text-align:center; vertial-align:middle">@sortablelink('patient_code', 'Code ⇅')</th>
                        <th style="min-width:120px;">
                            <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                                <select class="form-control filter" style="display:none;" id="region_select" name="region_select" multiple></select>
                                @sortablelink('region', '⇅')
                            </div>
                        </th>
                        <th style="min-width:130px; text-align:center;">
                            <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                                <select class="form-control filter" style="display:none;" id="province_select" name="province_select" multiple></select>
                                <a href="{{ route('home', ['sort' => 'province', 'order' => ($order == 'asc' ? 'desc' : 'asc')]) }}">⇅</a>
                            </div>
                        </th>
                        <th style="min-width:150px; text-align:center;">
                            <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                                <select class="form-control filter" style="display:none;" id="muncity_select" name="muncity_select" multiple></select>
                                <a href="{{ route('home', ['sort' => 'municipality', 'order' => ($order == 'asc' ? 'desc' : 'asc')]) }}">⇅</a>
                            </div>
                        </th>
                        <th style="min-width:150px; text-align:center;">
                            <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                                <select class="form-control filter" style="display:none;" id="barangay_select" name="barangay_select" multiple></select>
                                <a href="{{ route('home', ['sort' => 'barangay', 'order' => ($order == 'asc' ? 'desc' : 'asc')]) }}">⇅</a>
                            </div>
                        </th>
                        <th style="min-width:150px">
                            <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                                <select class="form-control filter" style="display:none;" id="on_select" name="on_select" multiple></select>
                                @sortablelink('created_at', '⇅')
                            </div>
                        </th>
                        <th style="min-width:150px">
                            <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                                <select class="form-control filter" style="display:none;" id="by_select" name="by_select" multiple></select>
                                <a href="{{ route('home', ['sort' => 'encoded_by', 'order' => ($order == 'asc' ? 'desc' : 'asc')]) }}">⇅</a>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody id="list_body">
                    @foreach($patients as $index=> $patient)
                        <tr>
                            <td>
                                <button type="button" href="#patient_history" data-backdrop="static" style="border-radius:0; width:70px;background-color:#006BC4; color:white; font-size:11px" data-toggle="modal" class="btn btn-xs" onclick="populateHistory({{$patient->id}})"><small>Edit History</small></button>
                                <button type="button" href="#get_mail" data-backdrop="static" data-toggle="modal" class="btn btn-xs" style="margin-top:1px; border-radius:0; width:70px;background-color:#005C6F; color:white; font-size:11px" onclick="populate({{$patient->id}})"><small>Mail History</small></button>
                                <!-- <div style="white-space: nowrap;">
                                    <div style="float:left; display:inline-block;">
                                        <button type="button" href="#patient_history" data-backdrop="static" style="border-radius:0; width:60px; background-color:#006BC4; color:white;" data-toggle="modal" class="btn btn-sm" onclick="populateHistory({{$patient->id}})"><small>Patient</small></button>
                                        <button type="button" href="#get_mail" data-backdrop="static" data-toggle="modal" class="btn btn-sm" style="border-radius:0; width:60px; background-color:#005C6F; color:white;" onclick="populate({{$patient->id}})"><small>Mail</small></button>
                                    </div>
                                    <div style="float:left; display:inline-block;">
                                        <a href="{{ route('patient.pdf', ['patientid' => $patient->id]) }}" style="border-radius:0; background-color:teal; color:white; width:60px;" target="_blank" type="button" class="btn btn-sm"><small>Print</small></a>
                                        <a href="{{ route('patient.sendpdf', ['patientid' => $patient->id]) }}" type="button" style="border-radius:0; width:60px;" class="btn btn-success btn-sm" id="send_btn"><small>Send</small></a>
                                    </div>
                                </div> -->
                            </td>
                            <td class="td">
                                <div style="display: flex; align-items: center; gap: 5px;">
                                    <div>
                                        <a href="{{ route('patient.pdf', ['patientid' => $patient->id]) }}" style="border-radius:0; background-color:teal;color:white; width:50px;" target="_blank" type="button" class="btn btn-xs">Print</a>
                                        @if($patient->facility_id && !in_array($patient->facility_id, $onhold_facs))
                                            <a href="{{ route('patient.sendpdf', ['patientid' => $patient->id]) }}" type="button" style="margin-top:1px; border-radius:0; width:50px;" class="btn btn-success btn-xs" id="send_btn">Send</a>
                                        @endif    
                                    </div>
                                    @if($patient->sent_type == 1 || $patient->fc_status == 'returned')
                                        <a href="{{ route('patient.accept', ['id' => $patient->id]) }}" style="margin-left:10px; font-size:14px"  title="Send this GL to facility">
                                            <i class="fa fa-paper-plane"></i>
                                        </a>
                                    @endif
                                </div>
                                <!-- @if($patient->status == 1)
                                    <i style="font-size:20px" class="typcn typcn-home menu-icon"></i>
                                @else
                                    <a href="{{ route('facility.send', ['id' => $patient->id]) }}" type="button"><i style="font-size:20px" class="typcn typcn-home-outline menu-icon"></i></a>
                                @endif -->
                            </td>
                            <td style="text-align:center;" class="group-email"  
                                data-patient-id="{{ $patient->id }}" >
                                <input class="sent_mails[] " id="mail_ids[]" name="mail_ids[]" type="hidden">
                                <input type="checkbox" style="width: 60px; height: 20px;" name="mailCheckbox[]" id="mailCheckboxId_{{ $patient->id }}" 
                                    data-stat="{{ $patient->sent_type == 1 || $patient->fc_status == 'returned' ? 1 : 0 }}" 
                                    data-patient-stat2="{{ $patient->facility_id && !in_array($patient->facility_id, $onhold_facs) ? 1 : 0 }}"
                                    class="group-mailCheckBox" onclick="itemChecked($(this))">
                            </td>
                            <td style="text-align:center">
                                {{ $patient->sent_type == 1 ? 'Sent from Proponent' : ($patient->sent_type == 2 ? 'Returned back to Proponent' : ( $patient->sent_type == 3 ? 'Credentials checked by MPU' : 'Credentials Check' )) }}
                            </td>
                            <td>{{$patient->pat_rem}}</td>
                            <td style="text-align:center;" class="group-amount" data-patient-id="{{ $patient->id }}" data-proponent-id="{{ $patient->proponent_id }}" 
                                data-amount="{{ $patient->actual_amount }}" data-facility-id="{{ $patient->facility_id }}" >
                                @if($patient->group_id == null)
                                <input type="checkbox" style="width: 60px; height: 20px;" name="someCheckbox[]" id="someCheckboxId_{{ $patient->id }}" 
                                    class="group-checkbox" onclick="groupItem($(this))">
                                @else
                                    w/group
                                @endif
                            </td>
                            <td class="editable-amount" data-actual-amount="{{ !Empty($patient->actual_amount)?number_format($patient->actual_amount, 2, '.', ','):0 }}" data-patient-id="{{ $patient->id }}" data-guaranteed-amount="{{str_replace(',', '', $patient->guaranteed_amount)}}">
                                <a href="#" class="number_editable"  title="Actual Amount" id="{{ $patient->id }}">{{!Empty($patient->actual_amount)?number_format($patient->actual_amount, 2, '.', ','): 0 }}</a>
                            </td>
                            <td class="td">{{ number_format((float) str_replace(',', '', $patient->guaranteed_amount), 2, '.', ',') }}</td>
                            <td>{{ date('F j, Y', strtotime($patient->date_guarantee_letter)) }}</td>
                            <td class="td">
                                <a href="#update_patient" onclick="editPatient('{{ $patient->id }}')" data-backdrop="static" data-toggle="modal">
                                    {{ $patient->fname }}
                                </a>
                            </td>   
                            <td class="td">{{ $patient->mname }}</td>
                            <td class="td">{{ $patient->lname }}</td>
                            <td class="td">{{ $patient->facility->name }}</td>
                            <td class="td">{{ $patient->proponentData ? $patient->proponentData->proponent : 'N/A' }}</td>
                            <td class="td">{{ $patient->patient_code}}</td>
                            {{-- <td>
                                @if(isset($patient->facility->description))
                                    {{ $patient->facility->description }}
                                @else
                                    {{ $patient->other_facility }}
                                @endif
                            </td> --}}
                            <td class="td">{{ $patient->region }}</td>
                            <td class="td">
                                @if(isset($patient->province->description))
                                    {{ $patient->province->description }}
                                @else
                                    {{ $patient->other_province }}
                                @endif
                            </td>
                            <td class="td">
                                @if(isset($patient->muncity->description))
                                    {{ $patient->muncity->description }}
                                @else
                                    {{ $patient->other_muncity }}
                                @endif
                            </td>
                            <td class="td">
                                @if(isset($patient->barangay->description))
                                    {{ $patient->barangay->description }}
                                @else
                                    {{ $patient->other_barangay }}
                                @endif
                            </td>
                            <td>{{date('F j, Y', strtotime($patient->created_at))}}</td>
                            <td class="td">{{ $patient->user_type == null ? $patient->encoded_by->lname .', '. $patient->encoded_by->fname: ($patient->gl_user? $patient->gl_user->lname .', '. $patient->gl_user->fname:'') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
            @else
                <div class="alert alert-danger" role="alert" style="width: 100%; margin-top:50px">
                    <i class="typcn typcn-times menu-icon"></i>
                    <strong>No patient found!</strong>
                </div>
            @endif
            <div class="pl-5 pr-5 mt-5" id ="pagination_links">
                {!! $patients->appends(request()->query())->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</div>

<!--end-->
<div class="modal fade" id="update_patient" tabindex="-1" role="dialog" style="opacity:1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="text-success" id="title"><i style = "font-size:30px"class="typcn typcn-user-add-outline menu-icon"></i>Update Patient</h4><hr />
                @csrf
            </div>
            <div class="modal_body">
                <form id="update_form" method="POST">
                    <input type="hidden" name="created_by" value="{{ $user->userid }}">
                    <input type="hidden" name="sent_type" class="sent_type" value="4">
                    <div class="modal-body">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fname">First Name</label>
                                    <input type="text" class="form-control fname" style="width:220px;" id="fname" name="fname" oninput="this.value = this.value.toUpperCase()" placeholder="First Name" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lname">Last Name</label>
                                    <input type="text" class="form-control lname" style="width:220px;" id="lname" name="lname" placeholder="Last Name" oninput="this.value = this.value.toUpperCase()" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fname">Middle Name</label>
                                    <input type="text" class="form-control mname" style="width:220px;" id="mname" name="mname" oninput="this.value = this.value.toUpperCase()" placeholder="Middle Name" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lname">Date of Birth</label>
                                    <input type="date" class="form-control dob" style="width:220px;" id="dob" name="dob" placeholder="Date of Birth" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fname">Region</label>
                                    <input type="text" class="form-control region" style="width:220px;" id="region" name="region" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lname">Province</label>
                                    <div id="province_body" class="province_body">
                                        <input type="text" class="form-control province_id" style="width:220px;" id="province_id" name="province_id" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lname">Municipality</label>
                                    <div id="muncity_body" class="muncity_body">
                                        <input type="text" class="form-control muncity_id" style="width:220px;" id="muncity_id" name="muncity_id" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lname">Barangay</label>
                                    <div id="barangay_body" class="barangay_body">
                                        <input type="text" class="form-control barangay_id" style="width:220px;" id="barangay_id" name="barangay_id" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fname">Date of Guarantee Letter</label>
                                    <input type="date" class="form-control date_guarantee_letter" style="width:220px;" id="date_guarantee_letter" name="date_guarantee_letter" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fname">Facility</label>
                                    <input type="text" class="form-control facility_id1" style="width:220px;" id="facility_id" name="facility_id" readonly>
                                </div>
                            </div> 
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fname">Proponent</label>
                                    <input type="text" class="form-control proponent_id1" style="width:220px;" id="proponent_id" name="proponent_id" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" id="patient-code-container">
                                    <input type="text" class="form-control loading-input patient_code" style="width:220px;" id="patient_code" name="patient_code" placeholder="Patient Code" readonly>
                                    <img id="loading-image" src="{{ asset('images/loading.gif') }}" alt="Loading" style="display: none;">
                                </div>
                            </div>
                        </div>
                        <hr>
                        <strong>Transaction</strong>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fname">Guaranteed Amount</label>
                                    <input type="text" class="form-control guaranteed_amount" id="guaranteed_amount" style="width:220px;" name="guaranteed_amount" readonly>
                                </div>
                            </div>
                            <div class="col-md-6"  id="actl_amnt">
                                <div class="form-group">
                                    <label for="fname">Actual Amount</label>
                                    <input type="number" step="any" class="form-control actual_amount" id="actual_amount" name="actual_amount" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fname">Remaining Balance</label>
                                    <input type="text" class="form-control remaining_balance" id="remaining_balance" style="width:220px;" name="remaining_balance" placeholder="Remaining Balance" readonly>
                                </div>
                                <div id="suggestions" class="suggestions"></div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="fname">Remarks</label>
                                    <textarea type="text" class="form-control pat_rem" id="pat_rem" style="width:470px;" name="pat_rem" placeholder="Remarks"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="close_modal" data-dismiss="modal">Close</button>
                        <button type="submit" id="create_pat_btn" style="display:none;" onclick="acceptPatient()" class="btn btn-primary">Process  & Sent to Facility</button>
                        <button class="btn btn-warning return_btn" onclick="returnPatient()" style="display:none; color:white">Return</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--end-->
<div class="modal fade" id="get_mail" tabindex="-1" role="dialog" aria-hidden="true" style="opacity:1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="text-success" id="title"><i class="typcn typcn-location menu-icon"></i>Mail History</h4><hr />
                @csrf
            </div>
            <div class="mail_body">
                
            </div>
            <div class="modal-footer">
                <button style = "background-color:lightgray"  class="btn btn-default" data-dismiss="modal"><i class="typcn typcn-times menu-icon"></i> Close</button>
            </div>
        </div>
    </div>
</div>
<!--end-->
<div class="modal fade" id="patient_history" tabindex="-1" role="dialog" aria-hidden="true" style="opacity:2">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="vertical-align:middle">
                <br>
                <h4 class="text-success" id="title"><i class="typcn typcn-location menu-icon"></i>Patient History</h4><hr/>
                @csrf
            </div>
            <div class="p_body">
            </div>
            <div class="modal-footer">
                <button style = "background-color:lightgray"  class="btn btn-default" data-dismiss="modal"><i class="typcn typcn-times menu-icon"></i> Close</button>
            </div>
        </div>
    </div>
</div>
<div class="loading-container">
    <img src="public\images\loading.gif" alt="Loading..." class="loading-spinner">
</div>
@endsection
@section('js')
<script src="{{ asset('admin/js/select2.js?v=').date('His') }}"></script>
<script src="{{ asset('admin/vendors/x-editable/bootstrap-editable.min.js?v=1') }}"></script>
<script src="{{ asset('admin/vendors/daterangepicker-master/moment.min.js?v=1') }}"></script>
<script src="{{ asset('admin/vendors/daterangepicker-master/daterangepicker.js?v=1') }}"></script>
<!-- <script src="{{ asset('admin/vendors/datatables.net/jquery.dataTables.js') }}"></script>
<script src="{{ asset('admin/vendors/datatables.net-bs4/dataTables.bootstrap4.js') }}"></script> -->

@include('maif.editable_js')

<script>
    $(document).ready(function () {
        $('.fa-sort').hide();

        function initializeSelect2(selector, route, placeholder) {
            $(selector).select2({
                ajax: {
                    url: route,
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            search: params.term || '',
                            page: params.page || 1,
                            _token: '{{ csrf_token() }}'
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.results,
                            pagination: {
                                more: data.has_more
                            }
                        };
                    },
                    cache: true
                },
                minimumInputLength: 0,
                placeholder: placeholder,
                allowClear: true,
                closeOnSelect: false,
                width: '100%',
                templateResult: function (option) {
                    if (option.loading) return option.text;
                    return $('<span>' + option.text + '</span>');
                },
                templateSelection: function (option) {
                    return option.text;
                }
            });

            $(selector).on('select2:select', function (e) {
                console.log(`Selected from ${selector}:`, e.params.data);
            });

            $(selector).on('select2:unselect', function (e) {
                console.log(`Unselected from ${selector}:`, e.params.data);
            });

            $(selector).on('select2:opening', function () {
                console.log(`Opening ${selector} dropdown...`);
            });
        }

        initializeSelect2("#date_select", '{{ route("get.dates", ["type" => "2"]) }}', "Date");
        initializeSelect2("#fname_select", '{{ route("get.names", ["type" => "2"]) }}', "First Name");
        initializeSelect2("#mname_select", '{{ route("get.m_names", ["type" => "2"]) }}', "Middle Name");
        initializeSelect2("#lname_select", '{{ route("get.l_names", ["type" => "2"]) }}', "Last Name");
        initializeSelect2("#facility_select", '{{ route("get.facilities") }}', "Facility");
        initializeSelect2("#proponent_select", '{{ route("get.proponents") }}', "Proponent");
        initializeSelect2("#region_select", '{{ route("get.region", ["type" => "2"]) }}', "Region");
        initializeSelect2("#province_select", '{{ route("get.province", ["type" => "2"]) }}', "Province");
        initializeSelect2("#muncity_select", '{{ route("get.municipalities", ["type" => "2"]) }}', "Municipality");
        initializeSelect2("#barangay_select", '{{ route("get.barangay", ["type" => "2"]) }}', "Barangay");
        initializeSelect2("#on_select", '{{ route("get.created_at", ["type" => "2"]) }}', "Created On");
        initializeSelect2("#by_select", '{{ route("get.created_by", ["type" => "2"]) }}', "Created By");
    });

    $(function() {
        $('#filter_dates').daterangepicker();
    });

    $('#system_sent').on('click', function(){
        $('.sent_type').val(1);
    });

    $('#crt_pnt').on('click', function(){
        $('#region').select2();
        $('#province_id').select2();
        $('#muncity_id').select2();
        $('#province_id').select2();
        $('#barangay_id').select2();
        $('#facility_id').select2();
        $('#proponent_id').select2();
        form_type = 'create';
    });

    $('#gen_btn').on('click', function(){
        $('#gen').val('1');
    });

    $('.filter').on('click', function(){
        $('#filter_col').css('display', 'block');
    });

    var selectFields = [
        '#date_select',
        '#fname_select',
        '#mname_select',
        '#lname_select',
        '#facility_select',
        '#proponent_select',
        '#region_select',
        '#province_select',
        '#muncity_select',
        '#barangay_select',
        '#on_select',
        '#by_select'
    ];

    $(selectFields.join(',')).on('select2:select select2:unselect', function () {
        var hasValue = selectFields.some(selector => {
            return $(selector).val() && $(selector).val().length > 0;
        });

        if (hasValue) {
            $('#filter_col').show();
        } else {
            $('#filter_col').hide();
        }
    });
    $(function() {
        $('#filter_dates').daterangepicker();
    });

    $('#system_sent').on('click', function(){
        $('.sent_type').val(1);
    });

    $('#gen_btn').on('click', function(){
        $('#gen').val('1');
    });

    $('.filter').on('click', function(){
        $('#filter_col').css('display', 'block');
    })
    $('#filter_col').on('click', function(){
        $('#filter_date').val($('#date_select').val());
        $('#filter_fname').val($('#fname_select').val());
        $('#filter_mname').val($('#mname_select').val());
        $('#filter_lname').val($('#lname_select').val());
        $('#filter_facility').val($('#facility_select').val());
        $('#filter_proponent').val($('#proponent_select').val());
        $('#filter_code').val($('#code_select').val());
        $('#filter_region').val($('#region_select').val());
        $('#filter_province').val($('#province_select').val());
        $('#filter_municipality').val($('#muncity_select').val());
        $('#filter_barangay').val($('#barangay_select').val());
        $('#filter_on').val($('#on_select').val());
        $('#filter_by').val($('#by_select').val());
    });

    $('#code_select').select2();

    $('#fname_select').change(function() {
        var selectedValues = $(this).val();
    });


    var proponents,all_patients;

    $.get("{{ url('patient-proponent') }}", function(result) {
        proponents = result.proponents;
        all_patients = result.all_pat;
    });

    $('#list_body').on('click', function(){
        $('.filter').hide();
    });

    $('.btn-secondary').on('click', function(e) {
        $('#update_form')[0].reset();
        $('#facility_id').val('').trigger('change');
        $('#proponent_id').val('').trigger('change');

    });

    $('.send_mailform').on('click', function(e) {
        $('.loading-container').show();
    });

    $('#send_btn').on('click', function(e) {
        $('.loading-container').show();
    });

    function error(){
        Lobibox.alert('error',{
            size: 'mini',
            msg: "There is an impostor! Find it"
        });
    }

    function amountError(){
        Lobibox.alert('error',{
            size: 'mini',
            msg: "There is no actual amount! Fill it"
        });
    }

    var all_ids = [];
    var id_list = [];
    var mail_ids = [];
    var selectedProponentId = 0, selectedFacilityId = 0;

    function getStat(){
        var all_stat = [];
        $('.group-mailCheckBox:checked').each(function(){ 
            var element = $(this);  
            var data_stat = element.attr('data-stat');
            all_stat.push(data_stat);
        });

        return all_stat;
    }

    function getStat2(){
        var all_stat = [];
        $('.group-mailCheckBox:checked').each(function(){ 
            var element = $(this);  
            var data_stat = element.attr('data-patient-stat2');
            all_stat.push(data_stat);
        });

        return all_stat;
    }

    function itemChecked(element){
        var parentTd = $(element).closest('td');   
        var patientId = parentTd.attr('data-patient-id');
        var checkboxId = element.attr("id");
        if(id_list.includes(patientId)){
            id_list = id_list.filter(id => id !== patientId);
            mail_ids = mail_ids.filter(id => id !== checkboxId);
        } else {
            id_list.push(patientId);
            mail_ids.push(checkboxId);
        }

        if(id_list.length != 0){
            $('.send_mails').val(id_list).show();
        }else{
            $('.send_mails').val('').hide();
        }
        if (getStat().includes('0')) {
            $('#system_sent').hide();
        }
        if (getStat2().includes('0')) {
            $('#email_sent').hide();
        }
    }

    var group_a = [];
    var group_i = [];
    var g_fac, g_pro;

    function groupItem(element){

        var parentTd = $(element).closest('td');   
        var patientId = parentTd.attr('data-patient-id');
        var row = $(element).closest('tr');   
        var edit = row.find('td.editable-amount');
        var val = edit.attr('data-actual-amount');
        amount = val.replace(/,/g,'');
        if(group_i.includes(patientId)){
            var r_index = group_i.indexOf(patientId); 
            group_i = group_i.filter(id => id !== patientId);
            if (r_index !== -1) {
                group_a.splice(r_index, 1);
            }
        }else{
            if(amount == 0 || amount == null || amount == ''){
                alert('Fill up actual amount first!');
                element.prop('checked', false);
            }else{
                var c_pro = parentTd.attr('data-proponent-id');
                var c_fac = parentTd.attr('data-facility-id');
                if (g_fac != 0 || c_fac == g_fac || g_pro != 0 || c_pro == g_pro) {
                    group_a.push(amount);
                    group_i.push(patientId);
                    g_fac = c_fac;
                    g_pro = c_pro;
                }else{
                    alert('Make sure you are selecting patient with same facility and proponent!');
                    element.prop('checked', false);
                }
            }
        }        
        var sum = group_a.reduce((accumulator, currentValue)  => accumulator + parseFloat(currentValue), 0);
        if(group_a.length != 0){
            sum = sum.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            $('.group_amountT').val(sum).show();
            $('.group_facility').val(g_fac);
            $('.group_proponent').val(g_pro);
            $('.group_patients').val(group_i.join(','));
            $('.group-btn').show();
            $('.totalAmountLabel').show();
        }else{
            $('.group_amountT').val('').hide();
            $('.totalAmountLabel').hide();
            $('.group-btn').hide();
        }
    }

    $(document).ready(function () {
        
        function loadPaginatedData(url) {
            $.ajax({
                url: url,
                method: 'GET',
                success: function(response) {
                    $('#patient_table_container').html($(response).find('#patient_table_container').html());
                    $('#pagination_links').html($(response).find('#pagination_links').html());

                    $.each(mail_ids, function(index, id) {
                        $('#' + id).prop('checked', true);
                    });

                    $.each(group_i, function(index, id) {
                        $('#someCheckboxId_' + id).prop('checked', true);
                    });
                    
                    if(selectedProponentId != 0){
                        $('#patient_table tbody tr').each(function() {
                            var row = $(this);
                            var proponentId = $(row).find('.group-amount').data('proponent-id');
                            var facilityId = $(row).find('.group-amount').data('facility-id');

                            if (proponentId !== selectedProponentId || facilityId !== selectedFacilityId) {
                                $(row).find('.group-checkbox').prop('disabled', true);
                                $(row).find('.group-checkbox').prop('checked', false);
                            } else {
                                $(row).find('.group-checkbox').prop('disabled', false);
                            }
                        });
                    }
                    initializeEditable();
                }
            });
        }

        $(document).on('click', '#pagination_links a', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            loadPaginatedData(url);
          
        });
        $(document).on('click', '.select_all', function() {
            if(all_patients){
                $('#patient_table').find('input.group-mailCheckBox').prop('checked', true).trigger('change');
                $('.send_mails').val('').show();
                all_patients.forEach(function(p){
                    id_list.push(String(p.id));
                    mail_ids.push('mailCheckboxId_'+p.id);
                });

                if (getStat().includes('0')) {
                    $('#system_sent').hide();
                }
                if (getStat2().includes('0')) {
                    $('#email_sent').hide();
                }
            }
        });
        
        $(document).on('click', '.unselect_all', function() {
            $('#patient_table').find('input.group-mailCheckBox').prop('checked', false).trigger('change');
            $('.send_mails').val('').hide();
            id_list = [];
            mail_ids = [];
        });


        function initializeEditable() {
            $.fn.editable.defaults.mode = 'popup';

            $(".number_editable").editable({
                type : 'number',
                name: 'actual_amount',
                title: $(this).data("title"),
                emptytext: 'empty',
                success: function(response, newValue) {
                    var cell = $(this).closest('.editable-amount');
                    var patientId = cell.data('patient-id');
                    var guaranteed_amount = cell.data('guaranteed-amount');
                    var actual_amount = cell.data('actual-amount');
                    var editableField = this;
                    var url = "{{ url('update/amount').'/' }}" + patientId + '/' + newValue;
                    var json = {
                        "_token" : "<?php echo csrf_token(); ?>",
                        "value" : newValue
                    };

                    if(newValue == ''){
                        Lobibox.alert('error',{
                            size: 'mini',
                            msg: "Actual amount accepts number only!"
                        }); 
                        $(editableField).text(actual_amount);
                        $(editableField).value(actual_amount);
                        cell.attr('data-actual-amount', actual_amount);
                        // location.reload();
                        return;  
                    }
                    var c_amount = newValue.replace(/,/g,'');

                    if(c_amount > guaranteed_amount){
                        $(this).html(newValue);
                        Lobibox.alert('error',{
                            size: 'mini',
                            msg: "Inputted actual amount if greater than guaranteed amount!"
                        }); 
                        cell.attr('data-actual-amount', actual_amount);
                        $(editableField).text(actual_amount);
                        $(editableField).value(actual_amount);
                        // location.reload();
                        return;           
                    }

                    $.post(url, json, function(result){
                        Lobibox.notify('success', {
                            title: "",
                            msg: "Successfully update actual amount!",
                            size: 'mini',
                            rounded: true
                        });
                        cell.attr('data-actual-amount', newValue);
                        // location.reload();
                    });
                }
            });
        }

        function initializeGroupFunctions() {

            $('#patient_table').on('change', '.group-checkbox', function() {
                if ($(this).prop('checked')) {
                    selectedProponentId = $(this).closest('.group-amount').data('proponent-id');
                    selectedFacilityId = $(this).closest('.group-amount').data('facility-id');
                    $('#patient_table tbody tr').each(function() {
                        var row = $(this);
                        var proponentId = $(row).find('.group-amount').data('proponent-id');
                        var facilityId = $(row).find('.group-amount').data('facility-id');

                        if (proponentId !== selectedProponentId || facilityId !== selectedFacilityId) {
                            $(row).find('.group-checkbox').prop('disabled', true);
                            $(row).find('.group-checkbox').prop('checked', false);
                        } else {
                            $(row).find('.group-checkbox').prop('disabled', false);
                        }
                    });
                } else {
                    $('#patient_table').find('.group-checkbox').prop('disabled', false);
                }
            });
        }
        
        initializeEditable();
        initializeGroupFunctions();

    });

    $('#update_send').on('click', function(){
        $('.loading-container').show();
    });

    var edit_c = 0;
    var return_id;

    function editPatient(id) {
        form_type='update';
        var patient;
        $.get("{{url('/gl/update').'/'}}" + id, function(result){
            patient = result.patients;
            ids = result.ids;
            id = patient.id
            edit_c = 1;
            var editRoute = `{{ route('patient.return', ['id' => ':id']) }}`;
            editRoute = editRoute.replace(':id', id);
            return_id = id;
            $('#update_form').attr('action', editRoute);
            if(patient){

                $('#btn.btn-warning').attr('data-id', id);
                $('.create_pat_btn').attr('data-id', id);

                $('.fname').val(patient.fname);
                $('.lname').val(patient.lname);
                $('.mname').val(patient.mname);
                $('.dob').val(patient.dob);
                $('.region').val(patient.region);
                $('.province_id').val(patient.province?patient.province.description:'');
                $('.muncity_id').val(patient.muncity?patient.muncity.description:'');
                $('.barangay_id').val(patient.barangay?patient.barangay.description:'');
                $('.date_guarantee_letter').val(patient.date_guarantee_letter);
                $('.guaranteed_amount').val(patient.guaranteed_amount);
                $('.actual_amount').val(patient.actual_amount);
                $('.proponent_id1').val(patient.proponent_data?patient.proponent_data.proponent   :'');
                $('.facility_id1').val(patient.facility.name);
                $('.patient_code').val(patient.patient_code);
                $('.remaining_balance').val(patient.remaining_balance);
                $('.pat_rem').val(patient.pat_rem);

                if(patient.sent_type == 1){
                    $('.btn.btn-warning').css('display', 'block');
                    $('#create_pat_btn').css('display', 'block');
                }else{
                    $('.btn.btn-warning').css('display', 'none');
                    $('#create_pat_btn').css('display', 'none');
                }
            }
            edit_c = 0;
        });
    }
    
    var form_type = 'create';

    var facility_id = 0;

    function formatDate(item){
        var date = new Date(item);
        var formattedDate = date.toLocaleString('en-US', {
            month: 'long',
            day: 'numeric',
            year: 'numeric'
        });
        return formattedDate;
    }
    
    function populateHistory(id){
        $('#gl_history').empty();
        $('.p_body').html(loading);
        var url = "{{ url('/patient/history').'/' }}"+id;
        $.ajax({
            url: url,
            type: 'GET',
            success: function(result) {
                $('.p_body').html(result);
            }
        });
    }

    function populate(id){
        $('#mail_history').empty();
        var url = "{{ url('/mail/history').'/' }}"+id;
        $.ajax({
            url: url,
            type: 'GET',
            success: function(result) {
                $('.mail_body').html(result);
            }
        });
    }

    function returnPatient() {
        $('.pat_rem').attr('required', true);
        $('.sent_type').val(2);
    }


    function acceptPatient(){
        $('.pat_rem').attr('required', false);
        $('.sent_type').val(3);
    }

</script>
@endsection
