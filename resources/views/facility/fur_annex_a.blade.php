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

    .table thead {
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .table th {
        background: gray;
        color: white;
        font-weight: 600;
        padding: 12px 8px;
        text-align: center;
        border: 1px solid black;
        font-size: 11px;
        line-height: 1.3;
        vertical-align: middle;
        position: relative;
    }

    .table td {
        border: 1px solid black;
    }

    .table thead tr:first-child th {
        height: 100px;
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
</style>
<div class="container-fluid col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <div class="card-body d-flex justify-content-end" style="padding:0px;">
                <form method="GET" action="">
                    <input type="hidden" name="tab_type" value="1">
                    <input type="hidden" name="id" value="{{ $id }}">
                    <div class="input-group">
                        <input type="text" id="yearPicker" name="year" value="{{ $year }}" readonly disabled>
                        <a href="{{ url('fur/annexA/excel/'.$id.'/'.$year, [], request()->isSecure()) }}"
   class="btn"
   style="background-color: teal; border-radius: 0px; color: white;">
   EXCEL
</a>
                        <!-- <a href="{{ route('annex_a.excel', ['id' => $id, 'year'=>$year]) }}" type="submit" value="excel" name="excel" class="btn" style="background-color: teal; border-radius: 0px; color: white;">EXCEL</a> -->
                    </div>
                </form>
            </div>
            <div id="patient_table_container" class="table-responsive" style="overflow-x: auto; scroll-behavior: smooth; flex-grow: 1; margin-top:10px">
                <table class="table" id="patient_table">
                    <thead>
                        <tr>
                            <th width="15%">SAA No. and Date of Issuance of SAA</th>
                            <th width="14%">Amount of SAA</th>
                            <th width="14%">Total Fund Allocation</th>
                            <th width="13%">Month Utilized</th>
                            <th width="14%">Total Number of Patients Served</th>
                            <th width="16%">Total Actual Approved Assistance through MAIPP (Utilized Amount)</th>
                            <th width="14%">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['monthly'] as $index => $item)
                            <tr>
                                <td></td>
                                <td></td>
                                @if($index == 0)
                                    <td rowspan="12"></td>
                                @endif
                                <td>{{ $item['month'] }}</td>
                                <td style="text-align:right">{{ $item['patients'] }}</td>
                                <td style="text-align:right">{{ number_format($item['total'],2,'.',',') }}</td>
                                <td></td>
                            </tr>        
                        @endforeach
                            <tr style="font-weight:bold;">
                                <td style="font-size:20px">TOTAL</td>
                                <td style="text-align:right; font-size:20px">-</td>
                                <td style="text-align:right; font-size:20px">-</td>
                                <td></td>
                                <td style="text-align:right; font-size:20px">{{ $data['overall']['patients'] }}</td>
                                <td style="text-align:right; font-size:20px">{{ number_format($data['overall']['total'],2,'.',',') }}</td>
                                <td></td>
                            </tr> 
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>