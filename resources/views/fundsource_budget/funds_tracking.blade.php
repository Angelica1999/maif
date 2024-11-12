@if(isset($result))
    @foreach($result as $row)
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
                {{ number_format(str_replace(',','',$saa->admin_cost), 2,'.',',') }}
            </td>
            <td style="border:1px solid gray; vertical-align:middle">

            </td>
            <td style="border:1px solid gray; vertical-align:middle">
            </td>
        </tr>
    @endforeach
@endif
                    

