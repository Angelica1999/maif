<div class="clone">
    <div class="card" style="border:none;">
        <div class="row">
            <div class="col-md-5">
                <label>Proponent (Main):</label>
                <div class="form-group">
                    <select class="form-control proponent_main" id="{{ $uniqueCode . '2' }}" name="proponent_main[]">
                        <option value=""></option>
                        @foreach($proponents as $proponent)
                            <option value="{{ $proponent->id }}" data-proponent-code="{{ $proponent->proponent_code }}">
                                {{ $proponent->proponent }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-5">
                <label>Proponent:</label>
                <div class="form-group">
                    <select class="form-control proponent" id="{{ $uniqueCode . '1' }}" name="proponent[]" onchange="proponentCode($(this))" required>
                        <option value=""></option>
                        @foreach($proponents as $proponent)
                            <option value="{{ $proponent->proponent }}" data-proponent-code="{{ $proponent->proponent_code }}">
                                {{ $proponent->proponent }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-7">
                <label>Proponent Code:</label>
                <div class="form-group" style="display: flex; align-items: center;">
                    <input type="text" class="form-control proponent_code" name="proponent_code[]" placeholder="Proponent Code" style="flex: 1; width:1000px;" onblur="checkCode($(this),this.value)" required>
                    <button type="button" class="form-control remove_pro-btn" style="width: 10px; margin-left: 5px; color:white; background-color:#00688B">-</button>
                </div>
            </div>
        </div>
        <div class="card1">
            <div class="row">
                <div class="col-md-5">
                <label>Facility:</label>
                    <div class="form-group">
                        <div class="facility_select">
                            <select class="form-control break_fac" id="{{ $uniqueCode }}" name="facility_id[id][]" multiple required>
                                <option value=""></option>
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
                            <input type="text" class="form-control alocated_funds" id="alocated_funds[]" name="alocated_funds[]" oninput="calculateFunds(this)" onkeyup="validateAmount(this)" placeholder="Allocated Fund" style="flex: 1; width:160px;" required>
                            <button type="button" class="form-control btn-info clone_facility-btn" style="width: 5px; margin-left: 5px; color:white; background-color:#355E3B">+</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>
</div>
<script>
    $("#"+"{{ $uniqueCode }}").select2({
        placeholder:"Select Facilities"
    });
    $("#"+"{{ $uniqueCode . '1' }}").select2({
        tags: true,
        placeholder: "Select/Input Proponent"
    });
    $("#"+"{{ $uniqueCode . '2' }}").select2({
        placeholder:"Select Proponent"
    });
</script>