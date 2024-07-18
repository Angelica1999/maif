<form id="dv2" method="POST" action="{{ route('dv2.save') }}">
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
                                <textarea class=" form-control mx-auto d-block text-center"  style="width:80%; font-weight:bold" readonly>{{$dv->facility->name .'('.$dv->proponent.')'}}</textarea>
                                <input type="hidden" class="form-control mx-auto d-block text-center" value="{{$dv->facility->name .'('.$dv->proponent.')'}}" name="facility" style="width:80%; height:40px; font-weight:bold" readonly>
                                <div class="auto_generated">
                                @if(isset($group))
                                @foreach($group as $per_group)
                                    <div class="card" style="margin: 2px auto; text-align: center; width: 90%; display: flex; flex-direction: column; align-items: center; justify-content: center; box-sizing: border-box; padding:10px; border:1px solid lightgray;">
                                        <div style="display: flex; justify-content: space-between; width: 100%; box-sizing: border-box;">
                                            <input class="form-control mx-auto d-block text-center" oninput="checkNo(this)" id="ref_no" name="ref_no[]" placeholder="Control No" style="width:80%; height:35px;">
                                        </div>
                                        <br>
                                        <div style="display: flex; justify-content: space-between; width: 100%; box-sizing: border-box;">
                                            <div style="overflow: hidden; text-overflow: ellipsis; height: 100%; box-sizing: border-box;">
                                                <select name="g_lname1[]"  onchange="" style="width:80%; height:30px" class="js-example-basic-single1" required>
                                                    <option value="">-Lastname-</option>
                                                    @foreach($per_group->patient as $pat)
                                                        <option value="{{$pat->id}}">{{$pat->lname}}</option>
                                                    @endforeach
                                                </select>
                                                <select name="g_lname2[]"  onchange="" style="width:80%; height:30px; margin-top:5px" class="js-example-basic-single2">
                                                    <option value="0" >-Lastname-</option>
                                                    @foreach($per_group->patient as $pat)
                                                        <option value="{{$pat->id}}">{{$pat->lname}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div style="overflow: hidden; text-overflow: ellipsis; height: 100%; box-sizing: border-box;">
                                                <input class="form-control mx-auto d-block text-center amount_total" id="amount[]" name="amount[]" value="{{$per_group->amount}}" style="width:80%; height:30px; margin-top:5px" readonly>
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
                                @endif
                                    <div class="for_clone" style="display:none">
                                        <div class="card" style="margin: 2px auto; text-align: center; width: 90%; display: flex; flex-direction: column; align-items: center; justify-content: center; box-sizing: border-box; padding:10px; border:1px solid lightgray;">
                                            <div style="display: flex; justify-content: space-between; width: 100%; box-sizing: border-box;">
                                                <input class="form-control mx-auto d-block text-center" id="ref_no" oninput="checkNo(this)" name="ref_no[]" placeholder="Control No" style="width:80%; height:35px; ">
                                                <button type="button" class= "btn-info clone-button">+</button>
                                            </div>
                                            <br>
                                            <div style="display: flex; justify-content: space-between; width: 100%; box-sizing: border-box;">
                                                <div style="overflow: hidden; text-overflow: ellipsis; height: 100%; box-sizing: border-box;">
                                                    <input class="form-control mx-auto d-block text-center" name="g_lname1[]" placeholder="LastName" style="width:80%; height:35px; ">
                                                    <input class="form-control mx-auto d-block text-center" name="g_lname2[]" value="" placeholder="LastName" style="width:80%; height:35px; margin-top:5px">
                                                </div>
                                                <div style="overflow: hidden; text-overflow: ellipsis; height: 100%; box-sizing: border-box;">
                                                    <input class="form-control mx-auto d-block text-center amount_total" id="amount[]" name="amount[]" value="" oninput="calculateSum()" onkeyup="validateAmount(this)" style="width:90%; height:30px; margin-top:5px" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <a style=" margin-left:290px" href="#" onclick="addManual()">Add Manual</a>
                                </div>
                            <br>

                         
                            <input class=" text-center total_cal" style="width:40%; height:35px; margin-left:210px" value="PHP {{number_format($total,2, '.', ',')}}" readonly>
                            <br>
                            <i><p class="text-danger text-center inform_user"></p></i>
                            <input type="hidden" id="route_no" name="route_no" value="{{$dv->route_no}}">
                            </div>
                        </div>

                    </div>
                   
                
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button style = "background-color:lightgray"  class="btn btn-default" data-dismiss="modal"><i class="typcn typcn-times menu-icon"></i> Close</button>
        <button type="submit" class="btn btn-primary" id ="create_btn">Create Dv2</button>
    </div>
</form>


<script src="{{ asset('admin/js/select2.js?v=').date('His') }}"></script>
<script>
   
    $(document).ready(function () {
        
        $(".for_clone").on("click", ".clone-button", function () {
            var clonedDiv = $(".for_clone .card:first").clone(true);
            $(clonedDiv).find(".clone-button").text("-");
            $(clonedDiv).find(".clone-button").removeClass("clone-button").addClass("remove-clone");
            $(".for_clone").append(clonedDiv);
        });

        $(".for_clone").on("click", ".remove-clone", function () {
            $(this).closest(".card").remove();
        });
    
    });
    function addManual(){
            $('.for_clone').css('display', 'block');       
            // $('.for_clone').toggle(); 
        }

    var controls = @json($control_nos);
    console.log('chaka', controls);

    function checkNo(data){
      
        var val =$(data).val();
        var existsInControls = controls.includes(val);
        console.log('chaka', val);

        if (existsInControls) {
            alert('Control no ' +val+ ' existed already!')
        }
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
    function calculateSum(){
        var amountArray = $('.amount_total').map(function() {
            return parseFloat($(this).val().replace(/,/g, '')) || 0;
        }).get();

        var sum = amountArray.reduce(function(total, current) {
            return total + current;
        }, 0);
        sum = parseFloat(sum.toFixed(2));

        console.log('sum', sum);
        if(sum> {{str_replace(',', '',$dv->total_amount)}}){
            $('.btn-primary').attr('disabled', 'disabled');
            $('.inform_user').text('Calculated Amount is greater than Dv1 total amount!');
        }else if(sum != {{str_replace(',', '',$dv->total_amount)}}){
            $('.btn-primary').attr('disabled', 'disabled');
            $('.inform_user').text('Calculated Amount is not equal to Dv1 total amount!');
        }else{
            $('.btn-primary').removeAttr('disabled');
            $('.inform_user').text('');
        }
        $('.total_cal').val('PHP ' + formatNumberWithCommas(sum));
    }
</script>