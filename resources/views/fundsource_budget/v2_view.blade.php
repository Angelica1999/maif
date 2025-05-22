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
                                        <div style="display: flex; align-items: center; flex-wrap: wrap;">
                                            <?php $amount = 0;?>
                                            @foreach($fundsources as $row)
                                                <input value="{{$row['saa']}}" name="saa" style="width: 200px; height: 42px; margin-bottom:5px; margin-left:5%" readonly>
                                                <input value="{{number_format(str_replace(',','',$row['amount']), 2, '.', ',')}}" name="amount" style="width:120px; height: 42px; margin-bottom:5px; margin-left:1%" class="ft15" readonly>
                                                <input value="{{number_format(str_replace(',','',$row['vat']), 2, '.', ',')}}" name="vat" style="margin-left: 8px; width: 80px; height: 42px; margin-bottom:5px" readonly>
                                                <input value="{{number_format(str_replace(',','',$row['ewt']), 2, '.', ',')}}" name="ewt" style="width: 80px; height: 42px; margin-bottom:5px; margin-left:1%" readonly>
                                                <?php $amount += $row['amount']; ?>
                                            @endforeach 
                                        </div>
                                    <br><br>
                                    <div>
                                        <span style="margin-left:20px" class="saa">Vat : </span>
                                        <input type="text" name="in_vat" value="{{(int)$info->vat}}" id="vat" style="margin-left:32px;width:40px; height: 25px;" oninput="" readonly>
                                        <input style="width:80px; height: 25px;" name="vat_val" value="{{number_format((($info->vat > 3)?$amount / 1.12 : $amount),2,'.',',')}}" readonly>
                                        <input name="vat_d" value="{{number_format((($info->vat > 3)? $amount / 1.12 * $info->vat / 100: $amount * $info->vat / 100),2,'.',',')}}" style="width:100px; height: 25px; font-size: 8pt" readonly>
                                    </div><br>
                                    <div padding-top:10px>
                                        <span style="margin-left:19.5px; margin-top:10px;" class="saa">Ewt : </span>
                                        <input value="{{(int)$info->Ewt}}" name="in_ewt" style="margin-left:31px;width:40px; height: 25px;" readonly>
                                        <input style="margin-left:0px; width:80px; height: 25px;" name="ewt_val" value="{{number_format((($info->vat > 3)?$amount / 1.12 : $amount),2,'.',',')}}" readonly>
                                        <input name="ewt_d" value="{{number_format((($info->vat > 3)? $amount / 1.12 * $info->Ewt / 100: $amount * $info->Ewt / 100),2,'.',',')}}" style="width:100px; height: 25px; font-size: 8pt" readonly>
                                    </div><br><br>
                                    <div>
                                        <span class="saa">Ref No:</span>
                                        <input name="control_no" value="{{$control}}" style="width:185px; height: 28px;" readonly>
                                    </div>
                                    <br><br>
                                    <span style="margin-left:200px; font-weight:bold">Amount Due</span> 
                                </td>
                                <td style="width:14%; border-left: 0 " ></td>
                                <td style="width:14%; border-left: 0 " ></td>
                                <td style="width:14%; border-left: 0; vertical-align:bottom" class= "header" >
                                    <p id="total_amount" style=" margin-top:10px">{{$amount}}</p>
                                    <input name="total_amount" type="hidden" value="{{$amount}}">
                                    <input type="hidden" name="total">
                                    <br><br><br><br><br>
                                    <p>{{number_format(((($info->vat > 3)? $amount / 1.12 * $info->vat / 100: $amount * $info->vat / 100) + (($info->vat > 3)? $amount / 1.12 * $info->Ewt / 100: $amount * $info->Ewt / 100)),2,'.',',')}}</p>
                                    <br><br><br><br><br>
                                    <input type="hidden" name="totalDeduction" id="totalDeductionInput" class="ft15 totalDeduction">
                                    <span style="text-align:center;">______________</span>
                                    <p style="">{{ number_format(($amount - ((($info->vat > 3)? $amount / 1.12 * $info->vat / 100: $amount * $info->vat / 100) + (($info->vat > 3)? $amount / 1.12 * $info->Ewt / 100: $amount * $info->Ewt / 100))),2,'.',',')}}</p>
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
                                    <br>
                                    <span>50214990</span><br>
                                    <span>30101010</span><br>
                                    <span>20201010</span><br> 
                                    <span>10104040</span> <br><br>
                                </td>
                                <td style="width:20%; border-left: 0 ; text-align:right; vertical-align:top" >
                                    <br>
                                    <span id="total_debit">{{number_format(($new_dv? $amount - $new_dv->accumulated: $amount),2,'.',',')}}</span><br>
                                    <input type="text" class="accumulated" name="accumulated" style="margin-top:1px;width:120px; height: 20px; text-align:right;" oninput="calculateSubsidy()" onkeyup="validateAmount(this)" value="{{$new_dv?$new_dv->accumulated:0}}"autocomplete="off" readonly>
                                </td>
                                <td style="width:20%; border-left: 0 ; text-align:right; vertical-align:top" >
                                    <br><br><br>
                                    <span>{{ number_format((($info->vat > 3) ? $amount / 1.12 * $info->vat / 100 : $amount * $info->vat / 100) + (($info->vat > 3) ? $amount / 1.12 * $info->Ewt / 100 : $amount * $info->Ewt / 100), 2, '.',',') }}</span><br>
                                    <span>{{ number_format(($amount - ((($info->vat > 3)? $amount / 1.12 * $info->vat / 100: $amount * $info->vat / 100) + (($info->vat > 3)? $amount / 1.12 * $info->Ewt / 100: $amount * $info->Ewt / 100))), 2, '.',',')}}</span>
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