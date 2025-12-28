<!-- Search Modal -->
<div class="modal fade" id="globalSearchModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <div class="input-group input-group-merge w-100 border-bottom-0">
          <span class="input-group-text border-0 ps-0" id="global-search-icon">
            <i class="bx bx-search fs-4 text-primary"></i>
          </span>
          <input
            type="text"
            class="form-control border-0 shadow-none ps-2 fs-5"
            placeholder="Search for modules, animals, or records... (Ctrl + K)"
            aria-label="Search..."
            id="globalSearchInput"
            autocomplete="off"
          />
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      </div>
      <div class="modal-body pt-0" style="max-height: 450px; overflow-y: auto;">
        <div id="searchResults" class="list-group list-group-flush mt-2">
            <!-- Initial content/Suggestions -->
            <div class="search-suggestions">
                <h6 class="text-muted text-uppercase fs-tiny fw-semibold mb-3">Quick Navigation</h6>
                <div class="row g-2">
                    <div class="col-md-4">
                        <a href="{{ url('/admin/dashboard') }}" class="card bg-label-primary border-0 shadow-none h-100 text-decoration-none p-3 shadow-hover-sm transition-all text-center">
                            <i class="fas fa-chart-line mb-2 fs-4"></i>
                            <span class="d-block fw-medium">Dashboard</span>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ url('/animal-health') }}" class="card bg-label-success border-0 shadow-none h-100 text-decoration-none p-3 shadow-hover-sm transition-all text-center">
                            <i class="fas fa-heartbeat mb-2 fs-4"></i>
                            <span class="d-block fw-medium">Animal Health</span>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ url('/milk-tracking') }}" class="card bg-label-info border-0 shadow-none h-100 text-decoration-none p-3 shadow-hover-sm transition-all text-center">
                            <i class="fas fa-vial mb-2 fs-4"></i>
                            <span class="d-block fw-medium">Milk Production</span>
                        </a>
                    </div>
                </div>

                <h6 class="text-muted text-uppercase fs-tiny fw-semibold mt-4 mb-3">Popular Searches</h6>
                <div class="list-group list-group-flush border-top-0">
                    <a href="javascript:void(0);" class="list-group-item list-group-item-action border-0 px-0 d-flex align-items-center" onclick="populateSearch('Vaccination')">
                        <i class="bx bx-history me-2"></i> Vaccination Records
                    </a>
                    <a href="javascript:void(0);" class="list-group-item list-group-item-action border-0 px-0 d-flex align-items-center" onclick="populateSearch('Employees')">
                        <i class="bx bx-history me-2"></i> Employee Attendance
                    </a>
                    <a href="javascript:void(0);" class="list-group-item list-group-item-action border-0 px-0 d-flex align-items-center" onclick="populateSearch('Revenue')">
                        <i class="bx bx-history me-2"></i> Financial Reports
                    </a>
                </div>
            </div>
            
            <!-- Dynamic Results -->
            <div id="dynamicSearchResults" style="display: none;">
                <!-- Results will be injected here -->
            </div>
        </div>
      </div>
      <div class="modal-footer justify-content-start border-top py-2">
        <div class="d-flex align-items-center gap-3">
            <small class="text-muted"><kbd class="bg-light text-dark px-1">ENTER</kbd> to select</small>
            <small class="text-muted"><kbd class="bg-light text-dark px-1">↑↓</kbd> to navigate</small>
            <small class="text-muted"><kbd class="bg-light text-dark px-1">ESC</kbd> to close</small>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
#searchResults .list-group-item {
    transition: all 0.2s ease;
    border-radius: 8px !important;
    margin-bottom: 4px;
}
#searchResults .list-group-item:hover, 
#searchResults .list-group-item.active {
    background-color: var(--bs-primary-bg-subtle);
    color: var(--bs-primary);
    padding-left: 1rem !important;
}
.transition-all {
    transition: all 0.2s ease-in-out;
}
.shadow-hover-sm:hover {
    transform: translateY(-2px);
    box-shadow: 0 .125rem .25rem rgba(0,0,0,.075) !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('globalSearchInput');
    const dynamicResults = document.getElementById('dynamicSearchResults');
    const suggestions = document.querySelector('.search-suggestions');
    const searchableItems = [
        { title: 'Admin Dashboard', category: 'Navigation', url: '/admin/dashboard', icon: 'fas fa-chart-line' },
        { title: 'Animal Health Monitoring', category: 'Health', url: '/animal-health', icon: 'fas fa-heartbeat' },
        { title: 'Milk Production Tracking', category: 'Production', url: '/milk-tracking', icon: 'fas fa-vial' },
        { title: 'Employee Management', category: 'HR', url: '/employees', icon: 'fas fa-users' },
        { title: 'Attendance Records', category: 'HR', url: '/attendance', icon: 'fas fa-user-check' },
        { title: 'Payroll System', category: 'HR', url: '/payroll', icon: 'fas fa-money-check-alt' },
        { title: 'Financial Analysis', category: 'Financials', url: '/profit-analysis', icon: 'fas fa-file-invoice-dollar' },
        { title: 'Inventory Management', category: 'Inventory', url: '/inventory', icon: 'fas fa-boxes-stacked' },
        { title: 'Expense Tracking', category: 'Financials', url: '/expenses', icon: 'fas fa-wallet' },
        { title: 'Medical History', category: 'Health', url: '/medical-history', icon: 'fas fa-notes-medical' },
        { title: 'Vaccination Records', category: 'Health', url: '/vaccination', icon: 'fas fa-syringe' },
        { title: 'Alerts & Notifications', category: 'System', url: '/alerts', icon: 'fas fa-exclamation-triangle' },
    ];

    // Global Shortcut Ctrl + K
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            const modal = new bootstrap.Modal(document.getElementById('globalSearchModal'));
            modal.show();
        }
    });

    // Handle Search Input
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        
        if (query.length > 0) {
            suggestions.style.display = 'none';
            dynamicResults.style.display = 'block';
            
            const filtered = searchableItems.filter(item => 
                item.title.toLowerCase().includes(query) || 
                item.category.toLowerCase().includes(query)
            );
            
            renderResults(filtered);
        } else {
            suggestions.style.display = 'block';
            dynamicResults.style.display = 'none';
        }
    });

    function renderResults(items) {
        if (items.length === 0) {
            dynamicResults.innerHTML = `
                <div class="text-center py-5">
                    <i class="bx bx-search fs-1 text-muted mb-2"></i>
                    <p class="text-muted">No results found for your search.</p>
                </div>
            `;
            return;
        }

        let html = '<h6 class="text-muted text-uppercase fs-tiny fw-semibold mb-3">Matching Results</h6>';
        items.forEach((item, index) => {
            html += `
                <a href="${item.url}" class="list-group-item list-group-item-action border-0 d-flex align-items-center py-3">
                    <div class="avatar avatar-sm me-3">
                        <span class="avatar-initial rounded bg-label-primary"><i class="${item.icon}"></i></span>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0">${item.title}</h6>
                        <small class="text-muted">${item.category}</small>
                    </div>
                    <i class="bx bx-chevron-right"></i>
                </a>
            `;
        });
        dynamicResults.innerHTML = html;
    }

    // Modal shown focus
    const modalEl = document.getElementById('globalSearchModal');
    modalEl.addEventListener('shown.bs.modal', () => {
        searchInput.focus();
    });

    // Populate search from popular searches
    window.populateSearch = function(text) {
        searchInput.value = text;
        searchInput.dispatchEvent(new Event('input'));
    };
});
</script>
