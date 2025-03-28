<div class="proponent_clone" style="text-align: center; border: 1px solid black; width: 100%; padding: 3%;  margin-top: 10px; ">
    <div class="card" style="border: none;">
        <div class="row" style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 4%;">
            <i class="typcn typcn-minus menu-icon btn_pro_remove" style="width:40px;background-color:red; color:white;border: 1px; padding: 2px;"></i>
            <select style="width: 50%; margin-bottom: 10px;" class="select2 proponent" onchange="checkPros(this)" required>
                <option value=''>SELECT PROPONENT</option>
                @foreach($proponents as $proponent)
                    <option value="{{$proponent->proponent}}">{{$proponent->proponent}}</option>
                @endforeach
            </select>
            <i onclick="cloneProponent($(this))" class="typcn typcn-plus menu-icon" style="width:40px; background-color:blue; color:white;border: 1px; padding: 2px;"></i>
        </div>
        <div class="control_div">
            <div class="control_clone" style="padding: 10px; border: 1px solid lightgray;">
                <div style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 4%;">
                    <input class="form-control control_no" style="text-align: center; width: 56%;" placeholder="CONTROL NUMBER" oninput="this.value = this.value.toUpperCase()" required>
                    <i class="typcn typcn-plus menu-icon control_clone_btn" style="width:40px;background-color:blue; color:white;border: 1px; padding: 2px;"></i>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <input placeholder="PATIENT" class="form-control patient_1" style="width: 41%;" oninput="this.value = this.value.toUpperCase()" required>
                    <input placeholder="AMOUNT/TRANSMITTAL" class="form-control amount" onkeyup="validateAmount(this)" style="width: 50%;" required>
                </div>
                <input placeholder="PATIENT" class="form-control patient_2" style="width: 41%; margin-top: 5px;" oninput="this.value = this.value.toUpperCase()">
            </div>
        </div>
        <div style="display: flex; justify-content: flex-end; margin-top: 5%; margin-bottom: 5%;">
            <input class="form-control total_amount" style="width: 60%; text-align: center;" placeholder="TOTAL AMOUNT PER PROPONENT" readonly>
        </div>
        <div class="saa_clone" style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 2%;">
            <select style="width: 50%;" class="select2 saa_id" onchange="autoDeduct($(this))" required>
                <option value=''>SELECT SAA</option>
                @foreach($info as $row)
                    <?php
                        $rem_balance = number_format((float)str_replace(',', '', $row->remaining_balance), 2, '.', ',');
                        $text_display = '';
                        if ($row->facility !== null) {
                            if ($row->facility->id == $facility->id) {
                                $text_display = $row->fundsource->saa . ' - ' . $row->proponent->proponent . ' - SF - ' . $rem_balance;
                            } else {
                                $text_display = $row->fundsource->saa . ' - ' . $row->proponent->proponent . ' - ' . $row->facility->name . ' - ' . $rem_balance;
                            }
                        } else {
                            if (strpos($row->facility_id, '702') !== false) {
                                $text_display = $row->fundsource->saa . ' - ' . $row->proponent->proponent . ' - DOH CVCHD - ' . $rem_balance;
                            } else {
                                $text_display = $row->fundsource->saa . ' - ' . $row->proponent->proponent . ' - SF - ' . $rem_balance;
                            }
                        }                            
                    ?>
                    <option data-color="{{ $rem_balance == 0 ? 'red' : '' }}" dataproponentInfo_id="{{$row->id}}" dataprogroup="{{$row->proponent->pro_group}}" dataproponent="{{$row->proponent->id}}" value="{{$row->fundsource_id}}" dataval="{{$row->remaining_balance}}">
                        {{$text_display}}
                    </option>
                @endforeach
            </select>
            <input placeholder="AMOUNT" class="form-control saa_amount" onkeyup="validateAmount(this)" style="width: 35%;" required>
            <i class="typcn typcn-plus menu-icon saa_clone_btn" style="width:40px;background-color:blue; color:white;border: 1px; padding: 2px;"></i>
        </div>
        <div style="display:inline-block;">
            <span class="text-info">Total fundsource inputted amount:</span>
            <span class="text-danger inputted_amount" id="inputted_amount"></span>
        </div>
    </div>
</div>
<script>
    $('.select2').select2(); 
    $('.saa_id').select2({
        templateResult: function (data) {
            if ($(data.element).data('color') === 'red') {
                return $('<span style="color: red;">' + data.text + '</span>');
            }
            return data.text;
        }
    });
</script>
