<!DOCTYPE html>
<html>
    <head>
        <title>Disbursement Voucher</title>
        <!-- <link rel="stylesheet" href="{{ public_path('bootstrap.min.css') }}"> -->
        <style>
            /* #cover {
                background-image: url("{{realpath(__DIR__ . '/../../..').'/public/images/doh-logo.png'}}");
                background-image-resize: 6;
            } */
      
          .header{
            font-size: 11px;
            font-weight: normal;
            text-align:center;
          }
          table td{
            font-size: 11px;
          }
          /* .box-container {
            display: flex;
          }
          .box {
              width: 10px;
              height: 5px;
              border: 1px solid black;
              margin-left: 7px;
              display: inline-block;
              vertical-align: middle;
              margin-top: 1px;
              margin-bottom: 1px;
              line-height:1;
          }
          .label {
              font-size: 11px;
              display: inline-block;
              margin-right: 8px;
              margin-left: 5px;
              line-height: 1;
          } */
          /* .barcode-container {
              position: absolute;
              right: 0; 
              top: 45%; 
              transform: translateY(-50%) rotate(-90deg); 
              transform-origin: right center; 
              margin-top: 1px;
          } */
      </style>
    </head>
    <body>
      @if($result !== null)
        <div>
            <?php $div=[1,2];?>
            @foreach($div as $d)
                <div style="page-break-inside: avoid;">
                    <table class="table" style="border-collapse:collapse; width: 100%; font-size:12px">
                        <tr style="border: 1px solid black;">
                            <td width="23%" style="text-align: center; border-right:none; padding:5px"><img src="{{realpath(__DIR__ . '/../../..').'/public/images/doh-logo.png'}}" width="70"></td>
                            <td width="54%" style="border-left:none; border-right:none; text-align:center; line-height:1.4">
                                <div class="header" style="margin-top: 15px;">
                                    <span>Republic of the Philippines</span> <br>
                                    <strong> DEPARTMENT OF HEALTH</strong> <br>
                                    <i>Central Visayas Center for Health Development</i><br>
                                </div>
                            </td>
                            <td width="23%" style="text-align: center; border-left:none;"><img src="{{realpath(__DIR__ . '/../../..').'/public/images/bagong_pilipinas.png'}}" width="70"></td>
                        </tr>
                    </table>
                    <table class="table" style="border-collapse:collapse; width: 99.9%;">
                        <tr style="border: 1px solid black; border-top:0px;">
                            <td style="text-align:center;font-size:14px; width:80%"> <strong>DISBURSEMENT VOUCHER</strong></td>
                            <td style="width:20%; border-left:1px solid black; font-size:10px" >
                                <b>
                                    <span style="margin-bottom: 20px">Fund Cluster :</span><br>
                                    <span style="margin-top: 20px">Date: {{ date('F j, Y', strtotime($result->date))}}</span><br>
                                    <span>DV No. :</span>   
                                </b>    
                            </td>
                        </tr>
                    </table>
                    <table class="table" style="border-collapse:collapse; width: 99.9%;">
                        <tr style="border: 1px solid black; border-top:0px;">
                            <td style="width:10.5%; border-right:1px solid black"><b> Mode of Payment</td>
                            <td style="width:85%; border-left: 0;" >
                                <div class="" style="margin-top:20px">
                                    &nbsp;&nbsp;&nbsp;
                                    <span style="border:1px solid black; margin-right:2px"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                    <span class="label">&nbsp;MDS Check</span>
                                    &nbsp;&nbsp;&nbsp;
                                    <span style="border:1px solid black;"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                    <span class="label">&nbsp;Commercial Check</span>
                                    &nbsp;&nbsp;&nbsp;
                                    <span style="border:1px solid black;"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                    <span class="label">&nbsp;ADA</span>
                                    &nbsp;&nbsp;&nbsp;
                                    <span style="border:1px solid black;"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                    <span class="label">&nbsp;Others (Please specify)</span>
                                    <span style="margin-top:1px;">____________________</span>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <table class="table" style="border-collapse:collapse; width: 99.9%;">
                        <tr style="border: 1px solid black; border-top:0px;">
                            <td style="width:11.15%; border-right:1px solid black; padding:3px"><b> Payee</td>
                            <td style="width:39.7%; border-left: 0; border-right:1px solid black"><b> {{$pre_dv->facility->name}}</td>
                            <td style="width:29%; border-right:1px solid black; vertical-align:top; " >
                                <span style="vertical-align:top; " >Tin/Employee No. :</span>
                            </td>
                            <td style="width:20%; border-left: 0 " >
                                <span style="vertical-align:top; ">ORS/BURS No. :</span>
                            </td>
                        </tr>
                    </table>
                    <table class="table" style="border-collapse:collapse; width: 99.9%;">
                        <tr style="border: 1px solid black; border-top:0px;">                        
                            <td style="width:11.25%; border-right:1px solid black; padding:4px"><b>Address</td>
                            <td style="width: 89.5%; border-left: 0 ;vertical-align:middle; "><b>{{$pre_dv->facility->address}}</td>
                        </tr>
                    </table>
                    <table class="table table-fixed" style="border-collapse:collapse; width: 99.9%; table-layout: fixed;">
                        <tr style="border: 1px solid black; border-top:0px; width:100%;">
                            <td style="width:51%; border-right:1px solid black; text-align:center; padding:3px"> Particulars</td>
                            <td style="width:18%; border-right:1px solid black; text-align:center">Responsibility Center</td>
                            <td style="width:14%; border-right:1px solid black; text-align:center">MFO/PAP</td>
                            <td style="width:17%;">Amount</td>
                        </tr>
                        <tr style="border: 1px solid black; border-top:0px;">
                            <td style="border-right:1px solid black">
                                <p style="text-align:justify;">For reimbursement of medical services rendered to patients under the Medical 
                                Assistance for Indigent Patient Program for {{$pre_dv->facility->name}}
                                per billing statement dated {{date('F Y', strtotime($result->date_from))}} {{!Empty($result->date_to)?' - '.date('F Y', strtotime($result->date_to)):''}}
                                in the amount of:</p>
                                <table style="width: 100%; border-collapse: collapse; margin-top:5px;">
                                    @foreach($fundsources as $index=> $fund_saa)
                                        <tr>
                                            <td style="text-align: left; padding: 2px;">{{ $fund_saa['saa'] }}</td>
                                            <td style="text-align: right;">{{ number_format(floatval(str_replace(',','',$fund_saa['amount'])), 2, '.', ',') }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                                <table style="width: 100%; border-collapse: collapse; margin-top:5px;">
                                    <tr>
                                        <td style="text-align: left; padding: 2px;font-weight:bold; width:80%">Total</td>
                                        <td style="border-top:1px solid black;text-align: right; width:20%">{{ number_format($amount, 2, '.',',') }}</td>
                                    </tr>
                                </table> 
                                <?php 
                                    if ($pre_dv->prof_fee !== null) {
                                        $data_vat = ($info->vat > 3) ? $pre_dv->prof_fee / 1.12 : $pre_dv->prof_fee;
                                    } else {
                                        $data_vat = ($info->vat > 3) ? $amount / 1.12 : $amount;
                                    } 
                                    
                                    $r1d2 = $r1d3 = $r2d2 = $r2d3 = 0;
                                    if($info->vat == 3){
                                        $r1d2 = number_format($amount,2,'.',',');
                                        $r1d3 = number_format($amount * $info->vat / 100,2,'.',',') ;
                                        $r2d2 = number_format($pre_dv->prof_fee,2,'.',',');
                                        $r2d3 = number_format(($pre_dv->prof_fee * $info->ewt_pf) / 100,2,'.',',');
                                    }else if($info->vat == 5){
                                        $r1d2 = number_format($data_vat,2,'.',',');
                                        $r1d3 = number_format($data_vat * $info->vat / 100,2,'.',',');
                                        $r2d2 = number_format(($pre_dv->prof_fee / 1.12 ),2,'.',',');
                                        $r2d3 = number_format(($pre_dv->prof_fee / 1.12 * $info->ewt_pf) / 100,2,'.',',');
                                    }
                                    $r3d2 = number_format($amount - $pre_dv->prof_fee,2,'.',',');
                                    $r3d3 = number_format((($amount - $pre_dv->prof_fee) * $info->Ewt )/ 100,2,'.',',');
                                    $all_tax = number_format(str_replace(',','',$r1d3) + str_replace(',','',$r2d3) + str_replace(',','',$r3d3), 2,'.',','); 
                                    $vat_ewt = number_format((($info->vat > 3)? $amount / 1.12 * $info->Ewt / 100: $amount * $info->Ewt / 100) + $data_vat * $info->vat / 100, 2,'.',',');
                                    $date_valid = (strtotime($result->updated_at) < strtotime('2025-07-17')) ? 0 : 1;
                                ?>
                                <table style="width: 100%; border-collapse: collapse; margin-top:5px;">
                                    @if($date_valid == 0)
                                        <tr>
                                            <td style="text-align: left;">{{ floor($info->vat) == 3 ? floor($info->vat) . '%' . ' ' . 'Percentage Tax' : floor($info->vat) . '%' . ' ' . 'VAT' }}</td>
                                            <td style="text-align: right;">{{ number_format($data_vat,2,'.',',') }}</td>
                                            <td style="text-align: right;">{{ number_format($data_vat * $info->vat / 100,2,'.',',') }}</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left;">{{ floor($info->Ewt).'%'.' '.'EWT' }}</td>
                                            <td style="text-align: right;">{{ floor($info->vat) == 3 ? number_format((($info->vat > 3)?$amount / 1.12 : $amount),2,'.',','): number_format((($info->vat > 3)?$amount / 1.12 : $amount),2,'.',',') }}</td>
                                            <td style="text-align: right;">{{ number_format((($info->vat > 3)? $amount / 1.12 * $info->Ewt / 100: $amount * $info->Ewt / 100),2,'.',',') }}</td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td style="text-align: left; width:60%">
                                                {{ 
                                                    floor($info->vat) == 3 
                                                        ? floor($info->vat) . '%' . ' ' . 'Percentage Tax on Total Hospital Bill' 
                                                        : floor($info->vat) . '%' . ' ' . 'VAT on Professional Fee' 
                                                    }}
                                            </td>
                                            <td style="text-align: right; width:20%">
                                                {{ $r1d2 }}
                                            </td>
                                            <td style="text-align: right; width:20%">
                                                {{ $r1d3 }}
                                            </td> 
                                        </tr>
                                        <tr>
                                            <td style="text-align: left;">
                                                {{ 
                                                    floor($info->vat) == 3 
                                                        ? floor($info->ewt_pf) . '%' . ' ' . 'EWT on Professional Fee' 
                                                        : floor($info->ewt_pf) . '%' . ' ' . 'EWT on Professional Fee'
                                                    }} 
                                            </td>
                                            <td style="text-align: right;">
                                                {{ $r2d2 }}
                                            </td>
                                            <td style="text-align: right;">
                                                {{ $r2d3 }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left;">
                                                {{ 
                                                    floor($info->vat) == 3 
                                                        ? floor($info->Ewt) . '%' . ' ' . 'EWT on Hospital Fee' 
                                                        : floor($info->Ewt) . '%' . ' ' . 'EWT on Hospital Bills'
                                                    }} 
                                            </td>
                                            <td style="text-align: right;">
                                                {{ $r3d2 }}
                                            </td>
                                            <td style="text-align: right;">
                                                {{ $r3d3 }}
                                            </td>
                                        </tr>
                                       
                                    @endif
                                </table>
                                <table style="width: 100%; border-collapse: collapse; margin-top:5px;">
                                    <tr>
                                        <td style="text-align: left;" colspan="3">Control No:{{ $control}}</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td style="padding:3px"><b>Amount Due</b></td>
                                        <td></td>
                                    </tr>
                                </table>
                                <br>
                            </td>
                            <?php 
                                $amount_here = number_format($amount, 2, '.',',');
                                $one = number_format((($info->vat > 3)? $amount / 1.12 * $info->vat / 100: $amount * $info->vat / 100),2,'.',',') ;
                                $two = number_format((($info->vat > 3)? $amount / 1.12 * $info->Ewt / 100: $amount * $info->Ewt / 100),2,'.',',');
                                $overall = number_format((str_replace(',', '', $one) + str_replace(',', '', $two)), 2, '.', ',');
                                $rem = number_format((str_replace(',', '', $amount_here) - str_replace(',', '', $vat_ewt)), 2, '.', ',');
                            ?>
                            <td style="border-right:1px solid black"></td>
                            <td style="border-right:1px solid black"></td>
                            <td style="text-align:center; vertical-align:bottom">
                                <table style="width: 90%; border-collapse: collapse; margin-top:10px;">
                                    <tr rowspan="5">
                                        <td >{{ $amount_here }}</td>
                                    </tr>
                                    <tr rowspan="5">
                                        <td style="padding:20px">{{ $date_valid == 0 ? $vat_ewt : $all_tax}}</td>
                                    </tr>
                                    <tr><td style="border-bottom:1px solid black;"></td></tr>
                                    <tr>
                                        <td style="padding:2px">
                                            {{ $date_valid == 0 ? $rem : number_format(str_replace(',','',$amount_here) - str_replace(',','',$all_tax), 2, '.', ',') }}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <table class="table" style="border-collapse:collapse; width: 99.9%;">
                        <tr style="border:1px solid black; border-bottom:none">
                            <td><span>A. Certified: Expenses/Cash Advance necessary, lawful and incurred under my direct supervision.</span></td>
                        </tr>
                        <tr style="border: 1px solid black; border-top:0px;">
                            <td style="text-align:center">
                                <table style="width: 40%; border-collapse: collapse; margin-top:10px">
                                    <tr><td style="border-bottom:1px solid black">JONATHAN NEIL V. ERASMO, MD, MPH, FPSMS</td></tr>
                                    <tr><td style="">OIC - Director III</td></tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <table class="table" style="border-collapse:collapse; width: 99.9%;">
                        <tr style="border: 1px solid black; border-top:0px;">
                            <td height=1.5% width =15%><strong>B. Accounting Entry:</strong></td>
                        </tr>
                    </table>
                    <table class="table" style="border-collapse:collapse; width: 100%; line-height:1">
                        <tr class="header" style="border: 1px solid black; border-top:0px;">
                            <td style="border-right:1px solid black; width:50%; padding:3px">Account Title</td>
                            <td style="width:20%; border-left: 0; vertical-align:top; border-right:1px solid black; " >Uacs Code</td>
                            <td style="width:15%; border-left: 0; border-right:1px solid black;" >Debit</td>
                            <td style="width:15%; border-left: 0; " >Credit</td>
                        </tr>
                        <tr class="header" style="border: 1px solid black; border-top:0px;">
                            <td height=6% style="text-align : left;vertical-align:top; border-right:1px solid black;">
                                &nbsp;&nbsp;&nbsp;&nbsp;<span>Subsidy / Others</span><br>
                                &nbsp;&nbsp;&nbsp;&nbsp;<span>Accumulated Surplus</span><br>
                                <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Due to BIR</span><br> 
                                <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; CIB-MDS</span> 
                            </td>
                            <td style="border-right:1px solid black;" >
                                <span>50214990</span><br>
                                <span>30101010</span><br>
                                <span>20201010</span><br> 
                                <span>10104040</span> 
                            </td>
                            <td style="border-right:1px solid black;; text-align:right; vertical-align:top" >
                                <span>
                                    {{ number_format((($result)? $amount - $result->accumulated: $amount),2,'.',',')}}
                                </span><br>
                                <span>{{ number_format(($result?$result->accumulated:0),2,'.',',') }}</span>
                            </td>
                            <td style=" border-left: 0 ; text-align:right; vertical-align:top" >
                                <br><br><span>{{ $date_valid == 0 ? $vat_ewt : $all_tax }}</span><br>
                                <span>{{ $date_valid == 0 ? $rem : number_format(str_replace(',','',$amount_here) - str_replace(',','',$all_tax), 2, '.', ',') }}</span>
                            </td>
                        </tr>
                    </table>
                    <table class="table" style="border-collapse:collapse; width: 99.9%; line-height:1">
                        <tr style="border: 1px solid black; border-top:0px;">
                            <td style="border-right:1px solid black; width:50%;  padding:1px"><strong>C. Certified:</strong></td>
                            <td><strong>D. Approved for Payment:</strong></td>
                        </tr>
                        <tr class="header" style="border: 1px solid black; border-top:0px;">
                            <td style="border-right:1px solid black; width:50%;">
                                <div style="margin-top:20px; line-height:1">
                                    <img src="\maif\public\images\box_16.png">
                                    <span>Cash Available</span><br>
                                    <img src="\maif\public\images\box_16.png">
                                    <span>Subject to Authority to Debit Account (when applicable)</span><br>
                                    <img src="\maif\public\images\box_16.png">
                                    <span>Supporting documents complete and amount claimed proper</span><br>
                                </div>
                            </td>
                            <td></td>  
                        </tr>
                    </table>
                    <table class="table" style="border-collapse:collapse; width: 100%;">
                        <tr class="header" style="border: 1px solid black; border-top:0px;">
                            <td style="border-right:1px solid black; width:12%; padding:5px">Signature</td>
                            <td style="border-right:1px solid black; width:38.1%;"></td>
                            <td style="border-right:1px solid black; width:12%;">Signature</td>
                            <td style="border-right:1px solid black; width:38%;"></td> 
                        </tr>
                        <tr class="header" style="border: 1px solid black; border-top:0px;">
                            <td style="border-right:1px solid black; width:12%; padding:4px">Printed Name</td>
                            <td style="border-right:1px solid black; width:38%;"><b>ANGIELINE T. ADLAON, CPA, MBA</td>
                            <td style="border-right:1px solid black; width:12%;">Printed Name</td>
                            <td style="border-right:1px solid black; width:38%;"><b>JOSHUA G. BRILLANTES, MD, MPH, CESO IV</td>
                        </tr>
                        <tr class="header" style="border: 1px solid black; border-top:0px;">
                            <td style="border-right:1px solid black; width:12%;">Position</td>
                            <td style="border-right:1px solid black; width:38%;">
                                <table width=100% style="text-align:center; line-height:1" border=0>
                                    <tr>
                                        <td style="border-bottom: 1px solid black">Head, Accounting Section</td>
                                    </tr>
                                    <tr>
                                        <td>Head, Accounting Unit/Authorized Representative</td>
                                    </tr>
                                </table>
                            </td>
                            <td style="border-right:1px solid black; width:12%;">Position</td>
                            <td style="border-right:1px solid black; width:38%;">
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
                        <tr class="header" style="border: 1px solid black; border-top:0px;">
                            <td style="border-right:1px solid black; width:12%; padding:5px">Date</td>
                            <td style="border-right:1px solid black; width:38%;"></td>
                            <td style="border-right:1px solid black; width:12%;">Date</td>
                            <td style="border-right:1px solid black; width:38%;"></td>
                        </tr>
                    </table>
                    <table class="table" style="border-collapse:collapse; width: 100%;">
                        <tr class="header" style="border: 1px solid black; border-top:0px;">  
                            <td colspan="4" style="vertical-align:top; border-right:1px solid black; border-bottom:none; width:80%; padding:5px"><b>E. Receipt of Payment</b></td>
                            <td rowspan="2" style="vertical-align:top;width:20%;">JEV No.</td>
                        </tr>
                        <tr class="header" style="border: 1px solid black; border-top:0px;">
                            <td style="border-right:1px solid black; width:12%;">Check/ADA No.:</td>
                            <td style="border-right:1px solid black; width:25%;"></td>
                            <td style="border-right:1px solid black; width:15%; vertical-align:top">Date:</td>
                            <td style="border-right:1px solid black; width:32%; vertical-align:top">Bank Name & Account Number:</td>
                        </tr>
                        <tr class="header" style="border: 1px solid black; border-top:0px;">
                            <td style="border-right:1px solid black; width:12%; padding:5px">Signature:</td>
                            <td style="border-right:1px solid black; width:25%;"></td>
                            <td style="border-right:1px solid black; width:15%; vertical-align:top">Date:</td>
                            <td style="border-right:1px solid black; width:32%; vertical-align:top">Printed Name:</td>
                            <td rowspan="2" style="vertical-align:top;">Date</td>
                        </tr>
                        <tr class="header" style="border: 1px solid black; border-top:0px;">
                            <td colspan="4" width =31% style="vertical-align:top; padding:4px">Official Receipt No. & Date/Other Documents</td>
                        </tr>
                    </table>        
                </div>
                <!-- <div>
                    <div class="barcode-container" style="position:absolute;text-align: center; margin-top: -450px; left: 0; margin-right: -735px;background-color:yellow">
                        <img src="data:image/png;base64,{{ $barcodePNG }}" alt="Barcode"
                            style="transform: rotate(-90deg); writing-mode: vertical-lr; text-align:left;"/>
                        <br><br>
                    </div>
                    <div class="barcode-container" style="text-align: center; margin-top: -105px; position: absolute; left: 0; margin-right: -780px;">
                        <img src="{{realpath(__DIR__ . '/../../..').'/public/images/route.png'}}" alt="Barcode" style="margin-top:10px"/>
                    </div>
                </div> -->
                <!-- <div class="barcode-container" style="text-align: center;line-height:1">
                    <br style="line-height:1px">
                    <font class="route_no" style="">{{ $result->route_no }}</font>
                    {!! DNS1D::getBarcodeHTML($result->route_no, 'C39E', 1, 25) !!}
                </div> -->
                <div>
                    <div class="barcode-container" style="position:absolute;text-align: center; margin-top: -400px; left: 0; margin-right: -720px;">
                        <img src="{{ realpath(__DIR__ . '/../../..') . '/public/images/' . $route_no . '.png' }}" alt="Barcode"
                            style="transform: rotate(-90deg); writing-mode: vertical-lr; text-align:left;"/>
                        <br><br>
                    </div>
                    <div class="barcode-container" style="text-align: center; margin-top: -70px; position: absolute; left: 0; margin-right: -735px;">
                        <img src="data:image/png;base64,{{ $barcodePNG }}" style="transform: rotate(-90deg); writing-mode: vertical-lr; text-align:left; margin-top:30px"/>
                    </div>
                </div>
                <!-- <div class="barcode-container" style="text-align: center;line-height:1">
                    <br style="line-height:1px">
                    <img src="data:image/png;base64,{{ $barcodePNG }}" alt="Barcode"style=""/>
                </div> -->
                @if($d == 1)
                  <div style="page-break-before: always;"></div>
                @endif
            @endforeach
            @if($result->saa_exclusion == 0)
                @foreach($fundsources as $index => $fund_saa)
                    @if($fund_saa['path'])
                        <div style="page-break-before: always;"></div>
                        <!-- <table style="width: 100%; border-collapse: collapse;">
                            <tr style="height: 1200px;">
                                <td style="width: 95%; text-align: center; height: 1200px;">
                                    <img src="{{ url('storage/app/rotate/' . $fund_saa['path']) }}" 
                                        style="max-width: 100%; max-height: 1500px; object-fit: contain;">
                                </td>
                                <td style="width: 5%; text-align: center; vertical-align: middle; height: 1200px;">
                                    <table style="height: 100%;">
                                        <tr text-rotate="-270">
                                            <td style="font-size:16px" rotate="-270">
                                                {{$fund_saa['saa']}}
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table> -->
                        <div style="margin-left: 1px; margin-right: 1px; height:auto; text-align: center;">
                            <div id="cover" style="position: absolute; left: 0; right: 0; top: 0; bottom: 0; height:98%;
                                background-image: url('{{ url('storage/app/rotate/' . $fund_saa['path']) }}');
                                background-size: contain;
                                background-repeat: no-repeat;
                                background-position: center;">
                            </div>
                            <span>{{ $fund_saa['saa'] }}</span>
                        </div> 
                    @endif
                @endforeach
            @endif
            <!-- <div style="page-break-before: always;"></div>
            <div style="width: 100%; text-align:center;margin-top:5px">
                <h6><b>V1 - {{ $pre_dv->facility->name }}</b><h6>
            </div>
            <div style="width:100%; border:1px solid black; padding:10px">
                @foreach($pre_dv->extension as $index => $row)
                    <table class="table" style="width: 100%;">
                        @if($index == 0)
                            <tr class="header" style="border: 1px solid black;text-align:center">  
                                <td style="vertical-align:top; border:1px solid black; width:40%; padding:4px; text-align:center"><b>SAA</b></td>
                                <td style="vertical-align:top; border:1px solid black; width:18%; padding:4px; text-align:center"><b>AMOUNT</b></td>
                                <td style="vertical-align:top; border:1px solid black; width:40%; padding:4px; text-align:center"><b>PROPONENT</b></td>
                            </tr>
                        @endif
                        @foreach ($row->saas as $data)
                            <tr class="header" style="border: 1px solid black;text-align:center">  
                                <td style="vertical-align:top; border:1px solid black; width:40%; padding:4px; text-align:center">{{ $data->saa->saa }}</td>
                                <td style="vertical-align:top; border:1px solid black; width:18%; padding:4px; text-align:center">{{ number_format(str_replace(',', '', $data->amount), 2, '.',',') }}</td>
                                <td style="vertical-align:top; border:1px solid black; width:40%; padding:4px; text-align:center">{{ $row->proponent->proponent }}</td>
                            </tr>     
                        @endforeach
                    </table>  
                @endforeach
                <table class="table" style="width: 100%; margin-top:15px">
                    <tr class="header" style="border: 1px solid black;text-align:center">  
                        <td style="vertical-align:top; border:none; width:30%; padding:4px; text-align:center"></td>
                        <td style="vertical-align:top; border:1px solid black; width:40%; padding:4px; text-align:center"><b>{{ number_format($pre_dv->grand_total,2,'.',',') }}</b></td>
                        <td style="vertical-align:top; border:none; width:30%; padding:4px; text-align:center"></td>
                    </tr>                        
                </table> 
            </div> -->
        @endif
    </body>
</html>
