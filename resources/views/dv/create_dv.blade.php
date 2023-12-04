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
<form  method="post" action="{{ route('dv.create.save') }}"> 
    @csrf   
 <input type="hidden" name="dv" value="">
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
            <input type="date" asp-for="Date" name="datefield"  id="dateField" style="position:absolute;top:70px;left:650px;white-space:nowrap; width: 150px; height: 28px; font-size:8pt" class="ft15" required>
            <p style="position:absolute;top:113px;left:600px;white-space:nowrap" class="ft15">DV No :</p>
            <p style="position:absolute;top:113px;left:728px;white-space:nowrap" class="ft15"></p>
            <p style="position:absolute;top:180px;left:20px;white-space:nowrap" class="ft18">Mode Of<br />Payment</p>
            <p style="position:absolute;top:180px;left:178px;white-space:nowrap" class="ft15">MDS Check</p>
            <p style="position:absolute;top:180px;left:311px;white-space:nowrap" class="ft18">Commercial<br />Check</p>
            <p style="position:absolute;top:180px;left:443px;white-space:nowrap" class="ft15">ADA</p>
            <p style="position:absolute;top:180px;left:566px;white-space:nowrap" class="ft15">Others (Please specify) ____________</p>
            <p style="position:absolute;top:217px;left:20px;white-space:nowrap" class="ft15">Payee</p>
  
        <select id="facilityDropdown" name="facilityname" id ="facility_id" onchange="onchangefacility($(this))" style="position:absolute;top:215px;left:140px;white-space:nowrap; width:260px; height: 28px; font-size: 9pt" class="ft15">
            <option value=""> Select Facility  </option>
            </select>
            <!-- <option value="" ></option> -->
            <!-- @foreach ($facilities as $facility)
                <option value="{{ $facility->id }}" >{{ $facility->name }}</option>
            @endforeach -->
   
            <p style="position:absolute;top:219px;left:434px;white-space:nowrap" class="ft18">Tin/Employee No.:<br /></p>
            <p style="position:absolute;top:219px;left:655px;white-space:nowrap" class="ft15">ORS/BURS No.:</p>
            <p style="position:absolute;top:260px;left:20px;white-space:nowrap" class="ft15">Address</p>
        <p style="position:absolute;top:260px;left:150px;white-space:nowrap; color:red; font-weight:bold" id="facilityAddress"  class="ft15"></p> 
                                                                    <input type="hidden" name="facilityAddress" id="facilitaddress" class="ft15">
    {{--<p style="position:absolute;top:365px;left:150px;white-space:nowrap; color:red; font-weight:bold" id="facilityAddress2" name="facilityAddress2" class="ft15"></p>--}}
            <p style="position:absolute;top:300px;left:246px;white-space:nowrap" class="ft15">Particulars</p>
            <p style="position:absolute;top:295px;left:522px;white-space:nowrap" class="ft15">Responsibility</p>
            <p style="position:absolute;top:310px;left:543px;white-space:nowrap" class="ft15">Center</p>
            <p style="position:absolute;top:300px;left:640px;white-space:nowrap" class="ft15">MFO/PAP</p>
            <p style="position:absolute;top:300px;left:759px;white-space:nowrap" class="ft15">Amount</p>
            <p style="position:absolute;top:345px;left:39px;white-space:nowrap" class="ft16">
                For reimbursement of medical services rendered to patients under the Medical Assistance for <br />
            </p>
            <p style="position:absolute;top:365px;left:39px;white-space:nowrap"  class="ft16">
                Indigent Patient Program for
                <span id="hospitalAddress" name="hospitalname" style="color:red;"></span>
            </p>
            <p style="position:absolute;top:385px;left:39px;white-space:nowrap" class="ft16">
                per billing statement dated
                <input type="month" id="billingMonth1" name="billingMonth1" asp-for="MonthYearFrom" style="width: 110px; height: 28px; font-size: 8pt;" class="ft15" required>
                <!-- Second Month Picker -->
            <input type="month" id="billingMonth2" name="billingMonth2" asp-for="MonthYearTo" style="width: 110px; height: 28px; font-size: 8pt;" class="ft15" required>
                    in the amount of:
            </p> 
                              <p style="position:absolute;top:420px;left:60px;white-space:nowrap; width:100px; height: 20px;"  class="ft16">
                               <span id="error-message" style="color:red;"></span>
                             </p>
                      <!-- List of saa to display -->
                    <select name="fundsource_id" id="saa1"  onclick="onchangeSaa($(this))" style="position:absolute;top:440px;left:60px;white-space:nowrap; width:100px; height: 20px;" class="ft15" required>
                            <option value="" data-facilities="">- Select SAA -</option>
                        @foreach($fundsources as $fund)
                            <option value="{{ $fund->id }}">{{ $fund->saa }}</option>  
                        @endforeach  
                    </select> 

                    <input type="hidden" name="saa1_infoId" id="saa1_infoId" style="position:absolute;top:440px;left:500px;white-space:nowrap; width:120px; height: 20px;">
                    <input type="hidden" name="saa1_beg" id="saa1_beg" style="position:absolute;top:440px;left:500px;white-space:nowrap; width:120px; height: 20px;">
                    <input type="hidden" name="saa1_discount" id="saa1_discount" style="position:absolute;top:440px;left:500px;white-space:nowrap; width:120px; height: 20px;">
                    <input type="hidden" name="saa1_utilize" id="saa1_utilize" style="position:absolute;top:440px;left:500px;white-space:nowrap; width:120px; height: 20px;">
                    
                    
                    <input type="text" name="amount1" id="inputValue1" style="position:absolute;top:440px;left:180px;white-space:nowrap; width:120px; height: 20px;" oninput="fundAmount()" class="ft15" required>
                    <input type="text" name="forvat" id="for_vat" style="position:absolute;top:420px;left:320px;white-space:nowrap; width:60px; height: 20px;" class="ft15" placeholder="for vat" readonly>
                    <!-- for vat each saa -->
                    <input type="text" name="vatValue1" id="vatValue1" style="position:absolute;top:440px;left:320px;white-space:nowrap; width:80px; height: 20px;" class="ft15" readonly required>
                    <input type="text" name="vatValue2" id="vatValue2" style="position:absolute;top:460px;left:320px;white-space:nowrap; width:80px; height: 20px; display: none;" class="ft15"  readonly>
                    <input type="text" name="vatValue3" id="vatValue3" style="position:absolute;top:480px;left:320px;white-space:nowrap; width:80px; height: 20px; display: none;" class="ft15"  readonly>
                    <!-- ---- -->
                    <input type="text" name="for_ewt" id="for_ewt" style="position:absolute;top:420px;left:410px;white-space:nowrap; width:60px; height: 20px;" class="ft15"  placeholder="for Ewt" readonly>
                    <!-- for ewt each saa -->
                    <input type="text" name="ewttValue1" id="ewttValue1" style="position:absolute;top:440px;left:410px;white-space:nowrap; width:80px; height: 20px;" class="ft15"  readonly required>
                    <input type="text" name="ewtValue2" id="ewtValue2" style="position:absolute;top:460px;left:410px;white-space:nowrap; width:80px; height: 20px;display: none;" class="ft15"  readonly>
                    <input type="text" name="ewtValue3" id="ewtValue3" style="position:absolute;top:480px;left:410px;white-space:nowrap; width:80px; height: 20px;display: none;" class="ft15"  readonly>
                    <!-- ---- -->
                    <div id="error-message" style="position: absolute; top: 470px; left: 270px; color: red;"></div>

                       <br />
                        <br />        
                    <select  name="fundsource_id_2"  id="saa2"  onchange="onchangeSaa($(this))" style="position:absolute;top:460px;left:60px;white-space:nowrap; width:100px; height: 20px; display: none;" class="ft15" >
                    <option value="">- Select SAA -</option>
                              <option value=""></option>
                    </select>

                    <input type="hidden" name="saa2_infoId" id="saa2_infoId" style="position:absolute;top:440px;left:600px;white-space:nowrap; width:120px; height: 20px;">
                    <input type="hidden" name="saa2_beg" id="saa2_beg" style="position:absolute;top:440px;left:600px;white-space:nowrap; width:120px; height: 20px;">
                    <input type="hidden" name="saa2_discount" id="saa2_discount" style="position:absolute;top:600px;left:500px;white-space:nowrap; width:120px; height: 20px;">
                    <input type="hidden" name="saa2_utilize" id="saa2_utilize" style="position:absolute;top:600px;left:500px;white-space:nowrap; width:120px; height: 20px;">

                <input type="text" name="amount2" id="inputValue2"  style="position:absolute;top:460px;left:180px;white-space:nowrap; width:120px; height: 20px; font-size: 8pt; display: none;"  class="ft15" oninput="fundAmount()">
                <span id="showSAAButton" class="fa fa-plus" style="position:absolute;top:442px;left:20px; width:20px; height: 20px; font-size:11px; cursor:pointer" onclick="toggleSAADropdowns()">Add</span>
                <span id="RemoveSAAButton" class="fa fa-plus" style="position:absolute;top:465px;left:20px; width:20px; height: 20px; font-size:11px; display: none; cursor:pointer" onclick="removeSAADropdowns()">remove</span>

               <select name="fundsource_id_3"  id="saa3" onchange="onchangeSaa($(this))" style="position:absolute;top:480px;left:60px;white-space:nowrap; width:100px; height: 20px; display: none" class="ft15">
                    <option value="">- Select SAA -</option>
                            <!-- @foreach($fundsources as $fund)
                                <option value="{{ $fund->id }}">{{ $fund->saa }}</option>
                            @endforeach  -->
                    </select>

                    <input type="hidden" name="saa3_infoId" id="saa3_infoId" style="position:absolute;top:440px;left:500px;white-space:nowrap; width:120px; height: 20px;">
                    <input type="hidden" name="saa3_beg" id="saa3_beg" style="position:absolute;top:440px;left:500px;white-space:nowrap; width:120px; height: 20px;">
                    <input type="hidden" name="saa3_discount" id="saa3_discount" style="position:absolute;top:440px;left:500px;white-space:nowrap; width:120px; height: 20px;">
                    <input type="hidden" name="saa3_utilize" id="saa3_utilize" style="position:absolute;top:440px;left:500px;white-space:nowrap; width:120px; height: 20px;">
                    
                    <label style="position:absolute;top:525px;left:40px;white-space:nowrap;" class="ft16">Vat</label>
                <input type="text" name="amount3" id="inputValue3"  style="position:absolute;top:480px;left:180px;white-space:nowrap; width:120px; height: 20px; font-size: 8pt; display:none" class="ft15" oninput="fundAmount()">
                <p style="position:absolute;top:520px;left:113px;white-space:nowrap; width:50px; height: 20px;" id ="forVat_left" class='ft16'>
               </p>
                <!-- <span id="showSaabutton" class="fa fa-plus" style="position:absolute;top:445px;left:75px; width:20px; height: 20px; cursor:pointer" onclick="toggleSAAdropdowns()">Add</span> -->
            <!-- <span id="showSAAButton1" class="fa fa-plus" style="position:absolute;top:455px;left:70px; width:20px; height: 20px; front-size:8px; cursor:pointer" onclick="toggleSAADropdowns()">Remove</span> -->
        <label style="position:absolute;top:545px;left:40px;white-space:nowrap;" class="ft16">Ewt</label>
        <input type="text" name="vat" id="vat" style="position:absolute;top:520px;left:60px;white-space:nowrap; width:50px; height: 20px;" class="ft15" oninput="" placeholder="Vat" readonly>
        <input type="text" name="ewt" id="ewt" style="position:absolute;top:540px;left:60px;white-space:nowrap; width:50px; height: 20px;" class="ft15" oninput="" placeholder="ewt" readonly>
        <p style="position:absolute;top:540px;left:113px;white-space:nowrap; width:50px; height: 20px;" class='ft16' id ="forEwt_left"></p>
        <!-- <select id="deduction1" name="deduction1"  style="position:absolute;top:520px;left:60px;white-space:nowrap; width:100px; height: 20px" class="ft15">
         <option value="">- Select Vat -</option>
         @foreach($VatFacility as $facilityvat)
                <option value="{{ $facilityvat->id  }}">{{ $facilityvat->vat }}%</option>
         @endforeach
        </select> -->
            <input type="text" name="deductionAmount1" id="inputDeduction1" style="position:absolute;top:520px;left:180px;white-space:nowrap; width:120px; height: 20px; font-size: 8pt" class="ft15" readonly required>

        <!-- <select id="deduction2" name="deduction2" style="position:absolute;top:540px;left:60px;white-space:nowrap; width:100px; height: 20px" class="ft15">
        <option value="">- Select Ewt -</option>
        @foreach($ewtFacility as $facilityewt)
          <option value="{{  $facilityewt->id }}">{{ $facilityewt->Ewt % 1 == 0? number_format($facilityewt->Ewt, 0) : $facilityewt->Ewt  }}%</option>

         {{-- <option value="{{  $facilityewt->id }}">{{ $facilityewt->Ewt % 1 == 0? number_format($facilityewt->Ewt, 0) : $facilityewt->Ewt }}%</option> --}}
         @endforeach
        </select> -->
        <p style="position:absolute;top:650px;left:113px;white-space:nowrap; width:50px; height: 20px;" type="hidden" id ="for_facility_id" class='ft16'></p>
        <input type="text" name="deductionAmount2" id="inputDeduction2" style="position:absolute;top:540px;left:180px;white-space:nowrap; width:120px; height: 20px; font-size: 8pt" class="ft15" min="1" readonly required>
            <p style="position:absolute;top:568px;left:69px;white-space:nowrap; font-weight:bold;" class="ft16">  &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; Amount Due</p>
            <p style="position:absolute;top:359px;left:746px;white-space:nowrap" class="ft15"></p>
            <p style="position:absolute;top:440px;left:755px;white-space:nowrap"  class="ft15 total"></p>
                                                  <input type="hidden" name="total" id="totalInput" class="ft15 total">
            <p style="position:absolute;top:521px;left:755px;white-space:nowrap" class="ft15 totalDeduction"></p>
                                            <input type="hidden" name="totalDeduction" id="totalDeductionInput" class="ft15">
            <p style="position:absolute;top:551px;left:760px;white-space:nowrap" class="ft15">_________________</p>
            <p style="position:absolute;top:568px;left:755px;white-space:nowrap; font-weight:bold" class="ft15 overallTotal">total</p>
                                            <input type="hidden" name="overallTotal" id="overallTotalInput" class="ft15 overallTotal">
            <p style="position:absolute;top:615px;left:20px;white-space:nowrap" class="ft17"><b>A. Certified: Expenses/Cash Advance necessary, lawful and incurred under my direct supervision.</b></p>
            <p style="position:absolute;top:652px;left:57px;white-space:nowrap; font-size:9pt" class="ft17"><b>&#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160;  SOPHIA M. MANCAO, MD, DPSP, RN-MAN </b></p>
            <p style="position:absolute;top:666px;left:60px;white-space:nowrap; font-size: 8pt" class="ft17">&#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; Director III</p>
            <p style="position:absolute;top:687px;left:20px;white-space:nowrap" class="ft17"><b>B. Accounting Entry:</b></p>
            <p style="position:absolute;top:709px;left:211px;white-space:nowrap" class="ft15">Account Title</p>
            <p style="position:absolute;top:709px;left:478px;white-space:nowrap" class="ft15">Uacs Code</p>
            <p style="position:absolute;top:709px;left:627px;white-space:nowrap" class="ft15">Debit</p>
              <p style="position:absolute;top:750px;left:627px;white-space:nowrap" id="totalDebit" class="ft15"></p>
            <p style="position:absolute;top:709px;left:755px;white-space:nowrap" class="ft15">Credit</p>
            <p style="position:absolute;top:760px;left:755px;white-space:nowrap" id ="DeductForCridet" class="ft15"></p>
            <p style="position:absolute;top:775px;left:755px;white-space:nowrap" id="OverTotalCredit" class="ft15"></p>
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

//   // Get the content of the <p> tag
//   var pContent = document.getElementById('overallTotal').innerText;
// // Set the value of the hidden input field
// document.getElementById('overallTotalInput').value = pContent;

          // Function to update the total and format with commas
        function updateTotal() {
            // Get input values
            var amount1 = parseFloat(getFormattedValue('inputValue1')) || 0;
            var amount2 = parseFloat(getFormattedValue('inputValue2')) || 0;
            var amount3 = parseFloat(getFormattedValue('inputValue3')) || 0;

            var deduct1 = parseFloat(getFormattedValue('inputDeduction1')) || 0;
            var deduct2 = parseFloat(getFormattedValue('inputDeduction2')) || 0;
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

           // document.querySelector('.totalDeduction').innerText = '' + formattedDecduction;
          //  document.querySelector('#totalDeductionInput').value = '' + formattedDecduction;

            document.querySelector('.overallTotal').innerText = '' + formattedTotalAmount;
           // document.querySelector('#overallTotalInput').value = '' + formattedTotalAmount;
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

        document.getElementById('inputDeduction1').addEventListener('input', updateTotal);
        document.getElementById('inputDeduction2').addEventListener('input', updateTotal);

        // Initial update
        updateTotal();
</script>

