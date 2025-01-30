<form class="pre_form" style="width:100%; font-weight:1px solid black" method="get">
    @csrf
    <div style="width: 100%; display:flex; justify-content: center;text-align:center;">
        <input value="{{$result->facility->name}}" class="form-control" style="width:80%; text-align:center; font-weight: bold" readonly> 
    </div>
    <div style="display: inline-block; width: 100%; margin-top:30px">
        @foreach($result->extension as $index => $row)
            @if($index == 0)
                <label style="display: inline-block; margin-left: 18%; width: 20%; font-weight: bold">SAA</label>
                <label style="display: inline-block; margin-left: 7%; width: 20%; font-weight: bold">AMOUNT</label>
                <label style="display: inline-block; margin-left: 8%; width: 20%; font-weight: bold">PROPONENT</label>

            @endif
            @foreach ($row->saas as $data)
                <input value="{{ $data->saa->saa }}" class="form-control" style="display: inline-block; width: 35%; margin-left: 2%; margin-top: 8px; text-align:center" disabled> 
                <input value="{{ number_format(str_replace(',', '', $data->amount), 2, '.',',')}}" class="form-control" style="display: inline-block; width: 20%; text-align: center; margin-left: 2%; text-align:center" disabled> 
                <input value="{{ $row->proponent->proponent }}" class="form-control" style="display: inline-block; width: 35%; text-align: center; margin-left: 2%; text-align:center" disabled> 
            @endforeach
        @endforeach
    </div>
    <div style="display: flex; justify-content: flex-end; margin-top: 2%;">
        <input class="form-control grand_total" name="grand_total" style="width: 30%; text-align: center; margin-right:3%" placeholder="GRAND TOTAL" value="{{$result->grand_total}}" readonly>
    </div>
    <button type="submit" class="btn-sm btn-success updated_submit" style="display:none">SUBMIT</button>
</form>