@extends('layouts.app')

@section('title', 'Income Management')

@push('styles')
<style>
    .border-left-success { border-left: 0.25rem solid var(--success-main) !important; }
    .border-left-info { border-left: 0.25rem solid var(--info-main) !important; }
    .border-left-warning { border-left: 0.25rem solid var(--warning-main) !important; }
    .border-left-primary { border-left: 0.25rem solid var(--primary-600) !important; }

    /* Action buttons styling */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        justify-content: center;
        align-items: center;
        flex-wrap: nowrap;
    }

    .action-buttons .btn {
        min-width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem;
        border-radius: 0.375rem;
        font-size: 1.1rem;
    }

    .action-buttons .btn i {
        font-size: 1.1rem;
    }

    @media (max-width: 768px) {
        .action-buttons {
            gap: 0.3rem;
        }

        .action-buttons .btn {
            min-width: 35px;
            height: 35px;
            font-size: 0.95rem;
        }
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

    .border-2 {
        border-width: 2px !important;
    }

    .fw-semibold {
        font-weight: 600;
    }

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
</style>
@endpush

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Income Management</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportIncome()">
                <i class="fas fa-download me-1"></i>Export
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="printIncome()">
                <i class="fas fa-print me-1"></i>Print
            </button>
        </div>
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addIncomeModal">
            <i class="fas fa-plus me-1"></i>Add Income
        </button>
    </div>
</div>

<!-- Income Overview Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-vibrant bg-vibrant-success me-3 shadow-success">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted uppercase fs-tiny fw-semibold">Today's Income</h6>
                        <h3 class="mb-0 fw-bold text-vibrant-success">Rs. {{ number_format($dailyIncome, 2) }}</h3>
                    </div>
                </div>
                <small class="text-success fw-medium"><i class="bx bx-trending-up me-1"></i> Daily Performance</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-vibrant bg-vibrant-info me-3 shadow-info">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted uppercase fs-tiny fw-semibold">Monthly Income</h6>
                        <h3 class="mb-0 fw-bold text-vibrant-info">Rs. {{ number_format($monthlyIncome, 2) }}</h3>
                    </div>
                </div>
                <small class="text-info fw-medium"><i class="bx bx-calendar me-1"></i> Current Month</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-vibrant bg-vibrant-warning me-3 shadow-warning">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted uppercase fs-tiny fw-semibold">Yearly Income</h6>
                        <h3 class="mb-0 fw-bold text-vibrant-warning">Rs. {{ number_format($yearlyIncome, 2) }}</h3>
                    </div>
                </div>
                <small class="text-warning fw-medium"><i class="bx bx-line-chart me-1"></i> Annual Performance</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-vibrant bg-vibrant-primary me-3 shadow-primary">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted uppercase fs-tiny fw-semibold">Total Records</h6>
                        <h3 class="mb-0 fw-bold text-vibrant-primary">{{ $totalIncome }}</h3>
                    </div>
                </div>
                <small class="text-primary fw-medium"><i class="bx bx-list-check me-1"></i> Total Transactions</small>
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
                    <!-- Source Filter -->
                    <div class="col-lg-3 col-md-6">
                        <label for="sourceFilter" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-tags text-primary me-2"></i>Income Source
                        </label>
                        <select class="form-select form-select-lg border-2" id="sourceFilter">
                            <option value="">All Sources</option>
                            <option value="milk_sales">Milk Sales</option>
                            <option value="animal_sales">Animal Sales</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <!-- Date Range Filter -->
                    <div class="col-lg-3 col-md-6">
                        <label for="dateRangeFilter" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-calendar-alt text-info me-2"></i>Date Range
                        </label>
                        <select class="form-select form-select-lg border-2" id="dateRangeFilter">
                            <option value="">All Time</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="year">This Year</option>
                        </select>
                    </div>

                    <!-- Amount Range Filter -->
                    <div class="col-lg-3 col-md-6">
                        <label for="amountRangeFilter" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-money-bill-wave text-success me-2"></i>Amount Range
                        </label>
                        <select class="form-select form-select-lg border-2" id="amountRangeFilter">
                            <option value="">All Amounts</option>
                            <option value="0-10000">Rs. 0 - 10,000</option>
                            <option value="10000-50000">Rs. 10,000 - 50,000</option>
                            <option value="50000-100000">Rs. 50,000 - 100,000</option>
                            <option value="100000+">Rs. 100,000+</option>
                        </select>
                    </div>

                    <!-- Search Box -->
                    <div class="col-lg-3 col-md-6">
                        <label for="searchIncome" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-search text-warning me-2"></i>Search
                        </label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-white border-2 border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control form-control-lg border-2 border-start-0 ps-0"
                                   id="searchIncome" placeholder="Search description, customer...">
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

<!-- Income Table -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Income Records</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                        data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow">
                        <a class="dropdown-item" href="#" onclick="refreshIncomeData()">Refresh Data</a>
                        <a class="dropdown-item" href="#" onclick="exportIncome()">Export Data</a>
                        <a class="dropdown-item" href="#" onclick="printIncome()">Print Report</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="incomeTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Source</th>
                                <th>Description</th>
                                <th>Amount (Rs.)</th>
                                <th>Customer</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="incomeBody">
                            <tr>
                                <td colspan="8" class="text-center">
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

<!-- Add Income Modal -->
<div class="modal fade" id="addIncomeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Income Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addIncomeForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="source" class="form-label">Income Source *</label>
                            <select class="form-select" id="source" name="source" required>
                                <option value="">Select Source</option>
                                <option value="milk_sales">Milk Sales</option>
                                <option value="animal_sales">Animal Sales</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="amount" class="form-label">Amount (Rs.) *</label>
                            <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0" required placeholder="0.00">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description *</label>
                        <input type="text" class="form-control" id="description" name="description" required placeholder="Brief description of income">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="incomeDate" class="form-label">Income Date *</label>
                            <input type="date" class="form-control" id="incomeDate" name="income_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="customer" class="form-label">Customer/Buyer</label>
                            <input type="text" class="form-control" id="customer" name="customer" placeholder="Customer name">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" step="0.01" min="0" placeholder="0.00">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="unit" class="form-label">Unit</label>
                            <input type="text" class="form-control" id="unit" name="unit" placeholder="e.g., Liters, Kg, Head">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Additional Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Any additional notes..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveIncome()">Save Income</button>
            </div>
        </div>
    </div>
</div>

<!-- View Income Modal -->
<div class="modal fade" id="viewIncomeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Income Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="incomeDetails">
                <!-- Details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Income Modal -->
<div class="modal fade" id="editIncomeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Income Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editIncomeForm">
                    @csrf
                    <input type="hidden" id="editIncomeId">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editSource" class="form-label">Income Source *</label>
                            <select class="form-select" id="editSource" name="source" required>
                                <option value="">Select Source</option>
                                <option value="milk_sales">Milk Sales</option>
                                <option value="animal_sales">Animal Sales</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editAmount" class="form-label">Amount (Rs.) *</label>
                            <input type="number" class="form-control" id="editAmount" name="amount" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="editDescription" class="form-label">Description *</label>
                        <input type="text" class="form-control" id="editDescription" name="description" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editIncomeDate" class="form-label">Income Date *</label>
                            <input type="date" class="form-control" id="editIncomeDate" name="income_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editCustomer" class="form-label">Customer/Buyer</label>
                            <input type="text" class="form-control" id="editCustomer" name="customer">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editQuantity" class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="editQuantity" name="quantity" step="0.01" min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editUnit" class="form-label">Unit</label>
                            <input type="text" class="form-control" id="editUnit" name="unit">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="editNotes" class="form-label">Additional Notes</label>
                        <textarea class="form-control" id="editNotes" name="notes" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updateIncome()">Update Income</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Pagination variables
    let allIncome = [];
    let filteredIncome = [];
    let currentPage = 1;
    const recordsPerPage = 20;

    // Load income on page load
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('incomeDate').value = today;
        loadIncome();

        // Add filter event listeners
        document.getElementById('sourceFilter')?.addEventListener('change', applyFilters);
        document.getElementById('dateRangeFilter')?.addEventListener('change', applyFilters);
        document.getElementById('amountRangeFilter')?.addEventListener('change', applyFilters);
        document.getElementById('searchIncome')?.addEventListener('input', debounce(applyFilters, 500));
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
        loadIncome(1);
    }

    function loadIncome(page = 1) {
        const tbody = document.getElementById('incomeBody');
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </td>
            </tr>
        `;

        const params = new URLSearchParams();
        params.set('page', page);
        
        const source = document.getElementById('sourceFilter').value;
        const dateRange = document.getElementById('dateRangeFilter').value;
        const amountRange = document.getElementById('amountRangeFilter').value;
        const search = document.getElementById('searchIncome').value;

        if (source) params.set('source', source);
        if (dateRange) params.set('date_range', dateRange);
        if (amountRange) params.set('amount_range', amountRange);
        if (search) params.set('search', search);

        fetch('/api/income/all?' + params.toString())
            .then(response => response.json())
            .then(response => {
                displayIncome(response.data);
                updatePaginationInfo(response.pagination.from || 0, response.pagination.to || 0, response.pagination.total);
                renderPaginationControls(response.pagination.last_page, response.pagination.current_page);
                currentPage = response.pagination.current_page;
            })
            .catch(error => {
                console.error('Error loading income:', error);
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center text-danger py-5">Error loading income records</td>
                    </tr>
                `;
            });
    }

    function displayIncome(records) {
        const tbody = document.getElementById('incomeBody');
        tbody.innerHTML = '';

        if (!records || records.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-5">No income records found</td>
                </tr>
            `;
            return;
        }

        records.forEach(income => {
            const sourceBadge = getSourceBadge(income.source);

            const row = `
                <tr>
                    <td>${formatDate(income.income_date)}</td>
                    <td>${sourceBadge}</td>
                    <td>${income.description}</td>
                    <td class="text-success fw-bold">Rs. ${parseFloat(income.amount).toFixed(2)}</td>
                    <td>${income.customer || '-'}</td>
                    <td>${income.quantity ? parseFloat(income.quantity).toFixed(2) : '-'}</td>
                    <td>${income.unit || '-'}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-sm btn-primary" onclick="viewIncome(${income.id})" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-info" onclick="editIncome(${income.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteIncome(${income.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    }

    function getSourceBadge(source) {
        const badges = {
            'milk_sales': '<span class="badge bg-primary">Milk Sales</span>',
            'animal_sales': '<span class="badge bg-success">Animal Sales</span>',
            'other': '<span class="badge bg-info">Other</span>'
        };
        return badges[source] || source;
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
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

        for (let i = startPage; i <= endPage; i++) {
            const pageLi = document.createElement('li');
            pageLi.className = `page-item ${i === activePage ? 'active' : ''}`;
            pageLi.innerHTML = `<a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a>`;
            paginationControls.appendChild(pageLi);
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
        loadIncome(page);
        document.getElementById('incomeTable').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // Filter functionality
    document.getElementById('sourceFilter')?.addEventListener('change', applyFilters);
    document.getElementById('dateRangeFilter')?.addEventListener('change', applyFilters);
    document.getElementById('amountRangeFilter')?.addEventListener('change', applyFilters);
    document.getElementById('searchIncome')?.addEventListener('input', applyFilters);

    function applyFilters() {
        currentPage = 1;
        loadIncome(1);
    }

    function clearFilters() {
        document.getElementById('sourceFilter').value = '';
        document.getElementById('dateRangeFilter').value = '';
        document.getElementById('amountRangeFilter').value = '';
        document.getElementById('searchIncome').value = '';

        currentPage = 1;
        loadIncome(1);
    }

    function refreshIncomeData() {
        currentPage = 1;
        loadIncome();
    }

    function exportIncome() {
        const params = new URLSearchParams();
        const source = document.getElementById('sourceFilter').value;
        const dateRange = document.getElementById('dateRangeFilter').value;
        const search = document.getElementById('searchIncome').value;

        if (source) params.set('source', source);
        if (dateRange) params.set('date_range', dateRange);
        if (search) params.set('search', search);

        window.location.href = '/income/export?' + params.toString();
    }

    function printIncome() {
        const params = new URLSearchParams();
        const source = document.getElementById('sourceFilter').value;
        const dateRange = document.getElementById('dateRangeFilter').value;
        const search = document.getElementById('searchIncome').value;

        if (source) params.set('source', source);
        if (dateRange) params.set('date_range', dateRange);
        if (search) params.set('search', search);

        window.open('/income/print?' + params.toString(), '_blank');
    }

    function saveIncome() {
        const form = document.getElementById('addIncomeForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const formData = new FormData(form);
        const data = Object.fromEntries(formData);

        fetch('/api/income', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Income saved successfully!');
                const modal = bootstrap.Modal.getInstance(document.getElementById('addIncomeModal'));
                modal.hide();
                form.reset();
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to save income'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving income. Please try again.');
        });
    }

    function viewIncome(id) {
        fetch(`/api/income/${id}`)
            .then(response => response.json())
            .then(income => {
                const details = `
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Source:</strong> ${getSourceBadge(income.source)}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Amount:</strong> <span class="text-success fw-bold">Rs. ${parseFloat(income.amount).toFixed(2)}</span>
                        </div>
                        <div class="col-12 mb-3">
                            <strong>Description:</strong> ${income.description}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Date:</strong> ${formatDate(income.income_date)}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Customer:</strong> ${income.customer || '-'}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Quantity:</strong> ${income.quantity ? parseFloat(income.quantity).toFixed(2) : '-'}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Unit:</strong> ${income.unit || '-'}
                        </div>
                        <div class="col-12 mb-3">
                            <strong>Notes:</strong><br>${income.notes || '-'}
                        </div>
                    </div>
                `;
                document.getElementById('incomeDetails').innerHTML = details;
                const modal = new bootstrap.Modal(document.getElementById('viewIncomeModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading income details.');
            });
    }

    function editIncome(id) {
        fetch(`/api/income/${id}`)
            .then(response => response.json())
            .then(income => {
                document.getElementById('editIncomeId').value = income.id;
                document.getElementById('editSource').value = income.source;
                document.getElementById('editAmount').value = income.amount;
                document.getElementById('editDescription').value = income.description;
                document.getElementById('editIncomeDate').value = income.income_date.split('T')[0];
                document.getElementById('editCustomer').value = income.customer || '';
                document.getElementById('editQuantity').value = income.quantity || '';
                document.getElementById('editUnit').value = income.unit || '';
                document.getElementById('editNotes').value = income.notes || '';

                const modal = new bootstrap.Modal(document.getElementById('editIncomeModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading income.');
            });
    }

    function updateIncome() {
        const form = document.getElementById('editIncomeForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const incomeId = document.getElementById('editIncomeId').value;
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);

        fetch(`/api/income/${incomeId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Income updated successfully!');
                const modal = bootstrap.Modal.getInstance(document.getElementById('editIncomeModal'));
                modal.hide();
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to update income'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating income.');
        });
    }

    function deleteIncome(id) {
        if (confirm('Are you sure you want to delete this income record? This action cannot be undone.')) {
            fetch(`/api/income/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Income deleted successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to delete income'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting income.');
            });
        }
    }
</script>
@endpush
