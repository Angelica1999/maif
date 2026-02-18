<?php
    use App\Models\TrackingDetails;
    use App\Models\Fundsource;
    use App\Models\Fundsource_Files;
?>
<style>
      .custom-center-align .lobibox-body .lobibox-message {
        text-align: center;
    }
</style>
@extends('layouts.app')
@section('content')
<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <form method="GET" action="">
                <div class="input-group float-right w-50" style="min-width: 600px;">
                    <input type="text" class="form-control" name="keyword" placeholder="Route No/DV No" value="{{ $keyword }}">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit"><img src="\maif\public\images\icons8_search_16.png">Search</button> 
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll"><img src="\maif\public\images\icons8_eye_16.png">View All</button>
                    </div>
                </div>
                <input type="hidden" class="all_route" id="all_route" name="all_route">
            </form> 
            <h4 class="card-title">
                DISBURSEMENT VOUCHER (V3)
                {{
                    ($type == 'unsettled' || $type == 'dv3_owed')
                        ? ' : PENDING'
                        : ($type == 'processed'
                            ? ' : OBLIGATED'
                            : ' : PAID')
                }}
            </h4>
            <p class="card-description">
                MAIF-IPP
            </p>
            @if(isset($dv3) && $dv3->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Tracking</th>
                            <th style="min-width:170px;">Route_No</th>
                            <th>Remarks</th>
                            <th>Facility</th>
                            <th style="min-width:150px;">SAA</th>
                            <th>Proponent</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th style="min-width:150px;">Created On</th>
                            <th style="min-width:150px;">Created By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dv3 as $index=> $row)
                            <tr>
                                <td>
                                    <button type="button"  class="btn btn-sm"  style="background: linear-gradient(135deg, #165A54 0%, #1a6e66 100%); width:80px; color: white;
                                        border: none; border-radius: 6px; padding: 8px 16px; font-weight: 500; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(22, 90, 84, 0.2);"
                                        data-toggle="modal" href="#iframeModal" data-routeId="{{ $row->route_no }}" id="track_load" onclick="openModal()"
                                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(22, 90, 84, 0.3)';"
                                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(22, 90, 84, 0.2)';">
                                        <i class="fa fa-map-marker" style="margin-right: 6px;"></i>Track
                                    </button>
                                </td>
                                <td>
                                    <?php
                                        $routed = TrackingDetails::where('route_no',$row->route_no)
                                            ->count();
                                        if($routed){
                                            $doc_id = TrackingDetails::where('route_no',$row->route_no)
                                            ->orderBy('id','desc')
                                            ->first()
                                            ->id;
                                        }else{
                                            $doc_id= 0;
                                        }
                                    ?>
                                    <a data-dvId="{{$row->id}}" onclick="updateDv3('{{ $row->route_no}} ')" class="text-info" data-backdrop="static" data-toggle="modal">{{ $row->route_no }}</a>
                                </td>
                                <td>
                                    @if($row->remarks == 0)
                                        Pending
                                    @elseif($row->remarks == 1)
                                        Obligated
                                    @elseif($row->remarks == 2)
                                        Processed
                                    @endif
                                </td>
                                <td>{{$row->facility->name}}</td>
                                <td>
                                    @foreach($row->extension as $item)
                                    <br>
                                        {{$item->proponentInfo->fundsource->saa}}
                                    @endforeach
                                </td>
                                <td>{{$row->extension[0]->proponentInfo->proponent->proponent}}</td>
                                <td>{{date('F j, Y', strtotime($row->date))}}</td>
                                <td>{{number_format($row->total, 2, '.', ',')}}</td>
                                <td>{{date('F j, Y', strtotime($row->created_at))}}</td>
                                <td>{{$row->user->lname .', '. $row->user->fname}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    </table>
                </div>
                @else
                    <div class="alert alert-danger" role="alert" style="width: 100%;">
                        <i class="typcn typcn-times menu-icon"></i>
                        <strong>No disbursement voucher version 3 found!</strong>
                    </div>
                @endif
            <div class="pl-5 pr-5 mt-5">
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="create_dv3" role="dialog" style="overflow-y:scroll;">
    <input type="hidden" class="identifier" id="identifier" value="{{$type}}">
    <div class="modal-dialog modal-lg" role="document" style="width:900px">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#17c964;padding:15px; color:white">
                <h4 class="modal-title"><i class="fa fa-plus" style="margin-right:auto;"></i> Disbursement Vouchers (v3)</h4>
                <button type="button" class="close" id="exit" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:white;">&times;</span></button>
            </div>
            <div class="dv3_body">
                <div class="modal_content"></div>
            </div>
        </div>
    </div>
</div>
@include('modal')
@endsection
@section('js')
<script>

    var doc_type = @json($type);
    $('#docViewerModal').hide();
    $('.filter-division').select2();
    $('.filter-section').select2();

    function createDv3() {
        $('.modal_body').html(loading);
        var url = "{{ route('dv3.create') }}";
        $.ajax({
            url: url,
            type: 'GET',
            success: function(result) {
                $('.modal_body').html(result);
            }
        });
    }

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

    // function updateDv3(route_no){
    //     $('#confirm_dv').modal('hide');
    //     $('#create_dv3').modal('show');
    //     $('.dv3_body').html(loading);
    //     $.get("{{url('dv3/update').'/'}}"+route_no, function(result){
    //         $('.dv3_body').html(result);
    //     });
    // }
    
    function updateDv3(route_no){
        $('#confirm_dv').modal('hide');
        $('#create_dv3').modal('show');
        $('.dv3_body').html(loading);
        $.get("{{url('dv3/update').'/'}}"+route_no, function(result){
            $('.dv3_body').html(result);
            Swal.fire({
                title: 'Loading...',
                html: 'Please wait while we render the data.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            $.get("{{url('dv3/update-extend').'/'}}"+route_no, function(result){
                $('.container').empty();
                if (result.dv3 && result.dv3.extension && result.dv3.extension.length > 0) {
                    result.dv3.extension.forEach(function(row, index) {
                        setTimeout(function() {
                            var containerDiv = $('<div>').attr({
                                'style': 'display: flex; align-items: center;',
                                'class': 'clone_saa'
                            });
                            
                            containerDiv.append('&nbsp;&nbsp;&nbsp;&nbsp;');
                            
                            var selectElement = $('<select>').attr({
                                'name': 'fundsource_id[]',
                                'id': 'dv3_saa1' + row.id,
                                'style': 'width:200px;',
                                'class': 'form-control dv3_saa',
                                'required': true
                            }).on('change', function() {
                                saaValue($(this));
                            });
                            
                            selectElement.append(
                                $('<option>').attr({
                                    'value': '',
                                    'data-facilities': '',
                                    'style': 'background-color:green'
                                }).text('- Select SAA -')
                            );
                            
                            if (result.processedInfo && result.processedInfo.length > 0) {
                                result.processedInfo.forEach(function(item) {
                                    var option = $('<option>').attr({
                                        'value': item.fundsource_id,
                                        'dataproponentInfo_id': item.id,
                                        'dataprogroup': item.pro_group,
                                        'data-facilities': '',
                                        'dataproponent': item.proponent_id,
                                        'd_color': item.is_zero_balance ? 'red' : 'normal',
                                        'style': 'background-color:green'
                                    }).text(item.text_display);
                                    
                                    if (row.info_id == item.id) {
                                        option.attr('selected', true);
                                    }
                                    
                                    selectElement.append(option);
                                });
                            }
                            
                            containerDiv.append(selectElement);
                            
                            containerDiv.append(
                                $('<input>').attr({
                                    'type': 'hidden',
                                    'name': 'info_id[]',
                                    'class': 'info_id',
                                    'value': row.info_id
                                })
                            );
                            
                            containerDiv.append(
                                $('<input>').attr({
                                    'type': 'hidden',
                                    'name': 'existing_info_id[]',
                                    'class': 'existing_info_id',
                                    'value': row.info_id
                                })
                            );
                            
                            containerDiv.append(
                                $('<input>').attr({
                                    'type': 'hidden',
                                    'name': 'existing[]',
                                    'class': 'existing',
                                    'value': row.amount
                                })
                            );
                            
                            var dropdownDiv = $('<div>').attr({
                                'class': 'custom-dropdown',
                                'style': 'margin-left: 8px;'
                            });
                            
                            var amountInput = $('<input>').attr({
                                'type': 'text',
                                'name': 'amount[]',
                                'value': row.amount,
                                'style': 'width:150px; height: 42px;',
                                'class': 'amount',
                                'required': true,
                                'autocompvare': 'off',
                                'disabled': true
                            }).on('keyup', function() {
                                validateAmount(this);
                            }).on('input', function() {
                                checkedAmount($(this));
                            });
                            
                            dropdownDiv.append(amountInput);
                            containerDiv.append(dropdownDiv);
                            
                            var addButton = $('<button>').attr({
                                'id': 'add_more',
                                'type': 'button',
                                'class': 'add_more fa fa-plus',
                                'style': 'border: none; width: 20px; height: 42px; font-size: 11px; cursor: pointer; width: 30px;',
                                'disabled': true
                            });
                            
                            containerDiv.append(addButton);
                            
                            $('.container').append(containerDiv.hide().fadeIn(200));
                            
                        }, index * 100); 
                    });
                    
                    var totalDelay = (result.dv3.extension.length - 1) * 150 + 200;
                    setTimeout(function() {
                        var type = $('.identifier').val();
                        if(type == "processed" || type == "done"){
                            $('.ors_no').css('display', 'none');
                            $('.btn-success').css('display', 'none');
                        }

                        var section = result.section;
                        if(section == 6 || section == 7){
                            $('.dv3_facility').prop('disabled', true);
                            $('.dv3_saa').prop('disabled', true);
                            $('#dv3_date').prop('disabled', true);
                        }else if(type == undefined){
                            $('.ors_no').css('display', 'none');
                            $('.btn-success').css('display', 'none');
                            $('.dv3_facility').prop('disabled', true);
                            $('.dv3_saa').prop('disabled', true);
                            $('#dv3_date').prop('disabled', true);
                        }else{
                            $('.add_more').prop('disabled', false);
                            $('.dv3_saa').removeAttr('disabled');
                            $('.amount').removeAttr('disabled');
                        }
    
                        $('.container .dv3_saa').select2({
                            width: '200px',
                            minimumResultsForSearch: 10,
                            templateResult: function (data) {
                                if ($(data.element).attr('d_color') === 'red') {
                                    return $('<span style="color: red;">' + data.text + '</span>');
                                }
                                return data.text;
                            }
                        });
                    }, totalDelay);
                    setTimeout(function() {
                        Swal.close(); 
                    }, totalDelay);
                }
            });
        });
    }

    function obligate(){
        $('#confirm_dv').modal('hide');
        con = 1;
        $('#create_dv3').modal('show');
        $('.dv3_body').html(loading);
        $.get("{{url('dv3/update').'/'}}"+route, function(result){
            $('.dv3_body').html(result);
        });
    }

    var util_id = 0;
    var con = 0;

    function confirmed(){

        var cs = $('.editable-input').val();
        if(cs == ''){
            Swal.fire({
                icon: "error",
                title: "Empty ORS No",
                text: " Ors no is required to confirm this data",
                timer: 1000,
                showConfirmButton: false
            });
        }else{
            $('#checkbox_' + util_id).prop('checked', true);

            var checkboxes = $('.confirm_check');
            var allChecked = checkboxes.filter(':not(:checked)').length == 0;

            if (allChecked) {
                $('.budget_obligate').css('display', 'block');
            } else {
                $('.budget_obligate').css('display', 'none');
            }
            $('#budget_confirm').modal('hide');
        }
    }
    var route;
    function displayFunds(route_no, proponent, id){
        util_id = id;
        route = route_no;
        $('#budget_confirm').modal('show');
        $('.confirm_budget').html(loading);
        $.get("{{ url('confirm-budget').'/' }}" + id, function(result) {
            $('.confirm_budget').html(result);
        });
    }
    
</script>
@endsection


