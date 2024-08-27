<div class="proponent_clone" style="text-align: center; border: 1px solid black; width: 100%; padding: 3%;  margin-top: 10px; ">
    <div class="card" style="border: none;">
        <div class="row" style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 4%;">
            <button type="button" class="form-control btn-xs btn-danger btn_pro_remove" style="width: 5%;"></button>
            <select style="width: 50%; margin-bottom: 10px;" class="select2 proponent" required>
                <option value=''>SELECT PROPONENT</option>
                @foreach($proponents as $proponent)
                    <option value="{{$proponent->proponent}}">{{$proponent->proponent}}</option>
                @endforeach
            </select>
            <button type="button" class="form-control btn-xs btn-info" onclick="cloneProponent($(this))" style="width: 5%;"></button>
        </div>
        <div class="control_div">
            <div class="control_clone" style="padding: 10px; border: 1px solid lightgray;">
                <div style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 4%;">
                    <input class="form-control control_no" style="text-align: center; width: 56%;" placeholder="CONTROL NUMBER" required>
                    <button type="button" class="form-control btn-xs btn-info control_clone_btn" style="width: 5%;"></button>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <input placeholder="PATIENT" class="form-control patient_1" style="width: 41%;" required>
                    <input placeholder="AMOUNT/TRANSMITTAL" class="form-control amount" onkeyup="validateAmount(this)" style="width: 50%;" required>
                </div>
                <input placeholder="PATIENT" class="form-control patient_2" style="width: 41%; margin-top: 5px;" required>
            </div>
        </div>
        <div style="display: flex; justify-content: flex-end; margin-top: 5%; margin-bottom: 5%;">
            <input class="form-control total_amount" style="width: 60%; text-align: center;" placeholder="TOTAL AMOUNT PER PROPONENT" readonly>
        </div>
        <div class="saa_clone" style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 2%;">
            <select style="width: 40%;" class="select2 saa_id" required>
                <option value=''>SELECT SAA</option>
                @foreach($saas as $saa)
                    <option value="{{$saa->id}}" data-balance="{{$saa->alocated_funds}}">{{$saa->saa}}</option>
                @endforeach
            </select>
            <input placeholder="AMOUNT" class="form-control saa_amount" onkeyup="validateAmount(this)" style="width: 41%;" required>
            <button type="button" class="form-control btn-xs btn-info saa_clone_btn" style="width: 5%;"></button>
        </div>
    </div>
</div>
<script>
    $('.select2').select2();
</script>
