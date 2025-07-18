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
              width: 20px;
              height: 12px;
              border: 1px solid black;
              margin-left: 7px;
              display: inline-block;
              vertical-align: middle;
              margin-top: 8px;
              margin-bottom: 2px;
          }
          .label {
              font-size: 12px;
              display: inline-block;
              margin-right: 8px;
              margin-left: 5px;
          }
          
      </style>
    </head>
    <body>
      @if($dv3 !== null)
        <div >
          <?php $div = [1, 2]; ?>
            @foreach($div as $d)
            <table border= 1px solid black width= 100%>
                  <div>
                      <table >
                          <tr>
                          <td width="23%" style="text-align: center; border-right:none"><img src="{{realpath(__DIR__ . '/../../..').'/public/images/doh-logo.png'}}" width="60%"></td>
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
                      <table width=100%>
                        <tr>
                          <td height=3% width =81% style="text-align:center;font-size:14px"> <strong>DISBURSEMENT VOUCHER</strong></td>
                          <td style="width:19%;" >
                          <b>
                            <span style="margin-bottom: 20px">Fund Cluster :</span><br>
                            <span style="margin-top: 20px">Date: {{ date('F j, Y', strtotime($dv3->date))}}</span><br>
                            <span>DV No. : {{$dv3->dv_no}}</span>   
                          </b>    
                          </td>
                        </tr>
                      </table>
                    
                      <table width=100%>
                        <tr>
                          <td height=3% width =10%><b> Mode of Payment</td>
                          <td style="width:85%; border-left: 0 " >
                          
                          <div class="box-container">
                            <span class="box"></span>
                            <span class="label">MDS Check</span>

                            <span class="box"></span>
                            <span class="label">Commercial Check</span>

                            <span class="box"></span>
                            <span class="label">ADA</span>
                            <span class="box"></span>
                            <span class="label">Others (Please specify)</span><br>
                            <span style="margin-top:1px; margin-left:480px">_________________</span>
                          </div>
                          </td>
                        </tr>
                      </table>
                      <table width=100%>
                        <tr>
                          <td width=10.4% height=2%><b> Payee</td>
                          <td style="width:39.6%; border-left: 0 "><b> {{$dv3->facility->name}}</td>
                          <td style="width:30%; border-left: 0; vertical-align:top; " >
                            <span style="vertical-align:top; " >Tin/Employee No. :</span>
                          </td>
                          <td style="width:19%; border-left: 0 " >
                          <span style="vertical-align:top; ">ORS/BURS No. : {{$dv3->ors_no}}</span>
                          </td>
                        </tr>
                      </table>
                      <table width=100%>
                        <tr>
                          <td width=10.5% height=2%><b>Address</td>
                          <td style="width: 89.5%; border-left: 0 ;vertical-align:top; "><b>{{$dv3->facility->address}}</td>
                          
                        </tr>
                      </table>
                      <table width=100%>
                        <tr class="header">
                          <td height=2.3% width =50%> Particulars</td>
                          <td style="width:20%; border-left: 0 " >Responsibility Center</td>
                          <td style="width:10%; border-left: 0 " >MFO/PAP</td>
                          <td style="width:20%; border-left: 0 " >Amount</td>
                        </tr>
                        <tr>
                          <?php $all = floor(count($dv3->extension)/2) ?>
                          <td height="" width="58%" style="vertical-align: top;">
                          
                              <p style="text-align:justify;vertical-align:top;">To transfer medical assistance program funds for 
                                {{$dv3->facility->name}} in the amount of:</p>

                              @foreach($dv3->extension as $row)
                                  <div style="width: 100%;">
                                      <span class="saa" style="float: left;">{{$row->proponentInfo->fundsource->saa}}</span>
                                      <span class="amount" style="float: right;">{{ number_format(floatval(str_replace(',','',$row->amount)), 2, '.', ',') }}</span>
                                      <div style="clear: both;"></div>
                                  </div>
                              @endforeach
                              @if($all < 3)
                                  <br><br>
                              @endif
                              <span style="margin-left:150px; font-weight:bold">Amount Due</span>
                        
                          </td>
                          <td style="width:14%; border-left: 0 " ></td>
                          <td style="width:14%; border-left: 0 " ></td>
                          <td style="width:14%; border-left: 0; vertical-align: top; text-align:center; ">
                            <br><br><br>
                            <!-- <label>{{ number_format(floatval(str_replace(',','',$dv3->total)), 2, '.', ',') }}</label><br> -->
                            @for ($i = 1; $i <= $all; $i++)
                              <br> 
                            @endfor
                            <!-- <label>{{ number_format(floatval(str_replace(',','',$total)), 2, '.', ',') }}</u></label>
                            <label><img src="{{realpath(__DIR__ . '/../../..').'/public/images/line.png'}}" width="60%"></label> -->
                            <label><?php echo number_format($total, 2,'.', ',')?></label>
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
                              <span><strong><u>JONATHAN NEIL V. ERASMO, MD, MPH, FPSM</u><strong></span>
                            </div>
                            <br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <span>OIC - Director III</span>
                          </dv>
                          </td>
                        </tr>
                      </table>
                      <table width=100%>
                        <tr >
                          <td height=1.5% width =15%><strong>B. Accounting Entry:</strong></td>
                        </tr>
                      </table>
                      <table width=100%>
                        <tr class="header">
                          <td height=2% width =50%>Account Title</td>
                          <td style="width:20%; border-left: 0 " >Uacs Code</td>
                          <td style="width:15%; border-left: 0 " >Debit</td>
                          <td style="width:15%; border-left: 0 " >Credit</td>
                        </tr>
                        <tr class="header">
                          <td height=6% style="text-align : left;">
                            &nbsp;&nbsp;&nbsp;&nbsp;<span>Subsidy / Others</span><br>
                            &nbsp;&nbsp;&nbsp;&nbsp;<span>Accumulated Surplus</span><br>
                            <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Due to BIR</span><br> 
                            <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; CIB-MDS</span> 
                          </td>
                          <td style=" border-left: 0 " ></td>
                          <td style=" border-left: 0 ; text-align:right; vertical-align:top" ></td>
                          <td style=" border-left: 0 ; text-align:right; vertical-align:top" ></td>
                        </tr>
                      </table>
                      <table width=100%>
                        <tr>
                            <td height=1.5% width =50%><strong>C. Certified:</strong></td>
                            <td width =50%><strong>D. Approved for Payment:</strong></td>
                        </tr>
                        <tr>
                            <td height=7%>
                              <div style="display: inline-flex; align-items: center;">
                                  <span class="box" style="display: inline-block;"></span>
                                  <p style="margin-left: 5px; display: inline;">Cash Available</p>
                              </div>
                              <div style="display: inline-flex; align-items: center; margin-top: 5px">
                                  <span class="box" style="display: inline-block;"></span>
                                  <p style="margin-left: 5px; display: inline;">Subject to Authority to Debit Account (when applicable)</p>
                              </div>
                              <div style="display: inline-flex; align-items: center; margin-top: 5px">
                                  <span class="box" style="display: inline-block;"></span>
                                  <p style="margin-left: 5px; display: inline;">Supporting documents complete and amount claimed proper</p>
                              </div>
                            </td>
                            <td></td>  
                        </tr>
                      </table>
                      <table width=100%>
                        <tr class="header">
                            <td height=2.5% width =10%>Signature</td>
                            <td width =40%></td>
                            <td height=2.5% width =15%>Signature</td>
                            <td width =35%></td> 
                        </tr>
                        <tr class="header">
                            <td>Printed Name</td>
                            <td><b>ANGIELINE T. ADLAON, CPA, MBA</td>
                            <td>Printed Name</td>
                            <td><b>JOSHUA G. BRILLANTES, MD, MPH, CESO IV</td>
                        </tr>
                        <tr class="header">
                            <td>Position</td>
                            <td>
                              <table width=100% style="text-align:center" border=0>
                                <tr>
                                  <td  style="border-bottom: 1px solid black">Head, Accounting Section</td>
                                </tr>
                                <tr>
                                  <td>Head, Accounting Unit/Authorized Representative</td>
                                </tr>
                            </table>
                            </td>
                            <td>Position</td>
                            <td>
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
                            <td>Date</td>
                            <td></td>
                            <td>Date</td>
                            <td></td>
                        </tr>
                      </table>
                      <table width=100%>
                        <tr>
                            
                            <td colspan="4" width =31% style="vertical-align:top"><b>E. Receipt of Payment</b></td>
                            <td width =19% style="vertical-align:top; border-bottom:none">JEV No.</td>
                        </tr>
                        <tr>
                            <td height=2.5% width =10%>Check/ADA No.:</td>
                            <td width =25%></td>
                            <td width =15% style="vertical-align:top">Date:</td>
                            <td width =31% style="vertical-align:top">Bank Name & Account Number:</td>
                            <td width =19% style="vertical-align:top; border-top:none"></td>
                        </tr>
                        <tr>
                            <td height=2.5%>Signature:</td>
                            <td></td>
                            <td style="vertical-align:top">Date:</td>
                            <td style="vertical-align:top">Printed Name:</td>
                            <td style="vertical-align:top; border-bottom:none">Date</td>
                        </tr>
                        <tr>
                            <td colspan="4" width =31% style="vertical-align:top">Official Receipt No. & Date/Other Documents</td>
                            <td width =19% style="vertical-align:top; border-top:none"></td>
                        </tr>
                      </table>     
                      </div>
                </table>
                <!-- <div style="position: absolute; bottom: 0; width: 100%; text-align: center;"> -->

                <div style="position:absolute; bottom: 0; left: 50%; transform: translateX(-50%); margin-top:15px;" class="modal_footer">
                    {!! DNS1D::getBarcodeHTML($dv3->route_no, 'C39E', 1, 28) !!}
                    <div style="text-align: center;">
                        <font class="route_no">{{ $dv3->route_no }}</font>
                    </div>
                </div>  
                @if($d == 1)
                  <div style="page-break-before: always;"></div>
                @endif
            @endforeach
            @foreach($dv3->extension as $image)
                @if($image->proponentInfo->fundsource->image)
                    <div style="page-break-before: always;"></div>
                    <div style="margin-left: 1px; margin-right: 1px;">
                        <div style="text-align: center;">
                            <span>{{$image->proponentInfo->fundsource->saa}}</span>
                            <br><br> <br><br> <br><br> <br><br> <br><br>
                            <img src="{{ url('storage/app/' . $image->proponentInfo->fundsource->image->path) }}" 
                                style="width:1000px; height:700px; transform: rotate(270deg);">
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
        @endif
    </body>
</html>
