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
            <form method="GET" action="{{ route('dv3') }}">
                <div class="input-group float-right w-50" style="min-width: 600px;">
                    <input type="text" class="form-control" name="keyword" placeholder="" value="">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit"><img src="\maif\public\images\icons8_search_16.png">Search</button> 
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll"><img src="\maif\public\images\icons8_eye_16.png">View All</button>
                        <button type="button" href="#create_dv3" onclick="createDv3()" data-backdrop="static" data-toggle="modal" class="btn btn-success btn-md"><img src="\maif\public\images\icons8_create_16.png">Create</button>
                        <button type="button" id="release_btn" data-target="#releaseTo" style="display:none; background:teal; color:white" onclick="putRoutes($(this))" data-target="#releaseTo" data-backdrop="static" data-toggle="modal" class="btn btn-md">Release All</button>

                    </div>
                </div>
                <input type="hidden" class="all_route" id="all_route" name="all_route">

            </form>
            <h4 class="card-title">Disbursement Voucher V2</h4>
            <p class="card-description">
                MAIF-IPP
            </p>
                <div class="table-responsive">
                    <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Tracking</th>
                            <th style="min-width:90px;">Route_No</th>
                            <th>Print</th>
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
                            <th>Facility</th>
                            <th style="min-width:150px;">SAA</th>
                            <th>Proponent</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Created On</th>
                            <th>Created By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($dv3) && $dv3->count() > 0)
                            @foreach($dv3 as $index=> $row)
                                <tr>
                                    <td>
                                        <button type="button" class="btn btn-xs col-sm-12" style="background-color:#165A54;color:white;" data-toggle="modal" href="#iframeModal" data-routeId="{{$row->route_no}}" id="track_load" onclick="openModal()">Track</button>
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
                                        <a data-dvId="{{$row->id}}" href="#create_dv3" onclick="updateDv3('{{$row->route_no}}')" style="background-color:teal;color:white;width:90px;" type="button" class="btn btn-xs" data-backdrop="static" data-toggle="modal">{{ $row->route_no }}</a>
                                        <button data-toggle="modal" data-target="#releaseTo" data-id="{{ $doc_id }}" data-route_no="{{ $row->route_no }}" onclick="putRoute($(this))" style="background-color:#1E90FF;color:white; width:90px;" type="button" class="btn btn-xs">Release To</button>
                                    </td>
                                    <td>
                                        <a href="{{ route('dv3.pdf', ['route_no' => $row->route_no]) }}" style="background-color:green;color:white; width:50px;" target="_blank" type="button" class="btn btn-xs">Print</a>
                                    </td>
                                    <td>Modified</td>
                                    <td>
                                        @if($row->status == 1)
                                            Forwarded
                                        @endif
                                    </td>
                                    <td style="text-align:center;" class="group-release" data-route_no="{{ $row->route_no }}" data-id="{{ $doc_id }}" >
                                        <input type="checkbox" style="width: 60px; height: 20px;" name="release_dv[]" id="releaseDvId_{{ $index }}" 
                                            class="group-releaseDv" >
                                    </td>
                                    <td>
                                        @if($row->remarks == 0)
                                            Pending
                                        @else
                                            Modify this
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
                            <div class="alert alert-danger" role="alert" style="width: 100%;">
                                <i class="typcn typcn-times menu-icon"></i>
                                <strong>No disbursement voucher version 3 found!</strong>
                            </div>
                        @endif
                    </tbody>
                    </table>
                </div>
           
            <div class="pl-5 pr-5 mt-5">
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

@endsection
@include('modal')
@section('js')
    <script>
        $('.filter-division').select2();
        $('.filter-section').select2();

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

            console.log('chakiii', ids);
            console.log('chakiii', routes);

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


