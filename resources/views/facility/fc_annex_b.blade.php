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
            <div class="card-body d-flex justify-content-end" style="padding:0px;">
                <form method="GET" action="">
                    <input type="hidden" name="tab_type" value="2">
                    <input type="hidden" name="id" value="{{ $id }}">
                    <div class="input-group">
                        <input class="form-control year" style="border-color:green" type="text" placeholder="Year" id="yearPicker" name="year" value="{{ $year }}">
                        <button class="btn btn-warning" name="viewAll" value="viewAll" style="border-radius:0px"><i class="fa fa-eye"></i> ViewAll</button>
                    </div>
                </form>
            </div>
            <div class="table-responsive" id ="patient_table_container" style="margin-top:10px">
                <table class="table table-striped" id="patient_table">
                    <thead>
                        <tr>
                            <th width="19%">Action</th>
                            <th width="10%">Month Year</th>
                            <th width="12%">Total Number of Patients Served</th>
                            <th width="16%">Total Actual Approved Assistance through MAIPP (Utilized Amount)</th>
                            <th width="10%">Status</th>
                            <th width="12%">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
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
                                        style="color:white; font-size:11px; display:inline-block; background-color:green">
                                        <i class="fa fa-file-excel"></i> Excel
                                    </a>
                                </td>
                                <!-- 0-pending/1-submitted/2-accepted/3-returned -->
                                <td>{{ \Carbon\Carbon::create()->month($row->month)->format('F'). ' '.$row->year }}</td>
                                <td style="text-align:right">{{ $row->patients }}</td>
                                <td style="text-align:right">{{ number_format($row->total, 2,'.',',') }}</td>
                                <td style="text-align:left">{{ $row->status == 1 ? 'For Evaluation' : ($row->status == 2 ? 'Submitted' : ($row->status == 3 ? 'Returned' : '')) }}</td>
                                <td style="text-align:left">{{ $row->remarks ?? ( $row->status == 2 ? 'To be reviewed' : ($row->status == 2 ? 'Accepted' : '')) }}</td>
                            </tr> 
                        @endforeach       
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>