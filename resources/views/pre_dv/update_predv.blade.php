<form class="pre_form" style="width:100%; font-weight:1px solid black" method="get" >
    @csrf
    <input type="hidden" class="status" value="1">
    <input type="hidden" id="pre_id" value="{{$result->id}}" name="pre_id">
    <div style="width: 100%; display:flex; justify-content: center;text-align:center;">
        <select class="select2 facility_id" style="width: 50%;" name="facility_id" required>
            <option value=''>SELECT FACILITY</option>
            @foreach($facilities as $facility)
              <option value="{{$facility->id}}" {{($result->facility->id == $facility->id)? 'selected': ''}}>{{$facility->name}}</option>
            @endforeach
        </select>
    </div>
    <div class="facility_div">
        @foreach($result->extension as $row)
            <div class="proponent_clone" style="text-align: center; border: 1px solid black; width: 100%; padding: 3%;  margin-top: 10px; ">
                <div class="card" style="border: none;">
                    <div class="row" style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 4%;">
                        <button type="button" class="form-control btn-xs btn-danger btn_pro_remove" style="width: 5%;"></button>
                        <select style="width: 50%; margin-bottom: 10px;" class="select2 proponent" required>
                            <option value=''>SELECT PROPONENT</option>
                            @foreach($proponents as $proponent)
                            <option value="{{$proponent->proponent}}" {{($row->proponent->proponent == $proponent->proponent)?'selected':'' }}>{{$proponent->proponent}}</option>
                            @endforeach
                        </select>
                        <button type="button" class="form-control btn-xs btn-info" onclick="cloneProponent($(this))" style="width: 5%;"></button>
                    </div>
                    <div class="control_div">
                        @foreach($row->controls as $row2)
                            <div class="control_clone" style="padding: 10px; border: 1px solid lightgray;">
                                <div style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 4%;">
                                    <input class="form-control control_no" style="text-align: center; width: 56%;" placeholder="CONTROL NUMBER" value="{{$row2->control_no}}" required>
                                    <button type="button" class="form-control btn-xs btn-info control_clone_btn" style="width: 5%;"></button>
                                </div>
                                <div style="display: flex; justify-content: space-between;">
                                    <input placeholder="PATIENT" class="form-control patient_1" style="width: 41%;" value="{{$row2->patient_1}}" required>
                                    <input placeholder="AMOUNT PER TRANSMITTAL" class="form-control amount" value="{{$row2->amount}}" onkeyup="validateAmount(this)" oninput="checkAmount($(this), $(this).val())" style="width: 50%;" required>
                                </div>
                                <input placeholder="PATIENT" value="{{$row2->patient_2}}" class="form-control patient_2" style="width: 41%; margin-top: 5px;" required>
                            </div>
                        @endforeach
                    </div>
                    <div style="display: flex; justify-content: flex-end; margin-top: 5%; margin-bottom: 5%;">
                        <input class="form-control total_amount" style="width: 60%; text-align: center;" value="{{$row->total_amount}}" placeholder="TOTAL AMOUNT PER PROPONENT" readonly>
                    </div>
                    @foreach($row->saas as $row3)
                        <div class="saa_clone" style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 2%;">
                            <select style="width: 40%;" class="select2 saa_id" required>
                                <option value=''>SELECT SAA</option>
                                @foreach($saas as $saa)
                                <option value="{{$saa->id}}" data-balance="{{$saa->alocated_funds}}" {{($row3->saa->id == $saa->id)?'selected':''}}>{{$saa->saa}}</option>
                                @endforeach
                            </select>
                            <input placeholder="AMOUNT" class="form-control saa_amount" onkeyup="validateAmount(this)" style="width: 41%;" value="{{$row3->amount}}" required>
                            <button type="button" class="form-control btn-xs btn-info saa_clone_btn" style="width: 5%;"></button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
    <div style="display: flex; justify-content: flex-end; margin-top: 5%; margin-bottom:5%">
        <input class="form-control grand_total" name="grand_total" style="width: 50%; text-align: center;" placeholder="GRAND TOTAL" value="{{$result->grand_total}}" readonly>
    </div>
    <button type="submit" class="btn-sm btn-success updated_submit" style="display:none">SUBMIT</button>
</form>
<script>
    $('.select2').select2();
</script>