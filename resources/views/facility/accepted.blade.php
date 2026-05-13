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
<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <div class="float-right">
                <div class="input-group">
                    <form method="GET" action="">
                        <div class="input-group" >
                            <input type="text" class="form-control" style="width:445px" name="keyword" placeholder="Control No." value="{{ $keyword }}">
                            <div class="input-group-append">
                                <button class="btn btn-sm btn-info" type="submit"><img src="\maif\public\images\icons8_search_16.png">Search</button>
                                <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll" style="width:95px;"><img src="\maif\public\images\icons8_eye_16.png">View All</button>
                            </div>
                        </div>
                        <div class="input-group">
                            <input type="text" title="Note: Data generated are based on the date when the transmittal was accepted, with records stored starting May 12, 2026."
                                style="text-align:center;" class="form-control" id="date_range" value="{{ $generated_dates }}" name="date_range" />
                            <button type="submit" id="gen_btn" name="generate_data" value="gen" style="background-color:teal; color:white; width:95px;font-size:11px" class="btn btn-xs"><i class="typcn typcn-calendar-outline menu-icon"></i>Generate</button>
                        </div>
                        <button id="filter_btn" name="facility_data[]" class="btn btn-sm btn-info" type="submit" style="display:none;"></button>
                        <button id="status_btn" name="status_data[]" class="btn btn-sm btn-info" type="submit" style="display:none;"></button>
                        <button id="dates_btn" name="selected_dates[]" class="btn btn-sm btn-info" type="submit" style="display:none;"></button>
                        <input type="hidden" id="generate" name="generate" value="{{ $generate }}"></input>
                    </form>
                </div>
            </div>
            <h1 class="card-title">TRANSMITTAL : ACCEPTED</h1>
            <p class="card-description">
                MAIF-IPP
            </p>
            <p><i class="text-info">Note: Data generated for date range are based on the date when the transmittal was accepted, with records stored starting May 12, 2026.</i></p>
            @if(count($transmittal) > 0)
                <div class="table-responsive" id="details_table">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="min-width:200px;"></th>
                                <th style="min-width:70px;">Remarks</th>
                                <th style="min-width:120px;">Control No @sortablelink('control', '⇅')</th>
                                <th style="min-width:150px;">
                                    <select id="status_filter" class="select2" style="width: 100px; border: none; background: transparent; display:none" multiple>
                                        <option value="">Status</option>
                                        <option value="5" {{ in_array(5, array_map('intval', $status)) ? 'selected' : '' }}>ACCEPTED</option>
                                        <option value="6" {{ in_array(6, array_map('intval', $status)) ? 'selected' : '' }}>DV CREATED</option>
                                        <option value="7" {{ in_array(7, array_map('intval', $status)) ? 'selected' : '' }}>OBLIGATED</option>
                                        <option value="8" {{ in_array(8, array_map('intval', $status)) ? 'selected' : '' }}>PAID</option>
                                    </select> 
                                    @sortablelink('remarks', '⇅')
                                </th>
                                <th style="min-width:250px;">
                                    <select id="facility_filter" class="select2" style="width: 200px; border: none; background: transparent; display:none" multiple>
                                        <option value="">Facility</option>
                                        @foreach($facilities as $facility)
                                            <option value="{{ $facility->id }}" {{ in_array((int) $facility->id, array_map('intval', $facs)) ? 'selected' : '' }}>{{ $facility->name }}</option>
                                        @endforeach
                                    </select>
                                    @sortablelink('name', '⇅')
                                </th>
                                <th style="min-width:250px;">
                                    <select id="dates_filter" class="select2" style="width: 200px; border: none; background: transparent; display:none" multiple>
                                        <option value="">Prepared Date</option>
                                        @foreach($dates as $row)
                                            <option value="{{ $row }}" {{ in_array( $row, $selected_dates) ? 'selected' : '' }}>{{ date('F j, Y', strtotime($row)) }}</option>
                                        @endforeach
                                    </select>
                                    @sortablelink('prepared', '⇅')
                                </th>
                                <th style="min-width:150px;">Total Amount @sortablelink('total', '⇅')</th>
                                <th style="min-width:120px;">Created On @sortablelink('on', '⇅')</th>
                                <th style="min-width:120px;">Created By @sortablelink('by', '⇅')</th>
                                <th style="min-width:120px;">Accepted By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transmittal  as $item)
                                <tr>
                                    <td>
                                        <button onclick="disRem({{ $item->id }})" class="btn btn-sm btn-success" data-toggle="modal" href="#trans_remarks">
                                            <i class="fa fa-upload"></i> Remarks
                                        </button>
                                        @if(!$item->used)
                                            <button onclick="returnTrans({{ $item->id }})" class="btn btn-sm btn-warning" title="Return to Incoming">
                                                <i class="fa fa-arrow-left"></i> RTI
                                            </button>
                                        @endif
                                    </td>
                                    <td>{{ $item->route_no }}</td>
                                    <td><a onclick="displaySum({{ $item->id }})" href="#summary_display" data-toggle="modal" data-backdrop="static">{{ $item->control_no }}</a></td>
                                    <td>
                                         {!!  $item->remarks == 1 ? 'In transit to MPU': ($item->remarks == 2 ? 'Received by MPU' : 
                                            ($item->remarks == 3 ? 'Returned by MPU' :
                                            ($item->remarks == 5 ? 'Accepted' : 
                                            ($item->remarks == 6 ? 'DV created' : 
                                            ($item->remarks == 7 ? 'Obligated' : 
                                            ($item->remarks == 8 ? 'Paid' : '')))) ))
                                        !!}
                                    </td>
                                    <td>{{ $item->user->facility->name }}</td>
                                    <td>{{ date('F j, Y', strtotime($item->prepared_date)) }}</td>
                                    <td>{{ number_format($item->total, 2, '.', ',') }}</td>
                                    <td>{{ date('F j, Y', strtotime($item->created_at)) }}</td>
                                    <td>{{ ucwords(strtolower($item->user->fname .' '.$item->user->lname)) }}</td>
                                    <td>{{ $item->accepted ? ucwords(strtolower($item->accepted->fname .' '.$item->accepted->lname)) : '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-danger" role="alert" style="width: 100%; margin-top:40px">
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
    $('#summary_display, #return, #trans_tracking, #trans_remarks').on('hide.bs.modal', function () {
        $(this).find('input, select, textarea, button').blur();
    });
    $('#date_range').daterangepicker({
        opens: 'left'
    });

    function returnTrans(id){
        Swal.fire({
            title: 'Return this Transmittal to Incoming List',
            text: "Are you sure you want to return this transmittal in the incoming list?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6', 
            cancelButtonColor: '#d33',   
            confirmButtonText: 'Yes',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`return-transmittal/${id}`, {
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
                    Swal.fire('Success!', 'This transmittal has been successfully returned to the incoming transmittal list.', 'success');
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
    $('#gen_btn').on('click', function(){
        $('#generate').val(1);
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

        //
        $('#dates_filter').select2({
            placeholder: 'Prepared Date',
            allowClear: true,
            width: 'resolve',
            dropdownAutoWidth: true
        });

        $('#dates_filter').next('.select2').find('.select2-selection').css({
            'border': 'none',
            'background': 'transparent',
            'height': 'auto',
            'min-height': '0',
            'padding': '0px'
        });
        $('#dates_filter').on('change', function() {
            $('#dates_btn').val(JSON.stringify($(this).val()));
            $('#dates_btn').click();
        });
    });

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