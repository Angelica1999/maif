@csrf
<table class="table table-list table-hover" id="">
    <thead style="position: sticky; top: 0; background-color: white; z-index: 1;">
        <tr style="background-color:#F5F5F5">
            <th>ROUTE #</th>
            <th>SAA #</th>
            <th>PROPONENT</th>
            <th>PAYEE</th>
            <th>RECIPIENT FACILITY</th>
            <th>Utilize Amount</th>
            <th>Confirm</th>
        </tr>
    </thead>
    <tbody id="confirm_body">
        @foreach($data as $row)
            <tr style="font-weight:normal">
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
                <td>{{ $row->facilitydata->name }}</td>
                <td>{{ number_format(str_replace(',','', $row->utilize_amount), 2,'.',',') }}</td>
                <td>
                    <input type="checkbox" id="checkbox_{{ $row->id }}" class="confirm_check" style="width: 50px; height: 15px;" disabled>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
