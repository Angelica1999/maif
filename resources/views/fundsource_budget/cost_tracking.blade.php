@csrf
<table class="table table-list table-hover" id="budget_table">
    <thead style="position: sticky; top: 0; background-color: white; z-index: 1;">
        <tr style="text-align:center;">
            <th class="budget_th" style="border:1px solid black; vertical-align:middle; background-color:#CEAB60" colspan="12">BREAKDOWN OF CHARGES</th>
        </tr>
        <tr style="text-align:center; background-color:#F5F5F5">
            <th class="budget_th" style="min-width:100px; border:1px solid black; vertical-align:middle">SAA #</th>
            <th class="budget_th" style="min-width:200px; border:1px solid black; vertical-align:middle">PROPONENT</th>
            <th class="budget_th" style="max-width:200px; border:1px solid black; vertical-align:middle">DATE OF OBLIGATION</th>
            <th class="budget_th" style="min-width:100px; border:1px solid black; vertical-align:middle">DV #</th>
            <th class="budget_th" style="min-width:200px; border:1px solid black; vertical-align:middle">PAYEE</th>
            <th class="budget_th" style="min-width:200px; border:1px solid black; vertical-align:middle">RECIPIENT FACILITY</th>
            <th class="budget_th" style="min-width:100px; border:1px solid black; vertical-align:middle">ORS #</th>
            <th class="budget_th" style="max-width:130px; border:1px solid black; vertical-align:middle">MAIFIPP SUBSIDY/ FINANCIAL ASSISTANCE UACS EXPENSE</th>
            <th class="budget_th" style="max-width:130px; border:1px solid black; vertical-align:middle">MAIFIPP SUBSIDY/ FINANCIAL ASSISTANCE AMOUNT</th>
            <th class="budget_th" style="max-width:90px; border:1px solid black; vertical-align:middle">MAIFIPP ADMIN COST UACS EXPENSE</th>
            <th class="budget_th" style="max-width:90px; border:1px solid black; vertical-align:middle">MAIFIPP ADMIN COST AMOUNT</th>
            <th class="budget_th" style="border:1px solid black; vertical-align:middle"></th>
        </tr>
    </thead>
    <tbody id="">
        @if(isset($saa))
            @foreach($saa->a_cost as $row)
                <tr style="text-align:center;">
                    <td class="budget_td" style="max-width:200px; border:1px solid gray; vertical-align:middle">{{ $saa->saa }}</td>
                    <td class="budget_td" style="max-width:190px; border:1px solid gray; vertical-align:middle">{{ $row->proponent }}</td>
                    <td class="budget_td" style="max-width:90px; border:1px solid gray; vertical-align:middle">
                        {{ date('F j, Y', strtotime($row->created_at)) }}
                    </td>
                    <td class="budget_td" style="max-width:60px; border:1px solid gray; vertical-align:middle">{{ $row->dv_no }}</td>
                    <td class="budget_td" style="max-width:150px; border:1px solid gray; vertical-align:middle">{{ $row->payee }}</td>
                    <td class="budget_td" style="max-width:150px; border:1px solid gray; vertical-align:middle">{{ $row->recipient }}</td>
                    <td class="budget_td" style="max-width:60px; border:1px solid gray; vertical-align:middle">{{ $row->ors_no }}</td>
                    <td class="budget_td" style="max-width:130px; border:1px solid gray; vertical-align:middle"></td>
                    <td class="budget_td" style="max-width:130px; border:1px solid gray; vertical-align:middle"></td>
                    <td class="budget_td" style="max-width:90px; border:1px solid gray; vertical-align:middle">{{ $row->admin_uacs }}</td>
                    <td class="budget_td" style="max-width:90px; border:1px solid black; vertical-align:middle">{{ number_format($row->admin_cost,2,'.',',') }}</td>
                    <td style="border:1px solid black; vertical-align:middle"></td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
                    

