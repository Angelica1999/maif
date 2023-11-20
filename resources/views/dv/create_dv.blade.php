<style>


    p {
        margin: 0;
        padding: 0;
    }

    .ft10 {
        font-size: 16px;
        font-family: Helvetica;
        color: #000000;
    }

    .ft11 {
        font-size: 11px;
        font-family: Times;
        color: #000000;
    }

    .ft12 {
        font-size: 11px;
        font-family: Times;
        color: #000000;
    }

    .ft13 {
        font-size: 10px;
        font-family: Times;
        color: #000000;
    }

    .ft14 {
        font-size: 16px;
        font-family: Times;
        color: #000000;
    }

    .ft15 {
        font-size: 11px;
        font-family: Helvetica;
        color: #000000;
    }

    .ft16 {
        font-size: 10px;
        font-family: Helvetica;
        color: #000000;
    }

    .ft17 {
        font-size: 11px;
        font-family: Helvetica;
        color: #000000;
    }

    .ft18 {
        font-size: 11px;
        line-height: 13px;
        font-family: Helvetica;
        color: #000000;
    }
</style>
<!-- <img src="{{ asset('images/target004.png') }}"  width="892" height="1263" class="img-responsive" /> -->
<!-- <div id="page1-div" style="position:relative;width:892px;height:1280px;"> -->
<form action="Maiff" method="post">     
    <div id="page1-div" style="position:relative;width:852px;height:1280px;">
        <img src="{{ asset('images/target005.png') }}"  width="892" height="1263" class="img-responsive" />
            <p style="position:absolute;top:23px;left:54px;white-space:nowrap" class="ft10">&#160;</p>
            <p style="position:absolute;top:43px;left:295px;white-space:nowrap" class="ft11">Republic of Philippines</p>
            <p style="position:absolute;top:63px;left:300px;white-space:nowrap" class="ft11">Department of Health</p>
            <p style="position:absolute;top:82px;left:164px;white-space:nowrap" class="ft12"><b>CENTRAL VISAYAS CENTER FOR HEALTH DEVELOPMENT</b></p>
            <p style="position:absolute;top:101px;left:214px;white-space:nowrap" class="ft13">Osmeña Boulevard, Sambag II, Cebu City, 6000 Philippines</p>
            <p style="position:absolute;top:120px;left:176px;white-space:nowrap" class="ft13">Regional Director's Office Tel. No. (032) 253-6335 Fax No. (032) 254-0109</p>
            <p style="position:absolute;top:140px;left:239px;white-space:nowrap" class="ft14"><b>DISBURSEMENT VOUCHER</b></p>
            <p style="position:absolute;top:30px;left:600px;white-space:nowrap" class="ft15">Fund Cluster :</p>
            <p style="position:absolute;top:27px;left:696px;white-space:nowrap" class="ft15"></p>
            <p style="position:absolute;top:41px;left:738px;white-space:nowrap" class="ft15"></p>
            <p style="position:absolute;top:74px;left:600px;white-space:nowrap" class="ft15">Date :</p>
            <input type="date" asp-for="Date"  id="dateField" style="position:absolute;top:70px;left:650px;white-space:nowrap; width: 150px; height: 28px; font-size:8pt" class="ft15">
            <p style="position:absolute;top:113px;left:600px;white-space:nowrap" class="ft15">DV No :</p>
            <p style="position:absolute;top:113px;left:728px;white-space:nowrap" class="ft15"></p>
            <p style="position:absolute;top:180px;left:20px;white-space:nowrap" class="ft18">Mode Of<br />Payment</p>
            <p style="position:absolute;top:180px;left:178px;white-space:nowrap" class="ft15">MDS Check</p>
            <p style="position:absolute;top:180px;left:311px;white-space:nowrap" class="ft18">Commercial<br />Check</p>
            <p style="position:absolute;top:180px;left:443px;white-space:nowrap" class="ft15">ADA</p>
            <p style="position:absolute;top:180px;left:566px;white-space:nowrap" class="ft15">Others (Please specify) ____________</p>
            <p style="position:absolute;top:217px;left:20px;white-space:nowrap" class="ft15">Payee</p>
  
        <select id="facilityDropdown" name="facility_id" id ="facility_id" style="position:absolute;top:215px;left:140px;white-space:nowrap; width:260px; height: 28px; font-size: 9pt" class="ft15">
            <option value=""> - Select Facility - </option>
            <!-- <option value="" ></option> -->
            <!-- @foreach ($facilities as $facility)
                <option value="{{ $facility->id }}" >{{ $facility->name }}</option>
            @endforeach -->
        </select>
            <p style="position:absolute;top:219px;left:434px;white-space:nowrap" class="ft18">Tin/Employee No.:<br /></p>
            <p style="position:absolute;top:219px;left:655px;white-space:nowrap" class="ft15">ORS/BURS No.:</p>
            <p style="position:absolute;top:260px;left:20px;white-space:nowrap" class="ft15">Address</p>
        <p style="position:absolute;top:260px;left:150px;white-space:nowrap; color:red; font-weight:bold" id="facilityAddress"  name="facilityAddress" class="ft15"></p> 
        <p style="position:absolute;top:260px;left:150px;white-space:nowrap; color:red; font-weight:bold" id="facilityAddress" name="facilityAddress" class="ft15"></p>
        <input type="hidden" id="facilityAddressHidden" name="facilityAddress" value="" />
            <p style="position:absolute;top:300px;left:246px;white-space:nowrap" class="ft15">Particulars</p>
            <p style="position:absolute;top:295px;left:522px;white-space:nowrap" class="ft15">Responsibility</p>
            <p style="position:absolute;top:310px;left:543px;white-space:nowrap" class="ft15">Center</p>
            <p style="position:absolute;top:300px;left:640px;white-space:nowrap" class="ft15">MFO/PAP</p>
            <p style="position:absolute;top:300px;left:759px;white-space:nowrap" class="ft15">Amount</p>
            <p style="position:absolute;top:345px;left:39px;white-space:nowrap" class="ft16">
                For reimbursement of medical services rendered to patients under the Medical Assistance for <br />
            </p>
            <p style="position:absolute;top:365px;left:39px;white-space:nowrap" class="ft16">
                Indigent Patient Program for
                <span id="hospitalAddress" style="color:red;"></span>
            </p>
            <p style="position:absolute;top:385px;left:39px;white-space:nowrap" class="ft16">
                per billing statement dated
                <input type="month" id="billingMonth1" asp-for="MonthYearFrom" style="width: 110px; height: 28px; font-size: 8pt;" class="ft15">
                <!-- Second Month Picker -->
            <input type="month" id="billingMonth2" asp-for="MonthYearTo" style="width: 110px; height: 28px; font-size: 8pt;" class="ft15">
                    in the amount of:
            </p>

            <select id="fundsourceDropdown" name="fundsource_id" id="saa1" onchange="onchangeSaa($(this))" style="position:absolute;top:440px;left:100px;white-space:nowrap; width:150px; height: 20px;" class="ft15">
                <option value="" data-facilities="">- Select SAA -</option>
                @foreach ($fundsources as $fundsource)
                    <option value="{{ $fundsource->id }}" data-facilities="{{ json_encode($fundsource->facilities) }}">{{ $fundsource->saa }}</option>
                @endforeach
            </select>

            <input type="text" name="amount1" id="inputValue1" style="position:absolute;top:440px;left:270px;white-space:nowrap; width:150px; height: 20px;" class="ft15">
                <br />
                <br />
            <span id="showSAAButton" class="fa fa-plus" style="position:absolute;top:445px;left:75px; width:20px; height: 20px; cursor:pointer" onclick="toggleSAADropdowns()"></span>
         
            <select id="fundsourceDropdown" name="fundsource_id"  id="saa2" style="position:absolute;top:460px;left:100px;white-space:nowrap; width:150px; height: 20px" class="ft15"  disabled>
              <option value="">- Select SAA -</option>
                @foreach ($fundsources as $fundsource)
                    <option value="{{ $fundsource->id }}">{{ $fundsource->saa }}</option>
                @endforeach
             </select>
           <input type="text" name="amount2" id="inputValue2" style="position:absolute;top:460px;left:270px;white-space:nowrap; width:150px; height: 20px; font-size: 8pt;" class="ft15" disabled>

           <select id="fundsourceDropdown1" name="fundsource_id"  id="saa3" style="position:absolute;top:480px;left:100px;white-space:nowrap; width:150px; height: 20px" class="ft15" disabled>
             <option value="">- Select SAA -</option>
                @foreach ($fundsources as $fundsource)
                    <option value="{{ $fundsource->id }}">{{ $fundsource->saa }}</option>
                @endforeach
             </select>
           <input type="text" name="amount3" id="inputValue3" style="position:absolute;top:480px;left:270px;white-space:nowrap; width:150px; height: 20px; font-size: 8pt;" class="ft15" disabled>

        <select id="deduction1" name="deduction1"  style="position:absolute;top:520px;left:100px;white-space:nowrap; width:150px; height: 20px" class="ft15">
         <option value="">- Select Vat -</option>
         @foreach($VatFacility as $facilityvat)
                <option value="{{ $facilityvat->id }}">{{ $facilityvat->vat }}%</option>
         @endforeach
        </select>
            <input type="text" id="inputDeduction1" asp-for="DeductionAmount1" style="position:absolute;top:520px;left:270px;white-space:nowrap; width:150px; height: 20px; font-size: 8pt" class="ft15">

        <select id="deduction2" name="deduction2" style="position:absolute;top:540px;left:100px;white-space:nowrap; width:150px; height: 20px" class="ft15">
        <option value="">- Select Ewt -</option>
        @foreach($ewtFacility as $facilityewt)
          <option value="{{  $facilityewt->id }}">{{ $facilityewt->Ewt }}%</option>

         {{-- <option value="{{  $facilityewt->id }}">{{ $facilityewt->Ewt % 1 == 0? number_format($facilityewt->Ewt, 0) : $facilityewt->Ewt }}%</option> --}}
         @endforeach
        </select>
        <input type="text" id="inputDeduction2" asp-for="DeductionAmount2" style="position:absolute;top:540px;left:270px;white-space:nowrap; width:150px; height: 20px; font-size: 8pt" class="ft15">
            <p style="position:absolute;top:568px;left:69px;white-space:nowrap; font-weight:bold;" class="ft16">  &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; Amount Due</p>
            <p style="position:absolute;top:359px;left:746px;white-space:nowrap" class="ft15"></p>
            <p style="position:absolute;top:440px;left:755px;white-space:nowrap" class="ft15 total"></p>
            <p style="position:absolute;top:521px;left:755px;white-space:nowrap" class="ft15 totalDeduction"></p>
            <p style="position:absolute;top:551px;left:760px;white-space:nowrap" class="ft15">_________________</p>
            <p style="position:absolute;top:568px;left:755px;white-space:nowrap; font-weight:bold" class="ft15 overallTotal"></p>
            <p style="position:absolute;top:615px;left:20px;white-space:nowrap" class="ft17"><b>A. Certified: Expenses/Cash Advance necessary, lawful and incurred under my direct supervision.</b></p>
            <p style="position:absolute;top:652px;left:57px;white-space:nowrap; font-size:9pt" class="ft17"><b>&#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160;  SOPHIA M. MANCAO, MD, DPSP, RN-MAN </b></p>
            <p style="position:absolute;top:666px;left:60px;white-space:nowrap; font-size: 8pt" class="ft17">&#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; Director III</p>
            <p style="position:absolute;top:687px;left:20px;white-space:nowrap" class="ft17"><b>B. Accounting Entry:</b></p>
            <p style="position:absolute;top:709px;left:211px;white-space:nowrap" class="ft15">Account Title</p>
            <p style="position:absolute;top:709px;left:478px;white-space:nowrap" class="ft15">Uacs Code</p>
            <p style="position:absolute;top:709px;left:627px;white-space:nowrap" class="ft15">Debit</p>
            <p style="position:absolute;top:709px;left:755px;white-space:nowrap" class="ft15">Credit</p>
            <p style="position:absolute;top:830px;left:20px;white-space:nowrap" class="ft17"><b>C. Certified:</b></p>
            <p style="position:absolute;top:830px;left:449px;white-space:nowrap" class="ft17"><b>D. Approved for Payment:</b></p>
            <p style="position:absolute;top:860px;left:135px;white-space:nowrap" class="ft15">Cash available</p>
            <p style="position:absolute;top:900px;left:135px;white-space:nowrap" class="ft18">Subject to Authority to Debit Account (when<br />applicable)</p>
            <p style="position:absolute;top:940px;left:135px;white-space:nowrap" class="ft18">Supporting documents complete and amount<br />claimed proper</p>
            <p style="position:absolute;top:980px;left:34px;white-space:nowrap" class="ft15">Signature</p>
            <p style="position:absolute;top:980px;left:457px;white-space:nowrap" class="ft15">Signature</p>
            <p style="position:absolute;top:1009px;left:44px;white-space:nowrap" class="ft16">Printed</p>
            <p style="position:absolute;top:1021px;left:47px;white-space:nowrap" class="ft16">Name</p>
            <p style="position:absolute;top:980px;left:185px;white-space:nowrap; font-size:8pt; font-weight:bold" class="ft15">ANGIELYN T. ADLAON, CPA, MBA</p>
            <p style="position:absolute;top:1009px;left:466px;white-space:nowrap" class="ft16">Printed</p>
            <p style="position:absolute;top:1021px;left:469px;white-space:nowrap" class="ft16">Name</p>
            <p style="position:absolute;top:980px;left:563px;white-space:nowrap; font-size:8pt; font-weight:bold" class="ft16">JAIME S. BERNADAS, MD, MGM, CESO III</p>
            <p style="position:absolute;top:1055px;left:44px;white-space:nowrap" class="ft15">Position</p>
            <p style="position:absolute;top:1005px;left:210px;white-space:nowrap; font-size: 8pt" class="ft16">Head, Accounting Section</p>
            <p style="position:absolute;top:1016px;left:160px;white-space:nowrap; font-size: 8pt" class="ft16">Head, Accounting Unit/ Authorized Representative</p>
            <p style="position:absolute;top:1055px;left:461px;white-space:nowrap" class="ft15">Position</p>
            <p style="position:absolute;top:1005px;left:644px;white-space:nowrap; font-size: 8pt" class="ft16">DIRECTOR IV</p>
            <p style="position:absolute;top:1016px;left:573px;white-space:nowrap; font-size: 8pt" class="ft16">Agency Head/Authorized Representative</p>
            <p style="position:absolute;top:1091px;left:44px;white-space:nowrap" class="ft15">Date</p> 
            <p style="position:absolute;top:1091px;left:471px;white-space:nowrap" class="ft15">Date</p>
            <p style="position:absolute;top:1120px;left:20px;white-space:nowrap" class="ft17"><b>E. Receipt of Payment</b></p>
            <p style="position:absolute;top:1151px;left:34px;white-space:nowrap" class="ft16">Check/ ADA</p>  
            <p style="position:absolute;top:1161px;left:54px;white-space:nowrap" class="ft16">No.:</p>
            <p style="position:absolute;top:1158px;left:343px;white-space:nowrap" class="ft15">&#160;Date:</p>
            <p style="position:absolute;top:1158px;left:493px;white-space:nowrap" class="ft15">Bank Name &amp; Account Number:</p>
            <p style="position:absolute;top:1158px;left:733px;white-space:nowrap" class="ft15">JEV No.</p>
            <p style="position:absolute;top:1187px;left:34px;white-space:nowrap" class="ft15">Signature:</p>
            <p style="position:absolute;top:1187px;left:343px;white-space:nowrap" class="ft15">&#160;Date:</p>
            <p style="position:absolute;top:1187px;left:493px;white-space:nowrap" class="ft15">Printed Name:</p>
            <p style="position:absolute;top:1187px;left:733px;white-space:nowrap" class="ft15">Date</p>
            <p style="position:absolute;top:1230px;left:20px;white-space:nowrap" class="ft15">Official Receipt No. &amp; Date/Other Documents</p>
        </div>



     <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal"><i class="typcn typcn-times"></i>Close</button>
        <button type="submit" class="btn btn-sm btn-primary"><i class="typcn typcn-tick menu-icon"></i>Submit</button>
    </div>
 </div>

    </form>

<script>

      $(document).ready(function() {
       $('#saa1').change(function() {
         $('#saa2').prop('disabled', true);
         $('#inputValue2').prop('disabled', true);

         $('#saa3').prop('disabled', true);
         $('#inputValue3').prop('disabled', true);

         $('#saa2').html('<option value="">Please Select a Saa</option>')

         setTimeout(function() {
            $('#saa2').prop('disabled', false);
            $('#inputValue2').prop('disabled', false);
         }, 500);

       });
         

      });

          // Function to update the total and format with commas
        function updateTotal() {
            // Get input values
            var amount1 = parseFloat(getFormattedValue('inputValue1')) || 0;
            var amount2 = parseFloat(getFormattedValue('inputValue2')) || 0;
            var amount3 = parseFloat(getFormattedValue('inputValue3')) || 0;

            // Calculate sum
            var total = amount1 + amount2 + amount3;

            // Format with commas for integer part
            var formattedTotal = total.toLocaleString('en-US', { maximumFractionDigits: 2 });

            // Update the total display
            document.querySelector('.total').innerText = '' + formattedTotal;
        }

        // Function to get the numeric value from the formatted input
        function getFormattedValue(elementId) {
    var inputElement = document.getElementById(elementId);
    var numericValue = parseFloat(inputElement.value.replace(/,/g, '').replace(/^\.|\.(?=.*\.)|[^\d.-]/g, '')) || 0;

    inputElement.value = numericValue.toLocaleString('en-US', { maximumFractionDigits: 2 });
    return numericValue;
}


        // Attach the updateTotal function to input change events
        document.getElementById('inputValue1').addEventListener('input', updateTotal);
        document.getElementById('inputValue2').addEventListener('input', updateTotal);
        document.getElementById('inputValue3').addEventListener('input', updateTotal);

        // Initial update
        updateTotal();
</script>

