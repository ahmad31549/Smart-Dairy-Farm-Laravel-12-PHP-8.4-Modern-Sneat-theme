@extends('layouts.app')

@section('title', 'Employee Management')

@push('styles')
<style>
    .border-left-success { border-left: 0.25rem solid var(--success-main) !important; }
    .border-left-warning { border-left: 0.25rem solid var(--warning-main) !important; }
    .border-left-danger { border-left: 0.25rem solid var(--danger-main) !important; }
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

    .action-buttons .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .action-buttons .btn-info {
        background-color: #007bff;
        border-color: #007bff;
    }

    .action-buttons .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
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

    .card {
        transition: all 0.3s ease;
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

    /* Responsive Adjustments */
    @media (max-width: 991px) {
        .form-select-lg, .form-control-lg {
            font-size: 0.9rem;
            padding: 0.5rem 0.75rem;
        }

        .form-label {
            font-size: 0.8rem;
        }
    }

    @media (max-width: 576px) {
        .card-body {
            padding: 1rem !important;
        }

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
    <h1 class="h2">Employee Management</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportEmployees()">
                <i class="fas fa-download me-1"></i>Export
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="printEmployees()">
                <i class="fas fa-print me-1"></i>Print
            </button>
        </div>
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
            <i class="fas fa-plus me-1"></i>Add Employee
        </button>
    </div>
</div>

<!-- Employee Overview Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Employees</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-employees">{{ $totalEmployees }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Active Today</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="active-today">{{ $activeToday }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            On Leave</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="on-leave">{{ $onLeave }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-times fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            New This Month</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="new-this-month">{{ $newThisMonth }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-plus fa-2x text-gray-300"></i>
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
                    <!-- Position Filter -->
                    <div class="col-lg-3 col-md-6">
                        <label for="positionFilter" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-briefcase text-primary me-2"></i>Position
                        </label>
                        <select class="form-select form-select-lg border-2" id="positionFilter">
                            <option value="">All Positions</option>
                            <option value="farm-manager">Farm Manager</option>
                            <option value="milk-technician">Milk Technician</option>
                            <option value="animal-care">Animal Care</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <!-- Department Filter -->
                    <div class="col-lg-3 col-md-6">
                        <label for="departmentFilter" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-building text-success me-2"></i>Department
                        </label>
                        <select class="form-select form-select-lg border-2" id="departmentFilter">
                            <option value="">All Departments</option>
                            <option value="production">Production</option>
                            <option value="health">Health</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="administration">Administration</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="col-lg-3 col-md-6">
                        <label for="statusFilter" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-toggle-on text-info me-2"></i>Status
                        </label>
                        <select class="form-select form-select-lg border-2" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="active">✓ Active</option>
                            <option value="inactive">⊗ Inactive</option>
                            <option value="terminated">✗ Terminated</option>
                        </select>
                    </div>

                    <!-- Search Box -->
                    <div class="col-lg-3 col-md-6">
                        <label for="employeeSearch" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-search text-warning me-2"></i>Search
                        </label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-white border-2 border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control form-control-lg border-2 border-start-0 ps-0"
                                   id="employeeSearch" placeholder="Employee ID or name...">
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

<!-- Employees Table -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Employees List</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                        data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow">
                        <a class="dropdown-item" href="#" onclick="refreshEmployees()">Refresh Data</a>
                        <a class="dropdown-item" href="#" onclick="exportEmployees()">Export Data</a>
                        <a class="dropdown-item" href="#" onclick="printEmployees()">Print Report</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="employeesTable">
                        <thead>
                            <tr>
                                <th>Employee ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Position</th>
                                <th>Department</th>
                                <th>Hire Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="employeesBody">
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

<!-- Add Employee Modal -->
<div class="modal fade" id="addEmployeeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addEmployeeForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="firstName" class="form-label">First Name *</label>
                            <input type="text" class="form-control" id="firstName" name="first_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lastName" class="form-label">Last Name *</label>
                            <input type="text" class="form-control" id="lastName" name="last_name" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="position" class="form-label">Position *</label>
                            <select class="form-select" id="position" name="position" required>
                                <option value="">Select Position</option>
                                <option value="farm-manager">Farm Manager</option>
                                <option value="milk-technician">Milk Technician</option>
                                <option value="animal-care">Animal Care</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="department" class="form-label">Department *</label>
                            <select class="form-select" id="department" name="department" required>
                                <option value="">Select Department</option>
                                <option value="production">Production</option>
                                <option value="health">Health</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="administration">Administration</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="hireDate" class="form-label">Hire Date *</label>
                            <input type="date" class="form-control" id="hireDate" name="hire_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="salary" class="form-label">Salary</label>
                            <input type="number" class="form-control" id="salary" name="salary" step="0.01">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveEmployee()">Save Employee</button>
            </div>
        </div>
    </div>
</div>

<!-- View Employee Modal -->
<div class="modal fade" id="viewEmployeeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Employee Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="employeeDetails">
                <!-- Details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Employee Modal -->
<div class="modal fade" id="editEmployeeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editEmployeeForm">
                    @csrf
                    <input type="hidden" id="editEmployeeId">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editFirstName" class="form-label">First Name *</label>
                            <input type="text" class="form-control" id="editFirstName" name="first_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editLastName" class="form-label">Last Name *</label>
                            <input type="text" class="form-control" id="editLastName" name="last_name" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editEmail" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="editEmail" name="email" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editPhone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="editPhone" name="phone">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editPosition" class="form-label">Position *</label>
                            <select class="form-select" id="editPosition" name="position" required>
                                <option value="">Select Position</option>
                                <option value="farm-manager">Farm Manager</option>
                                <option value="milk-technician">Milk Technician</option>
                                <option value="animal-care">Animal Care</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editDepartment" class="form-label">Department *</label>
                            <select class="form-select" id="editDepartment" name="department" required>
                                <option value="">Select Department</option>
                                <option value="production">Production</option>
                                <option value="health">Health</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="administration">Administration</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editHireDate" class="form-label">Hire Date *</label>
                            <input type="date" class="form-control" id="editHireDate" name="hire_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editSalary" class="form-label">Salary</label>
                            <input type="number" class="form-control" id="editSalary" name="salary" step="0.01">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editStatus" class="form-label">Status *</label>
                            <select class="form-select" id="editStatus" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="terminated">Terminated</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="editAddress" class="form-label">Address</label>
                        <textarea class="form-control" id="editAddress" name="address" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updateEmployee()">Update Employee</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Pagination variables
    let allEmployees = [];
    let filteredEmployees = [];
    let currentPage = 1;
    const recordsPerPage = 20;

    // Load employees on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadEmployees();
    });

    function loadEmployees() {
        const tbody = document.getElementById('employeesBody');
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </td>
            </tr>
        `;

        fetch('/api/employees')
            .then(response => response.json())
            .then(employees => {
                allEmployees = employees;
                filteredEmployees = employees;
                currentPage = 1;
                displayEmployees();
            })
            .catch(error => {
                console.error('Error loading employees:', error);
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" class="text-center text-danger">Error loading employees</td>
                    </tr>
                `;
            });
    }

    function displayEmployees() {
        const tbody = document.getElementById('employeesBody');
        tbody.innerHTML = '';

        if (filteredEmployees.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center">No employees found</td>
                </tr>
            `;
            updatePaginationInfo(0, 0, 0);
            renderPaginationControls(0);
            return;
        }

        // Calculate pagination
        const totalRecords = filteredEmployees.length;
        const totalPages = Math.ceil(totalRecords / recordsPerPage);
        const startIndex = (currentPage - 1) * recordsPerPage;
        const endIndex = Math.min(startIndex + recordsPerPage, totalRecords);
        const recordsToDisplay = filteredEmployees.slice(startIndex, endIndex);

        // Display employees
        recordsToDisplay.forEach(employee => {
            const statusBadge = getStatusBadge(employee.status);
            const position = formatPosition(employee.position);
            const department = capitalizeFirst(employee.department);

            const row = `
                <tr>
                    <td>${employee.employee_id}</td>
                    <td>${employee.first_name} ${employee.last_name}</td>
                    <td>${employee.email}</td>
                    <td>${employee.phone || '-'}</td>
                    <td>${position}</td>
                    <td>${department}</td>
                    <td>${formatDate(employee.hire_date)}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-sm btn-primary" onclick="viewEmployee(${employee.id})" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-info" onclick="editEmployee(${employee.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteEmployee(${employee.id})" title="Delete">
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
            pageLi.className = `page-item ${i === currentPage ? 'active' : ''}`;
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
        nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
        nextLi.innerHTML = `
            <a class="page-link" href="#" onclick="changePage(${currentPage + 1}); return false;">
                Next <i class="fas fa-chevron-right"></i>
            </a>
        `;
        paginationControls.appendChild(nextLi);
    }

    function changePage(page) {
        const totalPages = Math.ceil(filteredEmployees.length / recordsPerPage);
        if (page < 1 || page > totalPages) {
            return;
        }
        currentPage = page;
        displayEmployees();
        // Scroll to top of table
        document.getElementById('employeesTable').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function getStatusBadge(status) {
        const badges = {
            'active': '<span class="badge bg-success">Active</span>',
            'inactive': '<span class="badge bg-warning">Inactive</span>',
            'terminated': '<span class="badge bg-danger">Terminated</span>'
        };
        return badges[status] || status;
    }

    function formatPosition(position) {
        return position.split('-').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    }

    function capitalizeFirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    // Filter functionality
    document.getElementById('positionFilter')?.addEventListener('change', applyFilters);
    document.getElementById('departmentFilter')?.addEventListener('change', applyFilters);
    document.getElementById('statusFilter')?.addEventListener('change', applyFilters);
    document.getElementById('employeeSearch')?.addEventListener('input', applyFilters);

    function applyFilters() {
        const positionFilter = document.getElementById('positionFilter').value.toLowerCase();
        const departmentFilter = document.getElementById('departmentFilter').value.toLowerCase();
        const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
        const searchTerm = document.getElementById('employeeSearch').value.toLowerCase();

        // Filter the employees
        filteredEmployees = allEmployees.filter(employee => {
            const position = employee.position.toLowerCase();
            const department = employee.department.toLowerCase();
            const status = employee.status.toLowerCase();
            const employeeId = employee.employee_id.toLowerCase();
            const fullName = (employee.first_name + ' ' + employee.last_name).toLowerCase();

            // Position filter
            const matchesPosition = !positionFilter || position === positionFilter;

            // Department filter
            const matchesDepartment = !departmentFilter || department === departmentFilter;

            // Status filter
            const matchesStatus = !statusFilter || status === statusFilter;

            // Search filter
            const matchesSearch = !searchTerm ||
                employeeId.includes(searchTerm) ||
                fullName.includes(searchTerm);

            return matchesPosition && matchesDepartment && matchesStatus && matchesSearch;
        });

        // Reset to first page and display
        currentPage = 1;
        displayEmployees();
    }

    function clearFilters() {
        // Reset all filters
        document.getElementById('positionFilter').value = '';
        document.getElementById('departmentFilter').value = '';
        document.getElementById('statusFilter').value = '';
        document.getElementById('employeeSearch').value = '';

        // Reset filtered employees to all employees
        filteredEmployees = allEmployees;
        currentPage = 1;
        displayEmployees();
    }

    function refreshEmployees() {
        currentPage = 1;
        loadEmployees();
    }

    function exportEmployees() {
        const params = new URLSearchParams();
        const position = document.getElementById('positionFilter').value;
        const department = document.getElementById('departmentFilter').value;
        const status = document.getElementById('statusFilter').value;
        const search = document.getElementById('employeeSearch').value;

        if (position) params.set('position', position);
        if (department) params.set('department', department);
        if (status) params.set('status', status);
        if (search) params.set('search', search);

        window.location.href = '/employees/export?' + params.toString();
    }

    function printEmployees() {
        const params = new URLSearchParams();
        const position = document.getElementById('positionFilter').value;
        const department = document.getElementById('departmentFilter').value;
        const status = document.getElementById('statusFilter').value;
        const search = document.getElementById('employeeSearch').value;

        if (position) params.set('position', position);
        if (department) params.set('department', department);
        if (status) params.set('status', status);
        if (search) params.set('search', search);

        window.open('/employees/print?' + params.toString(), '_blank');
    }

    function saveEmployee() {
        const form = document.getElementById('addEmployeeForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const formData = new FormData(form);
        const data = Object.fromEntries(formData);

        fetch('/api/employees', {
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
                alert('Employee saved successfully!');
                const modal = bootstrap.Modal.getInstance(document.getElementById('addEmployeeModal'));
                modal.hide();
                form.reset();
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to save employee'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving employee. Please try again.');
        });
    }

    function viewEmployee(id) {
        fetch(`/api/employees/${id}`)
            .then(response => response.json())
            .then(employee => {
                const statusBadge = getStatusBadge(employee.status);
                const position = formatPosition(employee.position);

                const details = `
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Employee ID:</strong> ${employee.employee_id}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Full Name:</strong> ${employee.first_name} ${employee.last_name}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Email:</strong> ${employee.email}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Phone:</strong> ${employee.phone || '-'}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Position:</strong> ${position}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Department:</strong> ${capitalizeFirst(employee.department)}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Hire Date:</strong> ${formatDate(employee.hire_date)}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Salary:</strong> ${employee.salary ? '$' + parseFloat(employee.salary).toFixed(2) : '-'}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Status:</strong> ${statusBadge}
                        </div>
                        <div class="col-12 mb-3">
                            <strong>Address:</strong><br>${employee.address || '-'}
                        </div>
                    </div>
                `;
                document.getElementById('employeeDetails').innerHTML = details;
                const modal = new bootstrap.Modal(document.getElementById('viewEmployeeModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading employee details.');
            });
    }

    function editEmployee(id) {
        fetch(`/api/employees/${id}`)
            .then(response => response.json())
            .then(employee => {
                document.getElementById('editEmployeeId').value = employee.id;
                document.getElementById('editFirstName').value = employee.first_name;
                document.getElementById('editLastName').value = employee.last_name;
                document.getElementById('editEmail').value = employee.email;
                document.getElementById('editPhone').value = employee.phone || '';
                document.getElementById('editPosition').value = employee.position;
                document.getElementById('editDepartment').value = employee.department;
                document.getElementById('editHireDate').value = employee.hire_date;
                document.getElementById('editSalary').value = employee.salary || '';
                document.getElementById('editStatus').value = employee.status;
                document.getElementById('editAddress').value = employee.address || '';

                const modal = new bootstrap.Modal(document.getElementById('editEmployeeModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading employee.');
            });
    }

    function updateEmployee() {
        const form = document.getElementById('editEmployeeForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const employeeId = document.getElementById('editEmployeeId').value;
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);

        fetch(`/api/employees/${employeeId}`, {
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
                alert('Employee updated successfully!');
                const modal = bootstrap.Modal.getInstance(document.getElementById('editEmployeeModal'));
                modal.hide();
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to update employee'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating employee.');
        });
    }

    function deleteEmployee(id) {
        if (confirm('Are you sure you want to delete this employee? This action cannot be undone.')) {
            fetch(`/api/employees/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Employee deleted successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to delete employee'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting employee.');
            });
        }
    }
</script>
@endpush
