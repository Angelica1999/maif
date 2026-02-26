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
    .select2-container--default .select2-selection--single {
        border: none !important;
        box-shadow: none !important;
        background-color: transparent;
    }

    .select2-container--default .select2-selection--single:focus {
        outline: none !important;
    }

    .select2-dropdown {
        border: none !important;
        box-shadow: none !important;
    }
</style>
<div class="container-fluid col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <form method="GET" action="">
                <div class="input-group float-right w-50" style="min-width: 600px;">
                    <input type="text" class="form-control" name="keyword" placeholder="Control No." value="{{ $keyword }}">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit"><img src="\maif\public\images\icons8_search_16.png">Search</button>
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll"><img src="\maif\public\images\icons8_eye_16.png">View All</button>
                        <button type="button" href="#logbook" data-backdrop="static" data-toggle="modal" class="btn btn-success btn-md"><img src="\maif\public\images\icons8_create_16.png">Receive</button>
                    </div>
                </div>
                <button id="filter_btn" name="facility_data[]" class="btn btn-sm btn-info" type="submit" style="display:none;"></button>
                <button id="status_btn" name="status_data[]" class="btn btn-sm btn-info" type="submit" style="display:none;"></button>
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
                                <th style="min-width:120px;">Control No @sortablelink('control', '⇅')</th>
                                <th style="min-width:250px;">
                                    <select id="facility_filter" class="select2" style="width: 200px; border: none; background: transparent; display:none" multiple>
                                        <option value="">Facility</option>
                                        @foreach($facilities as $facility)
                                            <option value="{{ $facility->id }}" {{ in_array((int) $facility->id, array_map('intval', $facs)) ? 'selected' : '' }}>{{ $facility->name }}</option>
                                        @endforeach
                                    </select>
                                    @sortablelink('name', '⇅')
                                </th>
                                <th style="min-width:150px;">
                                    <select id="status_filter" class="select2" style="width: 100px; border: none; background: transparent; display:none" multiple>
                                        <option value="">Status</option>
                                        <option value="1" {{ in_array(1, array_map('intval', $status)) ? 'selected' : '' }}>In transit to MPU</option>
                                        <option value="2" {{ in_array(2, array_map('intval', $status)) ? 'selected' : '' }}>Received by MPU</option>
                                    </select> 
                                    @sortablelink('remarks', '⇅')
                                </th>
                                <th style="min-width:160px;">Prepared Date @sortablelink('prepared', '⇅')</th>
                                <th style="min-width:150px;">Total Amount @sortablelink('total', '⇅')</th>
                                <th style="min-width:120px;">Created On @sortablelink('on', '⇅')</th>
                                <th style="min-width:120px;">Created By @sortablelink('by', '⇅')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transmittal  as $item)
                                <tr>
                                    <td>
                                        @if($item->remarks == 2)
                                            <button onclick="returnTrans({{ $item->id }})" href="#return" style="color:white; width:90px" data-toggle="modal" data-backdrop="static" type="button" class="btn btn-warning btn-sm">
                                                <i class="fa fa-undo"></i> Return
                                            </button>
                                            <button onclick="accept({{ $item->id }})" style="color:white;  width:90px" type="button" class="btn btn-success btn-sm">
                                                <i class="fa fa-check"></i> Accept
                                            </button>
                                        @else
                                            <a onclick="receive('{{ $item->id }}')" style="color:white; width:90px" type="button" class="btn btn-info btn-sm">
                                                <i class="fa fa-inbox"></i> Receive
                                            </a>
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
                                    <td>{{ ucwords(strtolower($item->user->fname .' '.$item->user->lname)) }}</td>
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
            <div class="pl-5 pr-5 mt-5 alert alert-info" role="alert" style="width: 100%; margin-top:5px;">
                <strong>Total # of Patients: {{  number_format($patients, 2,'.',',') }}</strong>
                <strong style="margin-left: 20px;">|</strong>
                <strong style="margin-left: 20px;">Total No. of transmittals: {{  number_format($total, 2,'.',',') }}</strong>
                <strong style="margin-left: 20px;">|</strong>
                <strong style="margin-left: 20px;">Total Amount: {{  number_format($amount, 2,'.',',') }}</strong>
            </div>
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
    $('#logbook, #summary_display, #return, #trans_tracking').on('hide.bs.modal', function () {
        $(this).find('input, select, textarea, button').blur();
    });
    $(document).ready(function() {
        $('.fa-sort').hide();
        $('#facility_filter').select2({
            placeholder: 'Facility',
            allowClear: true,
            width: 'resolve',
            dropdownAutoWidth: true
        });

        $('#facility_filter').next('.select2').find('.select2-selection').css({
            'border': 'none',
            'background': 'transparent',
            'height': 'auto',
            'min-height': '0',
            'padding': '0px'
        });
        $('#facility_filter').on('change', function() {
            $('#filter_btn').val(JSON.stringify($(this).val()));
            $('#filter_btn').click();
        });

        //
        $('#status_filter').select2({
            placeholder: 'Status',
            allowClear: true,
            width: 'resolve',
            dropdownAutoWidth: true
        });

        $('#status_filter').next('.select2').find('.select2-selection').css({
            'border': 'none',
            'background': 'transparent',
            'height': 'auto',
            'min-height': '0',
            'padding': '0px'
        });
        $('#status_filter').on('change', function() {
            $('#status_btn').val(JSON.stringify($(this).val()));
            $('#status_btn').click();
        });
    });
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