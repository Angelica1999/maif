<?php
    use App\Models\TrackingDetails;
    use App\Models\Fundsource;
    use App\Models\Fundsource_Files;
?>
<style>
    #dv_table_length,
    #dv_table_filter {
        display: none;
    }
    div.dataTables_wrapper div.dataTables_filter {
        display: none;
    }
    #dv_container {
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
            <form method="GET" action="">
                <div class="input-group float-right w-50" style="min-width: 600px;">
                    <input type="text" class="form-control" name="keyword" placeholder="Search..." value="{{$keyword}}" id="search-input">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit"><img src="\maif\public\images\icons8_search_16.png">Search</button>
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll"><img src="\maif\public\images\icons8_eye_16.png">View All</button>
                        <button type="button" href="#filter_dv" data-backdrop="static" data-toggle="modal" style="background-color:teal; color:white; width:100px" class=""><i class="typcn typcn-calendar-outline menu-icon"></i>Generate</button>
                        @if(Auth::user()->userid != 1027 || Auth::user()->userid == 2660)
                            <button type="button" href="#create_dv" onclick="createDv()" data-backdrop="static" data-toggle="modal" class="btn btn-success btn-md"><img src="\maif\public\images\icons8_create_16.png">Create</button>
                            <button type="button" id="release_btn" data-target="#releaseTo" style="display:none; background:teal; color:white" onclick="putRoutes($(this))" data-target="#releaseTo" data-backdrop="static" data-toggle="modal" class="btn btn-md">Release All</button>
                            <input type="hidden" class="all_route" id="all_route" name="all_route">
                        @else
                        @endif
                        <button type="submit" value="filt" style="display:none; background-color:00563B; color:white;" name="filt_dv" id="filt_dv" class="btn btn-success btn-md"><i class="typcn typcn-filter menu-icon"></i>Filter</button>
                    </div>
                </div>
                <input type="hidden" id="filter_rem" name="filter_rem"></input>
                <input type="hidden" id="filter_fac" name="filter_fac"></input>
                <input type="hidden" id="filter_pro" name="filter_pro"></input>
                <input type="hidden" id="filter_date" name="filter_date"></input>
                <input type="hidden" id="filter_created" name="filter_created"></input>
                <input type="hidden" id="filter_saa" name="filter_saa"></input>
            </form>
            <h4 class="card-title">Disbursement Voucher</h4>
            <p class="card-description">
                MAIF-IPP
            </p>
            @if(isset($disbursement) && $disbursement->count() > 0)
                <div class="table-responsive" id="dv_container">
                    <table class="table table-striped" style="width:100%" id="dv_table">
                    <thead>
                        <tr>
                            <th style="min-width: 100px;"></th>
                            <th>Route No</th>
                            <th></th>
                            <th>Modified</th>
                            <th style="min-width: 50px;">Status</th>
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
                            <th style="min-width: 170px;">Remarks <i id="rem_i" class="typcn typcn-filter menu-icon"><i>
                                <div class="filter" id="rem_div" style="display:none;">
                                    <select style="width: 120px;" id="rem_select" name="rem_select">
                                        <?php $rem=['pending', 'obligated', 'processed']; ?>
                                        <option value=''>Select</option>
                                        @foreach($rem as $d)
                                            <option value="{{$d}}"  {{ is_array($filter_rem) && in_array($d, $filter_rem) ? 'selected' : '' }}>
                                                {{ $d }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </th>
                            <th style="min-width: 150px;"><a href="{{ route('dv', ['sort' => 'facility', 'order' => ($order == 'asc' ? 'desc' : 'asc')]) }}">Facility</a>
                                <i id="fac_i" class="typcn typcn-filter menu-icon"><i>
                                <div class="filter" id="fac_div" style="display:none;">
                                    <select style="width: 120px;" id="fac_select" name="fac_select" multiple>
                                        @foreach($facility as $d)
                                            <option value="{{$d->id}}"  {{ is_array($filter_fac) && in_array($d->id, $filter_fac) ? 'selected' : '' }}>
                                                {{ $d->name}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>    
                            </th>
                            <th style="min-width: 160px;"><a href="{{ route('dv', ['sort' => 'saa', 'order' => ($order == 'asc' ? 'desc' : 'asc')]) }}">SAA No.</a>
                                <i id="saa_i" class="typcn typcn-filter menu-icon"><i>
                                <div class="filter" id="saa_div" style="display:none;">
                                    <select style="width: 120px;" id="saa_select" name="saa_select" multiple>
                                        @foreach($fundsources as $d)
                                            <option value="{{$d->id}}"  {{ is_array($filter_saa) && in_array($d->id, $filter_saa) ? 'selected' : '' }}>
                                                {{ $d->saa}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>   
                            </th>
                            <th style="min-width: 180px;"><a href="{{ route('dv', ['sort' => 'proponent', 'order' => ($order == 'asc' ? 'desc' : 'asc')]) }}">Proponent</a>
                                <i id="pro_i" class="typcn typcn-filter menu-icon"><i>
                                <div class="filter" id="pro_div" style="display:none;">
                                    <select style="width: 120px;" id="pro_select" name="pro_select" multiple>
                                        @foreach($pros as $d)
                                            <option value="{{$d}}"  {{ is_array($filter_pro) && in_array($d, $filter_pro) ? 'selected' : '' }}>
                                                {{ $d}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>       
                            </th>
                            <th style="min-width: 140px;"><a href="{{ route('dv', ['sort' => 'date', 'order' => ($order == 'asc' ? 'desc' : 'asc')]) }}">Date</a>
                                <i id="date_i" class="typcn typcn-filter menu-icon"><i>
                                <div class="filter" id="date_div" style="display:none;">
                                    <select style="width: 120px;" id="date_select" name="date_select" multiple>
                                        @foreach($date as $d)
                                            <option value="{{ date('Y-m-d', strtotime($d)) }}" 
                                                    {{ is_array($filter_date) && in_array(date('Y-m-d', strtotime($d)), $filter_date) ? 'selected' : '' }}>
                                                {{ date('F j, Y', strtotime($d)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>  
                            </th>
                            <th style="min-width: 160px;">Month </th>
                            <th style="min-width: 170px;">Amount </th>
                            <th style="min-width: 150px;">Total </th>
                            <th style="min-width: 190px;"><a href="{{ route('dv', ['sort' => 'user', 'order' => ($order == 'asc' ? 'desc' : 'asc')]) }}">Created By</a>
                                <i id="created_i" class="typcn typcn-filter menu-icon"><i>
                                <div class="filter" id="created_div" style="display:none;">
                                    <select style="width: 120px;" id="created_select" name="created_select" multiple>
                                        @foreach($users as $d)
                                            <option value="{{ $d->userid }}" 
                                                    {{ is_array($filter_created) && in_array($d->userid, $filter_created) ? 'selected' : '' }}>
                                                {{ $d->lname }}, {{ $d->fname }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div> 
                            </th>
                        </tr>
                    </thead>
                    <tbody class="table_body" id="t_bod">
                        @foreach($disbursement as $index=> $dvs)
                            <tr> 
                                <td>                 
                                    <button type="button" class="btn btn-xs col-sm-12" style="background-color:#165A54;color:white;" data-toggle="modal" href="#iframeModal" data-routeId="{{$dvs->route_no}}" id="track_load" onclick="openModal()">Track</button>
                                    @if(Auth::user()->userid != 1027 || Auth::user()->userid == 2660)
                                        @if(count($dvs->dv2) > 0)
                                            <button type="button" class="btn btn-xs btn-success col-sm-12 create-dv2-btn" data-toggle="modal" href="#create_dv2" data-amount="{{$dvs->total_amount}}" data-routeId="{{$dvs->route_no}}" onclick="createDv2()">Update DV2</button>
                                        @else
                                            <button type="button" class="btn btn-xs btn-success col-sm-12 create-dv2-btn" data-toggle="modal" href="#create_dv2" data-amount="{{$dvs->total_amount}}" data-routeId="{{$dvs->route_no}}" onclick="createDv2()">Create DV2</button>
                                        @endif
                                    @endif
                                </td>
                                <td > 
                                    <a  data-dvId="{{$dvs->id}}" href="#create_dv" onclick="updateDv()" style="background-color:teal;color:white;" type="button" class="btn btn-xs" data-backdrop="static" data-toggle="modal">
                                    @if(Auth::user()->userid != 1027 || Auth::user()->userid == 2660)
                                        @if($dvs->facility)
                                            {{ $dvs->route_no }}
                                        @endif
                                        </a>
                                        <?php
                                            $routed = TrackingDetails::where('route_no',$dvs->route_no)
                                                ->count();
                                            if($routed){
                                                $doc_id = TrackingDetails::where('route_no',$dvs->route_no)
                                                ->orderBy('id','desc')
                                                ->first()
                                                ->id;
                                            }else{
                                                $doc_id= 0;
                                            }
                                        ?>
                                        <button data-toggle="modal" data-target="#releaseTo" data-id="{{ $doc_id }}" data-route_no="{{ $dvs->route_no }}" onclick="putRoute($(this))" style="background-color:#1E90FF;color:white; width:85px;" type="button" class="btn btn-xs">Release To</button>
                                    @else
                                        <a href="#obligate"  onclick="obligateDv('{{$dvs->route_no}}', 'add_dvno')" style="background-color:teal;color:white; width:83px;" data-backdrop="static" data-toggle="modal" type="button" class="btn btn-xs">{{ $dvs->route_no }}</a>
                                    @endif
                                </td> 
                                <td> 
                                    <a href="{{ route('dv.pdf', ['dvId' => $dvs->id]) }}" style="background-color:green;color:white; width:50px;" target="_blank" type="button" class="btn btn-xs">Print</a>
                                </td>
                                <td>
                                    <a href="#dv_history" onclick="getHistory('{{$dvs->route_no}}')" style="background-color:teal;color:white; width:80px;" data-backdrop="static" data-toggle="modal" type="button" class="btn btn-xs">Edit History</a>
                                </td>
                                <td class="td">
                                    @if($routed > 1)
                                        Forwarded
                                    @endif
                                </td>

                                <td style="text-align:center;" class="group-release" data-route_no="{{ $dvs->route_no }}" data-id="{{ $doc_id }}" >
                                    <input type="checkbox" style="width: 60px; height: 20px;" name="release_dv[]" id="releaseDvId_{{ $index }}" 
                                        class="group-releaseDv" >
                                </td>
                                <td class="td">
                                    @if($dvs->obligated !== null && $dvs->paid !== null)
                                        processed
                                    @elseif($dvs->obligated == null && $dvs->paid == null)
                                        pending
                                    @elseif($dvs->obligated !== null && $dvs->paid == null)
                                        obligated
                                    @endif
                                </td> 
                                <td class="td">{{ $dvs->facility->name }}</td> 
                                <td class="td">
                                    @if($dvs->fundsource_id)
                                        <?php $all= array_map('intval', json_decode($dvs->fundsource_id)); ?>
                                            @foreach($all as $fundsourceId) 
                                                <?php $saa = Fundsource::where('id', $fundsourceId)->value('saa');
                                                    $path = Fundsource_Files::where('saa_no', $saa)->value('path');
                                                ?>
                                                <a data-toggle="modal" onclick="displayImage('{{ $path }}')">{{$saa}}</a>
                                                <br>
                                            @endforeach
                                    @endif
                                </td> 
                                <td>
                                    <?php
                                        $intArray = array_map('intval', json_decode($dvs->proponent_id));
                                        if (!empty($intArray)) {
                                            $pro_name = $proponents->where('id', $intArray[0])->value('proponent');
                                            if($pro_name){
                                                echo $pro_name;
                                            }else{
                                                $ids = array_map('intval', json_decode($dvs->info_id));
                                                if($ids){
                                                    $id = $proponentInfo->where('id', $ids[0])->value('proponent_id');
                                                    echo $proponents->where('id', $id)->value('proponent');
                                                }
                                            }
                                        } else {
                                            $ids = array_map('intval', json_decode($dvs->info_id));
                                            if($ids){
                                                $id = $proponentInfo->where('id', $ids[0])->value('proponent_id');
                                                echo $proponents->where('id', $id)->value('proponent');
                                            }

                                        }
                                    ?>
                                </td>
                                <td class="td">{{date('F j, Y', strtotime($dvs->date))}}</td>
                                <td class="td"> @if($dvs->month_year_to !== null)
                                        {{date('F Y', strtotime($dvs->month_year_from)).' - '.date('F Y', strtotime($dvs->month_year_to))}}
                                        @else
                                        {{date('F Y', strtotime($dvs->month_year_from))}}
                                        @endif
                                </td>
                                <td class="td">
                                    {{$dvs->amount1}} <br>
                                    {{$dvs->amount2}} <br>
                                    {{$dvs->amount3}}
                                </td>
                            
                                <td class="td">{{$dvs->total_amount}}</td>
                                <td class="td">{{ $dvs->user->lname .', '. $dvs->user->fname }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-danger" role="alert" style="width: 100%;">
                    <i class="typcn typcn-times menu-icon"></i>
                    <strong>No disbursement voucher found!</strong>
                </div>
            @endif
            <div class="pl-5 pr-5 mt-5" id ="pagination_links">
                {!! $disbursement->appends(request()->query())->links('pagination::bootstrap-5') !!}
            </div>
            
        </div>
    </div>
</div>

<div class="modal fade" id="create_dv" role="dialog" style="overflow-y:scroll;">
    <div class="modal-dialog modal-lg" role="document" style="width:900px">
    <div class="modal-content">
            <div class="modal-header" style="background-color:#17c964;padding:15px; color:white">
                <h4 class="modal-title"><i class="fa fa-plus" style="margin-right:auto;"></i> Disbursement Voucher</h4>
                <button type="button" class="close" id="exit" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:white;">&times;</span></button>
            </div>
            <div class="modal_body">
                <div class="modal_content"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="create_dv2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <b><h5 class="text-success modal-dv2" name ="route_no" id="exampleModalLabel">Create Disbursement V2</h5></b>
            </div>
            <div class="modal_body">
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="fundsource_files" tabindex="-1" role="dialog" aria-hidden="true" style="background: transparent; border: none;">
    <div class="modal-dialog" role="document" style="background: transparent; border: none;">
        <div class="modal-content" style="background: transparent; border: none;">
            <div class="modal_body_files" style="background: transparent; border: none;">
                <div id="sample_modal" style="background: transparent; border: none;">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="filter_dv" tabindex="-1" style="" role="dialog" style="opacity:3" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content"> 
            <form method="GET" action="{{ route('dv') }}">
                <div class="modal-body_release" style="padding:10px">
                    <h4 class="text-success"><i style = "font-size:30px"class="typcn typcn-location-arrow menu-icon"></i> Filter Dates</h4><hr/>
                    <input type="text" style="text-align:center" class="form-control" id="dates_filter" value="" name="dates_filter" />
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
@include('dv.dv_js')