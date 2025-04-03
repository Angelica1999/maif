
<?php  
    use App\Models\Facility; 
?>
<style>
    hr {
        border: 1px solid;
        color: grey;
    }
</style>
<form id="contractForm" method="POST" action="{{ route('transfer.save')}}">
    <div class="modal-body">
        @csrf
        <div class="row ">
            <div class="col-md-12">
                <b><h5>From:</b> {{$from_info->proponent->proponent}} - {{$from_info->fundsource->saa}} </h5>
            </div>
        </div>
        <div class="row ">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Facility:</label>
                    <?php 
                        if (is_string($from_info->facility_id)) {
                            $facilityIds = json_decode($from_info->facility_id);
                            if (is_array($facilityIds)) {
                                $facilityIds = array_map('intval', $facilityIds);
                                $facilities = Facility::whereIn('id', $facilityIds)->pluck('name')->toArray();
                                $facility = implode(', ', $facilities);
                            } else {
                                $facility = Facility::where('id', $from_info->facility_id)->value('name');
                            }
                        } else {
                            $facility = Facility::where('id', $from_info->facility_id)->value('name');
                        }
                    ?>
                    <input type="facility" class="form-control" value="{{$facility}}" readonly >
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="rem_bal">Source Balance:</label> 
                    <select class="form-control sb_from" name="sb_from" id="sb_from" onchange="setData(this.options[this.selectedIndex].dataset.amount)">
                        <option></option>
                        <option value="1" data-amount="{{ $from_info->alocated_funds }}">Allocated Funds</option>
                        <option value="2" data-amount="{{ $from_info->remaining_balance }}">Remaining Balance</option>
                        <option value="3" data-amount="{{ $from_info->admin_cost }}">Admin Cost</option>
                    </select>
                    <!-- <input type="text" class="form-control " id="rem_bal" name="rem_bal" value="{{ number_format(floatval(str_replace(',', '',$from_info->remaining_balance)), 2, '.', ',') }}" readonly> -->
                </div>
            </div>
        </div>
        <hr>
        <strong>Transfer To:</strong>
        <h5 class="transfer transfer_to" style="display: inline;"></h5>

        <div class="row ">
            <div class="col-md-6">
                <div class="form-group">
                    <br>
                    <label>SAA NO:</label>
                    <div id="saa_body">
                        <select  style="height:48px" class="form-control to_saa" id="to_saa" name="to_saa" onchange = "updateProponent(this.value)" required>
                            <option value="">Please select SAA NO</option>
                            @foreach($fundsources as $fundsource)
                                <option value="{{$fundsource->id}}">{{$fundsource->saa}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <br>
                    <label for="alocated_funds">Proponent:</label>
                    <select  style="height:48px" class="form-control to_proponent" name="to_proponent" onchange = "updateFacility(this.value)" required disabled>
                        <option value="">Please select proponent</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row ">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Facility:</label>
                    <div id="facility_body">
                        <select style="height:48px" class="form-control break_fac" name="to_info" onchange = "updateFacField(this.value)" required disabled>
                            <option value="">Please select facility</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Source Balance:</label>
                    <div id="">
                        <select style="height:38px" class="form-control to_source" name="to_source" required>
                            <option value=""></option>
                            <option value="1">Allocated Funds</option>
                            <option value="2">Remaining Balance</option>
                            <option value="3">Admin Cost</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="alocated_funds">Amount to Transfer:</label>
                    <input type="text" class="form-control to_amount" id="to_amount" name="to_amount" oninput="validateBalance(this.value)" onkeyup= "validateAmount(this)" required disabled>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="alocated_funds">Remarks:</label>
                    <textarea style="height:38px" type="text" class="form-control transfer_remarks" id="transfer_remarks" name="transfer_remarks"></textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Transfer Funds</button>
        <input type="hidden" class="form-control fac" id="fac" name="fac" value="">
        <input type="hidden" class="form-control" id="from_info" name="from_info" value="{{$from_info->id}}" >
    </div>
</form>

<script src="{{ asset('admin/js/select2.js?v=').date('His') }}"></script>
<script>
    var selected_amount;
    function setData(amount) {
        selected_amount = parseFloat(amount.replace(/,/g,''));
    }

    $('.sb_from').select2({
        tag: true,
        placeholder: 'Please Select',
        templateSelection: function (data) {
            var $selectedElement = $(data.element);
            var amount = $selectedElement.attr('data-amount');
            return amount 
            ? parseFloat(amount.replace(/,/g, '')).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) 
            : data.text;
        }
    });
    $('.to_source').select2({
        tag: true,
        placeholder: 'Select Type'
    });
    $(document).ready(function() {
        $('.break_fac').select2();
        $('.to_saa').select2();
        $('.to_proponent').select2();

        $('.trans_fac').select2({
            theme: 'bootstrap', 
            width: '100%',     
            placeholder: 'Please select facility', 
        });
    });

    function validateBalance(value){
        var to_transfer = parseFloat(value.replace(/,/g,''));
       
        if( to_transfer > selected_amount){
            Lobibox.alert('error',{
                size: 'mini',
                msg: "Insufficient Funds!"
            });
            $('.to_amount').val('');
        }
            var amount= document.getElementById("to_amount").value;
    }
    var saa_id, proponent;

    function updateProponent(fundsource_id){
        $('.fac').val('');
        $('.break_fac').empty();
        $('.break_fac').append($('<option>',{
            value : "",
            text : "Please select facility"
        }));
        saa_id = fundsource_id;
        $('.to_proponent').empty();
        $.get("proponents/lists/" + fundsource_id, function(result) {
            $('.to_proponent').append($('<option>',{
                value : "",
                text : "Please select proponent"
            }));

            $.each(result, function(index, option){
                $('.to_proponent').append($('<option>',{
                    value : option.id,
                    text : option.proponent
                }));
            });
            if(fundsource_id == "all"){
                $('.to_proponent').append($('<option>',{
                    value : "specific",
                    text : "Back to specific..."
                }));
            }else{
                $('.to_proponent').append($('<option>',{
                    value : "others",
                    text : "Others..."
                }));
            }
        });

        $('.to_proponent').prop('disabled', false);
        $('.break_fac').prop('disabled', true);
    }

    function updateFacility(proponent_id){
        $('.fac').val('');
        proponent = proponent_id

        if(proponent_id == "others"){
            $('.to_proponent').empty();
            $.get("proponents/lists/" + "all", function(result) {

                $('.to_proponent').append($('<option>',{
                    value : "",
                    text : "Please select proponent"
                }));
                $.each(result, function(index, option){
                    $('.to_proponent').append($('<option>',{
                        value : option.id,
                        text : option.proponent
                    }));
                });
                $('.to_proponent').append($('<option>',{
                    value : "specific",
                    text : "Back to specific..."
                }));
            });

            $('.to_proponent').prop('disabled', false);
        }else if(proponent_id == "specific"){
            updateProponent(saa_id);
    
        }else{
            generateInfo(saa_id, proponent);
        }
    }

    function updateFacField(pro_info){
        if(pro_info == "others"){
            $('.fac').val('new_fac');
            $('.break_fac').empty();
            $('.break_fac').prop('disabled', true);

            $.get("transfer/facility/" + "others", function(result) {

                $('.break_fac').append($('<option>',{
                    value : "",
                    text : "Please select facility"
                }));
                $.each(result, function(index, option){
                    $('.break_fac').append($('<option>',{
                        value : option.id,
                        text : option.name
                    }));
                });
                $('.break_fac').append($('<option>',{
                    value : "specific",
                    text : "Back to specific..."
                }));
                $('.break_fac').prop('disabled', false);
            });
        }else if(pro_info == "specific"){
            $('.fac').val('');
            generateInfo(saa_id, proponent);
        }else{
            $('.to_amount').prop('disabled', false);
        }
    }

    function generateInfo(saa_id, proponent){
        $('.break_fac').prop('disabled', true);
        $('.break_fac').empty();
        $.get("transfer/proponentInfo/" + saa_id + "/" + proponent, function(result) {
            $('.break_fac').append($('<option>', {
                value: "",
                text: "Please select Facility"
            }));

            var promises = [];

            $.each(result, function(index, option) {
                if (checkIfJsonString(option.facility_id)) {
                    var ids = JSON.parse(option.facility_id).map(Number);
                    var name_list = [];

                    ids.forEach(function(id) {
                        var promise = $.get("transfer/facility/" + id, function(result) {
                            name_list.push(result.name);
                        });
                        promises.push(promise);
                    });

                    Promise.all(promises).then(function() {
                        $('.break_fac').append($('<option>', {
                            value: option.id,
                            text: name_list.join(' - ')
                        }));
                    });
                } else {
                    var promise = $.get("transfer/facility/" + option.facility_id, function(result) {
                        $('.break_fac').append($('<option>', {
                            value: option.id,
                            text: result.name
                        }));
                    });
                    promises.push(promise);
                }
            });

            Promise.all(promises).then(function() {
                $('.break_fac').append($('<option>', {
                    value: "others",
                    text: "Others..."
                }));
                $('.break_fac').prop('disabled', false);
            });
        });
    }

    function checkIfJsonString(str) {
        if (typeof str !== 'string'){
            return false;
        }
        if (str.startsWith('[') && str.endsWith(']')) {
            try {
                JSON.parse(str);
                return true;
            } catch (error) {
                return false;
            }
        }
        return false;
    }

</script>

