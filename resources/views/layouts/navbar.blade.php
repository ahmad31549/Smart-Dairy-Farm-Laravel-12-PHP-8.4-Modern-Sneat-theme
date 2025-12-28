<nav
  class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
  id="layout-navbar"
>
  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
      <i class="bx bx-menu bx-sm"></i>
    </a>
  </div>

  <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
    <!-- Search -->
    <div class="navbar-nav align-items-center">
      <div class="nav-item d-flex align-items-center position-relative">
        <i class="bx bx-search fs-4 lh-0 search-icon"></i>
        <input
          type="text"
          class="form-control border-0 shadow-none bg-transparent"
          placeholder="Search modules... (Ctrl + K)"
          aria-label="Search..."
          id="navbarSearchInput"
          autocomplete="off"
        />
        <!-- Integrated Search Results Dropdown -->
        <div id="navbarSearchResults" class="dropdown-menu search-dropdown-menu shadow-lg border-0 py-2 w-px-400" style="display: none; position: absolute; top: 100%; left: 0; z-index: 1000; max-height: 400px; overflow-y: auto;">
            <div id="searchResultsContent"></div>
        </div>
      </div>
    </div>
    <!-- /Search -->

    <ul class="navbar-nav flex-row align-items-center ms-auto">
      <!-- Theme Toggle -->
      <li class="nav-item me-2 me-xl-1">
        <a class="nav-link theme-toggle-btn cursor-pointer" onclick="toggleTheme()" title="Toggle Dark/Light Mode">
          <i class="bx bx-moon fs-4"></i>
        </a>
      </li>
      <!-- / Theme Toggle -->

      <!-- Notifications -->
      <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-1">
        <a
          class="nav-link dropdown-toggle hide-arrow"
          href="javascript:void(0);"
          data-bs-toggle="dropdown"
          data-bs-auto-close="outside"
          aria-expanded="false"
        >
          <i class="bx bx-bell bx-sm"></i>
          @if(auth()->user()->unreadNotifications->count() > 0)
            <span class="badge bg-danger rounded-pill badge-notifications">{{ auth()->user()->unreadNotifications->count() }}</span>
          @endif
        </a>
        <ul class="dropdown-menu dropdown-menu-end py-0">
          <li class="dropdown-menu-header border-bottom">
            <div class="dropdown-header d-flex align-items-center py-3">
              <h5 class="text-body mb-0 me-auto">Notifications</h5>
              @if(auth()->user()->unreadNotifications->count() > 0)
              <a
                href="javascript:void(0)"
                class="dropdown-notifications-all text-body"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                title="Mark all as read"
                onclick="markNotificationsRead()"
              >
                <i class="bx fs-4 bx-envelope-open"></i>
              </a>
              @endif
            </div>
          </li>
          <li class="dropdown-notifications-list scrollable-container">
            <ul class="list-group list-group-flush">
              @forelse(auth()->user()->unreadNotifications as $notification)
              <li class="list-group-item list-group-item-action dropdown-notifications-item">
                <a href="{{ $notification->data['link'] ?? 'javascript:void(0)' }}" class="d-flex text-decoration-none text-body">
                  <div class="flex-shrink-0 me-3">
                    <div class="avatar">
                        @if(($notification->data['type'] ?? '') == 'milk_entry')
                          <span class="avatar-initial rounded-circle bg-label-primary"><i class="fas fa-vial"></i></span>
                        @elseif(($notification->data['type'] ?? '') == 'new_animal')
                           <span class="avatar-initial rounded-circle bg-label-success"><i class="fas fa-cow"></i></span>
                        @elseif(($notification->data['type'] ?? '') == 'emergency_alert')
                           <span class="avatar-initial rounded-circle bg-label-danger"><i class="fas fa-exclamation-triangle"></i></span>
                        @elseif(($notification->data['type'] ?? '') == 'doctor_advice')
                           <span class="avatar-initial rounded-circle bg-label-info"><i class="fas fa-user-md"></i></span>
                        @elseif(($notification->data['type'] ?? '') == 'doctor_forward')
                           <span class="avatar-initial rounded-circle bg-label-warning"><i class="fas fa-hospital-user"></i></span>
                        @else
                           <span class="avatar-initial rounded-circle bg-label-secondary"><i class="bx bx-bell"></i></span>
                        @endif
                    </div>
                  </div>
                  <div class="flex-grow-1">
                    <h6 class="mb-1">{{ $notification->data['message'] ?? 'Notification' }}</h6>
                    <p class="mb-0">{{ $notification->data['details'] ?? '' }}</p>
                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                  </div>
                </a>
              </li>
              @empty
              <li class="list-group-item list-group-item-action dropdown-notifications-item">
                  <div class="d-flex align-items-center justify-content-center pt-2">
                       <p class="mb-0">No new notifications</p>
                  </div>
              </li>
              @endforelse
            </ul>
          </li>
          @if(auth()->user()->unreadNotifications->count() > 0)
          <li class="dropdown-menu-footer border-top text-center p-3">
            <button class="btn btn-primary text-uppercase w-100" onclick="markNotificationsRead()">Mark all as read</button>
          </li>
          @endif
        </ul>
      </li>
      <!--/ Notifications -->

      <!-- User -->
      <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
          <div class="avatar avatar-online">
            <img src="{{ Auth::user()->profile_image ? asset(Auth::user()->profile_image) : asset('assets/sneat/img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle" />
          </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <a class="dropdown-item" href="#">
              <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                  <div class="avatar avatar-online">
                    <img src="{{ Auth::user()->profile_image ? asset(Auth::user()->profile_image) : asset('assets/sneat/img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle" />
                  </div>
                </div>
                <div class="flex-grow-1">
                  <span class="fw-semibold d-block">{{ Auth::user()->name }}</span>
                  <small class="text-muted">{{ Auth::user()->role }}</small>
                </div>
              </div>
            </a>
          </li>
          <li>
            <div class="dropdown-divider"></div>
          </li>
          <li>
            <a class="dropdown-item" href="{{ url('/profile') }}">
              <i class="bx bx-user me-2"></i>
              <span class="align-middle">My Profile</span>
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="{{ url('/settings') }}">
              <i class="bx bx-cog me-2"></i>
              <span class="align-middle">Settings</span>
            </a>
          </li>
          <li>
            <div class="dropdown-divider"></div>
          </li>
          <li>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dropdown-item">
                    <i class="bx bx-power-off me-2"></i>
                    <span class="align-middle">Log Out</span>
                </button>
            </form>
          </li>
        </ul>
      </li>
      <!--/ User -->
    </ul>
  </div>
</nav>

<style>
.search-dropdown-menu {
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
}
.search-item {
    transition: all 0.2s ease;
    border-radius: 8px;
    margin: 2px 8px;
}
.search-item:hover {
    background-color: #f8fafc;
    color: #696cff;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('navbarSearchInput');
    const resultsContainer = document.getElementById('navbarSearchResults');
    const resultsContent = document.getElementById('searchResultsContent');
    
    const searchableItems = [
        { title: 'Admin Dashboard', category: 'Navigation', url: '/admin/dashboard', icon: 'fas fa-chart-line' },
        { title: 'Animal Health Monitoring', category: 'Health', url: '/animal-health', icon: 'fas fa-heartbeat' },
        { title: 'Milk Production Tracking', category: 'Production', url: '/milk-tracking', icon: 'fas fa-vial' },
        { title: 'Employee Management', category: 'General', url: '/employees', icon: 'fas fa-users' },
        { title: 'Attendance Records', category: 'General', url: '/attendance', icon: 'fas fa-user-check' },
        { title: 'Financial Analysis', category: 'Admin', url: '/profit-analysis', icon: 'fas fa-file-invoice-dollar' },
        { title: 'Inventory Management', category: 'Admin', url: '/inventory', icon: 'fas fa-boxes-stacked' },
        { title: 'Emergency Records', category: 'Health', url: '/alerts', icon: 'fas fa-exclamation-triangle' },
    ];

    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        if (query.length > 0) {
            const filtered = searchableItems.filter(item => 
                item.title.toLowerCase().includes(query) || 
                item.category.toLowerCase().includes(query)
            );
            renderNavbarResults(filtered);
            resultsContainer.style.display = 'block';
        } else {
            resultsContainer.style.display = 'none';
        }
    });

    function renderNavbarResults(items) {
        if (items.length === 0) {
            resultsContent.innerHTML = '<div class="px-3 py-2 text-muted small">No results matches.</div>';
            return;
        }
        let html = '';
        items.forEach(item => {
            html += `
                <a href="${item.url}" class="dropdown-item d-flex align-items-center py-2 search-item">
                    <div class="avatar avatar-xs me-2">
                        <span class="avatar-initial rounded bg-label-primary"><i class="${item.icon} font-size-12"></i></span>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0 small fw-semibold">${item.title}</h6>
                        <small class="text-muted" style="font-size: 0.65rem;">${item.category}</small>
                    </div>
                </a>
            `;
        });
        resultsContent.innerHTML = html;
    }

    // Close dropdown on outside click
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
            resultsContainer.style.display = 'none';
        }
    });

    // Keyboard navigation Ctrl + K
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            searchInput.focus();
        }
    });
});

function markNotificationsRead() {
    // Show loading
    Swal.fire({
        title: 'Marking as read...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch('/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        Swal.close();
        if (data.success) {
            Toast.fire({
                icon: 'success',
                title: 'All notifications marked as read'
            });
            setTimeout(() => location.reload(), 1000);
        } else {
            Toast.fire({
                icon: 'error',
                title: 'Failed to mark notifications'
            });
        }
    })
    .catch(error => {
        Swal.close();
        console.error('Error:', error);
        Toast.fire({
            icon: 'error',
            title: 'Error marking notifications'
        });
    });
}
</script>

