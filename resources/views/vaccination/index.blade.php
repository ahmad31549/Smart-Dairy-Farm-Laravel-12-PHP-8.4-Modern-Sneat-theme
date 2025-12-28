@extends('layouts.app')

@section('title', 'Vaccination Logs')

@push('styles')
<style>
    .border-left-success { border-left: 0.25rem solid var(--success-main) !important; }
    .border-left-warning { border-left: 0.25rem solid var(--warning-main) !important; }
    .border-left-danger { border-left: 0.25rem solid var(--danger-main) !important; }

    /* Action buttons styling */
    .action-buttons {
        display: flex;
        flex-direction: row;
        gap: 0.5rem;
        justify-content: center;
        align-items: center;
        flex-wrap: nowrap;
        white-space: nowrap;
    }

    .action-buttons .btn {
        min-width: 40px;
        height: 40px;
        width: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        border: none;
        padding: 0;
        flex-shrink: 0;
    }

    .action-buttons .btn i {
        font-size: 1rem;
    }

    .action-buttons .btn-primary {
        background-color: #2563eb;
    }

    .action-buttons .btn-primary:hover {
        background-color: #1d4ed8;
    }

    .action-buttons .btn-warning {
        background-color: #2563eb;
        color: white;
    }

    .action-buttons .btn-warning:hover {
        background-color: #1d4ed8;
        color: white;
    }

    .action-buttons .btn-danger {
        background-color: #dc2626;
    }

    .action-buttons .btn-danger:hover {
        background-color: #b91c1c;
    }

    /* Filter Section Styling */
    .form-label {
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }

    .form-select, .form-control {
        border-radius: 0.5rem;
        transition: all 0.3s ease;
    }

    .form-select:focus, .form-control:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.15);
    }

    .form-select-lg {
        font-size: 0.95rem;
        padding: 0.625rem 1rem;
    }

    .form-control-lg {
        font-size: 0.95rem;
        padding: 0.625rem 1rem;
    }

    .input-group-text {
        border-radius: 0.5rem 0 0 0.5rem;
    }

    .border-2 {
        border-width: 2px !important;
    }

    .fw-semibold {
        font-weight: 600;
    }

    /* Status badges */
    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 500;
    }
    .status-completed {
        background-color: #d1fae5;
        color: #065f46;
    }
    .status-due {
        background-color: #fef3c7;
        color: #92400e;
    }
    .status-overdue {
        background-color: #fee2e2;
        color: #991b1b;
    }

    /* Pagination styling */
    .pagination-info {
        color: #6c757d;
        font-size: 0.875rem;
    }

    .pagination .page-link {
        color: #4e73df;
        border: 1px solid #dee2e6;
        padding: 0.375rem 0.75rem;
    }

    .pagination .page-item.active .page-link {
        background-color: #4e73df;
        border-color: #4e73df;
        color: white;
    }

    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        cursor: not-allowed;
    }

    .pagination .page-link:hover:not(.disabled) {
        background-color: #e9ecef;
        border-color: #dee2e6;
    }

    /* Filter Card Animation */
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .card-body {
        animation: slideDown 0.4s ease;
    }

    @media (max-width: 576px) {
        .pagination-info {
            font-size: 0.75rem;
            margin-bottom: 0.5rem;
        }

        .pagination {
            font-size: 0.875rem;
        }

        .pagination .page-link {
            padding: 0.25rem 0.5rem;
        }
    }
</style>
@endpush

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Vaccination Logs</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportVaccinationData()">
                <i class="fas fa-download me-1"></i>Export
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="printVaccinationReport()">
                <i class="fas fa-print me-1"></i>Print
            </button>
        </div>
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addVaccinationModal">
            <i class="fas fa-plus me-1"></i>Add Vaccination Record
        </button>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Vaccinated This Month</div>
                        <div class="h5 mb-0 font-weight-bold" id="thisMonthCount">{{ $thisMonth }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-syringe fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Due This Week</div>
                        <div class="h5 mb-0 font-weight-bold" id="dueThisWeekCount">{{ $dueThisWeek }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Overdue</div>
                        <div class="h5 mb-0 font-weight-bold" id="overdueCount">{{ $overdue }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Search -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <div class="row g-3 align-items-end">
                    <!-- Status Filter -->
                    <div class="col-lg-3 col-md-6">
                        <label for="statusFilter" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-heartbeat text-primary me-2"></i>Status
                        </label>
                        <select class="form-select form-select-lg border-2" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="completed">✓ Completed</option>
                            <option value="due">⚠ Due Soon</option>
                            <option value="overdue">⚠ Overdue</option>
                        </select>
                    </div>

                    <!-- Vaccine Type Filter -->
                    <div class="col-lg-3 col-md-6">
                        <label for="vaccineFilter" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-vial text-success me-2"></i>Vaccine Type
                        </label>
                        <select class="form-select form-select-lg border-2" id="vaccineFilter">
                            <option value="">All Vaccines</option>
                            <option value="FMD">FMD</option>
                            <option value="Brucellosis">Brucellosis</option>
                            <option value="Blackleg">Blackleg</option>
                            <option value="IBR">IBR</option>
                            <option value="BVD">BVD</option>
                            <option value="Leptospirosis">Leptospirosis</option>
                            <option value="Anthrax">Anthrax</option>
                        </select>
                    </div>

                    <!-- Date Range Filter -->
                    <div class="col-lg-3 col-md-6">
                        <label for="dateRangeFilter" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-calendar-alt text-info me-2"></i>Date Range
                        </label>
                        <select class="form-select form-select-lg border-2" id="dateRangeFilter">
                            <option value="">All Dates</option>
                            <option value="last-7-days">Last 7 Days</option>
                            <option value="last-30-days">Last 30 Days</option>
                            <option value="last-90-days">Last 90 Days</option>
                            <option value="this-year">This Year</option>
                        </select>
                    </div>

                    <!-- Search Box -->
                    <div class="col-lg-3 col-md-6">
                        <label for="vaccinationSearch" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-search text-warning me-2"></i>Search
                        </label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-white border-2 border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control form-control-lg border-2 border-start-0 ps-0"
                                   id="vaccinationSearch" placeholder="Animal ID, vaccine...">
                        </div>
                    </div>
                </div>

                <!-- Clear Filters Button -->
                <div class="row mt-3">
                    <div class="col-12 text-end">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearFilters()">
                            <i class="fas fa-redo me-1"></i>Clear Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Vaccination Records Table -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Vaccination Records</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                        data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow">
                        <a class="dropdown-item" href="#" onclick="refreshVaccinationData()">Refresh Data</a>
                        <a class="dropdown-item" href="#" onclick="exportVaccinationData()">Export Data</a>
                        <a class="dropdown-item" href="#" onclick="printVaccinationReport()">Print Report</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="vaccinationsTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Animal ID</th>
                                <th>Animal Name</th>
                                <th>Vaccine Name</th>
                                <th>Batch Number</th>
                                <th>Veterinarian</th>
                                <th>Next Due Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="vaccinationsBody">
                            <tr>
                                <td colspan="9" class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center flex-wrap mt-3 gap-2">
                    <div class="pagination-info">
                        Showing <span id="showingFrom">1</span> to <span id="showingTo">20</span> of <span id="totalRecords">0</span> records
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination mb-0" id="paginationControls">
                            <!-- Pagination buttons will be generated here -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Vaccination Modal -->
<div class="modal fade" id="addVaccinationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Vaccination Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addVaccinationForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Animal <span class="text-danger">*</span></label>
                        <select class="form-select" name="animal_id" id="animal_id" required>
                            <option value="">Select Animal</option>
                            @foreach($animals as $animal)
                                <option value="{{ $animal->id }}">{{ $animal->tag_number }} - {{ $animal->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Vaccine Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="vaccine_name" id="vaccine_name" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date Administered <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="date_administered" id="date_administered" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Next Due Date</label>
                            <input type="date" class="form-control" name="next_due_date" id="next_due_date">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Batch Number</label>
                        <input type="text" class="form-control" name="batch_number" id="batch_number">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Veterinarian <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="veterinarian" id="veterinarian" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" id="notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveVaccination()">Save Record</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Vaccination Modal -->
<div class="modal fade" id="editVaccinationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Vaccination Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editVaccinationForm">
                    @csrf
                    <input type="hidden" name="vaccination_id" id="edit_vaccination_id">
                    <div class="mb-3">
                        <label class="form-label">Animal <span class="text-danger">*</span></label>
                        <select class="form-select" name="animal_id" id="edit_animal_id" required>
                            <option value="">Select Animal</option>
                            @foreach($animals as $animal)
                                <option value="{{ $animal->id }}">{{ $animal->tag_number }} - {{ $animal->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Vaccine Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="vaccine_name" id="edit_vaccine_name" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date Administered <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="date_administered" id="edit_date_administered" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Next Due Date</label>
                            <input type="date" class="form-control" name="next_due_date" id="edit_next_due_date">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Batch Number</label>
                        <input type="text" class="form-control" name="batch_number" id="edit_batch_number">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Veterinarian <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="veterinarian" id="edit_veterinarian" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" id="edit_notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updateVaccination()">Update Record</button>
            </div>
        </div>
    </div>
</div>

<!-- View Vaccination Modal -->
<div class="modal fade" id="viewVaccinationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Vaccination Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="vaccinationDetails">
                <!-- Details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Pagination variables
    let allRecords = [];
    let filteredRecords = [];
    let currentPage = 1;
    const recordsPerPage = 20;

    // Load vaccinations on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadVaccinations();

        // Add filter event listeners
        document.getElementById('statusFilter')?.addEventListener('change', applyFilters);
        document.getElementById('vaccineFilter')?.addEventListener('change', applyFilters);
        document.getElementById('dateRangeFilter')?.addEventListener('change', applyFilters);
        document.getElementById('vaccinationSearch')?.addEventListener('input', debounce(applyFilters, 500));
    });

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function applyFilters() {
        currentPage = 1;
        loadVaccinations(1);
    }

    // Load vaccination records
    function loadVaccinations(page = 1) {
        const tbody = document.getElementById('vaccinationsBody');
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </td>
            </tr>
        `;

        const params = new URLSearchParams();
        params.set('page', page);
        
        const status = document.getElementById('statusFilter').value;
        const vaccine = document.getElementById('vaccineFilter').value;
        const dateRange = document.getElementById('dateRangeFilter').value;
        const search = document.getElementById('vaccinationSearch').value;

        if (status) params.set('status', status);
        if (vaccine) params.set('vaccine', vaccine);
        if (dateRange) params.set('date_range', dateRange);
        if (search) params.set('search', search);

        fetch('/api/vaccinations?' + params.toString())
            .then(response => response.json())
            .then(response => {
                displayRecords(response.data);
                updatePaginationInfo(response.pagination.from || 0, response.pagination.to || 0, response.pagination.total);
                renderPaginationControls(response.pagination.last_page, response.pagination.current_page);
                currentPage = response.pagination.current_page;
            })
            .catch(error => {
                console.error('Error loading vaccinations:', error);
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" class="text-center text-danger py-5">Error loading vaccination records</td>
                    </tr>
                `;
            });
    }

    function displayRecords(records) {
        const tbody = document.getElementById('vaccinationsBody');
        tbody.innerHTML = '';

        if (!records || records.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center py-5">No vaccination records found</td>
                </tr>
            `;
            return;
        }

        records.forEach(vaccination => {
            const statusClass = getStatusClass(vaccination.status);
            const row = `
                <tr>
                    <td>${vaccination.date_administered}</td>
                    <td>${vaccination.animal_id}</td>
                    <td>${vaccination.animal_name}</td>
                    <td>${vaccination.vaccine_name}</td>
                    <td>${vaccination.batch_number}</td>
                    <td>${vaccination.veterinarian}</td>
                    <td>${vaccination.next_due_date}</td>
                    <td><span class="status-badge ${statusClass}">${vaccination.status.toUpperCase()}</span></td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-primary" onclick="viewVaccination(${vaccination.id})" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-warning" onclick="editVaccination(${vaccination.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger" onclick="deleteVaccination(${vaccination.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    }

    function updatePaginationInfo(from, to, total) {
        document.getElementById('showingFrom').textContent = from;
        document.getElementById('showingTo').textContent = to;
        document.getElementById('totalRecords').textContent = total;
    }

    function renderPaginationControls(totalPages, activePage) {
        const paginationControls = document.getElementById('paginationControls');
        paginationControls.innerHTML = '';

        if (totalPages <= 1) {
            return;
        }

        // Previous button
        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${activePage === 1 ? 'disabled' : ''}`;
        prevLi.innerHTML = `
            <a class="page-link" href="#" onclick="changePage(${activePage - 1}); return false;">
                <i class="fas fa-chevron-left"></i> Previous
            </a>
        `;
        paginationControls.appendChild(prevLi);

        // Page numbers
        const maxVisiblePages = 5;
        let startPage = Math.max(1, activePage - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

        if (endPage - startPage < maxVisiblePages - 1) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }

        // First page
        if (startPage > 1) {
            const firstLi = document.createElement('li');
            firstLi.className = 'page-item';
            firstLi.innerHTML = `<a class="page-link" href="#" onclick="changePage(1); return false;">1</a>`;
            paginationControls.appendChild(firstLi);

            if (startPage > 2) {
                const dotsLi = document.createElement('li');
                dotsLi.className = 'page-item disabled';
                dotsLi.innerHTML = `<a class="page-link" href="#">...</a>`;
                paginationControls.appendChild(dotsLi);
            }
        }

        // Page numbers
        for (let i = startPage; i <= endPage; i++) {
            const pageLi = document.createElement('li');
            pageLi.className = `page-item ${i === activePage ? 'active' : ''}`;
            pageLi.innerHTML = `<a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a>`;
            paginationControls.appendChild(pageLi);
        }

        // Last page
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                const dotsLi = document.createElement('li');
                dotsLi.className = 'page-item disabled';
                dotsLi.innerHTML = `<a class="page-link" href="#">...</a>`;
                paginationControls.appendChild(dotsLi);
            }

            const lastLi = document.createElement('li');
            lastLi.className = 'page-item';
            lastLi.innerHTML = `<a class="page-link" href="#" onclick="changePage(${totalPages}); return false;">${totalPages}</a>`;
            paginationControls.appendChild(lastLi);
        }

        // Next button
        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${activePage === totalPages ? 'disabled' : ''}`;
        nextLi.innerHTML = `
            <a class="page-link" href="#" onclick="changePage(${activePage + 1}); return false;">
                Next <i class="fas fa-chevron-right"></i>
            </a>
        `;
        paginationControls.appendChild(nextLi);
    }

    function changePage(page) {
        loadVaccinations(page);
        document.getElementById('vaccinationsTable').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // Get status class for badge
    function getStatusClass(status) {
        switch(status) {
            case 'completed':
                return 'status-completed';
            case 'due':
                return 'status-due';
            case 'overdue':
                return 'status-overdue';
            default:
                return 'status-completed';
        }
    }

    // Filter functionality
    function applyFilters() {
        currentPage = 1;
        loadVaccinations(1);
    }

    function clearFilters() {
        document.getElementById('statusFilter').value = '';
        document.getElementById('vaccineFilter').value = '';
        document.getElementById('dateRangeFilter').value = '';
        document.getElementById('vaccinationSearch').value = '';

        currentPage = 1;
        loadVaccinations(1);
    }

    // Save vaccination record
    function saveVaccination() {
        const form = document.getElementById('addVaccinationForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const formData = new FormData(form);

        fetch('/vaccination', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                const modal = bootstrap.Modal.getInstance(document.getElementById('addVaccinationModal'));
                modal.hide();
                form.reset();
                loadVaccinations();
                location.reload(); // Reload to update stats
            }
        })
        .catch(error => {
            console.error('Error saving vaccination:', error);
            alert('Error saving vaccination record. Please try again.');
        });
    }

    // View vaccination details
    function viewVaccination(id) {
        fetch(`/api/vaccinations/${id}`)
            .then(response => response.json())
            .then(data => {
                const statusBadge = `<span class="status-badge ${getStatusClass(data.status)}">${data.status.toUpperCase()}</span>`;
                const details = `
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Animal ID:</strong> ${data.animal_tag}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Animal Name:</strong> ${data.animal_name}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Vaccine Name:</strong> ${data.vaccine_name}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Batch Number:</strong> ${data.batch_number || 'N/A'}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Date Administered:</strong> ${data.date_administered}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Next Due Date:</strong> ${data.next_due_date || 'N/A'}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Veterinarian:</strong> ${data.veterinarian}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Status:</strong> ${statusBadge}
                        </div>
                        <div class="col-12 mb-3">
                            <strong>Notes:</strong><br>${data.notes || 'No notes'}
                        </div>
                    </div>
                `;
                document.getElementById('vaccinationDetails').innerHTML = details;

                const modal = new bootstrap.Modal(document.getElementById('viewVaccinationModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error loading vaccination:', error);
                alert('Error loading vaccination details. Please try again.');
            });
    }

    // Edit vaccination
    function editVaccination(id) {
        fetch(`/api/vaccinations/${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('edit_vaccination_id').value = data.id;
                document.getElementById('edit_animal_id').value = data.animal_id;
                document.getElementById('edit_vaccine_name').value = data.vaccine_name;
                document.getElementById('edit_date_administered').value = data.date_administered;
                document.getElementById('edit_next_due_date').value = data.next_due_date;
                document.getElementById('edit_batch_number').value = data.batch_number;
                document.getElementById('edit_veterinarian').value = data.veterinarian;
                document.getElementById('edit_notes').value = data.notes;

                const modal = new bootstrap.Modal(document.getElementById('editVaccinationModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error loading vaccination:', error);
                alert('Error loading vaccination details. Please try again.');
            });
    }

    // Update vaccination record
    function updateVaccination() {
        const form = document.getElementById('editVaccinationForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const id = document.getElementById('edit_vaccination_id').value;
        const formData = new FormData(form);
        const data = {};
        formData.forEach((value, key) => {
            if (key !== 'vaccination_id') {
                data[key] = value;
            }
        });

        fetch(`/api/vaccinations/${id}`, {
            method: 'PUT',
            body: JSON.stringify(data),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                const modal = bootstrap.Modal.getInstance(document.getElementById('editVaccinationModal'));
                modal.hide();
                loadVaccinations();
                location.reload(); // Reload to update stats
            }
        })
        .catch(error => {
            console.error('Error updating vaccination:', error);
            alert('Error updating vaccination record. Please try again.');
        });
    }

    // Delete vaccination record
    function deleteVaccination(id) {
        if (!confirm('Are you sure you want to delete this vaccination record?')) {
            return;
        }

        fetch(`/api/vaccinations/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                loadVaccinations();
                location.reload(); // Reload to update stats
            }
        })
        .catch(error => {
            console.error('Error deleting vaccination:', error);
            alert('Error deleting vaccination record. Please try again.');
        });
    }

    // Export functionality
    function exportVaccinationData() {
        const params = new URLSearchParams();
        const status = document.getElementById('statusFilter').value;
        const vaccine = document.getElementById('vaccineFilter').value;
        const dateRange = document.getElementById('dateRangeFilter').value;
        const search = document.getElementById('vaccinationSearch').value;

        if (status) params.set('status', status);
        if (vaccine) params.set('vaccine', vaccine);
        if (dateRange) params.set('date_range', dateRange);
        if (search) params.set('search', search);

        window.location.href = '/vaccination/export?' + params.toString();
    }

    function printVaccinationReport() {
        const params = new URLSearchParams();
        const status = document.getElementById('statusFilter').value;
        const vaccine = document.getElementById('vaccineFilter').value;
        const dateRange = document.getElementById('dateRangeFilter').value;
        const search = document.getElementById('vaccinationSearch').value;

        if (status) params.set('status', status);
        if (vaccine) params.set('vaccine', vaccine);
        if (dateRange) params.set('date_range', dateRange);
        if (search) params.set('search', search);

        window.open('/vaccination/print?' + params.toString(), '_blank');
    }

    // Refresh data
    function refreshVaccinationData() {
        currentPage = 1;
        clearFilters();
        loadVaccinations();
    }
</script>
@endpush
