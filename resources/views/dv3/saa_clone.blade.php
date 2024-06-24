<div style="display: flex; align-items: center;" class="clone_saa">
    &nbsp;&nbsp;&nbsp;&nbsp;
    <select name="fundsource_id[]" id="{{$uniqueCode}}" style="width:150px;" class="dv3_saa" onchange="saaValue($(this))" required>
        <option value="" data-facilities="" style="background-color:green">- Select SAA -</option> 
    </select> 
    <input type="hidden" name="info_id[]" id="info_id" class="info_id">
    <input type="hidden" name="existing[]" id="existing" class="existing" value="0">
    <div class="custom-dropdown" style="margin-left: 8px;">
        <input type="text" name="amount[]" id="amount[]" style="width:120px; height: 42px;" onkeyup="validateAmount(this)" oninput="checkedAmount($(this))" class="amount" required autocomplete="off">
    </div>
    <input type="text" name="vat_amount[]" id="vat_amount" class="vat_amount" style="margin-left: 8px; width: 80px; height: 42px;" class="ft15" readonly required>
    <input type="text" name="ewt_amount[]" id="ewt_amount" class="ewt_amount" style="width: 80px; height: 42px;" class="ft15" readonly required>
    <button type="button" id="remove_saa" class="remove_saa" class="fa fa-plus" style="border: none; width: 20px; height: 42px; font-size: 11px; cursor: pointer; width: 30px;">-</button>
</div>
<script>
    $("#"+"{{ $uniqueCode }}").select2();
    $(document).ready(function() {
        handleChangesF({{$id}});
    });
    function addOption(data){
        data.forEach(function(item) {
            var option = $('<option>', {
                value: item.value,
                text: item.text,
                dataval: item.dataval,
                dataproponentInfo_id: item.dataproponentInfo_id,
                dataprogroup: item.dataprogroup,
                dataproponent: item.dataproponent,
                'data-color': item.d_color
            });

            $("#"+"{{ $uniqueCode }}").append(option.clone());
        });
    }

    function handleChangesF(facility_id){
        $.get("{{ url('fetch/fundsource').'/' }}"+facility_id, function(result) {

            var data_result = result.info;
            var text_display;

            var first = [],sec = [],third = [],fourth = [],fifth = [],six = [];

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

                var color = '';
                if(rem_balance == '0' || rem_balance == '0.00'){
                    color = 'red';
                    if(optionData.fundsource.saa.includes('CONAP')){
                            obj = {
                                value: optionData.fundsource_id,
                                text: text_display,
                                dataval: optionData.remaining_balance,
                                dataproponentInfo_id: optionData.id,
                                dataprogroup: optionData.proponent.pro_group,
                                dataproponent: optionData.proponent.id,
                                d_color: color
                              }
                            fifth.push(obj);
                        }else{
                            obj = {
                                value: optionData.fundsource_id,
                                text: text_display,
                                dataval: optionData.remaining_balance,
                                dataproponentInfo_id: optionData.id,
                                dataprogroup: optionData.proponent.pro_group,
                                dataproponent: optionData.proponent.id,
                                d_color: color
                              }
                            six.push(obj);
                        }
                }else{

                    color = 'normal';

                    if(optionData.fundsource.saa.includes('CONAP')){
                        if(check_p == 1){
                            obj = {
                                value: optionData.fundsource_id,
                                text: text_display,
                                dataval: optionData.remaining_balance,
                                dataproponentInfo_id: optionData.id,
                                dataprogroup: optionData.proponent.pro_group,
                                dataproponent: optionData.proponent.id,
                                d_color: color
                              }
                            sec.push(obj);
                        }else{
                            obj = {
                                value: optionData.fundsource_id,
                                text: text_display,
                                dataval: optionData.remaining_balance,
                                dataproponentInfo_id: optionData.id,
                                dataprogroup: optionData.proponent.pro_group,
                                dataproponent: optionData.proponent.id,
                                d_color: color
                              }
                            first.push(obj);
                        }
                    }else{
                        if(check_p == 1){
                            obj = {
                                value: optionData.fundsource_id,
                                text: text_display,
                                dataval: optionData.remaining_balance,
                                dataproponentInfo_id: optionData.id,
                                dataprogroup: optionData.proponent.pro_group,
                                dataproponent: optionData.proponent.id,
                                d_color: color
                              }
                            fourth.push(obj);
                        }else{
                            obj = {
                                value: optionData.fundsource_id,
                                text: text_display,
                                dataval: optionData.remaining_balance,
                                dataproponentInfo_id: optionData.id,
                                dataprogroup: optionData.proponent.pro_group,
                                dataproponent: optionData.proponent.id,
                                d_color: color
                              }
                            third.push(obj);
                        }
                    }
                }

                $("."+"{{ $uniqueCode }}").select2({
                    templateResult: function (data) {
                        if ($(data.element).data('color') === 'red') {
                            return $('<span style="color: red;">' + data.text + '</span>');
                        }
                        return data.text;
                    }
                });
            });

            addOption(first);
            addOption(sec);
            addOption(third);
            addOption(fourth);
            addOption(fifth);
            addOption(six);
        });
    }
</script>