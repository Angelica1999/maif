    <div class="row">
        <div class="col-md-5">
            <label>Facility:</label>
            <div class="form-group">
                <div class="facility_select">
                    <select class="form-control break_fac"  id="{{ $uniqueCode }}" name="facility_id[]" multiple required>
                        <option value="">Please select facility</option>
                        @foreach($facilities as $facility)
                            <option value="{{ $facility->id }}">{{ $facility->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <label>Allocated Funds:</label>
            <div class="form-group">
                <div class="form-group" style="display: flex; align-items: center;">
                    <input type="hidden" class="info_id" value="0">
                    <input type="text" class="form-control alocated_funds" id="alocated_funds[]" name="alocated_funds[]" onkeyup="validateAmount(this)" oninput="calculateFunds(this)" placeholder="Allocated Fund" style="flex: 1; width:160px;" required>
                    <button type="button" class="form-control btn-info remove_fac-clone" style="width: 5px; margin-left: 5px; color:white; background-color:#355E3B">-</button>
                </div>
            </div>
        </div>
    </div>
<script>
    $("#"+"{{ $uniqueCode }}").select2({
        placeholder:"Select Facilties"
    });
</script>