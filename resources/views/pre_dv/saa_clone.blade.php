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
            <option data-color="{{ $rem_balance == 0 ? 'red' : '' }}" dataproponentInfo_id="{{$row->id}}" dataprogroup="{{$row->proponent->pro_group}}" dataproponent="{{$row->proponent->id}}" value="{{$row->fundsource_id}}" dataval="{{$row->remaining_balance}}">
                {{$text_display}}
            </option>
        @endforeach
    </select>
    <input placeholder="AMOUNT" class="form-control saa_amount" onkeyup="validateAmount(this)" style="width: 35%;" required>
    <i class="typcn typcn-plus menu-icon saa_clone_btn" style="width:40px;background-color:blue; color:white;border: 1px; padding: 2px;"></i>
</div>
<script>
    $('.select2').select2(); 
    $('.saa_id').select2({
        templateResult: function (data) {
            if ($(data.element).data('color') === 'red') {
                return $('<span style="color: red;">' + data.text + '</span>');
            }
            return data.text;
        },
        placeholder: "Select SAA"
    });
</script>