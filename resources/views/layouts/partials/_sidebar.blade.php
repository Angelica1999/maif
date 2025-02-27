<style>
    @font-face{
        font-family:'Glyphicons Halflings';src:url('http://netdna.bootstrapcdn.com/bootstrap/3.0.0/fonts/glyphicons-halflings-regular.eot');src:url('http://netdna.bootstrapcdn.com/bootstrap/3.0.0/fonts/glyphicons-halflings-regular.eot?#iefix') format('embedded-opentype'),url('http://netdna.bootstrapcdn.com/bootstrap/3.0.0/fonts/glyphicons-halflings-regular.woff') format('woff'),url('http://netdna.bootstrapcdn.com/bootstrap/3.0.0/fonts/glyphicons-halflings-regular.ttf') format('truetype'),url('http://netdna.bootstrapcdn.com/bootstrap/3.0.0/fonts/glyphicons-halflings-regular.svg#glyphicons-halflingsregular') format('svg');}.glyphicon{position:relative;top:1px;display:inline-block;font-family:'Glyphicons Halflings';font-style:normal;font-weight:normal;line-height:1;-webkit-font-smoothing:antialiased;}
        .glyphicon-ok:before{content:"\e013";}
        .glyphicon-remove:before{content:"\e014";
    }
    .nav-item .sub-menu {
        display: none;
    }
    .nav-item:hover .sub-menu {
        display: block;
    }
    .sidebar-note {
        width: 90%;
        background-color: #fff3cd;
        padding: 10px;
        margin: 5px 0 0 15px;
        border: 1px solid #ffeeba; 
        border-radius: 4px;
        font-size: 14px; 
        color: #856404; 
    }
    .nav-item label {
        font-weight: bold;
        display: block;
        margin: 0 0 5px 10px;
        color:green;
    }
</style>
<?php 
    use App\Models\Notes;

    if(!Auth::user()){
      return route('logout');
    }

    $id = Auth::user()->userid;
    $joinedData = DB::connection('dohdtr')
                    ->table('users')
                    ->leftJoin('dts.users', 'users.userid', '=', 'dts.users.username')
                    ->where('users.userid', '=', $id)
                    ->select('users.section', 'users.division')
                    ->first();
    $notes = Notes::where('created_by', $id)->with('user')->get();
?>
<nav class="sidebar sidebar-offcanvas" id="sidebar" style="width:280px">
    <ul class="nav">
        <li class="nav-item">
            <div class="d-flex sidebar-profile">
                <div class="sidebar-profile-image">
                    <img src="{{ asset('images/doh-logo.png') }}" alt="image">
                    <span class="sidebar-status-indicator"></span>
                </div>
                <div class="sidebar-profile-name">
                    <p class="sidebar-name">
                        {{ Auth::user()->name }}
                    </p>
                    <p class="sidebar-designation">
                        Welcome
                    </p>
                </div>
            </div>
            <p class="sidebar-menu-title">Dash menu</p>
        </li>
        @if($joinedData->section == 6)
            <ul class="nav flex-column" style=" margin-bottom: 0;">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('fundsource_budget') }}">
                        <img src="\maif\public\images\funds_16.png" style="margin-right:12px">
                        <span class="menu-title">Fundsource</span>
                        &nbsp;&nbsp;<i class="typcn typcn-arrow-sorted-down menu-icon"></i>
                    </a>
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('fundsource_budget') }}">
                                <img src="\maif\public\images\budget_funds_16.png" style="margin-right:12px">
                                <span class="menu-title">BUDGET</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('fundsource') }}">
                                <img src="\maif\public\images\maip_funds_16.png" style="margin-right:12px">
                                <span class="menu-title">MAIFIPP</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <ul class="nav flex-column" style=" margin-bottom: 0;">
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <img src="\maif\public\images\dv_16.png" style="margin-right:12px">
                        <span class="menu-title">Disbursement Voucher (1)</span>
                        &nbsp;&nbsp;<i class="typcn typcn-arrow-sorted-down menu-icon"></i>
                    </a>
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('fundsource_budget.pendingDv', ['type' => 'pending']) }}">
                                <img src="\maif\public\images\pending_16.png" style="margin-right:12px">
                                <span class="menu-title">Pending DV</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('fundsource_budget.pendingDv', ['type' => 'obligated']) }}">
                                <img src="\maif\public\images\paid_bill_16.png" style="margin-right:12px">
                                <span class="menu-title">Obligated DV</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <ul class="nav flex-column" style=" margin-bottom: 0;">
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <img src="\maif\public\images\dv3_16.png" style="margin-right:12px">
                        <span class="menu-title">Disbursement Voucher (3)</span>
                            &nbsp;&nbsp;<i class="typcn typcn-arrow-sorted-down menu-icon"></i>
                    </a>
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('budget.dv3', ['type' => 'unsettled']) }}">
                                <img src="\maif\public\images\pending_16.png" style="margin-right:12px">
                                <span class="menu-title">Pending DV3</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('budget.dv3', ['type' => 'processed']) }}">
                                <img src="\maif\public\images\paid_bill_16.png" style="margin-right:12px">
                                <span class="menu-title">Obligated DV3</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <ul class="nav flex-column" style=" margin-bottom: 0;">
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <img src="\maif\public\images\new_16.png" style="margin-right:12px">
                        <span class="menu-title">Disbursement Vouchers (new)</span>
                            &nbsp;&nbsp;<i class="typcn typcn-arrow-sorted-down menu-icon"></i>
                    </a>
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                        <a class="nav-link" href="{{ route('pre_dv_budget', ['type' => 'awaiting']) }}">
                            <img src="\maif\public\images\pending_16.png" style="margin-right:12px">
                            <span class="menu-title">Pending DV (new)</span>
                        </a>
                        </li>
                        <li class="nav-item">
                        <a class="nav-link" href="{{ route('pre_dv_budget', ['type' => 'accomplished']) }}">
                            <img src="\maif\public\images\paid_bill_16.png" style="margin-right:12px">
                            <span class="menu-title">Obligated DV (new)</span>
                        </a>
                        </li>
                    </ul>
                </li>
            </ul>
        @endif
        @if($joinedData->section == 105 || $id == 2760 || $id == 201400208 || $joinedData->section == 36 || $joinedData->section == 31)
            <ul class="nav flex-column" style=" margin-bottom: 0;">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard') }}">
                        <img src="\maif\public\images\dashboard_16.png" style="margin-right:12px">
                        <span class="menu-title">Dashboard</span>
                    </a>
                </li>
            </ul>
            <ul class="nav flex-column" style=" margin-bottom: 0;">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('fundsource') }}">
                        <img src="\maif\public\images\funds_16.png" style="margin-right:12px">
                        <span class="menu-title">Fundsource</span>
                        &nbsp;&nbsp;<i class="typcn typcn-arrow-sorted-down menu-icon"></i>
                    </a>
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('fundsource') }}">
                                <img src="\maif\public\images\maip_funds_16.png" style="margin-right:12px">
                                <span class="menu-title">MAIFIPP</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('proponents.fundsource') }}">
                                <img src="\maif\public\images\proponents_16.png" style="margin-right:12px">
                                <span class="menu-title">PROPONENTS</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('fundsource_budget') }}">
                                <img src="\maif\public\images\budget_funds_16.png" style="margin-right:12px">
                                <span class="menu-title">BUDGET</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin_cost') }}">
                                <img src="\maif\public\images\admin_cost_16.png" style="margin-right:12px">
                                <span class="menu-title">ADMIN COST</span>
                            </a>
                        </li>
                        <li class="nav-item">
                        <a class="nav-link" href="{{ route('file') }}">
                            <img src="\maif\public\images\icons8_upload_16.png">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <span class="menu-title">FILE UPLOAD</span>
                        </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <ul class="nav flex-column" style=" margin-bottom: 0;">
                <li class="nav-item">
                    <a class="nav-link" href="#" >
                        <i><img class="menu-icon" src="\maif\public\images\user_account_16.png"></i>
                        <span class="menu-title" style="margin-left:12px">Proponents</span>
                        <i class="typcn typcn-arrow-sorted-down menu-icon"></i>
                    </a>
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('proponents') }}">
                                <img src="\maif\public\images\proponents_16.png" style="margin-right:12px">
                                <span class="menu-title">PROPONENTS</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <ul class="nav flex-column" style=" margin-bottom: 0;">
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <img src="\maif\public\images\patients_16.png" style="margin-right:12px">
                        <span class="menu-title">Patients</span>
                        &nbsp;&nbsp;<i class="typcn typcn-arrow-sorted-down menu-icon"></i>
                    </a>
                    <ul class="nav flex-column sub-menu">
                        <!-- <li class="nav-item">
                            <a class="nav-link" href="{{ route('home') }}">
                                <img src="\maif\public\images\letter_16.png" style="margin-right:12px">
                                <span class="menu-title">Guarantee Letter (MPU)</span>
                            </a>
                        </li> -->
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('home') }}">
                                <img src="\maif\public\images\patients_16.png" style="margin-right:12px">
                                <span class="menu-title">Guarantee Letter (MPU)</span>
                                &nbsp;&nbsp;<i class="typcn typcn-arrow-sorted-down menu-icon"></i>
                            </a>
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('returned.patients') }}">
                                        <img src="\maif\public\images\letter_16.png" style="margin-right:12px">
                                        <span class="menu-title">Returned</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('expired.patients') }}">
                                        <img src="\maif\public\images\hos_letter_16.png" style="margin-right:12px">
                                        <span class="menu-title">Expired</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('patients') }}">
                                <img src="\maif\public\images\hos_letter_16.png" style="margin-right:12px">
                                <span class="menu-title">Guarantee Letter (Proponent)</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('group') }}">
                                <img src="\maif\public\images\gr_pat_16.png" style="margin-right:12px">
                                <span class="menu-title">Group Patients</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <ul class="nav flex-column" style=" margin-bottom: 0;">
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <img src="\maif\public\images\dv_16.png" style="margin-right:12px">
                        <span class="menu-title">Disbursement Voucher</span>
                        &nbsp;&nbsp;<i class="typcn typcn-arrow-sorted-down menu-icon"></i>
                    </a>
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dv') }}">
                                <img src="\maif\public\images\dv1_16.png" style="margin-right:12px">
                                <span class="menu-title">Disbursement V1</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dv2') }}">
                                <img src="\maif\public\images\dv2_16.png" style="margin-right:12px">
                                <span class="menu-title">Disbursement V2</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dv3') }}">
                                <img src="\maif\public\images\dv3_16.png" style="margin-right:12px">
                                <span class="menu-title">Disbursement V3</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <ul class="nav flex-column" style=" margin-bottom: 0;">
                <li class="nav-item">
                    <a class="nav-link" href="#" >
                        <img src="\maif\public\images\new_16.png" style="margin-right:12px">
                        <span class="menu-title">Disbursement Voucher(new)</span>
                        <i class="typcn typcn-arrow-sorted-down menu-icon"></i>
                    </a>
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('pre_dv') }}">
                                <img src="\maif\public\images\new_1_16.png" style="margin-right:12px">
                                <span class="menu-title">PRE-DV</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('pre_dv1') }}">
                                <img src="\maif\public\images\new_2_16.png" style="margin-right:12px">
                                <span class="menu-title">V1</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('pre_dv2') }}">
                                <img src="\maif\public\images\new3_16.png" style="margin-right:12px">
                                <span class="menu-title">V2</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <ul class="nav flex-column" style=" margin-bottom: 0;">
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <img src="\maif\public\images\transaction_16.png" style="margin-right:12px">
                        <span class="menu-title">Transmittal</span>
                            &nbsp;&nbsp;<i class="typcn typcn-arrow-sorted-down menu-icon"></i>
                    </a>
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('incoming') }}">
                                <img src="\maif\public\images\incoming_16.png" style="margin-right:12px">
                                <span class="menu-title">Incomming Send Bills</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('returned') }}">
                                <img src="\maif\public\images\returned_16.png" style="margin-right:12px">
                                <span class="menu-title">Returned Send Bills</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('accepted') }}">
                                <img src="\maif\public\images\accepted_16.png" style="margin-right:12px">
                                <span class="menu-title">Accepted Send Bills</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            @if( $id == 2760 || $id == 2680)
                <ul class="nav flex-column" style=" margin-bottom: 0;">
                    <li class="nav-item">
                        <a class="nav-link" href="#" >
                            <i><img class="menu-icon" src="\maif\public\images\user_account_16.png"></i>
                            <span class="menu-title" style="margin-left:20px">Accounts</span>
                            <i class="typcn typcn-arrow-sorted-down menu-icon"></i>
                        </a>
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('users') }}">
                                    <i><img class="menu-icon" src="\maif\public\images\user_accounts_16.png"></i>
                                    <span class="menu-title" style="margin-left:15px">USERS</span>
                                </a>
                            </li>
                        </ul>
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('on-hold') }}">
                                    <img src="\maif\public\images\proponents_16.png" style="margin-right:12px">
                                    <span class="menu-title">HOLD GL</span>
                                </a>
                            </li>
                        </ul>
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('included.facility') }}">
                                    <img src="\maif\public\images\proponents_16.png" style="margin-right:12px">
                                    <span class="menu-title">Facilities</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            @endif
            <ul class="nav flex-column" style=" margin-bottom: 0;">
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <img src="\maif\public\images\report_16.png" style="margin-right:12px">
                        <span class="menu-title">Report</span>
                        &nbsp;&nbsp;<i class="typcn typcn-arrow-sorted-down menu-icon"></i>
                    </a>
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                                <a class="nav-link" href="{{ route('report') }}">
                                    <img src="\maif\public\images\pre_re_16.png" style="margin-right:12px">
                                    <span class="menu-title">Proponent</span>
                                </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('report.facility') }}">
                                <img src="\maif\public\images\fc_re_16.png" style="margin-right:12px">
                                <span class="menu-title">Facility</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('report.saa') }}">
                                <img src="\maif\public\images\saa_re_16.png" style="margin-right:12px">
                                <span class="menu-title">SAA</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('logbook') }}">
                    <img src="\maif\public\images\log_16.png" style="margin-right:12px">
                    <span class="menu-title">Logbook</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('facility') }}">
                    <img src="\maif\public\images\facility_16.png" style="margin-right:12px">
                    <span class="menu-title">Facility</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('tasks') }}">
                    <img src="\maif\public\images\note_16.png" style="margin-right:12px">
                    <span class="menu-title">Notes</span>
                </a>
            </li>
        @endif
        @if(Auth::user()->userid == 1027 || Auth::user()->userid == 2660)
            <ul class="nav flex-column" style=" margin-bottom: 0;">
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <img src="\maif\public\images\dv_16.png" style="margin-right:12px">
                        <span class="menu-title">Disbursement Voucher</span>
                        &nbsp;&nbsp;<i class="typcn typcn-arrow-sorted-down menu-icon"></i>
                    </a>
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dv') }}">
                                <img src="\maif\public\images\dv_16.png" style="margin-right:12px">
                                <span class="menu-title">Disbursement V1</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dv2') }}">
                                <img src="\maif\public\images\dv2_16.png" style="margin-right:12px">
                                <span class="menu-title">Disbursement V2</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dv3') }}">
                                <img src="\maif\public\images\dv3_16.png" style="margin-right:12px">
                                <span class="menu-title">Disbursement V3</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        @endif
        @if($joinedData->section == 7)
            <ul class="nav flex-column" style="margin-bottom: 0;">
                <li class="nav-item">
                        <a class="nav-link" href="#">
                            <img src="\maif\public\images\dv_16.png" style="margin-right:12px">
                            <span class="menu-title">Disbursement Voucher (1)</span>
                                &nbsp;&nbsp;<i class="typcn typcn-arrow-sorted-down menu-icon"></i>
                        </a>
                    <ul class="nav flex-column sub-menu dv-menu-1">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('cashier', ['type' => 'pending']) }}">
                            <img src="\maif\public\images\pending_16.png" style="margin-right:12px">
                                <span class="menu-title">Pending DV</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('cashier', ['type' => 'paid']) }}">
                                <img src="\maif\public\images\paid_bill_16.png" style="margin-right:12px">
                                <span class="menu-title">Paid DV</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('dv2') }}">
                    <img src="\maif\public\images\dv2_16.png" style="margin-right:12px">
                    <span class="menu-title">Disbursement Voucher (2)</span>
                </a>
            </li>
            <ul class="nav flex-column" style=" margin-bottom: 0;">
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <img src="\maif\public\images\dv3_16.png" style="margin-right:12px">
                        <span class="menu-title">Disbursement Vouchers (3)</span>
                        &nbsp;&nbsp;<i class="typcn typcn-arrow-sorted-down menu-icon"></i>
                    </a>
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('budget.dv3', ['type' => 'dv3_owed']) }}">
                                <img src="\maif\public\images\pending_16.png" style="margin-right:12px">
                                <span class="menu-title">Pending DV3</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('budget.dv3', ['type' => 'done']) }}">
                                <img src="\maif\public\images\paid_bill_16.png" style="margin-right:12px">
                                <span class="menu-title">Paid DV3</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <ul class="nav flex-column" style="margin-bottom: 0;">
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <img src="\maif\public\images\new_16.png" style="margin-right:12px">
                        <span class="menu-title">Disbursement Vouchers (new)</span>
                        &nbsp;&nbsp;<i class="typcn typcn-arrow-sorted-down menu-icon"></i>
                    </a>
                    <ul class="nav flex-column sub-menu dv-menu-2">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('pre_dv_budget', ['type' => 'deferred']) }}">
                                <img src="\maif\public\images\dv3_16.png" style="margin-right:12px">
                                <span class="menu-title">Pending DVs (new)</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('pre_dv_budget', ['type' => 'disbursed']) }}">
                                <img src="\maif\public\images\paid_bill_16.png" style="margin-right:12px">
                                <span class="menu-title">Paid DV (new)</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        @endif
        <br>
        <li class="nav-item">
            <label for="sidebar-note" style="display: flex; align-items: center;">
                Note(s):
                <a href="#" data-toggle="modal" data-target="#new_note">
                    <i class="typcn typcn-plus menu-icon"></i>                
                </a>
            </label>
            <label style="color:gray">#Legend:
                <br>
                <small style="margin-left:20px">
                    <i style="color:green" class="typcn typcn-media-record menu-icon"></i> DONE
                    <i style="color:blue" class="typcn typcn-media-record-outline menu-icon"></i>  IN-PROGRESS
                </small>
            </label>
            @foreach($notes as $note)
                @if($note->status == 0)
                    <div class="sidebar-note">
                        <textarea name="note" class="form-control sidebar-note" rows="10" style="esize: vertical; background-color:#fff3cd" readonly>{{$note->notes}}</textarea>
                        <small>{{' ('. $note->user->lname .', '.$note->user->fname.')'}}</small>
                        @if($note->status == 0)
                            <a href="{{ route('process.note', ['id' => $note->id]) }}">
                                <i style="color:blue; float:right" class="typcn typcn-media-record-outline menu-icon"></i> 
                            </a>
                        @elseif($note->status == 1)
                            <i style="color:green; float:right" class="typcn typcn-media-record menu-icon"></i>
                        @endif
                    </div>
                @endif
            @endforeach
        </li>
    </ul>
</nav>
<div class="modal fade" id="new_note" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form action="{{ route('save.note') }}" method="POST" style="background-color: #fff3cd;">
                @csrf
                <div class="" style="padding:10px">
                    <h4 class="text-success">
                        <i style="font-size:30px" class="typcn typcn-document-text menu-icon"></i> New Note
                    </h4>
                    <hr />
                    <div class="form-group">
                        <textarea name="note" class="form-control" rows="15" style="resize: vertical;" placeholder="..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button style="background-color:lightgray" class="btn btn-default" data-dismiss="modal">
                        <i class="typcn typcn-times menu-icon"></i> Close
                    </button>
                    <button type="submit" class="btn btn-success btn-submit">
                        <i class="typcn typcn-location-arrow menu-icon"></i> Create
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>