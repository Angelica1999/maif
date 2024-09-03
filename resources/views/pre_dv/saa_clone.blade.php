<div class="saa_clone" style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 2%;">
    <select style="width: 50%;" class="select2 saa_id" required>
        <option value=''>SELECT SAA</option>
    </select>
    <input placeholder="AMOUNT" class="form-control saa_amount" onkeyup="validateAmount(this)" style="width: 35%;" required>
    <i class="typcn typcn-plus menu-icon saa_clone_btn" style="width:40px;background-color:blue; color:white;border: 1px; padding: 2px;"></i>
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