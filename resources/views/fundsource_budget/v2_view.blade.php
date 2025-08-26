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
<form  method="post" action="{{ route('pre_dv.process') }}" id ="new_form"> 
    @csrf   
    <input type="hidden" name="id" value="{{$result->id}}">
    <input type="hidden" name="extension_id" value="{{$result->id}}">
    <input type="hidden" name="type" value="{{$type}}">
    <input type="hidden" class="new_dv_id" name="new_dv_id" value="{{$new_dv?$new_dv->route_no :0}}">
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
                                    &nbsp;&nbsp;&nbsp;&nbsp;<input type="date" asp-for="Date" name="date" style="width: 150px; height: 28px; font-size:8pt" value="{{$new_dv?$new_dv->date :(new DateTime())->format('Y-m-d')}}" readonly>
                                    <br>
                                    <div>
                                        <span>DV No:  {{$new_dv->dv_no}}</span>
                                        @if(Auth::user()->userid == 1027 || Auth::user()->userid == 2660)
                                        &nbsp;<input type="text" name="dv_no" id="dv_no" style="width:150px; height: 28px;" class="ft15" required>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <table border="2" style="width: 100%;border-top: 0px;">
                            <tr height=30px>
                                <td  width =11.85% height= 3%><b> Mode of Payment</td>
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
                                <td height=4% width =11.8%><b> Payee</td>
                                <td style="width:29%; border-left: 0 "><b> 
                                    <input type="hidden" id ="for_facility_id">
                                    <input id="facilityDropdown" name="facilityname" value="{{$result->facility->name}}" style="margin-left:5px;width:260px;" class="form-control" readonly>
                                </td>
                                <td style="width:28%; border-left: 0 " >
                                    <span>Tin/Employee No. :</span>
                                </td>
                                <td style="width:27%; border-left: 0 " >
                                    @if( $type == 'awaiting')
                                        <div style="display: flex; align-items: center;">
                                            <span style="vertical-align: middle;">ORS/BURS No. : *</span>
                                            <textarea name="ors_no" style="width:150px; height:30px; margin-left: 10px;" class="form-control" required>{{ $ors }}</textarea>
                                        </div>
                                        <div style="display: flex; align-items: center;">
                                            <span style="vertical-align: middle;">Date of Obligation : </span>
                                            &nbsp;<input class="form-control" type="date" asp-for="Obligated" name="obligated_on" style="width: 150px; height: 28px; font-size:8pt" value="{{$new_dv?$new_dv->obligated_on :(new DateTime())->format('Y-m-d')}}" required>
                                        </div>
                                    @else
                                        <span>ORS/BURS No. : {{$new_dv->ors_no}}</span>
                                    @endif
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
                                    <input type="month" name="date_from" style="width: 110px; height: 28px; font-size: 8pt;" value="{{$new_dv ? date('Y-m', strtotime($new_dv->date_from)) : ''}}" readonly>
                                    <input type="month" name="date_to" style="width: 110px; height: 28px; font-size: 8pt;" value="{{$new_dv && $new_dv->date_to ? date('Y-m', strtotime($new_dv->date_to)) : ''}}" readonly>
                                    in the amount of:</p><br>
                                    <div style="padding:20px;">
                                        <table style="width: 100%; border-collapse: collapse; margin-bottom:15px;">
                                            @foreach($fundsources as $index=> $fund_saa)
                                                <tr>
                                                    <td style="text-align: left; padding: 2px;">{{ $fund_saa['saa'] }}</td>
                                                    <td style="text-align: right;">{{ number_format(floatval(str_replace(',','',$fund_saa['amount'])), 2, '.', ',') }}</td>
                                                </tr>
                                            @endforeach
                                        </table>
                                        <?php
                                            if ($result->prof_fee !== null && $result->prof_fee != 0) {
                                                $data_vat = ($info->vat > 3) ? $result->prof_fee / 1.12 : $result->prof_fee;
                                            } else {
                                                $data_vat = ($info->vat > 3) ? $amount / 1.12 : $amount;
                                            }                                        
                                            $vat_ewt = number_format((($info->vat > 3)? $amount / 1.12 * $info->Ewt / 100: $amount * $info->Ewt / 100) + $data_vat * $info->vat / 100, 2,'.',',');
                                            //newly added way of calculation
                                            $r1d2 = $r1d3 = $r2d2 = $r2d3 = 0;
                                            if($info->vat == 3){
                                                $r1d2 = number_format($amount,2,'.',',');
                                                $r1d3 = number_format($amount * $info->vat / 100,2,'.',',') ;
                                                $r2d2 = number_format($result->prof_fee,2,'.',',');
                                                $r2d3 = number_format(($result->prof_fee * $info->ewt_pf) / 100,2,'.',',');
                                            }else if($info->vat == 5){
                                                $r1d2 = number_format($data_vat,2,'.',',');
                                                $r1d3 = number_format($data_vat * $info->vat / 100,2,'.',',');
                                                $r2d2 = number_format(($result->prof_fee / 1.12 ),2,'.',',');
                                                $r2d3 = number_format(($result->prof_fee / 1.12 * $info->ewt_pf) / 100,2,'.',',');
                                            }
                                            $r3d2 = number_format($amount - $result->prof_fee,2,'.',',');
                                            $r3d3 = number_format((($amount - $result->prof_fee) * $info->Ewt )/ 100,2,'.',',');
                                            $all_tax = number_format(str_replace(',','',$r1d3) + str_replace(',','',$r2d3) + str_replace(',','',$r3d3), 2,'.',','); 
                                            $date_valid = (strtotime($result->updated_at) < strtotime('2025-07-17')) ? 0 : 1;
                                        ?>
                                        <table style="width: 100%; border-collapse: collapse; margin-top:5px; padding: 20px;">
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
                                                    <td style="text-align: left; width:60%; line-height:2">
                                                        {{ 
                                                            floor($info->vat) == 3 
                                                                ? floor($info->vat) . '%' . ' ' . 'Percentage Tax on Total Hospital Bill' 
                                                                : floor($info->vat) . '%' . ' ' . 'VAT on Professional Fee' 
                                                            }}
                                                    </td>
                                                    <td style="text-align: right; width:20%; line-height:2">
                                                        {{ $r1d2 }}
                                                    </td>
                                                    <td style="text-align: right; width:20%; line-height:2">
                                                        {{ $r1d3 }}
                                                    </td> 
                                                </tr>
                                                <tr>
                                                    <td style="text-align: left; line-height:2">
                                                        {{ 
                                                            floor($info->vat) == 3 
                                                                ? floor($info->ewt_pf) . '%' . ' ' . 'EWT on Professional Fee' 
                                                                : floor($info->ewt_pf) . '%' . ' ' . 'EWT on Professional Fee'
                                                            }} 
                                                    </td>
                                                    <td style="text-align: right; line-height:2">
                                                        {{ $r2d2 }}
                                                    </td>
                                                    <td style="text-align: right; line-height:2">
                                                        {{ $r2d3 }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align: left; line-height:2">
                                                        {{ 
                                                            floor($info->vat) == 3 
                                                                ? floor($info->Ewt) . '%' . ' ' . 'EWT on Hospital Fee' 
                                                                : floor($info->Ewt) . '%' . ' ' . 'EWT on Hospital Bills'
                                                            }} 
                                                    </td>
                                                    <td style="text-align: right; line-height:2">
                                                        {{ $r3d2 }}
                                                    </td>
                                                    <td style="text-align: right; line-height:2">
                                                        {{ $r3d3 }}
                                                    </td>
                                                </tr>
                                            
                                            @endif
                                        </table>
                                        <table style="width: 100%; border-collapse: collapse; margin-top:5px;">
                                            <tr>
                                                <td style="text-align: left; line-height:2" colspan="3">Control No:{{ $control}}</td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td style="padding:3px; line-height:2"><b>Amount Due</b></td>
                                                <td></td>
                                            </tr>
                                        </table>
                                    </div>    
                                </td>
                                <td style="width:14%; border-left: 0 " ></td>
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
                                    <div style="padding:20px">    
                                        <table style="width: 90%; border-collapse: collapse;">
                                            <tr rowspan="5">
                                                <td >{{ $amount_here }}</td>
                                                <input type="hidden" value="{{ str_replace(',','',$amount_here) }}" name="total_amount">
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
                                            <span style="margin-left:350px"><strong>JONATHAN NEIL V. ERASMO, MD, MPH, FPSM<strong></span>
                                        </div>
                                        <br>
                                        <span style="margin-left:420px">OIC - Director III</span>
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
                                <br>
                                <span>50214990</span><br>
                                <span>30101010</span><br>
                                <span>20201010</span><br> 
                                <span>10104040</span> <br><br>
                                </td>
                                <td style="width:20%; border-left: 0 ; text-align:right; vertical-align:top" >
                                <br>
                                <span id="total_debit" class="debit_after">{{number_format(($new_dv? $amount - $new_dv->accumulated: $amount),2,'.',',')}}</span><br>
                                <input type="text" class="accumulated" id="accumulated" name="accumulated" style="margin-top:1px;width:120px; height: 20px; text-align:right;" oninput="calculateSubsidy(this)" onkeyup="validateAmount(this)" value="{{$new_dv?number_format($new_dv->accumulated,2,'.',','):0}}"autocomplete="off">
                                </td>
                                <td style="width:20%; border-left: 0 ; text-align:right; vertical-align:top" >
                                <br><br><br>
                                <span>{{ $date_valid == 0 ? $vat_ewt : $all_tax }}</span><br>
                                <span>{{ $date_valid == 0 ? $rem : number_format(str_replace(',','',$amount_here) - str_replace(',','',$all_tax), 2, '.', ',') }}</span>
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
                                <td width =32%><b>JOSHUA G. BRILLANTES, MD, MPH, CESO IV</td>
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
                                            <td>Agency Head/Authorized Representative</td>
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
                                <td width =15%>Date:</td>
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
                    <button id="close_btn" style="background-color:lightgray; border-radius:0px" type="button" class="btn btn-sm btn" data-dismiss="modal"><i class="typcn typcn-times"></i>Close</button>
                    @if( $type == 'awaiting')
                        <button type="submit" style="border-radius:0px" id="submitBtn" class="btn btn-sm btn-primary"><i class="typcn typcn-tick menu-icon"></i>Obligate</button>
                    @elseif( $type == 'deferred')
                        <button type="submit" style="border-radius:0px" id="submitBtn" class="btn btn-sm btn-primary"><i class="typcn typcn-tick menu-icon"></i>Pay</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</form>