<!DOCTYPE html>
<html>
    <head>
        <title>Generate DV PDF</title>
        <link rel="stylesheet" href="{{ public_path('bootstrap.min.css') }}">
        <style>
      
          .header{
            font-size: 11px;
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
              margin-left: 7px;
              display: inline-block;
              vertical-align: middle;
          }
          .label {
              font-size: 12px;
              display: inline-block;
              margin-right: 8pxpx;
              margin-left: 5px;
          }
          
        
      </style>
    </head>
    <body>
      @if($dv !== null)
        <div >
          <table border= 1px solid black width= 100%>
                <div>
                <table >
                    <tr>
                    <td width="23%" style="text-align: center; border-right:none"><img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/doh-logo.png'))) }}" width="60%" ></td>
                    <td width="54%" style="border-left:none; border-right:none; ">
                      <div class="header" style="margin-top: 20px">
                        <span style="margin-top: 10px">Republic of the Philippines</span> <br>
                        <strong> CENTRAL VISAYAS for HEALTH DEVELOPMENT</strong> <br>
                        <small>Osmeña Boulevard, Cebu City, Philippines 6000</small> <br>
                        <small>Regional Director's Office Tel. NO. (032) 253-6335 Fax No. (032) 254-0109</small><br>
                        <small>Official Website <u>www.ro7.doh.gov.ph/</u> Email Address <u>dohro7@gmail.com</u></small><br>
                      </div>
                    </td>
                    <td width="23%" style="text-align: right; border-left:none;"><small><i><br><br><br><br><br><br><br><br><br>Appendix 32&nbsp;&nbsp;&nbsp;</i></small> </td>
                    </tr>
                </table>
                <table width=100%>
                  <tr>
                    <td height=3% width =73% style="text-align:center;font-size:14px"> <strong>DISBURSEMENT VOUCHER</strong></td>
                    <td style="width:27%;" >
                      <span style="margin-bottom: 20px">Fund Cluster :</span><br>
                      <span style="margin-top: 20px">Date: {{ date('F j, Y', strtotime($dv->date))}}</span><br>
                      <span>DV No.:{{$dv->id}}</span>       
                    </td>
                  </tr>
                </table>
              
                <table width=100%>
                  <tr>
                    <td height=3% width =15%><b> Mode of Payment</td>
                    <td style="width:85%; border-left: 0 " >
                    
                    <div class="box-container">
                      <span class="box"></span>
                      <span class="label">MDS Check</span>

                      <span class="box"></span>
                      <span class="label">Commercial Check</span>

                      <span class="box"></span>
                      <span class="label">ADA</span>
                      <span class="box"></span>
                      <span class="label">Others (Please specify)_________________</span>
                    </div>
                    </td>
                  </tr>
                </table>
                <table width=100%>
                  <tr>
                    <td height=2.5% width =15%><b> Payee</td>
                    <td style="width:29%; border-left: 0 "><b> {{$facility->name}}</td>
                    <td style="width:29%; border-left: 0 " >
                    <span?>Tin/Employee No. :</span>
                    </td>
                    <td style="width:27%; border-left: 0 " >
                    <span>ORS/BURS No. :</span>
                    </td>
                  </tr>
                </table>
                <table width=100%>
                  <tr>
                    <td height=2.5% width =15% ><b>Address</td>
                    <td style="width:85%; border-left: 0 "><b>{{$facility->address}}</td>
                    
                  </tr>
                </table>
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
                        ?>
                <table width=100%>
                  <tr class="header">
                    <td height=2.5% width =58%> Particulars</td>
                    <td style="width:15%; border-left: 0 " >Responsibility Center</td>
                    <td style="width:15%; border-left: 0 " >MFO/PAP</td>
                    <td style="width:15%; border-left: 0 " >Amount</td>
                  </tr>
                  <tr>
                    <td height=4% width =58%>
                    
                        <p style="text-align:justify;">For reimbursement of medical services rendered to patients under the Medical 
                        Assistance for Indigent Patient Program for {{$facility->name}}<br>
                        per billing statement dated {{date('F Y', strtotime($dv->month_year_from))}} to {{date('F Y', strtotime($dv->month_year_to))}}
                        in the amount of:</p>
                        
                        @foreach($fund_source as $index=> $fund_saa)
                        <div>   
                        <span class="saa"><?php echo $saa_source[$index] ;?></span>
                        <span style="margin-left:150px;"><?php echo $saa_amount[$index];?></span>
                        </div>
                        @endforeach
                        <br>
                        
                        <div>   
                          <span class="saa">Vat : {{$dv->deduction1}}</span>
                          <span style="margin-left:50px;"><?php echo number_format($total, 2, '.', ',')?></span>
                          <span style="margin-left:50px;"><?php echo number_format($vat, 2, '.', ',')?></span>
                        </div>
                        <div>
                          <span class="saa">Ewt : {{$dv->deduction2}}</span>
                          <span style="margin-left:50px;"><?php echo number_format($total, 2, '.', ',')?></span>
                          <span style="margin-left:50px;" ><?php echo number_format($ewt, 2, '.', ',')?></span>
                        </dv><br><br>
              
                        <span class="saa">Ref No:{{$dv->control_no}}</span><br><br>
                        <span style="margin-left:150px; font-weight:bold">Amount Due</span>
                    
                    </td>
                    <td style="width:14%; border-left: 0 " ></td>
                    <td style="width:14%; border-left: 0 " ></td>
                    <td style="width:14%; border-left: 0 " >
                    <div class= "header">
                      <br><br><br><br>
                      <span style="text-align:center;"><?php echo number_format($total_overall, 2, '.', ',')?></span>
                      <br><br><br><br>
                      <span style="text-align:center;"><?php echo number_format($vat + $ewt, 2, '.', ',')?></span><br>
                      <span style="text-align:center;">______________</span><br>
                      <span style="text-align:center;"><?php echo number_format($total_overall -  ($vat + $ewt), 2, '.', ',')?></span>
                    </div> 
                  </td>
                  </tr>
                </table>
                <table width=100%>
                  <tr>
                    <td height=3% width =15%>
                    <dv>  
                      <span>A. Certified: Expenses/Cash Advance necessary, lawful and incurred under my direct supervision.</span>
                      <br><br>
                      <div style="display:inline-block;">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;
                        <span><strong>SOPHIA M. MANCAO, MD, DPSP, RN-MAN<strong></span>
                      </div>
                      <br>
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                      <span>Director III</span>
                    </dv>
                    </td>
                  </tr>
                </table>
                <table width=100%>
                  <tr>
                    <td height=2% width =15%><strong>B. Accounting Entry:</strong></td>
                  </tr>
                </table>
                <table width=100%>
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
                      <span>50214990</span><br>
                      <span>20201010</span><br> 
                      <span>10104040</span> 
                    </td>
                    <td style="width:20%; border-left: 0 ; text-align:right; vertical-align:top" > <span>{{$dv->total_amount}}</span></td>
                    <td style="width:20%; border-left: 0 ; text-align:right; vertical-align:top" >
                      <br><span><?php echo number_format($total + $ewt, 2, '.', ',')?></span><br>
                      <span><?php echo number_format($total_overall -  ($total + $ewt), 2, '.', ',')?></span>
                    </td>
                  </tr>
                </table>
                <table width=100%>
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
                <table width=100%>
                  <tr class="header">
                      <td height=2.5% width =15%>Signature</td>
                      <td width =38%></td>
                      <td height=2.5% width =15%>Signature</td>
                      <td width =32%></td> 
                  </tr>
                  <tr class="header">
                      <td height=2.5% width =15%>Printed Name</td>
                      <td width =38%><b>ANGIELYN T. ADLAON, CPA, MBA</td>
                      <td height=2.5% width =15%>Printed Name</td>
                      <td width =32%><b>JAIME S. BERNADAS, MD, MGM, CESO III</td>
                  </tr>
                  <tr class="header">
                      <td height=2.5% width =15%>Position</td>
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
                      <td height=2.5% width =15%>Position</td>
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
                      <td height=2.5% width =15%>Date</td>
                      <td width =38%></td>
                      <td height=2.5% width =15%>Date</td>
                      <td width =32%></td>
                  </tr>
                </table>
                <table width=100%>
                  <tr>
                      <td height=2.5% ><strong>E. Receipt of Payment</strong></td>
                  </tr>
                </table>
                <table width=100%>
                  <tr>
                      <td height=2.5% width =15%>Check/ADA No.:</td>
                      <td width =35%></td>
                      <td width =20%>Date:</td>
                      <td width =35%>Bank Name & Account Number:</td>
                      <td width =20%>JEV No.</td>
                  </tr>
                  <tr>
                      <td height=2.5% width =15%>Signature:</td>
                      <td width =35%></td>
                      <td width =20%>Date:</td>
                      <td width =35%>Printed Name:</td>
                      <td width =20%>Date</td>
                  </tr>
                </table>
                <table border= 1px solid black width=100%>
                  <tr>
                      <td height=2.5%>Official Receipt No. & Date/Other Documents</td>
                  </tr>
                </table>
                
                </div>
          </table>
        </div>
        @endif
    </body>
</html>
