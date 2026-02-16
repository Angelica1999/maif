<!DOCTYPE html>
<html>
<head>
    <title>Track Details - {{ $proponent->proponent }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        .header {
            background: #6c757d;
            color: white;
            padding: 12px 20px;
            margin-bottom: 20px;
            font-size: 18px;
            font-weight: bold;
        }
        .container {
            background: white;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        th, td {
            border: 1px solid #dee2e6;
            padding: 10px;
            text-align: left;
        }
        th {
            background: #e9ecef;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
        }
        td {
            background: white;
        }
        td:not(:first-child) {
            text-align: right;
        }
        .total-row {
            background: #d9edf7 !important;
            font-weight: bold;
        }
        .facility-list {
            line-height: 1.6;
        }
        .no-data {
            padding: 40px;
            text-align: center;
            color: #6c757d;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .container {
                box-shadow: none;
            }
        }
    </style>
</head>
<body>

@if(count($data) > 0)
    <div class="container">
        <h2>{{ $proponent->proponent }}</h2>

        <table>
            <thead>
                <tr>
                    <th>Facility</th>
                    <th>Allocated Funds</th>
                    <th>Admin Cost</th>
                    <th>Usage</th>
                    <th>Remaining</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $row)
                    @php
                        // Calculate allocated funds
                        $allocated = $row['allocated'];
                        if(in_array('702', $row['facility_ids'])) {
                            $allocated += $supplemental - $subtracted;
                        }
                        
                        // Calculate usage
                        $usage = $row['util'] + $row['patient_amount'];
                        if(in_array('702', $row['facility_ids'])) {
                            $usage += $rem_patients + $for_cvchd;
                        }
                        
                        // Calculate remaining
                        $remaining = $allocated - $row['admin_cost'] - $usage;
                    @endphp
                    <tr>
                        <td>
                            <div class="facility-list">
                                @foreach($row['facilities'] as $facility)
                                    {{ $facility }}<br>
                                @endforeach
                            </div>
                        </td>
                        <td>{{ number_format($allocated, 2, '.', ',') }}</td>
                        <td>{{ number_format($row['admin_cost'], 2, '.', ',') }}</td>
                        <td>{{ number_format($usage, 2, '.', ',') }}</td>
                        <td>{{ number_format($remaining, 2, '.', ',') }}</td>
                    </tr>
                @endforeach

                <tr class="total-row">
                    <td>TOTAL :</td>
                    <td>{{ number_format($total_allocated, 2, '.', ',') }}</td>
                    <td>{{ number_format($total_admin_cost, 2, '.', ',') }}</td>
                    <td>{{ number_format($total_usage, 2, '.', ',') }}</td>
                    <td>{{ number_format($remaining, 2, '.', ',') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
@else
    <div class="container">
        <p class="no-data">No data available.</p>
    </div>
@endif

</body>
</html>