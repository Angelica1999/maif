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
            <div class="float-right" >
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
                            <button type="submit" id="gen3_btn" style="background-color:teal; color:white; width:107px" class=""><i class="typcn typcn-calendar-outline menu-icon"></i>Generate</button>
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
            <h4 class="card-title">DISBURSEMENT VOUCHER V3</h4>
            <p class="card-description">
                MAIF-IPP
            </p>
                <div class="table-responsive">
                    <table class="table table-striped" style="border-spacing: 0;">
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
                            <th style="min-width:150px;"><a href="{{ route('dv3', ['sort' => 'saa', 'order' => ($order == 'asc' ? 'desc' : 'asc')]) }}">SAA</a>
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
                                            @if($row->text_remarks != null)
                                                {{$row->text_remarks}}
                                                <a href="#update_remarks" onclick="updateRemarks('{{$row->route_no}}', '{{($row->text_remarks ==null)?0:$row->text_remarks}}')" data-backdrop="static" data-toggle="modal" type="button" class="btn btn-xs"><i class="typcn typcn-edit menu-icon" style="border-radius:0; color:green; font-size: 24px; width:200px;"></i></a>
                                            @else
                                                <a href="#update_remarks" onclick="updateRemarks('{{$row->route_no}}', '{{($row->text_remarks ==null)?0:$row->text_remarks}}')" data-backdrop="static" data-toggle="modal" type="button" class="btn btn-xs"><i class="typcn typcn-edit menu-icon" style="border-radius:0; color:green; font-size: 24px; width:200px;"></i></a>
                                            @endif
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
                                <td colspan="14">
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

<div class="modal fade" id="create_patient" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal_body">
                
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
            <div class="modal_body">
                <div class="modal_content"></div>
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
@endsection
@include('modal')
@section('js')
    <script src="{{ asset('admin/vendors/daterangepicker-master/moment.min.js?v=1') }}"></script>
    <script src="{{ asset('admin/vendors/daterangepicker-master/daterangepicker.js?v=1') }}"></script>                                        
    <script>

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

        function createDv3() {
            $('.modal_body').html(loading);
            $('.modal-title').html("Create Disbursement (v3)");
            var url = "{{ route('dv3.create') }}";
            $.ajax({
                url: url,
                type: 'GET',
                success: function(result) {
                    $('.modal_body').html(result);
                }
            });
        }
        function putRoutes(form){
            $('#route_no').val($('#all_route').val());
            $('#currentID').val($('#release_btn').val());
            $('#multiple').val('multiple');
            $('#op').val(0);
            console.log('route_no', $('#route_no').val());
            console.log('route_no', $('#currentID').val());
        }
        
        function putRoute(form){
            var route_no = form.data('route_no');
            $('#route_no').val(route_no);
            $('#op').val(0);
            $('#currentID').val(form.data('id'));
            console.log('id', form.data('id'));
            $('#multiple').val('single');
        }

        function openModal() {
            var routeNoo = event.target.getAttribute('data-routeId'); 
            var src = "https://mis.cvchd7.com/dts/document/trackMaif/" + routeNoo;
            setTimeout(function() {
                $("#trackIframe").attr("src", src);
                $("#iframeModal").css("display", "block");
            }, 150);
        }

        function updateDv3(route_no){
            $.get("{{url('dv3/update').'/'}}"+route_no, function(result){
                $('.modal_body').html(result);
            });
        }

        function updateRemarks(route_no, remarks){
            console.log('remarks', remarks);
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
                    console.log('res', result);

                    $('.filter-section').append($('<option>', {
                        value: optionData.id,
                        text: optionData.description
                    }));  
                });
            });
        });

        
    </script>
@endsection


