<style>
    
    /* Profile section */
    .maif-sidebar .sidebar-profile {
        /* padding: 24px 20px;
        background: rgba(255,255,255,0.05); */
        border-bottom: 1px solid rgba(255,255,255,0.1);
        display: flex;
        align-items: center;
    }
    
    .maif-sidebar .sidebar-profile-image {
        position: relative;
        margin-right: 15px;
    }
    
    .maif-sidebar .sidebar-status-indicator {
        position: absolute;
        bottom: 5px;
        right: 5px;
        width: 10px;
        height: 10px;
        background: var(--success-color);
        border: 2px solid var(--primary-color);
        border-radius: 50%;
    }
    
    .maif-sidebar .sidebar-profile-name {
        flex: 1;
    }
    
    .maif-sidebar .sidebar-name {
        font-size: 16px;
        font-weight: 600;
        margin: 0 0 4px 0;
        color: white;
    }
    
    .maif-sidebar .sidebar-designation {
        font-size: 13px;
        color: rgba(255,255,255,0.7);
        margin: 0;
        font-weight: 400;
    }
    
    /* Menu sections */
    .maif-sidebar .sidebar-menu-title {
        color: rgba(255,255,255,0.5);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 600;
        padding: 20px 20px 10px;
        margin: 0;
    }
    
    .maif-sidebar .nav {
        list-style: none;
        padding: 0 2px;
        margin: 0;
    }
    
    .maif-sidebar .nav-item {
        /* position: relative; */
        margin-bottom: 2px;
        /* border-radius: 6px; */
        overflow: hidden;
    }
    
    /* Navigation links */
    .maif-sidebar .nav-link {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        color: rgba(255,255,255,0.85);
        text-decoration: none;
        transition: all 0.2s ease;
        cursor: pointer;
        user-select: none;
        position: relative;
        background: transparent;
    }
    
    .maif-sidebar .nav-link:hover {
        background: rgba(255,255,255,0.08);
        color: white;
        padding-left: 18px;
    }
    
    /* .maif-sidebar .nav-link.active {
        background: rgba(52, 152, 219, 0.2);
        color: white;
        border-left: 3px solid #3498db;
    } */
    
    .maif-sidebar .menu-icon {
        width: 20px;
        height: 20px;
        margin-right: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        filter: brightness(0.9);
    }
    
    .maif-sidebar .menu-icon img {
        width: 16px;
        height: 16px;
        object-fit: contain;
    }
    
    .maif-sidebar .menu-title {
        flex: 1;
        font-size: 14px;
        font-weight: 500;
    }
    
    .maif-sidebar .dropdown-icon {
        margin-left: auto;
        transition: transform 0.3s ease;
        font-size: 10px;
        color: rgba(255,255,255,0.5);
        width: 16px;
        height: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .maif-sidebar .dropdown-icon.rotated {
        transform: rotate(180deg);
    }
    
    /* .maif-sidebar .sub-menu {
        max-height: 0;
        overflow: hidden;
        background: transparent; 
        transition: max-height 0.3s ease, background 0.2s ease;
    } */

    /* .maif-sidebar .nav-item:hover > .sub-menu,
    .maif-sidebar .nav-item:focus-within > .sub-menu {
        max-height: 1000px;
        background: rgba(0,0,0,0.2);
        transition-delay: 0s;
    } */

    .maif-sidebar .nav-item > .sub-menu {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
    }

    .maif-sidebar .nav-item:focus-within > .sub-menu {
        max-height: 1000px;
        background: rgba(0,0,0,0.2);
    }

    .maif-sidebar .sub-menu {
        transition-delay: 1s; 
    }

    .maif-sidebar .sub-menu .nav-link {
        padding: 10px 15px 10px 35px;
        font-size: 13.5px;
        border-left: 2px solid transparent;
    }
    
    .maif-sidebar .sub-menu .nav-link:hover {
        border-left: 2px solid rgba(255,255,255,0.3);
        padding-left: 38px;
    }
    
    .maif-sidebar .sub-menu .sub-menu .nav-link {
        padding-left: 50px;
        font-size: 13px;
    }
    
    /* Badges */
    .maif-sidebar .badge {
        font-size: 0.75rem;
        padding: 0.2rem 0.5rem;
        border-radius: 10px;
        font-weight: 600;
        min-width: 20px;
        text-align: center;
    }
    
    .maif-sidebar .gl_lists {
        background-color: #3498db;
        color: white;
    }
    
    .maif-sidebar .returned_lists {
        background-color: #e74c3c;
        color: white;
    }
    
    .maif-sidebar .expired_lists {
        background-color: #f39c12;
        color: #2c3e50;
    }
    
    /* Notes section */
    .maif-sidebar .notes-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 20px 10px;
        margin-top: 10px;
        border-top: 1px solid rgba(255,255,255,0.1);
    }
    
    .maif-sidebar .notes-header-title {
        font-size: 14px;
        font-weight: 600;
        color: #3498db;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .maif-sidebar .add-note-btn {
        width: 30px;
        height: 30px;
        border-radius: 10%;
        background: #3498db;
        color: white;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        font-weight: bold;
        transition: all 0.2s ease;
    }
    
    .add-note-btn:hover {
        background: #2980b9;
        transform: scale(1.1);
        box-shadow: 0 3px 10px rgba(52, 152, 219, 0.3);
    }
    
    .maif-sidebar .notes-legend {
        padding: 8px 20px;
        font-size: 11px;
        color: rgba(255,255,255,0.6);
    }
    
    .maif-sidebar .legend-item {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-right: 15px;
    }
    
    /* Note items */
    .maif-sidebar .sidebar-note {
        margin: 10px 15px;
        padding: 14px;
        background: rgba(255,255,255,0.95);
        border: 1px solid rgba(0,0,0,0.1);
        border-left: 4px solid var(--warning-color);
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        transition: all 0.2s ease;
    }
    
    .maif-sidebar .sidebar-note:hover {
        transform: translateX(2px);
        box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    }
    
    .maif-sidebar .sidebar-note textarea {
        width: 100%;
        border: none;
        background: transparent;
        font-size: 12px;
        color: #78350f;
        resize: vertical;
        font-family: 'Inter', sans-serif;
        line-height: 1.5;
    }
    
    .maif-sidebar .sidebar-note textarea:focus {
        outline: none;
    }

    .maif-sidebar .sidebar-note small {
        display: block;
        margin-top: 8px;
        font-size: 10px;
        color: #92400e;
        font-weight: 500;
    }
    .maif-sidebar .note-status {
        float: right;
        margin-top: 4px;
    }
    .maif-sidebar .status-icon {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 12px;
        font-weight: 500;
    }
    .maif-sidebar .status-progress {
        background: #dbeafe;
        color: #1e40af;
    }
    .maif-sidebar .status-done {
        background: #d1fae5;
        color: #065f46;
    }

    .maif-sidebar .status-icon:hover {
        transform: translateY(-1px);
    }

    /* Modal */
    .maif-sidebar .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 10000;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(3px);
    }
    
    .maif-sidebar .modal.show {
        display: flex;
        animation: fadeIn 0.3s ease;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    .maif-sidebar .modal-content {
        background: white;
        border-radius: 12px;
        width: 90%;
        max-width: 420px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        animation: modalSlideIn 0.3s ease;
        overflow: hidden;
    }
    
    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* .maif-sidebar .modal-header {
        padding: 10px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 1px solid var(--border-color);
    }
     */
    /* .maif-sidebar .modal-title {
        display: flex;
        align-items: center;
        gap: 12px;
        color: var(--text-dark);
        font-size: 18px;
        font-weight: 600;
        margin: 0;
    } */
    
    .maif-sidebar .modal-body {
        padding: 20px;
    }
    
    .maif-sidebar .form-group textarea {
        width: 100%;
        height: 150px;
        padding: 15px;
        border: 2px solid var(--border-color);
        border-radius: 8px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 14px;
        resize: vertical;
        transition: border-color 0.2s;
    }
    
    .maif-sidebar .form-group textarea:focus {
        outline: none;
        border-color: var(--accent-color);
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }

    /* Scrollbar styling */
    .maif-sidebar .sidebar::-webkit-scrollbar {
        width: 6px;
    }
    
    .maif-sidebar .sidebar::-webkit-scrollbar-track {
        background: rgba(255,255,255,0.05);
    }
    
    .maif-sidebar .sidebar::-webkit-scrollbar-thumb {
        background: rgba(255,255,255,0.2);
        border-radius: 3px;
    }
    
    .maif-sidebar .sidebar::-webkit-scrollbar-thumb:hover {
        background: rgba(255,255,255,0.3);
    }

    /* Divider */
    .maif-sidebar .nav-divider {
        height: 1px;
        background: rgba(255,255,255,0.1);
        margin: 10px 20px;
    }
</style>
<nav class="maif-sidebar sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <!-- Profile Section -->
        <li class="nav-item">
            <div class="sidebar-profile">
                <div class="sidebar-profile-image">
                    <img src="{{ asset('images/doh-logo.png') }}" alt="DOH Logo">
                    <span class="sidebar-status-indicator "></span>
                </div>
                <div class="sidebar-profile-name">
                    <p class="sidebar-name">
                        {{ Auth::user()->fname .' '. Auth::user()->lname }}
                    </p>
                    <p class="sidebar-designation">
                        Welcome
                    </p>
                </div>
            </div>
            <p class="sidebar-menu-title">Navigation Menu</p>
        </li>
        @if($joinedData->section == 6)
            <ul class="nav flex-column" style="margin-bottom: 0;">
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)">
                        <span class="menu-icon">
                            <img src="\maif\public\images\funds_16.png">
                        </span>
                        <span class="menu-title">Fundsource</span>
                        <span class="dropdown-icon">▼</span>
                    </a>
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('fundsource_budget') }}">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\budget_funds_16.png">
                                </span>
                                <span class="menu-title">BUDGET</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('fundsource') }}">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\maip_funds_16.png">
                                </span>
                                <span class="menu-title">MAIFIPP</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('fundsource_budget.summary') }}">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\maip_funds_16.png">
                                </span>
                                <span class="menu-title">Budget Summary</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- DV (1) Section -->
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)">
                        <span class="menu-icon">
                            <img src="\maif\public\images\dv_16.png">
                        </span>
                        <span class="menu-title">Disbursement Voucher (1)</span>
                        <span class="dropdown-icon">▼</span>
                    </a>
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('fundsource_budget.pendingDv', ['type' => 'pending']) }}">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\pending_16.png">
                                </span>
                                <span class="menu-title">Pending DV</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('fundsource_budget.pendingDv', ['type' => 'obligated']) }}">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\paid_bill_16.png">
                                </span>
                                <span class="menu-title">Obligated DV</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- DV (3) Section -->
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)">
                        <span class="menu-icon">
                            <img src="\maif\public\images\dv3_16.png">
                        </span>
                        <span class="menu-title">Disbursement Voucher (3)</span>
                        <span class="dropdown-icon">▼</span>
                    </a>
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('budget.dv3', ['type' => 'unsettled']) }}">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\pending_16.png">
                                </span>
                                <span class="menu-title">Pending DV3</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('budget.dv3', ['type' => 'processed']) }}">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\paid_bill_16.png">
                                </span>
                                <span class="menu-title">Obligated DV3</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- DV (new) Section -->
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)">
                        <span class="menu-icon">
                            <img src="\maif\public\images\new_16.png">
                        </span>
                        <span class="menu-title">Disbursement Vouchers (new)</span>
                        <span class="dropdown-icon">▼</span>
                    </a>
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('pre_dv_budget', ['type' => 'awaiting']) }}">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\pending_16.png">
                                </span>
                                <span class="menu-title">Pending DV (new)</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('pre_dv_budget', ['type' => 'accomplished']) }}">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\paid_bill_16.png">
                                </span>
                                <span class="menu-title">Obligated DV (new)</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        @endif
        @if($joinedData->section == 105 || $id == 200200097 || $id == 2760 || $id == 201400208 || $id == 1721 || $id == "0881" || $joinedData->section == 36 || $joinedData->section == 31)
            <!-- Dashboard -->
            <ul class="nav flex-column" style="margin-bottom: 0;">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard') }}">
                        <span class="menu-icon">
                            <img src="\maif\public\images\dashboard_16.png">
                        </span>
                        <span class="menu-title">Dashboard</span>
                    </a>
                </li>
            </ul>
            <!-- Fundsource -->
            <ul class="nav flex-column" style="margin-bottom: 0;">
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)">
                        <span class="menu-icon">
                            <img src="\maif\public\images\funds_16.png">
                        </span>
                        <span class="menu-title">Fundsource</span>
                        <span class="dropdown-icon">▼</span>
                    </a>
                    <ul class="nav flex-column sub-menu">
                        <!-- Sub-items -->
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('fundsource') }}">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\maip_funds_16.png">
                                </span>
                                <span class="menu-title">MAIFIPP</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('proponents.fundsource') }}">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\proponents_16.png">
                                </span>
                                <span class="menu-title">Proponents</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('fundsource_budget') }}">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\budget_funds_16.png">
                                </span>
                                <span class="menu-title">Budget</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin_cost') }}">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\admin_cost_16.png">
                                </span>
                                <span class="menu-title">Admin Cost</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('file') }}">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\icons8_upload_16.png">
                                </span>
                                <span class="menu-title">File Upload</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <!-- Proponents -->
            <ul class="nav flex-column" style=" margin-bottom: 0;">
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" >
                        <span class="menu-icon">
                            <img src="\maif\public\images\user_account_16.png">
                        </span>
                        <span class="menu-title">Proponents</span>
                        <span class="dropdown-icon">▼</span>
                    </a>
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('proponents') }}">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\proponents_16.png">
                                </span>
                                <span class="menu-title">Proponents</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <!-- Patients Section -->
            <ul class="nav flex-column" style="margin-bottom: 0;">
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)">
                        <span class="menu-icon">
                            <img src="\maif\public\images\patients_16.png">
                        </span>
                        <span class="menu-title">Patients</span>
                        <span class="dropdown-icon">▼</span>
                    </a>
                    <ul class="nav flex-column sub-menu">
                        <!-- MPU Submenu -->
                        <li class="nav-item">
                            <a class="nav-link" href="javascript:void(0)">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\patients_16.png">
                                </span>
                                <span class="menu-title">MPU</span>
                                <span class="dropdown-icon">▶</span>
                            </a>
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('home') }}" style="display: flex; align-items: center; justify-content: space-between;">
                                        <span style="display: flex; align-items: center;">
                                            <span class="menu-icon"><img src="\maif\public\images\letter_16.png"></span>
                                            <span class="menu-title">Lists</span>
                                        </span>
                                        <span class="badge gl_lists"></span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('returned.patients') }}" style="display: flex; align-items: center; justify-content: space-between;">
                                        <span style="display: flex; align-items: center;">
                                            <span class="menu-icon"><img src="\maif\public\images\letter_16.png"></span>
                                            <span class="menu-title">Returned</span>
                                        </span>
                                        <span class="badge returned_lists"></span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('expired.patients') }}" style="display: flex; align-items: center; justify-content: space-between;">
                                        <span style="display: flex; align-items: center;">
                                            <span class="menu-icon"><img src="\maif\public\images\hos_letter_16.png"></span>
                                            <span class="menu-title">Expired</span>
                                        </span>
                                        <span class="badge expired_lists"></span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('patients') }}">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\hos_letter_16.png">
                                </span>    
                                <span class="menu-title">Proponent GL</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('group') }}">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\gr_pat_16.png">
                                </span>    
                                <span class="menu-title">Group Patients</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <!-- Disbursement Voucher -->
            <ul class="nav flex-column" style=" margin-bottom: 0;">
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)">
                        <span class="menu-icon">
                            <img src="\maif\public\images\dv_16.png">
                        </span>    
                        <span class="menu-title">Disbursement Voucher</span>
                        <span class="dropdown-icon">▼</span>
                    </a>
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dv') }}">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\dv1_16.png">
                                </span>    
                                <span class="menu-title">Disbursement V1</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dv2') }}">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\dv2_16.png">
                                </span>
                                <span class="menu-title">Disbursement V2</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dv3') }}">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\dv3_16.png">
                                </span>
                                <span class="menu-title">Disbursement V3</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <!-- DV (new) -->
            <ul class="nav flex-column" style=" margin-bottom: 0;">
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" >
                        <span class="menu-icon">
                            <img src="\maif\public\images\new_16.png">
                        </span> 
                        <span class="menu-title">Disbursement (new)</span>
                        <span class="dropdown-icon">▼</span>
                    </a>
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('pre_dv') }}">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\new_1_16.png">
                                </span> 
                                <span class="menu-title">Pre-DV</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('pre_dv1') }}">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\new_2_16.png">
                                </span> 
                                <span class="menu-title">V1</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('pre_dv2') }}">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\new3_16.png">
                                </span> 
                                <span class="menu-title">V2</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <!-- Transmittal -->
            <ul class="nav flex-column" style=" margin-bottom: 0;">
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)">
                        <span class="menu-icon">
                            <img src="\maif\public\images\transaction_16.png">
                        </span> 
                        <span class="menu-title">Transmittal</span>
                        <span class="dropdown-icon">▼</span>
                    </a>
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('incoming') }}">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\incoming_16.png">
                                </span>
                                <span class="menu-title">Incoming Send Bills</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('returned') }}">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\returned_16.png">
                                </span>
                                <span class="menu-title">Returned Send Bills</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('accepted') }}">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\accepted_16.png">
                                </span>
                                <span class="menu-title">Accepted Send Bills</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <!-- Accounts (if condition) -->
            @if( $id == 2760 || $id == 2680 || $id == 1721|| $id == "0881")
                <ul class="nav flex-column" style=" margin-bottom: 0;">
                    <li class="nav-item">
                        <a class="nav-link" href="javascript:void(0)">
                            <span class="menu-icon">
                                <img src="\maif\public\images\user_account_16.png">
                            </span>
                            <span class="menu-title">Accounts</span>
                            <span class="dropdown-icon">▼</span>
                        </a>
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('users') }}">
                                    <span class="menu-icon">
                                        <img src="\maif\public\images\user_accounts_16.png">
                                    </span>
                                    <span class="menu-title">Online Users</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('users.activate') }}">
                                    <span class="menu-icon">
                                        <img src="\maif\public\images\user_accounts_16.png">
                                    </span>
                                    <span class="menu-title">Activation</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="javascript:void(0)">
                                    <span class="menu-icon">
                                        <img src="\maif\public\images\proponents_16.png">
                                    </span>
                                    <span class="menu-title">HOLD GL</span>
                                    <span class="dropdown-icon">▶</span>
                                </a>
                                <ul class="nav flex-column sub-menu">
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('on-hold') }}">
                                            <span class="menu-icon">
                                                <img src="\maif\public\images\proponents_16.png">
                                            </span>
                                            <span class="menu-title" style="margin-left:5px">Proponent</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('send-hold') }}">
                                            <span class="menu-icon">
                                                <img src="\maif\public\images\proponents_16.png">
                                            </span>
                                            <span class="menu-title" style="margin-left:5px">Facility</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('included.facility') }}">
                                    <span class="menu-icon">
                                        <img src="\maif\public\images\proponents_16.png">
                                    </span>
                                    <span class="menu-title">Facilities</span>
                                </a>
                            </li>
                            <!-- ABC -->
                            <li class="nav-item">
                                <a class="nav-link" href="javascript:void(0)">
                                    <span class="menu-icon">
                                        <img src="\maif\public\images\proponents_16.png">
                                    </span>
                                    <span class="menu-title">ABC - BUDGET</span>
                                    <span class="dropdown-icon">▶</span>
                                </a>
                                <ul class="nav flex-column sub-menu">
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('fundsource_budget.summary') }}">
                                            <span class="menu-icon">
                                                <img src="\maif\public\images\maip_funds_16.png">
                                            </span>
                                            <span class="menu-title">Summary</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="javascript:void(0)">
                                            <span class="menu-icon">
                                                <img src="\maif\public\images\dv_16.png">
                                            </span>
                                            <span class="menu-title">DV(1)</span>
                                            <span class="dropdown-icon">▼</span>
                                        </a>
                                        <ul class="nav flex-column sub-menu">
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{ route('fundsource_budget.pendingDv', ['type' => 'pending']) }}">
                                                    <span class="menu-icon">
                                                        <img src="\maif\public\images\pending_16.png">
                                                    </span>
                                                    <span class="menu-title">PND</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{ route('fundsource_budget.pendingDv', ['type' => 'obligated']) }}">
                                                    <span class="menu-icon">
                                                        <img src="\maif\public\images\paid_bill_16.png">
                                                    </span>
                                                    <span class="menu-title">OBL</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="javascript:void(0)">
                                            <span class="menu-icon">
                                                <img src="\maif\public\images\dv3_16.png">
                                            </span>
                                            <span class="menu-title">DV(3)</span>
                                            <span class="dropdown-icon">▼</span>
                                        </a>
                                        <ul class="nav flex-column sub-menu">
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{ route('budget.dv3', ['type' => 'unsettled']) }}">
                                                    <span class="menu-icon">
                                                        <img src="\maif\public\images\pending_16.png">
                                                    </span>
                                                    <span class="menu-title">PND</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{ route('budget.dv3', ['type' => 'processed']) }}">
                                                    <span class="menu-icon">
                                                        <img src="\maif\public\images\paid_bill_16.png">
                                                    </span>
                                                    <span class="menu-title">OBL</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="javascript:void(0)">
                                            <span class="menu-icon">
                                                <img src="\maif\public\images\new_16.png">
                                            </span>
                                            <span class="menu-title">DV(new)</span>
                                            <span class="dropdown-icon">▼</span>
                                        </a>
                                        <ul class="nav flex-column sub-menu">
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{ route('pre_dv_budget', ['type' => 'awaiting']) }}">
                                                    <span class="menu-icon">
                                                        <img src="\maif\public\images\pending_16.png">
                                                    </span>
                                                    <span class="menu-title">PND</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{ route('pre_dv_budget', ['type' => 'accomplished']) }}">
                                                    <span class="menu-icon">
                                                        <img src="\maif\public\images\paid_bill_16.png">
                                                    </span>
                                                    <span class="menu-title">OBL</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="javascript:void(0)">
                                    <span class="menu-icon">
                                        <img src="\maif\public\images\proponents_16.png">
                                    </span>
                                    <span class="menu-title">ABC - CASHIER</span>
                                    <span class="dropdown-icon">▶</span>
                                </a>
                                <ul class="nav flex-column sub-menu">
                                    <li class="nav-item">
                                        <a class="nav-link" href="javascript:void(0)">
                                            <span class="menu-icon">
                                                <img src="\maif\public\images\dv_16.png">
                                            </span>
                                            <span class="menu-title">DV (1)</span>
                                            <span class="dropdown-icon">▼</span>
                                        </a>
                                        <ul class="nav flex-column sub-menu">
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{ route('cashier', ['type' => 'pending']) }}">
                                                    <span class="menu-icon">
                                                        <img src="\maif\public\images\pending_16.png">
                                                    </span>
                                                    <span class="menu-title">PND</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{ route('cashier', ['type' => 'paid']) }}">
                                                    <span class="menu-icon">
                                                        <img src="\maif\public\images\paid_bill_16.png">
                                                    </span>
                                                    <span class="menu-title">PAID</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('dv2') }}">
                                            <span class="menu-icon">
                                                <img src="\maif\public\images\dv2_16.png">
                                            </span>
                                            <span class="menu-title">DV(2)</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="javascript:void(0)">
                                            <span class="menu-icon">
                                                <img src="\maif\public\images\dv3_16.png">
                                            </span>
                                            <span class="menu-title">DV(3)</span>
                                            <span class="dropdown-icon">▼</span>
                                        </a>
                                        <ul class="nav flex-column sub-menu">
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{ route('budget.dv3', ['type' => 'dv3_owed']) }}">
                                                    <span class="menu-icon">
                                                        <img src="\maif\public\images\pending_16.png">
                                                    </span>
                                                    <span class="menu-title">PND</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{ route('budget.dv3', ['type' => 'done']) }}">
                                                    <span class="menu-icon">
                                                        <img src="\maif\public\images\paid_bill_16.png">
                                                    </span>
                                                    <span class="menu-title">PAID</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="javascript:void(0)">
                                            <span class="menu-icon">
                                                <img src="\maif\public\images\new_16.png">
                                            </span>
                                            <span class="menu-title">DV(new)</span>
                                            <span class="dropdown-icon">▼</span>
                                        </a>
                                        <ul class="nav flex-column sub-menu">
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{ route('pre_dv_budget', ['type' => 'deferred']) }}">
                                                    <span class="menu-icon">
                                                        <img src="\maif\public\images\dv3_16.png">
                                                    </span>
                                                    <span class="menu-title">PND</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{ route('pre_dv_budget', ['type' => 'disbursed']) }}">
                                                    <span class="menu-icon">
                                                        <img src="\maif\public\images\paid_bill_16.png">
                                                    </span>
                                                    <span class="menu-title">PAID</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                </ul>
            @endif
            <!-- Report -->
            <ul class="nav flex-column" style=" margin-bottom: 0;">
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)">
                        <span class="menu-icon">
                            <img src="\maif\public\images\report_16.png">
                        </span>
                        <span class="menu-title">Report</span>
                        <span class="dropdown-icon">▼</span>
                    </a>
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('report') }}">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\pre_re_16.png">
                                </span>
                                <span class="menu-title">Proponent</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('report.facility') }}">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\fc_re_16.png">
                                </span>
                                <span class="menu-title">Facility</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('report.saa') }}">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\saa_re_16.png">
                                </span>
                                <span class="menu-title">SAA</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <!-- FUR -->
            <ul class="nav flex-column" style=" margin-bottom: 0;">
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)">
                        <span class="menu-icon">
                            <img src="\maif\public\images\report_16.png">
                        </span>
                        <span class="menu-title">FUR</span>
                        <span class="dropdown-icon">▼</span>
                    </a>
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="javascript:void(0)">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\pre_re_16.png">
                                </span>
                                <span class="menu-title">Government</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="javascript:void(0)">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\fc_re_16.png">
                                </span>
                                <span class="menu-title">Private</span>
                                <span class="dropdown-icon">▶</span>
                            </a>
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('fur.submission') }}">
                                        <span class="menu-icon">
                                            <img src="\maif\public\images\pre_re_16.png">
                                        </span>
                                        <span class="menu-title">Submission</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('fur.facilities') }}">
                                        <span class="menu-icon">
                                            <img src="\maif\public\images\fc_re_16.png">
                                        </span>
                                        <span class="menu-title">Facilities</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="javascript:void(0)">
                                        <span class="menu-icon">
                                            <img src="\maif\public\images\saa_re_16.png">
                                        </span>
                                        <span class="menu-title">Consolidated</span>
                                        <span class="dropdown-icon">▶</span>
                                    </a>
                                    <ul class="nav flex-column sub-menu">
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('fur.consolidated_a') }}">
                                                <span class="menu-icon">
                                                    <img src="\maif\public\images\pre_re_16.png">
                                                </span>
                                                <span class="menu-title">Annex A</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('fur.consolidated_b') }}">
                                                <span class="menu-icon">
                                                    <img src="\maif\public\images\fc_re_16.png">
                                                </span>
                                                <span class="menu-title">Annex B</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
            </ul>
            <!-- Logbook -->
            <ul class="nav flex-column" style="margin-bottom: 0;">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('logbook') }}">
                        <span class="menu-icon">
                            <img src="\maif\public\images\log_16.png">
                        </span>
                        <span class="menu-title">Logbook</span>
                    </a>
                </li>
            </ul>
            <!-- Facility -->
            <ul class="nav flex-column" style="margin-bottom: 0;">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('facility') }}">
                        <span class="menu-icon">
                            <img src="\maif\public\images\facility_16.png">
                        </span>
                        <span class="menu-title">Facility</span>
                    </a>
                </li>
            </ul>
            <!-- Notes -->
            <ul class="nav flex-column" style="margin-bottom: 0;">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('tasks') }}">
                        <span class="menu-icon">
                            <img src="\maif\public\images\note_16.png">
                        </span>
                        <span class="menu-title">Notes</span>
                    </a>
                </li> 
            </ul>
        @endif
        @if(Auth::user()->userid == 1027 || Auth::user()->userid == 2660)
            <!-- DV Section for specific users -->
            <ul class="nav flex-column" style="margin-bottom: 0;">
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)">
                        <span class="menu-icon">
                            <img src="\maif\public\images\dv_16.png">
                        </span>
                        <span class="menu-title">Disbursement Voucher</span>
                        <span class="dropdown-icon">▼</span>
                    </a>
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dv') }}">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\dv_16.png">
                                </span>
                                <span class="menu-title">Disbursement V1</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dv2') }}">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\dv2_16.png">
                                </span>
                                <span class="menu-title">Disbursement V2</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dv3') }}">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\dv3_16.png">
                                </span>
                                <span class="menu-title">Disbursement V3</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        @endif
        @if($joinedData->section == 7)
            <!-- Cashier Section -->
            <ul class="nav flex-column" style="margin-bottom: 0;">
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)">
                        <span class="menu-icon">
                            <img src="\maif\public\images\dv_16.png">
                        </span>
                        <span class="menu-title">DV (v1)</span>
                        <span class="dropdown-icon">▼</span>
                    </a>
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('cashier', ['type' => 'pending']) }}">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\pending_16.png">
                                </span>
                                <span class="menu-title">Pending</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('cashier', ['type' => 'paid']) }}">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\paid_bill_16.png">
                                </span>
                                <span class="menu-title">Paid</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dv2') }}">
                        <span class="menu-icon">
                            <img src="\maif\public\images\dv2_16.png">
                        </span>
                        <span class="menu-title">DV (v2)</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)">
                        <span class="menu-icon">
                            <img src="\maif\public\images\dv3_16.png">
                        </span>
                        <span class="menu-title">DV (v3)</span>
                        <span class="dropdown-icon">▼</span>
                    </a>
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('budget.dv3', ['type' => 'dv3_owed']) }}">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\pending_16.png">
                                </span>
                                <span class="menu-title">Pending</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('budget.dv3', ['type' => 'done']) }}">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\paid_bill_16.png">
                                </span>
                                <span class="menu-title">Paid</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- DV (new) Section -->
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)">
                        <span class="menu-icon">
                            <img src="\maif\public\images\new_16.png">
                        </span>
                        <span class="menu-title">DV (new)</span>
                        <span class="dropdown-icon">▼</span>
                    </a>
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('pre_dv_budget', ['type' => 'deferred']) }}">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\dv3_16.png">
                                </span>
                                <span class="menu-title">Pending</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('pre_dv_budget', ['type' => 'disbursed']) }}">
                                <span class="menu-icon">
                                    <img src="\maif\public\images\paid_bill_16.png">
                                </span>
                                <span class="menu-title">Paid</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        @endif
    </ul>
    <!-- Notes Section -->
    <div class="notes-header">
        <span class="notes-header-title">Personal Notes</span>
        <button class="add-note-btn" onclick="showModal()">+</button>
    </div>
    
    <div class="notes-legend">
        <small>Status Legend:</small>
        <div>
            <span class="legend-item">
                <span style="color: #27ae60;">●</span> DONE
            </span>
            <span class="legend-item">
                <span style="color: #3498db;">○</span> IN-PROGRESS
            </span>
        </div>
    </div>

    @foreach($notes as $note)
        @if($note->status == 0)
            <div class="sidebar-note">
                <textarea readonly>{{ $note->notes }}</textarea>
                <small>{{ $note->user->lname }}, {{ $note->user->fname }}</small>
                @if($note->status == 0)
                    <a href="{{ route('process.note', ['id' => $note->id]) }}" class="note-status"> 
                        <span class="status-icon status-progress">○ In Progress</span>
                    </a>
                @elseif($note->status == 1)
                    <span class="status-icon status-done">● Done</span>
                @endif
            </div>
        @endif
    @endforeach
    <!-- Modal -->
    <div class="modal" id="new_note">
        <div class="modal-content">
            <form action="{{ route('save.note') }}" method="POST" style="background-color: #fff3cd;">
                @csrf
                <div class="modal-header">
                    <h4 class="text-success d-flex align-items-center">
                        <i style="font-size:30px" class="typcn typcn-document-text menu-icon mr-2"></i>
                        New Note
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="form-group" style="margin-bottom:0px">
                        <textarea name="notes" placeholder="Enter your note here..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" style="background-color:gray; color:white" onclick="hideModal()">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        Save Note
                    </button>
                </div>
            </form>
        </div>
    </div>
</nav>

<script>    
    function showModal() {
        document.getElementById('new_note').classList.add('show');
    }
    
    function hideModal() {
        document.getElementById('new_note').classList.remove('show');
    }
    
    document.getElementById('new_note').addEventListener('click', function(e) {
        if (e.target === this) {
            hideModal();
        }
    });
</script>