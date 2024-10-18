@extends('layouts.app')
@section('content')
<style>
   .table th {
        position: sticky; 
        top: 0; 
        z-index: 2; 
        background-color: #fff; 
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4); 
    }
    #details_table {
        max-height: 500px; 
        overflow-y: auto; 
    }
</style>
<div class="container-fluid col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <form method="GET" action="">
                <div class="input-group float-right w-50" style="min-width: 600px;">
                    <input type="text" class="form-control" name="keyword" placeholder="" value="">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit"><img src="\maif\public\images\icons8_search_16.png">Search</button>
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll"><img src="\maif\public\images\icons8_eye_16.png">View All</button>
                        <button type="button" href="#logbook" data-backdrop="static" data-toggle="modal" class="btn btn-success btn-md"><img src="\maif\public\images\icons8_create_16.png">Receive</button>
                    </div>
                </div>
            </form>
            <h1 class="card-title">TRANSMITTAL : INCOMING</h1>
            <p class="card-description">
                MAIF-IPP
            </p>
            @if(count($transmittal) > 0)
                <div class="table-responsive" id="details_table">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width:150px"></th>
                                <th>Control No</th>
                                <th>Status</th>
                                <th>Prepared Date</th>
                                <th>Total Amount</th>
                                <th>Created On</th>
                                <th>Created By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transmittal  as $item)
                                <tr>
                                    <td>
                                        @if($item->remarks == 2)
                                            <button onclick="returnTrans({{ $item->id }})" href="#return" style="border-radius:0; color:white" data-toggle="modal" data-backdrop="static" type="button" class="btn btn-warning btn-xs">Return</button>
                                            <button onclick="accept({{ $item->id }})" style="border-radius:0; color:white" type="button" class="btn btn-success btn-xs">Accept</button>
                                        @else
                                            <i class="text-danger">this transmittal is not yet received</i>
                                        @endif
                                    </td>
                                    <td><a onclick="displaySum({{ $item->id }})" href="#summary_display" data-toggle="modal" data-backdrop="static">{{ $item->control_no }}</a></td>
                                    <td></td>
                                    <td>{{ date('F j, Y', strtotime($item->prepared_date)) }}</td>
                                    <td>{{ number_format($item->total, 2, '.', ',') }}</td>
                                    <td>{{ date('F j, Y', strtotime($item->created_at)) }}</td>
                                    <td>{{ $item->user->fname .' '.$item->user->lname }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-danger" role="alert" style="width: 100%;">
                <i class="typcn typcn-times menu-icon"></i>
                    <strong>No bills found!</strong>
                </div>
            @endif
            <div class="pl-5 pr-5 mt-5" id ="pagination_links">
                {!! $transmittal->appends(request()->query())->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="summary_display" role="dialog" style="overflow-y:scroll;">
    <div class="modal-dialog modal-lg" role="document" style="width:900px">
        <div class="modal-content" style="border-radius:0px">
            <div class="modal-header" style="text-align:center">
                <h2 class="text-success modal-title">TRANSMITTALS</h2>
            </div>
            <div class="summary_body" style="display: flex; flex-direction: column; align-items: center;">
                
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" style="background-color: gray; color: white; border: none; padding: 8px 15px; cursor: pointer; float:right">CLOSE</button>
                <button type="button" style="border: none; padding: 8px 15px; cursor: pointer; float:right; color:white" class="btn-warning">RETURN</button>
                <button type="button" style="border: none; padding: 8px 15px; cursor: pointer; float:right" class="btn-success">ACCEPT</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="trans_tracking" role="dialog" style="overflow-y:scroll;">
    <div class="modal-dialog modal-lg" role="document" style="width:300px; height:500px">
        <div class="modal-content" style="border-radius:0px">
            <div class="modal-header" style="text-align:center">
                <h3 class="text-success modal-title"><i style="font-size:20px" class="typcn typcn-printer menu-icon"></i>TRANSMITTAL TRACKING</h3>
            </div>
            <div class="trans_tracking">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="return" role="dialog" style="overflow-y:scroll;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="border-radius:0px">
            <div class="modal-header" style="text-align:center">
                <h4 class="text-success modal-title"><i style="font-size:15px" class="typcn typcn-printer menu-icon"></i>RETURN REMARKS</h4>
            </div>
            <form method="POST" action="{{ route('transmittal.return') }}">
                <input type="hidden" class="id" name="id">
                @csrf
                <div class="return_body"></div>
                <div class="modal-footer">
                    <button type="submit" style="border: none; padding: 8px 15px; cursor: pointer; float:right" class="btn-warning">RETURN</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="logbook" tabindex="-1" role="dialog" aria-hidden="true" style="opacity:1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="text-success" id="title"><i style = "font-size:30px"class="typcn typcn-user-add-outline menu-icon"></i>ADD LOGS</h4><hr />
                @csrf
            </div>
            <div class="modal_body">
                <form id="log_form" method="POST" action="{{ route('logbook.save') }}">
                    <div class="modal-body">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fname">Control No</label>
                                    <select class="form-control control_no" style="width:220px;" name="control_no[]" placeholder="Select Control No" multiple required>
                                        @foreach($control_no as $item)
                                            <option value="{{ $item}}">{{ $item }}</option>
                                        @endforeach
                                    </select>                                
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lname">Received On</label>
                                    <input type="date" class="form-control" style="width:220px;" name="received_on" value="{{ now()->format('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fname">Delivered By</label>
                                    <input type="text" class="form-control" style="width:220px;" name="delivered_by" oninput="this.value = this.value.toUpperCase()" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fname">Received By</label>
                                    <input type="text" class="form-control" style="width:220px;" name="received_by" oninput="this.value = this.value.toUpperCase()" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="close_modal" data-dismiss="modal">Close</button>
                        <button type="submit" id="create_pat_btn" class="btn btn-success">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('admin/js/select2.js?v=').date('His') }}"></script>

<script>

    $('.control_no').select2({
        tags: true,
    });

    function addRow(id){
        $.get("{{ url('transmittal/references/2').'/' }}" + id, function(result){
            $('.con').append(result);
        });
    }

    function accept(id){
        $.get("{{ url('transmittal/accept').'/' }}" + id, function(result){
            if(result == 'success'){
                Swal.fire({
                    icon: "success",
                    title: "Success!",
                    text: "Transmittal was sent!",
                    timer: 1000,
                    showConfirmButton: false 
                }).then(() => {
                    location.reload(); 
                });
            }
        });
    }
    
    $(document).ready(function () {
        $(document).on('click', '.remove', function () {
            $(this).closest('.clone').remove();
        });
    });

    function returnTrans(id){
        $('.id').val(id);
        $.get("{{ url('transmittal/references/1').'/' }}" + id, function(result){
            $('.return_body').html(result);
        });
    }


    var trans_id = 0;
    
    function displaySum(id){
        trans_id = id;
        console.log('id', id);
        $.get("{{ url('transmittal').'/' }}" + id, function(result){
            $('.summary_body').html(result);
        });
    }

    function getPortrait(){
        window.open('print/portrait/' + trans_id , '_blank');
    }

    function getLandscape(){
        window.open('print/landscape/' + trans_id, '_blank');
    }
  
</script>
@endsection