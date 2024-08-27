<div class="control_clone" style="padding: 10px; border: 1px solid lightgray; margin-top: 2%;">
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