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
    } #search_patient {
    width: 250px;   
    max-width: 100%;
}
.input-group {
        justify-content: flex-end;
        gap: 1px;
        flex-wrap: nowrap; 
    }
     .input-group-append {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end; 
        
    }
   
.input-group .form-control {
    width: 250px; 
    max-width: 100%;
}
    
     @media (max-width: 767px) {
    .input-group {
        flex-direction: column;     
        align-items: stretch;     
    }

    .input-group .form-control {
        width: 200%;
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
}
</style>
<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div class="mb-2 mb-md-0">
                    <h4 class="card-title">TRANSMITTAL: RETURNED</h4>
                    <p class="card-description">MAIF-IPP</p>
                </div>
            <form method="GET" action="">
                <div class="input-group">
                    <input type="text" class="form-control" name="keyword" placeholder="enter.." value="">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit"><img src="\maif\public\images\icons8_search_16.png">Search</button>
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll"><img src="\maif\public\images\icons8_eye_16.png">View All</button>
                    </div>
                </div>
            </form>
        </div>
            @if(count($transmittal) > 0)
                <div class="table-responsive" id="details_table">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Control No</th>
                                <th>Facility</th>
                                <th>Status</th>
                                <th>Prepared Date</th>
                                <th>Total Amount</th>
                                <th>Created On</th>
                                <th>Created By</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transmittal  as $item)
                                <tr>
                                    <td>
                                        @if($item->remarks != null)
                                            <button onclick="checkRemarks({{ $item->id }})" href="#return" style="border-radius:0; color:white" data-toggle="modal" data-backdrop="static" type="button" class="btn btn-info btn-xs">Remarks</button>
                                        @else
                                            <i class="text-danger">this transmittal is not yet received</i>
                                        @endif
                                    </td>
                                    <td><a onclick="displaySum({{ $item->id }})" href="#summary_display" data-toggle="modal" data-backdrop="static">{{ $item->control_no }}</a></td>
                                    <td>{{ $item->user->facility->name }}</td>
                                    <td></td>
                                    <td>{{ date('F j, Y', strtotime($item->prepared_date)) }}</td>
                                    <td>{{ number_format($item->total, 2, '.', ',') }}</td>
                                    <td>{{ date('F j, Y', strtotime($item->created_at)) }}</td>
                                    <td>{{ $item->user->fname .' '.$item->user->lname }}</td>
                                    <td>
                                        @if($item->status != 3 && $item->status != 1)
                                            <button style="border-radius:0px; color:white" onclick="sendTrans({{ $item->id }})" class="btn btn-sm btn-info">
                                                {{ $item->status == 1 ? 'Return to MPU' : 'Send to MPU' }}
                                            </button>
                                        @endif
                                    </td>
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
            <!-- <div class="modal-footer">
                <button type="button" data-dismiss="modal" style="background-color: gray; color: white; border: none; padding: 8px 15px; cursor: pointer; float:right">CLOSE</button>
                <button type="button" style="border: none; padding: 8px 15px; cursor: pointer; float:right; color:white" class="btn-warning">RETURN</button>
                <button type="button" style="border: none; padding: 8px 15px; cursor: pointer; float:right" class="btn-success">ACCEPT</button>
            </div> -->
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
                <h4 class="text-success modal-title"><i style="font-size:15px" class="typcn typcn-printer menu-icon"></i>RETURNED DETAILS</h4>
            </div>
            <div class="return_details"></div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" style="background-color: gray; color: white; border: none; padding: 8px 15px; cursor: pointer; float:right">CLOSE</button>
            </div>
        </div>
    </div>
</div>

@endsection
@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

    function checkRemarks(id){
        $('.return_details').empty();
        $.get("{{ url('returned/details').'/' }}" + id, function(result){
            $('.return_details').append(result);
        });
    }

    var trans_id = 0;
    
    function displaySum(id){
        trans_id = id;
        $('.summary_body').html(loading);
        $.get("{{ url('transmittal').'/' }}" + id, function(result){
            $('.summary_body').html(result);
        });
    }
    
    
</script>
@endsection