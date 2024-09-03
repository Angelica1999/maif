<div class="proponent_clone" style="text-align: center; border: 1px solid black; width: 100%; padding: 3%;  margin-top: 10px; ">
    <div class="card" style="border: none;">
        <div class="row" style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 4%;">
            <i class="typcn typcn-minus menu-icon btn_pro_remove" style="width:40px;background-color:red; color:white;border: 1px; padding: 2px;"></i>
            <select style="width: 50%; margin-bottom: 10px;" class="select2 proponent" required>
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
                    <input class="form-control control_no" style="text-align: center; width: 56%;" placeholder="CONTROL NUMBER" required>
                    <i class="typcn typcn-plus menu-icon control_clone_btn" style="width:40px;background-color:blue; color:white;border: 1px; padding: 2px;"></i>
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
            <select style="width: 50%;" class="select2 saa_id" required>
                <option value=''>SELECT SAA</option>
            </select>
            <input placeholder="AMOUNT" class="form-control saa_amount" onkeyup="validateAmount(this)" style="width: 35%;" required>
            <i class="typcn typcn-plus menu-icon saa_clone_btn" style="width:40px;background-color:blue; color:white;border: 1px; padding: 2px;"></i>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.select2').select2();

        var facility_id = {{$facility_id}};
        
        $.get("{{ url('fetch/fundsource').'/' }}"+ facility_id, function(result) {
            var data_result = result.info;
            var text_display;

            var first = [], sec = [], third = [], fourth = [], fifth = [], six = [];

            $.each(data_result, function(index, optionData){
                var rem_balance = parseFloat(optionData.remaining_balance.replace(/,/g, '')).toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2});

                var check_p = 0;  
                var id = optionData.facility_id;

                if(optionData.facility !== null){
                    if(optionData.facility.id == facility_id){
                        text_display = optionData.fundsource.saa + ' - ' + optionData.proponent.proponent + ' - SF - ' + rem_balance;
                    }else{
                        text_display = optionData.fundsource.saa + ' - ' + optionData.proponent.proponent + ' - ' + optionData.facility.name + ' - ' + rem_balance;
                        check_p = 1;
                    } 
                }else{
                    if(id.includes('702')){
                        check_p = 1;
                        text_display = optionData.fundsource.saa + ' - ' + optionData.proponent.proponent + ' - ' + 'DOH CVCHD' + ' - ' + rem_balance;
                    }else{
                        text_display = optionData.fundsource.saa + ' - ' + optionData.proponent.proponent + ' - SF - ' + rem_balance;
                    }
                }

                var color = rem_balance == '0' || rem_balance == '0.00' ? 'red' : 'normal';
                var obj = {
                    value: optionData.fundsource_id,
                    text: text_display,
                    dataval: optionData.remaining_balance,
                    dataproponentInfo_id: optionData.id,
                    dataprogroup: optionData.proponent.pro_group,
                    dataproponent: optionData.proponent.id,
                    d_color: color
                };

                if(color === 'red') {
                    if(optionData.fundsource.saa.includes('CONAP')) fifth.push(obj);
                    else six.push(obj);
                } else {
                    if(optionData.fundsource.saa.includes('CONAP')){
                        if(check_p == 1) sec.push(obj);
                        else first.push(obj);
                    } else {
                        if(check_p == 1) fourth.push(obj);
                        else third.push(obj);
                    }
                }
            });

            addOption(first);
            addOption(sec);
            addOption(third);
            addOption(fourth);
            addOption(fifth);
            addOption(six);

            $('.saa_id').select2({
                templateResult: function (data) {
                    if ($(data.element).data('color') === 'red') {
                        return $('<span style="color: red;">' + data.text + '</span>');
                    }
                    return data.text;
                }
            }).prop('disabled', false);
        });

        function addOption(data){
            var fragment = document.createDocumentFragment();
            data.forEach(function(item) {
                var option = $('<option>', {
                    value: item.value,
                    text: item.text,
                    dataval: item.dataval,
                    dataproponentInfo_id: item.dataproponentInfo_id,
                    dataprogroup: item.dataprogroup,
                    dataproponent: item.dataproponent,
                    'data-color': item.d_color
                })[0]; 

                fragment.appendChild(option);
            });
            $('.saa_id').append(fragment);
        }
    });

</script>
