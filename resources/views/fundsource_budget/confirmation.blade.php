@foreach($data as $row)
    <tr>
        <td><a href="#" data-toggle="modal" onclick="displayFunds('{{ $dv->route_no }}','{{ $row->proponentData->proponent }}', {{ $row->id }})">{{ $dv->route_no }}</a></td>
        <td>{{ $row->saaData->saa }}</td>
        <td>{{ $row->proponentData->proponent }}</td>
        <td>
            <?php
                $ids = json_decode($row->infoData->facility_id);
                if (!is_array($ids)) {
                    $ids = [$ids];
                }
            ?>
            @foreach($ids as $id)
                @php
                    $facility = $facilities->where('id', $id)->first();
                @endphp
                @if($facility)
                    {{ $facility->name }}<br>
                @endif
            @endforeach
        </td>
        <td>{{ $dv->preDv->facility->name }}</td>
        <td>{{ number_format(str_replace(',','', $row->utilize_amount), 2,'.',',') }}</td>
        <td>
            <input type="checkbox" id="checkbox_{{ $row->id }}" class="confirm_check" style="width: 50px; height: 15px;" disabled>
        </td>
    </tr>
@endforeach