@extends('layouts.app')

@section('title', 'Employee Attendance')

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
        background-color: #17a2b8;
        border-color: #17a2b8;
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
    <h1 class="h2">Employee Attendance</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportAttendance()">
                <i class="fas fa-download me-1"></i>Export
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="printAttendance()">
                <i class="fas fa-print me-1"></i>Print
            </button>
        </div>
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addAttendanceModal">
            <i class="fas fa-plus me-1"></i>Mark Attendance
        </button>
    </div>
</div>

<!-- Attendance Overview Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-vibrant bg-vibrant-primary me-3 shadow-primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted uppercase fs-tiny fw-semibold">Total Employees</h6>
                        <h3 class="mb-0 fw-bold text-vibrant-primary" id="total-employees">{{ $totalEmployees }}</h3>
                    </div>
                </div>
                <small class="text-primary fw-medium"><i class="bx bx-group me-1"></i> Total Workforce</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-vibrant bg-vibrant-success me-3 shadow-success">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted uppercase fs-tiny fw-semibold">Present Today</h6>
                        <h3 class="mb-0 fw-bold text-vibrant-success" id="present-today">{{ $presentToday }}</h3>
                    </div>
                </div>
                <small class="text-success fw-medium"><i class="bx bx-user-check me-1"></i> Checked-in Today</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-vibrant bg-vibrant-danger me-3 shadow-danger">
                        <i class="fas fa-user-times"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted uppercase fs-tiny fw-semibold">Absent Today</h6>
                        <h3 class="mb-0 fw-bold text-vibrant-danger" id="absent-today">{{ $absentToday }}</h3>
                    </div>
                </div>
                <small class="text-danger fw-medium"><i class="bx bx-user-x me-1"></i> Not Present</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-vibrant bg-vibrant-warning me-3 shadow-warning">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted uppercase fs-tiny fw-semibold">On Leave</h6>
                        <h3 class="mb-0 fw-bold text-vibrant-warning" id="on-leave">{{ $onLeaveToday }}</h3>
                    </div>
                </div>
                <small class="text-warning fw-medium"><i class="bx bx-calendar-event me-1"></i> Approved Leaves</small>
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
                    <!-- Date Filter -->
                    <div class="col-lg-3 col-md-6">
                        <label for="dateFilter" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-calendar text-primary me-2"></i>Date
                        </label>
                        <input type="date" class="form-control form-control-lg border-2" id="dateFilter">
                    </div>

                    <!-- Status Filter -->
                    <div class="col-lg-3 col-md-6">
                        <label for="statusFilter" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-toggle-on text-success me-2"></i>Status
                        </label>
                        <select class="form-select form-select-lg border-2" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="present">✓ Present</option>
                            <option value="absent">✗ Absent</option>
                            <option value="late">⏰ Late</option>
                            <option value="half_day">⌚ Half Day</option>
                            <option value="leave">📅 Leave</option>
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
                        <label for="attendanceSearch" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-search text-warning me-2"></i>Search
                        </label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-white border-2 border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control form-control-lg border-2 border-start-0 ps-0"
                                   id="attendanceSearch" placeholder="Employee name...">
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

<!-- Attendance Table -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Attendance Records</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                        data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow">
                        <a class="dropdown-item" href="#" onclick="refreshAttendance()">Refresh Data</a>
                        <a class="dropdown-item" href="#" onclick="exportAttendance()">Export Data</a>
                        <a class="dropdown-item" href="#" onclick="printAttendance()">Print Report</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="attendanceTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Employee ID</th>
                                <th>Employee Name</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Status</th>
                                <th>Notes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="attendanceBody">
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

<!-- Add Attendance Modal -->
<div class="modal fade" id="addAttendanceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mark Attendance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addAttendanceForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="employeeId" class="form-label">Employee *</label>
                            <select class="form-select" id="employeeId" name="employee_id" required>
                                <option value="">Select Employee</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->employee_id }} - {{ $employee->first_name }} {{ $employee->last_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="attendanceDate" class="form-label">Date *</label>
                            <input type="date" class="form-control" id="attendanceDate" name="attendance_date" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="checkIn" class="form-label">Check In Time</label>
                            <input type="time" class="form-control" id="checkIn" name="check_in">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="checkOut" class="form-label">Check Out Time</label>
                            <input type="time" class="form-control" id="checkOut" name="check_out">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status *</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="">Select Status</option>
                            <option value="present">Present</option>
                            <option value="absent">Absent</option>
                            <option value="late">Late</option>
                            <option value="half_day">Half Day</option>
                            <option value="leave">Leave</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Additional notes..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveAttendance()">Save Attendance</button>
            </div>
        </div>
    </div>
</div>

<!-- View Attendance Modal -->
<div class="modal fade" id="viewAttendanceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Attendance Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="attendanceDetails">
                <!-- Details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Attendance Modal -->
<div class="modal fade" id="editAttendanceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Attendance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editAttendanceForm">
                    @csrf
                    <input type="hidden" id="editAttendanceId">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editEmployeeId" class="form-label">Employee *</label>
                            <select class="form-select" id="editEmployeeId" name="employee_id" required>
                                <option value="">Select Employee</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->employee_id }} - {{ $employee->first_name }} {{ $employee->last_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editAttendanceDate" class="form-label">Date *</label>
                            <input type="date" class="form-control" id="editAttendanceDate" name="attendance_date" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editCheckIn" class="form-label">Check In Time</label>
                            <input type="time" class="form-control" id="editCheckIn" name="check_in">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editCheckOut" class="form-label">Check Out Time</label>
                            <input type="time" class="form-control" id="editCheckOut" name="check_out">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="editStatus" class="form-label">Status *</label>
                        <select class="form-select" id="editStatus" name="status" required>
                            <option value="">Select Status</option>
                            <option value="present">Present</option>
                            <option value="absent">Absent</option>
                            <option value="late">Late</option>
                            <option value="half_day">Half Day</option>
                            <option value="leave">Leave</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editNotes" class="form-label">Notes</label>
                        <textarea class="form-control" id="editNotes" name="notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updateAttendance()">Update Attendance</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Pagination variables
    let allAttendance = [];
    let filteredAttendance = [];
    let currentPage = 1;
    const recordsPerPage = 20;

    // Load attendance on page load
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('attendanceDate').value = today;
        document.getElementById('dateFilter').value = today;
        loadAttendance();

        // Add filter event listeners
        document.getElementById('statusFilter')?.addEventListener('change', applyFilters);
        document.getElementById('dateRangeFilter')?.addEventListener('change', applyFilters);
        document.getElementById('dateFilter')?.addEventListener('change', applyFilters);
        document.getElementById('attendanceSearch')?.addEventListener('input', debounce(applyFilters, 500));
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
        loadAttendance(1);
    }

    function loadAttendance(page = 1) {
        const tbody = document.getElementById('attendanceBody');
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
        
        const status = document.getElementById('statusFilter').value;
        const dateRange = document.getElementById('dateRangeFilter').value;
        const date = document.getElementById('dateFilter').value;
        const search = document.getElementById('attendanceSearch').value;

        if (status) params.set('status', status);
        if (dateRange) params.set('date_range', dateRange);
        if (date) params.set('date', date);
        if (search) params.set('search', search);

        fetch('/api/attendance?' + params.toString())
            .then(response => response.json())
            .then(response => {
                displayAttendance(response.data);
                updatePaginationInfo(response.pagination.from || 0, response.pagination.to || 0, response.pagination.total);
                renderPaginationControls(response.pagination.last_page, response.pagination.current_page);
                currentPage = response.pagination.current_page;
            })
            .catch(error => {
                console.error('Error loading attendance:', error);
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center text-danger py-5">Error loading attendance records</td>
                    </tr>
                `;
            });
    }

    function displayAttendance(records) {
        const tbody = document.getElementById('attendanceBody');
        tbody.innerHTML = '';

        if (!records || records.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-5">No attendance records found</td>
                </tr>
            `;
            return;
        }

        records.forEach(attendance => {
            const employee = attendance.employee;
            const statusBadge = getStatusBadge(attendance.status);

            const row = `
                <tr>
                    <td>${formatDate(attendance.attendance_date)}</td>
                    <td>${employee ? employee.employee_id : 'N/A'}</td>
                    <td>${employee ? employee.first_name + ' ' + employee.last_name : 'Unknown'}</td>
                    <td>${attendance.check_in || '-'}</td>
                    <td>${attendance.check_out || '-'}</td>
                    <td>${statusBadge}</td>
                    <td>${attendance.notes ? (attendance.notes.length > 30 ? attendance.notes.substring(0, 30) + '...' : attendance.notes) : '-'}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-sm btn-primary" onclick="viewAttendance(${attendance.id})" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-info" onclick="editAttendance(${attendance.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteAttendance(${attendance.id})" title="Delete">
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
        loadAttendance(page);
        document.getElementById('attendanceTable').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }


    function getStatusBadge(status) {
        const badges = {
            'present': '<span class="badge bg-success">Present</span>',
            'absent': '<span class="badge bg-danger">Absent</span>',
            'late': '<span class="badge bg-warning">Late</span>',
            'half_day': '<span class="badge bg-info">Half Day</span>',
            'leave': '<span class="badge bg-secondary">Leave</span>'
        };
        return badges[status] || status;
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    }

    // Filter functionality
    document.getElementById('statusFilter')?.addEventListener('change', applyFilters);
    document.getElementById('dateRangeFilter')?.addEventListener('change', applyFilters);
    document.getElementById('dateFilter')?.addEventListener('change', applyFilters);
    document.getElementById('attendanceSearch')?.addEventListener('input', applyFilters);

    function applyFilters() {
        currentPage = 1;
        loadAttendance(1);
    }

    function clearFilters() {
        // Reset all filters
        document.getElementById('statusFilter').value = '';
        document.getElementById('dateRangeFilter').value = '';
        document.getElementById('dateFilter').value = '';
        document.getElementById('attendanceSearch').value = '';

        // Reset filtered attendance to all attendance
        filteredAttendance = allAttendance;
        currentPage = 1;
        displayAttendance();
    }

    function refreshAttendance() {
        currentPage = 1;
        loadAttendance();
    }

    function exportAttendance() {
        const params = new URLSearchParams();
        const status = document.getElementById('statusFilter').value;
        const dateRange = document.getElementById('dateRangeFilter').value;
        const search = document.getElementById('attendanceSearch').value;

        if (status) params.set('status', status);
        if (dateRange) params.set('date_range', dateRange);
        if (search) params.set('search', search);

        window.location.href = '/attendance/export?' + params.toString();
    }

    function printAttendance() {
        const params = new URLSearchParams();
        const status = document.getElementById('statusFilter').value;
        const dateRange = document.getElementById('dateRangeFilter').value;
        const search = document.getElementById('attendanceSearch').value;

        if (status) params.set('status', status);
        if (dateRange) params.set('date_range', dateRange);
        if (search) params.set('search', search);

        window.open('/attendance/print?' + params.toString(), '_blank');
    }

    function saveAttendance() {
        const form = document.getElementById('addAttendanceForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const formData = new FormData(form);
        const data = Object.fromEntries(formData);

        fetch('/api/attendance', {
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
                alert('Attendance saved successfully!');
                const modal = bootstrap.Modal.getInstance(document.getElementById('addAttendanceModal'));
                modal.hide();
                form.reset();
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to save attendance'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving attendance. Please try again.');
        });
    }

    function viewAttendance(id) {
        fetch(`/api/attendance/${id}`)
            .then(response => response.json())
            .then(attendance => {
                const employee = attendance.employee;
                const statusBadge = getStatusBadge(attendance.status);

                const details = `
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Employee ID:</strong> ${employee.employee_id}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Employee Name:</strong> ${employee.first_name} ${employee.last_name}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Date:</strong> ${formatDate(attendance.attendance_date)}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Status:</strong> ${statusBadge}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Check In:</strong> ${attendance.check_in || '-'}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Check Out:</strong> ${attendance.check_out || '-'}
                        </div>
                        <div class="col-12 mb-3">
                            <strong>Notes:</strong><br>${attendance.notes || '-'}
                        </div>
                    </div>
                `;
                document.getElementById('attendanceDetails').innerHTML = details;
                const modal = new bootstrap.Modal(document.getElementById('viewAttendanceModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading attendance details.');
            });
    }

    function editAttendance(id) {
        fetch(`/api/attendance/${id}`)
            .then(response => response.json())
            .then(attendance => {
                document.getElementById('editAttendanceId').value = attendance.id;
                document.getElementById('editEmployeeId').value = attendance.employee_id;
                document.getElementById('editAttendanceDate').value = attendance.attendance_date;
                document.getElementById('editCheckIn').value = attendance.check_in || '';
                document.getElementById('editCheckOut').value = attendance.check_out || '';
                document.getElementById('editStatus').value = attendance.status;
                document.getElementById('editNotes').value = attendance.notes || '';

                const modal = new bootstrap.Modal(document.getElementById('editAttendanceModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading attendance.');
            });
    }

    function updateAttendance() {
        const form = document.getElementById('editAttendanceForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const attendanceId = document.getElementById('editAttendanceId').value;
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);

        fetch(`/api/attendance/${attendanceId}`, {
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
                alert('Attendance updated successfully!');
                const modal = bootstrap.Modal.getInstance(document.getElementById('editAttendanceModal'));
                modal.hide();
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to update attendance'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating attendance.');
        });
    }

    function deleteAttendance(id) {
        if (confirm('Are you sure you want to delete this attendance record? This action cannot be undone.')) {
            fetch(`/api/attendance/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Attendance deleted successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to delete attendance'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting attendance.');
            });
        }
    }
</script>
@endpush
