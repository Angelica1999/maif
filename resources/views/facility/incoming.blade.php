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
                    </div>
                </div>
            </form>
            <h1 class="card-title">TRANSMITTAL</h1>
            <p class="card-description">
                MAIF-IPP
            </p>
            @if(count($transmittal) > 0)
                <div class="table-responsive" id="details_table">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Control No</th>
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
                                    <td><a onclick="displaySum({{ $item->id }})" href="#summary_display" data-toggle="modal" data-backdrop="static">{{ $item->control_no }}</a></td>
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

@endsection
@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
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

    function sendTrans(id){
        $.get("{{ url('transmittal/send').'/'}}"+id, function(result){
            if(result == 'success'){
                Swal.fire({
                    icon: "success",
                    title: "Success!",
                    text: "Transmittal was sent!"
                });
            }
        });
    }
    
    
</script>
@endsection