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
    #search_patient {
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
        flex-wrap: nowrap;
        justify-content: flex-end; 
        
    }

.input-group .form-control {
    width: 200px;   
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
                    <h4 class="card-title">TRANSMITTAL : INCOMING</h4>
                    <p class="card-description">MAIF-IPP</p>
                </div>
            <form method="GET" action="">
                <div class="input-group ">
                    <input type="text" class="form-control" name="keyword" placeholder="ENTER .." value="">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit"><img src="\maif\public\images\icons8_search_16.png">Search</button>
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll"><img src="\maif\public\images\icons8_eye_16.png">View All</button>
                        <button type="button" href="#logbook" data-backdrop="static" data-toggle="modal" class="btn btn-success btn-md"><img src="\maif\public\images\icons8_create_16.png">Receive</button>
                    </div>
                </div>
            </form>
</div>
            @if(count($transmittal) > 0)
                <div class="table-responsive" id="details_table" style="margin-top:20px">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width:150px"></th>
                                <th>Control No</th>
                                <th>Facility</th>
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
                                            <!-- <i class="text-danger">this transmittal is not yet received</i> -->
                                            <a onclick="receive('{{ $item->id }}')" style="border-radius:0; color:white" type="button" class="btn btn-success btn-xs">Receive</a>
                                        @endif
                                    </td>
                                    <td><a onclick="displaySum({{ $item->id }}, {{ $item->remarks }})" href="#summary_display" data-toggle="modal" data-backdrop="static">{{ $item->control_no }}</a></td>
                                    <td>{{ $item->user->facility->name }}</td>
                                    <td>
                                         {!!  $item->remarks == 1 ? 'In transit to MPU': ($item->remarks == 2 ? 'Received by MPU' : 
                                            ($item->remarks == 3 ? 'Returned by MPU' :
                                            ($item->remarks == 5 ? 'Accepted' : 
                                            ($item->remarks == 6 ? 'DV created' : 
                                            ($item->remarks == 7 ? 'Obligated' : 
                                            ($item->remarks == 8 ? 'Paid' : '')))) ))
                                        !!}
                                    </td>
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
                <button type="button" href="#return" data-toggle="modal" data-backdrop="static" style="border: none; padding: 8px 15px; cursor: pointer; float:right; color:white; display:none" onclick="returnTrans(0)" class="btn-warning sum_return">RETURN</button>
                <button type="button" style="border: none; padding: 8px 15px; cursor: pointer; float:right; display:none" onclick="accept(0)" class="btn-success sum_accept">ACCEPT</button>
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
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="fname">Delivered By</label>
                                    <input type="text" class="form-control" style="width:100%;" name="delivered_by" oninput="this.value = this.value.toUpperCase()" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="close_modal" data-dismiss="modal">Close</button>
                        <button type="submit" id="create_pat_btn" class="btn btn-success">Receive</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
@section('js')
<script src="{{ asset('admin/vendors/sweetalert2/sweetalert2.js?v=1') }}"></script>
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

    function receive(cntrl_no){
        Swal.fire({
            title: 'Receive transmittal',
            input: 'text', 
            inputLabel: 'Delivered By:',
            inputPlaceholder: 'Delivery name',
            showCancelButton: true,
            confirmButtonText: 'Submit',
            cancelButtonText: 'Cancel',
            inputValidator: (value) => {
                if (!value) {
                    return 'Please enter the delivery name!';
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                var name = encodeURIComponent(result.value); 
                var control_no = encodeURIComponent(cntrl_no);

                fetch(`transmittal/received/${control_no}/${name}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    Swal.fire('Success!', 'Your data has been submitted.', 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                })
                .catch(error => {
                    Swal.fire('Error!', 'There was an error submitting your data.', 'error');
                });
            }
        });
    }

    function accept(id){
        if(id == 0){
            id= trans_id;
        }
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
        if(id == 0){
            id= trans_id;
        }
        $('.id').val(id);
        $('.return_body').html(loading);
        $.get("{{ url('transmittal/references/1').'/' }}" + id, function(result){
            $('.return_body').html(result);
        });
    }

    var trans_id = 0;
    
    function displaySum(id, remarks){
        trans_id = id;
        $('.summary_body').html(loading);
        $.get("{{ url('transmittal').'/' }}" + id, function(result){
            $('.summary_body').html(result);
            $('#sum_footer').css('display', 'none');
            if(remarks != 2){
                $('.sum_return').css('display', 'none');
                $('.sum_accept').css('display', 'none');
            }else{
                $('.sum_return').css('display', 'block');
                $('.sum_accept').css('display', 'block');
            }
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