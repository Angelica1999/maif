    @csrf
    <input type="hidden" class="status" value="1">
    <input type="hidden" id="pre_id" value="{{$result->id}}" name="pre_id">
    <div style="width: 100%; display:flex; justify-content: center;text-align:center;">
        <select class="select2 facility_id" style="width: 50%;" id="facility_id" name="facility_id" onchange="getFundsource($(this).val())" required>
            <option value=''>SELECT FACILITY</option>
            @foreach($facilities as $facility)
              <option value="{{$facility->id}}" {{($result->facility->id == $facility->id)? 'selected': ''}}>{{$facility->name}}</option>
            @endforeach
        </select>
    </div>
    <div class="facility_div">
        @foreach($result->extension as $index=>$row)
            <div class="proponent_clone" style="text-align: center; border: 1px solid black; width: 100%; padding: 3%;  margin-top: 10px; ">
                <div class="card" style="border: none;">
                    <div class="row" style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 4%;">
                        <i class="typcn typcn-minus menu-icon btn_pro_remove" style="width:40px; background-color:red; color:white;border: 1px; padding: 2px;"></i>
                        <select style="width: 50%; margin-bottom: 10px;" class="select2 proponent" onchange="checkPros(this)" required>
                            <option value=''>SELECT PROPONENT</option>
                            @foreach($proponents as $proponent)
                            <option value="{{$proponent->proponent}}" {{($row->proponent->proponent == $proponent->proponent)?'selected':'' }}>{{$proponent->proponent}}</option>
                            @endforeach
                        </select>
                        <i onclick="cloneProponent($(this))" class="typcn typcn-plus menu-icon" style="width:40px; background-color:blue; color:white;border: 1px; padding: 2px;"></i>
                    </div>
                    <div class="control_div">
                        @foreach($row->controls as $index1 => $row2)
                            <div class="control_clone" style="padding: 10px; border: 1px solid lightgray;">
                                <div style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 4%;">
                                    <input type="hidden" class="saa_number" value="0">
                                    <input class="form-control control_no" style="text-align: center; width: 56%;" placeholder="CONTROL NUMBER" value="{{ $row2->control_no }}" oninput="this.value = this.value.toUpperCase()" required>
                                    <i class="{{($index1 == 0)?'typcn typcn-plus menu-icon control_clone_btn': 'typcn typcn-minus menu-icon control_remove_btn' }}" style="width:40px; color:white;border: 1px; padding: 2px; {{($index1 == 0)?'background-color:blue' : 'background-color:red'}}"></i>
                                </div>
                                <div style="display: flex; justify-content: space-between;">
                                    <input placeholder="PATIENT" class="form-control patient_1" style="width: 41%;" value="{{ $row2->patient_1 }}" oninput="this.value = this.value.toUpperCase()" required>
                                    <input placeholder="AMOUNT PER TRANSMITTAL" class="form-control amount" value="{{number_format(str_replace(',','',$row2->amount), 2, '.',',')}}" onkeyup="validateAmount(this)" oninput="checkAmount($(this), $(this).val())" style="width: 50%;" required>
                                </div>
                                <input placeholder="PATIENT" value="{{ $row2->patient_2 }}" class="form-control patient_2" style="width: 41%; margin-top: 5px;" oninput="this.value = this.value.toUpperCase()">
                            </div>
                        @endforeach
                    </div>
                    <div style="display: flex; justify-content: flex-end; margin-top: 5%; margin-bottom: 5%;">
                        <input class="form-control total_amount" style="width: 60%; text-align: center;" value="{{number_format(str_replace(',','',$row->total_amount), 2, '.',',')}}" placeholder="TOTAL AMOUNT PER PROPONENT" readonly>
                    </div>
                    <?php $total_saa = number_format(str_replace(',','',$row->total_amount), 2, '.',','); ?>
                    @foreach($row->saas as $index => $row3)
                        <div class="saa_clone" style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 2%;">
                            <select style="width: 50%;" class="select2 saa_id" onchange="autoDeduct($(this))" required>
                                <option value=''></option>
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
                                    <option style="background-color:green" dataproponentInfo_id="{{ $row->id }}" dataprogroup="{{ $row->proponent->pro_group }}" dataproponent="{{ $row->proponent->id }}" value="{{ $row->fundsource_id }}" dataval="{{ $row->remaining_balance }}" {{ ($row3->fundsource_id == $row->fundsource_id && $row3->info_id == $row->id)?'selected':'' }}>
                                        {{ $text_display }} 
                                    </option>
                                @endforeach
                            </select>
                            <input placeholder="AMOUNT" class="form-control saa_amount" onkeyup="validateAmount(this)" style="width: 35%;" value="{{ number_format(str_replace(',','',$row3->amount), 2, '.',',') }}" required>
                            <i class="{{ ($index == 0)?'typcn typcn-plus menu-icon saa_clone_btn' : 'typcn typcn-minus menu-icon saa_remove_btn' }}" style="width:40px; color:white;border: 1px; padding: 2px; {{ ($index == 0)?'background-color:blue' : 'background-color:red' }}"></i>
                        </div>
                    @endforeach
                    <div style="display:inline-block;">
                        <span class="text-info">Total fundsource inputted amount:</span>
                        <span class="text-danger inputted_amount" id="inputted_amount">{{ $total_saa }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div style="display: flex; justify-content: flex-end; margin-top: 5%; margin-bottom:5%">
        <input class="form-control grand_total" name="grand_total" id="grand_total" style="width: 50%; text-align: center;" placeholder="GRAND TOTAL" value="{{ number_format(str_replace(',','',$result->grand_total), 2, '.',',') }}" readonly> 
    </div>
    <button type="submit" class="btn-sm btn-success updated_submit" style="display:none">SUBMIT</button>

<script>
    $('.select2').select2();
    $(document).ready(function () {
        $('.select2').select2({
            templateResult: function (data) {
                if (!data.id) {
                    return data.text;
                }

                var balance = $(data.element).attr("dataval");
                var text = $(data.element).text(); 

                var color = balance == 0 ? 'red' : 'black'; 
                return $('<span style="color:' + color + ';">' + text + '</span>');
            },
            templateSelection: function (data) {
                return data.text;
            },
            tags: true,
            placeholder: "SELECT SAA"
        });
    });
</script>