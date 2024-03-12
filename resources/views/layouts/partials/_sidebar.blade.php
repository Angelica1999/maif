<style>
    .nav-item .sub-menu {
        display: none;
    }

    .nav-item:hover .sub-menu {
        display: block;
    }
</style>
<?php $joinedData = DB::connection('dohdtr')
                    ->table('users')
                    ->leftJoin('dts.users', 'users.userid', '=', 'dts.users.username')
                    ->where('users.userid', '=', Auth::user()->userid)
                    ->select('users.section', 'users.division')
                    ->first();?>
<nav class="sidebar sidebar-offcanvas" id="sidebar">
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
        <li class="nav-item">
          <a class="nav-link" href="{{ route('fundsource_budget') }}">
            <i class="typcn typcn-film menu-icon"></i>
            <span class="menu-title">Fundsource</span>
          </a>
        </li>
        <ul class="nav flex-column" style=" margin-bottom: 0;">
            <li class="nav-item">
                <a class="nav-link" href="#">
                <i class="typcn typcn-group menu-icon"></i>
                    <span class="menu-title">Disbursement Voucher</span>
                    &nbsp;&nbsp;<i class="typcn typcn-arrow-sorted-down menu-icon"></i>
                </a>
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                      <a class="nav-link" href="{{ route('fundsource_budget.pendingDv', ['type' => 'pending']) }}">
                        <i class="typcn typcn-film menu-icon"></i>
                        <span class="menu-title">Pending DV</span>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="{{ route('fundsource_budget.pendingDv', ['type' => 'obligated']) }}">
                        <i class="typcn typcn-film menu-icon"></i>
                        <span class="menu-title">Obligated DV</span>
                      </a>
                    </li>
                </ul>
            </li>
        </ul>
    @endif
    @if($joinedData->section == 105 || $joinedData->section == 80)

        <ul class="nav flex-column" style=" margin-bottom: 0;">
            <li class="nav-item">
                <a class="nav-link" href="#">
                <i class="typcn typcn-th-list menu-icon"></i>
                    <span class="menu-title">Fundsource</span>
                    &nbsp;&nbsp;<i class="typcn typcn-arrow-sorted-down menu-icon"></i>
                </a>
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                      <a class="nav-link" href="{{ route('fundsource') }}">
                        <i class="typcn typcn-film menu-icon"></i>
                        <span class="menu-title">MAIFIPP</span>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="{{ route('fundsource_budget') }}">
                        <i class="typcn typcn-document menu-icon"></i>
                        <span class="menu-title">BUDGET</span>
                      </a>
                    </li>
                </ul>
            </li>
        </ul>

        <ul class="nav flex-column" style=" margin-bottom: 0;">
            <li class="nav-item">
                <a class="nav-link" href="#">
                <i class="typcn typcn-group menu-icon"></i>
                    <span class="menu-title">Patients</span>
                    &nbsp;&nbsp;<i class="typcn typcn-arrow-sorted-down menu-icon"></i>
                </a>
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                      <a class="nav-link" href="{{ route('home') }}">
                        <i class="typcn typcn-user-add-outline menu-icon"></i>
                        <span class="menu-title">Guarantee Letter</span>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="{{ route('group') }}">
                        <i class="typcn typcn-group-outline menu-icon"></i>
                        <span class="menu-title">Group Patients</span>
                      </a>
                    </li>
                </ul>
            </li>
        </ul>

        <ul class="nav flex-column" style=" margin-bottom: 0;">
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="typcn typcn-document-text menu-icon"></i>
                    <span class="menu-title">Disbursement Voucher</span>
                    &nbsp;&nbsp;<i class="typcn typcn-arrow-sorted-down menu-icon"></i>
                </a>
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dv') }}">
                            <i class="typcn typcn-document-text menu-icon"></i>
                            <span class="menu-title">Disbursement V1</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dv2') }}">
                            <i class="typcn typcn-document menu-icon"></i>
                            <span class="menu-title">Disbursement V2</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
        <ul class="nav flex-column" style=" margin-bottom: 0;">
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="typcn typcn-folder-open menu-icon"></i>
                    <span class="menu-title">Report</span>
                    &nbsp;&nbsp;<i class="typcn typcn-arrow-sorted-down menu-icon"></i>
                </a>
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item">
                    <a class="nav-link" href="{{ route('report') }}">
                      <i class="typcn typcn-th-list-outline menu-icon"></i>
                      <span class="menu-title">Proponent</span>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="{{ route('report.facility') }}">
                      <i class="typcn typcn-th-list-outline menu-icon"></i>
                      <span class="menu-title">Facility</span>
                    </a>
                  </li>
                </ul>
            </li>
        </ul>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('facility') }}">
            <i class="typcn typcn-flow-switch menu-icon"></i>
            <span class="menu-title">Facility</span>
          </a>
        </li>
    @endif

    @if(Auth::user()->userid == 1027)
        <ul class="nav flex-column" style=" margin-bottom: 0;">
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="typcn typcn-document-text menu-icon"></i>
                    <span class="menu-title">Disbursement Voucher</span>
                    &nbsp;&nbsp;<i class="typcn typcn-arrow-sorted-down menu-icon"></i>
                </a>
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dv') }}">
                            <i class="typcn typcn-document-text menu-icon"></i>
                            <span class="menu-title">Disbursement V1</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dv2') }}">
                            <i class="typcn typcn-document menu-icon"></i>
                            <span class="menu-title">Disbursement V2</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    @endif
    @if($joinedData->section == 7)
      <ul class="nav flex-column" style=" margin-bottom: 0;">
            <li class="nav-item">
                <a class="nav-link" href="#">
                <i class="typcn typcn-group menu-icon"></i>
                    <span class="menu-title">Disbursement Voucher</span>
                    &nbsp;&nbsp;<i class="typcn typcn-arrow-sorted-down menu-icon"></i>
                </a>
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                      <a class="nav-link" href="{{ route('cashier', ['type' => 'pending']) }}">
                        <i class="typcn typcn-film menu-icon"></i>
                        <span class="menu-title">Pending DV</span>
                      </a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="{{ route('cashier', ['type' => 'paid']) }}">
                        <i class="typcn typcn-film menu-icon"></i>
                        <span class="menu-title">Paid DV</span>
                      </a>
                    </li>
                </ul>
            </li>
        </ul>
    @endif
    <!-- @if(Auth::user()->userid == 1027)
      <li class="nav-item">
        <a class="nav-link" href="{{ route('dv')}}">
          <i class="typcn typcn-document-text menu-icon"></i>
          <span class="menu-title">Disbursement Voucher</span>
        </a>
      </li>
    @endif -->

  </ul>
  <ul class="sidebar-legend">
    <li>
      <p class="sidebar-menu-title">Category</p>
    </li>
    <li class="nav-item"><a href="#" class="nav-link">#Patients</a></li>
    <li class="nav-item"><a href="#" class="nav-link">#Fundsource</a></li>
    <li class="nav-item"><a href="#" class="nav-link">#MAIFF</a></li>
  </ul>
</nav>