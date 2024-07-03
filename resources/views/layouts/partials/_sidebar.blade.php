<style>
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
        text-align: justify;
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
    $id = Auth::user()->userid;
    $joinedData = DB::connection('dohdtr')
                    ->table('users')
                    ->leftJoin('dts.users', 'users.userid', '=', 'dts.users.username')
                    ->where('users.userid', '=', $id)
                    ->select('users.section', 'users.division')
                    ->first();
    $notes = Notes::where('created_by', $id)->with('user')->get();
?>
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
    <ul class="nav flex-column" style=" margin-bottom: 0;">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('fundsource_budget') }}">
                <i class="typcn typcn-th-list menu-icon"></i>
                    <span class="menu-title">Fundsource</span>
                    &nbsp;&nbsp;<i class="typcn typcn-arrow-sorted-down menu-icon"></i>
                </a>
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                      <a class="nav-link" href="{{ route('fundsource_budget') }}">
                        <i class="typcn typcn-film menu-icon"></i>
                        <span class="menu-title">BUDGET</span>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="{{ route('fundsource') }}">
                        <i class="typcn typcn-film menu-icon"></i>
                        <span class="menu-title">MAIFIPP</span>
                      </a>
                    </li>
                </ul>
            </li>
        </ul>

        <ul class="nav flex-column" style=" margin-bottom: 0;">
            <li class="nav-item">
                <a class="nav-link" href="#">
                <i class="typcn typcn-group menu-icon"></i>
                    <span class="menu-title">Disbursement Voucher (1)</span>
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
        <ul class="nav flex-column" style=" margin-bottom: 0;">
            <li class="nav-item">
                <a class="nav-link" href="#">
                <i class="typcn typcn-group-outline menu-icon"></i>
                    <span class="menu-title">Disbursement Voucher (3)</span>
                    &nbsp;&nbsp;<i class="typcn typcn-arrow-sorted-down menu-icon"></i>
                </a>
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                      <a class="nav-link" href="{{ route('budget.dv3', ['type' => 'unsettled']) }}">
                        <i class="typcn typcn-document menu-icon"></i>
                        <span class="menu-title">Pending DV3</span>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="{{ route('budget.dv3', ['type' => 'processed']) }}">
                        <i class="typcn typcn-th-list menu-icon"></i>
                        <span class="menu-title">Obligated DV3</span>
                      </a>
                    </li>
                </ul>
            </li>
        </ul>

    @endif
    @if($joinedData->section == 105 || $id == 2760 || $id == 201400208 || $joinedData->section == 36 || $joinedData->section == 31)

        <ul class="nav flex-column" style=" margin-bottom: 0;">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('fundsource') }}">
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
                    <li class="nav-item">
                      <a class="nav-link" href="{{ route('admin_cost') }}">
                        <i class="typcn typcn-document-delete menu-icon"></i>
                        <span class="menu-title">ADMIN COST</span>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="{{ route('file') }}">
                        <img src="\maif\public\images\icons8_upload_16.png">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <span class="menu-title">FILE UPLOAD</span>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="{{ route('proponents') }}">
                        <i class="typcn typcn-user-add-outline menu-icon"></i>
                        <span class="menu-title">PROPONENTS</span>
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
                        <i class="typcn typcn-document-text menu-icon"></i>
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
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dv3') }}">
                            <i class="typcn typcn-document-add menu-icon"></i>
                            <span class="menu-title">Disbursement V3</span>
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
                  <li class="nav-item">
                    <a class="nav-link" href="{{ route('report.saa') }}">
                      <i class="typcn typcn-th-list-outline menu-icon"></i>
                      <span class="menu-title">SAA</span>
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
        <li class="nav-item">
          <a class="nav-link" href="{{ route('tasks') }}">
            <i class="typcn typcn-pin menu-icon"></i>
            <span class="menu-title">Notes</span>
          </a>
        </li>
    @endif

    @if(Auth::user()->userid == 1027 || Auth::user()->userid == 2660)
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
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dv3') }}">
                            <i class="typcn typcn-document menu-icon"></i>
                            <span class="menu-title">Disbursement V3</span>
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
                  <span class="menu-title">Disbursement Voucher (1)</span>
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
      <li class="nav-item">
          <a class="nav-link" href="{{ route('dv2') }}">
              <i class="typcn typcn-document menu-icon"></i>
              <span class="menu-title">Disbursement Voucher (2)</span>
          </a>
      </li>
      <ul class="nav flex-column" style=" margin-bottom: 0;">
          <li class="nav-item">
              <a class="nav-link" href="#">
              <i class="typcn typcn-group menu-icon"></i>
                  <span class="menu-title">Disbursement Voucher (3)</span>
                  &nbsp;&nbsp;<i class="typcn typcn-arrow-sorted-down menu-icon"></i>
              </a>
              <ul class="nav flex-column sub-menu">
                  <li class="nav-item">
                      <a class="nav-link" href="{{ route('budget.dv3', ['type' => 'unpaid']) }}">
                      <i class="typcn typcn-film menu-icon"></i>
                        <span class="menu-title">Pending DV3</span>
                      </a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" href="{{ route('budget.dv3', ['type' => 'done']) }}">
                      <i class="typcn typcn-film menu-icon"></i>
                        <span class="menu-title">Paid DV3</span>
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
                    <i class="typcn typcn-plus menu-icon"></i>                </a>
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
                    @foreach(explode('-', $note->notes) as $noteItem)
                        @if(!empty(trim($noteItem)))
                            <p>- {{ trim($noteItem) }}</p>
                        @endif
                    @endforeach
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