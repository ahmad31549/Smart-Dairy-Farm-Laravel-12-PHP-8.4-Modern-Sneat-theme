@extends('layouts.app')

@section('title', 'Payroll Management')

@push('styles')
<style>
    .border-left-success { border-left: 0.25rem solid var(--success-main) !important; }
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
    <h1 class="h2">Payroll Management</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportPayroll()">
                <i class="fas fa-download me-1"></i>Export
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="printPayroll()">
                <i class="fas fa-print me-1"></i>Print
            </button>
        </div>
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addPayrollModal">
            <i class="fas fa-plus me-1"></i>Process Payroll
        </button>
    </div>
</div>

<!-- Payroll Overview Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-vibrant bg-vibrant-primary me-3 shadow-primary">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted uppercase fs-tiny fw-semibold">Monthly Payroll</h6>
                        <h3 class="mb-0 fw-bold text-vibrant-primary">Rs. {{ number_format($totalPayroll, 2) }}</h3>
                    </div>
                </div>
                <small class="text-primary fw-medium"><i class="bx bx-stats me-1"></i> Current Month</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-vibrant bg-vibrant-warning me-3 shadow-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted uppercase fs-tiny fw-semibold">Pending</h6>
                        <h3 class="mb-0 fw-bold text-vibrant-warning">{{ $pendingCount }}</h3>
                    </div>
                </div>
                <small class="text-warning fw-medium"><i class="bx bx-time me-1"></i> Awaiting Action</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-vibrant bg-vibrant-info me-3 shadow-info">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted uppercase fs-tiny fw-semibold">Processed</h6>
                        <h3 class="mb-0 fw-bold text-vibrant-info">{{ $processedCount }}</h3>
                    </div>
                </div>
                <small class="text-info fw-medium"><i class="bx bx-check-double me-1"></i> Ready for Payment</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-vibrant bg-vibrant-success me-3 shadow-success">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted uppercase fs-tiny fw-semibold">Paid</h6>
                        <h3 class="mb-0 fw-bold text-vibrant-success">{{ $paidCount }}</h3>
                    </div>
                </div>
                <small class="text-success fw-medium"><i class="bx bx-check-circle me-1"></i> Salary Disbursed</small>
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
                    <!-- Month Filter -->
                    <div class="col-lg-3 col-md-6">
                        <label for="monthFilter" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-calendar text-primary me-2"></i>Payroll Month
                        </label>
                        <select class="form-select form-select-lg border-2" id="monthFilter">
                            <option value="">All Months</option>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ date('Y-m', mktime(0, 0, 0, $m, 1)) }}" {{ $m == date('n') ? 'selected' : '' }}>
                                    {{ date('F Y', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="col-lg-3 col-md-6">
                        <label for="statusFilter" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-info-circle text-success me-2"></i>Payment Status
                        </label>
                        <select class="form-select form-select-lg border-2" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="processed">Processed</option>
                            <option value="paid">Paid</option>
                        </select>
                    </div>

                    <!-- Employee Filter -->
                    <div class="col-lg-3 col-md-6">
                        <label for="employeeFilter" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-user text-info me-2"></i>Employee
                        </label>
                        <select class="form-select form-select-lg border-2" id="employeeFilter">
                            <option value="">All Employees</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->employee_id }} - {{ $emp->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Search Box -->
                    <div class="col-lg-3 col-md-6">
                        <label for="searchPayroll" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-search text-warning me-2"></i>Search
                        </label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-white border-2 border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control form-control-lg border-2 border-start-0 ps-0"
                                   id="searchPayroll" placeholder="Search employee...">
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

<!-- Payroll Table -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Payroll Records</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                        data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow">
                        <a class="dropdown-item" href="#" onclick="refreshPayrollData()">Refresh Data</a>
                        <a class="dropdown-item" href="#" onclick="exportPayroll()">Export Data</a>
                        <a class="dropdown-item" href="#" onclick="printPayroll()">Print Report</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="payrollTable">
                        <thead>
                            <tr>
                                <th>Employee ID</th>
                                <th>Name</th>
                                <th>Month</th>
                                <th>Basic Salary</th>
                                <th>Overtime</th>
                                <th>Bonus</th>
                                <th>Deductions</th>
                                <th>Net Salary</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="payrollBody">
                            <tr>
                                <td colspan="10" class="text-center">
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

<!-- Add Payroll Modal -->
<div class="modal fade" id="addPayrollModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Process Payroll</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addPayrollForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="employeeId" class="form-label">Employee *</label>
                            <select class="form-select" id="employeeId" name="employee_id" required onchange="loadEmployeeSalary(this.value)">
                                <option value="">Select Employee</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" data-salary="{{ $emp->salary }}">
                                        {{ $emp->employee_id }} - {{ $emp->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="payrollMonth" class="form-label">Payroll Month *</label>
                            <input type="month" class="form-control" id="payrollMonth" name="payroll_month" required value="{{ date('Y-m') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="basicSalary" class="form-label">Basic Salary (Rs.) *</label>
                            <input type="number" class="form-control" id="basicSalary" name="basic_salary" step="0.01" min="0" required placeholder="0.00" onchange="calculateNetSalary()">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="overtimeHours" class="form-label">Overtime Hours</label>
                            <input type="number" class="form-control" id="overtimeHours" name="overtime_hours" step="0.01" min="0" placeholder="0.00" onchange="calculateOvertimeAmount()">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="overtimeAmount" class="form-label">Overtime Amount (Rs.)</label>
                            <input type="number" class="form-control" id="overtimeAmount" name="overtime_amount" step="0.01" min="0" placeholder="0.00" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="bonus" class="form-label">Bonus (Rs.)</label>
                            <input type="number" class="form-control" id="bonus" name="bonus" step="0.01" min="0" placeholder="0.00" onchange="calculateNetSalary()">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="deductions" class="form-label">Deductions (Rs.)</label>
                            <input type="number" class="form-control" id="deductions" name="deductions" step="0.01" min="0" placeholder="0.00" onchange="calculateNetSalary()">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="netSalary" class="form-label">Net Salary (Rs.) *</label>
                            <input type="number" class="form-control bg-light" id="netSalary" name="net_salary" step="0.01" required readonly placeholder="0.00">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="pending">Pending</option>
                                <option value="processed">Processed</option>
                                <option value="paid">Paid</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="paymentDate" class="form-label">Payment Date</label>
                            <input type="date" class="form-control" id="paymentDate" name="payment_date">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Additional notes..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="savePayroll()">Process Payroll</button>
            </div>
        </div>
    </div>
</div>

<!-- View Payroll Modal -->
<div class="modal fade" id="viewPayrollModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payroll Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="payrollDetails">
                <!-- Details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="printPayslip()">
                    <i class="fas fa-print me-1"></i>Print Payslip
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Payroll Modal -->
<div class="modal fade" id="editPayrollModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Payroll</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editPayrollForm">
                    @csrf
                    <input type="hidden" id="editPayrollId">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editEmployeeId" class="form-label">Employee *</label>
                            <select class="form-select" id="editEmployeeId" name="employee_id" required disabled>
                                <option value="">Select Employee</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->employee_id }} - {{ $emp->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editPayrollMonth" class="form-label">Payroll Month *</label>
                            <input type="month" class="form-control" id="editPayrollMonth" name="payroll_month" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editBasicSalary" class="form-label">Basic Salary (Rs.) *</label>
                            <input type="number" class="form-control" id="editBasicSalary" name="basic_salary" step="0.01" min="0" required onchange="calculateEditNetSalary()">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editOvertimeHours" class="form-label">Overtime Hours</label>
                            <input type="number" class="form-control" id="editOvertimeHours" name="overtime_hours" step="0.01" min="0" onchange="calculateEditOvertimeAmount()">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editOvertimeAmount" class="form-label">Overtime Amount (Rs.)</label>
                            <input type="number" class="form-control" id="editOvertimeAmount" name="overtime_amount" step="0.01" min="0" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editBonus" class="form-label">Bonus (Rs.)</label>
                            <input type="number" class="form-control" id="editBonus" name="bonus" step="0.01" min="0" onchange="calculateEditNetSalary()">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editDeductions" class="form-label">Deductions (Rs.)</label>
                            <input type="number" class="form-control" id="editDeductions" name="deductions" step="0.01" min="0" onchange="calculateEditNetSalary()">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editNetSalary" class="form-label">Net Salary (Rs.) *</label>
                            <input type="number" class="form-control bg-light" id="editNetSalary" name="net_salary" step="0.01" required readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editStatus" class="form-label">Status *</label>
                            <select class="form-select" id="editStatus" name="status" required>
                                <option value="pending">Pending</option>
                                <option value="processed">Processed</option>
                                <option value="paid">Paid</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editPaymentDate" class="form-label">Payment Date</label>
                            <input type="date" class="form-control" id="editPaymentDate" name="payment_date">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="editNotes" class="form-label">Notes</label>
                        <textarea class="form-control" id="editNotes" name="notes" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updatePayroll()">Update Payroll</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Pagination variables
    let allPayroll = [];
    let filteredPayroll = [];
    let currentPage = 1;
    const recordsPerPage = 20;
    const overtimeRate = 200; // Rs. per hour

    // Load payroll on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadPayroll();
    });

    function loadPayroll() {
        const tbody = document.getElementById('payrollBody');
        tbody.innerHTML = `
            <tr>
                <td colspan="10" class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </td>
            </tr>
        `;

        fetch('/api/payroll')
            .then(response => response.json())
            .then(payroll => {
                allPayroll = payroll;
                filteredPayroll = payroll;
                currentPage = 1;
                displayPayroll();
            })
            .catch(error => {
                console.error('Error loading payroll:', error);
                tbody.innerHTML = `
                    <tr>
                        <td colspan="10" class="text-center text-danger">Error loading payroll records</td>
                    </tr>
                `;
            });
    }

    function displayPayroll() {
        const tbody = document.getElementById('payrollBody');
        tbody.innerHTML = '';

        if (filteredPayroll.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="10" class="text-center">No payroll records found</td>
                </tr>
            `;
            updatePaginationInfo(0, 0, 0);
            renderPaginationControls(0);
            return;
        }

        // Calculate pagination
        const totalRecords = filteredPayroll.length;
        const totalPages = Math.ceil(totalRecords / recordsPerPage);
        const startIndex = (currentPage - 1) * recordsPerPage;
        const endIndex = Math.min(startIndex + recordsPerPage, totalRecords);
        const recordsToDisplay = filteredPayroll.slice(startIndex, endIndex);

        // Display records
        recordsToDisplay.forEach(payroll => {
            const employee = payroll.employee;
            const statusBadge = getStatusBadge(payroll.status);

            const row = `
                <tr>
                    <td>${employee.employee_id}</td>
                    <td>${employee.name}</td>
                    <td>${formatMonth(payroll.payroll_month)}</td>
                    <td>Rs. ${parseFloat(payroll.basic_salary).toFixed(2)}</td>
                    <td>Rs. ${parseFloat(payroll.overtime_amount || 0).toFixed(2)}</td>
                    <td>Rs. ${parseFloat(payroll.bonus || 0).toFixed(2)}</td>
                    <td>Rs. ${parseFloat(payroll.deductions || 0).toFixed(2)}</td>
                    <td class="fw-bold text-success">Rs. ${parseFloat(payroll.net_salary).toFixed(2)}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-sm btn-primary" onclick="viewPayroll(${payroll.id})" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-info" onclick="editPayroll(${payroll.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deletePayroll(${payroll.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            tbody.innerHTML += row;
        });

        // Update pagination info and controls
        updatePaginationInfo(startIndex + 1, endIndex, totalRecords);
        renderPaginationControls(totalPages);
    }

    function getStatusBadge(status) {
        const badges = {
            'pending': '<span class="badge bg-warning">Pending</span>',
            'processed': '<span class="badge bg-info">Processed</span>',
            'paid': '<span class="badge bg-success">Paid</span>'
        };
        return badges[status] || status;
    }

    function formatMonth(monthString) {
        const date = new Date(monthString + '-01');
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long' });
    }

    function updatePaginationInfo(from, to, total) {
        document.getElementById('showingFrom').textContent = from;
        document.getElementById('showingTo').textContent = to;
        document.getElementById('totalRecords').textContent = total;
    }

    function renderPaginationControls(totalPages) {
        const paginationControls = document.getElementById('paginationControls');
        paginationControls.innerHTML = '';

        if (totalPages <= 1) {
            return;
        }

        // Previous button
        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
        prevLi.innerHTML = `
            <a class="page-link" href="#" onclick="changePage(${currentPage - 1}); return false;">
                <i class="fas fa-chevron-left"></i> Previous
            </a>
        `;
        paginationControls.appendChild(prevLi);

        // Page numbers
        const maxVisiblePages = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

        if (endPage - startPage < maxVisiblePages - 1) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }

        for (let i = startPage; i <= endPage; i++) {
            const pageLi = document.createElement('li');
            pageLi.className = `page-item ${i === currentPage ? 'active' : ''}`;
            pageLi.innerHTML = `<a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a>`;
            paginationControls.appendChild(pageLi);
        }

        // Next button
        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
        nextLi.innerHTML = `
            <a class="page-link" href="#" onclick="changePage(${currentPage + 1}); return false;">
                Next <i class="fas fa-chevron-right"></i>
            </a>
        `;
        paginationControls.appendChild(nextLi);
    }

    function changePage(page) {
        const totalPages = Math.ceil(filteredPayroll.length / recordsPerPage);
        if (page < 1 || page > totalPages) {
            return;
        }
        currentPage = page;
        displayPayroll();
        document.getElementById('payrollTable').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // Filter functionality
    document.getElementById('monthFilter')?.addEventListener('change', applyFilters);
    document.getElementById('statusFilter')?.addEventListener('change', applyFilters);
    document.getElementById('employeeFilter')?.addEventListener('change', applyFilters);
    document.getElementById('searchPayroll')?.addEventListener('input', applyFilters);

    function applyFilters() {
        const monthFilter = document.getElementById('monthFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;
        const employeeFilter = document.getElementById('employeeFilter').value;
        const searchTerm = document.getElementById('searchPayroll').value.toLowerCase();

        filteredPayroll = allPayroll.filter(payroll => {
            const employee = payroll.employee;

            // Month filter
            const matchesMonth = !monthFilter || payroll.payroll_month === monthFilter;

            // Status filter
            const matchesStatus = !statusFilter || payroll.status === statusFilter;

            // Employee filter
            const matchesEmployee = !employeeFilter || payroll.employee_id == employeeFilter;

            // Search filter
            const matchesSearch = !searchTerm ||
                employee.employee_id.toLowerCase().includes(searchTerm) ||
                employee.name.toLowerCase().includes(searchTerm);

            return matchesMonth && matchesStatus && matchesEmployee && matchesSearch;
        });

        currentPage = 1;
        displayPayroll();
    }

    function clearFilters() {
        document.getElementById('monthFilter').value = '';
        document.getElementById('statusFilter').value = '';
        document.getElementById('employeeFilter').value = '';
        document.getElementById('searchPayroll').value = '';

        filteredPayroll = allPayroll;
        currentPage = 1;
        displayPayroll();
    }

    function refreshPayrollData() {
        currentPage = 1;
        loadPayroll();
    }

    function exportPayroll() {
        const params = new URLSearchParams();
        const month = document.getElementById('monthFilter').value;
        const status = document.getElementById('statusFilter').value;
        const employee = document.getElementById('employeeFilter').value;
        const search = document.getElementById('searchPayroll').value;

        if (month) params.set('month', month);
        if (status) params.set('status', status);
        if (employee) params.set('employee_id', employee);
        if (search) params.set('search', search);

        window.location.href = '/payroll/export?' + params.toString();
    }

    function printPayroll() {
        const params = new URLSearchParams();
        const month = document.getElementById('monthFilter').value;
        const status = document.getElementById('statusFilter').value;
        const employee = document.getElementById('employeeFilter').value;
        const search = document.getElementById('searchPayroll').value;

        if (month) params.set('month', month);
        if (status) params.set('status', status);
        if (employee) params.set('employee_id', employee);
        if (search) params.set('search', search);

        window.open('/payroll/print?' + params.toString(), '_blank');
    }

    function loadEmployeeSalary(employeeId) {
        if (!employeeId) {
            document.getElementById('basicSalary').value = '';
            return;
        }

        const select = document.getElementById('employeeId');
        const selectedOption = select.options[select.selectedIndex];
        const salary = selectedOption.getAttribute('data-salary');

        if (salary) {
            document.getElementById('basicSalary').value = salary;
            calculateNetSalary();
        }
    }

    function calculateOvertimeAmount() {
        const hours = parseFloat(document.getElementById('overtimeHours').value) || 0;
        const amount = hours * overtimeRate;
        document.getElementById('overtimeAmount').value = amount.toFixed(2);
        calculateNetSalary();
    }

    function calculateNetSalary() {
        const basicSalary = parseFloat(document.getElementById('basicSalary').value) || 0;
        const overtimeAmount = parseFloat(document.getElementById('overtimeAmount').value) || 0;
        const bonus = parseFloat(document.getElementById('bonus').value) || 0;
        const deductions = parseFloat(document.getElementById('deductions').value) || 0;

        const netSalary = basicSalary + overtimeAmount + bonus - deductions;
        document.getElementById('netSalary').value = netSalary.toFixed(2);
    }

    function calculateEditOvertimeAmount() {
        const hours = parseFloat(document.getElementById('editOvertimeHours').value) || 0;
        const amount = hours * overtimeRate;
        document.getElementById('editOvertimeAmount').value = amount.toFixed(2);
        calculateEditNetSalary();
    }

    function calculateEditNetSalary() {
        const basicSalary = parseFloat(document.getElementById('editBasicSalary').value) || 0;
        const overtimeAmount = parseFloat(document.getElementById('editOvertimeAmount').value) || 0;
        const bonus = parseFloat(document.getElementById('editBonus').value) || 0;
        const deductions = parseFloat(document.getElementById('editDeductions').value) || 0;

        const netSalary = basicSalary + overtimeAmount + bonus - deductions;
        document.getElementById('editNetSalary').value = netSalary.toFixed(2);
    }

    function savePayroll() {
        const form = document.getElementById('addPayrollForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const formData = new FormData(form);
        const data = Object.fromEntries(formData);

        fetch('/api/payroll', {
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
                alert('Payroll processed successfully!');
                const modal = bootstrap.Modal.getInstance(document.getElementById('addPayrollModal'));
                modal.hide();
                form.reset();
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to process payroll'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error processing payroll. Please try again.');
        });
    }

    function viewPayroll(id) {
        fetch(`/api/payroll/${id}`)
            .then(response => response.json())
            .then(payroll => {
                const employee = payroll.employee;
                const details = `
                    <div class="row">
                        <div class="col-12 mb-3">
                            <h6 class="font-weight-bold">Employee Information</h6>
                            <hr>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Employee ID:</strong> ${employee.employee_id}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Name:</strong> ${employee.name}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Position:</strong> ${employee.position}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Department:</strong> ${employee.department}
                        </div>
                        <div class="col-12 mb-3 mt-3">
                            <h6 class="font-weight-bold">Payroll Details</h6>
                            <hr>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Month:</strong> ${formatMonth(payroll.payroll_month)}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Status:</strong> ${getStatusBadge(payroll.status)}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Basic Salary:</strong> Rs. ${parseFloat(payroll.basic_salary).toFixed(2)}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Overtime Hours:</strong> ${payroll.overtime_hours || 0} hrs
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Overtime Amount:</strong> Rs. ${parseFloat(payroll.overtime_amount || 0).toFixed(2)}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Bonus:</strong> Rs. ${parseFloat(payroll.bonus || 0).toFixed(2)}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Deductions:</strong> Rs. ${parseFloat(payroll.deductions || 0).toFixed(2)}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Net Salary:</strong> <span class="text-success fw-bold">Rs. ${parseFloat(payroll.net_salary).toFixed(2)}</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Payment Date:</strong> ${payroll.payment_date ? new Date(payroll.payment_date).toLocaleDateString() : '-'}
                        </div>
                        <div class="col-12 mb-3">
                            <strong>Notes:</strong><br>${payroll.notes || '-'}
                        </div>
                    </div>
                `;
                document.getElementById('payrollDetails').innerHTML = details;
                const modal = new bootstrap.Modal(document.getElementById('viewPayrollModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading payroll details.');
            });
    }

    function editPayroll(id) {
        fetch(`/api/payroll/${id}`)
            .then(response => response.json())
            .then(payroll => {
                document.getElementById('editPayrollId').value = payroll.id;
                document.getElementById('editEmployeeId').value = payroll.employee_id;
                document.getElementById('editPayrollMonth').value = payroll.payroll_month;
                document.getElementById('editBasicSalary').value = payroll.basic_salary;
                document.getElementById('editOvertimeHours').value = payroll.overtime_hours || '';
                document.getElementById('editOvertimeAmount').value = payroll.overtime_amount || '';
                document.getElementById('editBonus').value = payroll.bonus || '';
                document.getElementById('editDeductions').value = payroll.deductions || '';
                document.getElementById('editNetSalary').value = payroll.net_salary;
                document.getElementById('editStatus').value = payroll.status;
                document.getElementById('editPaymentDate').value = payroll.payment_date ? payroll.payment_date.split('T')[0] : '';
                document.getElementById('editNotes').value = payroll.notes || '';

                const modal = new bootstrap.Modal(document.getElementById('editPayrollModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading payroll.');
            });
    }

    function updatePayroll() {
        const form = document.getElementById('editPayrollForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const payrollId = document.getElementById('editPayrollId').value;
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);
        data.employee_id = document.getElementById('editEmployeeId').value;

        fetch(`/api/payroll/${payrollId}`, {
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
                alert('Payroll updated successfully!');
                const modal = bootstrap.Modal.getInstance(document.getElementById('editPayrollModal'));
                modal.hide();
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to update payroll'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating payroll.');
        });
    }

    function deletePayroll(id) {
        if (confirm('Are you sure you want to delete this payroll record? This action cannot be undone.')) {
            fetch(`/api/payroll/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Payroll deleted successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to delete payroll'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting payroll.');
            });
        }
    }

    function printPayslip() {
        window.print();
    }
</script>
@endpush
