@extends('layouts.app')

@section('title', 'Expenses Management')

@push('styles')
<style>
    .border-left-danger { border-left: 0.25rem solid var(--danger-main) !important; }
    .border-left-warning { border-left: 0.25rem solid var(--warning-main) !important; }
    .border-left-info { border-left: 0.25rem solid var(--info-main) !important; }
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
    <h1 class="h2">Expenses Management</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportExpenses()">
                <i class="fas fa-download me-1"></i>Export
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="printExpenses()">
                <i class="fas fa-print me-1"></i>Print
            </button>
        </div>
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
            <i class="fas fa-plus me-1"></i>Add Expense
        </button>
    </div>
</div>

<!-- Expense Overview Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-vibrant bg-vibrant-danger me-3 shadow-danger">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted uppercase fs-tiny fw-semibold">Today's Expenses</h6>
                        <h3 class="mb-0 fw-bold text-vibrant-danger">Rs. {{ number_format($dailyExpenses, 2) }}</h3>
                    </div>
                </div>
                <small class="text-danger fw-medium"><i class="bx bx-trending-down me-1"></i> Current Day</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-vibrant bg-vibrant-warning me-3 shadow-warning">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted uppercase fs-tiny fw-semibold">Monthly Expenses</h6>
                        <h3 class="mb-0 fw-bold text-vibrant-warning">Rs. {{ number_format($monthlyExpenses, 2) }}</h3>
                    </div>
                </div>
                <small class="text-warning fw-medium"><i class="bx bx-stats me-1"></i> Current Month</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-vibrant bg-vibrant-info me-3 shadow-info">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted uppercase fs-tiny fw-semibold">Yearly Expenses</h6>
                        <h3 class="mb-0 fw-bold text-vibrant-info">Rs. {{ number_format($yearlyExpenses, 2) }}</h3>
                    </div>
                </div>
                <small class="text-info fw-medium"><i class="bx bx-calendar me-1"></i> Annual Total</small>
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
                        <h3 class="mb-0 fw-bold text-vibrant-primary">{{ $totalExpenses }}</h3>
                    </div>
                </div>
                <small class="text-primary fw-medium"><i class="bx bx-file me-1"></i> Financial Logs</small>
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
                    <!-- Category Filter -->
                    <div class="col-lg-3 col-md-6">
                        <label for="categoryFilter" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-tags text-primary me-2"></i>Category
                        </label>
                        <select class="form-select form-select-lg border-2" id="categoryFilter">
                            <option value="">All Categories</option>
                            <option value="Feed">Feed</option>
                            <option value="Medicine">Medicine</option>
                            <option value="Equipment">Equipment</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Utilities">Utilities</option>
                            <option value="Salary">Salary</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <!-- Payment Method Filter -->
                    <div class="col-lg-3 col-md-6">
                        <label for="paymentMethodFilter" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-credit-card text-success me-2"></i>Payment Method
                        </label>
                        <select class="form-select form-select-lg border-2" id="paymentMethodFilter">
                            <option value="">All Methods</option>
                            <option value="cash">Cash</option>
                            <option value="check">Check</option>
                            <option value="card">Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
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

                    <!-- Search Box -->
                    <div class="col-lg-3 col-md-6">
                        <label for="searchExpense" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-search text-warning me-2"></i>Search
                        </label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-white border-2 border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control form-control-lg border-2 border-start-0 ps-0"
                                   id="searchExpense" placeholder="Search description, vendor...">
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

<!-- Expenses Table -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Expense Records</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                        data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow">
                        <a class="dropdown-item" href="#" onclick="refreshExpenseData()">Refresh Data</a>
                        <a class="dropdown-item" href="#" onclick="exportExpenses()">Export Data</a>
                        <a class="dropdown-item" href="#" onclick="printExpenses()">Print Report</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="expensesTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th>Amount (Rs.)</th>
                                <th>Vendor</th>
                                <th>Payment Method</th>
                                <th>Receipt #</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="expensesBody">
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

<!-- Add Expense Modal -->
<div class="modal fade" id="addExpenseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Expense Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addExpenseForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label">Category *</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="Feed">Feed</option>
                                <option value="Medicine">Medicine</option>
                                <option value="Equipment">Equipment</option>
                                <option value="Maintenance">Maintenance</option>
                                <option value="Utilities">Utilities</option>
                                <option value="Salary">Salary</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="amount" class="form-label">Amount (Rs.) *</label>
                            <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0" required placeholder="0.00">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description *</label>
                        <input type="text" class="form-control" id="description" name="description" required placeholder="Brief description of expense">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="expenseDate" class="form-label">Expense Date *</label>
                            <input type="date" class="form-control" id="expenseDate" name="expense_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="paymentMethod" class="form-label">Payment Method *</label>
                            <select class="form-select" id="paymentMethod" name="payment_method" required>
                                <option value="">Select Method</option>
                                <option value="cash">Cash</option>
                                <option value="check">Check</option>
                                <option value="card">Card</option>
                                <option value="bank_transfer">Bank Transfer</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="vendor" class="form-label">Vendor/Supplier</label>
                            <input type="text" class="form-control" id="vendor" name="vendor" placeholder="Vendor name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="receiptNumber" class="form-label">Receipt Number</label>
                            <input type="text" class="form-control" id="receiptNumber" name="receipt_number" placeholder="Receipt/Invoice #">
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
                <button type="button" class="btn btn-primary" onclick="saveExpense()">Save Expense</button>
            </div>
        </div>
    </div>
</div>

<!-- View Expense Modal -->
<div class="modal fade" id="viewExpenseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Expense Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="expenseDetails">
                <!-- Details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Expense Modal -->
<div class="modal fade" id="editExpenseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Expense Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editExpenseForm">
                    @csrf
                    <input type="hidden" id="editExpenseId">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editCategory" class="form-label">Category *</label>
                            <select class="form-select" id="editCategory" name="category" required>
                                <option value="">Select Category</option>
                                <option value="Feed">Feed</option>
                                <option value="Medicine">Medicine</option>
                                <option value="Equipment">Equipment</option>
                                <option value="Maintenance">Maintenance</option>
                                <option value="Utilities">Utilities</option>
                                <option value="Salary">Salary</option>
                                <option value="Other">Other</option>
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
                            <label for="editExpenseDate" class="form-label">Expense Date *</label>
                            <input type="date" class="form-control" id="editExpenseDate" name="expense_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editPaymentMethod" class="form-label">Payment Method *</label>
                            <select class="form-select" id="editPaymentMethod" name="payment_method" required>
                                <option value="">Select Method</option>
                                <option value="cash">Cash</option>
                                <option value="check">Check</option>
                                <option value="card">Card</option>
                                <option value="bank_transfer">Bank Transfer</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editVendor" class="form-label">Vendor/Supplier</label>
                            <input type="text" class="form-control" id="editVendor" name="vendor">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editReceiptNumber" class="form-label">Receipt Number</label>
                            <input type="text" class="form-control" id="editReceiptNumber" name="receipt_number">
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
                <button type="button" class="btn btn-primary" onclick="updateExpense()">Update Expense</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Pagination variables
    let allExpenses = [];
    let filteredExpenses = [];
    let currentPage = 1;
    const recordsPerPage = 20;

    // Load expenses on page load
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('expenseDate').value = today;
        loadExpenses();

        // Add filter event listeners
        document.getElementById('categoryFilter')?.addEventListener('change', applyFilters);
        document.getElementById('paymentMethodFilter')?.addEventListener('change', applyFilters);
        document.getElementById('dateRangeFilter')?.addEventListener('change', applyFilters);
        document.getElementById('searchExpense')?.addEventListener('input', debounce(applyFilters, 500));
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
        loadExpenses(1);
    }

    function loadExpenses(page = 1) {
        const tbody = document.getElementById('expensesBody');
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
        
        const category = document.getElementById('categoryFilter').value;
        const paymentMethod = document.getElementById('paymentMethodFilter').value;
        const dateRange = document.getElementById('dateRangeFilter').value;
        const search = document.getElementById('searchExpense').value;

        if (category) params.set('category', category);
        if (paymentMethod) params.set('payment_method', paymentMethod);
        if (dateRange) params.set('date_range', dateRange);
        if (search) params.set('search', search);

        fetch('/api/expenses/all?' + params.toString())
            .then(response => response.json())
            .then(response => {
                displayExpenses(response.data);
                updatePaginationInfo(response.pagination.from || 0, response.pagination.to || 0, response.pagination.total);
                renderPaginationControls(response.pagination.last_page, response.pagination.current_page);
                currentPage = response.pagination.current_page;
            })
            .catch(error => {
                console.error('Error loading expenses:', error);
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center text-danger py-5">Error loading expenses</td>
                    </tr>
                `;
            });
    }

    function displayExpenses(records) {
        const tbody = document.getElementById('expensesBody');
        tbody.innerHTML = '';

        if (!records || records.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-5">No expense records found</td>
                </tr>
            `;
            return;
        }

        records.forEach(expense => {
            const paymentBadge = getPaymentMethodBadge(expense.payment_method);

            const row = `
                <tr>
                    <td>${formatDate(expense.expense_date)}</td>
                    <td><span class="badge bg-secondary">${expense.category}</span></td>
                    <td>${expense.description}</td>
                    <td class="text-danger fw-bold">Rs. ${parseFloat(expense.amount).toFixed(2)}</td>
                    <td>${expense.vendor || '-'}</td>
                    <td>${paymentBadge}</td>
                    <td>${expense.receipt_number || '-'}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-sm btn-primary" onclick="viewExpense(${expense.id})" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-info" onclick="editExpense(${expense.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteExpense(${expense.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    }

    function getPaymentMethodBadge(method) {
        const badges = {
            'cash': '<span class="badge bg-success">Cash</span>',
            'check': '<span class="badge bg-info">Check</span>',
            'card': '<span class="badge bg-primary">Card</span>',
            'bank_transfer': '<span class="badge bg-warning">Bank Transfer</span>'
        };
        return badges[method] || method;
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
        loadExpenses(page);
        document.getElementById('expensesTable').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // Filter functionality
    document.getElementById('categoryFilter')?.addEventListener('change', applyFilters);
    document.getElementById('paymentMethodFilter')?.addEventListener('change', applyFilters);
    document.getElementById('dateRangeFilter')?.addEventListener('change', applyFilters);
    document.getElementById('searchExpense')?.addEventListener('input', applyFilters);

    function applyFilters() {
        currentPage = 1;
        loadExpenses(1);
    }

    function clearFilters() {
        document.getElementById('categoryFilter').value = '';
        document.getElementById('paymentMethodFilter').value = '';
        document.getElementById('dateRangeFilter').value = '';
        document.getElementById('searchExpense').value = '';

        currentPage = 1;
        loadExpenses(1);
    }

    function refreshExpenseData() {
        currentPage = 1;
        loadExpenses();
    }

    function exportExpenses() {
        const params = new URLSearchParams();
        const category = document.getElementById('categoryFilter').value;
        const paymentMethod = document.getElementById('paymentMethodFilter').value;
        const dateRange = document.getElementById('dateRangeFilter').value;
        const search = document.getElementById('searchExpense').value;

        if (category) params.set('category', category);
        if (paymentMethod) params.set('payment_method', paymentMethod);
        if (dateRange) params.set('date_range', dateRange);
        if (search) params.set('search', search);

        window.location.href = '/expenses/export?' + params.toString();
    }

    function printExpenses() {
        const params = new URLSearchParams();
        const category = document.getElementById('categoryFilter').value;
        const paymentMethod = document.getElementById('paymentMethodFilter').value;
        const dateRange = document.getElementById('dateRangeFilter').value;
        const search = document.getElementById('searchExpense').value;

        if (category) params.set('category', category);
        if (paymentMethod) params.set('payment_method', paymentMethod);
        if (dateRange) params.set('date_range', dateRange);
        if (search) params.set('search', search);

        window.open('/expenses/print?' + params.toString(), '_blank');
    }

    function saveExpense() {
        const form = document.getElementById('addExpenseForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const formData = new FormData(form);
        const data = Object.fromEntries(formData);

        fetch('/api/expenses', {
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
                alert('Expense saved successfully!');
                const modal = bootstrap.Modal.getInstance(document.getElementById('addExpenseModal'));
                modal.hide();
                form.reset();
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to save expense'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving expense. Please try again.');
        });
    }

    function viewExpense(id) {
        fetch(`/api/expenses/${id}`)
            .then(response => response.json())
            .then(expense => {
                const details = `
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Category:</strong> <span class="badge bg-secondary">${expense.category}</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Amount:</strong> <span class="text-danger fw-bold">Rs. ${parseFloat(expense.amount).toFixed(2)}</span>
                        </div>
                        <div class="col-12 mb-3">
                            <strong>Description:</strong> ${expense.description}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Date:</strong> ${formatDate(expense.expense_date)}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Payment Method:</strong> ${getPaymentMethodBadge(expense.payment_method)}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Vendor:</strong> ${expense.vendor || '-'}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Receipt Number:</strong> ${expense.receipt_number || '-'}
                        </div>
                        <div class="col-12 mb-3">
                            <strong>Notes:</strong><br>${expense.notes || '-'}
                        </div>
                    </div>
                `;
                document.getElementById('expenseDetails').innerHTML = details;
                const modal = new bootstrap.Modal(document.getElementById('viewExpenseModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading expense details.');
            });
    }

    function editExpense(id) {
        fetch(`/api/expenses/${id}`)
            .then(response => response.json())
            .then(expense => {
                document.getElementById('editExpenseId').value = expense.id;
                document.getElementById('editCategory').value = expense.category;
                document.getElementById('editAmount').value = expense.amount;
                document.getElementById('editDescription').value = expense.description;
                document.getElementById('editExpenseDate').value = expense.expense_date.split('T')[0];
                document.getElementById('editPaymentMethod').value = expense.payment_method;
                document.getElementById('editVendor').value = expense.vendor || '';
                document.getElementById('editReceiptNumber').value = expense.receipt_number || '';
                document.getElementById('editNotes').value = expense.notes || '';

                const modal = new bootstrap.Modal(document.getElementById('editExpenseModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading expense.');
            });
    }

    function updateExpense() {
        const form = document.getElementById('editExpenseForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const expenseId = document.getElementById('editExpenseId').value;
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);

        fetch(`/api/expenses/${expenseId}`, {
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
                alert('Expense updated successfully!');
                const modal = bootstrap.Modal.getInstance(document.getElementById('editExpenseModal'));
                modal.hide();
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to update expense'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating expense.');
        });
    }

    function deleteExpense(id) {
        if (confirm('Are you sure you want to delete this expense record? This action cannot be undone.')) {
            fetch(`/api/expenses/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Expense deleted successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to delete expense'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting expense.');
            });
        }
    }
</script>
@endpush
