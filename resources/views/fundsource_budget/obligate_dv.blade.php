<style>
  .custom-dropdown {
    position: relative;
  }

  #dropdownContent1,
  #dropdownContent2,
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

  #dropdownContent1 label,
  #dropdownContent2 label,
  #dropdownContent3 label {
      display: block;
      padding: 8px;
      cursor: pointer;
  }

  #dropdownContent1 label:hover,
  #dropdownContent2 label:hover,
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
    }.row{
      font-size: 12px;
    }
    .modal-title {
      text-align: center !important;
    }
    .hide {
        display: none; /* Adjust the height to your desired value */
    }
   
</style>

<form  method="post" action="{{ route('dv.obligate') }}" id ="dv_form"> 
    @csrf   
 <input type="hidden" name="dv_id" id="dv_id" value="{{$dv->id}}">
 <div class="clearfix"></div>
    <div class="new-times-roman table-responsive">
    <div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <table cellpadding="0" cellspacing="0" width="100%" style="margin-top: 10px">
              <tr>
                <td width="23%" style="text-align: center; border-right:none"><img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/doh-logo.png'))) }}" width="60%" ></td>
                <td width="54%" style="border-left:none; border-right:none; ">
                  <div class="header" style="margin-top: 15px">
                    <span style="margin-top: 10px">Republic of the Philippines</span> <br>
                    <strong> CENTRAL VISAYAS CENTER for HEALTH DEVELOPMENT</strong> <br>
                    <small>Osmeña Boulevard, Cebu City, Philippines 6000</small> <br>
                    <small>Regional Director's Office Tel. No (032) 253-6335 Fax No. (032) 254-0109</small><br>
                    <small>Official Website <u>www.ro7.doh.gov.ph/</u> Email Address <u>dohro7@gmail.com</u></small><br>
                  </div>
                </td>
                <td width="23%" style="text-align: right; border-left:none;"><small><i><br><br><br><br><br><br><br><br><br>Appendix 32&nbsp;&nbsp;&nbsp;</i></small> </td>
              </tr>

            </table>
            <table border="2" style="width: 100%;" >
                  <tr>
                    <td height=3% width =81% style="text-align:center;font-size:14px"> <strong>DISBURSEMENT VOUCHER</strong></td>
                    <td style="width:19%;" >
                    <b>
                      <span style="margin-bottom: 20px">Fund Cluster :</span><br>
                      <span style="margin-top: 20px">Date: {{ date('F j, Y', strtotime($dv->date))}}</span><br>
                      <span>DV No. :{{$dv->dv_no}}</span>   
                    </b>    
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
                    <td style="width:29%; border-left: 0 "><b> {{$dv->facility->name}}</td>
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
                    <td style="width:88%; border-left: 0 ">{{$dv->facility->address}}<b>  </td>
                </tr>
            </table>
            <table border="2" style="width: 100%;border-top: 0px;" >
            <?php 
              $saa_source = [$fund_source[0]->saa, !Empty($fund_source[1]->saa)?$fund_source[1]->saa : '',  !Empty($fund_source[2]->saa)?$fund_source[2]->saa : ''];
              $saa_amount = array_values(array_filter([$dv->amount1, !Empty($dv->amount2)?$dv->amount2 : 0,  !Empty($dv->amount3)?$dv->amount3: 0], function($value){ return $value !== 0 && $value!== null;}));
              $index=0;
              $total_overall = (float)str_replace(',', '', $dv->amount1) + (!Empty($dv->amount2)?(float)str_replace(',', '', $dv->amount2) : 0) 
              + (!Empty($dv->amount3)?(float)str_replace(',', '', $dv->amount3): 0);
              if($dv->deduction1>3){
                $total = $total_overall/1.12;
              }else{
                $total = $total_overall;
              }
          
              $vat = $total*$dv->deduction1/100;
              $ewt = $total*$dv->deduction2/100;
              $subsidy = $total_overall - (float)str_replace(',','',$dv->accumulated);
            ?>
            <tr class="header">
                    <td height=2.5% width =58%> Particulars</td>
                    <td style="width:15%; border-left: 0 " >Responsibility Center</td>
                    <td style="width:15%; border-left: 0 " >MFO/PAP</td>
                    <td style="width:15%; border-left: 0 " >Amount</td>
                  </tr>
                  <tr style="text-align:left;" >
                    <td height=4% width =58%>
                        <p style="text-align:justify;">For reimbursement of medical services rendered to patients under the Medical 
                        Assistance for Indigent Patient Program for {{$dv->facility->name}}
                        per billing statement dated {{date('F Y', strtotime($dv->month_year_from))}} - {{!Empty($dv->month_year_to)?date('F Y', strtotime($dv->month_year_to)):''}}
                        in the amount of:</p><br>

                        @foreach($fund_source as $index=> $fund_saa)
                        <div style="width: 100%;">
                          <span class="saa" style="margin-left:10px;"><?php echo $saa_source[$index]; ?></span>
                          <span class="amount" style="float: right;"><?php echo number_format(floatval(str_replace(',','',$saa_amount[$index])), 2, '.', ','); ?></span>
                          <div style="clear: both;"></div>
                        </div>
                        @endforeach
                        <br>
                        
                        <div style="width: 100%;">   
                          <span class="saa" style="margin-left:10px;"> {{number_format($dv->deduction1).'%'.' '.'VAT'}}</span>
                          <span style="margin-left:50px;"><?php echo number_format($total, 2, '.', ',')?></span>
                          <span style="float: right;"><?php echo number_format($vat, 2, '.', ',')?></span>
                          <div style="clear: both;"></div>
                        </div>
                        <div style="width: 100%;">   
                          <span class="saa" style="margin-left:10px;"> {{number_format($dv->deduction2).'%'.' '.'EWT'}}</span>
                          <span style="margin-left:48px;"><?php echo number_format($total, 2, '.', ',')?></span>
                          <span style="float: right;"><?php echo number_format($ewt, 2, '.', ',')?></span>
                          <div style="clear: both;"></div>
                        </div>
                        <br><br>
              
                        <span type="text" style="width:95%; margin-left:10px;" class="saa">Control No:{{!Empty($dv->control_no)?$dv->control_no:''}}<span><br>
                        <span style="margin-left:10px; font-weight:bold">Amount Due</span>
                        
                    </td>
                    <td style="width:14%; border-left: 0 " ></td>
                    <td style="width:14%; border-left: 0 " ></td>
                    <td style="width:14%; border-left: 0 " >
                    <div class= "header">
                      <br><br><br><br>
                      <span style="text-align:center;"><?php echo number_format($total_overall, 2, '.', ',')?></span>
                      <br><br><br><br>
                      <span style="text-align:center;"><?php echo number_format($vat + $ewt, 2, '.', ',')?></span><br><br>
                      <span style="text-align:center;">_________________</span><br>
                      <span style="text-align:center;"><?php echo number_format($total_overall -  ($vat + $ewt), 2, '.', ',')?></span>
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
                      &nbsp;&nbsp;&nbsp;&nbsp;<span>Accumulated Surplus</span><br>
                      <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Due to BIR</span><br> 
                      <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; CIB-MDS</span> 
                    </td>
                    <td style="width:20%; border-left: 0 " >
                      
                      <span>50214990</span><br>
                      <span>30101010</span><br>
                      <span>20201010</span><br> 
                      <span>10104040</span> <br><br>
                    </td>
                    <td style=" border-left: 0 ; text-align:right; vertical-align:top" >
                      <span><?php echo number_format($subsidy, 2, '.', ',') ?></span><br>
                      <span>{{number_format(str_replace(',','',$dv->accumulated),2,'.',',')}}</span>
                    </td>
                    <td style=" border-left: 0 ; text-align:right; vertical-align:top" >
                      <br><br><span><?php echo number_format($vat + $ewt, 2, '.', ',')?></span><br>
                      <span><?php echo number_format($total_overall -  ($vat + $ewt), 2, '.', ',')?></span>
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
        @if($type == "obligate")
        <button type="submit" style="background-color:#17c964;color:white" id="submitBtn" class="btn btn-sm"><i class="typcn typcn-tick menu-icon"></i>Obligate</button>
        @endif
        <input type="hidden" name="group_id" id="group_id" >

    </div>
</div>
</div>
</div>
</form>

