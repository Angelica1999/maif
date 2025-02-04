<table class="table table-list table-hover" id="budget_funds">
    <thead style="position: sticky; top: 0; background-color: white; z-index: 1;">
        <tr style="text-align:center;">
            <th class="budget_th" style="border:1px solid black; vertical-align:middle; background-color:#CEAB60" colspan="3">BREAKDOWN OF FUNDS</th>
            <th class="budget_th" style="border:1px solid black; vertical-align:middle; background-color:#C9C9C9" colspan="3">MAIFIPP</th>
            <th class="budget_th" style="border:1px solid black; vertical-align:middle; background-color:#9BC2E6" colspan="3">ADMIN COST</th>
        </tr>
        <tr style="text-align:center; background-color:#F5F5F5">
            <th class="budget_th" style="min-width:200px; border:1px solid black; vertical-align:middle">PRIMARY PROPONENT</th>
            <th class="budget_th" style="min-width:200px; border:1px solid black; vertical-align:middle">C/O PROPONENT</th>
            <th class="budget_th" style="min-width:200px; border:1px solid black; vertical-align:middle">NAME OF HOSPITAL | RECIPIENT FACILITY</th>
            <th class="budget_th" style="min-width:80px; border:1px solid black; vertical-align:middle">TOTAL ALLOCATED AMOUNT</th>
            <th class="budget_th" style="min-width:130px; border:1px solid black; vertical-align:middle">AMOUNT OBLIGATED FOR MAIFIPP</th>
            <th class="budget_th" style="min-width:130px; border:1px solid black; vertical-align:middle">UNOBLIGATED ALLOTMENT FOR MAIFIPP (BALANCE)</th>
            <th class="budget_th" style="min-width:80px; border:1px solid black; vertical-align:middle">ALLOWABLE ADMIN COST (1%)</th>
            <th class="budget_th" style="min-width:130px; border:1px solid black; vertical-align:middle">AMOUNT OBLIGATED FOR ADMIN COST</th>
            <th class="budget_th" style="min-width:100px; border:1px solid black; vertical-align:middle">UNOBLIGATED ALLOTMENT FOR ADMIN COST (BALANCE)</th>
        </tr>
    </thead>
    <tbody >
        @if(isset($result))
            @foreach($result as $index => $row)
                <tr style="text-align:center;">
                    @csrf    
                    <td style="border:1px solid gray; vertical-align:middle">
                        {{ isset($row['info']['main_pro'])?$row['info']['main_pro']['proponent'] :'' }}
                    </td>
                    <td style="border:1px solid gray; vertical-align:middle">
                        {{ $row['info']['proponent']['proponent'] }}
                    </td>
                    <td style="border:1px solid gray; vertical-align:middle">
                        <?php
                            $ids = json_decode($row['info']['facility_id']);
                            if (!is_array($ids)) {
                                $ids = [$ids];
                            }
                        ?>
                        @foreach($ids as $id)
                            @php
                                $facility = $facilities->where('id', $id)->first();
                            @endphp
                            @if($facility)
                                {{ $facility['name'] }}<br>
                            @endif
                        @endforeach
                    </td>
                    <td style="border:1px solid gray; vertical-align:middle">
                        {{ number_format(str_replace(',','',$row['info']->alocated_funds), 2,'.',',') }}
                    </td>
                    <td style="border:1px solid gray; vertical-align:middle">
                        {{ number_format($row['obligated'],2,'.',',') }}
                    </td>
                    <td style="border:1px solid gray; vertical-align:middle">
                        {{ number_format(str_replace(',','',$row['info']->alocated_funds) - $row['obligated'],2,'.',',') }}
                    </td>
                    <td style="border:1px solid gray; vertical-align:middle">
                        @if($index == 0)
                            {{ number_format(str_replace(',','',$saa->admin_cost + $saa->budget_cost), 2,'.',',') }}
                        @endif
                    </td>
                    <td style="border:1px solid gray; vertical-align:middle">
                        @if($index == 0)
                            {{ number_format(str_replace(',','',$saa->a_cost[0]->total_admin_cost), 2,'.',',') }}
                        @endif    
                    </td>
                    <td style="border:1px solid gray; vertical-align:middle">
                        @if($index == 0)
                            {{ number_format(str_replace(',','',$saa->admin_cost + $saa->budget_cost - $saa->a_cost[0]->total_admin_cost), 2,'.',',') }}
                        @endif    
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
<div class="pl-6 pr-6 mt-6 funds_pagination" style="margin-top:20px">
    {!! $result->appends(request()->query())->links('pagination::bootstrap-5') !!}
</div>

                    

