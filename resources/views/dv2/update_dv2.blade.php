<?php
    use App\Models\Patients;
?>
<form id="update_dv2" method="POST" action="{{ route('dv2.update') }}">
    
    <div class="modal-body">
        @csrf
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                  
                    <h4 class="card-title">Disbursement Voucher V2</h4>
                    <p class="card-description">
                        MAIF-IPP
                    </p>
                    <div class="row">
                        <div class="cgrid-margin grid-margin-md-0 stretch-card" style="width:100%">
                            <div class="card" style="padding: 10px; border: 1px solid gray" >
                                <br>
                                <input class=" form-control mx-auto d-block text-center" value="{{ $dv2[0]->facility }}" name="facility" style="width:80%; height:40px; font-weight:bold" >
                                <div class="auto_generated">
                                    <input type="hidden" value="{{$dv2[0]->route_no}}" name="route_no">
                                    <div width="100%" class="for_clone1">
                                        @foreach($dv2 as $dv)
                                        <div class="card" style="margin: 2px auto; text-align: center; width: 90%; display: flex; flex-direction: column; align-items: center; justify-content: center; box-sizing: border-box; padding:10px; border:1px solid lightgray;">
                                        <div style="display: flex; justify-content: space-between; width: 100%; box-sizing: border-box;">
                                            <input class="form-control mx-auto d-block text-center" id="ref_no" name="ref_no[]" placeholder="Control No" style="width:80%; height:35px;" value="{{ htmlspecialchars($dv->ref_no) }}">
                                        </div>
                                        <br>
                                        <div style="display: flex; justify-content: space-between; width: 100%; box-sizing: border-box;">
                                            <div style="overflow: hidden; text-overflow: ellipsis; height: 100%; box-sizing: border-box;">
                                                <?php

                                                    $result = 0;
                                                    $result2 = 0;

                                                    if(is_numeric($dv->lname)){
                                                        $result = 1;
                                                        $group = Patients::where('id', (int)$dv->lname)->value('group_id');
                                                        $patients = Patients:: where('group_id', $group)->get();
                                                    }else{
                                                        $result = 2;
                                                    }

                                                    if($dv->lname2 == 0){
                                                        $result2 = 2;
                                                    }else{
                                                        if(is_numeric($dv->lname2)){
                                                            $result2 = 1;
                                                            $group2 = Patients::where('id', (int)$dv->lname2)->value('group_id');
                                                            $patients2 = Patients:: where('group_id', $group2)->get();
                                                        }else{
                                                            $result2 = 2;
                                                        }
                                                    }
                                            
                                                ?>
                                                <!-- {{$dv}} -->
                                                @if($result == 1)
                                                    <select name="g_lname1[]"  onchange="" style="width:80%; height:30px" class="js-example-basic-single1" required>
                                                        <option value="">-Lastname-</option> 
                                                        @foreach($patients as $pat)
                                                            <option value="{{$pat->id}}" {{($dv->lname == $pat->id)? 'selected':''}}>{{$pat->lname}}</option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <input class="form-control mx-auto d-block text-center" name="g_lname1[]" value="{{!Empty($dv->lname1)? htmlspecialchars($dv->lname1):$dv->lname }}" style="width:80%; height:35px; " required>
                                                @endif
                                                @if($result2 == 1)
                                                    <select name="g_lname2[]"  onchange="" style="width:80%; height:30px; margin-top:5px" class="js-example-basic-single2">
                                                        <option value="0" >-Lastname-</option>
                                                        @foreach($patients2 as $pat)
                                                            <option value="{{$pat->id}}" {{($dv->lname2 == $pat->id)? 'selected':''}}>{{$pat->lname}}</option>
                                                        @endforeach
                                                    </select> 
                                                @else
                                                    <input class="form-control mx-auto d-block text-center" name="g_lname2[]" value="{{($dv->lname2 == 0)?'':htmlspecialchars($dv->lname_2)}}" placeholder="LastName" style="width:80%; height:35px; margin-top:5px">
                                                @endif
                                                <!-- <select name="g_lname1[]"  onchange="" style="width:80%; height:30px" class="js-example-basic-single1" required>
                                                    <option value="">-Lastname-</option>
                                                
                                                </select>
                                                <select name="g_lname2[]"  onchange="" style="width:80%; height:30px; margin-top:5px" class="js-example-basic-single2">
                                                    <option value="0" >-Lastname-</option>
                                                 
                                                </select> -->
                                            </div>
                                            <div style="overflow: hidden; text-overflow: ellipsis; height: 100%; box-sizing: border-box;">
                                                <input class="form-control mx-auto d-block text-center amount_total" id="amount[]" name="amount[]" oninput="calculateSum(this.value)" value="{{$dv->amount}}" style="width:80%; height:30px; margin-top:5px" >
                                            </div>
                                        </div>
                                    </div>
                                    <script>
                                        $(document).ready(function() {
                                            $('.js-example-basic-single1').select2();
                                            $('.js-example-basic-single2').select2();

                                        });
                                    </script>
                                        @endforeach
                                        <div class="for_clone" style="display:none">
                                            <div class="card" style="margin: 2px auto; text-align: center; width: 90%; display: flex; flex-direction: column; align-items: center; justify-content: center; box-sizing: border-box; padding:10px; border:1px solid lightgray;">
                                                <div style="display: flex; justify-content: space-between; width: 100%; box-sizing: border-box;">
                                                    <input class="form-control mx-auto d-block text-center" id="ref_no" name="ref_no[]" placeholder="Control No" style="width:80%; height:35px; ">
                                                    <button type="button" class= "btn-info clone-button">+</button>
                                                </div>
                                                <br>
                                                <div style="display: flex; justify-content: space-between; width: 100%; box-sizing: border-box;">
                                                    <div style="overflow: hidden; text-overflow: ellipsis; height: 100%; box-sizing: border-box;">
                                                        <input class="form-control mx-auto d-block text-center" name="g_lname1[]" placeholder="LastName" style="width:80%; height:35px; ">
                                                        <input class="form-control mx-auto d-block text-center" name="g_lname2[]" value="" placeholder="LastName" style="width:80%; height:35px; margin-top:5px">
                                                    </div>
                                                    <div style="overflow: hidden; text-overflow: ellipsis; height: 100%; box-sizing: border-box;">
                                                        <input class="form-control mx-auto d-block text-center amount_total" id="amount[]" name="amount[]" oninput="calculateSum(this.value)" onkeyup="validateAmount(this)" style="width:90%; height:30px; margin-top:5px" >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <a style=" margin-left:290px" href="#" class="manual" onclick="addManual()">Add Manual</a>
                                </div>
                            </div>
                            <br>
                            <input class=" text-center total_cal" style="width:40%; height:35px; margin-left:210px" value="PHP {{number_format($total,2, '.', ',')}}" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="close_b" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
</form>

<script src="{{ asset('admin/js/select2.js?v=').date('His') }}"></script>
<script>
    var remove = {{count($dv2)}};
    var count = remove;
    $(document).ready(function () {
        
        $(".for_clone").on("click", ".clone-button", function () {
            count = count + 1;
            var clonedDiv = $(".for_clone .card:first").clone(true);
            $(clonedDiv).find(".clone-button").text("-");
            $(clonedDiv).find(".clone-button").removeClass("clone-button").addClass("remove-clone");
            $(".for_clone").append(clonedDiv);
        });

        $(".for_clone").on("click", ".remove-clone", function () {
            count = count - 1;
            $(this).closest(".card").remove();
            calculateSum();
        });
    
    });
    function addManual(){
            count = count + 1;
            $('.for_clone').css('display', 'block');       
            $('.manual').css('display', 'none');
            // $('.g_lname1').prop('required', true);
        }
       
    function validateAmount(element) {
        if (event.keyCode === 32) {
            event.preventDefault();
        }
        var cleanedValue = element.value.replace(/[^\d.]/g, '');
        var numericValue = parseFloat(cleanedValue);
        if (!isNaN(numericValue) || cleanedValue === '' || cleanedValue === '.') {
            element.value = cleanedValue.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        } else {
            element.value = ''; 
        }
    }
    function formatNumberWithCommas(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }
    function calculateSum(value){
        console.log('value',value);

        var amountArray = $('.amount_total').map(function() {
            return parseFloat($(this).val().replace(/,/g, '')) || 0;
        }).get().filter(function(value) {
            return value !== null && value !== undefined && value !== 0;
        });

        amountArray.splice(0, remove);
        amountArray.splice(count);

        var sum = amountArray.reduce(function(total, current) {
            return total + current;
        }, 0);

        $('.total_cal').val('PHP ' + formatNumberWithCommas(sum));
    }
</script>