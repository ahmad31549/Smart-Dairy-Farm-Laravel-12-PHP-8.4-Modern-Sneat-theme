@extends('layouts.app')

@section('title', 'Medical History')

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
    <h1 class="h2">Medical History</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportMedicalHistory()">
                <i class="fas fa-download me-1"></i>Export
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="printMedicalHistory()">
                <i class="fas fa-print me-1"></i>Print
            </button>
        </div>
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addMedicalRecordModal">
            <i class="fas fa-plus me-1"></i>Add Medical Record
        </button>
    </div>
</div>

<!-- Health Overview Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Total Records</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-records">{{ $totalRecords }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-file-medical fa-2x text-gray-300"></i>
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
                            Recovered</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="recovered-count">{{ $healthyCount }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-heart fa-2x text-gray-300"></i>
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
                            Under Treatment</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="treatment-count">{{ $treatmentCount }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-ambulance fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Critical Cases</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="critical-count">{{ $criticalCount }}</div>
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
                    <!-- Health Status Filter -->
                    <div class="col-lg-3 col-md-6">
                        <label for="healthStatusFilter" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-heartbeat text-primary me-2"></i>Health Status
                        </label>
                        <select class="form-select form-select-lg border-2" id="healthStatusFilter">
                            <option value="">All Status</option>
                            <option value="healthy">✓ Healthy</option>
                            <option value="treatment">⚕ Under Treatment</option>
                            <option value="critical">⚠ Critical</option>
                        </select>
                    </div>

                    <!-- Breed Filter -->
                    <div class="col-lg-3 col-md-6">
                        <label for="breedFilter" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-paw text-success me-2"></i>Breed
                        </label>
                        <select class="form-select form-select-lg border-2" id="breedFilter">
                            <option value="">All Breeds</option>
                            <option value="holstein">Holstein</option>
                            <option value="jersey">Jersey</option>
                            <option value="guernsey">Guernsey</option>
                            <option value="ayrshire">Ayrshire</option>
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
                        <label for="animalSearch" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-search text-warning me-2"></i>Search
                        </label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-white border-2 border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control form-control-lg border-2 border-start-0 ps-0"
                                   id="animalSearch" placeholder="Animal ID or name...">
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

<!-- Medical Records Table -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Medical Records History</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                        data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow">
                        <a class="dropdown-item" href="#" onclick="refreshMedicalData()">Refresh Data</a>
                        <a class="dropdown-item" href="#" onclick="exportMedicalHistory()">Export Data</a>
                        <a class="dropdown-item" href="#" onclick="printMedicalHistory()">Print Report</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="medicalHistoryTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Animal ID</th>
                                <th>Name/Tag</th>
                                <th>Breed</th>
                                <th>Status</th>
                                <th>Symptoms</th>
                                <th>Treatment</th>
                                <th>Veterinarian</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="medicalHistoryBody">
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

<!-- Add Medical Record Modal -->
<div class="modal fade" id="addMedicalRecordModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Medical Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addMedicalRecordForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="animalId" class="form-label">Animal *</label>
                            <select class="form-select" id="animalId" name="animal_id" required>
                                <option value="">Select Animal</option>
                                @foreach($animals as $animal)
                                    <option value="{{ $animal->id }}">{{ $animal->animal_id }} - {{ $animal->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="healthStatus" class="form-label">Health Status *</label>
                            <select class="form-select" id="healthStatus" name="health_status" required>
                                <option value="">Select Status</option>
                                <option value="healthy">Healthy/Recovered</option>
                                <option value="treatment">Under Treatment</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="checkDate" class="form-label">Check Date *</label>
                            <input type="date" class="form-control" id="checkDate" name="check_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nextCheckDate" class="form-label">Next Check Date</label>
                            <input type="date" class="form-control" id="nextCheckDate" name="next_check_date">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="veterinarian" class="form-label">Veterinarian</label>
                            <input type="text" class="form-control" id="veterinarian" name="veterinarian" placeholder="Dr. Smith">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="temperature" class="form-label">Temperature (°F)</label>
                            <input type="number" class="form-control" id="temperature" name="temperature" step="0.1" placeholder="101.5">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="symptoms" class="form-label">Symptoms/Observations</label>
                        <textarea class="form-control" id="symptoms" name="symptoms" rows="3" placeholder="Describe symptoms or observations..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="treatment" class="form-label">Treatment/Medication</label>
                        <textarea class="form-control" id="treatment" name="treatment" rows="3" placeholder="Describe treatment or medication given..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Additional Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Any additional notes..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveMedicalRecord()">Save Record</button>
            </div>
        </div>
    </div>
</div>

<!-- View Medical Record Modal -->
<div class="modal fade" id="viewMedicalRecordModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Medical Record Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="medicalRecordDetails">
                <!-- Details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Medical Record Modal -->
<div class="modal fade" id="editMedicalRecordModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Medical Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editMedicalRecordForm">
                    @csrf
                    <input type="hidden" id="editRecordId">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editAnimalId" class="form-label">Animal *</label>
                            <select class="form-select" id="editAnimalId" name="animal_id" required>
                                <option value="">Select Animal</option>
                                @foreach($animals as $animal)
                                    <option value="{{ $animal->id }}">{{ $animal->animal_id }} - {{ $animal->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editHealthStatus" class="form-label">Health Status *</label>
                            <select class="form-select" id="editHealthStatus" name="health_status" required>
                                <option value="">Select Status</option>
                                <option value="healthy">Healthy/Recovered</option>
                                <option value="treatment">Under Treatment</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editCheckDate" class="form-label">Check Date *</label>
                            <input type="date" class="form-control" id="editCheckDate" name="check_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editNextCheckDate" class="form-label">Next Check Date</label>
                            <input type="date" class="form-control" id="editNextCheckDate" name="next_check_date">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editVeterinarian" class="form-label">Veterinarian</label>
                            <input type="text" class="form-control" id="editVeterinarian" name="veterinarian">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editTemperature" class="form-label">Temperature (°F)</label>
                            <input type="number" class="form-control" id="editTemperature" name="temperature" step="0.1">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="editSymptoms" class="form-label">Symptoms/Observations</label>
                        <textarea class="form-control" id="editSymptoms" name="symptoms" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editTreatment" class="form-label">Treatment/Medication</label>
                        <textarea class="form-control" id="editTreatment" name="treatment" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editNotes" class="form-label">Additional Notes</label>
                        <textarea class="form-control" id="editNotes" name="notes" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updateMedicalRecord()">Update Record</button>
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

    // Load medical records on page load
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('checkDate').value = today;
        loadMedicalRecords();

        // Add filter event listeners
        document.getElementById('healthStatusFilter')?.addEventListener('change', applyFilters);
        document.getElementById('breedFilter')?.addEventListener('change', applyFilters);
        document.getElementById('dateRangeFilter')?.addEventListener('change', applyFilters);
        document.getElementById('animalSearch')?.addEventListener('input', debounce(applyFilters, 500));
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
        loadMedicalRecords(1);
    }

    function loadMedicalRecords(page = 1) {
        const tbody = document.getElementById('medicalHistoryBody');
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
        
        const status = document.getElementById('healthStatusFilter').value;
        const breed = document.getElementById('breedFilter').value;
        const dateRange = document.getElementById('dateRangeFilter').value;
        const search = document.getElementById('animalSearch').value;

        if (status) params.set('status', status);
        if (breed) params.set('breed', breed);
        if (dateRange) params.set('date_range', dateRange);
        if (search) params.set('search', search);

        fetch('/api/medical-history/all?' + params.toString())
            .then(response => response.json())
            .then(response => {
                displayRecords(response.data);
                updatePaginationInfo(response.pagination.from || 0, response.pagination.to || 0, response.pagination.total);
                renderPaginationControls(response.pagination.last_page, response.pagination.current_page);
                currentPage = response.pagination.current_page;
            })
            .catch(error => {
                console.error('Error loading medical records:', error);
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" class="text-center text-danger py-5">Error loading medical records</td>
                    </tr>
                `;
            });
    }

    function displayRecords(records) {
        const tbody = document.getElementById('medicalHistoryBody');
        tbody.innerHTML = '';

        if (!records || records.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center py-5">No medical records found</td>
                </tr>
            `;
            return;
        }

        records.forEach(record => {
            const animal = record.animal;
            const statusBadge = getStatusBadge(record.health_status);

            const row = `
                <tr>
                    <td>${formatDate(record.check_date)}</td>
                    <td>${animal ? animal.animal_id : 'N/A'}</td>
                    <td>${animal ? animal.name : 'Unknown'}<br><small class="text-muted">${animal ? animal.tag_number : '-'}</small></td>
                    <td>${animal ? capitalizeFirst(animal.breed) : '-'}</td>
                    <td>${statusBadge}</td>
                    <td>${record.symptoms ? (record.symptoms.length > 40 ? record.symptoms.substring(0, 40) + '...' : record.symptoms) : '-'}</td>
                    <td>${record.treatment ? (record.treatment.length > 40 ? record.treatment.substring(0, 40) + '...' : record.treatment) : '-'}</td>
                    <td>${record.veterinarian || '-'}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-sm btn-primary" onclick="viewRecord(${record.id})" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-info" onclick="editRecord(${record.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteRecord(${record.id})" title="Delete">
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
        loadMedicalRecords(page);
        document.getElementById('medicalHistoryTable').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function calculateAge(birthDate) {
        const birth = new Date(birthDate);
        const today = new Date();
        let age = today.getFullYear() - birth.getFullYear();
        const monthDiff = today.getMonth() - birth.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
            age--;
        }
        return age;
    }

    function getStatusBadge(status) {
        const badges = {
            'healthy': '<span class="badge bg-success">Healthy</span>',
            'treatment': '<span class="badge bg-warning">Treatment</span>',
            'critical': '<span class="badge bg-danger">Critical</span>'
        };
        return badges[status] || status;
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    }

    function capitalizeFirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    // Filter functionality
    document.getElementById('healthStatusFilter')?.addEventListener('change', applyFilters);
    document.getElementById('breedFilter')?.addEventListener('change', applyFilters);
    document.getElementById('dateRangeFilter')?.addEventListener('change', applyFilters);
    document.getElementById('animalSearch')?.addEventListener('input', applyFilters);


    function clearFilters() {
        document.getElementById('healthStatusFilter').value = '';
        document.getElementById('breedFilter').value = '';
        document.getElementById('dateRangeFilter').value = '';
        document.getElementById('animalSearch').value = '';

        currentPage = 1;
        loadMedicalRecords(1);
    }

    function refreshMedicalData() {
        currentPage = 1;
        loadMedicalRecords();
    }

    function exportMedicalHistory() {
        const params = new URLSearchParams();
        const status = document.getElementById('healthStatusFilter').value;
        const breed = document.getElementById('breedFilter').value;
        const dateRange = document.getElementById('dateRangeFilter').value;
        const search = document.getElementById('animalSearch').value;

        if (status) params.set('health_status', status);
        if (breed) params.set('breed', breed);
        if (dateRange) params.set('date_range', dateRange);
        if (search) params.set('search', search);

        window.location.href = '/medical-history/export?' + params.toString();
    }

    function printMedicalHistory() {
        const params = new URLSearchParams();
        const status = document.getElementById('healthStatusFilter').value;
        const breed = document.getElementById('breedFilter').value;
        const dateRange = document.getElementById('dateRangeFilter').value;
        const search = document.getElementById('animalSearch').value;

        if (status) params.set('health_status', status);
        if (breed) params.set('breed', breed);
        if (dateRange) params.set('date_range', dateRange);
        if (search) params.set('search', search);

        window.open('/medical-history/print?' + params.toString(), '_blank');
    }

    function saveMedicalRecord() {
        const form = document.getElementById('addMedicalRecordForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const formData = new FormData(form);
        const data = Object.fromEntries(formData);

        fetch('/api/medical-history', {
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
                alert('Medical record saved successfully!');
                const modal = bootstrap.Modal.getInstance(document.getElementById('addMedicalRecordModal'));
                modal.hide();
                form.reset();
                loadMedicalRecords(1); // Refresh the first page
            } else {
                alert('Error: ' + (data.message || 'Failed to save record'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving medical record. Please try again.');
        });
    }

    function viewRecord(id) {
        fetch(`/api/medical-history/${id}`)
            .then(response => response.json())
            .then(record => {
                const animal = record.animal;
                const statusBadge = record.health_status === 'healthy' ? '<span class="badge bg-success">Healthy</span>' :
                                  record.health_status === 'treatment' ? '<span class="badge bg-warning">Under Treatment</span>' :
                                  '<span class="badge bg-danger">Critical</span>';

                const details = `
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Animal ID:</strong> ${animal.animal_id}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Animal Name:</strong> ${animal.name}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Tag Number:</strong> ${animal.tag_number}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Breed:</strong> ${animal.breed.charAt(0).toUpperCase() + animal.breed.slice(1)}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Health Status:</strong> ${statusBadge}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Check Date:</strong> ${new Date(record.check_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Next Check:</strong> ${record.next_check_date ? new Date(record.next_check_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : '-'}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Veterinarian:</strong> ${record.veterinarian || '-'}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Temperature:</strong> ${record.temperature ? record.temperature + '°F' : '-'}
                        </div>
                        <div class="col-12 mb-3">
                            <strong>Symptoms:</strong><br>${record.symptoms || '-'}
                        </div>
                        <div class="col-12 mb-3">
                            <strong>Treatment:</strong><br>${record.treatment || '-'}
                        </div>
                        <div class="col-12 mb-3">
                            <strong>Notes:</strong><br>${record.notes || '-'}
                        </div>
                    </div>
                `;
                document.getElementById('medicalRecordDetails').innerHTML = details;
                const modal = new bootstrap.Modal(document.getElementById('viewMedicalRecordModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading medical record details.');
            });
    }

    function editRecord(id) {
        fetch(`/api/medical-history/${id}`)
            .then(response => response.json())
            .then(record => {
                document.getElementById('editRecordId').value = record.id;
                document.getElementById('editAnimalId').value = record.animal_id;
                document.getElementById('editHealthStatus').value = record.health_status;
                document.getElementById('editCheckDate').value = record.check_date.split('T')[0];
                document.getElementById('editNextCheckDate').value = record.next_check_date ? record.next_check_date.split('T')[0] : '';
                document.getElementById('editVeterinarian').value = record.veterinarian || '';
                document.getElementById('editTemperature').value = record.temperature || '';
                document.getElementById('editSymptoms').value = record.symptoms || '';
                document.getElementById('editTreatment').value = record.treatment || '';
                document.getElementById('editNotes').value = record.notes || '';

                const modal = new bootstrap.Modal(document.getElementById('editMedicalRecordModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading medical record.');
            });
    }

    function updateMedicalRecord() {
        const form = document.getElementById('editMedicalRecordForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const recordId = document.getElementById('editRecordId').value;
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);

        fetch(`/api/medical-history/${recordId}`, {
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
                alert('Medical record updated successfully!');
                const modal = bootstrap.Modal.getInstance(document.getElementById('editMedicalRecordModal'));
                modal.hide();
                loadMedicalRecords(currentPage); // Refresh current page
            } else {
                alert('Error: ' + (data.message || 'Failed to update record'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating medical record.');
        });
    }

    function deleteRecord(id) {
        if (confirm('Are you sure you want to delete this medical record? This action cannot be undone.')) {
            fetch(`/api/medical-history/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Medical record deleted successfully!');
                    loadMedicalRecords(currentPage); // Refresh current page
                } else {
                    alert('Error: ' + (data.message || 'Failed to delete record'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting medical record.');
            });
        }
    }

</script>
@endpush
