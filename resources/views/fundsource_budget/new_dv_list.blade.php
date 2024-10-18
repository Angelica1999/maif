<?php
use App\Models\TrackingMaster;
use App\Models\TrackingDetails; 
?>
@extends('layouts.app')
@section('content')
<div class="container-fluid col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <form method="GET" action="">
                <div class="input-group float-right w-50" style="min-width: 600px;">
                    <input type="text" class="form-control" name="keyword" placeholder="Facility/Route No" value="{{ $keyword }}">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit"><img src="\maif\public\images\icons8_search_16.png">Search</button>
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll"><img src="\maif\public\images\icons8_eye_16.png">View All</button>
                        <button type="button" id="release_btn" data-target="#releaseTo" style="display:none; background:teal; color:white" onclick="putRoutes($(this))" data-target="#releaseTo" data-backdrop="static" data-toggle="modal" class="btn btn-md">Release All</button>
                    </div>
                </div>
            </form>
            <h4 class="card-title">Pre - DVs (v2)</h4>
            <p class="card-description">
                MAIF-IPP
            </p>
            <div class="table-responsive">
            @if(count($results) > 0)
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="padding:5px; min-width:100px"></th>
                            <th style="min-width:100px">Route</th>
                            <th style="min-width:300px">Facility</th>
                            <th>Proponent</th>
                            <th>Grand Total</th>
                            <th style="min-width:200px">Created By</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($results as $index => $row)
                        <tr>
                            <td class="td" style="padding:5px;text-align:center">
                                <button type="button" class="btn btn-xs" style="background-color:#165A54;color:white;width:50px; border-radius:0px" data-toggle="modal" href="#iframeModal" data-routeId="{{$row->new_dv->route_no}}" id="track_load" onclick="openModal()">Track</button>
                                <a href="{{ route('new_dv.pdf', ['id' => $row->id]) }}" style="background-color:green;color:white; width:50px; border-radius:0px; margin-top:1px" target="_blank" type="button" class="btn btn-xs">Print</a>
                            </td>
                            <td><a data-toggle="modal" data-backdrop="static" href="#view_v2" onclick="viewV1({{ $row->id }})">{{ $row->new_dv->route_no }}</a></td>
                            <td class="td">{{ $row->facility->name }}</td>
                            <td class="td">
                                @foreach($row->extension as $index => $data)
                                    {{$data->proponent->proponent}}
                                    @if($index + 1 % 2 == 0)
                                    <br>
                                    @endif
                                    @if($index < count($row->extension) - 1)
                                        ,
                                    @endif
                                @endforeach
                            </td>
                            <td class="td">{{ number_format($row->grand_total,2,'.',',') }}</td>
                            <td class="td">{{$row->user->lname .', '.$row->user->fname}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <div class="alert alert-danger" role="alert" style="width: 100%;">
                    <i class="typcn typcn-times menu-icon"></i>
                    <strong>No data found!</strong>
                </div>
            @endif
            </div>
            <div class="pl-5 pr-5 mt-5">
                {!! $results->appends(request()->query())->links('pagination::bootstrap-5') !!}
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
            <div class="pre_body" style="display: flex; flex-direction: column; align-items: center; padding:15px">

            </div>
        </div>
    </div>
</div>

@include('modal')
@endsection
@section('js')
<script>
    $('.filter-division').select2();
    $('.filter-section').select2();

    var doc_type = @json($type);
    
    function viewV1(id) {
        $('.pre_body').empty();
        $.get("{{ url('pre-dv/budget/v2/').'/' }}" + doc_type + '/' + id, function(result) {
            $('.pre_body').append(result);
        });
    }

    function openModal() {
        var routeNoo = event.target.getAttribute('data-routeId'); 
        var src = "https://mis.cvchd7.com/dts/document/trackMaif/" + routeNoo;
        // $('.modal-body').html(loading);
        setTimeout(function() {
            $("#trackIframe").attr("src", src);
            $("#iframeModal").css("display", "block");
        }, 150);
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

    var s_ident = 0; 

     //select_all
    $('.select_all').on('click', function(){
        if(s_ident == 0){
            document.getElementById('release_btn').style.display = 'inline-block';
            $('.group-releaseDv').prop('checked', true);
            $('.group-releaseDv').trigger('change');
            s_ident = 1; 
        }else{
            document.getElementById('release_btn').style.display = 'none';
            $('.group-releaseDv').prop('checked', false);
            $('.group-releaseDv').trigger('change');
            s_ident = 0; 
        }
    });

</script>
@endsection