<div class="control_clone" style="padding: 10px; border: 1px solid lightgray; margin-top: 2%;">
    <div style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 4%;">
        <input class="form-control control_no" onblur="checkControlNo(this)" style="text-align: center; width: 56%;" placeholder="CONTROL NUMBER" oninput="this.value = this.value.toUpperCase()" required>
        <i class="typcn typcn-plus menu-icon control_clone_btn" style="width:40px;background-color:blue; color:white;border: 1px; padding: 2px;"></i>
    </div>
    <div style="display: flex; justify-content: space-between;">
        <input placeholder="PATIENT" class="form-control patient_1" style="width: 41%;" oninput="this.value = this.value.toUpperCase()" required>
        <input placeholder="AMOUNT/TRANSMITTAL" class="form-control amount" onkeyup="validateAmount(this)" style="width: 50%;" required>
    </div>
    <div style="display: flex; justify-content: space-between;">
        <input placeholder="PATIENT" class="form-control patient_2" style="width: 41%; margin-top: 5px;" oninput="this.value = this.value.toUpperCase()">
        <input placeholder="PROFESSIONAL FEE" class="form-control prof_fee" onkeyup="validateAmount(this)" style="width: 50%; margin-top: 5px;">
    </div>
</div>