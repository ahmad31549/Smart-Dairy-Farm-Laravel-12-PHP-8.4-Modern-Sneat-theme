@extends('layouts.app')

@section('title', 'Milk Quality Analysis')

@push('styles')
<style>
    .border-left-success { border-left: 0.25rem solid var(--success-main) !important; }
    .border-left-danger { border-left: 0.25rem solid var(--danger-main) !important; }
    .border-left-warning { border-left: 0.25rem solid var(--warning-main) !important; }
    .border-left-info { border-left: 0.25rem solid var(--info-main) !important; }

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

    /* Status and Quality badges */
    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 500;
    }
    .status-passed {
        background-color: #d1fae5;
        color: #065f46;
    }
    .status-failed {
        background-color: #fee2e2;
        color: #991b1b;
    }
    .status-pending {
        background-color: #fef3c7;
        color: #92400e;
    }
    .quality-a {
        background-color: #d1fae5;
        color: #065f46;
    }
    .quality-b {
        background-color: #dbeafe;
        color: #1e40af;
    }
    .quality-c {
        background-color: #fef3c7;
        color: #92400e;
    }
    .quality-d {
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
    <h1 class="h2">Milk Quality Analysis</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportQualityData()">
                <i class="fas fa-download me-1"></i>Export
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="printQualityReport()">
                <i class="fas fa-print me-1"></i>Print
            </button>
        </div>
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addQualityTestModal">
            <i class="fas fa-plus me-1"></i>Add Quality Test
        </button>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-vibrant bg-vibrant-success me-3 shadow-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted uppercase fs-tiny fw-semibold">Passed Tests</h6>
                        <h3 class="mb-0 fw-bold text-vibrant-success" id="passedCount">{{ $passedTests }}</h3>
                    </div>
                </div>
                <small class="text-success fw-medium"><i class="bx bx-check-double me-1"></i> Quality Verified</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-vibrant bg-vibrant-danger me-3 shadow-danger">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted uppercase fs-tiny fw-semibold">Failed Tests</h6>
                        <h3 class="mb-0 fw-bold text-vibrant-danger" id="failedCount">{{ $failedTests }}</h3>
                    </div>
                </div>
                <small class="text-danger fw-medium"><i class="bx bx-error me-1"></i> Requires Review</small>
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
                        <h6 class="mb-0 text-muted uppercase fs-tiny fw-semibold">Pending Tests</h6>
                        <h3 class="mb-0 fw-bold text-vibrant-warning" id="pendingCount">{{ $pendingTests }}</h3>
                    </div>
                </div>
                <small class="text-warning fw-medium"><i class="bx bx-time me-1"></i> In Progress</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-vibrant bg-vibrant-info me-3 shadow-info">
                        <i class="fas fa-award"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted uppercase fs-tiny fw-semibold">Grade A Tests</h6>
                        <h3 class="mb-0 fw-bold text-vibrant-info" id="gradeACount">{{ $gradeA }}</h3>
                    </div>
                </div>
                <small class="text-info fw-medium"><i class="bx bx-star me-1"></i> Top Quality</small>
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
                    <!-- Test Result Filter -->
                    <div class="col-lg-3 col-md-6">
                        <label for="resultFilter" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-check-double text-success me-2"></i>Test Result
                        </label>
                        <select class="form-select form-select-lg border-2" id="resultFilter">
                            <option value="">All Results</option>
                            <option value="Passed">✓ Passed</option>
                            <option value="Failed">✗ Failed</option>
                            <option value="Pending">⏳ Pending</option>
                        </select>
                    </div>

                    <!-- Quality Grade Filter -->
                    <div class="col-lg-3 col-md-6">
                        <label for="gradeFilter" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-award text-warning me-2"></i>Quality Grade
                        </label>
                        <select class="form-select form-select-lg border-2" id="gradeFilter">
                            <option value="">All Grades</option>
                            <option value="A">Grade A</option>
                            <option value="B">Grade B</option>
                            <option value="C">Grade C</option>
                            <option value="D">Grade D</option>
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
                            <option value="this-month">This Month</option>
                            <option value="this-year">This Year</option>
                        </select>
                    </div>

                    <!-- Search Box -->
                    <div class="col-lg-3 col-md-6">
                        <label for="qualitySearch" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-search text-primary me-2"></i>Search
                        </label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-white border-2 border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control form-control-lg border-2 border-start-0 ps-0"
                                   id="qualitySearch" placeholder="Batch number...">
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

<!-- Quality Test Results Table -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Quality Test Results</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                        data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow">
                        <a class="dropdown-item" href="#" onclick="refreshQualityData()">Refresh Data</a>
                        <a class="dropdown-item" href="#" onclick="exportQualityData()">Export Data</a>
                        <a class="dropdown-item" href="#" onclick="printQualityReport()">Print Report</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="qualityTable">
                        <thead>
                            <tr>
                                <th>Test Date</th>
                                <th>Batch Number</th>
                                <th>Fat %</th>
                                <th>Protein %</th>
                                <th>pH Level</th>
                                <th>Temp (°C)</th>
                                <th>Grade</th>
                                <th>Result</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="qualityBody">
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

<!-- Add Quality Test Modal -->
<div class="modal fade" id="addQualityTestModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Quality Test</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addQualityForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Test Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="test_date" id="test_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Batch Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="batch_number" id="batch_number" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Fat Content (%) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="fat_content" id="fat_content" step="0.01" min="0" max="100" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Protein (%) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="protein_content" id="protein_content" step="0.01" min="0" max="100" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Lactose (%)</label>
                            <input type="number" class="form-control" name="lactose_content" id="lactose_content" step="0.01" min="0" max="100">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">pH Level <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="ph_level" id="ph_level" step="0.01" min="0" max="14" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Temperature (°C) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="temperature" id="temperature" step="0.1" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Somatic Cell Count</label>
                            <input type="number" class="form-control" name="somatic_cell_count" id="somatic_cell_count" min="0">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Quality Grade <span class="text-danger">*</span></label>
                            <select class="form-select" name="quality_grade" id="quality_grade" required>
                                <option value="">Select Grade</option>
                                <option value="A">Grade A</option>
                                <option value="B">Grade B</option>
                                <option value="C">Grade C</option>
                                <option value="D">Grade D</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Test Result <span class="text-danger">*</span></label>
                            <select class="form-select" name="test_result" id="test_result" required>
                                <option value="">Select Result</option>
                                <option value="Passed">Passed</option>
                                <option value="Failed">Failed</option>
                                <option value="Pending">Pending</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tested By</label>
                            <input type="text" class="form-control" name="tested_by" id="tested_by">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" id="notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveQualityTest()">Save Test</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Quality Test Modal -->
<div class="modal fade" id="editQualityModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Quality Test</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editQualityForm">
                    @csrf
                    <input type="hidden" name="quality_id" id="edit_quality_id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Test Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="test_date" id="edit_test_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Batch Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="batch_number" id="edit_batch_number" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Fat Content (%) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="fat_content" id="edit_fat_content" step="0.01" min="0" max="100" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Protein (%) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="protein_content" id="edit_protein_content" step="0.01" min="0" max="100" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Lactose (%)</label>
                            <input type="number" class="form-control" name="lactose_content" id="edit_lactose_content" step="0.01" min="0" max="100">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">pH Level <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="ph_level" id="edit_ph_level" step="0.01" min="0" max="14" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Temperature (°C) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="temperature" id="edit_temperature" step="0.1" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Somatic Cell Count</label>
                            <input type="number" class="form-control" name="somatic_cell_count" id="edit_somatic_cell_count" min="0">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Quality Grade <span class="text-danger">*</span></label>
                            <select class="form-select" name="quality_grade" id="edit_quality_grade" required>
                                <option value="">Select Grade</option>
                                <option value="A">Grade A</option>
                                <option value="B">Grade B</option>
                                <option value="C">Grade C</option>
                                <option value="D">Grade D</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Test Result <span class="text-danger">*</span></label>
                            <select class="form-select" name="test_result" id="edit_test_result" required>
                                <option value="">Select Result</option>
                                <option value="Passed">Passed</option>
                                <option value="Failed">Failed</option>
                                <option value="Pending">Pending</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tested By</label>
                            <input type="text" class="form-control" name="tested_by" id="edit_tested_by">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" id="edit_notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updateQualityTest()">Update Test</button>
            </div>
        </div>
    </div>
</div>

<!-- View Quality Test Modal -->
<div class="modal fade" id="viewQualityModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quality Test Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="qualityDetails">
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

    // Load quality tests on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadQualityTests();

        // Set today's date as default
        document.getElementById('test_date').valueAsDate = new Date();

        // Add filter event listeners
        document.getElementById('resultFilter')?.addEventListener('change', applyFilters);
        document.getElementById('gradeFilter')?.addEventListener('change', applyFilters);
        document.getElementById('dateRangeFilter')?.addEventListener('change', applyFilters);
        document.getElementById('qualitySearch')?.addEventListener('input', debounce(applyFilters, 500));
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
        loadQualityTests(1);
    }

    // Load quality test records
    function loadQualityTests(page = 1) {
        const tbody = document.getElementById('qualityBody');
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
        
        const result = document.getElementById('resultFilter').value;
        const grade = document.getElementById('gradeFilter').value;
        const dateRange = document.getElementById('dateRangeFilter').value;
        const search = document.getElementById('qualitySearch').value;

        if (result) params.set('test_result', result);
        if (grade) params.set('quality_grade', grade);
        if (dateRange) params.set('date_range', dateRange);
        if (search) params.set('search', search);

        fetch('/api/quality-tests/all?' + params.toString())
            .then(response => response.json())
            .then(response => {
                displayRecords(response.data);
                updatePaginationInfo(response.pagination.from || 0, response.pagination.to || 0, response.pagination.total);
                renderPaginationControls(response.pagination.last_page, response.pagination.current_page);
                currentPage = response.pagination.current_page;
            })
            .catch(error => {
                console.error('Error loading quality tests:', error);
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" class="text-center text-danger py-5">Error loading quality test records</td>
                    </tr>
                `;
            });
    }

    function displayRecords(records) {
        const tbody = document.getElementById('qualityBody');
        tbody.innerHTML = '';

        if (!records || records.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center py-5">No quality test records found</td>
                </tr>
            `;
            return;
        }

        records.forEach(test => {
            const qualityClass = getQualityClass(test.quality_grade);
            const resultClass = getResultClass(test.test_result);
            const row = `
                <tr>
                    <td>${test.test_date}</td>
                    <td><strong>${test.batch_number}</strong></td>
                    <td>${test.fat_content}%</td>
                    <td>${test.protein_content}%</td>
                    <td>${test.ph_level}</td>
                    <td>${test.temperature}°C</td>
                    <td><span class="status-badge ${qualityClass}">Grade ${test.quality_grade}</span></td>
                    <td><span class="status-badge ${resultClass}">${test.test_result}</span></td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-primary" onclick="viewQualityTest(${test.id})" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-warning" onclick="editQualityTest(${test.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger" onclick="deleteQualityTest(${test.id})" title="Delete">
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
        loadQualityTests(page);
        document.getElementById('qualityTable').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // Get quality class for badge
    function getQualityClass(grade) {
        switch(grade) {
            case 'A':
                return 'quality-a';
            case 'B':
                return 'quality-b';
            case 'C':
                return 'quality-c';
            case 'D':
                return 'quality-d';
            default:
                return 'quality-b';
        }
    }

    // Get result class for badge
    function getResultClass(result) {
        switch(result) {
            case 'Passed':
                return 'status-passed';
            case 'Failed':
                return 'status-failed';
            case 'Pending':
                return 'status-pending';
            default:
                return 'status-pending';
        }
    }

    // Filter functionality

    function clearFilters() {
        document.getElementById('resultFilter').value = '';
        document.getElementById('gradeFilter').value = '';
        document.getElementById('dateRangeFilter').value = '';
        document.getElementById('qualitySearch').value = '';

        currentPage = 1;
        loadQualityTests(1);
    }

    // Save quality test
    function saveQualityTest() {
        const form = document.getElementById('addQualityForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const formData = new FormData(form);

        fetch('/quality-analysis', {
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
                const modal = bootstrap.Modal.getInstance(document.getElementById('addQualityTestModal'));
                modal.hide();
                form.reset();
                document.getElementById('test_date').valueAsDate = new Date();
                loadQualityTests();
                location.reload(); // Reload to update stats
            }
        })
        .catch(error => {
            console.error('Error saving quality test:', error);
            alert('Error saving quality test. Please try again.');
        });
    }

    // View quality test details
    function viewQualityTest(id) {
        fetch(`/api/quality-tests/${id}`)
            .then(response => response.json())
            .then(data => {
                const qualityBadge = `<span class="status-badge ${getQualityClass(data.quality_grade)}">Grade ${data.quality_grade}</span>`;
                const resultBadge = `<span class="status-badge ${getResultClass(data.test_result)}">${data.test_result}</span>`;
                const details = `
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Test Date:</strong> ${data.test_date}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Batch Number:</strong> ${data.batch_number}
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>Fat Content:</strong> ${data.fat_content}%
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>Protein Content:</strong> ${data.protein_content}%
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>Lactose Content:</strong> ${data.lactose_content ? data.lactose_content + '%' : 'N/A'}
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>pH Level:</strong> ${data.ph_level}
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>Temperature:</strong> ${data.temperature}°C
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>Somatic Cell Count:</strong> ${data.somatic_cell_count ? data.somatic_cell_count : 'N/A'}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Quality Grade:</strong> ${qualityBadge}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Test Result:</strong> ${resultBadge}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Tested By:</strong> ${data.tested_by || 'N/A'}
                        </div>
                        <div class="col-12 mb-3">
                            <strong>Notes:</strong><br>${data.notes || 'No notes'}
                        </div>
                    </div>
                `;
                document.getElementById('qualityDetails').innerHTML = details;

                const modal = new bootstrap.Modal(document.getElementById('viewQualityModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error loading quality test:', error);
                alert('Error loading quality test details. Please try again.');
            });
    }

    // Edit quality test
    function editQualityTest(id) {
        fetch(`/api/quality-tests/${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('edit_quality_id').value = data.id;
                document.getElementById('edit_test_date').value = data.test_date;
                document.getElementById('edit_batch_number').value = data.batch_number;
                document.getElementById('edit_fat_content').value = data.fat_content;
                document.getElementById('edit_protein_content').value = data.protein_content;
                document.getElementById('edit_lactose_content').value = data.lactose_content || '';
                document.getElementById('edit_ph_level').value = data.ph_level;
                document.getElementById('edit_temperature').value = data.temperature;
                document.getElementById('edit_somatic_cell_count').value = data.somatic_cell_count || '';
                document.getElementById('edit_quality_grade').value = data.quality_grade;
                document.getElementById('edit_test_result').value = data.test_result;
                document.getElementById('edit_tested_by').value = data.tested_by || '';
                document.getElementById('edit_notes').value = data.notes || '';

                const modal = new bootstrap.Modal(document.getElementById('editQualityModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error loading quality test:', error);
                alert('Error loading quality test details. Please try again.');
            });
    }

    // Update quality test
    function updateQualityTest() {
        const form = document.getElementById('editQualityForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const id = document.getElementById('edit_quality_id').value;
        const formData = new FormData(form);
        const data = {};
        formData.forEach((value, key) => {
            if (key !== 'quality_id') {
                data[key] = value;
            }
        });

        fetch(`/api/quality-tests/${id}`, {
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
                const modal = bootstrap.Modal.getInstance(document.getElementById('editQualityModal'));
                modal.hide();
                loadQualityTests();
                location.reload(); // Reload to update stats
            }
        })
        .catch(error => {
            console.error('Error updating quality test:', error);
            alert('Error updating quality test. Please try again.');
        });
    }

    // Delete quality test
    function deleteQualityTest(id) {
        if (!confirm('Are you sure you want to delete this quality test record?')) {
            return;
        }

        fetch(`/api/quality-tests/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                loadQualityTests();
                location.reload(); // Reload to update stats
            }
        })
        .catch(error => {
            console.error('Error deleting quality test:', error);
            alert('Error deleting quality test. Please try again.');
        });
    }

    // Export functionality
    function exportQualityData() {
        const params = new URLSearchParams();
        const result = document.getElementById('resultFilter').value;
        const grade = document.getElementById('gradeFilter').value;
        const dateRange = document.getElementById('dateRangeFilter').value;
        const search = document.getElementById('qualitySearch').value;

        if (result) params.set('test_result', result);
        if (grade) params.set('quality_grade', grade);
        if (dateRange) params.set('date_range', dateRange);
        if (search) params.set('search', search);

        window.location.href = '/quality-analysis/export?' + params.toString();
    }

    function printQualityReport() {
        const params = new URLSearchParams();
        const result = document.getElementById('resultFilter').value;
        const grade = document.getElementById('gradeFilter').value;
        const dateRange = document.getElementById('dateRangeFilter').value;
        const search = document.getElementById('qualitySearch').value;

        if (result) params.set('test_result', result);
        if (grade) params.set('quality_grade', grade);
        if (dateRange) params.set('date_range', dateRange);
        if (search) params.set('search', search);

        window.open('/quality-analysis/print?' + params.toString(), '_blank');
    }

    // Refresh data
    function refreshQualityData() {
        currentPage = 1;
        clearFilters();
        loadQualityTests();
    }
</script>
@endpush
