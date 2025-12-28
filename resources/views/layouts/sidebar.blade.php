@php
    $user = auth()->user();
    $role = $user->role;
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="{{ url('/') }}" class="app-brand-link">
      <span class="app-brand-logo demo" style="font-size: 32px;">
        🐄
      </span>
      <span class="app-brand-text demo menu-text fw-bold ms-2 mt-1">Smart Dairy</span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
      <i class="bx bx-chevron-left bx-sm align-middle"></i>
    </a>
  </div>

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    <!-- Dashboard -->
    @php
        $dashboardRoute = 'dashboard';
        if(in_array($role, ['super_admin', 'admin'])) $dashboardRoute = 'admin.dashboard';
        elseif($role === 'veterinary_doctor') $dashboardRoute = 'doctor.dashboard';
        elseif($role === 'farm_worker') $dashboardRoute = 'worker.dashboard';
    @endphp
    <li class="menu-item {{ request()->routeIs($dashboardRoute) ? 'active' : '' }}">
      <a href="{{ route($dashboardRoute) }}" class="menu-link">
        <i class="menu-icon tf-icons bx bxs-dashboard"></i>
        <div data-i18n="Dashboard">Dashboard</div>
      </a>
    </li>

    <!-- Animal Health: Doctor & Admin & Manager -->
    @if(in_array($role, ['super_admin', 'admin', 'manager', 'veterinary_doctor']))
    <li class="menu-item {{ request()->is('animal-health*') || request()->is('medical-history*') || request()->is('vaccination*') || request()->is('alerts*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons fas fa-heartbeat"></i>
        <div data-i18n="Animal Health">Animal Health</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->is('animal-health') ? 'active' : '' }}">
          <a href="{{ url('/animal-health') }}" class="menu-link">
            <div data-i18n="Health Monitoring">Health Monitoring</div>
          </a>
        </li>
        <li class="menu-item {{ request()->is('medical-history') ? 'active' : '' }}">
          <a href="{{ url('/medical-history') }}" class="menu-link">
            <div data-i18n="Medical History">Medical History</div>
          </a>
        </li>
        <li class="menu-item {{ request()->is('vaccination') ? 'active' : '' }}">
          <a href="{{ url('/vaccination') }}" class="menu-link">
            <div data-i18n="Vaccination Logs">Vaccination Logs</div>
          </a>
        </li>
        <li class="menu-item {{ request()->is('alerts') ? 'active' : '' }}">
          <a href="{{ url('/alerts') }}" class="menu-link">
            <div data-i18n="Emergency Records">Emergency Records</div>
          </a>
        </li>
      </ul>
    </li>
    @endif

    <!-- Milk Production: Worker & Admin & Manager -->
    @if(in_array($role, ['super_admin', 'admin', 'manager', 'farm_worker']))
    <li class="menu-item {{ request()->is('milk-tracking*') || request()->is('quality-analysis*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons fas fa-vial"></i>
        <div data-i18n="Milk Production">Milk Production</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->is('milk-tracking') ? 'active' : '' }}">
          <a href="{{ url('/milk-tracking') }}" class="menu-link">
            <div data-i18n="Milk Tracking">Milk Tracking</div>
          </a>
        </li>
        <li class="menu-item {{ request()->is('quality-analysis') ? 'active' : '' }}">
          <a href="{{ url('/quality-analysis') }}" class="menu-link">
            <div data-i18n="Quality Analysis">Quality Analysis</div>
          </a>
        </li>
      </ul>
    </li>
    @endif

    <!-- Emergency History for Worker Only - Simplified -->
    @if($role === 'farm_worker')
    <li class="menu-item {{ request()->is('alerts*') ? 'active' : '' }}">
      <a href="{{ url('/alerts') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-bell"></i>
        <div data-i18n="Emergency History">Emergency History</div>
      </a>
    </li>
    @endif

    <!-- Admin ONLY Modules -->
    @if(in_array($role, ['super_admin', 'admin', 'manager']))
    <li class="menu-header small text-uppercase"><span class="menu-header-text">Administration</span></li>

    <li class="menu-item {{ request()->is('employees*') || request()->is('attendance*') || request()->is('payroll*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons fas fa-users-cog"></i>
        <div data-i18n="HR Management">HR Management</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->is('employees') ? 'active' : '' }}">
          <a href="{{ url('/employees') }}" class="menu-link">
            <div data-i18n="Employee List">Employee List</div>
          </a>
        </li>
        <li class="menu-item {{ request()->is('attendance') ? 'active' : '' }}">
          <a href="{{ url('/attendance') }}" class="menu-link">
            <div data-i18n="Attendance">Attendance</div>
          </a>
        </li>
        <li class="menu-item {{ request()->is('payroll') ? 'active' : '' }}">
          <a href="{{ url('/payroll') }}" class="menu-link">
            <div data-i18n="Payroll">Payroll</div>
          </a>
        </li>
      </ul>
    </li>

    <li class="menu-item {{ request()->is('expenses*') || request()->is('income*') || request()->is('profit-analysis*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons fas fa-file-invoice-dollar"></i>
        <div data-i18n="Financials">Financials</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->is('expenses') ? 'active' : '' }}">
          <a href="{{ url('/expenses') }}" class="menu-link">
            <div data-i18n="Expenses">Expenses</div>
          </a>
        </li>
        <li class="menu-item {{ request()->is('income') ? 'active' : '' }}">
          <a href="{{ url('/income') }}" class="menu-link">
            <div data-i18n="Income">Income</div>
          </a>
        </li>
        <li class="menu-item {{ request()->is('profit-analysis') ? 'active' : '' }}">
          <a href="{{ url('/profit-analysis') }}" class="menu-link">
            <div data-i18n="Profit Analysis">Profit Analysis</div>
          </a>
        </li>
      </ul>
    </li>

    <li class="menu-item {{ request()->is('inventory*') || request()->is('lifecycle*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons fas fa-boxes-stacked"></i>
        <div data-i18n="Inventory">Inventory Management</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->is('inventory') && !request()->is('inventory/*') ? 'active' : '' }}">
          <a href="{{ url('/inventory') }}" class="menu-link">
            <div data-i18n="Stock Overview">Stock Overview</div>
          </a>
        </li>
        <li class="menu-item {{ request()->is('inventory/feed-supplies') ? 'active' : '' }}">
          <a href="{{ url('/inventory/feed-supplies') }}" class="menu-link">
            <div data-i18n="Feed & Supplies">Feed & Supplies</div>
          </a>
        </li>
        <li class="menu-item {{ request()->is('inventory/medical-supplies') ? 'active' : '' }}">
          <a href="{{ url('/inventory/medical-supplies') }}" class="menu-link">
            <div data-i18n="Medical Supplies">Medical Supplies</div>
          </a>
        </li>
      </ul>
    </li>

    <li class="menu-item {{ request()->is('reports*') ? 'active' : '' }}">
      <a href="{{ url('/reports') }}" class="menu-link">
        <i class="menu-icon tf-icons fas fa-chart-bar"></i>
        <div data-i18n="Data Reports">Data Reports</div>
      </a>
    </li>
    @endif

    <!-- Account Services -->
    @if(!in_array($role, ['farm_worker', 'veterinary_doctor']))
    <li class="menu-header small text-uppercase"><span class="menu-header-text">Account Services</span></li>
    @endif

    <!-- Profile (For All) -->
    <li class="menu-item {{ request()->is('profile*') ? 'active' : '' }}">
      <a href="{{ url('/profile') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-user"></i>
        <div data-i18n="My Profile">My Profile</div>
      </a>
    </li>

    <!-- Settings (Admin ONLY) -->
    @if(in_array($role, ['super_admin', 'admin', 'manager']))
    <li class="menu-item {{ request()->is('settings*') || request()->routeIs('users.index') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-cog"></i>
        <div data-i18n="System Settings">System Settings</div>
        @if(isset($pendingUsersCount) && $pendingUsersCount > 0)
          <span class="badge badge-center rounded-pill bg-danger ms-auto">{{ $pendingUsersCount }}</span>
        @endif
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->is('settings') ? 'active' : '' }}">
          <a href="{{ url('/settings') }}" class="menu-link">
            <div data-i18n="General Configuration">General Configuration</div>
          </a>
        </li>
        @if(in_array($role, ['super_admin', 'admin']))
        <li class="menu-item {{ request()->routeIs('users.index') ? 'active' : '' }}">
          <a href="{{ route('users.index') }}" class="menu-link">
            <div data-i18n="User Authorization">User Authorization</div>
            @if(isset($pendingUsersCount) && $pendingUsersCount > 0)
                <span class="badge badge-center rounded-pill bg-danger ms-auto">{{ $pendingUsersCount }}</span>
            @endif
          </a>
        </li>
        @endif
      </ul>
    </li>
    @endif

  </ul>
</aside>
