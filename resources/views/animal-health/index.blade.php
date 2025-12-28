@extends('layouts.app')

@section('title', 'Animal Health Monitoring')

@push('styles')
<style>
    .border-left-success { border-left: 0.25rem solid var(--success-main) !important; }
    .border-left-warning { border-left: 0.25rem solid var(--warning-main) !important; }
    .border-left-danger { border-left: 0.25rem solid var(--danger-main) !important; }
    .border-left-info { border-left: 0.25rem solid var(--info-main) !important; }

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

    .card {
        transition: all 0.3s ease;
    }

    /* Filter Icons */
    .form-label i {
        font-size: 1rem;
    }

    /* Clear Filters Button */
    .btn-outline-secondary {
        border-width: 2px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-outline-secondary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
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

    /* Input Group Styling */
    .input-group:focus-within .input-group-text {
        border-color: #4e73df;
        background-color: #f8f9fc;
    }

    .input-group:focus-within .form-control {
        border-color: #4e73df;
    }

    /* Select Dropdown Styling */
    .form-select option {
        padding: 0.5rem;
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

        .form-select-lg, .form-control-lg {
            font-size: 0.875rem;
        }
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
    <h1 class="h2">Animal Health Monitoring</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportHealthData()">
                <i class="fas fa-download me-1"></i>Export
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="printHealthReport()">
                <i class="fas fa-print me-1"></i>Print
            </button>
        </div>
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addHealthRecordModal">
            <i class="fas fa-plus me-1"></i>Add Health Record
        </button>
    </div>
</div>

<!-- Health Overview Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-vibrant bg-vibrant-success me-3 shadow-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted uppercase fs-tiny fw-semibold">Healthy Animals</h6>
                        <h3 class="mb-0 fw-bold text-vibrant-success" id="healthy-count">{{ $healthyCount }}</h3>
                    </div>
                </div>
                <small class="text-success fw-medium"><i class="bx bx-check-double me-1"></i> System Verified</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-vibrant bg-vibrant-warning me-3 shadow-warning">
                        <i class="fas fa-stethoscope"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted uppercase fs-tiny fw-semibold">Under Treatment</h6>
                        <h3 class="mb-0 fw-bold text-vibrant-warning" id="treatment-count">{{ $treatmentCount }}</h3>
                    </div>
                </div>
                <small class="text-warning fw-medium"><i class="bx bx-plus-medical me-1"></i> Active Monitoring</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-vibrant bg-vibrant-danger me-3 shadow-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted uppercase fs-tiny fw-semibold">Critical Alert</h6>
                        <h3 class="mb-0 fw-bold text-vibrant-danger" id="critical-count">{{ $criticalCount }}</h3>
                    </div>
                </div>
                <small class="text-danger fw-medium"><i class="bx bx-error me-1"></i> Requires Attention</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-vibrant bg-vibrant-info me-3 shadow-info">
                        <i class="fas fa-syringe"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted uppercase fs-tiny fw-semibold">Vaccinations Due</h6>
                        <h3 class="mb-0 fw-bold text-vibrant-info" id="vaccination-due">{{ $vaccinationDue }}</h3>
                    </div>
                </div>
                <small class="text-info fw-medium"><i class="bx bx-alarm me-1"></i> Scheduled Soon</small>
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

                    <!-- Age Group Filter -->
                    <div class="col-lg-3 col-md-6">
                        <label for="ageGroupFilter" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-calendar-alt text-info me-2"></i>Age Group
                        </label>
                        <select class="form-select form-select-lg border-2" id="ageGroupFilter">
                            <option value="">All Ages</option>
                            <option value="calf">Calf (0-1 year)</option>
                            <option value="heifer">Heifer (1-2 years)</option>
                            <option value="cow">Cow (2+ years)</option>
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
                                   id="animalSearch" placeholder="ID, name, or tag...">
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

<!-- Health Records Table -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Animal Health Records</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                        data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow">
                        <a class="dropdown-item" href="#" onclick="refreshHealthData()">Refresh Data</a>
                        <a class="dropdown-item" href="#" onclick="exportHealthData()">Export Data</a>
                        <a class="dropdown-item" href="#" onclick="printHealthReport()">Print Report</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="healthRecordsTable">
                        <thead>
                            <tr>
                                <th>Animal ID</th>
                                <th>Name/Tag</th>
                                <th>Breed</th>
                                <th>Age</th>
                                <th>Health Status</th>
                                <th>Last Check</th>
                                <th>Next Check</th>
                                <th>Veterinarian</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="healthRecordsBody">
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

<!-- Add Health Record Modal -->
<div class="modal fade" id="addHealthRecordModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Health Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addHealthRecordForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="animalId" class="form-label">Animal ID *</label>
                            <select class="form-select" id="animalId" name="animal_id" required>
                                <option value="">Select Animal</option>
                                @foreach($animals as $animal)
                                    <option value="{{ $animal->id }}">{{ $animal->animal_id }} - {{ $animal->name }} ({{ $animal->tag_number }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="healthStatus" class="form-label">Health Status *</label>
                            <select class="form-select" id="healthStatus" required>
                                <option value="">Select Status</option>
                                <option value="healthy">Healthy</option>
                                <option value="treatment">Under Treatment</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="checkDate" class="form-label">Check Date *</label>
                            <input type="date" class="form-control" id="checkDate" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nextCheckDate" class="form-label">Next Check Date</label>
                            <input type="date" class="form-control" id="nextCheckDate">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="veterinarian" class="form-label">Veterinarian</label>
                            <input type="text" class="form-control" id="veterinarian" placeholder="Dr. Smith">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="temperature" class="form-label">Temperature (°F)</label>
                            <input type="number" class="form-control" id="temperature" step="0.1" placeholder="101.5">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="symptoms" class="form-label">Symptoms/Observations</label>
                        <textarea class="form-control" id="symptoms" rows="3" placeholder="Describe any symptoms or observations..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="treatment" class="form-label">Treatment/Medication</label>
                        <textarea class="form-control" id="treatment" rows="3" placeholder="Describe treatment or medication given..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Additional Notes</label>
                        <textarea class="form-control" id="notes" rows="2" placeholder="Any additional notes..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveHealthRecord()">Save Health Record</button>
            </div>
        </div>
    </div>
</div>

<!-- View Health Record Modal -->
<div class="modal fade" id="viewHealthRecordModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Health Record Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="healthRecordDetails">
                <!-- Details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Health Record Modal -->
<div class="modal fade" id="editHealthRecordModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Health Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editHealthRecordForm">
                    <input type="hidden" id="editRecordId">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editAnimalId" class="form-label">Animal ID *</label>
                            <select class="form-select" id="editAnimalId" name="animal_id" required>
                                <option value="">Select Animal</option>
                                @foreach($animals as $animal)
                                    <option value="{{ $animal->id }}">{{ $animal->animal_id }} - {{ $animal->name }} ({{ $animal->tag_number }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editHealthStatus" class="form-label">Health Status *</label>
                            <select class="form-select" id="editHealthStatus" name="health_status" required>
                                <option value="">Select Status</option>
                                <option value="healthy">Healthy</option>
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
                            <input type="text" class="form-control" id="editVeterinarian" name="veterinarian" placeholder="Dr. Smith">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editTemperature" class="form-label">Temperature (°F)</label>
                            <input type="number" class="form-control" id="editTemperature" name="temperature" step="0.1" placeholder="101.5">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="editSymptoms" class="form-label">Symptoms/Observations</label>
                        <textarea class="form-control" id="editSymptoms" name="symptoms" rows="3" placeholder="Describe any symptoms or observations..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editTreatment" class="form-label">Treatment/Medication</label>
                        <textarea class="form-control" id="editTreatment" name="treatment" rows="3" placeholder="Describe treatment or medication given..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editNotes" class="form-label">Additional Notes</label>
                        <textarea class="form-control" id="editNotes" name="notes" rows="2" placeholder="Any additional notes..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updateHealthRecord()">Update Health Record</button>
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

    // Load health records on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadHealthRecords();

        // Add event listeners for filters
        document.getElementById('healthStatusFilter').addEventListener('change', applyFilters);
        document.getElementById('breedFilter').addEventListener('change', applyFilters);
        document.getElementById('ageGroupFilter').addEventListener('change', applyFilters);
        document.getElementById('animalSearch').addEventListener('input', debounce(applyFilters, 500));
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
        loadHealthRecords(1);
    }

    function loadHealthRecords(page = 1) {
        const tbody = document.getElementById('healthRecordsBody');
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
        const ageGroup = document.getElementById('ageGroupFilter').value;
        const search = document.getElementById('animalSearch').value;

        if (status) params.set('health_status', status);
        if (breed) params.set('breed', breed);
        if (ageGroup) params.set('age_group', ageGroup);
        if (search) params.set('search', search);

        fetch('/api/animal-health/records?' + params.toString())
            .then(response => response.json())
            .then(response => {
                displayRecords(response.data);
                updatePaginationInfo(response.pagination.from || 0, response.pagination.to || 0, response.pagination.total);
                renderPaginationControls(response.pagination.last_page, response.pagination.current_page);
                currentPage = response.pagination.current_page;
            })
            .catch(error => {
                console.error('Error loading health records:', error);
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" class="text-center text-danger py-5">Error loading health records</td>
                    </tr>
                `;
            });
    }

    function displayRecords(records) {
        const tbody = document.getElementById('healthRecordsBody');
        tbody.innerHTML = '';

        if (!records || records.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center py-5">No health records found</td>
                </tr>
            `;
            return;
        }

        records.forEach(record => {
            const animal = record.animal;
            const age = animal ? calculateAge(animal.birth_date) : '-';
            const statusBadge = getStatusBadge(record.health_status);

            const row = `
                <tr>
                    <td>${animal ? animal.animal_id : 'N/A'}</td>
                    <td>${animal ? animal.name : 'Unknown'}<br><small class="text-muted">${animal ? animal.tag_number : '-'}</small></td>
                    <td>${animal ? capitalizeFirst(animal.breed) : '-'}</td>
                    <td>${age} years</td>
                    <td>${statusBadge}</td>
                    <td>${formatDate(record.check_date)}</td>
                    <td>${record.next_check_date ? formatDate(record.next_check_date) : '-'}</td>
                    <td>${record.veterinarian || '-'}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-sm btn-primary" onclick="viewHealthRecord(${record.id})" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-info" onclick="editHealthRecord(${record.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteHealthRecord(${record.id})" title="Delete">
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
        loadHealthRecords(page);
        document.getElementById('healthRecordsTable').scrollIntoView({ behavior: 'smooth', block: 'start' });
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

    function exportHealthData() {
        const params = new URLSearchParams();
        const status = document.getElementById('healthStatusFilter').value;
        const breed = document.getElementById('breedFilter').value;
        const ageGroup = document.getElementById('ageGroupFilter').value;
        const search = document.getElementById('animalSearch').value;

        if (status) params.set('health_status', status);
        if (breed) params.set('breed', breed);
        if (ageGroup) params.set('age_group', ageGroup);
        if (search) params.set('search', search);

        const url = '/animal-health/export' + (params.toString() ? ('?' + params.toString()) : '');
        window.location.href = url;
    }

    function printHealthReport() {
        const params = new URLSearchParams();
        const status = document.getElementById('healthStatusFilter').value;
        const breed = document.getElementById('breedFilter').value;
        const ageGroup = document.getElementById('ageGroupFilter').value;
        const search = document.getElementById('animalSearch').value;

        if (status) params.set('health_status', status);
        if (breed) params.set('breed', breed);
        if (ageGroup) params.set('age_group', ageGroup);
        if (search) params.set('search', search);

        const url = '/animal-health/print' + (params.toString() ? ('?' + params.toString()) : '');
        window.open(url, '_blank');
    }

    function refreshHealthData() {
        currentPage = 1;
        loadHealthRecords();
    }

    function saveHealthRecord() {
        const form = document.getElementById('addHealthRecordForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const formData = {
            animal_id: document.getElementById('animalId').value,
            health_status: document.getElementById('healthStatus').value,
            check_date: document.getElementById('checkDate').value,
            next_check_date: document.getElementById('nextCheckDate').value,
            veterinarian: document.getElementById('veterinarian').value,
            temperature: document.getElementById('temperature').value,
            symptoms: document.getElementById('symptoms').value,
            treatment: document.getElementById('treatment').value,
            notes: document.getElementById('notes').value
        };

        fetch('/api/animal-health', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
            alert('Health record saved successfully!');
            const modal = bootstrap.Modal.getInstance(document.getElementById('addHealthRecordModal'));
            modal.hide();
            form.reset();
                loadHealthRecords();
                location.reload(); // Reload to update counts
            }
        })
        .catch(error => {
            console.error('Error saving health record:', error);
            alert('Error saving health record. Please try again.');
        });
    }

    function viewHealthRecord(id) {
        fetch(`/api/animal-health/records/${id}`)
            .then(response => response.json())
            .then(record => {
                const animal = record.animal;
                const details = `
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Animal ID:</strong> ${animal.animal_id}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Name:</strong> ${animal.name}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Tag Number:</strong> ${animal.tag_number}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Breed:</strong> ${capitalizeFirst(animal.breed)}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Health Status:</strong> ${getStatusBadge(record.health_status)}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Check Date:</strong> ${formatDate(record.check_date)}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Next Check:</strong> ${record.next_check_date ? formatDate(record.next_check_date) : '-'}
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
                document.getElementById('healthRecordDetails').innerHTML = details;
                const modal = new bootstrap.Modal(document.getElementById('viewHealthRecordModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error loading health record details:', error);
                alert('Error loading health record details. Please try again.');
            });
    }

    function editHealthRecord(id) {
        // Load record data and open edit modal
        fetch(`/api/animal-health/records/${id}`)
            .then(response => response.json())
            .then(record => {
                // Populate form fields
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

                // Open modal
                const modal = new bootstrap.Modal(document.getElementById('editHealthRecordModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error loading health record:', error);
                alert('Error loading health record. Please try again.');
            });
    }

    function updateHealthRecord() {
        const form = document.getElementById('editHealthRecordForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const recordId = document.getElementById('editRecordId').value;
        const formData = {
            animal_id: document.getElementById('editAnimalId').value,
            health_status: document.getElementById('editHealthStatus').value,
            check_date: document.getElementById('editCheckDate').value,
            next_check_date: document.getElementById('editNextCheckDate').value,
            veterinarian: document.getElementById('editVeterinarian').value,
            temperature: document.getElementById('editTemperature').value,
            symptoms: document.getElementById('editSymptoms').value,
            treatment: document.getElementById('editTreatment').value,
            notes: document.getElementById('editNotes').value
        };

        fetch(`/api/animal-health/records/${recordId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Health record updated successfully!');
                const modal = bootstrap.Modal.getInstance(document.getElementById('editHealthRecordModal'));
                modal.hide();
                loadHealthRecords();
                location.reload(); // Reload to update counts
            } else {
                alert('Error updating health record: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error updating health record:', error);
            alert('Error updating health record. Please try again.');
        });
    }

    function deleteHealthRecord(id) {
        if (confirm('Are you sure you want to delete this health record?')) {
            fetch(`/api/animal-health/records/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Health record deleted successfully!');
                    loadHealthRecords();
                    location.reload(); // Reload to update counts
                } else {
                    alert('Error deleting health record: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error deleting health record:', error);
                alert('Error deleting health record. Please try again.');
            });
        }
    }

</script>
@endpush

