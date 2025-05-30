@extends('layouts.app')

@section('content')
<?php 
    use App\Models\Proponent; 
    use App\Models\ProponentInfo; 
    use App\Models\Facility; 
?>
<style>
    .btn {
        border-radius:0;
    }
    .clone:last-child .clone_facility-btn {
        z-index: 1000; 
    }
    
    .loading-container {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1056;
        display: none; 
    }

    .loading-spinner {
        width: 100%; 
        height: 100%; 
    }
</style>
<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div class="mb-2 mb-md-0">
                    <h4 class="card-title">MANAGE FUNDSOURCE: DV</h4>
                    <p class="card-description">MAIF-IPP</p>
                </div>

                <form method="GET" action="{{ route('fundsource') }}" class="w-50">
                    <div class="input-group">
                        <input type="text" class="form-control" name="keyword" placeholder="SAA, PROPONENT, FACILITY" style="height:40px" value="{{ $keyword }}">
                        <div class="input-group-append">
                            <button class="btn btn-sm btn-info" type="submit">
                                <img src="/maif/public/images/icons8_search_16.png">Search
                            </button> 
                            <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll">
                                <img src="/maif/public/images/icons8_eye_16.png">View All
                            </button>
                            @if($user->section != 6)
                                <button type="button" id="create_btn" href="#create_fundsource2" data-backdrop="static" data-toggle="modal" class="btn btn-success btn-md">
                                    <img src="/maif/public/images/icons8_create_16.png">Create
                                </button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
            @if(isset($fundsources) && $fundsources->count() >0)
                <div class="row">
                    @foreach($fundsources as $fund)
                        <div class="col-md-4 mt-2 grid-margin grid-margin-md-0 stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
                                        <h4 class="card-title" style="text-align: left; margin: 0; color: {{ $fund->remaining_balance <= 0 ? 'red' : 'inherit' }};">
                                            {{ $fund->saa }} 
                                            @if($fund->budget_cost != null | $fund->budget_cost != 0)
                                                <br><small class="text-info">Fundsource Budget(admin cost) : {{ number_format($fund->budget_cost,2,'.',',') }}</small>
                                            @endif
                                        </h4>
                                        @if($user->section != 6)
                                            <button class="btn btn-sm update_saa" style="height: 35px; min-width: 110px; cursor: pointer; text-align: center; color: white; background-color: #417524; border-radius: 0; flex-shrink: 0;" data-proponent-id="" data-backdrop="static" data-toggle="modal" onclick="createBreakdowns({{ $fund->id }})" href="#create_fundsource">Breakdowns</button>                                      
                                        @endif
                                    </div>

                                    @foreach($fund->proponents as $proponent)
                                        @if(count($proponent->proponentInfo)>0)
                                            <br>
                                            @if(isset($proponent->proponentInfo->first()->main_pro))
                                                <b><p class="text-success">{{ $proponent->proponentInfo->first()->main_pro->proponent }} (main)</p></b>
                                            @endif
                                            <b><p class="">{{ $proponent->proponent }} (c/o)</p></b>
                                            <ul class="list-arrow mt-3">
                                                @foreach($proponent->proponentInfo as $proponentInfo)
                                                    @if( $proponentInfo->facility !== null)
                                                        <li><b>{{ $proponentInfo->facility->name }}</b></li>
                                                    @else
                                                        <?php 
                                                            $facilityIds = json_decode($proponentInfo->facility_id);
                                                        ?>
                                                        <li>
                                                            @foreach($facilityIds as $facilityId)
                                                                @php
                                                                    $facility = $facilities->where('id', $facilityId)->first();
                                                                @endphp
                                                                @if($facility)
                                                                    <b>{{ $facility->name }}</b><br>
                                                                @endif
                                                            @endforeach
                                                        </li>
                                                    @endif
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="ml-3">Allocated Funds &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <strong class="text-info">{{ number_format(floatval(str_replace(',', '', $proponentInfo->alocated_funds)), 2, '.', ',') }}</strong></span>
                                                        <button style="min-width:90px; border-radius:0;" id="track" data-backdrop="static" data-proponentInfo-id="{{ $proponentInfo->id }}" data-toggle="modal" href="#track_details2" onclick="track_details2(event)" class='btn btn-sm btn-outline-info track_details2'>Track</button>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="ml-3">Administrative Cost : <strong class="text-info">{{ number_format(floatval(str_replace(',', '', $proponentInfo->admin_cost)), 2, '.', ',') }}</strong></span>
                                                        @if($user->section != 6)
                                                            <button style="min-width:90px; border-radius:0; margin-top:1px" id="transfer_funds" data-backdrop="static" data-toggle="modal" href="#transfer_fundsource" onclick="transferFunds({{ $proponentInfo->id }})" style="width:100px" class='btn btn-sm btn-outline-success ml-2 transfer_funds'>Transfer</button>
                                                        @endif
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        @if( $proponentInfo->remaining_balance == 0)
                                                            <span class="ml-3">Remaining Balance &nbsp;: <strong class="text-danger">{{ number_format(floatval(str_replace(',', '', $proponentInfo->remaining_balance)), 2, '.', ',') }}</strong></span>
                                                        @else
                                                            <span class="ml-3">Remaining Balance &nbsp;: <strong class="text-info">{{ number_format(floatval(str_replace(',', '', $proponentInfo->remaining_balance)), 2, '.', ',') }}</strong></span>
                                                        @endif
                                                    </div>
                                                    <div class="d-flex justify-content-end mt-2"></div>
                                                @endforeach
                                            </ul>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-danger" role="alert" style="width: 100%;">
                    <i class="typcn typcn-times menu-icon"></i>
                    <strong>No fundsource found!</strong>
                </div>
            @endif
            <div class="pl-5 pr-5 mt-5">
                {!! $fundsources->appends(request()->query())->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="create_fundsource" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Create Fund Source</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal_body">
                
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="track_details2" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius:0px;">
            <div class="modal-header" style="text-align:center">
                <h4 class="text-success modal-title">
                    <i style="font-size:15px" class="typcn typcn-location-arrow menu-icon"></i>
                    TRANSMITTAL TRACKING
                </h4>
            </div>
            <div class="table-container" style="height: 800px; overflow-y: auto;">
                <table class="table table-list table-hover table-striped" id="track_details2">
                    <thead style="position: sticky; top: 0; background-color: white; z-index: 1;">
                        <tr style="text-align:center;">
                            <th>FundSource</th>
                            <th>Proponent</th>
                            <th>Facility</th>
                            <th>Balance</th>
                            <th>Tax</th>
                            <th>Amount</th>
                            <th>Route</th>
                            <th>By</th>
                            <th>On</th>
                            <th>Obligated</th>
                            <th>Paid</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody id="track_body">
                        <!-- Data rows go here -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="breakdowns_close" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="version2" tabindex="-2" role="dialog">
    <div class="modal-dialog modal-lg " role="document" style="max-width:600px">
        <div class="modal-content">
            <div class="modal-header" >
                <h4 class="modal-title text-success" id="exampleModalLabel" >Disbursement Version - 2 Details</h4>
                <span type="button" data-dismiss="modal">
                    <i class="typcn typcn-times menu-icon" style="font-size:17px"></i>
                </span>
            </div>
            <div class="v2_body" >
            </div>
        </div>
    </div>
</div>  
<div class="modal fade" id="i_frame" tabindex="-2" role="dialog" aria-hidden="true" >
    <div class="modal-dialog modal-lg " role="document" style="max-width:1000px">
        <div class="modal-content">
            <div class="modal-header" >
                <h4 class="modal-title" id="exampleModalLabel" >Disbursement Tracking Details</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe id="track_iframe" width="100%" height="400" frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="transfer_fundsource" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="text-success"><i style = "font-size:30px"class="typcn typcn-location-arrow menu-icon"></i>Transfer Fund Source</h4><hr />
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> 
                <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal_body">
                
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="create_fundsource2" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Fundsource</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body-body">
                <form id="fundsource_form" method="POST" action="{{ route('fundsource_budget.save') }}">
                    <div class="body_body">
                        <div class="for_clone" style="padding:10px;">
                            @csrf
                            <div class="rows">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label >Fundsource:</label>
                                            <input type="text" onblur="checkFundsource($(this),this.value)" class="form-control saa_name" id="saa" name="saa[]" placeholder="SAA" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group" style="display: flex; flex-direction: column;">
                                            <label for="allocated_funds">Allocated Fund</label>
                                            <div style="display: flex; align-items: center;">
                                                <input type="text" class="form-control" id="allocated_funds" name="allocated_funds[]" onkeyup="validateAmount(this)" placeholder="Allocated Fund" required>
                                                <button type="button" class="form-control btn-info add_saa" style="width: 100px; margin-left: 5px; color:white; background-color:#013F3E">+ SAA</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Admin Cost:</label>
                                            <input type="number" class="form-control" id="admin_cost" name="admin_cost[]" placeholder="Administrative Cost" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <button type="button" class="btn btn-info add_breakdowns" style="width: 100%;">ADD BREAKDOWNS</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="breakdown_total_div" style="display:none">
                                    <hr>
                                    <span>Total Breakdowns:</span>
                                    <span class="breakdown_total"></span>
                                </div>
                                <br>
                                <div class="break_clone"></div>
                                <hr style="border:2px solid gray">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="breakdowns_close" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="loading-container">
    <img src="\maif\public\images\loading.gif" alt="Loading..." class="loading-spinner">
</div>
@endsection
@section('js')
<script>
    function checkFundsource(element, value){
        var funds_list = @json($funds_list);
        var match = funds_list.some(function(item) {
            return item.saa === value;
        });
        if (match) {
            alert('Sorry! This Fundsource exists already.');
            $(element).val('');
        }
        new_saa(element, value);
    }

    function new_saa(element, value){
        value = value.trim();
        var count = 0;

        $('.saa_name').each(function () {
            const currentVal = $(this).val().trim();
            if (currentVal === value) {
                count++;
            }
        });

        if (count > 1) {
            Swal.fire({
                title: 'Error!',
                text: 'Sorry! This saa is existing already.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            $(element).val('');
        }
    }

    function checkCode(element, value) {
        var proponents_list = @json($proponents);
        var hasMatchingProponent = proponents_list.some(function(item) {
            return item.proponent_code === value;
        });
        if (hasMatchingProponent) {
            alert('Sorry! This code has been used by another proponent already.');
            var proponentCodeInput = element.closest('.row').find('.proponent_code');
            proponentCodeInput.val('');
        }
        new_code(element, value);
    }

    function new_code(element, value){
        value = value.trim();
        var count = 0;

        $('.proponent_code').each(function () {
            const currentVal = $(this).val().trim();
            if (currentVal === value) {
                count++;
            }
        });

        if (count > 1) {
            Swal.fire({
                title: 'Error!',
                text: 'Sorry! This code has been used by another proponent already.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            $(element).val('');
        }
    }

    function getFundsourceData(){
        var formData = [];
        $('.for_clone .rows').each(function (index, clone_rows) {
            var saa = $(clone_rows).find('#saa').val();
            var allocated_fund = $(clone_rows).find('#allocated_funds').val() || 0;
            var admin_cost = $(clone_rows).find('#admin_cost').val() || 0;
            var breakdown = $(clone_rows).find('.break_clone');
         
            var break_data = [];
            $(breakdown).find('.clone .card').each(function (rowIndex, clone) {
                var proponent = $(clone).find('.proponent').val();
                var proponent_code = $(clone).find('.proponent_code').val();
                var proponent_main = $(clone).find('.proponent_main').val();
                $(clone).find('.row').each(function (rowIndex, row) {
                    var facility_id = $(row).find('.break_fac').val();
                    
                    if(facility_id !== '' && facility_id !== undefined){

                        var allocated_funds = $(row).find('.alocated_funds').val(); 
                        var info_id = $(row).find('.info_id').val(); 
                        if(info_id == undefined){
                            info_id = 0;
                        }
                        var cloneData = {
                            proponent: proponent,
                            proponent_main: proponent_main,
                            proponent_code: proponent_code,
                            facility_id: facility_id,
                            alocated_funds: allocated_funds,
                            remaining_balance: allocated_funds,
                            info_id: info_id
                        };
                        break_data.push(cloneData);
                    }
                });
            });
            var funds_data = {
                saa: saa,
                allocated_fund: allocated_fund,
                admin_cost: admin_cost,
                break_data: break_data
            };
            formData.push(funds_data);
        });
        return formData;
    }

    $(document).off('click', '.for_clone .rows .break_clone .clone .card1 .clone_facility-btn').on('click', '.clone .card1 .clone_facility-btn', function () {
        $('.loading-container').show();
        var $this = $(this);
        setTimeout(function () {
            $.get("{{ route('facilities.get', ['type'=>'fac']) }}", function (result) {
                $this.closest('.card').find('.card1:last').append(result);
                $('.loading-container').hide();
            });
        }, 1);
    });

    $(document).on('click', '.for_clone .rows .break_clone .clone .remove_pro-btn', function () {
        var rows = $(this).closest('.for_clone .rows');
        $(this).closest('.clone').remove();
        $(this).closest('.clone hr').remove();
        getFundsourceData();
        display(rows);
    });

    $(document).on('click', '.for_clone .rows .break_clone .clone .card .card1 .remove_fac-clone', function () {
        var rows = $(this).closest('.for_clone .rows');
        $(this).closest('.row').remove();
        getFundsourceData();
        display(rows);        
    });

    function calculateFunds(inputElement) {
        var clone = $(inputElement).closest('.break_clone');
        var rows = $(inputElement).closest('.rows');
        var main_saa = $(rows).find('#allocated_funds').val() || 0;
        main_saa = parseFloat(main_saa.replace(/[, ]/g, '')) || 0;

        console.log('main_saa', main_saa);

        var total = 0;

        clone.find('.alocated_funds').each(function () {
            var rawVal = $(this).val().replace(/,/g, ''); 
            var val = parseFloat(rawVal) || 0;
            total += val;

            if(total > main_saa){
                Swal.fire({
                    title: 'Error!',
                    text: 'Breakdown totals exceed the allocated SAA funds. Please update.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                $(this).val('');
                return false;
            }
        });
        display(rows);
    }

    function display(clone){
        var total = 0;
        clone.find('.alocated_funds').each(function () {
            var rawVal = $(this).val().replace(/,/g, ''); 
            var val = parseFloat(rawVal) || 0;
            total += val;
        });

        clone.closest('.rows').find('.breakdown_total').text(total.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }));
    }

    $('#fundsource_form').submit(function(e) {
        $('.loading-container').show();
        e.preventDefault();
        console.log('dataaa',getFundsourceData());
        $.ajax({
            type: 'POST',
            url: '{{ route("fundsource_budget.save") }}',
            data: {
                _token: '{{ csrf_token() }}',
                breakdowns: getFundsourceData(),

            },
            success: function (response) {
                Lobibox.notify('success', {
                    msg: "Successfully created breakdowns!",
                });
                window.location.href = '{{ route("fundsource") }}';
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

    });

    function proponentCode(selectElement) {
        var selectedValue = selectElement.val();
        var proponentCode = selectElement.find(':selected').data('proponent-code');
        if (proponentCode != "" || proponentCode != undefined ) {
            var proponentCodeInput = selectElement.closest('.row').find('.proponent_code');
            proponentCodeInput.val(proponentCode);
            proponentCodeInput.prop('disabled', true);
        }
        proponentCodeInput.prop('disabled', false);
    }

    $('.for_clone').on('click', '.add_breakdowns', function () {
        $('.loading-container').show();
        var modal = $(this).closest('.rows').find('.break_clone');
        var breakdowns = $(this).closest('.rows').find('.breakdown_total_div');
        setTimeout(function () {
            $.get("{{ route('facilities.get', ['type'=>'div']) }}", function (result) {
                $(modal).append(result);
                $('.loading-container').css('display', 'none');
                $(breakdowns).show();
            });
        }, 1);  
    });
        
    var close = 0;
    $('#create_btn').on('click', function(){});

        $(document).ready(function () {

        $('#create_fundsource2').on('hidden.bs.modal', function () {
            $(this).find('form')[0].reset(); 
            $('.for_clone .rows:not(:first)').remove();
        });

        $(".for_clone").on("click", ".add_saa", function () {
            var clonedDiv = $(".for_clone .rows:first").clone(true); 

            clonedDiv.find('#saa').val('');
            clonedDiv.find('#allocated_funds').val('');
            clonedDiv.find('#admin_cost').val('');
            clonedDiv.find(".add_saa").text("-");
            clonedDiv.find(".add_saa").removeClass("add_saa").addClass("remove_saa");
            clonedDiv.find(".break_clone").empty();
            clonedDiv.find(".breakdown_total_div").hide();
            $(".for_clone .rows:last").after(clonedDiv);
        });

        $(".for_clone").on("click", ".remove_saa", function () {
            $(this).closest(".rows").remove();
        });
    
    });

    function splitIntoLines(text, length) {
        var lines = [];
        for (var i = 0; i < text.length; i += length) {
            lines.push(text.substring(i, i + length));
        }
        return lines.join('<br>');
    }

    function track_details2(event){
        event.stopPropagation();
        $('#track_details2').modal('show');
        var info_id = event.target.getAttribute('data-proponentInfo-id');
        var i = 0;
        
        var url = "{{ url('tracking').'/' }}"+ info_id;
        $.ajax({
            url: url,
            type: 'GET',
            
            success: function(result) {
                $('#track_body').empty(); 
                if(result.length > 0){
                    result.forEach(function(item) {
                        var saa = item.fund_sourcedata && item.fund_sourcedata.saa !== null ? item.fund_sourcedata.saa : '-';
                        var proponentName = item.proponentdata && item.proponentdata.proponent !== null ? item.proponentdata.proponent : '-';
                        var facility_name = item.facilitydata && item.facilitydata.name !== null ? item.facilitydata.name : '-';
                        var user = item.user && item.user !== null ? item.user.lname + ', ' + item.user.fname : '-';
                        var timestamp = item.created_at;
                        var date = new Date(timestamp);
                        var formattedDate = date.toLocaleString('en-US', {
                            month: 'long',
                            day: 'numeric',
                            year: 'numeric'
                        });
                        var formattedTime = date.toLocaleTimeString('en-US', {
                            hour: 'numeric',
                            minute: 'numeric'
                        });
                        var stat='';
                        if(item.status == 0){
                            stat = 'DV';
                        }else if(item.status == 2 || item.status == 3){
                            var from_proponent = item.transfer && item.transfer.from_proponent_info && item.transfer.from_proponent_info.proponent !== null 
                                ? item.transfer.from_proponent_info.proponent : '';
                            var to_proponent = item.transfer && item.transfer.to_proponent_info && item.transfer.to_proponent_info.proponent !== null 
                                ? item.transfer.to_proponent_info.proponent : '';
                            var from_saa = item.transfer && item.transfer.from_fundsource && item.transfer.from_fundsource.saa !== null 
                                ? item.transfer.from_fundsource.saa : '';   
                            var to_saa = item.transfer && item.transfer.to_fundsource && item.transfer.to_fundsource.saa !== null 
                                ? item.transfer.to_fundsource.saa : '';   
                            if(item.transfer_type == null && item.transfer_to == null){
                                stat = "Transferred funds amounting to " + item.utilize_amount + " from " + from_proponent +" " + from_saa + " to " + to_proponent +" " + to_saa + " amounting to " + item.utilize_amount;
                            }else{
                                var item1 = item.transfer_type == 1? 'Allocated Funds' : (item.transfer_type == 2 ? 'Remaining Balance' : (item.transfer_type == 3 ? 'Administrative Cost' : ''));
                                var item2 = item.transfer_to == 1? 'Allocated Funds' : (item.transfer_to == 2 ? 'Remaining Balance' : (item.transfer_to == 3 ? 'Administrative Cost' : ''));
                                stat = "Transferred " + item1 + " amounting to " + item.utilize_amount + " from " + from_proponent +" " + from_saa + " to " + item2 + " amounting to " + item.utilize_amount + " to " +  to_proponent +" " + to_saa;
                            }
                        }
                        var item_remarks = item.transfer && item.transfer.remarks !== null ? item.transfer.remarks : ''; 
                        // if(item.transfer)
                        // else if(item.status == 2){
                        //     if(item)
                        //     stat = 'Transfered/Deducted: ' + (item.transfer && item.transfer.remarks !== null ? item.transfer.remarks : '');
                        // }else if(item.status == 3){
                        //     stat = 'Transfered/Added: ' + (item.transfer && item.transfer.remarks !== null ? item.transfer.remarks : '');
                        // }

                        // stat = splitIntoLines(stat, 35); 

                        // else if(item.status == 1){
                        //     stat = 'Modified';
                        // }
                        var beg_balance = item.beginning_balance.replace(',', '');
                        var discount = (item.discount !== null)?number_format(parseFloat(item.discount.replace(/,/g, '')), 2, '.', ','):'';
                        var utilize = (item.utilize_amount !== null)?number_format(parseFloat(item.utilize_amount.replace(/,/g, '')), 2, '.', ','):'';
                        var route = item.div_id.toString();
                        var new_row = '<tr style="text-align:center">' +
                            '<td>' + saa + '</td>' +
                            '<td>' + proponentName + '</td>' +
                            '<td>' + '<a class="modal-link" href="#i_frame" data-routeId="'+route+'" onclick="version2(this)">' + facility_name + '</a>' + '</td>' +
                            '<td>' + number_format(parseFloat(beg_balance.replace(',', '')), 2, '.', ',') + '</td>' +
                            '<td>' + discount + '</td>' +
                            '<td>' +(item.div_id != 0 ?'<a class="modal-link" href="#i_frame" data-routeId="'+route+'" onclick="openModal(this)">' + utilize + '</a>' :utilize) +'</td>' +
                            // '<td>' + (item.div_id != 0 ? '<a href="{{ route("dv", ["keyword" => ""]) }}' + encodeURIComponent(route) + '">' + route + '</a>' : '') + '</td>' +
                            '<td>' + (item.div_id != 0 ? '<a href="{{ url("checkdv").'/' }}' + encodeURIComponent(route) + '">' + route + '</a>' : '') +'</td>'+
                            '<td>' + user + '</td>' +
                            '<td>' + formattedDate+'<br>'+ formattedTime + '</td>' +
                            '<td>' + (item.obligated == 1 ? '<i class="typcn typcn-tick menu-icon"></i>' : '') + '</td>' +
                            '<td>' + (item.paid == 1 ? '<i class="typcn typcn-tick menu-icon"></i>' : '') + '</td>' +
                            '<td style="text-align:justify">' + stat +'<br>'+ '<span class="text-success">' +item_remarks+'</span>' + '</td>' ;
                            '</tr>';
                        if(item.status != 1){
                            $('#track_body').append(new_row);
                        }
                        i= i+1;
                    });
                
                }else{
                    var new_row = '<tr>' +
                        '<td colspan ="12">' + "No Data Available" + '</td>' +
                        '</tr>';
                    $('#track_body').append(new_row);
                }
            },
            error: function (xhr, status, error) {
                console.log('Track AJAX error:', error);
            }
        });

    }

    function openModal(link) {
        var routeNo = $(link).data('routeid');
        var src = "http://192.168.110.17/dts/document/trackMaif/" + routeNo;
        var base_url = "{{ url('/') }}";
        $('.modal-body').append('<img class="loadingGif" src="' + base_url + '/public/images/loading.gif" alt="Loading..." style="display:block; margin:auto;">');

        var iframe = $('#track_iframe');

        iframe.hide();

        iframe.attr('src', src);
    
        iframe.on('load', function() {
            iframe.show(); 
            $('.loadingGif').css('display', 'none');
        });

        $('#i_frame').modal('show');
    }

    function number_format(number, decimals, decimalSeparator, thousandsSeparator) {
        decimals = decimals || 0;
        number = parseFloat(number);

        if (!isFinite(number) || !number && number !== 0) return NaN;

        var result = number.toFixed(decimals);
        result = result.replace('.', decimalSeparator);

        var parts = result.split(decimalSeparator);
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandsSeparator);

        return parts.join(decimalSeparator);
    }


    function editfundsource(fundsourceId){
        var proponent_id = event.target.getAttribute('data-proponent-id');
        $('.modal_body').html(loading);
        $('.modal-title').html("Update Fundsource");
        var url = "{{ url('fundsource/edit').'/' }}"+ fundsourceId +'/'+proponent_id;
        setTimeout(function() {
            $.ajax({
                url: url,
                type: 'GET',
                success: function(result){
                    $('.modal_body').html(result);
                }
            });
        }, 500);
    }

    function createBreakdowns(fundsourceId){
        $('.modal_body').empty();
        $('.modal_body').html(loading);
        $('.modal-title').html("Create Breakdowns");
        var url = "{{ url('fundsource/breakdowns').'/' }}"+ fundsourceId;
        setTimeout(function() {
            $.ajax({
                url: url,
                type: 'GET',
                success: function(result){
                    $('.modal_body').html(result);
                }
            });
        }, 0);
    }

    function transferFunds(info_id){
        $('.modal_body').html(loading);
        $('.modal-title').html("Transfer Funds");
        var url = "{{ url('fundsource/transfer_funds').'/' }}"+ info_id;
        setTimeout(function() {
            $.ajax({
                url: url,
                type: 'GET',
                success: function(result){
                    $('.modal_body').html(result);
                }
            });
        }, 500);
    }

    function createFundSource() {
        $('.modal_body').html(loading);
        $('.modal-title').html("Create Fundsource");
        var url = "{{ route('fundsource.create') }}";
        setTimeout(function(){
            $.ajax({
                url: url,
                type: 'GET',
                success: function(result) {
                    $('.modal_body').html(result);
                }
            });
        },500);
    }

    function addTransaction() {
        event.preventDefault();
        $.get("{{ route('transaction.get') }}",function(result) {
            $("#transaction-container").append(result);
        });
    }

    function validateAmount(element) {

        if (event.keyCode === 32) {
            event.preventDefault();
        }
        var cleanedValue = element.value.replace(/[^\d.]/g, '');
        var numericValue = parseFloat(cleanedValue);
        if (!isNaN(numericValue) || cleanedValue === '' || cleanedValue === '.') {
            element.value = cleanedValue.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        } else {
            element.value = ''; 
        }
    }

    // function proponentCode(proponent){
    //     if(proponent.val()){
    //         var proponent_id = proponent.val()
    //         var url = "{{ url('proponent').'/' }}"+ proponent_id;
    //         setTimeout(function() {
    //             $.ajax({
    //                 url: url,
    //                 type: 'GET',
    //                 success: function(result){
    //                     $("#    ").val(result).prop('readonly', true);
    //                     var selectedText = $('#proponent_exist option:selected').text();
    //                     $("#proponent").val(selectedText).prop('readonly', true);
    //                 }
    //             });
    //         }, 500);
    //     }else{
    //         $("#proponent_code").val('').prop('readonly', false);
    //     }   
    // }

    function version2(data) {
        var route_no = $(data).data('routeid');
        $('.v2_body').html(loading);
        $.get(" {{ url('/version2').'/'}}" + route_no, function (result){
            $('.v2_body').html(result);
            console.log('res', route_no);
        });
        $('#version2').modal('show');
    }
    
</script>
@endsection
