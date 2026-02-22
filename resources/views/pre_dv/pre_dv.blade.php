@extends('layouts.app')
@section('content')
<style>
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: green;
        color: white;
    }
    .red-option {
        background-color: red !important;
        color: white !important;
    }

    .green-option {
        background-color: green !important;
        color: white !important;
    }
    .loading-container {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1060; 
        display: none; 
    }
    .loading-spinner {
        width: 100%; 
        height: 100%; 
    }
</style>
<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <div class="float-right">
                <div class="input-group">
                    <form method="GET" action="">
                        <div class="input-group">
                            <input type="text" class="form-control" name="keyword" placeholder="Search..." value="{{$keyword}}" id="search-input" style="width:350px;">
                            <div class="input-group-append">
                                <button class="btn btn-sm btn-info" type="submit"><img src="\maif\public\images\icons8_search_16.png">Search</button>
                                <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll"><img src="\maif\public\images\icons8_eye_16.png">View All</button>
                                <button type="button" class="btn btn-success" data-toggle="modal" data-backdrop="static" href="#create_predv" style="width:95px; display: inline-flex; align-items: center; border-radius: 0;"><img src="\maif\public\images\icons8_create_16.png" style="margin-right: 5px;"><span style="vertical-align: middle;">Create</span></button>      
                                <button type="submit" value="filt" style="display:none; background-color:green; color:white; width:95px;" name="filt_dv" id="filt_dv" class="btn btn-xs"><i class="typcn typcn-filter menu-icon"></i>&nbsp;&nbsp;&nbsp;Filter</button>
                            </div>
                        </div>
                        <div class = "input-group">
                            <input type="text" style="text-align:center" class="form-control" id="dates_filter" value="{{ $generated_dates }}" name="dates_filter" />
                            <button type="submit" id="gen_btn" style="background-color:teal; color:white; width:95px; border-radius: 0; font-size:11px" class="btn btn-xs"><i class="typcn typcn-calendar-outline menu-icon"></i>Generate</button>
                        </div>
                        <input type="hidden" name="f_id" class="fc_id" value="{{ implode(',',$f_id) }}">
                        <input type="hidden" name="b_id" class="user_id" value="{{ implode(',',$b_id) }}">
                        <input type="hidden" id="generate" name="generate" value="{{ $generate }}"></input>
                    </form>
                </div>
            </div>
            <h4 class="card-title">PRE - DV</h4>
            <p class="card-description">
                MAIF-IPP
            </p>
            <div class="table-responsive">
                @if(count($results) > 0)
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="min-width:250px"></th>
                                <th style="min-width:120px">Route No</th>
                                <th class="fc">Facility
                                    <i id="fac_i" class="typcn typcn-filter menu-icon"><i>
                                    <div class="filter" id="fac_div" style="display:none;">
                                        <select style="width: 120px;" id="fac_select" name="fac_select" multiple>
                                            <?php $check = []; ?>
                                            @foreach($facility_data as $index => $d)
                                                @if(!in_array($d->id, $check))
                                                    <option value="{{ $d->id }}" {{ is_array($f_id) && in_array($d->id, $f_id) ? 'selected' : '' }}>
                                                        {{ $d->name}}
                                                    </option>
                                                    <?php $check[] = $d->id; ?>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>  
                                </th>
                                <th>No. of Transmittals</th>
                                <th style="min-width:150px">Professional Fee</th>
                                <th style="min-width:100px">Grand Total</th>
                                <th class="user" style="min-width:120px">Created By
                                    <i id="by_i" class="typcn typcn-filter menu-icon"><i>
                                    <div class="filter" id="by_div" style="display:none;">
                                        <select style="width: 120px;" id="by_select" name="by_select" multiple>
                                            <?php $check = []; ?>
                                            @foreach($user_data as $index => $d)
                                                @if(!in_array($d->userid, $check))
                                                    <option value="{{ $d->userid }}" {{ is_array($b_id) && in_array($d->userid, $b_id) ? 'selected' : '' }}>
                                                        {{ $d->fname .' '.$d->lname }}
                                                    </option>
                                                    <?php $check[] = $d->userid; ?>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>  
                                </th>
                                <th style="min-width:120px">Created On</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results as $row)
                                <tr>
                                    <td>
                                        <a href="{{ route('pre.excel', ['id' => $row->id]) }}" style="background-color:teal; border-radius:0; color:white; width:70px;" type="button" class="btn btn-xs">
                                            <i class="fa fa-file-excel"></i> Excel
                                        </a>    
                                        @if($row->new_dv)                                  
                                            <button type="button" class="btn btn-xs" style="border-radius:0; background-color:#165A54; color:white;" data-toggle="modal" href="#iframeModal" data-routeId="{{$row->new_dv->route_no}}" id="track_load" onclick="openModal()">
                                                <i class="fa fa-search-location"></i> Track
                                            </button>
                                            <a href="{{ route('pre.pdf', ['id' => $row->id]) }}" style="background-color:green; border-radius:0; color:white; width:70px;" target="_blank" type="button" class="btn btn-xs">
                                                <i class="fa fa-print"></i> Print
                                            </a>
                                            <a href="{{ route('pre.image', ['id' => $row->id]) }}" style="background-color:blue; border-radius:0; color:white; width:70px;" target="_blank" type="button" class="btn btn-xs">
                                                <i class="fa fa-image"></i> Image
                                            </a>    
                                        @else
                                            <a data-toggle="modal" style="border-radius:0; margin-left:5px" title="Create Pre-DV (v2)" data-backdrop="static" href="#view_v2" onclick="viewV1({{ $row->id }})" class="text-danger"><i>dv is not yet created</i></a>
                                        @endif
                                    </td>
                                    <td>
                                        @if($row->new_dv)
                                            {{ $row->new_dv->route_no }}
                                        @endif
                                    </td>
                                    <td><a data-toggle="modal" data-backdrop="static" href="#update_predv" onclick="updatePre( {{ $row->id }}, {{ $row->new_dv?1:2 }}, {{ $row->new_dv && $row->new_dv->edit_status == 1 ? 1: 0 }} )">{{ $row->facility->name }}</a></td>
                                    <td>
                                        <?php
                                            $total = 0;
                                            foreach($row->extension as $item){
                                                $total = $total + count($item->controls);
                                            }
                                            echo $total;
                                        ?>
                                    </td>
                                    <td>{{ $row->prof_fee != null ? number_format(str_replace(',','',$row->prof_fee), 2, '.',',') : '0.00' }}</td>
                                    <td>{{ number_format(str_replace(',','',$row->grand_total), 2, '.',',') }}</td>
                                    <td>{{ $row->user->lname .', '.$row->user->fname }}</td>
                                    <td>{{ date('F j, Y', strtotime($row->created_at)) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-danger" role="alert" style="width: 100%; margin-top:5px;">
                        <i class="typcn typcn-times menu-icon"></i>
                        <strong>No data found!</strong>
                    </div>
                @endif
            </div>
            <div class="pl-5 pr-5 mt-5 alert alert-info" role="alert" style="width: 100%; margin-top:5px;">
                <strong>Total number of data generated: {{ $num_generated }}</strong>
                <strong style="margin-left: 20px;">|</strong>
                <strong style="margin-left: 20px;">Total No. of transmittals:  {{  number_format($total_control, 2,'.',',') }}</strong>
                <strong style="margin-left: 20px;">|</strong>
                <strong style="margin-left: 20px;">Total Professional Fee:  {{  number_format($grand_fee, 2,'.',',') }}</strong>
                <strong style="margin-left: 20px;">|</strong>
                <strong style="margin-left: 20px;">Total amount:  {{ number_format($grand_amount, 2,'.',',') }}</strong>
            </div>
            <div class="pl-5 pr-5 mt-5">
                {!! $results->appends(request()->query())->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="create_predv" role="dialog" style="overflow-y:scroll;" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="width:700px">
        <div class="modal-content">
            <div class="modal-header" style="text-align:center">
                <h5 class="text-success modal-title">Pre - DV</h5>
            </div>
            <div class="modal-body" style="display: flex; flex-direction: column; align-items: center;">
                <form class="pre_form" id="pre_form" style="width:100%; font-weight:1px solid black" method="get" >
                    @csrf
                    <input type="hidden" class="status" value="0">
                    <div style="width: 100%; display:flex; justify-content: center;text-align:center;">
                        <select class="select2 facility_id" style="width: 50%;" name="facility_id" onchange="getFundsource($(this).val())" required>
                            <option value=''>SELECT FACILITY</option>
                            @foreach($facilities as $facility)
                                <option datavat="{{ ($facility->addFacilityInfo && $facility->addFacilityInfo->vat)?1:0 }}" 
                                    value="{{ $facility->id }}">{{ $facility->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="text-info" style="width: 100%; display:flex; justify-content: center;text-align:center; margin-top:10px">
                        <u onclick="displayControl()" class="text-info">Check Available Control No</u>
                    </div>
                    <div class="control_option" style="width: 100%; display:none; justify-content: center;text-align:center; margin-top:10px">
                        <select class="select2 control_id" style="width: 50%;" name="control_id" onchange="getTransmittal($(this))">
                            <option value=''>SELECT AVAILABLE CONTROL</option>
                        </select>
                    </div>
                    <div class="selected_control" style="width: 100%; display:none; justify-content: center;text-align:center; margin-top:10px">
                        <input style="width:50%; text-align: center;" name="selected_no" id="selected_no" class="form-control selected_no">
                    </div>
                    <input type="hidden" name="trans_control_no">
                    <div class="facility_div">
                        <div class="fac_control"></div>
                        <div class="proponent_clone" style="text-align: center; border: 1px solid black; width: 100%; padding: 3%;  margin-top: 10px; ">
                            <div class="card" style="border: none;">
                                <div class="row" style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 4%;">
                                    <i class="typcn typcn-minus menu-icon btn_pro_remove" style="width:40px; background-color:red; color:white;border: 1px; padding: 2px;"></i>
                                    <select style="width: 50%; margin-bottom: 10px;" class="select2 proponent" onchange="checkPros(this)" required>
                                        <option value=''>SELECT PROPONENT</option>
                                        @foreach($proponents as $proponent)
                                            <option value="{{ $proponent->proponent }}">{{ $proponent->proponent }}</option>
                                        @endforeach
                                    </select>
                                    <i onclick="cloneProponent($(this))" class="typcn typcn-plus menu-icon" style="width:40px; background-color:blue; color:white;border: 1px; padding: 2px;"></i>
                                </div>
                                <div class="control_div">
                                    <div class="control_clone" style="padding: 10px; border: 1px solid lightgray;">
                                        <div style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 4%;">
                                            <input class="form-control control_no" onblur="checkControlNo(this)" style="text-align: center; width: 56%;" placeholder="CONTROL NUMBER" oninput="this.value = this.value.toUpperCase()" required>
                                            <i class="typcn typcn-plus menu-icon control_clone_btn" style="width:40px;background-color:blue; color:white;border: 1px; padding: 2px;"></i>
                                        </div>
                                        <div style="display: flex; justify-content: space-between;">
                                            <input placeholder="PATIENT" class="form-control patient_1" style="width: 41%;" oninput="this.value = this.value.toUpperCase()" required>
                                            <input placeholder="AMOUNT/TRANSMITTAL" class="form-control amount" onkeyup="validateAmount(this)" style="width: 50%;" required>
                                        </div>
                                        <div style="display: flex; justify-content: space-between;">
                                            <input placeholder="PATIENT" class="form-control patient_2" style="width: 41%; margin-top: 5px;" oninput="this.value = this.value.toUpperCase()">
                                            <input placeholder="PROFESSIONAL FEE" class="form-control prof_fee" onkeyup="validateAmount(this)" style="width: 50%; margin-top: 5px;">
                                        </div>
                                    </div>
                                </div>
                                <div style="display: flex; justify-content: flex-end; margin-top: 5%; margin-bottom: 5%;">
                                    <input class="form-control total_amount" style="width: 60%; text-align: center;" placeholder="TOTAL AMOUNT PER PROPONENT" readonly>
                                </div>
                                <div class="saa_clone" style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 2%;">
                                    <select style="width: 50%;" class="select2 saa_id" onchange="autoDeduct($(this))" required>
                                        <option value=''></option>
                                        <!-- @foreach($saas as $saa)
                                            <option value="{{$saa->id}}" data-balance="{{$saa->alocated_funds}}">{{$saa->saa}}</option>
                                        @endforeach -->
                                    </select>
                                    <input placeholder="AMOUNT" class="form-control saa_amount" onkeyup="validateAmount(this)" style="width: 35%;" required>
                                    <i class="typcn typcn-plus menu-icon saa_clone_btn" style="width:40px;background-color:blue; color:white;border: 1px; padding: 2px;"></i>
                                </div>
                                <div style="display:inline-block;">
                                    <span class="text-info">Total fundsource inputted amount:</span>
                                    <span class="text-danger inputted_amount" id="inputted_amount"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div style="display: flex; justify-content: space-between; margin-top: 5%;">
                        <label style="width: 48%; text-align: center;">GRAND TOTAL OF PROFESSIONAL FEE:</label>
                        <label style="width: 48%; text-align: center;">GRAND TOTAL:</label>
                    </div> -->
                    <div style="display: flex; justify-content: space-between; margin-top: 5%; margin-bottom:5%;">
                        <input class="form-control grand_fee" name="grand_fee" style="width: 48%; text-align: center;" placeholder="TOTAL PROFESSIONAL FEE" readonly>
                        <input class="form-control grand_total" name="grand_total" style="width: 48%; text-align: center;" placeholder="GRAND TOTAL" readonly>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-sm btn-secondary crt_btn" data-dismiss="modal" onclick="this.blur();">CLOSE</button>
                        <button type="submit" class="btn-sm btn-success submit_btn">SUBMIT</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="update_predv" role="dialog" style="overflow-y:scroll;" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="width:700px">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#17c964;padding:15px; color:white">
                <h4 class="modal-title"><i class="fa fa-plus" style="margin-right:auto;"></i>Update Pre - DV</h4>
                <button type="button" class="close" id="exit" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:white;">&times;</span></button>
            </div>
            <div class="pre_body" style="display: flex; flex-direction: column; align-items: center; padding:15px">
                <form class="pre_form1" id="pre_form" style="width:100%; font-weight:1px solid black" method="get" >
                    <div class="form_body"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn-sm btn-secondary update_close" data-dismiss="modal">CLOSE</button>
                        <button type="button" onclick="deletePre()" class="btn-sm btn-warning delete_btn">DELETE</button>
                        <button type="submit" class="btn-sm btn-success submit_btn">SUBMIT</button>
                    </div>
                </form>
            </div>        
        </div>
    </div>
</div>
<div class="modal fade" id="view_v2" role="dialog" style="overflow-y:scroll;">
    <div class="modal-dialog modal-lg" role="document" style="width:1000px">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#17c964;padding:15px; color:white">
                <h4 class="modal-title"><i class="fa fa-plus" style="margin-right:auto;"></i>DV ( new version )</h4>
                <button type="button" class="close" id="exit" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:white;">&times;</span></button>
            </div>
            <div class="pre_body_dv" style="display: flex; flex-direction: column; align-items: center; padding:15px">

            </div>
        </div>
    </div>
</div>
<div class="loading-container" style="display:none">
    <img src="\maif\public\images\loading.gif" alt="Loading..." class="loading-spinner">
</div>

@include('modal')
@endsection
@section('js')
<script src="{{ asset('admin/vendors/sweetalert2/sweetalert2.js?v=1') }}"></script>
<script src="{{ asset('admin/vendors/daterangepicker-master/moment.min.js?v=1') }}"></script>
<script src="{{ asset('admin/vendors/daterangepicker-master/daterangepicker.js?v=1') }}"></script>
<script>
    $('#create_predv, #update_predv, #view_v2, #releaseTo').on('hide.bs.modal', function () {
        $(this).find('input, select, textarea, button').blur();
        console.log('sample');
    });
    $('#docViewerModal').hide();
    var btn_val = 0;

    $('.crt_btn').on('click', function(){
        btn_val = 1;
        location.reload();
    });

    $('.delete_btn').on('click', function(){
        $('#update_predv').modal('hide');
        $('.loading-container').modal('show');
        $('.loading-container').html(loading);
    });

    function viewV1(id) {
        $('.pre_body_dv').empty();
        $('.pre_body_dv').html(loading);

        $.get("{{ url('pre-dv/v2/').'/' }}" + id, function(result) {
            $('.pre_body_dv').html(result);
        });
    }

    function displayControl(){
        $('.control_option').css('display', 'block');
    }

    // Build SAA options HTML (called once per proponent)
    function buildSAAOptionsHTML(proponent_name, facility_id) {
        // Check cache first
        var cacheKey = proponent_name + '_' + facility_id;
        if (saaOptionsCache[cacheKey]) {
            return saaOptionsCache[cacheKey];
        }

        if (!all_options || !all_options.info) {
            console.warn('Fundsource data not loaded yet');
            return '<option value="">SELECT SAA</option>';
        }

        var data_result = all_options.info;
        var facilitiesArray = Array.isArray(all_options.facilities) ? all_options.facilities : [];
        
        var first = [], sec = [], third = [], fourth = [], fifth = [], six = [];

        $.each(data_result, function(index, optionData) {
            var rem_balance = parseFloat(optionData.remaining_balance.replace(/,/g, ''))
                .toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            var pro_name = optionData.proponent.proponent;
            var rem_balance_num = parseFloat(optionData.remaining_balance.replace(/,/g, ''));

            var check_p = 0;
            var id = optionData.facility_id;
            var name_id = optionData.facility_id;

            if (typeof name_id === 'string') {
                try {
                    name_id = JSON.parse(name_id);
                } catch (e) {
                    name_id = [name_id];
                }
            }
            if (typeof name_id === 'number') {
                name_id = [String(name_id)];
            }

            var facilityNames = facilitiesArray
                .filter(f => name_id.includes(String(f.id)))
                .map(f => f.name)
                .join(' & ');

            var text_display;
            if (optionData.facility !== null) {
                if (optionData.facility.id == facility_id) {
                    text_display = optionData.fundsource.saa + ' - ' + optionData.proponent.proponent + ' - ' + facilityNames + ' - ' + rem_balance;
                } else {
                    text_display = optionData.fundsource.saa + ' - ' + optionData.proponent.proponent + ' - ' + facilityNames + ' - ' + rem_balance;
                    check_p = 1;
                }
            } else {
                if (id.includes('702')) {
                    check_p = 0;
                } else {
                    check_p = 1;
                }
                text_display = optionData.fundsource.saa + ' - ' + optionData.proponent.proponent + ' - ' + facilityNames + ' - ' + rem_balance;
            }

            var color = '';
            var obj;

            if (rem_balance == '0' || rem_balance == '0.00' || rem_balance_num <= 0) {
                color = 'red';
                obj = {
                    value: optionData.fundsource_id,
                    text: text_display,
                    dataval: optionData.remaining_balance,
                    dataproponentInfo_id: optionData.id,
                    dataprogroup: optionData.proponent.pro_group,
                    dataproponent: optionData.proponent.id,
                    d_color: color
                };

                if (optionData.fundsource.saa.includes('CONAP')) {
                    fifth.push(obj);
                } else {
                    six.push(obj);
                }
            } else {
                color = 'normal';
                proponent_name = proponent_name.replace(/\s+/g, ' ').trim();
                pro_name = pro_name.replace(/\s+/g, ' ').trim();

                if (check_p != 1 && proponent_name == pro_name) {
                    color = "normal";
                } else {
                    color = "#DAA520";
                }

                obj = {
                    value: optionData.fundsource_id,
                    text: text_display,
                    dataval: optionData.remaining_balance,
                    dataproponentInfo_id: optionData.id,
                    dataprogroup: optionData.proponent.pro_group,
                    dataproponent: optionData.proponent.id,
                    d_color: color
                };

                if (optionData.fundsource.saa.includes('CONAP')) {
                    if (check_p != 1 && proponent_name == pro_name) first.push(obj);
                    else third.push(obj);
                } else {
                    if (check_p != 1 && proponent_name == pro_name) sec.push(obj);
                    else fourth.push(obj);
                }
            }
        });

        function buildOptions(arr) {
            let html = "";
            arr.forEach(o => {
                let coloredHTML = `<span style="color:${o.d_color}">${o.text}</span>`;
                html += `
                <option 
                    value="${o.value}"
                    data-color="${o.d_color}"
                    dataval="${o.dataval}"
                    dataproponentinfo_id="${o.dataproponentInfo_id}"
                    dataprogroup="${o.dataprogroup}"
                    dataproponent="${o.dataproponent}"
                    data-html='${coloredHTML.replace(/'/g, "&apos;")}'
                >${o.text}</option>`;
            });
            return html;
        }

        var final_html = '<option value="">SELECT SAA</option>' +
            buildOptions(first) +
            buildOptions(sec) +
            buildOptions(third) +
            buildOptions(fourth) +
            buildOptions(fifth) +
            buildOptions(six);

        // Cache the result
        saaOptionsCache[cacheKey] = final_html;
        
        return final_html;
    }

    // Update a SINGLE SAA select element
    function updateSAAOption(data_select, proponent_select) {
        var facility_id = $('.facility_id').val();
        var proponent_name = $(proponent_select).find('option:selected').text();

        if (!proponent_name || proponent_name == '' || proponent_name == 'undefined') {
            proponent_name = $(proponent_select).val();
        }

        if (!proponent_name || proponent_name == '' || proponent_name == 'undefined') {
            return false;
        }

        // Wait for data if not loaded
        if (!all_options || !all_options.info) {
            $.get("{{ url('fetch/pre-dv/fundsource').'/' }}" + facility_id, function(result) {
                if (result && result.info) {
                    all_options = result;
                    saaOptionsCache = {}; // Clear cache
                    updateSAAOption(data_select, proponent_select);
                }
            });
            return;
        }
        // Build options HTML
        var optionsHTML = buildSAAOptionsHTML(proponent_name, facility_id);
        
        // Update only this select element
        data_select.html(optionsHTML);
        
        data_select.select2({
            escapeMarkup: m => m,
            templateResult: function (data) {
                return $(data.element).data("html") || data.text;
            },
            templateSelection: function (data) {
                return $(data.element).data("html") || data.text;
            }
        });
    }

    var all_control = [];
    var all_trans = [];

    function getTransmittal(data) {  
        var trans_id = data.val();
        var trans_control = data.find('option:selected').text();
        $('.loading-container').modal('show');
        $('.selected_control').css('display', 'flex'); 
        
        if (!all_control.includes(trans_control)) {
            all_control.push(trans_control);
        }

        if (!all_trans.includes(trans_id)) {
            all_trans.push(trans_id);
        }

        $('#selected_no').val("Selected Control No: " + all_control);
        $('#trans_control_no').val(all_trans.join(','));

        $.get("{{ url('transmittal/details').'/' }}" + trans_id + '/' + f_id, function(result) {
            var $html = $('<div>').html(result);
            var result_proponent = $html.find('.proponent').first().val();
            var $controlClones = $html.find('.control_div .control_clone');
            var check = 0;
            var result_p = $html.children('.proponent_clone');

            var unused_rem = [];

            $('.proponent').each(function () {
                var $proponentInput = $(this);
                var proponentVal = $proponentInput.val();
                var a = proponentVal.replace(/\s+/g, ' ').trim().normalize();

                if (a !== null && a !== "") {

                    result_p.each(function(){
                        var c_clone = $(this).find('.control_clone');
                        var sample = $(this);

                        var in_pro = $(this).find('.proponent').val();

                        var in_clone = $(this).closest('.proponent_clone');
                        
                        var b = in_pro.replace(/\s+/g, ' ').trim().normalize();

                        var $proponentClone = $proponentInput.closest('.proponent_clone');
                        var $controlDiv = $proponentClone.find('.control_div');

                        if (a === b) {
                            $controlDiv.append(c_clone);

                            var total_amount = 0;
                            $controlDiv.find('.control_clone .amount').each(function () {
                                var val = parseFloat($(this).val().replace(/,/g, '')) || 0;
                                total_amount += val;
                            });

                            var $totalAmountInput = $proponentClone.find('.total_amount');
                            if ($totalAmountInput.length) {
                                $totalAmountInput.val(total_amount.toLocaleString('en-US', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                }));
                            }

                            check = 1;
                        }else{
                            unused_rem.push(in_clone);
                        }
                    });
                }
            });

            if(check == 0){
                var newBlocks = $(result);

                $('.fac_control').append(newBlocks); 

                newBlocks.find('.saa_id').each(function () {
                    $(this).select2();       
                    var proponent_input = $(this)
                        .closest('.proponent_clone')
                        .find('.proponent');
                    updateSAAOption($(this), proponent_input); 
                });
            }else{
                unused_rem.forEach(function(element){
                    // var transd_id = element.transd_id;
                    var transd_id = $(element).find('.proponent').val();

                    var alreadyExists = $('.proponent').filter(function() {
                        return $(this).val() == transd_id; 
                    }).length > 0;

                    if (!alreadyExists) {
                        $(element).insertAfter($('.fac_control .proponent_clone').last());
                        var saaSelect = $(element).find('.saa_id');
                        saaSelect.select2();
                        var proponent_input = $(element)
                            .closest('.proponent_clone')
                            .find('.proponent');
                        updateSAAOption(saaSelect, proponent_input);
                    }
                });
            }

            getGrand();
            getGrandFee();
            
        });

        all_trans.forEach(function(value) {
            $('.control_id option').each(function() {
                if ($(this).val() == value) {
                    $(this).remove();
                }
            });
        });

        setTimeout(function () {
            $('.loading-container').modal('hide');
        }, 5000);
    }

    $('#gen_btn').on('click', function(){
        $('#generate').val(1);
    });
    
    $('.select2').select2();
    $('.saa_id').select2({
        placeholder: "Select SAA"
    });
    $('#fac_select').select2();
    $('#by_select').select2();
    
    $('#by_select, #fac_select').on('change', function(){
        $('#filt_dv').trigger('click');
    });

    $('#dates_filter').daterangepicker();

    $('.fc').on('click', function(){
        $('#fac_div').css('display', 'block');
    });

    $('.user').on('click', function(){
        $('#by_div').css('display', 'block');
    });

    $('#filt_dv').on('click', function(){
        $('.fc_id').val($('#fac_select').val());
        $('.user_id').val($('#by_select').val());
    }); 

    $('#create_predv').on('hidden.bs.modal', function () {
        $(this).find('form')[0].reset(); 
        $('.facility_div .proponent_clone:not(:first)').remove();
        $('.facility_div .proponent_clone .control_clone:not(:first)').remove();
        $('.facility_div .saa_clone:not(:first)').remove();
        $('.facility_id, .proponent, .saa_id').val('').trigger('change');
        $('.inputted_amount').text('');
    });

    $('.update_close').on('click', function(){
        location.reload();
    })

    var all_options = null;
    var saaOptionsCache = {};

    function checkPros(data) {

        var saa_list = $(data).closest('.proponent_clone').find('.saa_id');
        var facility_id = $('.facility_id').val();
        var proponent_name = data.options[data.selectedIndex].text;
        
        if (!all_options || !all_options.info) {

            $.get("{{ url('fetch/pre-dv/fundsource').'/' }}"+facility_id, function(result) {
                var data_result = result.info;
                all_options = result;
            });
        }

        // Build options once
        var optionsHTML = buildSAAOptionsHTML(proponent_name, facility_id);

        // Update all SAA selects in this proponent clone efficiently
        saa_list.each(function() {
            var $this = $(this);
            $this.html(optionsHTML);

            $this.select2({
                escapeMarkup: m => m,
                templateResult: function (data) {
                    return $(data.element).data("html") || data.text;
                },
                templateSelection: function (data) {
                    return $(data.element).data("html") || data.text;
                }
            });
            $this.prop('disabled', false);
        });

        // Validate duplicate proponent
        var arr = getPros();
        var index = arr.indexOf(data.value);
        if (index !== -1) {
            arr.splice(index, 1);
        }
        if (arr.includes(data.value)) {
            alert('This proponent has been selected already!');
            return;
        }
    }

    function getPros(){
        var pros = [];
        $('.proponent').each(function(){
            pros.push($(this).val());
        });
        return pros;
    }
    
    var f_id = $('.facility_id').val();

    function openModal() {
        var routeNoo = event.target.getAttribute('data-routeId'); 
        var src = "http://192.168.110.17/dts/document/trackMaif/" + routeNoo;

        var base_url = "{{ url('/') }}";
        $('.modal-body').append('<img class="loadingGif" src="' + base_url + '/public/images/loading.gif" alt="Loading..." style="display:block; margin:auto;">');

        var iframe = $('#trackIframe');

        iframe.hide();

        iframe.attr('src', src);
    
        iframe.on('load', function() {
            iframe.show(); 
            $('.loadingGif').css('display', 'none');
        });

        $('#myModal').modal('show');
    }

    function error(){
        Swal.fire({
            icon: "error",
            title: "No facility selected!",
            text: "Select facility first!"
        });
    }

    function getFundsource(facility_id) {
        $('.loading-container').modal('show');
        $('.loading-container').html(loading);
        $.get("{{ url('fetch/pre-dv/fundsource').'/' }}"+facility_id, function(result) {
            var data_result = result.info;
            all_options = result;
            $('.loading-container').modal('hide');
        });

        var check_vat = $('.facility_id').find(':selected').attr('datavat');

        if (check_vat == 0) {
            Swal.fire({
                icon: "error",
                title: "No VAT and EWT added",
                text: "Please add vat and ewt first!"
            });
            $('.facility_id').val('').trigger('change');
            return;
        }

        f_id = facility_id;

        $.get("{{url('pre-dv/control_nos').'/'}}" + f_id, function (result) {
            existing_control = result.controls;
            transmittal = result.transmittal;

            var html = "<option value=''>Select Control No</option>";
            transmittal.forEach(optionData => {
                html += `<option value="${optionData.id}">${optionData.control_no}</option>`;
            });

            $('.control_id').html(html);
        });
    }

    function addOption(data){
        data.forEach(function(item) {
            var option = $('<option>', {
                value: item.value,
                text: item.text,
                dataval: item.dataval,
                dataproponentInfo_id: item.dataproponentInfo_id,
                dataprogroup: item.dataprogroup,
                dataproponent: item.dataproponent,
                'data-color': item.d_color
            });

            $('.saa_id').append(option.clone());
        });
    }

    function deletePre(){
        var id = $('#pre_id').val();

        Lobibox.alert('error',
            {
                size: 'mini',
                msg: '<div style="text-align:center;"><i class="typcn typcn-delete menu-icon" style="color:red; font-size:30px"></i>Are you sure you want to remove this?</div>',
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
                        window.location.href="delete/" + id;
                    }
                }
            }
        );
    }

    var edit_stat = 0;

    function updatePre(id, data, stat){

        $('.form_body').html(loading);
        $.get("{{ url('fetch/pre-dv/fundsource').'/' }}"+1, function(result) {
            var data_result = result.info;
            all_options = result;
            $('.loading-container').modal('hide');
        });

        $.get("{{ url('pre-dv/update/').'/' }}"+id, function(result) {
            $('.form_body').html(result);

            if (!all_options || !all_options.info) {
                $('.loading-container').modal('show');
                $('.loading-container').html(loading);
            }else{
                $('.loading-container').modal('hide');
            }
            
            if(data == 1){
                $('.delete_btn').css('display', 'none');
                $('.submit_btn').css('display', 'none');
                btn_val = 1;
            }else{
                $('.delete_btn').css('display', 'block');
                $('.submit_btn').css('display', 'block');
                btn_val = 0;
            }

            if(stat == 1){
                $('.submit_btn').css('display', 'block');
                edit_stat = 1;
            }
            
            f_id = $('#facility_id').val();
            $.get("{{url('pre-dv/control_nos').'/'}}" + f_id, function (result){
                existing_control = result.controls; 
            }); 
        });
    }

    function validateAmount(element) {
        if (event.keyCode === 32) {
            event.preventDefault(); 
        }

        var cleanedValue = element.value.replace(/[^\d.]/g, ''); 
        var numericValue = parseFloat(cleanedValue);

        if (!isNaN(numericValue) || cleanedValue === '' || cleanedValue === '.') {
            var parts = cleanedValue.split('.');
            if (parts.length > 1) {
                parts[1] = parts[1].substring(0, 2); 
            }

            cleanedValue = parts.join('.'); 

            element.value = cleanedValue.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        } else {
            element.value = ''; 
        }
    }

    function getTransId(){
        var transmittal_ids = [];
        $('.trans_id').each(function(){
            transmittal_ids.push($(this).val());
        });

        return transmittal_ids;
    }

    function getGrand(){
        var grand_total = 0;
        $('.total_amount').each(function(){
            grand_total += parseFloat(($(this).val()).replace(/,/g, '')) || 0;
        });
        $('.grand_total').val(Number(grand_total.toFixed(2)).toLocaleString('en-US', { maximumFractionDigits: 2 }));
    }

    function getGrandFee(){
        var grand_total = 0;
        $('.prof_fee').each(function(){
            grand_total += parseFloat(($(this).val()).replace(/,/g, '')) || 0;
        });
        $('.grand_fee').val(Number(grand_total.toFixed(2)).toLocaleString('en-US', { maximumFractionDigits: 2 }));
    }

    function cloneProponent(element){
        if(f_id){
            $.get("{{ url('pre-dv/proponent-clone') .'/' }}" + f_id, function (result) {
                $('.facility_div').append(result);
                var lastSaaSelect = $('.proponent_clone').last().find('.saa_id');
                var proponent_input = $('.proponent_clone').last().find('.proponent');
                lastSaaSelect.select2();
                proponent_input.select2();
            });
        }else{
            error();
        }
        
    }

    function calculateAmount(data){
        var total = 0;
        data.find('.amount').each(function(){
            var amount = parseFloat(($(this).val()).replace(/,/g, '')) || 0;
            total += amount;
        });
        data.find('.total_amount').val(Number(total.toFixed(2)).toLocaleString('en-US', { maximumFractionDigits: 2 }));
        getGrand();
    }

    function evaluateSAA(data){
        var total_saa = 0;
        var total_pro = parseFloat(data.find('.total_amount').val().replace(/,/g, ''));
        data.find('.saa_amount').each(function(){

            var amount = parseFloat($(this).val().replace(/,/g, '')) || 0;
            total_saa = parseFloat(total_saa) || 0; 
            total_saa = total_saa.toFixed(2);
            total_saa = Number(total_saa) + Number(amount);
            total_saa = total_saa.toFixed(2);

            if(total_saa > total_pro){
                alert('Mismatch total amount!');
                $(this).val('');
            }
        });
        
        getGrand();
    }

    var existing_control;
    var transmittal;
    var new_control = [];
    var hasErrors = false; 

    function controls(){
        var cons = [];
        $('.control_clone').each(function (index, clone) {
            var control_no = $(clone).find('.control_no').val();
            cons.push(control_no);
        });
        return cons;
    }

    function checkControlNo(data){
        var control_clone = $(data).closest('.control_clone');
        var control_no = $(control_clone).find('.control_no').val();  
        var cons = controls();
        var index = cons.findIndex(item => item === control_no);
        if (index > -1) {
            cons.splice(index, 1); 
        }            
        var exist = existing_control.find(item => item === control_no);

        if (cons.includes(control_no) || exist) {
            alert('Control no ' +control_no+ ' existed already!')
            // $(control_clone).find('.control_no').val('');
            return false;
        }
    }

    $(document).on('input', '.prof_fee', function(){
        getGrandFee();
    });

    $(document).on('input', '.amount', function(){
        var p_clone = $(this).closest('.proponent_clone');
        calculateAmount(p_clone);
    });

    $(document).on('input', '.saa_amount', function(){
        var p_clone = $(this).closest('.proponent_clone');
        inputted_fundsource(p_clone);
    });

    function inputted_fundsource(data){
        var total = 0;
        data.find('.saa_amount').each(function(){
            var amount = parseFloat(($(this).val()).replace(/,/g, '')) || 0;
            total += amount;
        });
        data.find('.inputted_amount').text(Number(total.toFixed(2)).toLocaleString('en-US', { maximumFractionDigits: 2 }));
    }

    function autoDeduct(element){
        if(btn_val == 0){
            var amountValue = parseFloat(element.closest('.saa_clone').find('.saa_amount').val().replace(/,/g, '')) || 0;
            var w_pro = element.closest('.proponent_clone');
            var m_amount = 0;
            w_pro.find('.saa_amount').each(function(){
                var amount = $(this).val().replace(/,/g, ''); 
                m_amount += parseFloat(amount) || 0;
            });
            var w_saa = element.closest('.saa_clone');
            var w_amount = w_saa.find('.saa_amount');
            var w_total = element.closest('.card').find('.total_amount');
            var amount_overall = parseFloat((w_total.val() || "0").replace(/,/g, ''));

            var dataval = element
                .closest('.saa_clone')
                .find('.saa_id')
                .find(':selected')
                .attr('dataval')|| "0";
            var saa_rem = parseFloat(dataval.replace(/,/g, ''));
            var total_result = amount_overall - (m_amount - amountValue);

            if(saa_rem >= total_result){
                w_amount.val(total_result.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2}));
            }else{
                w_amount.val(saa_rem.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2}));

                Lobibox.alert('error',{
                    size : 'mini',
                    msg : 'Insufficient balance, would you like to use another saa?',
                    buttons : {
                        yes: {
                            'class': 'btn-xs btn-success',
                            text: 'ADD',
                            closeOnClick:true
                        },
                        no: {
                            'class': 'btn-xs btn-warning',
                            text: 'NO',
                            closeOnClick:true
                        }
                    },
                    callback: function (lobibox, type){
                        if(type == 'yes'){
                            $.get("{{ url('pre-dv/saa-clone').'/' }}" + f_id, function (result) {

                                var clonedElement = w_saa.last().after(result).next();

                                clonedElement.find('.saa_clone_btn')
                                    .removeClass('saa_clone_btn btn-info typcn typcn-plus menu-icon')
                                    .addClass('saa_remove_btn btn-danger')
                                    .css('background-color', 'red')
                                    .text('')
                                    .html('<span class="typcn typcn-minus menu-icon"></span>');

                                    var saaSelect = clonedElement.find('.saa_id');
                                    saaSelect.select2();
                                    var proponent_input = saaSelect
                                    .closest('.proponent_clone')
                                    .find('.proponent');
                                    updateSAAOption(saaSelect, proponent_input);
                            }); 
                        }
                    }
                }); 
            }
            inputted_fundsource(w_pro);  
        }
    }

    $(document).on('input', '.saa_amount', function(){
        var data = $(this);
        var clone_pro = data.closest('.proponent_clone');
        var clone_saa =  data.closest('.saa_clone');
        var input_value =  parseFloat(data.val().replace(/,/g, ''));

        var saa_value = clone_saa.find('.saa_id').find(':selected').attr('dataval');
        var saa_balance = parseFloat(saa_value.replace(/,/g, ''));
    
        if(saa_balance != '' || saa_balance != undefined){

            if(input_value > saa_balance){
                Lobibox.alert('error',{
                    size : 'mini',
                    msg : 'Insufficient balance, would you like to use another saa?',
                    buttons : {
                        yes: {
                            'class': 'btn-xs btn-success',
                            text: 'ADD',
                            closeOnClick:true
                        },
                        no: {
                            'class': 'btn-xs btn-warning',
                            text: 'NO',
                            closeOnClick:true
                        }
                    },
                    callback: function (lobibox, type){
                        if(type == 'yes'){
                            $.get("{{ url('pre-dv/saa-clone').'/' }}" + f_id, function (result) {

                                var clonedElement = clone_saa.last().after(result).next();

                                clonedElement.find('.saa_clone_btn')
                                    .removeClass('saa_clone_btn btn-info typcn typcn-plus menu-icon')
                                    .addClass('saa_remove_btn btn-danger')
                                    .css('background-color', 'red')
                                    .text('')
                                    .html('<span class="typcn typcn-minus menu-icon"></span>');

                                var saaSelect = clonedElement.find('.saa_id');
                                saaSelect.select2();
                                var proponent_input = saaSelect
                                    .closest('.proponent_clone')
                                    .find('.proponent');
                                updateSAAOption(saaSelect, proponent_input);
                            }); 
                            data.val(saa_balance.toFixed(2));
                        }else{
                            data.val('');
                            inputted_fundsource(clone_pro)
                        }
                    }
                });     
            }
        }
        evaluateSAA(clone_pro);
        inputted_fundsource(clone_pro)
    });

    $(document).on('click', '.proponent_clone .btn_pro_remove', function () {
   
        $(this).closest('.proponent_clone').remove();
        
        getGrand();
        getGrandFee();
        getTransId();
    });

    $(document).on('click', '.proponent_clone .saa_clone .saa_clone_btn', function () {
        if(f_id){
            var button = $(this);

            $.get("{{ url('pre-dv/saa-clone').'/' }}" + f_id, function (result) {
                var clonedElement = button.closest('.proponent_clone').find('.saa_clone').last().after(result).next();

                clonedElement.find('.saa_clone_btn')
                    .removeClass('saa_clone_btn btn-info typcn typcn-plus menu-icon')
                    .addClass('saa_remove_btn btn-danger')
                    .css('background-color', 'red')
                    .text('')
                    .html('<span class="typcn typcn-minus menu-icon"></span>');

                    var saaSelect = clonedElement.find('.saa_id');
                    saaSelect.select2();
                    var proponent_input = saaSelect
                    .closest('.proponent_clone')
                    .find('.proponent');
                    updateSAAOption(saaSelect, proponent_input);

            });
        }else{
            error();
        }
    });

    $(document).on('click', '.proponent_clone .saa_clone .saa_remove_btn', function () {
        var element = $(this);
        var p_clone = element.closest('.proponent_clone');
        element.closest('.saa_clone').remove();  
        inputted_fundsource(p_clone); 
    });

    $(document).on('click', '.proponent_clone .control_div .control_clone_btn', function () {

        if(f_id){
            var button = $(this);
            $.get("{{ route('clone.control') }}", function (result) {
                var clonedElement = $(result).appendTo(button.closest('.control_div')).last();

                clonedElement.find('.control_clone_btn')                
                    .removeClass('control_clone_btn btn-info typcn typcn-plus menu-icon')
                    .addClass('control_remove_btn btn-danger')
                    .css('background-color','red')
                    .text('')                    
                    .html('<span class="typcn typcn-minus menu-icon"></span>');
            });
        }else{
            error();
        }
    });

    $(document).on('click', '.control_remove_btn', function () {
        var p_clone = $(this).closest('.proponent_clone');
        $(this).closest('.control_clone').remove();  
        calculateAmount(p_clone);
    });

    $('.pre_form1, #pre_form').submit( function(e){
        btn_val = 1;
        e.preventDefault();
        var trans_ids = getTransId();
        var facility_id = f_id;
        var grand_total = $('.grand_total').val();
        var all_data = [];

        $('.facility_div .proponent_clone').each(function (index, proponent_clone) {
            var proponent = $(proponent_clone).find('.proponent').val();
            hasErrors = false; 

            if(proponent != ''){
                var total_amount = $(proponent_clone).find('.total_amount').val();
                var pro_clone = [];
                var control_total = 0;
                $(proponent_clone).find('.control_clone').each(function (index, control_clone){
                    var control_no = $(control_clone).find('.control_no').val();
                    var patient_1 = $(control_clone).find('.patient_1').val();
                    var patient_2 = $(control_clone).find('.patient_2').val();
                    var amount = $(control_clone).find('.amount').val();
                    var prof_fee = $(control_clone).find('.prof_fee').val() || 0;
                    var saa_number = $(control_clone).find('.saa_number').val();
                    var exist = existing_control.find(item => item.includes(control_no));

                    var data = {
                        control_no : control_no,
                        patient_1 : patient_1,
                        patient_2 : patient_2,
                        amount : amount,
                        prof_fee : prof_fee,
                    };
                    pro_clone.push(data);
                });

                var fundsource_clone = [];
                var saa_total = 0;

                $(proponent_clone).find('.saa_clone').each(function (index, saa_clone){
                    var info_id = $(saa_clone).find('.saa_id');
                    info_id = info_id.find(':selected').attr('dataproponentInfo_id');
                    var saa_id = $(saa_clone).find('.saa_id').val();
                    var saa_amount = $(saa_clone).find('.saa_amount').val();
                    saa_total += parseFloat(saa_amount.replace(/,/g, ''));
                    
                    var data1 = {
                        saa_id : saa_id,
                        saa_amount : saa_amount,
                        info_id : info_id
                    };
                    fundsource_clone.push(data1);
                });

                saa_total = saa_total.toFixed(2);
            
                if(saa_total != parseFloat(total_amount.replace(/,/g, ''))){
                    Swal.fire({
                        icon: "error",
                        title: "Mismatch Amount!",
                        text: "Kindly check added amount!"
                    });
                    
                    $(proponent_clone).find('.saa_clone').find('.saa_amount').val('');
                    btn_val = 1;
                    $(proponent_clone).find('.saa_clone').find('.saa_id').val('').trigger('change');
                    btn_val = 0;
                    hasErrors = true; // Set error flag
                    return false;
                }

                var data2 = {
                    proponent : proponent,
                    pro_clone : pro_clone,
                    fundsource_clone : fundsource_clone,
                    total_amount : total_amount
                };
                all_data.push(data2);
            }
        });  

        if (hasErrors) return;
        var jsonData = JSON.stringify(all_data);
        var encodedData = encodeURIComponent(jsonData);

        if(edit_stat == 0){
            if($('#pre_id').val() == undefined){
                $.ajax({
                    type: 'POST',
                    url: '{{ route("pre_dv.save") }}',
                    data: {
                        _token: '{{ csrf_token() }}',
                        data: encodedData,
                        transmittal_id: trans_ids,
                        facility_id: $('.facility_id').val(),
                        grand_total: $('.grand_total').val(),
                        grand_fee: $('.grand_fee').val() || 0,
                        all_transmittal : all_trans
                    },
                    success: function (response) {
                        Lobibox.notify('success', {
                            msg: "Successfully created pre_dv!",
                        });
                        location.reload();
                    }
                });
            }else{
                $.ajax({
                    type: 'POST',
                    url: '{{ route("pre_update.save") }}',
                    data: {
                        _token: '{{ csrf_token() }}',
                        data: encodedData,
                        facility_id: $('#facility_id').val(),
                        grand_total: $('#grand_total').val(),
                        grand_fee: $('#grand_fee').val() || 0,
                        pre_id: $('#pre_id').val()
                    },
                    success: function (response) {
                        Lobibox.notify('success', {
                            msg: "Successfully created pre_dv!",
                        });
                        location.reload();
                    },
                    error: function (error) {
                        if (error.status) {
                            console.error('Status Code:', error.status);
                        }

                        if (error.responseJSON) {
                            console.error('Response JSON:', error.responseJSON);
                        }

                    }
                });
            }
        }else if( edit_stat == 1){

            $.ajax({
                type: 'POST',
                url: '{{ route("pre_modify.save") }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    data: encodedData,
                    facility_id: $('#facility_id').val(),
                    grand_total: $('#grand_total').val(),
                    grand_fee: $('#grand_fee').val() || 0,
                    pre_id: $('#pre_id').val()
                },
                success: function (response) {
                    Lobibox.notify('success', {
                        msg: "Successfully updated this pre_dv!",
                    });
                    location.reload();
                },
                error: function (error) {
                    if (error.status) {
                        console.error('Status Code:', error.status);
                    }

                    if (error.responseJSON) {
                        console.error('Response JSON:', error.responseJSON);
                    }

                }
            });
        }

        $('#update_predv').modal('hide');
        $('#create_predv').modal('hide');
        $('.loading-container').modal('show');
        $('.loading-container').html(loading);
    });
</script>
@endsection