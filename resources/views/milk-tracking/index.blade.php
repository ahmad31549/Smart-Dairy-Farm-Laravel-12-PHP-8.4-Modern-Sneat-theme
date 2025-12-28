@extends('layouts.app')

@section('title', 'Milk Tracking')

@push('styles')
<style>
    .border-left-success { border-left: 0.25rem solid var(--success-main) !important; }
    .border-left-primary { border-left: 0.25rem solid var(--primary-600) !important; }
    .border-left-info { border-left: 0.25rem solid var(--info-main) !important; }
    .border-left-warning { border-left: 0.25rem solid var(--warning-main) !important; }

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

    /* Quality badges */
    .quality-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 500;
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
    <h1 class="h2">Milk Production Tracking</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportMilkData()">
                <i class="fas fa-download me-1"></i>Export
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="printMilkReport()">
                <i class="fas fa-print me-1"></i>Print
            </button>
        </div>
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addMilkRecordModal">
            <i class="fas fa-plus me-1"></i>Record Milk Production
        </button>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card card-stats shadow-sm border-0 h-100 mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-vibrant bg-vibrant-success me-3 shadow-success">
                        <i class="fas fa-vial"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted uppercase fs-tiny fw-semibold">Today's Production</h6>
                        <h3 class="mb-0 fw-bold text-vibrant-success" id="todayCount">{{ number_format($todayProduction, 2) }} L</h3>
                    </div>
                </div>
                <small class="text-success fw-medium"><i class="bx bx-check-double me-1"></i> Recorded Today</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stats shadow-sm border-0 h-100 mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-vibrant bg-vibrant-primary me-3 shadow-primary">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted uppercase fs-tiny fw-semibold">This Week</h6>
                        <h3 class="mb-0 fw-bold text-vibrant-primary" id="weekCount">{{ number_format($weekProduction, 2) }} L</h3>
                    </div>
                </div>
                <small class="text-primary fw-medium"><i class="bx bx-trending-up me-1"></i> Weekly Performance</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stats shadow-sm border-0 h-100 mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-vibrant bg-vibrant-info me-3 shadow-info">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted uppercase fs-tiny fw-semibold">This Month</h6>
                        <h3 class="mb-0 fw-bold text-vibrant-info" id="monthCount">{{ number_format($monthProduction, 2) }} L</h3>
                    </div>
                </div>
                <small class="text-info fw-medium"><i class="bx bx-calendar-star me-1"></i> Monthly Aggregated</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stats shadow-sm border-0 h-100 mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-vibrant bg-vibrant-warning me-3 shadow-warning">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted uppercase fs-tiny fw-semibold">Avg Daily</h6>
                        <h3 class="mb-0 fw-bold text-vibrant-warning" id="avgCount">{{ number_format($avgDailyProduction, 2) }} L</h3>
                    </div>
                </div>
                <small class="text-warning fw-medium"><i class="bx bx-line-chart me-1"></i> Daily Efficiency</small>
            </div>
        </div>
    </div>
</div>

<!-- Daily Worker Reports Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow border-left-primary">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-clipboard-list me-2"></i>Daily Herd Reports (Worker Submissions)
                </h6>
                <button class="btn btn-sm btn-outline-primary" onclick="loadDailyWorkerReports()">
                    <i class="fas fa-sync-alt me-1"></i> Refresh
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Date</th>
                                <th>Total Quantity</th>
                                <th>Milking Buffaloes</th>
                                <th>Sick / Pregnant</th>
                                <th>Total Herd</th>
                                <th>Recorded By</th>
                                <th>Submitted</th>
                            </tr>
                        </thead>
                        <tbody id="dailyWorkerReportsBody">
                            <tr>
                                <td colspan="7" class="text-center py-3 text-muted">
                                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                    Loading reports...
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
                    <!-- Quality Grade Filter -->
                    <div class="col-lg-3 col-md-6">
                        <label for="qualityFilter" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-award text-success me-2"></i>Quality Grade
                        </label>
                        <select class="form-select form-select-lg border-2" id="qualityFilter">
                            <option value="">All Grades</option>
                            <option value="A">Grade A</option>
                            <option value="B">Grade B</option>
                            <option value="C">Grade C</option>
                        </select>
                    </div>

                    <!-- Animal Filter -->
                    <div class="col-lg-3 col-md-6">
                        <label for="animalFilter" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-cow text-primary me-2"></i>Animal
                        </label>
                        <select class="form-select form-select-lg border-2" id="animalFilter">
                            <option value="">All Animals</option>
                            @foreach($animals as $animal)
                                <option value="{{ $animal->tag_number }}">{{ $animal->tag_number }} - {{ $animal->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Range Filter -->
                    <div class="col-lg-3 col-md-6">
                        <label for="dateRangeFilter" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-calendar-alt text-info me-2"></i>Date Range
                        </label>
                        <select class="form-select form-select-lg border-2" id="dateRangeFilter">
                            <option value="">All Dates</option>
                            <option value="today">Today</option>
                            <option value="last-7-days">Last 7 Days</option>
                            <option value="last-30-days">Last 30 Days</option>
                            <option value="this-month">This Month</option>
                        </select>
                    </div>

                    <!-- Search Box -->
                    <div class="col-lg-3 col-md-6">
                        <label for="milkSearch" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-search text-warning me-2"></i>Search
                        </label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-white border-2 border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control form-control-lg border-2 border-start-0 ps-0"
                                   id="milkSearch" placeholder="Animal ID, date...">
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

<!-- Milk Production Records Table -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Milk Production Records</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                        data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow">
                        <a class="dropdown-item" href="#" onclick="refreshMilkData()">Refresh Data</a>
                        <a class="dropdown-item" href="#" onclick="exportMilkData()">Export Data</a>
                        <a class="dropdown-item" href="#" onclick="printMilkReport()">Print Report</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="milkTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Animal ID</th>
                                <th>Animal Name</th>
                                <th>Morning (L)</th>
                                <th>Evening (L)</th>
                                <th>Total (L)</th>
                                <th>Fat %</th>
                                <th>Protein %</th>
                                <th>Quality</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="milkBody">
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

<!-- Add Milk Record Modal -->
<div class="modal fade" id="addMilkRecordModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Record Milk Production</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addMilkForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Animal <span class="text-danger">*</span></label>
                            <select class="form-select" name="animal_id" id="animal_id" required>
                                <option value="">Select Animal</option>
                                @foreach($animals as $animal)
                                    <option value="{{ $animal->id }}">{{ $animal->tag_number }} - {{ $animal->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Production Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="production_date" id="production_date" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Morning Production (L) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="morning_quantity" id="morning_quantity" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Evening Production (L) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="evening_quantity" id="evening_quantity" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Fat Content (%)</label>
                            <input type="number" class="form-control" name="fat_content" id="fat_content" step="0.01" min="0" max="100">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Protein Content (%)</label>
                            <input type="number" class="form-control" name="protein_content" id="protein_content" step="0.01" min="0" max="100">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Quality Grade <span class="text-danger">*</span></label>
                            <select class="form-select" name="quality_grade" id="quality_grade" required>
                                <option value="">Select Grade</option>
                                <option value="A">Grade A</option>
                                <option value="B">Grade B</option>
                                <option value="C">Grade C</option>
                            </select>
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
                <button type="button" class="btn btn-primary" onclick="saveMilkRecord()">Save Record</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Milk Record Modal -->
<div class="modal fade" id="editMilkModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Milk Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editMilkForm">
                    @csrf
                    <input type="hidden" name="milk_id" id="edit_milk_id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Animal <span class="text-danger">*</span></label>
                            <select class="form-select" name="animal_id" id="edit_animal_id" required>
                                <option value="">Select Animal</option>
                                @foreach($animals as $animal)
                                    <option value="{{ $animal->id }}">{{ $animal->tag_number }} - {{ $animal->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Production Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="production_date" id="edit_production_date" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Morning Production (L) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="morning_quantity" id="edit_morning_quantity" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Evening Production (L) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="evening_quantity" id="edit_evening_quantity" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Fat Content (%)</label>
                            <input type="number" class="form-control" name="fat_content" id="edit_fat_content" step="0.01" min="0" max="100">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Protein Content (%)</label>
                            <input type="number" class="form-control" name="protein_content" id="edit_protein_content" step="0.01" min="0" max="100">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Quality Grade <span class="text-danger">*</span></label>
                            <select class="form-select" name="quality_grade" id="edit_quality_grade" required>
                                <option value="">Select Grade</option>
                                <option value="A">Grade A</option>
                                <option value="B">Grade B</option>
                                <option value="C">Grade C</option>
                            </select>
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
                <button type="button" class="btn btn-primary" onclick="updateMilkRecord()">Update Record</button>
            </div>
        </div>
    </div>
</div>

<!-- View Milk Record Modal -->
<div class="modal fade" id="viewMilkModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Milk Production Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="milkDetails">
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

    // Load milk records on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadMilkRecords();

        // Set today's date as default
        document.getElementById('production_date').valueAsDate = new Date();

        // Add filter event listeners
        document.getElementById('qualityFilter')?.addEventListener('change', applyFilters);
        document.getElementById('animalFilter')?.addEventListener('change', applyFilters);
        document.getElementById('dateRangeFilter')?.addEventListener('change', applyFilters);
        document.getElementById('milkSearch')?.addEventListener('input', applyFilters);
        
        // Load Daily Worker Reports
        loadDailyWorkerReports();
    });

    // Load Daily Worker Reports (Aggregate)
    function loadDailyWorkerReports() {
        const tbody = document.getElementById('dailyWorkerReportsBody');
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-3">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                    Refreshing...
                </td>
            </tr>
        `;

        fetch('/api/daily-milk-records')
            .then(res => res.json())
            .then(data => {
                tbody.innerHTML = '';
                if (!data || data.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="7" class="text-center text-muted py-3">No daily reports found.</td></tr>`;
                    return;
                }

                data.forEach(record => {
                    const date = new Date(record.date).toLocaleDateString();
                    const created = new Date(record.created_at).toLocaleString();
                    const recorderName = record.recorder ? record.recorder.name : 'Unknown';
                    
                    const row = `
                        <tr>
                            <td class="fw-bold">${date}</td>
                            <td><span class="badge bg-primary text-white" style="font-size: 0.9rem;">${parseFloat(record.total_milk_quantity).toFixed(2)} L</span></td>
                            <td>${record.total_buffaloes_milked}</td>
                            <td>
                                <span class="text-danger fw-bold" title="Sick">${record.sick_animals}</span> / 
                                <span class="text-success fw-bold" title="Pregnant">${record.pregnant_animals}</span>
                            </td>
                            <td>${record.total_herd_size}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-light rounded-circle text-center" style="width:24px;height:24px;line-height:24px;">
                                        <i class="fas fa-user text-secondary" style="font-size:12px;"></i>
                                    </div>
                                    ${recorderName}
                                </div>
                            </td>
                            <td class="small text-muted">${created}</td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });
            })
            .catch(err => {
                console.error('Error loading daily reports:', err);
                tbody.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-3">Error loading reports.</td></tr>`;
            });
    }

    // Load milk production records
    function loadMilkRecords(page = 1) {
        const tbody = document.getElementById('milkBody');
        tbody.innerHTML = `
            <tr>
                <td colspan="10" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Fetching records from server...</p>
                </td>
            </tr>
        `;

        const params = new URLSearchParams();
        params.set('page', page);
        
        const quality = document.getElementById('qualityFilter')?.value;
        const animal = document.getElementById('animalFilter')?.value;
        const dateRange = document.getElementById('dateRangeFilter')?.value;
        const search = document.getElementById('milkSearch')?.value;

        if (quality) params.set('quality_grade', quality);
        if (animal) params.set('animal', animal);
        if (dateRange) params.set('date_range', dateRange);
        if (search) params.set('search', search);

        fetch('/api/milk-tracking/all?' + params.toString())
            .then(response => response.json())
            .then(response => {
                displayRecords(response.data);
                updatePaginationInfo(response.pagination.from || 0, response.pagination.to || 0, response.pagination.total);
                renderPaginationControls(response.pagination.last_page, response.pagination.current_page);
                currentPage = response.pagination.current_page;
            })
            .catch(error => {
                console.error('Error loading milk records:', error);
                tbody.innerHTML = `
                    <tr>
                        <td colspan="10" class="text-center text-danger py-5">
                            <i class="fas fa-exclamation-triangle mb-2" style="font-size: 2rem;"></i>
                            <p>Error loading milk records. Please refresh the page.</p>
                        </td>
                    </tr>
                `;
            });
    }

    function displayRecords(records) {
        const tbody = document.getElementById('milkBody');
        tbody.innerHTML = '';

        if (!records || records.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="10" class="text-center py-5 text-muted">No milk production records found</td>
                </tr>
            `;
            return;
        }

        records.forEach(record => {
            const qualityClass = getQualityClass(record.quality_grade);
            const row = `
                <tr>
                    <td>${record.production_date}</td>
                    <td><span class="badge bg-light text-dark border">${record.animal_id}</span></td>
                    <td>${record.animal_name}</td>
                    <td>${record.morning_quantity} L</td>
                    <td>${record.evening_quantity} L</td>
                    <td><strong class="text-primary">${record.total_quantity} L</strong></td>
                    <td>${record.fat_content}</td>
                    <td>${record.protein_content}</td>
                    <td><span class="quality-badge ${qualityClass}">Grade ${record.quality_grade}</span></td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-primary" onclick="viewMilkRecord(${record.id})" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-warning" onclick="editMilkRecord(${record.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger" onclick="deleteMilkRecord(${record.id})" title="Delete">
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
        loadMilkRecords(page);
        document.getElementById('milkTable').scrollIntoView({ behavior: 'smooth', block: 'start' });
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
            default:
                return 'quality-b';
        }
    }

    // Filter functionality
    function applyFilters() {
        currentPage = 1;
        loadMilkRecords(1);
    }

    function clearFilters() {
        document.getElementById('qualityFilter').value = '';
        document.getElementById('animalFilter').value = '';
        document.getElementById('dateRangeFilter').value = '';
        document.getElementById('milkSearch').value = '';

        currentPage = 1;
        loadMilkRecords(1);
    }

    // Save milk record
    function saveMilkRecord() {
        const form = document.getElementById('addMilkForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const formData = new FormData(form);

        fetch('/milk-tracking', {
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
                const modal = bootstrap.Modal.getInstance(document.getElementById('addMilkRecordModal'));
                modal.hide();
                form.reset();
                document.getElementById('production_date').valueAsDate = new Date();
                loadMilkRecords();
                location.reload(); // Reload to update stats
            }
        })
        .catch(error => {
            console.error('Error saving milk record:', error);
            alert('Error saving milk record. Please try again.');
        });
    }

    // View milk record details
    function viewMilkRecord(id) {
        fetch(`/api/milk-tracking/${id}`)
            .then(response => response.json())
            .then(data => {
                const qualityBadge = `<span class="quality-badge ${getQualityClass(data.quality_grade)}">Grade ${data.quality_grade}</span>`;
                const details = `
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Production Date:</strong> ${data.production_date}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Animal ID:</strong> ${data.animal_tag}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Animal Name:</strong> ${data.animal_name}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Quality Grade:</strong> ${qualityBadge}
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>Morning Production:</strong> ${data.morning_quantity} L
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>Evening Production:</strong> ${data.evening_quantity} L
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>Total Production:</strong> <span class="text-primary fw-bold">${data.total_quantity} L</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Fat Content:</strong> ${data.fat_content ? data.fat_content + '%' : 'N/A'}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Protein Content:</strong> ${data.protein_content ? data.protein_content + '%' : 'N/A'}
                        </div>
                        <div class="col-12 mb-3">
                            <strong>Notes:</strong><br>${data.notes || 'No notes'}
                        </div>
                    </div>
                `;
                document.getElementById('milkDetails').innerHTML = details;

                const modal = new bootstrap.Modal(document.getElementById('viewMilkModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error loading milk record:', error);
                alert('Error loading milk record details. Please try again.');
            });
    }

    // Edit milk record
    function editMilkRecord(id) {
        fetch(`/api/milk-tracking/${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('edit_milk_id').value = data.id;
                document.getElementById('edit_animal_id').value = data.animal_id;
                document.getElementById('edit_production_date').value = data.production_date;
                document.getElementById('edit_morning_quantity').value = data.morning_quantity;
                document.getElementById('edit_evening_quantity').value = data.evening_quantity;
                document.getElementById('edit_fat_content').value = data.fat_content || '';
                document.getElementById('edit_protein_content').value = data.protein_content || '';
                document.getElementById('edit_quality_grade').value = data.quality_grade;
                document.getElementById('edit_notes').value = data.notes || '';

                const modal = new bootstrap.Modal(document.getElementById('editMilkModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error loading milk record:', error);
                alert('Error loading milk record details. Please try again.');
            });
    }

    // Update milk record
    function updateMilkRecord() {
        const form = document.getElementById('editMilkForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const id = document.getElementById('edit_milk_id').value;
        const formData = new FormData(form);
        const data = {};
        formData.forEach((value, key) => {
            if (key !== 'milk_id') {
                data[key] = value;
            }
        });

        fetch(`/api/milk-tracking/${id}`, {
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
                const modal = bootstrap.Modal.getInstance(document.getElementById('editMilkModal'));
                modal.hide();
                loadMilkRecords();
                location.reload(); // Reload to update stats
            }
        })
        .catch(error => {
            console.error('Error updating milk record:', error);
            alert('Error updating milk record. Please try again.');
        });
    }

    // Delete milk record
    function deleteMilkRecord(id) {
        if (!confirm('Are you sure you want to delete this milk production record?')) {
            return;
        }

        fetch(`/api/milk-tracking/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                loadMilkRecords();
                location.reload(); // Reload to update stats
            }
        })
        .catch(error => {
            console.error('Error deleting milk record:', error);
            alert('Error deleting milk record. Please try again.');
        });
    }

    // Export functionality
    function exportMilkData() {
        const params = new URLSearchParams();
        const quality = document.getElementById('qualityFilter').value;
        const animal = document.getElementById('animalFilter').value;
        const dateRange = document.getElementById('dateRangeFilter').value;
        const search = document.getElementById('milkSearch').value;

        if (quality) params.set('quality_grade', quality);
        if (animal) params.set('animal', animal);
        if (dateRange) params.set('date_range', dateRange);
        if (search) params.set('search', search);

        window.location.href = '/milk-tracking/export?' + params.toString();
    }

    function printMilkReport() {
        const params = new URLSearchParams();
        const quality = document.getElementById('qualityFilter').value;
        const animal = document.getElementById('animalFilter').value;
        const dateRange = document.getElementById('dateRangeFilter').value;
        const search = document.getElementById('milkSearch').value;

        if (quality) params.set('quality_grade', quality);
        if (animal) params.set('animal', animal);
        if (dateRange) params.set('date_range', dateRange);
        if (search) params.set('search', search);

        window.open('/milk-tracking/print?' + params.toString(), '_blank');
    }

    // Refresh data
    function refreshMilkData() {
        currentPage = 1;
        clearFilters();
        loadMilkRecords();
    }
</script>
@endpush
