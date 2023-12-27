<style>
      
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
          }.row{
            font-size: 12px;
          }
          .modal-title {
            text-align: center !important;
         }
        
      </style>
<form  method="post" action="{{ route('dv.create.save') }}" id ="dv_form"> 
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
                              <!-- <h6 style="margin-top: 10px"> DISBURSEMENT VOUCHER <h6> -->
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
                            <input type="date" asp-for="Date" name="datefield"  id="dateField" style="width: 150px; height: 28px; font-size:8pt" class="ft15" required>
                            <br>
                            <span>DV No.:</span>
                       
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
                    <td height=3% width =12%><b> Payee</td>
                    <td style="width:29%; border-left: 0 "><b> 
                        <select id="facilityDropdown" name="facilityname" onchange="onchangefacility($(this))" style="margin-left:5px;width:260px; height: 28px; font-size: 9pt" class="ft15" required>
                            <option value=""> Select Facility  </option>
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
                        <p style="color:red;" id="facilityAddress"  class="ft15"></p>
                        <input type="hidden" name="facilityAddress" id="facilitaddress"></td>
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
                    <td height=4% width =58%>
                    
                        <p style="text-align:justify;">For reimbursement of medical services rendered to patients under the Medical 
                        Assistance for Indigent Patient Program for
                        <span id="hospitalAddress" name="hospitalname" style="color:red;"></span>
                        per billing statement dated to 
                        
                        <input type="month" id="billingMonth1" name="billingMonth1" asp-for="MonthYearFrom" style="width: 110px; height: 28px; font-size: 8pt;" class="ft15" required>
                
                        <input type="month" id="billingMonth2" name="billingMonth2" asp-for="MonthYearTo" style="width: 110px; height: 28px; font-size: 8pt;" class="ft15" required>

                        in the amount of:</p><br>

                        <div>   
                            <span id="showSAAButton" class="fa fa-plus" style="width:20px; height: 20px; font-size:11px; cursor:pointer; width:50px" onclick="toggleSAADropdowns()">Add</span>
                            <select name="fundsource_id" id="saa1"  onclick="onchangeSaa($(this))" style="margin-left:55px; width:100px; height: 28px;" class="ft15" required>
                                <option value="" data-facilities="">- Select SAA -</option>
                                @foreach($fundsources as $fund)
                                    <option value="{{ $fund->id }}">{{ $fund->saa }}</option>  
                                @endforeach  
                            </select> 
                            <input type="text" name="amount1" id="inputValue1" style="width:120px; height: 28px;" onkeyup="validateAmount(this)"  oninput="fundAmount()" class="ft15" disabled required>
                            <input type="text" name="vatValue1" id="vatValue1" style="margin-left: 8px;width:80px; height: 28px;" class="ft15" readonly required>
                            <input type="text" name="ewttValue1" id="ewttValue1" style="width:80px; height: 28px;" class="ft15"  readonly required>
                        </div>
                        <div>  
                            <span id="RemoveSAAButton" class="fa fa-plus" style=" width:20px; height: 20px; font-size:11px; display: none; cursor:pointer; width:50px" onclick="removeSAADropdowns()">remove</span> 

                            <select  name="fundsource_id_2"  id="saa2"  onchange="onchangeSaa($(this))" style="margin-left: 78px;width:100px; height: 28px; display: none;" class="ft15">
                                <option value="">- Select SAA -</option>
                                <option value=""></option>
                            </select> 
                            <input type="text" name="amount2" id="inputValue2"  style="width:120px; height: 28px; display: none;" oninput="fundAmount()" onkeyup="validateAmount(this)" disabled required>
                            <input type="text" name="vatValue2" id="vatValue2" style="margin-left:8px; width:80px; height: 28px; display: none;" class="ft15" readonly>
                            <input type="text" name="ewtValue2" id="ewtValue2" style="width:80px; height: 28px;display: none;" class="ft15"  readonly>
                        </div>
                        <div>  
                            <span id="RemoveSAAButton1" class="fa fa-plus" style=" width:20px; height: 20px; font-size:11px; display: none; cursor:pointer; width:50px" onclick="removeSAADropdowns1()">remove</span> 
                            <select name="fundsource_id_3"  id="saa3" onchange="onchangeSaa($(this))" style="margin-left:55px; width:100px; height: 28px; display: none" class="ft15">
                                <option value="">- Select SAA -</option>
                            </select>
                            <input type="text" name="amount3" id="inputValue3"  style="width:120px; height: 28px; font-size: 8pt; display:none" oninput="fundAmount()" onkeyup="validateAmount(this)"class="ft15" disabled required>                        
                            <input type="text" name="vatValue3" id="vatValue3" style="margin-left:8px; width:80px; height: 28px; display: none;" class="ft15" readonly>
                            <input type="text" name="ewtValue3" id="ewtValue3" style="width:80px; height: 28px;display: none;" class="ft15"  readonly>
                        </div><br>


                        <input type="hidden" name="pro_id1" id="pro_id1" >
                        <input type="hidden" name="pro_id2" id="pro_id2" >
                        <input type="hidden" name="pro_id3" id="pro_id3" >

                        <input type="hidden" name="fac_id1" id="fac_id1" >
                        <input type="hidden" name="fac_id2" id="fac_id2" >
                        <input type="hidden" name="fac_id3" id="fac_id3" >


                        <input type="hidden" name="saa1_infoId" id="saa1_infoId" >
                        <input type="hidden" name="saa1_discount" id="saa1_discount" >
                        <input type="hidden" name="saa1_utilize" id="saa1_utilize" >

                        <input type="hidden" name="saa2_infoId" id="saa2_infoId" >
                        <input type="hidden" name="saa2_discount" id="saa2_discount">
                        <input type="hidden" name="saa2_utilize" id="saa2_utilize">

                        <input type="hidden" name="saa3_infoId" id="saa3_infoId" >
                        <input type="hidden" name="saa3_discount" id="saa3_discount">
                        <input type="hidden" name="saa3_utilize" id="saa3_utilize" >

                        <input type="hidden" name="save_amount1" id="save_amount1" >
                        <input type="hidden" name="save_amount2" id="save_amount2">
                        <input type="hidden" name="save_amount3" id="save_amount3" >

                        <input type="hidden" name="save_saa1" id="save_saa1" >
                        <input type="hidden" name="save_saa2" id="save_saa2">
                        <input type="hidden" name="save_saa3" id="save_saa3" >

                        <input type="hidden" name="save_fac1" id="save_fac1" >

                        <input type="hidden" id ="for_facility_id" class='ft16'></input>
                        <br>
                        <div>
                            <span style="margin-left:20px" class="saa">Vat : </span>
                            <input type="text" name="vat" id="vat" style="margin-left:32px;width:40px; height: 25px;" oninput="" readonly>
                            <input style="width:80px; height: 25px;" id="forVat_left">
                            <input type="text" id="inputDeduction1" name="deductionAmount1" style="width:100px; height: 25px; font-size: 8pt" readonly required>
                            <span type="hidden" id="balance" name="balance" style=" height: 25px; font-size: 8pt" readonly></span>
                        </div><br>
                        <div padding-top:10px>
                            <span style="margin-left:20px; margin-top:10px;" class="saa">Ewt : </span>
                            <input type="text" name="ewt" id="ewt" style="margin-left:31px;width:40px; height: 25px;" class="ft15" oninput="" readonly>
                            <input style="margin-left:0px; width:80px; height: 25px;" id="forEwt_left">
                            <input type="number" id="inputDeduction2" name="deductionAmount2" style="width:100px; height: 25px; font-size: 8pt" min="1" readonly required>
                            <span type="hidden" id="per_deduct" name="per_deduct" style="width:120px; height: 25px; font-size: 8pt" readonly></span>
                        </div><br><br>
                        <div>
                            <span class="saa">Ref No:</span>
                            <input type="text" name="control_no" id="control_no" style="width:185px; height: 28px;" class="ft15" required>
                        </div>

                        <br><br>
                        <span style="margin-left:200px; font-weight:bold">Amount Due</span>
                    
                    </td>
                    <td style="width:14%; border-left: 0 " ></td>
                    <td style="width:14%; border-left: 0 " ></td>
                    <td style="width:14%; border-left: 0 " >
                    <div class= "header">
                      <br><br><br><br><br><br><br><br>
                      <p class="ft15 total"></p>
                      <input type="hidden" name="total" id="totalInput" class="ft15 total">
                      <br><br><br>
                      <p class="ft15 totalDeduction"></p>
                      <input type="hidden" name="totalDeduction" id="totalDeductionInput" class="ft15 totalDeduction">
                      <br><br><br><br><br>
                      <span style="text-align:center;">______________</span><br>
                      <p class="ft15 overallTotal" ></p>
                      <input type="hidden" name="overallTotal1" id="overallTotalInput" class="ft15 overallTotal">
                    </div> 
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
                      &nbsp;&nbsp;&nbsp;&nbsp;<span>Subsidy / Others</span><br>
                      <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Due to BIR</span><br> 
                      <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; CIB-MDS</span> 
                    </td>
                    <td style="width:20%; border-left: 0 " >
                      <br>
                      <span>50214990</span><br>
                      <span>20201010</span><br> 
                      <span>10104040</span> <br><br>
                    </td>
                    <td style="width:20%; border-left: 0 ; text-align:right; vertical-align:top" >
                      <br><p id="totalDebit" class="ft15"></p>
                    </td>
                    <td style="width:20%; border-left: 0 ; text-align:right; vertical-align:top" >
                      <br>
                      <p id ="DeductForCridet" class="ft15"></p>
                      <p id="OverTotalCredit" class="ft15"></p>
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
                      <td width =38%><b>ANGIELYN T. ADLAON, CPA, MBA</td>
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
        <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal"><i class="typcn typcn-times"></i>Close</button>
        <button type="submit" class="btn btn-sm btn-primary"><i class="typcn typcn-tick menu-icon"></i>Submit</button>
       
    </div>
</div>
</div>
</div>
</form>

<script>

    $('#facilityDropdown').change(function(){
       $('#inputValue1').prop('disabled', true);
         setTimeout(function() {
            $('#inputValue1').prop('disabled', false);
         }, 500);
    });
    
    $('#saa2').change(function(){
        $('#inputValue2').prop('disabled', false);
        // $('#saa3').prop('disabled', true);
        setTimeout(function(){
            // $('#inputValue2').prop('disabled', false);
        }, 500);
    });
    $('#saa3').change(function(){
      $('#inputValue3').prop('disabled', false);
      setTimeout(function(){
        // $('#inputValue3').prop('disabled', false);
      }, 500);
    });


          // Function to update the total and format with commas
        function updateTotal() {
            // Get input values
            var amount1 = parseFloat('inputValue1') || 0;
            var amount2 = parseFloat('inputValue2') || 0;
            var amount3 = parseFloat('inputValue3') || 0;

            var deduct1 = parseFloat('inputDeduction1') || 0;
            var deduct2 = parseFloat('inputDeduction2') || 0;
            //calculate the deduction 
             var totaldeduction = deduct1 + deduct2;
            // Calculate sum
            var total = amount1 + amount2 + amount3;
            //add deduction and sum
             var totalAmount = total - totaldeduction ;
            // Format with commas for integer part
            var formattedTotal = total.toLocaleString('en-US', { maximumFractionDigits: 2 });
            var formattedDecduction = totaldeduction.toLocaleString('en-Us', {maximumFractionDigits: 2});
            var formattedTotalAmount = totalAmount.toLocaleString('en-Us', {maximumFractionDigits: 2});
            // Update the total display
            document.querySelector('.total').innerText = '' + formattedTotal;
            document.querySelector('#totalInput').value = '' + formattedTotal;

            document.querySelector('.totalDeduction').innerText = '' + formattedDecduction;
            document.querySelector('#totalDeductionInput').value = '' + formattedDecduction;

           // document.querySelector('.overallTotal').innerText = '' + formattedTotalAmount;
           // document.querySelector('#overallTotalInput').value = '' + formattedTotalAmount;
        }

        // Function to get the numeric value from the formatted input
        // function getFormattedValue(elementId) {
        //     var inputElement = document.getElementById(elementId); 
        //     // var numericValue = parseFloat(inputElement.value.replace(/[^0-9.]|\.(?=.*\.)/g, '')) || 0;
        //     // inputElement.value = numericValue.toLocaleString('en-US', { maximumFractionDigits: 2 });
        //     // return numericValue;
        // }

        function validateAmount(element) {
            var cleanedValue = element.value.replace(/[^\d.]/g, '');
            if (cleanedValue !== '0') {
                cleanedValue = cleanedValue.replace(/^0+/, '');
            }

            var numericValue = parseFloat(cleanedValue);

            if (!isNaN(numericValue) || cleanedValue === '' || cleanedValue === '.') {
                element.value = cleanedValue.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            } else {
                element.value = ''; 
            }
        }

        // Attach the updateTotal function to input change events
        document.getElementById('inputValue1').addEventListener('input', updateTotal);
        document.getElementById('inputValue2').addEventListener('input', updateTotal);
        document.getElementById('inputValue3').addEventListener('input', updateTotal);

        document.getElementById('inputDeduction1').addEventListener('input', updateTotal);
        document.getElementById('inputDeduction2').addEventListener('input', updateTotal);

        // Initial update
        updateTotal();
</script>

