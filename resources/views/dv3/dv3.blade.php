<?php
    use App\Models\TrackingDetails;
    use App\Models\Fundsource;
    use App\Models\Fundsource_Files;
?>
<style>
      .custom-center-align .lobibox-body .lobibox-message {
        text-align: center;
    }
           
.input-group {
        justify-content: flex-end;
        gap: 1px;
        flex-wrap: nowrap; 
    }
     .input-group-append {
        display: flex;
        flex-wrap: nowrap;
        width: 100%;
        justify-content: flex-end; 
        
    }
.input-group .form-control {
    width: 250px !important;    /* adjust as needed */
   
}
       @media (max-width: 767px) {
    .input-group {
        flex-direction: column;     
        align-items: stretch;     
    }

    .input-group .form-control {
        width: 200% !important;
        margin-bottom: 5px;
    }

    .input-group-append {
        flex-direction: column;     /* stack buttons */
        width: 100%;
    }

    .input-group-append .btn {
        width: 100%;   
        border-radius: 5px !important;
        margin-bottom: 5px;
    }
    #gen3_btn{
         width: 100% !important;   
        border-radius: 5px !important;
        margin-bottom: 5px;
    }
}

.table-wrapper {
    max-width: 100%;
    border: 2px solid #ddd;
    position: relative; 
}


.scroll-container {
    position: absolute; 
    top: 50%; 
    left: 0;
    right: 0;
    transform: translateY(-50%);
    display: flex;
    justify-content: space-between; 
    align-items: center;
    padding: 0 10px;
    pointer-events: none; 
    z-index: 10; 
}

.scroll-arrow {
    pointer-events: auto; 
    flex-shrink: 0;
    width: 40px;
    height: 40px;
    border: none;
    background: rgba(0, 123, 255, 0.9);
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    font-size: 16px;
    border-radius: 50%;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.scroll-arrow:hover {
    background: rgba(0, 86, 179, 0.95);
    transform: scale(1.1); 
}

.scroll-arrow:active {
    background: rgba(0, 64, 133, 1);
    transform: scale(0.95);
}

.scroll-arrow:disabled {
    background: rgba(204, 204, 204, 0.7);
    cursor: not-allowed;
    opacity: 0.5;
}

.scroll-info {
    position: absolute;
    top: -30px; 
    left: 50%;
    transform: translateX(-50%);
    text-align: center;
    font-size: 14px;
    color: #666;
    font-weight: 500;
    background: rgba(248, 249, 250, 0.95);
    padding: 5px 15px;
    border-radius: 4px;
    white-space: nowrap;
}

.scroll-bottom {
    overflow-x: auto;
    overflow-y: hidden;
    background: #f8f9fa;
    border-top: 1px solid #ddd;
    height: 20px;
}

.scroll-content {
    height: 1px;
}

.table-responsive {
    overflow-x: auto;
    overflow-y: auto;
    max-height: 600px;
}
 @media (max-width: 1600px) {
    .scroll-container {
    position: sticky; 
    top:40%;
    }
   
}
 @media (max-width: 1024px) {
    .scroll-container {
        display: none;
   
    }

 }
</style>
@extends('layouts.app')
@section('content')
<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div class="mb-2 mb-md-0">
                    <h4 class="card-title">DISBURSEMENT VOUCHER V3</h4>
                    <p class="card-description">MAIF-IPP</p>
                </div>
                <div class="input-group">
                    <form method="GET" action="{{ route('dv3') }}">
                        <div class="input-group">
                            <input type="text" class="form-control" name="keyword" value="{{$keyword}}" style="width:350px;" placeholder="Search...">
                            <div class="input-group-append">
                                <button class="btn btn-sm btn-info" type="submit"><img src="\maif\public\images\icons8_search_16.png">Search</button> 
                                <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll"><img src="\maif\public\images\icons8_eye_16.png">View All</button>
                                <button type="button" href="#create_dv3" onclick="createDv3()" data-backdrop="static" data-toggle="modal" class="btn btn-success btn-md"><img src="\maif\public\images\icons8_create_16.png">Create</button>
                                <button type="button" id="release_btn" data-target="#releaseTo" style="display:none; background:teal; color:white" onclick="putRoutes($(this))" data-target="#releaseTo" data-backdrop="static" data-toggle="modal" class="btn btn-md">Release All</button>
                                <button type="submit" value="filt3" style="display:none; background-color:00563B; color:white;" name="filt3_dv" id="filt3_dv" class="btn btn-success btn-md"><i class="typcn typcn-filter menu-icon"></i>Filter</button>
                            </div>
                        </div>
                        <div class="input-group">
                            <input type="text" style="text-align:center" style="width:100px" class="form-control" id="filter_dates" value="{{($generated_dates)?$generated_dates:''}}" name="filter_dates" />
                            <button type="submit" id="gen3_btn" style="background-color:teal; color:white; width:91px; border-radius:0px" class="btn btn-sm" ><i class="typcn typcn-calendar-outline menu-icon"></i>Generate</button>
                        </div>
                        <input type="hidden" class="all_route" id="all_route" name="all_route">
                        <input type="hidden" id="filter_rem3" name="filter_rem3" value="{{implode(',', $filter_rem3)}}"></input>
                        <input type="hidden" id="filter_fac3" name="filter_fac3" value="{{implode(',', $filter_fac3)}}"></input>
                        <input type="hidden" id="filter_saa3" name="filter_saa3" value="{{implode(',', $filter_saa3)}}"></input>
                        <input type="hidden" id="filter_pro3" name="filter_pro3" value="{{implode(',', $filter_pro3)}}"></input>
                        <input type="hidden" id="filter_date3" name="filter_date3" value="{{implode(',', $filter_date3)}}"></input>
                        <input type="hidden" id="filter_on3" name="filter_on3" value="{{implode(',', $filter_on3)}}"></input>
                        <input type="hidden" id="filter_by3" name="filter_by3" value="{{implode(',', $filter_by3)}}"></input>
                        <input type="hidden" id="gen_key" name="gen_key" value="{{$gen_key}}"></input>
                    </form>
                </div>
            </div>
                <div class="table-responsive" id="dvdv">
                    <table class="table table-striped" style="border-spacing: 0;" id="dvdvdv">
                    <thead>
                        <tr>
                            <th>Tracking</th>
                            <th style="min-width:90px;">Route_No</th>
                            @if(Auth::user()->userid != 1027 && Auth::user()->userid != 2660)
                                <th>Print</th>
                                <th>Modified</th>
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
                                <th style="text-align:center">Remarks</th>
                                <th><a href="{{ route('dv3', ['sort' => 'status', 'order' => ($order == 'asc' ? 'desc' : 'asc')]) }}">Forwarded</a></th>
                            @endif
                            <th style="min-width:120px;"><a href="{{ route('dv3', ['sort' => 'remarks', 'order' => ($order == 'asc' ? 'desc' : 'asc')]) }}">Status</a>
                                <i id="rem3_i" class="typcn typcn-filter menu-icon"><i>
                                <div class="filter3" id="rem3_div" style="display:none;">
                                    <select style="width: 120px;" id="rem3_select" name="rem3_select" multiple>
                                        <?php $rem=['pending', 'obligated', 'processed']; 
                                            $val = [0,1,2];
                                        ?>
                                        <option value=''>Select</option>
                                        @foreach($rem as $index=>$d)
                                            <option value="{{$val[$index]}}"  {{ is_array($filter_rem3) && in_array($val[$index], $filter_rem3) ? 'selected' : '' }}>
                                                {{ $d }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </th>
                            <th style="min-width:120px;"><a href="{{ route('dv3', ['sort' => 'facility', 'order' => ($order == 'asc' ? 'desc' : 'asc')]) }}">Facility</a>
                                <i id="fac3_i" class="typcn typcn-filter menu-icon"><i>
                                <div class="filter3" id="fac3_div" style="display:none;">
                                    <select style="width: 120px;" id="fac3_select" name="fac3_select" multiple>
                                        <option value=''>Select</option>
                                        @foreach($facilities as $d)
                                            <option value="{{$d->id}}"  {{ is_array($filter_fac3) && in_array($d->id, $filter_fac3) ? 'selected' : '' }}>
                                                {{ $d->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </th>
                            <th style="min-width:250px;"><a href="{{ route('dv3', ['sort' => 'saa', 'order' => ($order == 'asc' ? 'desc' : 'asc')]) }}">SAA</a>
                                <i id="saa3_i" class="typcn typcn-filter menu-icon"><i>
                                <div class="filter3" id="saa3_div" style="display:none;">
                                    <select style="width: 120px;" id="saa3_select" name="saa3_select" multiple>
                                        <option value=''>Select</option>
                                        @foreach($saa as $d)
                                            <option value="{{$d->id}}"  {{ is_array($filter_saa3) && in_array($d->id, $filter_saa3) ? 'selected' : '' }}>
                                                {{ $d->saa }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </th>
                            <th style="min-width:150px;"><a href="{{ route('dv3', ['sort' => 'proponent', 'order' => ($order == 'asc' ? 'desc' : 'asc')]) }}">Proponent</a>
                                <i id="pro3_i" class="typcn typcn-filter menu-icon"><i>
                                <div class="filter3" id="pro3_div" style="display:none;">
                                    <select style="width: 120px;" id="pro3_select" name="pro3_select" multiple>
                                        <option value=''>Select</option>
                                        @foreach($proponents as $d)
                                            <option value="{{$d->id}}"  {{ is_array($filter_pro3) && in_array($d->id, $filter_pro3) ? 'selected' : '' }}>
                                                {{ $d->proponent }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </th>
                            <th  style="min-width:120px;"><a href="{{ route('dv3', ['sort' => 'date', 'order' => ($order == 'asc' ? 'desc' : 'asc')]) }}">Date</a>
                                <i id="date3_i" class="typcn typcn-filter menu-icon"><i>
                                <div class="filter3" id="date3_div" style="display:none;">
                                    <select style="width: 120px;" id="date3_select" name="date3_select" multiple>
                                        <option value=''>Select</option>
                                        @foreach($dates as $d)
                                            <option value="{{$d}}"  {{ is_array($filter_date3) && in_array($d, $filter_date3) ? 'selected' : '' }}>
                                                {{ date('F j, Y', strtotime($d)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </th>
                            <th><a href="{{ route('dv3', ['sort' => 'total', 'order' => ($order == 'asc' ? 'desc' : 'asc')]) }}">Total</a></th>
                            <th style="min-width:150px;"><a href="{{ route('dv3', ['sort' => 'on', 'order' => ($order == 'asc' ? 'desc' : 'asc')]) }}">Created On</a>
                                <i id="on3_i" class="typcn typcn-filter menu-icon"><i>
                                <div class="filter3" id="on3_div" style="display:none;">
                                    <select style="width: 120px;" id="on3_select" name="on3_select" multiple>
                                        <option value=''>Select</option>
                                        @foreach($on as $d)
                                            <option value="{{ date('Y-m-d', strtotime($d)) }}" 
                                                    {{ is_array($filter_on3) && in_array(date('Y-m-d', strtotime($d)), $filter_on3) ? 'selected' : '' }}>
                                                {{ date('F j, Y', strtotime($d)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </th>
                            <th style="min-width:150px;"><a href="{{ route('dv3', ['sort' => 'by', 'order' => ($order == 'asc' ? 'desc' : 'asc')]) }}">Created By</a>
                                <i id="by3_i" class="typcn typcn-filter menu-icon"><i>
                                <div class="filter3" id="by3_div" style="display:none;">
                                    <select style="width: 120px;" id="by3_select" name="by3_select" multiple>
                                        <option value=''>Select</option>
                                        @foreach($by as $d)
                                            <option value="{{$d->userid}}"  {{ is_array($filter_by3) && in_array($d->userid, $filter_by3) ? 'selected' : '' }}>
                                                {{ $d->lname.', '. $d->fname}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($dv3) && $dv3->count() > 0)
                        <div class="scroll-container">
                    <button class="scroll-arrow scroll-arrow-left" id="scrollLeftTop" title="Scroll Left">
                        <i class="fa fa-chevron-left"></i>
                    </button>
                    <div class="" id="">
                    
                    </div>
                    <button class="scroll-arrow scroll-arrow-right" id="scrollRightTop" title="Scroll Right">
                        <i class="fa fa-chevron-right"></i>
                    </button>
                </div>
                            @foreach($dv3 as $index=> $row)
                                <tr>
                                    <td style="padding: 5;">
                                        <button type="button" class="btn btn-xs col-sm-12" style="border-radius:0; background-color:#165A54; color:white;" data-toggle="modal" href="#iframeModal" data-routeId="{{$row->route_no}}" id="track_load" onclick="openModal()">Track</button>
                                    </td>
                                    <td style="padding: 5;">
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
                                        <a data-dvId="{{$row->id}}" href="#create_dv3" onclick="updateDv3('{{$row->route_no}}')" style="border-radius:0; background-color:teal; color:white;width:90px;" type="button" class="btn btn-xs" data-backdrop="static" data-toggle="modal">{{ $row->route_no }}</a>
                                        @if(Auth::user()->userid != 1027 && Auth::user()->userid != 2660)
                                            <button data-toggle="modal" data-target="#releaseTo" data-id="{{ $doc_id }}" data-route_no="{{ $row->route_no }}" onclick="putRoute($(this))" style="border-radius:0; background-color:#1E90FF; color:white; width:90px; margin-top:1px" type="button" class="btn btn-xs">Release To</button>
                                        @endif
                                    </td>
                                    @if(Auth::user()->userid != 1027 && Auth::user()->userid != 2660)
                                        <td style="padding: 5;">
                                            <a href="{{ route('dv3.pdf', ['route_no' => $row->route_no]) }}" style="border-radius:0; background-color:green; color:white; width:60px;" target="_blank" type="button" class="btn btn-xs">Print</a>
                                        </td>
                                        <td style="padding: 5;">
                                            <a href="#dv_history" onclick="getHistory('{{$row->route_no}}')" style="border-radius:0; background-color:#0D98BA; color:white; width:80px;" data-backdrop="static" data-toggle="modal" type="button" class="btn btn-xs">Edit History</a>
                                        </td>
                                        <td style="text-align:center;padding: 5;" class="group-release" data-route_no="{{ $row->route_no }}" data-id="{{ $doc_id }}" >
                                            <input type="checkbox" style="width: 60px; height: 20px; border-radius:0;" name="release_dv[]" id="releaseDvId_{{ $index }}" 
                                                class="group-releaseDv" >
                                        </td>
                                        <td style="padding: 4px; text-align:center; word-wrap: break-word; min-width: 200px; ">
                                            @if($row->text_remarks)
                                                @php
                                                    $words = explode(' ', $row->text_remarks);
                                                    $firstFive = implode(' ', array_slice($words, 0, 5));
                                                @endphp

                                                <div class="remarks-container">
                                                    <span class="text-preview">{{ $firstFive }}{{ count($words) > 5 ? '...' : '' }}</span>

                                                    @if(count($words) > 5)
                                                        <a href="javascript:void(0);" class="see-more-toggle" onclick="toggleRemarks(this)">See more</a>
                                                        <span class="full-text" style="display:none;">{{ $row->text_remarks }}</span>
                                                    @endif
                                                </div>
                                            @endif
                                            <a href="#update_remarks"
                                                onclick='updateRemarks({!! json_encode($row->route_no) !!}, {!! json_encode($row->text_remarks ?? '') !!})'
                                                data-backdrop="static"
                                                data-toggle="modal"
                                                type="button"
                                                class="btn btn-xs">
                                                <i class="typcn typcn-edit menu-icon" style="border-radius:0; color:green; font-size: 24px; width:200px;"></i>
                                            </a>
                                        </td>
                                        <td>
                                            @if($row->status == 1)
                                                Forwarded
                                            @endif
                                        </td>
                                    @endif
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
                        @else
                            <tr>
                                <td colspan="15">
                                    <div class="alert alert-danger" role="alert" style="width: 100%;">
                                        <i class="typcn typcn-times menu-icon"></i>
                                        <strong>No disbursement voucher version 3 found!</strong>
                                    </div>
                                </td>
                            </tr>
                            
                        @endif
                    </tbody>
                    </table>
                </div>
            <div class="pl-5 pr-5 mt-5">
                {!! $dv3->appends(request()->query())->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="create_dv3" role="dialog" style="overflow-y:scroll;">
    <input type="hidden" class="identifier" id="identifier" value="none">
    <div class="modal-dialog modal-lg" role="document" style="width:900px">
    <div class="modal-content">
            <div class="modal-header" style="background-color:#17c964;padding:15px; color:white">
                <h4 class="modal-title"><i class="fa fa-plus" style="margin-right:auto;"></i> Disbursement Voucher (v3)</h4>
                <button type="button" class="close" id="exit" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:white;">&times;</span></button>
            </div>
            <div class="dv3_body">
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="filter_dv3" tabindex="-1" role="dialog" aria-hidden="true" style="opacity:2">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form method="GET" action="{{ route('dv3') }}">
                <div class="modal-body_release" style="padding:10px">
                    <h4 class="text-success"><i style = "font-size:30px"class="typcn typcn-location-arrow menu-icon"></i> Filter Dates</h4><hr/>
                    <input type="text" style="text-align:center" class="form-control" id="filter_dates" name="filter_dates" required/>
                    @csrf    
                </div>
                <div class="modal-footer">
                    <button style = "background-color:gray; color:white"  class="btn btn-xs btn-default" data-dismiss="modal"><i class="typcn typcn-times menu-icon"></i> Close</button>
                    <button type="submit" class="btn btn-success btn-xs btn-submit" onclick=""><i style = "" class="typcn typcn-location-arrow menu-icon"></i> Generate</button>
                </div>
            </form>
        </div>
    </div>
</div>
@include('modal')
@endsection
@section('js')
<script src="{{ asset('admin/vendors/daterangepicker-master/moment.min.js?v=1') }}"></script>
<script src="{{ asset('admin/vendors/daterangepicker-master/daterangepicker.js?v=1') }}"></script>                                        
<script>

function toggleRemarks(link) {
    const container = link.closest('.remarks-container');
    const preview = container.querySelector('.text-preview');
    const full = container.querySelector('.full-text');

    if (full.style.display === 'none') {
        preview.style.display = 'none';
        full.style.display = 'inline';
        link.textContent = 'See less';
    } else {
        preview.style.display = 'inline';
        full.style.display = 'none';
        link.textContent = 'See more';
    }
}


    $(function() {
        $('#filter_dates').daterangepicker();
    });
    $('#gen3_btn').on('click', function(){
        $('#gen_key').val(1);
    });
    $('.filter-division').select2();
    $('.filter-section').select2();
    $('#rem3_select').select2();
    $('#fac3_select').select2();
    $('#saa3_select').select2();
    $('#pro3_select').select2();
    $('#date3_select').select2();
    $('#on3_select').select2();
    $('#by3_select').select2();

    $('#rem3_i').on('click', function(){
        $('#rem3_div').css('display', 'block');
    });
    $('#fac3_i').on('click', function(){
        $('#fac3_div').css('display', 'block');
    });
    $('#saa3_i').on('click', function(){
        $('#saa3_div').css('display', 'block');
    });
    $('#pro3_i').on('click', function(){
        $('#pro3_div').css('display', 'block');
    });
    $('#date3_i').on('click', function(){
        $('#date3_div').css('display', 'block');
    });
    $('#on3_i').on('click', function(){
        $('#on3_div').css('display', 'block');
    });
    $('#by3_i').on('click', function(){
        $('#by3_div').css('display', 'block');
    });
    $('.filter3').on('click', function(){
        $('#filt3_dv').css('display', 'block');
    });
    $('#filt3_dv').on('click', function(){
        $('#filter_rem3').val($('#rem3_select').val());
        $('#filter_fac3').val($('#fac3_select').val());
        $('#filter_saa3').val($('#saa3_select').val());
        $('#filter_pro3').val($('#pro3_select').val());
        $('#filter_date3').val($('#date3_select').val());
        $('#filter_on3').val($('#on3_select').val());

    });

    $('#dv3_facility').select2();

    function createDv3() {
        $('.dv3_body').html(loading);
        
        $('.modal-title').html("Create Disbursement (v3)");
        var url = "{{ route('dv3.create') }}";
        $.ajax({
            url: url,
            type: 'GET',
            success: function(result) {
                $('.dv3_body').html(result);
            }
        });
    }
    function putRoutes(form){
        $('#route_no').val($('#all_route').val());
        $('#currentID').val($('#release_btn').val());
        $('#multiple').val('multiple');
        $('#op').val(0);
    }
    
    function putRoute(form){
        var route_no = form.data('route_no');
        $('#route_no').val(route_no);
        $('#op').val(0);
        $('#currentID').val(form.data('id'));
        $('#multiple').val('single');
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

    function updateDv3(route_no){
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
                        console.log('section', section);
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
                            console.log('dsad');
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

    function updateRemarks(route_no, remarks){
        console.log(remarks);
        if(remarks != 0){
            $('.text_remarks').val(remarks);
        }
        $('.remarks_id').val(route_no);
    }

    $('.group-releaseDv').change(function () {
        document.getElementById('release_btn').style.display = 'inline-block';
        
        var checkedMailBoxes = $('.group-releaseDv:checked');
        var ids = [];
        var routes = [];

        checkedMailBoxes.each(function () {
            var doc_id = $(this).closest('.group-release').data('id');
            var route = $(this).closest('.group-release').data('route_no');
            ids.push(doc_id);
            routes.push(route);
        });
        if(ids.length ==  0){
            document.getElementById('release_btn').style.display = 'none';
        }
        $('#release_btn').val(ids);
        $('#all_route').val(routes);

    });

    $('.filter-division').on('change',function(){
        // checkDestinationForm();
        var id = $(this).val();
        $('.filter-section').html('<option value="">Select section...</option>')
        $.get("{{ url('getsections').'/' }}"+id, function(result) {
            $.each(result, function(index, optionData) {
                $('.filter-section').append($('<option>', {
                    value: optionData.id,
                    text: optionData.description
                }));  
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const tableContainer = document.getElementById('dvdv');
        const table = document.getElementById('dvdvdv');
        
        const scrollLeftTop = document.getElementById('scrollLeftTop');
        const scrollRightTop = document.getElementById('scrollRightTop');
        
        const scrollAmount = 200; 
    
        function updateArrowStates() {
            const scrollLeft = tableContainer.scrollLeft;
            const maxScroll = tableContainer.scrollWidth - tableContainer.clientWidth;
            scrollLeftTop.disabled = scrollLeft <= 0;
          
            scrollRightTop.disabled = scrollLeft >= maxScroll - 1; 
           
        }
        
        function scrollHorizontally(direction) {
            const currentScroll = tableContainer.scrollLeft;
            const newScroll = direction === 'left' 
                ? Math.max(0, currentScroll - scrollAmount)
                : currentScroll + scrollAmount;
            
            tableContainer.scrollTo({
                left: newScroll,
                behavior: 'smooth'
            });
        }
        
        scrollLeftTop.addEventListener('click', () => scrollHorizontally('left'));
        scrollRightTop.addEventListener('click', () => scrollHorizontally('right'));
       
        tableContainer.addEventListener('scroll', updateArrowStates);
        
        updateArrowStates();
        
        window.addEventListener('resize', updateArrowStates);
        
        tableContainer.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') {
                scrollHorizontally('left');
                e.preventDefault();
            } else if (e.key === 'ArrowRight') {
                scrollHorizontally('right');
                e.preventDefault();
            }
        });
    });
</script>
@endsection


