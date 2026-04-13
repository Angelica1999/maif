@csrf
<input type="hidden" class="status" value="1">
<input type="hidden" id="pre_id" value="{{ $result->id }}" name="pre_id">

<div style="width: 100%; display:flex; justify-content: center;text-align:center;">
    <select class="select2 facility_id" style="width: 50%;" id="facility_id" name="facility_id" onchange="getFundsource($(this).val())" required>
        <option value=''>SELECT FACILITY</option>
        @foreach($facilities as $facility)
            <option value="{{ $facility->id }}" {{ ($result->facility->id == $facility->id)? 'selected': '' }}>{{ $facility->name }}</option>
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
                            <option value="{{ $proponent->proponent }}" {{ ($row->proponent->proponent == $proponent->proponent)?'selected':'' }}>{{$proponent->proponent}}</option>
                        @endforeach
                    </select>
                    <i onclick="cloneProponent($(this))" class="typcn typcn-plus menu-icon" style="width:40px; background-color:blue; color:white;border: 1px; padding: 2px;"></i>
                </div>

                <div class="control_div">
                    @foreach($row->controls as $index1 => $row2)
                        <div class="control_clone" style="padding: 10px; border: 1px solid lightgray;">
                            <div style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 4%;">
                                <input type="hidden" class="saa_number" value="0">
                                <input class="form-control control_no" onblur="checkControlNo(this)" style="text-align: center; width: 56%;" placeholder="CONTROL NUMBER" value="{{ $row2->control_no }}" oninput="this.value = this.value.toUpperCase()" required>
                                <i class="{{ ($index1 == 0)?'typcn typcn-plus menu-icon control_clone_btn': 'typcn typcn-minus menu-icon control_remove_btn' }}" style="width:40px; color:white;border: 1px; padding: 2px; {{($index1 == 0)?'background-color:blue' : 'background-color:red'}}"></i>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <input class="form-control patient_1" style="width: 41%;" value="{{ $row2->patient_1 }}" oninput="this.value = this.value.toUpperCase()" required>
                                <input class="form-control amount" value="{{ number_format(str_replace(',','',$row2->amount), 2, '.',',') }}" onkeyup="validateAmount(this)" style="width: 50%;" required>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <input value="{{ $row2->patient_2 }}" class="form-control patient_2" style="width: 41%; margin-top: 5px;" oninput="this.value = this.value.toUpperCase()">
                                <input class="form-control prof_fee" onkeyup="validateAmount(this)" value="{{ $row2->prof_fee? number_format(str_replace(',','',$row2->prof_fee), 2, '.',','):'' }}" style="width: 50%; margin-top: 5px;">
                            </div>
                        </div>
                    @endforeach
                </div>

                <div style="display: flex; justify-content: flex-end; margin-top: 5%; margin-bottom: 5%;">
                    <input class="form-control total_amount" style="width: 60%; text-align: center;" value="{{ number_format(str_replace(',','',$row->total_amount), 2, '.',',') }}" placeholder="TOTAL AMOUNT PER PROPONENT" readonly>
                </div>

                <?php $total_saa = number_format(str_replace(',','',$row->total_amount), 2, '.',','); ?>
                @foreach($row->saas as $index => $row3)
                    <div class="saa_clone" style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 2%;">
                        <select style="width: 50%;" class="select2 saa_id" data-rowId="{{ $row3->info_id }}" data-fundsourceRowId="{{ $row3->fundsource_id }}" onchange="autoDeduct($(this))" required>
                            <option value=''>Loading Options...</option>
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

<div style="display: flex; justify-content: space-between; margin-top: 5%;">
    <input class="form-control grand_fee" name="grand_fee" id="grand_fee" style="width: 48%; text-align: center;" value="{{ $result->prof_fee ? number_format(str_replace(',','',$result->prof_fee), 2, '.',','):0 }}" readonly>
    <input class="form-control grand_total" name="grand_total" id="grand_total" style="width: 50%; text-align: center;" value="{{ $result->grand_total ? number_format(str_replace(',','',$result->grand_total), 2, '.',',') : 0 }}" readonly> 
</div>

<div style="display: flex; justify-content: space-between; margin-bottom:5%; margin-top:1%">
    <label style="width: 48%; text-align: center;" class="text-info">TOTAL PROFESSIONAL FEE</label>
    <label style="width: 50%; text-align: center;" class="text-info">GRAND TOTAL</label>
</div>

<button type="submit" class="btn-sm btn-success updated_submit" style="display:none">SUBMIT</button>

<script>

    var df=1;
(function() {
    'use strict';
    
    $('.select2').select2();

    var facility_id = @json($result->facility->id);
    var url = "{{ url('fetch/pre-dv/fundsource') }}/" + facility_id;

    var parseBalance = (balance) => parseFloat(balance.replace(/,/g, ''));
    
    var formatBalance = (balance) => parseBalance(balance).toLocaleString('en-US', { 
        minimumFractionDigits: 2, 
        maximumFractionDigits: 2 
    });

    var parseFacilityId = (id) => {
        if (typeof id === 'string') {
            try {
                var parsed = JSON.parse(id);
                return Array.isArray(parsed) ? parsed : [parsed];
            } catch (e) {
                return [id];
            }
        }

        if (typeof id === 'number') {
            return [String(id)];
        }

        if (Array.isArray(id)) {
            return id;
        }

        return id ? [id] : [];
    };

    var getFacilityNames = (facilityIds, facilitiesArray) => {
        var idsArray = Array.isArray(facilityIds) ? facilityIds : [facilityIds];
        
        return facilitiesArray
            .filter(f => idsArray.includes(String(f.id)))
            .map(f => f.name)
            .join(' & ');
    };

    var determineColor = (remBalanceNum, checkP, proponentName, proName) => {
        if (remBalanceNum <= 0) return 'red';
        if (checkP !== 1 && proponentName === proName) return '';
        return 'yellow';
    };

    var createOptionObject = (optionData, text, color) => ({
        value: optionData.fundsource_id,
        text: text,
        dataval: optionData.remaining_balance,
        dataproponentInfo_id: optionData.id,
        dataprogroup: optionData.proponent.pro_group,
        dataproponent: optionData.proponent.id,
        d_color: color
    });

    var buildOptionsHtml = (arr) => arr.map(o => 
        `<option 
            value="${o.value}"
            data-color="${o.d_color}"
            dataval="${o.dataval}"
            dataproponentinfo_id="${o.dataproponentInfo_id}"
            dataprogroup="${o.dataprogroup}"
            dataproponent="${o.dataproponent}"
        >${o.text}</option>`
    ).join('');

    var categorizeOptions = (optionData, proponentName, facilityId, facilitiesArray) => {
        var categories = { first: [], sec: [], third: [], fourth: [], fifth: [], six: [] };
        
        optionData.forEach(data => {
            var proName = data.proponent.proponent;
            var remBalance = formatBalance(data.remaining_balance);
            var remBalanceNum = parseBalance(data.remaining_balance);
            var facilityIds = parseFacilityId(data.facility_id);
            var facilityNames = getFacilityNames(facilityIds, facilitiesArray);
            
            let checkP = 0;
            if (data.facility !== null) {
                checkP = data.facility.id !== facility_id ? 1 : 0;
            } else {
                checkP = data.facility_id.includes('702') ? 0 : 1;
            }
            
            var textDisplay = `${data.fundsource.saa} - ${proName} - ${facilityNames} - ${remBalance}`;
            var color = determineColor(remBalanceNum, checkP, proponentName, proName);
            var obj = createOptionObject(data, textDisplay, color);
            
            var isConap = data.fundsource.saa.includes('CONAP');
            var isMatching = checkP !== 1 && proponentName === proName;
            
            if (remBalanceNum <= 0) {
                categories[isConap ? 'fifth' : 'six'].push(obj);
            } else {
                if (isConap) {
                    categories[isMatching ? 'first' : 'third'].push(obj);
                } else {
                    categories[isMatching ? 'sec' : 'fourth'].push(obj);
                }
            }
        });
        
        return categories;
    };

    var initializeSaaSelects = ($proponentClone, categories) => {
        var finalHtml = ['first', 'sec', 'third', 'fourth', 'fifth', 'six']
            .map(key => buildOptionsHtml(categories[key]))
            .join('');
        
        $proponentClone.find('.saa_id').each(function() {
            var $select = $(this);
            var rowId = $select.data('rowid');
            var fundsourceRowId = $select.data('fundsourcerowid');

            $select.html(`<option value="">SELECT SAA</option>${finalHtml}`);
            $select.select2({
                templateResult: function(data) {
                    var color = $(data.element).data('color');
                    if (color === 'red') return $('<span style="color:red;">' + data.text + '</span>');
                    if (color === 'yellow') return $('<span style="color:#DAA520;">' + data.text + '</span>');
                    return data.text;
                },
                placeholder: "Select SAA"
            });
            
            if (fundsourceRowId && rowId) {
                $select.find('option').each(function() {
                    if ($(this).val() == fundsourceRowId && 
                    $(this).attr('dataproponentinfo_id') == rowId) {
                        $select.find('option').prop('selected', false);
                        $(this).prop('selected', true);
                        $select.trigger('change');
                        return false; 
                    }       
                });
            }
            
            $select.prop('disabled', false);
        });
    };

    $.get(url, function(result) {
        var dataResult = result.info;
        var facilitiesArray = Array.isArray(result.facilities) ? result.facilities : [];
        
        $('.proponent_clone').each(function() {
            var $proponentClone = $(this);
            var proponentName = $proponentClone.find('.proponent').val();
            
            var categories = categorizeOptions(dataResult, proponentName, facility_id, facilitiesArray);
            initializeSaaSelects($proponentClone, categories);
        });
    }).fail(function(error) {
        console.error('Error fetching fundsource data:', error);
    });
})();
</script>