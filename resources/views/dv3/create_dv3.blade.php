<style>
    .custom-dropdown {
      position: relative;
    }
    #dropdownContent1,#dropdownContent2,
    #dropdownContent3 {
      display: none;
      position: absolute;
      border: 1px solid #ccc;
      max-height: 150px; /* Adjust as needed */
      overflow-y: auto;
      background-color: white;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      z-index: 1;
      width:120px;
    }
    #dropdownContent1 label,#dropdownContent2 label,
    #dropdownContent3 label {
      display: block;
      padding: 8px;
      cursor: pointer;
    }
    #dropdownContent1 label:hover,#dropdownContent2 label:hover,
    #dropdownContent3 label:hover {
        background-color: #f1f1f1;
    }
    .header{
      font-size: 12px;
      font-weight: normal;
      text-align:center;
    }
    table td{
      font-size: 11px;
    }
    .box-container {
      display: flex;
    }
    .box {
        width: 23px;
        height: 16px;
        border: 1px solid black;
        margin-left: 20px;
        display: inline-block;
        vertical-align: middle;
    }
    .label {
        font-size: 12px;
        display: inline-block;
        margin-right: 8pxpx;
        margin-left: 10px;
    }
    .saa{
      margin-left:60px;
    }
    .row{
      font-size: 12px;
    }
    .modal-title {
      text-align: center !important;
    }
    .hide {
        display: none;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: green;
        color: white;
    }
   
</style>

<form  method="post" action="{{ route('dv3.save.create') }}" id ="dv_form"> 
    @csrf   
    <input type="hidden" name="dv" id ="dv" value="">
    <input type="hidden" name="dv_id" id="dv_id" value="">
    <div class="clearfix"></div>
        <div class="new-times-roman table-responsive">
          <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <table cellpadding="0" cellspacing="0" width="100%" style="margin-top: 10px">
                        <tr>
                            <td width="23%" style="text-align: center; border-right:none"><img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/doh-logo.png'))) }}" width="40%" ></td>
                            <td width="54%" style="border-left:none; border-right:none; ">
                              <div class="header" style="margin-top: 20px">
                                  <span style="margin-top: 10px">Republic of the Philippines</span> <br>
                                  Department of Health<br>
                                  <strong> CENTRAL VISAYAS FOR HEALTH DEVELOPMENT</strong> <br>
                                  <small>Osmeña Boulevard, Sambag II, Cebu City, 6000 Philippines</small> <br>
                                  <small>Regional Director's Office Tel. NO. (032) 253-6335 Fax No. (032) 254-0109</small><br>
                              </div>
                            </td>
                          <td width="23%" style="text-align: right; border-left:none;"><small><i><br><br><br><br><br><br><br><br><br>Appendix 32&nbsp;&nbsp;&nbsp;</i></small> </td>
                        </tr>
                    </table>
                    <table border="2" style="width: 100%;" >
                        <tr>
                            <td width="75%">
                                <p style="padding: 10px;">
                                    DISBURSEMENT VOUCHER <br /><b></b>
                                </p>
                            </td>
                            <td width="25%">
                                <span style="margin-bottom: 20px">Fund Cluster :</span><br>
                                <span style="margin-top: 20px">Date: </span>
                                &nbsp;&nbsp;&nbsp;&nbsp;<input type="date" asp-for="Date" name="dv3_date"  id="dv3_date" style="width: 150px; height: 28px; font-size:8pt" class="ft15" value="{{(new DateTime())->format('Y-m-d')}}" required>
                                <br>
                                <div>
                                    <span>DV No:</span>
                                    @if(Auth::user()->userid == 1027 || Auth::user()->userid == 2660)
                                      &nbsp;<input type="text" name="dv_no" id="dv_no" style="width:150px; height: 28px;" class="ft15" required>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    </table>
                    <table border="2" style="width: 100%;border-top: 0px;">
                        <tr height=30px>
                            <td  width =12% height= 3%><b> Mode of Payment</td>
                            <td style="width:85%; border-left: 0 " >
                            <div class="box-container">
                              <span class="box"></span>
                              <span class="label">MDS Check</span>
                              <span class="box"></span>
                              <span class="label">Commercial Check</span>
                              <span class="box"></span>
                              <span class="label">ADA</span>
                              <span class="box"></span>
                              <span class="label">Others (Please specify)  _________________________</span>
                            </div>
                            </td>
                        </tr>
                    </table>
                    <table border="2" style="width: 100%;">
                        <tr>
                            <td height=4% width =12%><b> Payee</td>
                            <td style="width:29%; border-left: 0 "><b> 
                                <select id="dv3_facility" class="dv3_facility" name="dv3_facility" onchange="getFacility($(this))" style="margin-left:5px;width:260px;" required>
                                  <option value="">- Select Facility -</option>
                                  @foreach($facilities as $facility)
                                    <option value="{{$facility->id}}">{{$facility->name}}</option>
                                  @endforeach  
                                </select>
                            </td>
                            <td style="width:28%; border-left: 0 " >
                                <span?>Tin/Employee No. :</span>
                            </td>
                            <td style="width:27%; border-left: 0 " >
                                <span>ORS/BURS No. :</span>
                            </td>
                          </tr>
                    </table>
                    <table border="2" style="width: 100%;" >
                        <tr>
                            <td style="height:30px;"width =12.3% ><b>Address</td>
                            <td style="width:88%; border-left: 0 "><b> 
                              <p style="color:red;" class="dv3_address"></p>
                            </td>
                        </tr>
                    </table>
                    <table border="2" style="width: 100%;border-top: 0px;" >
                        <tr class="header">
                            <td height=2.5% width =58%> Particulars</td>
                            <td style="width:15%; border-left: 0 " >Responsibility Center</td>
                            <td style="width:15%; border-left: 0 " >MFO/PAP</td>
                            <td style="width:15%; border-left: 0 " >Amount</td>
                        </tr>
                        <tr style="text-align:left;" >
                            <td height="" width="58%" style="vertical-align: top; border: 1px solid #000;">
                                <p style="text-align:justify;vertical-align:top;">To transfer medical assistance program funds for 
                                <span class="hospitalAddress" name="hospitalname" style="color:red;"></span>
                                 in the amount of:</p><br>
                                <div class="container">
                                    <div style="display: flex; align-items: center;" class="clone_saa">
                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                        <select name="fundsource_id[]" id="dv3_saa1" style="width:200px;" class="dv3_saa" onchange="saaValue($(this))" disabled required>
                                            <option value="" data-facilities="" style="background-color:green">- Select SAA -</option> 
                                        </select> 
                                        <input type="hidden" name="info_id[]" id="info_id" class="info_id">
                                        <div class="custom-dropdown" style="margin-left: 8px;">
                                            <input type="text" name="amount[]" id="amount[]" style="width:150px; height: 42px;" onkeyup="validateAmount(this)" oninput="checkedAmount($(this))" class="amount" required autocomplete="off" disabled>
                                        </div>
                                        <!-- <input type="text" name="vat_amount[]" id="vat_amount" class="vat_amount" style="margin-left: 8px; width: 80px; height: 42px;" class="ft15" readonly required>
                                        <input type="text" name="ewt_amount[]" id="ewt_amount" class="ewt_amount" style="width: 80px; height: 42px;" class="ft15" readonly required> -->
                                        <button type="button" id="add_more" class="add_more" class="fa fa-plus" style="border: none; width: 20px; height: 42px; font-size: 11px; cursor: pointer; width: 30px;" disabled>+</button>
                                    </div>
                                </div>
                                <br><br>
                                <span style="margin-left:50px; font-weight:bold;font-size:12px">Amount Due</span>
                            </td>
                            <td style="width:14%; border-left: 0 " ></td>
                            <td style="width:14%; border-left: 0 " ></td>
                            <td style="width:14%; border-left: 0; vertical-align: top; text-align:center; ">
                                <br><br><br><br>
                                <label class="total_amount"></label>
                                <input type="hidden" class="total_amount" name="total_amount" id="total_amount"><br>
                                <label class="deduction"></label><br><br>
                                <!-- <label>____________</label><br> -->
                                <label class="remaining"></label>
                            </td>
                          </tr>
                    </table>
                    <table border="2" style="width: 100%;">
                        <tr>
                            <td width =15%>
                            <dv>  
                              <span>A. Certified: Expenses/Cash Advance necessary, lawful and incurred under my direct supervision.</span>
                              <br><br><br>
                              <div style="display:inline-block;">
                                <span style="margin-left:350px"><strong>SOPHIA M. MANCAO, MD, DPSP, RN-MAN<strong></span>
                              </div>
                              <br>
                              <span style="margin-left:420px">Director III</span>
                            </dv>
                            </td>
                        </tr>
                    </table>
                    <table border="2" style="width: 100%;" >
                        <tr>
                            <td height=2% width =15%><strong>B. Accounting Entry:</strong></td>
                        </tr>
                    </table>
                    <table border="2" style="width: 100%;" >
                        <tr class="header">
                            <td height=2.5% width =40%>Account Title</td>
                            <td style="width:20%; border-left: 0 " >Uacs Code</td>
                            <td style="width:20%; border-left: 0 " >Debit</td>
                            <td style="width:20%; border-left: 0 " >Credit</td>
                          </tr>
                          <tr class="header">
                            <td height=6% width =40% style="text-align : left;">
                              <br><br><br><br><br><br>
                            </td>
                          </tr>
                    </table>
                    <table border="2" style="width: 100%;" >
                        <tr>
                              <td height=2% width =53%><strong>C. Certified:</strong></td>
                              <td width =47%><strong>D. Approved for Payment:</strong></td>
                          </tr>
                          <tr>
                              <td height=7%>
                                <span class="box" style="margin-bottom:5px;"></span>
                                <span class="label">Cash available</span> <br>
                                <span class="box" style="margin-bottom:5px;"></span>
                                <span class="label">Subject to Authority to Debit Account (when applicable)</span>
                                <br>
                                <span class="box"></span>
                                <span class="label">Supporting documents complete and amount claimed proper</span>
                              </td>
                              <td></td>  
                          </tr>
                    </table>
                    <table border="2" style="width: 100%;" >
                    <tr class="header">
                              <td height=30px width =15%>Signature</td>
                              <td width =38%></td>
                              <td height=30px width =15%>Signature</td>
                              <td width =32%></td> 
                          </tr>
                          <tr class="header">
                              <td height=30px width =15%>Printed Name</td>
                              <td width =38%><b>ANGIELINE T. ADLAON, CPA, MBA</td>
                              <td height=30px width =15%>Printed Name</td>
                              <td width =32%><b>JAIME S. BERNADAS, MD, MGM, CESO III</td>
                          </tr>
                          <tr class="header">
                              <td height=35px width =15%>Position</td>
                              <td width =38%>
                                <table width=100% style="text-align:center" border=0>
                                  <tr>
                                    <td  style="border-bottom: 1px solid black">Head, Accounting Section</td>
                                  </tr>
                                  <tr>
                                    <td>Head, Accounting Unit/Authorized Representative</td>
                                  </tr>
                              </table>
                              </td>
                              <td height=35px width =15%>Position</td>
                              <td width =32%>
                                <table width=100% style="text-align:center" border=0>
                                    <tr>
                                      <td style="border-bottom: 1px solid black">Director IV</td>
                                    </tr>
                                    <tr>
                                      <td >Agency Head/Authorized Representative</td>
                                    </tr>
                                </table>
                              </td>
                          </tr>
                          <tr class="header">
                              <td height=30px width =15%>Date</td>
                              <td width =38%></td>
                              <td height=30px width =15%>Date</td>
                              <td width =32%></td>
                          </tr>
                    </table>
                    <table border="2" style="width: 100%;" >
                        <tr>
                            <td height=2.5% ><strong>E. Receipt of Payment</strong></td>
                        </tr>
                    </table>
                    <table border="2" style="width: 100%;" >
                        <tr>
                              <td height=30px width =15%>Check/ADA No.:</td>
                              <td width =25%></td>
                              <td width =15%>Date:</td>
                              <td width =30%>Bank Name & Account Number:</td>
                              <td width =15%>JEV No.</td>
                          </tr>
                          <tr>
                              <td height=30px width =15%>Signature:</td>
                              <td width =25%></td>
                              <td width =15%>Date:</td>
                              <td width =30%>Printed Name:</td>
                              <td width =15%>Date</td>
                          </tr>
                    </table>
                    <table border="2" style="width: 100%;" >
                        <tr>
                            <td height=30px>Official Receipt No. & Date/Other Documents</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="modal-footer" id="dv_footer"> 
                <!-- <button style="background-color:lightgray; border-radius:0px" type="button" class="btn btn-sm btn" data-dismiss="modal"><i class="typcn typcn-times"></i>Close</button> -->
                <button style="border-radius:0px" type="submit" id="submitBtn" class="btn btn-sm btn-success"><i class="typcn typcn-tick menu-icon"></i>Submit</button>
                <input type="hidden" name="group_id" id="group_id" >
            </div>
          </div>
      </div>
    </div>
</form>

<script src="{{ asset('admin/js/select2.js?v=').date('His') }}"></script>
<script>

    $('.dv3_facility').select2();
    $('.dv3_saa').select2();
    $(document).ready(function() {
        $(document).off('click', '.container .clone_saa .add_more').on('click', '.container .clone_saa .add_more', function () {
            $('.loading-container').show();
            var $this = $(this);
            setTimeout(function () {
                var url = "{{ route('saa.get', ['id' => ':id']) }}".replace(':id', facility_id);
                $.get(url, function (result) {
                    $this.closest('.container').append(result);
                    $('.loading-container').hide();
                });
            }, 1);
        });

        $(document).on('click', '.container .remove_saa', function () {
            $(this).closest('.clone_saa').remove();
            cal();
        });
    });

    function saaValue(element){
        var row = $(element).closest('div.clone_saa');   
        var amountInput = row.find('input.amount').val('');
        var info_input = row.find('input.info_id').val(row.find('select.dv3_saa option:selected').attr('dataproponentInfo_id'));
    }

    var timer;
    var vat = 0, ewt = 0;

    function checkedAmount(element){
        clearTimeout(timer);
        value = Number(element.val().replace(/[^\d.]/g, ''));
        timer = setTimeout(function () {
            var row = $(element).closest('div.clone_saa');
            var info_id = row.find('select.dv3_saa option:selected').attr('dataproponentInfo_id');
            $.get("{{ url('/balance')}}", function(result) {
                var allocated_funds = (result.allocated_funds.find(item =>item.id == info_id)|| {}).remaining_balance|| 0;
                allocated_funds = Number(allocated_funds.replace(/[^\d.]/g, ''));
                if(value > allocated_funds){
                    Lobibox.alert('error',{
                        size : 'mini',
                        msg : 'Make sure inputted amount is not greater than allocated balance!'
                    });
                    row.find('input.amount').val('');
                }else{
                    // if(vat> 3){
                    //     row.find('input.vat_amount').val((value/1.12 * vat / 100). toFixed(2));
                    //     row.find('input.ewt_amount').val((value/1.12 * ewt / 100). toFixed(2));
                    // }else{
                    //     row.find('input.vat_amount').val((value * vat / 100). toFixed(2));
                    //     row.find('input.ewt_amount').val((value * ewt / 100). toFixed(2));
                    // }
                }

            cal();

            });
        },500);
    }

    function cal(){
        var allValues = [];
        var total = 0;
        $('.amount').each(function() {
            if($(this).val() != ''){
                allValues.push($(this).val());
                total = total + Number($(this).val().replace(/[^\d.]/g, ''));
            }
        });
        // if(vat> 3){
        //     $('.deduction').text(formatToLocaleString(total/1.12 * (Number(vat) + Number(ewt)) / 100));
        //     $('.remaining').text(formatToLocaleString(total- (total/1.12 * (Number(vat) + Number(ewt)) / 100)));
        // }else{
        //     $('.deduction').text(formatToLocaleString((total * (Number(vat) + Number(ewt)) / 100). toFixed(2)));
        //     $('.remaining').text(formatToLocaleString(total- (total * (Number(vat) + Number(ewt)) / 100)));
        // }
        $('.remaining').text(formatToLocaleString(total));
        // $('.total_amount').text(formatToLocaleString(total));
        $('.total_amount').val(total);
    }

    function formatToLocaleString(value, locale = 'en-US', options = { minimumFractionDigits: 2, maximumFractionDigits: 2 }) {
        return value.toLocaleString(locale, options);
    }

    function validateAmount(element) {
        if (event.keyCode === 32) {   
            event.preventDefault();
        }
        var cleanedValue = element.value.replace(/[^\d.]/g, '');
        var numericValue = parseFloat(cleanedValue);

        if ((!isNaN(numericValue) || cleanedValue === '' || cleanedValue === '.') &&
            !(cleanedValue.length === 1 && cleanedValue[0] === '0')) {
                element.value = cleanedValue.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }else{
            element.value = '';
        }
    }

    var facility_id = 0;

    function getFacility(data){
        if(data.val()) {
            facility_id = data.val();
            handleChangesF(facility_id);
            getVat(facility_id);
        }
        $('.add_more').removeAttr('disabled');
        $('.dv3_saa').removeAttr('disabled');
        $('.amount').removeAttr('disabled');
      }
    
    function getVat(facility_id){
        // $.get("{{ url('/getvatEwt').'/' }}"+facility_id, function(result) {
        //     if(result == 0){
        //         alert('Please update VAT and EWT of this facility first!');
        //     }else{
        //         vat = result.vat;
        //         ewt = result.Ewt
        //     }
        // });
    }

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

            $('.dv3_saa').append(option.clone());
        });
    }

    var all_facilities = @json($facilities);

    function handleChangesF(facility_id){
        $.get("{{ url('list/fundsources').'/' }}"+facility_id, function(result) {

            $('.dv3_address').text(result.facility.address);
            $('.hospitalAddress').text(result.facility.name);
            $('#for_facility_id').val(facility_id);

            var data_result = result.info;
            var text_display;

            $('#saa2').append($('<option>', {value: '',text: 'Select SAA'}));
            $('#saa3').append($('<option>', {value: '',text: 'Select SAA'}));
            var first = [],sec = [],third = [],fourth = [],fifth = [],six = [];
            $.each(data_result, function(index, optionData){
                var rem_balance = parseFloat(optionData.remaining_balance.replace(/,/g, '')).toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2});

                var check_p = 0;  

                var id = optionData.facility_id;
               
                if(optionData.facility !== null){
                    if(optionData.facility.id == facility_id){
                        text_display = optionData.fundsource.saa + ' - ' + optionData.proponent.proponent + ' - ' + optionData.facility.name + ' - ' + rem_balance;
                    }else{
                        text_display = optionData.fundsource.saa + ' - ' + optionData.proponent.proponent + ' - ' + optionData.facility.name + ' - ' + rem_balance;
                        check_p = 1;
                    } 
                }else{
                    var data_id = JSON.parse(optionData.facility_id).map(id => parseInt(id, 10));
                    var f_name = '';
                    data_id.forEach(id => {
                        var facility = all_facilities.find(facility => facility.id === id);
                        f_name = f_name + ' - ' + facility.name + ' - ';
                    });
                    
                    if(id.includes('702')){
                        check_p = 1;
                        text_display = optionData.fundsource.saa + ' - ' + optionData.proponent.proponent + f_name + rem_balance;
                    }else{
                        text_display = optionData.fundsource.saa + ' - ' + optionData.proponent.proponent + f_name + rem_balance;
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

                $('#saa1').select2({
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

