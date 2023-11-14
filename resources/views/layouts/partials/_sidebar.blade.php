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
    @if(Auth::user()->roles == 'maif')
    <li class="nav-item">
      <a class="nav-link" href="{{ route('home') }}">
        <i class="typcn typcn-user-add-outline menu-icon"></i>
        <span class="menu-title">Guarantee Letter</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="#">
        <i class="typcn typcn-document-text menu-icon"></i>
        <span class="menu-title">Disbursement Letter</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ route('facility') }}">
        <i class="typcn typcn-flow-switch menu-icon"></i>
        <span class="menu-title">Facility</span>
      </a>
    </li>
    @endif
    @if(Auth::user()->roles == 'budget')
    <li class="nav-item">
      <a class="nav-link" href="{{ route('fundsource') }}">
        <i class="typcn typcn-film menu-icon"></i>
        <span class="menu-title">Fundsource</span>
      </a>
    </li>
    @endif
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