<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Facility</label>
            <select class="form-control" id="{{ $uniqueCode }}" name="facility_id[]" required>
                <option value="">Please select facility</option>
                @foreach($facilities as $facility)
                    <option value="{{ $facility->id }}">{{ $facility->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="alocated_funds">Allocated Funddsfdf</label>
            <input type="text" class="form-control" id="alocated_funds" name="alocated_funds[]" placeholder="Allocated Fund" onkeyup = "validateAmount(this)" required>
        </div>
    </div>
</div>

<script>
    $("#"+"{{ $uniqueCode }}").select2();
</script>