@if(isset($result))
    @foreach($result as $row)
        <input type="hidden" class="last_id" value="{{ $last->id }}">
        <tr style="text-align:center;">
            @csrf    
            <td class="budget_td" style="min-width:200px; border:1px solid gray; vertical-align:middle">
                {{ $row->fundSourcedata->saa }}
            </td>
            <td class="budget_td" style="max-width:190px; border:1px solid gray; vertical-align:middle">
                {{ $row->proponentdata->proponent }}
            </td>
            <td class="budget_td" style="max-width:90px; border:1px solid gray; vertical-align:middle">
                {{ $row->obligated_on? date('F j, Y', strtotime($row->obligated_on)):'' }}
            </td>
            <td class="budget_td" style="max-width:60px; border:1px solid gray; vertical-align:middle">
                {{ $row->dv? $row->dv->dv_no:''  }}
            </td>
            <td class="budget_td" style="max-width:150px; border:1px solid gray; vertical-align:middle">
                <?php
                    $ids = json_decode($row->infoData->facility_id);
                    if (!is_array($ids)) {
                        $ids = [$ids];
                    }
                ?>

                @foreach($ids as $id)
                    @php    
                        // Retrieve the facility by ID
                        $facility = $facilities->where('id', $id)->first();
                    @endphp
                    @if($facility)
                        {{ $facility->name }}<br>
                    @endif
                @endforeach

           </td>
            <td class="budget_td" style="max-width:150px; border:1px solid gray; vertical-align:middle">
                {{ $row->dv? $row->dv->facility->name: ($row->dv3? $row->dv3->facility->name:($row->newDv? $row->newDv->preDv->facility->name:'')) }}
            </td>
            <td class="budget_td" style="max-width:60px; border:1px solid gray; vertical-align:middle">
                {{ $row->dv? $row->dv->ors_no:''  }}
            </td>
            <td class="budget_td" style="max-width:130px; border:1px solid gray; vertical-align:middle"></td>
            <td class="budget_td" style="max-width:130px; border:1px solid gray; vertical-align:middle">
                {{ number_format($row->utilize_amount, 2,'.',',') }}
            </td>
            <td class="budget_td" style="max-width:90px; border:1px solid gray; vertical-align:middle"></td>
            <td class="budget_td" style="max-width:90px; border:1px solid black; vertical-align:middle"></td>
            <td style="border:1px solid black; vertical-align:middle"></td>
        </tr>
    @endforeach
@endif
                    

