@if($type == 1)
    <div class="con">
        <div class="clone" style="display:flex; padding:10px">
            <select class="form-control reference_no select2" style="width:250px" name="ref_no[]" required>
                <option value=''>Select Ref. No</option>
                @foreach($references as $item)
                    <option value="{{ $item['id'] }}">{{ $item['ref_no'] }}</option>
                @endforeach
            </select>     
            <input class="form-control remarks" name="remarks[]" style="margin-left:10px; width:250px" placeholder="remarks...">
            <button class="btn btn-xs btn-success add" type="button" style="margin-left:10px; border-radius:0px" onclick="addRow({{ $references[0]['transmittal_id'] }})">+</button>
        </div>
    </div>
@else
    <div class="clone" style="display:flex; padding:10px">
        <select class="form-control reference_no select2" id="{{ $code }}" style="width:250px" name="ref_no[]" required>
            <option value=''>Select Ref. No</option>
            @foreach($references as $item)
                <option value="{{ $item['id'] }}">{{ $item['ref_no'] }}</option>
            @endforeach
        </select>     
        <input class="form-control remarks" name="remarks[]" style="margin-left:10px; width:250px" placeholder="remarks...">
        <button class="btn btn-xs btn-warning remove" type="button" style="margin-left:10px; border-radius:0px">-</button>
    </div>
@endif
<script>
    $('#code').select2();
    $('.reference_no').select2();
</script>
