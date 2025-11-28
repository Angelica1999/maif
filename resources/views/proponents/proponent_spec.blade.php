@if(count($data)>0)
    <div class="table-container" style="padding:10px">
        <table class="table table-list table-hover table-striped" id="track_details">
            <thead style="position: sticky; top: 0; background-color: white; z-index: 1;">
                <tr style="text-align:left; background-color:gray; color:white">
                    <th colspan=5>{{ $proponent->proponent }}</th>
                </tr>
                <tr>
                    <th style="text-align:left;">FACILITY</th>
                    <th>ALLOCATED FUNDS</th>
                    <th>ADMIN COST</th>
                    <th>USAGE</th>
                    <th>REMAINING</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $row)
                    <tr id="">
                        <td style="text-align:margin-left;">
                            @foreach($row['facilities'] as $item)
                                {{ $item }}
                                <br>
                            @endforeach
                        </td>
                        <td style="text-align:margin-right;">
                            @if(in_array('702', $row['facility_ids']))
                                {{ number_format($row['allocated'] + $supplemental - $subtracted, 2,'.',',') }}
                            @else
                                {{ number_format($row['allocated'], 2,'.',',') }}
                            @endif
                        </td>
                        <td style="text-align:margin-right;">{{ number_format($row['admin_cost'], 2,'.',',') }}</td>
                        <td style="text-align:margin-right;">
                            @if(in_array('702', $row['facility_ids']))
                                {{ number_format($row['util'] + $row['patient_amount'] + $rem_patients + $for_cvchd, 2,'.',',') }}
                            @else
                                {{ number_format($row['util'] + $row['patient_amount'], 2,'.',',') }}
                            @endif
                        </td>
                        <td style="text-align:margin-right;">
                            @if(in_array('702', $row['facility_ids']))
                                {{ number_format(($row['allocated'] + $supplemental - $subtracted) - ($row['admin_cost'] + $row['util'] + $row['patient_amount'] + $rem_patients + $for_cvchd), 2,'.',',') }}
                            @else
                                {{ number_format($row['allocated'] - ($row['admin_cost'] + $row['util'] + $row['patient_amount']), 2,'.',',') }}
                            @endif
                        </td>
                    </tr>
                @endforeach
                <tr style="background-color:skyblue; font-weight:bold">
                    <td>TOTAL :</td>
                    <td style="text-align:margin-right;">{{ number_format($total_allocated, 2,'.',',') }}</td>
                    <td style="text-align:margin-right;">{{ number_format($total_admin_cost, 2,'.',',') }}</td>
                    <td style="text-align:margin-right;">{{ number_format($total_usage, 2,'.',',') }}</td>
                    <td style="text-align:margin-right;">{{ number_format($remaining, 2,'.',',') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
@else
    <div class="alert alert-danger" role="alert" style="width: 100%; padding:10px">
        <i class="typcn typcn-times menu-icon"></i>
        <strong>No data available found!</strong>
    </div>
@endif
<div class="modal-footer">
    <button style = "background-color:lightgray" class="btn btn-default" data-dismiss="modal"><i class="typcn typcn-times menu-icon"></i> Close</button>
</div>