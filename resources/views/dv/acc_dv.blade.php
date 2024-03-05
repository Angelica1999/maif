<?php
    use App\Models\TrackingDetails;
    use App\Models\Tracking_Releasev2;

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
                        @if(Auth::user()->userid != 1027)
                            <button type="button" href="#create_dv" onclick="createDv()" data-backdrop="static" data-toggle="modal" class="btn btn-success btn-md">Create</button>
                        @else
                        @endif
                    </div>
                </div>
            </form>
            <h4 class="card-title">Disbursement Voucher</h4>
            <p class="card-description">
                MAIF-IPP
            </p>
           
            @if(isset($disbursement) && $disbursement->count() > 0)
            <div class="table-responsive ">
                <table class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th style="min-width: 150px;"></th>
                        <th style="min-width: 120px;">Route No</th>
                        <th>Status</th>
                        <th>Payee</th>
                        <th  style="min-width: 120px;">Saa Number</th>
                        <th style="min-width: 140px;">Prepared Date</th>
                        <th style="min-width: 150px;">Exclusive Month</th>
                        <th>Amount</th>
                        <th  style="min-width: 150px;">Total Amount</th>
                        <th>Vat/Ewt</th>
                        <th style="min-width: 180px;">Deduction (VAT/EWT)</th>
                        <th style="min-width: 130px;">Total Deduction</th> 
                        <th>OverAllTotal</th>
                        <th style="min-width: 120px;">Created By</th>
                    </tr>
                </thead>
                <tbody class="table_body">
                    @foreach($disbursement as $dvs)
                        <?php
                            $routed = Tracking_Releasev2::where('route_no',$dvs->route_no)
                                ->count();
                        ?>

                        @if($routed > 0)
                            <tr> 
                                <td>                 
                                    <button type="button" class="btn btn-xs col-sm-12" style="background-color:teal;color:white;" data-toggle="modal" href="#iframeModal" data-routeId="{{$dvs->route_no}}" id="track_load" onclick="openModal()">Track</button>
                                    @if(Auth::user()->userid != 1027)
                                        <button type="button" class="btn btn-xs btn-success col-sm-12 create-dv2-btn" data-toggle="modal" href="#create_dv2" data-routeId="{{$dvs->route_no}}" onclick="createDv2()">Create DV2</button>
                                    @endif
                                </td>
                                <td> 
                                    
                                    @if(Auth::user()->userid != 1027)
                                        <a  data-dvId="{{$dvs->id}}" href="#create_dv" onclick="updateDv()" style="background-color:teal;color:white;" type="button" class="btn btn-xs" data-backdrop="static" data-toggle="modal">
                                            @if($dvs->facility)
                                                {{ $dvs->route_no }}
                                            @endif
                                        </a>
                                        <?php
                                            $routed = TrackingDetails::where('route_no',$dvs->route_no)
                                                ->count();
                                        ?>
                                        @if($routed < 2)
                                            <?php
                                                $doc_id = TrackingDetails::where('route_no',$dvs->route_no)
                                                        ->orderBy('id','desc')
                                                        ->first()
                                                        ->id;
                                            ?>
                                            <button data-toggle="modal" data-target="#releaseTo" data-id="{{ $doc_id }}" data-route_no="{{ $dvs->route_no }}" onclick="putRoute($(this))" style="width:85px;" type="button" class="btn btn-info btn-xs">Release To</button>
                                        @endif
                                    @else
                                        <a href="#obligate"  onclick="obligateDv('{{$dvs->route_no}}','0', 'add_dvno')" style="background-color:teal;color:white;" data-backdrop="static" data-toggle="modal" type="button" class="btn btn-xs">{{ $dvs->route_no }}</a>
                                    @endif
                                    
                                    
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
                                    VAT - {{$dvs->deduction1}}% 
                                    <br>
                                    EWT - {{$dvs->deduction2}}% 
                                </td>
                                <td>
                                    {{$dvs->deduction_amount1}} <br>
                                    {{$dvs->deduction_amount2}}
                                </td>
                                <td>{{$dvs->overall_total_amount}}</td>
                                <td>{{$dvs->total_amount}}</td>
                                <td>
                                        {{ $dvs->user->lname .', '. $dvs->user->fname }}
                                    
                                </td>
                            </tr>
                        @endif
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
                <b><h5 class="modal-title" name ="route_no" id="exampleModalLabel">Create Disbursement V2</h5> </b>
            </div>
            <div class="modal_body">
            </div>
        </div>
    </div>
</div>


@include('modal')
@endsection

@section('js')
<script src="{{ asset('admin/js/select2.js?v=').date('His') }}"></script>

<script>

    @if($user == 1027)
        $(document).ready(function() {
            $('#dv2_btn').prop('disabled', false).hide();
        });
    @endif

    function obligateDv(route_no, dv_no, type){
    console.log('dv', type);
        $('.modal_body').html(loading);
        $('.modal-title').html("Disbursement Voucher");
        var url = "{{ url('dv').'/' }}"+route_no + '/'+dv_no +'/' + type;
        setTimeout(function(){
            $.ajax({
                url: url,
                type: 'GET',
                success: function(result) {
                    $('.modal_body').html(result);
                }
            });
        },1000);
    }

    function openModal() {
        var routeNoo = event.target.getAttribute('data-routeId');  
        setTimeout(function() {
            var src = "https://mis.cvchd7.com/dts/document/trackMaif/" + routeNoo;
            $("#trackIframe").attr("src", src);
            $("#iframeModal").css("display", "block");
        }, 100);
    }
    
    

</script>

@endsection