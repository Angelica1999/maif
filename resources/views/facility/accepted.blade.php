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
            <h1 class="card-title">TRANSMITTAL : ACCEPTED</h1>
            <p class="card-description">
                MAIF-IPP
            </p>
            @if(count($transmittal) > 0)
                <div class="table-responsive" id="details_table">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Control No</th>
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
                                        <button onclick="disRem({{ $item->id }})" class="btn btn-sm btn-success" style="border-radius:0px" data-toggle="modal" href="#trans_remarks">Remarks</button>
                                    </td>
                                    <td><a onclick="displaySum({{ $item->id }})" href="#summary_display" data-toggle="modal" data-backdrop="static">{{ $item->control_no }}</a></td>
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
            @error('trans_files.*')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
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

<div class="modal fade" id="trans_remarks" role="dialog" style="overflow-y:scroll;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="border-radius:0px">
            <div class="modal-header" style="text-align:center">
                <h3 class="text-success modal-title"><i style="font-size:20px" class="typcn typcn-printer menu-icon"></i>TRANSMITTAL REMARKS</h3>
            </div>
            <form action="{{ route('accepted.remarks') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="rem_id" class="rem_id">
                <div class="trans_rem">
                    <div class="" style="display: flex; align-items: center; padding: 10px;">
                        <h3 style="width: 10%;">Link:</h3>
                        <textarea style="width: 90%;" class="form-control" name="trans_link"></textarea>
                    </div>
                    <div class="" style="display: flex; align-items: center; padding: 10px;">
                        <h3 style="width: 10%;">Files:</h3>
                        <!-- <input type="file" name="trans_files[]" multiple> -->
                        <input style="width:90%" class="form-control" id="file-upload" type="file" name="trans_files[]" multiple>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" style="border: none; padding: 8px 15px; cursor: pointer; float:right" class="btn-success">Submit</button>
                </div>
            </form>
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
        $('.summary_body').html(loading);
        $.get("{{ url('transmittal').'/' }}" + id, function(result) {
            $('.summary_body').html(result);
            $('.sum_gen').css('display', 'none');
        });
    }

    function disRem(id){
        $('.rem_id').val(id);
    }

</script>
@endsection