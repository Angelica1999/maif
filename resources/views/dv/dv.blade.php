<?php
    use App\Models\TrackingDetails;
?>
@extends('layouts.app')
@section('content')
<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
           
            <form method="GET" action="">
                <div class="input-group float-right w-50" style="min-width: 600px;">
                    <input type="text" class="form-control" name="keyword" placeholder="Route No" value="{{$keyword}}">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit">Search</button>
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll">View All</button>
                        @if(Auth::user()->userid != 1027 || Auth::user()->userid == 2660)
                            <button type="button" href="#create_dv" onclick="createDv()" data-backdrop="static" data-toggle="modal" class="btn btn-success btn-md">Create</button>
                            <button type="button" id="release_btn" data-target="#releaseTo" style="display:none; background:teal; color:white" onclick="putRoutes($(this))" data-target="#releaseTo" data-backdrop="static" data-toggle="modal" class="btn btn-md">Release All</button>
                            <input type="hidden" class="all_route" id="all_route" name="all_route">
                        @else
                        @endif
                    </div>
                </div>
            </form>
            <h4 class="card-title">Disbursement Voucher</h4>
            <p class="card-description">
                MAIF-IPP
            </p>
            <!-- @if(Auth::user()->userid != 1027)
                <input type="hidden" id="maif_tab" value="maif">
                <ul class="nav nav-tabs" id="dvTabs">
                    <li class="nav-item">
                        <a class="nav-link active" id="all-tab" href="{{ route('dv') }}">All</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pending-tab" data-toggle="tab" href="#pending">Pending</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="obligated-tab" data-toggle="tab" href="#obligated">Obligated</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="paid-tab" data-toggle="tab" href="#paid">Paid</a>
                    </li>
                </ul>
            @else
            <input type="hidden" id="accounting_tab" value="accounting">
                <ul class="nav nav-tabs" id="dvTabs">
                    <li class="nav-item">
                        <a class="nav-link active" id="all-tab" href="{{ route('dv') }}">All</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="withdv-tab" data-toggle="tab" href="#withdv">w/ dv_no</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="withoutdv-tab" data-toggle="tab" href="#withoutdv">w/o dv_no</a>
                    </li>
                </ul>
            @endif -->
            @if(isset($disbursement) && $disbursement->count() > 0)
            <div class="table-responsive ">
                <table class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th style="min-width: 150px;"></th>
                        <th style="min-width: 120px;">Route No</th>
                        <th>Modified</th>
                        <th>Status</th>
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
                        <th>Remarks</th>
                        <th>Payee</th>
                        <th  style="min-width: 120px;">Saa Number</th>
                        <th style="min-width: 140px;">Prepared Date</th>
                        <th style="min-width: 150px;">Exclusive Month</th>
                        <th>Amount</th>
                        <th>Total</th>
                        <th style="min-width: 150px;">Deduction</th>
                        <th style="min-width: 130px;">Total</th> 
                        <th style="min-width: 120px;">Created By</th>
                    </tr>
                </thead>
                <tbody class="table_body">
                    @foreach($disbursement as $index=> $dvs)
                        <tr> 
                            <td>                 
                                <button type="button" class="btn btn-xs col-sm-12" style="background-color:#165A54;color:white;" data-toggle="modal" href="#iframeModal" data-routeId="{{$dvs->route_no}}" id="track_load" onclick="openModal()">Track</button>
                                @if(Auth::user()->userid != 1027 || Auth::user()->userid == 2660)
                                    <button type="button" class="btn btn-xs btn-success col-sm-12 create-dv2-btn" data-toggle="modal" href="#create_dv2" data-routeId="{{$dvs->route_no}}" onclick="createDv2()">Create DV2</button>
                                @endif
                            </td>
                            <td> 
                                <a  data-dvId="{{$dvs->id}}" href="#create_dv" onclick="updateDv()" style="background-color:teal;color:white;" type="button" class="btn btn-xs" data-backdrop="static" data-toggle="modal">
                                @if(Auth::user()->userid != 1027 || Auth::user()->userid == 2660)
                                    @if($dvs->facility)
                                        {{ $dvs->route_no }}
                                    @endif
                                    </a>
                                    <?php
                                        $routed = TrackingDetails::where('route_no',$dvs->route_no)
                                            ->count();
                                        $doc_id = TrackingDetails::where('route_no',$dvs->route_no)
                                                ->orderBy('id','desc')
                                                ->first()
                                                ->id;
                                    ?>
                                    <button data-toggle="modal" data-target="#releaseTo" data-id="{{ $doc_id }}" data-route_no="{{ $dvs->route_no }}" onclick="putRoute($(this))" style="width:87px;" type="button" class="btn btn-info btn-xs">Release To</button>
                                @else
                                    <a href="#obligate"  onclick="obligateDv('{{$dvs->route_no}}','0', 'add_dvno')" style="background-color:teal;color:white; width:85px;" data-backdrop="static" data-toggle="modal" type="button" class="btn btn-xs">{{ $dvs->route_no }}</a>
                                @endif
                            </td> 
                            <td>
                                <a href="#dv_history" onclick="getHistory('{{$dvs->route_no}}')" style="background-color:teal;color:white; width:85px;" data-backdrop="static" data-toggle="modal" type="button" class="btn btn-xs">Edit History</a>
                            </td>
                            <td>
                                @if($routed > 1)
                                    Forwarded
                                @endif

                            </td>

                            <td style="text-align:center" class="group-release" data-route_no="{{ $dvs->route_no }}" data-id="{{ $doc_id }}" >
                                <input type="checkbox" style="width: 60px; height: 20px;" name="release_dv[]" id="releaseDvId_{{ $index }}" 
                                    class="group-releaseDv" >
                            </td>
                            <td>
                                @if($dvs->obligated !== null && $dvs->paid !== null)
                                    proccessed
                                @elseif($dvs->obligated == null && $dvs->paid == null)
                                    pending
                                @elseif($dvs->obligated !== null && $dvs->paid == null)
                                    obligated
                                @endif
                               
                            </td> 
                            <td>{{ $dvs->facility->name }}</td> 
                            <td>
                                @if($dvs->fundsource_id)
                                    @php
                                        $all= array_map('intval', json_decode($dvs->fundsource_id));
                                        foreach($all as $fundsourceId) {
                                            echo \App\Models\Fundsource::where('id',$fundsourceId)->value('saa');
                                            echo '<br>';
                                            }
                                    @endphp
                                @endif
                            </td> 
                            <td>{{date('F j, Y', strtotime($dvs->date))}}</td>
                            <td> @if($dvs->month_year_to !== null)
                                    {{date('F j, Y', strtotime($dvs->month_year_from)).' - '.date('F j, Y', strtotime($dvs->month_year_to))}}
                                    @else
                                    {{date('F j, Y', strtotime($dvs->month_year_from))}}
                                    @endif
                            </td>
                            <td>
                                {{$dvs->amount1}} <br>
                                {{$dvs->amount2}} <br>
                                {{$dvs->amount3}}
                            </td>
                           
                            <td>{{$dvs->total_amount}}</td>
                            <td>
                                VAT &nbsp;- {{floor($dvs->deduction1)}}% = {{$dvs->deduction_amount1}}
                                <br>
                                EWT - {{floor($dvs->deduction2)}}% = {{$dvs->deduction_amount2}}
                            </td>
                            <td>{{$dvs->overall_total_amount}}</td>
                            <td>{{ $dvs->user->lname .', '. $dvs->user->fname }}</td>
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
            <div class="pl-5 pr-5 mt-5">
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
                <b><h5 class="modal-title" name ="route_no" id="exampleModalLabel">Create Disbursement V2</h5></b>
            </div>
            <div class="modal_body">
            </div>
        </div>
    </div>
</div>

@include('modal')
@endsection
@include('dv.dv_js')