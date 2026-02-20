@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

<style>
    .datepicker {
        font-size: 13px;
    }
    
    .datepicker table tr td span {
        height: 40px;
        line-height: 40px;
    }

    /* Custom scrollbar */
    #patient_table_container::-webkit-scrollbar {
        width: 10px;
        height: 10px;
    }

    #patient_table_container::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    #patient_table_container::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 5px;
    }

    #patient_table_container::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }

    .table th {
        background: gray;
        color: white;
        font-weight: 600;
        padding: 12px 8px;
        text-align: center;
        border: 1px solid gray;
        font-size: 11px;
        line-height: 1.3;
        vertical-align: middle;
        position: relative;
    }

    .table thead tr:first-child th {
        vertical-align:middle;
    }
    .table tbody tr:hover td {
        background-color: #f2f2f2;   
    }

    .table tbody tr:nth-child(even) td {
        background-color: #f7f7f7;   
    }

    .table tbody tr:nth-child(even):hover td {
        background-color: #eaeaea;   
    }
    .btn-excel {
        background-color: #1D6F42;
        color: #fff;
    }
    .year:focus {
        border-color: green !important;
        box-shadow: 0 0 3px green;
    }
    .select2-container--default .select2-selection--single {
        border: 1px solid #009DD1;
        height: 42px;
    }
    .select2-container--default .select2-selection--single:focus,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #00B4D8 !important;
        box-shadow: 0 0 3px #00B4D8;
    }

</style>
<div class="container-fluid col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="card-title">ANNEX B SUBMISSION</h1>
                    <p class="card-description">MAIF-IPP</p>
                </div>
                <form method="GET" action="">
                    <div class="input-group">
                        <input class="form-control year" style="border-color:green" type="text" placeholder="Year" id="yearPicker" name="year" value="{{ $year }}">
                        <select class="form-control" id="type" name="type">
                            <option value=""></option>
                            <option value="1" {{ $type == 1 ? 'selected' :'' }}>Incoming</option>
                            <option value="2" {{ $type == 2 ? 'selected' :'' }}>Accepted</option>
                            <option value="3" {{ $type == 3 ? 'selected' :'' }}>Returned</option>
                        </select>
                        <button class="btn btn-warning" name="viewAll" value="viewAll" style="border-radius:0px"><i class="fa fa-eye"></i> ViewAll</button>
                    </div>
                </form>
            </div>
            <div class="table-responsive" id ="patient_table_container">
                <table class="table table-striped" id="patient_table">
                    <thead>
                        <tr>
                            <th width="19%">Action</th>
                            <th width="21%">Facility</th>
                            <th width="10%">Month Year</th>
                            <th width="12%">Total Number of Patients Served</th>
                            <th width="16%">Total Actual Approved Assistance through MAIPP (Utilized Amount)</th>
                            <th width="10%">Status</th>
                            <th width="12%">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($data) > 0)
                            @foreach($data as $row)
                                <tr>
                                    <td>
                                        <a href="{{ route('fur.submission', [
                                                'month' => $row->month,
                                                'year' => $row->year,
                                                'facility_id' => $row->facility_id
                                            ]) }}"
                                            class="btn btn-xs btn-info"
                                            style="color:white; font-size:11px;">
                                            <i class="fa fa-eye"></i> View
                                        </a>
                                        <a href="{{
                                            route('annex_b.excel', [
                                                    'id' => $row->facility_id,
                                                    'month' => $row->month,
                                                    'year' => $row->year
                                                ])
                                            }}"
                                            class="btn btn-xs btn-excel"
                                            style="color:white; font-size:11px; display:inline-block;">
                                            <i class="fa fa-file-excel"></i> Excel
                                        </a>
                                        @if($row->status == 1)
                                            <button data-month="{{ $row->month }}" data-year="{{ $row->year }}" data-id="{{ $row->facility_id }}"
                                                class="btn btn-xs btn-success accept" style="color:white; font-size:11px; display:inline-block;">
                                                <i class="fa fa-thumbs-up"></i>  Accept
                                            </button>
                                        @elseif($row->status == 2)
                                            <button data-month="{{ $row->month }}" data-year="{{ $row->year }}" data-id="{{ $row->facility_id }}"
                                                class="btn btn-xs btn-danger return" style="color:white; font-size:11px; display:inline-block;">
                                                <i class="fa fa-undo"></i>  Return
                                            </button>
                                        @endif
                                    </td>
                                    <td style="text-align:left">{{ $row->name }}</td>
                                    <!-- 0-pending/1-submitted/2-accepted/3-returned -->
                                    <td>{{ \Carbon\Carbon::create()->month($row->month)->format('F'). ' '.$row->year }}</td>
                                    <td style="text-align:right">{{ $row->patients }}</td>
                                    <td style="text-align:right">{{ number_format($row->total, 2,'.',',') }}</td>
                                    <td style="text-align:center">{{ $row->status == 1 ? 'Pending' : ($row->status == 2 ? 'Submitted' : 'Returned') }}</td>
                                    <td>{{ $row->remarks }}</td>
                                </tr> 
                            @endforeach       
                        @else
                            <tr><td style="color:#850000; background-color:#ffcccc; border-color:#ffb8b8;" colspan="7">No data Found</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script>
    $('#type').select2({
        placeholder: 'All'
    }).on('change', function(){
        this.form.submit();
    });
    
    $('#yearPicker').datepicker({
        format: "yyyy",
        viewMode: "years", 
        minViewMode: "years",
        autoclose: true,
        orientation: "bottom auto"
    }).on('changeDate', function(e) {
        this.form.submit();
    });

    $('.accept').on('click', function(){
        var id = $(this).attr('data-id');
        var month = $(this).attr('data-month');
        var year = $(this).attr('data-year');
        Swal.fire({
            icon: 'warning',
            title: 'Accept Annex B',
            text: 'Please ensure all details have been reviewed before accepting.',
            showCancelButton: true,
            confirmButtonText: 'Accept',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                type: 'POST',
                url: '{{ route("accept.fur") }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    month: month,
                    year: year
                },
                success: function (response) {
                    Lobibox.notify('success', {
                        msg: "Annex B has been successfully accepted and will be included in the consolidated Annex B!",
                    });
                    location.reload();
                },
                error: function (error) {
                    if (error.status) {
                        console.error('Status Code:', error.status);
                    }

                    if (error.responseJSON) {
                        console.error('Response JSON:', error.responseJSON);
                    }
                    $('.loading-container').css('display', 'none');
                }
            });

            }
        });
    });

    $('.return').on('click', function(){
        var id = $(this).attr('data-id');
        var month = $(this).attr('data-month');
        var year = $(this).attr('data-year');
        Swal.fire({
            icon: 'warning',
            title: 'Return Annex B',
            input: 'text', 
            inputLabel: 'Remarks:',
            inputPlaceholder: '...',
            showCancelButton: true,
            confirmButtonText: 'Return',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545',
            inputValidator: (value) => {
                if (!value) {
                    return 'Please enter the reason for returning!';
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                var remarks = result.value; 
                $.ajax({
                type: 'POST',
                url: '{{ route("return.fur") }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    remarks: remarks,
                    id: id,
                    month: month,
                    year: year
                },
                success: function (response) {
                    Lobibox.notify('success', {
                        msg: "Annex B was successfully returned to facility!",
                    });
                    location.reload();
                },
                error: function (error) {
                    if (error.status) {
                        console.error('Status Code:', error.status);
                    }

                    if (error.responseJSON) {
                        console.error('Response JSON:', error.responseJSON);
                    }
                    $('.loading-container').css('display', 'none');
                }
            });

            }
        });
    });
</script>
    
@endsection