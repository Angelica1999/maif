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
                    <form method="GET" action="{{ route('home') }}">
                        <div class="input-group">
                            <input type="hidden" class="form-control" name="key">
                            <input type="text" class="form-control" name="keyword" id="search_patient" placeholder="Search..." value="{{$keyword}}" style="width:350px;">
                            <div class="input-group-append">
                                <button class="btn btn-sm btn-info" type="submit"><img src="\maif\public\images\icons8_search_16.png">Search</button> 
                                <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll"><img src="\maif\public\images\icons8_eye_16.png">View All</button>
                                <button type="button" href="#create_patient" id="crt_pnt" data-backdrop="static" data-toggle="modal" class="btn btn-success btn-md"><img src="\maif\public\images\icons8_create_16.png">Create</button>
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

                        <div class="input-group-append" style="display: flex; justify-content: flex-end;">
                            <button class="btn btn-md send_mails" name="send_mails[]" style="display:none; background-color:green; color:white; height:41px; border-radius:0px; width:100%">Send Mails <img src="\maif\public\images\email_16.png"></button>
                        </div>
                        <div class="input-group">
                            <button class="btn btn-md send_mails" name="send_mails[]" id="system_sent" style="display:none; background-color:darkgreen; color:white; height:40px; border-radius:0px; width:100%">Send GL to <img src="{{ asset('images/doh-logo.png') }}" style="width:20px"></button>
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
           
            <h4 class="card-title">MANAGE PATIENTS</h4>
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
                        <th style="min-width:80px">@sortablelink('remarks', 'Status')</th>
                        <th>Remarks</th>
                        <th style="min-width:10px; text-align:center;">Group</th>
                        <th style="min-width:140px">Actual Amount</th>
                        <th style="min-width:100px">Guaranteed </th>
                        <th style="min-width:120px; text-align:center;">@sortablelink('date_guarantee_letter', 'Date') <i id="date_i" class="typcn typcn-filter menu-icon"></i>
                            <div class="filter" id="date_div" style="display:none;">
                                <select style="width: 120px;" id="date_select" name="date_select" multiple></select>
                            </div>
                        </th>
                        <th style="min-width:150px; text-align:center;">@sortablelink('fname', 'Firstname')<i id="fname_i" class="typcn typcn-filter menu-icon"></i>
                            <div class="filter" id="fname_div" style="display:none;">
                                <select style="width: 120px;" id="fname_select" name="fname_select" multiple>
                                </select>
                            </div>
                        </th>
                        <th style="min-width:150px; text-align:center;">@sortablelink('mname', 'Middlename')<i id="mname_i" class="typcn typcn-filter menu-icon"></i>
                            <div class="filter" id="mname_div" style="display:none;">
                                <select style="width: 120px;" id="mname_select" name="mname_select" multiple>
                                </select>
                            </div>
                        </th>
                        <th style="min-width:150px; text-align:center;">@sortablelink('lname', 'Lastname')<i id="lname_i" class="typcn typcn-filter menu-icon"></i>
                            <div class="filter" id="lname_div" style="display:none;">
                                <select style="width: 120px;" id="lname_select" name="lname_select" multiple>
                                </select>
                            </div>
                        </th>
                        <th style="min-width:150px; text-align:center;">
                            <a href="{{ route('home', ['sort' => 'facility']) }}">Facility</a><i id="facility_i" class="typcn typcn-filter menu-icon"></i>
                            <div class="filter" id="facility_div" style="display:none;">
                                <select style="width: 120px;" id="facility_select" name="facility_select" multiple>
                                </select>
                            </div>
                        </th>
                        <th style="min-width:150px; text-align:center;">
                            <a href="{{ route('home', ['sort' => 'proponent']) }}">Proponent</a><i id="proponent_i" class="typcn typcn-filter menu-icon"></i>
                            <div class="filter" id="proponent_div" style="display:none;">
                                <select style="width: 120px;" id="proponent_select" name="proponent_select" multiple>
                                </select>
                            </div>
                        </th>
                        <th>@sortablelink('patient_code', 'Code')
                        </th>
                        <th style="min-width:150px;">@sortablelink('region', 'Region') <i id="region_i" class="typcn typcn-filter menu-icon"></i>
                            <div class="filter" id="region_div" style="display:none;">
                                <select style="width: 120px;" id="region_select" name="region_select" multiple>
                                </select>
                            </div>
                        </th>
                        <th style="min-width:150px; text-align:center;">
                            <a href="{{ route('home', ['sort' => 'province', 'order' => ($order == 'asc' ? 'desc' : 'asc')]) }}">Province</a>
                            <i id="province_i" class="typcn typcn-filter menu-icon"></i>
                            <div class="filter" id="province_div" style="display:none;">
                                <select style="width: 120px;" id="province_select" name="province_select" multiple>
                                </select>
                            </div>
                        </th>
                        <th style="min-width:150px; text-align:center;">
                            <a href="{{ route('home', ['sort' => 'municipality', 'order' => ($order == 'asc' ? 'desc' : 'asc')]) }}">Municipality</a>
                            <i id="muncity_i" class="typcn typcn-filter menu-icon"></i>
                            <div class="filter" id="muncity_div" style="display:none;">
                                <select style="width: 120px;" id="muncity_select" name="muncity_select" multiple>
                                </select>
                            </div>
                        </th>
                        <th style="min-width:150px; text-align:center;">
                            <a href="{{ route('home', ['sort' => 'barangay', 'order' => ($order == 'asc' ? 'desc' : 'asc')]) }}">Barangay</a>
                            <i id="barangay_i" class="typcn typcn-filter menu-icon"></i>
                            <div class="filter" id="barangay_div" style="display:none;">
                                <select style="width: 120px;" id="barangay_select" name="barangay_select" multiple>
                                </select>
                            </div>
                        </th>
                        <th style="min-width:150px">@sortablelink('created_at', 'Created On') <i id="on_i" class="typcn typcn-filter menu-icon"></i>
                            <div class="filter" id="on_div" style="display:none;">
                                <select style="width: 120px;" id="on_select" name="on_select" multiple>
                                </select>
                            </div>
                        </th>
                        <th style="min-width:190px">
                            <a href="{{ route('home', ['sort' => 'encoded_by', 'order' => ($order == 'asc' ? 'desc' : 'asc')]) }}">Created By</a>
                            <i id="by_i" class="typcn typcn-filter menu-icon"></i>
                            <div class="filter" id="by_div" style="display:none;">
                                <select style="width: 120px;" id="by_select" name="by_select" multiple>
                                </select>
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
                            </td>
                            <td class="td">
                                <div style="display: flex; align-items: center; gap: 5px;">
                                    <div>
                                        <a href="{{ route('patient.pdf', ['patientid' => $patient->id]) }}" 
                                        style="border-radius:0; background-color:teal; color:white; width:50px;" 
                                        target="_blank" 
                                        type="button" 
                                        class="btn btn-xs">Print</a>
                                        <a href="{{ route('patient.sendpdf', ['patientid' => $patient->id]) }}" 
                                        type="button" 
                                        style="margin-top:1px; border-radius:0; width:50px;" 
                                        class="btn btn-success btn-xs" 
                                        id="send_btn">Send</a>
                                    </div>
                                    <a href="{{ route('patient.accept', ['id' => $patient->id]) }}" style="margin-left:10px; font-size:14px"  title="Send this GL to facility">
                                        <i class="fa fa-paper-plane"></i>
                                    </a>
                                </div>
                            </td>
                            <td style="text-align:center;" class="group-email" data-patient-id="{{ $patient->id }}" >
                                <input class="sent_mails[] " id="mail_ids[]" name="mail_ids[]" type="hidden">
                                <input type="checkbox" style="width: 60px; height: 20px;" name="mailCheckbox[]" id="mailCheckboxId_{{ $patient->id }}" 
                                    class="group-mailCheckBox" onclick="itemChecked($(this))">
                            </td>
                            <td style="text-align:center">
                                @if($patient->remarks == 1)
                                    <i class="typcn typcn-tick menu-icon">
                                @endif
                            </td>
                            <td>
                                    {{$patient->pat_rem}}   
                            </td>
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
                            <td>{{date('F j, Y', strtotime($patient->date_guarantee_letter))}}</td>
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
                            <td style="text-align:center">
                                {{ date('F j, Y', strtotime($patient->created_at)) }}<br>
                                ( {{  date('H:i:s', strtotime($patient->created_at)) }} )
                            </td>
                            <td class="td">{{ $patient->user_type == null ? $patient->encoded_by->lname .', '. $patient->encoded_by->fname: ($patient->gl_user? $patient->gl_user->lname .', '. $patient->gl_user->fname:'') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
            @else
                <div class="alert alert-danger" role="alert" style="width: 100%;">
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
<div class="modal fade" id="create_patient" tabindex="-1" role="dialog" aria-hidden="true" style="opacity:1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="text-success" id="title"><i style = "font-size:30px"class="typcn typcn-user-add-outline menu-icon"></i> Create Patient</h4><hr />
                @csrf
            </div>
            <div class="modal_body">
                <form id="contractForm" method="POST" action="{{ route('patient.create.save') }}">
                    <input type="hidden" name="created_by" value="{{ $user->userid }}">
                    <div class="modal-body">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fname">First Name</label>
                                    <input type="text" class="form-control fname" style="width:220px;" id="fname" name="fname" oninput="this.value = this.value.toUpperCase()" placeholder="First Name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lname">Last Name</label>
                                    <input type="text" class="form-control lname" style="width:220px;" id="lname" name="lname" placeholder="Last Name" oninput="this.value = this.value.toUpperCase()" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fname">Middle Name</label>
                                    <input type="text" class="form-control mname" style="width:220px;" id="mname" name="mname" oninput="this.value = this.value.toUpperCase()" placeholder="Middle Name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lname">Date of Birth</label>
                                    <input type="date" class="form-control dob" style="width:220px;" id="dob" name="dob" placeholder="Date of Birth">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fname">Region</label>
                                    <select class="js-example-basic-single region" style="width:220px;" id="region" onchange="othersRegion($(this));" name="region">
                                        <option value="">Please select region</option>
                                        <option value="Region 7">Region 7</option>
                                        <option value="NCR">NCR</option>
                                        <option value="CAR">CAR</option>
                                        <option value="Region 1">Region 1</option>
                                        <option value="Region 2">Region 2</option>
                                        <option value="Region 3">Region 3</option>
                                        <option value="Region 4">Region 4</option>
                                        <option value="Region 5">Region 5</option>
                                        <option value="Region 6">Region 6</option>
                                        <option value="Region 8">Region 8</option>
                                        <option value="Region 9">Region 9</option>
                                        <option value="Region 10">Region 10</option>
                                        <option value="Region 11">Region 11</option>
                                        <option value="Region 12">Region 12</option>
                                        <option value="Region 13">Region 13</option>
                                        <option value="BARMM">BARMM</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lname">Province</label>
                                    <div id="province_body" class="province_body">
                                        <select class="js-example-basic-single province_id" style="width:220px;" id="province_id" name="province_id" onchange="onchangeProvince($(this))">
                                            <option value="">Please select province</option>
                                            @foreach($provinces as $prov)
                                                <option value="{{ $prov->id }}">{{ $prov->description }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lname">Municipality</label>
                                    <div id="muncity_body" class="muncity_body">
                                        <select class="js-example-basic-single muncity_id" style="width:220px;" id="muncity_id" name="muncity_id" onchange="onchangeMuncity($(this))">
                                            <option value="">Please select Municipality</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lname">Barangay</label>
                                    <div id="barangay_body" class="barangay_body">
                                        <select class="js-example-basic-single barangay_id" style="width:220px;" id="barangay_id" name="barangay_id">
                                            <option value="">Please select Barangay</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fname">Date of Guarantee Letter</label>
                                    <input type="date" class="form-control date_guarantee_letter" style="width:220px;" id="date_guarantee_letter" name="date_guarantee_letter" placeholder="Date of Guarantee Letter" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fname">Facility</label>
                                    <select class="js-example-basic-single facility_id1" style="width:220px;" id="facility_id" name="facility_id" onchange="onchangeForProponent($(this))" required>
                                        <option value="">Please select Facility</option>
                                        @foreach($facilities as $facility)
                                            <option value="{{ $facility->id }}">{{ $facility->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> 
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fname">Proponent</label>
                                    <select class="js-example-basic-single proponent_id1" style="width:220px;" id="proponent_id" name="proponent_id" onchange="onchangeForPatientCode($(this))" required>
                                        <option value="">Please select Proponent</option>
                                    </select>
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
                                    <input type="text" class="form-control guaranteed_amount" id="guaranteed_amount" style="width:220px;" oninput="check()" onkeyup= "validateAmount(this)" name="guaranteed_amount" placeholder="Guaranteed Amount" required>
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
                        <button type="submit" id="create_pat_btn" class="btn btn-primary">Create Patient</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--end-->
<div class="modal fade" id="update_patient" tabindex="-1" role="dialog" aria-hidden="true" style="opacity:1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="text-success" id="title"><i style = "font-size:30px"class="typcn typcn-user-add-outline menu-icon"></i>Update Patient</h4><hr />
                @csrf
            </div>
            <div class="modal_body">
                <form id="update_form" method="POST">
                    <input type="hidden" name="created_by" value="{{ $user->userid }}">
                    <div class="modal-body">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fname">First Name</label>
                                    <input type="text" class="form-control fname" style="width:220px;" id="fname" name="fname" oninput="this.value = this.value.toUpperCase()" placeholder="First Name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lname">Last Name</label>
                                    <input type="text" class="form-control lname" style="width:220px;" id="lname" name="lname" placeholder="Last Name" oninput="this.value = this.value.toUpperCase()" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fname">Middle Name</label>
                                    <input type="text" class="form-control mname" style="width:220px;" id="mname" name="mname" oninput="this.value = this.value.toUpperCase()" placeholder="Middle Name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lname">Date of Birth</label>
                                    <input type="date" class="form-control dob" style="width:220px;" id="dob" name="dob" placeholder="Date of Birth">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fname">Region</label>
                                    <select class="js-example-basic-single region" style="width:220px;" id="region" onchange="othersRegion($(this));" name="region">
                                        <option value="">Please select region</option>
                                        <option value="Region 7">Region 7</option>
                                        <option value="NCR">NCR</option>
                                        <option value="CAR">CAR</option>
                                        <option value="Region 1">Region 1</option>
                                        <option value="Region 2">Region 2</option>
                                        <option value="Region 3">Region 3</option>
                                        <option value="Region 4">Region 4</option>
                                        <option value="Region 5">Region 5</option>
                                        <option value="Region 6">Region 6</option>
                                        <option value="Region 8">Region 8</option>
                                        <option value="Region 9">Region 9</option>
                                        <option value="Region 10">Region 10</option>
                                        <option value="Region 11">Region 11</option>
                                        <option value="Region 12">Region 12</option>
                                        <option value="Region 13">Region 13</option>
                                        <option value="BARMM">BARMM</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lname">Province</label>
                                    <div id="province_body" class="province_body">
                                        <select class="js-example-basic-single province_id" style="width:220px;" id="province_id" name="province_id" onchange="onchangeProvince($(this))">
                                            <option value="">Please select province</option>
                                            @foreach($provinces as $prov)
                                                <option value="{{ $prov->id }}">{{ $prov->description }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lname">Municipality</label>
                                    <div id="muncity_body" class="muncity_body">
                                        <select class="js-example-basic-single muncity_id" style="width:220px;" id="muncity_id" name="muncity_id" onchange="onchangeMuncity($(this))">
                                            <option value="">Please select Municipality</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lname">Barangay</label>
                                    <div id="barangay_body" class="barangay_body">
                                        <select class="js-example-basic-single barangay_id" style="width:220px;" id="barangay_id" name="barangay_id">
                                            <option value="">Please select Barangay</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fname">Date of Guarantee Letter</label>
                                    <input type="date" class="form-control date_guarantee_letter" style="width:220px;" id="date_guarantee_letter" name="date_guarantee_letter" placeholder="Date of Guarantee Letter" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fname">Facility</label>
                                    <select class="js-example-basic-single facility_id1" style="width:220px;" id="facility_id" name="facility_id" onchange="onchangeForProponent($(this))" required>
                                        <option value="">Please select Facility</option>
                                        @foreach($facilities as $facility)
                                            <option value="{{ $facility->id }}">{{ $facility->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> 
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fname">Proponent</label>
                                    <select class="js-example-basic-single proponent_id1" style="width:220px;" id="proponent_id" name="proponent_id" onchange="onchangeForPatientCode($(this))" required>
                                        <option value="">Please select Proponent</option>
                                        @foreach($proponents as $pro)
                                            <option value="{{ $pro->id }}">{{ $pro->proponent }}</option>
                                        @endforeach
                                    </select>
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
                                    <input type="text" class="form-control guaranteed_amount" id="guaranteed_amount" style="width:220px;" oninput="check()" onkeyup= "validateAmount(this)" name="guaranteed_amount" placeholder="Guaranteed Amount" required>
                                </div>
                            </div>
                            <div class="col-md-6"  id="actl_amnt">
                                <div class="form-group">
                                    <label for="fname">Actual Amount</label>
                                    <input type="number" step="any" class="form-control actual_amount" id="actual_amount" name="actual_amount">
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
                        <button type="submit" id="create_pat_btn" class="btn btn-primary">Update</button>
                        <button type="submit" id="update_send" name="update_send" value="upsend" class="btn btn-success" style="color:white" >Update & Send</button>
                        <a type="button" class="btn btn-danger" onclick="removePatient()" style="display:none; color:white">Remove</a>
                        <a type="button" class="btn btn-warning" onclick="returnPatient()" style="display:none; color:white">Return</a>
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
<div class="modal fade" id="patient_history" tabindex="-1" role="dialog" aria-hidden="true" style="opacity:1">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 90vw;">
        <div class="modal-content">
            <div class="modal-header" style="vertical-align:middle">
                <h4 class="text-success" id="title"><i class="typcn typcn-location menu-icon"></i>Patient History</h4><hr/>
                @csrf
            </div>
            <div class="p_body" style="max-height: 70vh; overflow-y: auto;">
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
    $('#date_i').on('click', function(){
        $('#date_select').empty();
        $('#date_div').css('display', 'block');
        var date = @json($date);
        var filter_date = @json($filter_date);
        var filtered = filter_date.filter(item => item !== '');
        date.forEach(function(optionData) {
            var date_obj = moment(optionData);
            var isSelected = false;
            if(filtered.length !== 0){
              isSelected = filter_date.includes(optionData) ? true : false;
            }
            $('#date_select').append($('<option>', {
                value: optionData,
                text: date_obj.format('MMMM d, YYYY'),
                selected: isSelected
            }));
        });
    });
    $('#fname_i').on('click', function(){
        $('#fname_div').css('display', 'block');
        var fname = @json($fname);
        var filter_fname = @json($filter_fname);
        var filtered = filter_fname.filter(item => item !== '');
        fname.forEach(function(optionData) {
            var isSelected = false;
            if(filtered.length !== 0){
              isSelected = filter_fname.includes(optionData) ? true : false;
            }
            $('#fname_select').append($('<option>', {
                value: optionData,
                text: optionData,
                selected: isSelected
            }));
        });
    });
    $('#mname_i').on('click', function(){
        $('#mname_div').css('display', 'block');
        var mname = @json($mname);
        var filter_mname = @json($filter_mname);
        var filtered = filter_mname.filter(item => item !== '');
        mname.forEach(function(optionData) {
            var isSelected = false;
            if(filtered.length !== 0){
              isSelected = filter_mname.includes(optionData) ? true : false;
            }
            $('#mname_select').append($('<option>', {
                value: optionData,
                text: optionData,
                selected: isSelected
            }));
        });
    });
    $('#lname_i').on('click', function(){
        $('#lname_div').css('display', 'block');
        var lname = @json($lname);
        var filter_lname = @json($filter_lname);
        var filtered = filter_lname.filter(item => item !== '');
        lname.forEach(function(optionData) {
            var isSelected = false;
            if(filtered.length !== 0){
              isSelected = filter_lname.includes(optionData) ? true : false;
            }
            $('#lname_select').append($('<option>', {
                value: optionData,
                text: optionData,
                selected: isSelected
            }));
        });
    });
    $('#facility_i').on('click', function(){
        $('#facility_div').css('display', 'block');
        var fc_list = @json($fc_list);
        var filter_facility = @json($filter_facility);
        filtered = filter_facility.map(Number);
        fc_list.forEach(function(optionData) {
            var isSelected = false;
            var optionId = Number(optionData.id);
            if(filtered.length !== 0){
              isSelected = filtered.includes(optionId) ? true : false;
            }
            $('#facility_select').append($('<option>', {
                value: optionData.id,
                text: optionData.name,
                selected: isSelected
            }));
        });
    });
    $('#proponent_i').on('click', function(){
        $('#proponent_select').empty();
        $('#proponent_div').css('display', 'block');
        var pros = @json($pros);
        var filter_proponent = @json($filter_proponent);
        filter_proponent = filter_proponent.map(Number);
        pros.forEach(function(optionData) {
            var isSelected = false;
            var optionId = Number(optionData.id);
            if(filter_proponent.length !== 0){
              isSelected = filter_proponent.includes(optionId) ? true : false;
            }
            $('#proponent_select').append($('<option>', {
                value: optionData.id,
                text: optionData.proponent,
                selected: isSelected
            }));
        });
    });
    $('#region_i').on('click', function(){
        $('#region_div').css('display', 'block');
        $('#region_select').empty();
        var region = @json($region);
        var filter_region = @json($filter_region);
        filter_region = filter_region.filter(item => item !== '');
        region.forEach(function(optionData) {
            var isSelected = false;
            if(filter_region.length !== 0){
              isSelected = filter_region.includes(optionData) ? true : false;
            }
            $('#region_select').append($('<option>', {
                value: optionData,
                text: optionData,
                selected: isSelected
            }));
        });
    });
    $('#province_i').on('click', function(){
        $('#province_select').empty();
        $('#province_div').css('display', 'block');
        var province = @json($pro1).filter(item => item !== '' && item !== null);
        var filter_province = @json($filter_province);
        filter_province = filter_province.filter(item => item !== '');
        province.forEach(function(optionData) {
            var isSelected = false;
            if(filter_province.length !== 0){
              isSelected = filter_province.includes(optionData) ? true : false;
            }
            $('#province_select').append($('<option>', {
                value: optionData,
                text: optionData,
                selected: isSelected
            }));
        });

        var province2 = @json($prvnc);
        var prov = @json($filter_facility);
        prov = prov.map(Number);
        province2.forEach(function(optionData) {
            var isSelected = false;
            var optionId = Number(optionData.id);
            if(prov.length !== 0){
              isSelected = prov.includes(optionId) ? true : false;
            }
            $('#province_select').append($('<option>', {
                value: optionData.id,
                text: optionData.description,
                selected: isSelected
            }));
        });

    });
    $('#muncity_i').on('click', function(){
        $('#muncity_select').empty();
        $('#muncity_div').css('display', 'block');
        var muncity = @json($muncity).filter(item => item !== '' && item !== null);
        var filter_muncity = @json($filter_municipality);
        filter_muncity = filter_muncity.filter(item => item !== '');
        muncity.forEach(function(optionData) {
            var isSelected = false;
            if(filter_muncity.length !== 0){
              isSelected = filter_muncity.includes(optionData) ? true : false;
            }
            $('#muncity_select').append($('<option>', {
                value: optionData,
                text: optionData,
                selected: isSelected
            }));
        });

        var muncity2 = @json($mncty);
        var f_mun = @json($filter_municipality);
        f_mun = f_mun.map(Number);
        muncity2.forEach(function(optionData) {
            var isSelected = false;
            var optionId = Number(optionData.id);
            if(f_mun.length !== 0){
              isSelected = f_mun.includes(optionId) ? true : false;
            }
            $('#muncity_select').append($('<option>', {
                value: optionData.id,
                text: optionData.description,
                selected: isSelected
            }));
        });
    });
    $('#barangay_i').on('click', function(){
        $('#barangay_select').empty();
        $('#barangay_div').css('display', 'block');
        var barangay = @json($barangay).filter(item => item !== '' && item !== null);
        var filter_barangay = @json($filter_barangay);
        filter_barangay = filter_barangay.filter(item => item !== '');
        barangay.forEach(function(optionData) {
            var isSelected = false;
            if(filter_barangay.length !== 0){
              isSelected = filter_barangay.includes(optionData) ? true : false;
            }
            $('#barangay_select').append($('<option>', {
                value: optionData,
                text: optionData,
                selected: isSelected
            }));
        });

        var barangay2 = @json($brgy);
        var f_brgy = @json($filter_barangay);
        f_brgy = f_brgy.map(Number);
        barangay2.forEach(function(optionData) {
            var isSelected = false;
            var optionId = Number(optionData.id);
            if(f_brgy.length !== 0){
              isSelected = f_brgy.includes(optionId) ? true : false;
            }
            $('#barangay_select').append($('<option>', {
                value: optionData.id,
                text: optionData.description,
                selected: isSelected
            }));
        });
    });
    $('#on_i').on('click', function(){
        $('#on_select').empty();
        $('#on_div').css('display', 'block');
        var on = @json($on);
        var filter_on = @json($filter_on);
        filter_on = filter_on.filter(item => item !== '');
        on.forEach(function(optionData) {
            var date_obj = moment(optionData);
            var isSelected = false;
            if(filter_on.length !== 0){
              isSelected = filter_on.includes(optionData) ? true : false;
            }
            $('#on_select').append($('<option>', {
                value: date_obj.format('YYYY-MM-DD'),
                text: moment(optionData).format('MMMM D, YYYY'),
                selected: isSelected
            }));
        });

    });
    $('#by_i').on('click', function(){
        $('#by_select').empty();
        $('#by_div').css('display', 'block');
        var by = @json($by);
        var filter_by = @json($filter_by);
        filter_by = filter_by.map(Number);
        by.forEach(function(optionData) {
            var isSelected = false;
            var optionId = Number(optionData.userid);
            if(filter_by.length !== 0){
              isSelected = filter_by.includes(optionId) ? true : false;
            }
            $('#by_select').append($('<option>', {
                value: optionData.userid,
                text: optionData.lname + ", "+ optionData.fname,
                selected: isSelected
            }));
        });
    });
    $('#date_select').select2();
    $('#fname_select').select2();
    $('#mname_select').select2();
    $('#lname_select').select2();
    $('#facility_select').select2();
    $('#proponent_select').select2();
    $('#code_select').select2();
    $('#region_select').select2();
    $('#province_select').select2();
    $('#muncity_select').select2();
    $('#barangay_select').select2();
    $('#on_select').select2();
    $('#by_select').select2();

    $('#fname_select').change(function() {
        var selectedValues = $(this).val(); 
    });

    var proponents,all_patients;

    $.get("{{ url('patient') }}", function(result) {
        proponents = result.proponents;
        all_patients = result.all_pat;
    });

    $('#list_body').on('click', function(){
        $('.filter').hide();
    });

    $('.btn-secondary').on('click', function(e) {
        $('#contractForm')[0].reset();
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
            // if(all_patients){
                $('#patient_table').find('input.group-mailCheckBox').prop('checked', true).trigger('change');
                $('.send_mails').val('').show();

                // $('#patient_table').find('input.group-mailCheckBox:checked').each(function() {
                //     var p_id = $(this).val(); // or use $(this).data('id') if the ID is stored as a data attribute

                //     // Add the checkbox value (ID) to id_list
                //     id_list.push(String(p_id));

                //     // Create mailCheckboxId based on the checkbox value and add it to mail_ids
                //     mail_ids.push('mailCheckboxId_' + p_id);
                // });

                // all_patients.forEach(function(p){
                //     id_list.push(String(p.id));
                //     mail_ids.push('mailCheckboxId_'+p.id);
                // });
            // }
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

    function validateAmount(element) {
        if (event.keyCode === 32) {
            event.preventDefault();
        }
        var cleanedValue = element.value.replace(/[^\d.]/g, '');
        var numericValue = parseFloat(cleanedValue);

        if ((!isNaN(numericValue) || cleanedValue === '' || cleanedValue === '.') &&
            !(cleanedValue.length === 1 && cleanedValue[0] === '0')) {
                element.value = cleanedValue.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }else{
            element.value = '';
        }
    }

    $('#update_send').on('click', function(){
        $('.loading-container').show();
    });

    var edit_c = 0;

    function editPatient(id) {
        form_type='update';
        var patient;
        $.get("{{url('/gl/update').'/'}}" + id, function(result){
            patient = result.patients;
            ids = result.ids;
            id = patient.id
            edit_c = 1;
            var editRoute = `{{ route('patient.update', ['id' => ':id']) }}`;
            editRoute = editRoute.replace(':id', id);

            $('#update_form').attr('action', editRoute);
            if(patient){
                if(patient.group_id == null || patient.group_id == null){
                    var removeRoute = `{{ route('patient.remove', ['id' => ':id']) }}`;
                    removeRoute = removeRoute.replace(':id', id);
                    $('.btn.btn-danger').attr('data-id', id).css('display', 'inline-block').text('Remove');
                }
                $('.fname').val(patient.fname);
                $('.lname').val(patient.lname);
                $('.mname').val(patient.mname);
                $('.dob').val(patient.dob);
                $('.region').select2().val(patient.region).trigger('change');
                if(patient.region == "Region 7"){
                    $('.province_id').select2().val(patient.province_id).trigger('change');
                    $('.muncity_id').select2().val(patient.muncity_id).trigger('change');
                    $('.barangay_id').select2().val(patient.barangay_id).trigger('change');
                }else{
                    $('.other_province').val(patient.other_province);
                    $('.other_muncity').val(patient.other_muncity);
                    $('.other_barangay').val(patient.other_barangay);
                }
                $('.date_guarantee_letter').val(patient.date_guarantee_letter);
                $('.guaranteed_amount').val(patient.guaranteed_amount);
                $('.actl_amnt').show();
                $('.actual_amount').val(patient.actual_amount);

                var $proponentSelect = $('.proponent_id1'); 

                for (var i = 0; i < ids.length; i++) {
                    var id = ids[i]; 
                    if ($proponentSelect.find('option[value="' + id + '"]').length > 0) {
                        $proponentSelect.val(id).trigger('change').select2();
                        break; 
                    }
                }

                $('.facility_id1').val(patient.facility_id).trigger('change').select2();
                $('.patient_code').val(patient.patient_code);
                $('.remaining_balance').val(patient.remaining_balance);
                $('.pat_rem').val(patient.pat_rem);
            }
            edit_c = 0;
        });
    }
    
    var form_type = 'create';

    function othersRegion(data) {
        if(data.val() != "Region 7"){
            $(".province_body").html("<input type='text' class='form-control other_province' value='' id='other_province' name='other_province'>");
            $(".muncity_body").html("<input type='text' class='form-control other_muncity' id='other_muncity' name='other_muncity'>");
            $(".barangay_body").html("<input type='text' class='form-control other_barangay' id='other_barangay' name='other_barangay'>");
        }else {
            
            $(".province_body").html("<select class=\"js-example-basic-single w-100 province_id\" id=\"province_id\"  name=\"province_id\" onchange=\"onchangeProvince($(this))\">\n" +
                "\n" + "</select>");

            $('.province_id').append($('<option>', {
                value: "",
                text: "Please select a municipality"
            }));

            jQuery.each(JSON.parse('<?php echo $provinces; ?>'), function(i,val){
                $('.province_id').append($('<option>', {
                    value: val.id,
                    text : val.description
                }));
            });

            $(".muncity_body").html("<select class=\"form-control muncity_id\" id=\"muncity_id\" name=\"muncity_id\" onchange=\"onchangeMuncity($(this))\">\n" +
                "<option value=\"\">Please select municipality</option>\n" +
                "</select>");

            $(".barangay_body").html("<select class=\"form-control barangay_id\" id=\"barangay_id\" name=\"barangay_id\">\n" +
                "<option value=\"\">please select barangay</option>\n" +
                "</select>");
            if(form_type == 'update'){
                $(".muncity_id").select2({ width: '220px' });
                $(".barangay_id").select2({ width: '220px' });
                $(".province_id").select2({ width: '220px' });
            }else{
                $("#muncity_id").select2({ width: '220px' });
                $("#barangay_id").select2({ width: '220px' });
                $("#province_id").select2({ width: '220px' });
            }
        }
    } 

    function onchangeProvince(data) {
        $('.muncity_id').empty();
        $('.barangay_id').empty();
        var municipalities = @json($municipalities);
        var muncity = municipalities.filter(item => item.province_id == data.val());
        $('.muncity_id').append($('<option>', {
            value: "",
            text: "Please select a municipality"
        }));
        $('.barangay_id').append($('<option>', {
            value: "",
            text: "Please select a barangay"
        }));
        muncity.forEach(function(optionData) {
            $('.muncity_id').append($('<option>', {
                value: optionData.id,
                text: optionData.description
            }));
        });
    }

    function onchangeMuncity(data) {
        if(data.val()) {
            $('.barangay_id').html('');
            var barangays = @json($barangays);
            var barangay = barangays.filter(item => item.muncity_id == data.val());
            $('.barangay_id').append($('<option>', {
                value: "",
                text: "Please select a barangay"
            }));
            barangay.forEach(function(optionData) {
                $('.barangay_id').append($('<option>', {
                    value: optionData.id,
                    text: optionData.description
                }));
            });
        }
    }

    var facility_id = 0;

    function onchangeForProponent(data){
        if(edit_c == 0){
            if(data.val()){
                facility_id = data.val();
                $.get("{{ url('facility/proponent').'/' }}"+data.val(), function(result) {

                    $('#proponent_id').html('');
                    $('.patient_code').val('');
                    $('#proponent_id').append($('<option>', {
                        value: "",
                        text: "Select Proponent"
                    }));
                    $.each(result, function(index, optionData) {
                        $('#proponent_id').append($('<option>', {
                            value: result[index].id,
                            text: result[index].proponent
                        }));
                    });
                    $('#proponent_id').prop('disabled', false); 
                });
                $('.proponent_id1').val('').trigger('change');
            }
        }
    }
    
    function formatBalance(balance) {
        return new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }).format(balance);
    }

    function onchangeForPatientCode(data) {

        if(facility_id == 0){
            facility_id = $('#facility_id').val();
        }
        if(edit_c == 0){
            if(data.val()) {
                $.get("{{ url('patient/code').'/' }}"+data.val()+"/"+facility_id, function(result) {
                    $(".patient_code").val(result.patient_code);
                    const formattedBalance = new Intl.NumberFormat('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2,
                    }).format(result.balance);
                    if(result.balance == 0 || result.balance < 0){

                        data.val('').trigger('change');
                        $('#patient_code').val('');
                        Swal.fire({
                            icon: 'error',
                            title: 'Insufficient Balance!',
                            text: 'Remaining balance is now ' + formattedBalance,
                            timer: 2000, 
                            showConfirmButton: false
                        });
                    }else{
                        $('.remaining_balance').val(formattedBalance);
                        var suggestions =[];
                        suggestions.push('Breakdowns: ');
                        suggestions.push('Allocated Funds - ' + formatBalance(result.total_funds));
                        suggestions.push('Sum of all GL - ' + formatBalance(result.gl_sum));
                        // suggestions.push('DV - ' + formatBalance(result.disbursement));
                        suggestions.push('Supplemental Funds - ' + formatBalance(result.supplemental));
                        suggestions.push('Negative Amount - ' + formatBalance(result.subtracted));
                        suggestions.push('Remaining Funds - ' + formatBalance(result.balance));
                        
                        var suggestionsDiv = $('.suggestions');
                        suggestionsDiv.empty();

                        suggestions.forEach(function(suggestion) {
                            suggestionsDiv.append('<div>' + suggestion + '</div>');
                        });
                        suggestionsDiv.show();
                    }
                   
                });
            }
        }
    }

    function parseNumberWithCommas(value) {
        if(typeof value === 'string'){
            return parseFloat(value.replace(/,/g, '')) || 0;
        } else{
            return parseFloat(value) || 0;
        }
    }

    function check(){
        var rem = parseNumberWithCommas($('#remaining_balance').val());
        var g_amount = parseNumberWithCommas($('#guaranteed_amount').val());
        if(g_amount>rem){
            Lobibox.alert('error', {
                size: 'mini',
                msg: 'Inputted amount is greater than the remaning balance!'
            })
            $('#guaranteed_amount').val('');
        }
    }

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

    function removePatient(){
        var id = event.target.getAttribute('data-id');
        Lobibox.alert('error',
            {
                size: 'mini',
                msg: '<div style="text-align:center;"><i class="typcn typcn-delete menu-icon" style="color:red; font-size:30px"></i>Are you sure you want to delete this?</div>',
                buttons:{
                    ok:{
                        'class': 'lobibox-btn lobibox-btn-ok',
                        text: 'Delete',
                        closeOnClick: true
                    },
                    cancel: {
                        'class': 'lobibox-btn lobibox-btn-cancel',
                        text: 'Cancel',
                        closeOnClick: true
                    }
                },
                callback: function(lobibox, type){
                    if (type == "ok"){
                        window.location.href="patient/remove/" + id;
                    }
                }
            }
        )
    }

    function returnPatient(){
        var id = event.target.getAttribute('data-id');
        window.location.href= "patient/return/" + id;
    }
</script>
@endsection
