@extends('layouts.app')

@section('title', 'Animal Lifecycle Status')

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

    /* Lifecycle Stage Badge */
    .stage-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.75rem;
        font-weight: 600;
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

    @media (max-width: 991px) {
        .form-select-lg, .form-control-lg {
            font-size: 0.9rem;
            padding: 0.5rem 0.75rem;
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
    }
</style>
@endpush

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-chart-line"></i> Animal Lifecycle Status</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportLifecycle()">
                <i class="fas fa-download me-1"></i>Export
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="printLifecycle()">
                <i class="fas fa-print me-1"></i>Print
            </button>
        </div>
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addAnimalModal">
            <i class="fas fa-plus me-1"></i>Add Animal
        </button>
    </div>
</div>

<!-- Lifecycle Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Animals</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-animals">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-paw fa-2x text-gray-300"></i>
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
                            Active/Healthy</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="active-count">0</div>
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
                            Monitoring Required</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="monitoring-count">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-eye fa-2x text-gray-300"></i>
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
                            Avg Age (Years)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="avg-age">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
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
                            <i class="fas fa-filter text-primary me-2"></i>Status
                        </label>
                        <select class="form-select form-select-lg border-2" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="active">✓ Active</option>
                            <option value="pregnant">🤰 Pregnant</option>
                            <option value="sick">🏥 Sick</option>
                            <option value="dry">⏸ Dry Period</option>
                            <option value="sold">💰 Sold</option>
                        </select>
                    </div>

                    <!-- Gender Filter -->
                    <div class="col-lg-2 col-md-6">
                        <label for="genderFilter" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-venus-mars text-success me-2"></i>Gender
                        </label>
                        <select class="form-select form-select-lg border-2" id="genderFilter">
                            <option value="">All</option>
                            <option value="female">Female</option>
                            <option value="male">Male</option>
                        </select>
                    </div>

                    <!-- Breed Filter -->
                    <div class="col-lg-2 col-md-6">
                        <label for="breedFilter" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-dna text-info me-2"></i>Breed
                        </label>
                        <select class="form-select form-select-lg border-2" id="breedFilter">
                            <option value="">All Breeds</option>
                            <option value="holstein">Holstein</option>
                            <option value="jersey">Jersey</option>
                            <option value="guernsey">Guernsey</option>
                            <option value="ayrshire">Ayrshire</option>
                        </select>
                    </div>

                    <!-- Age Range Filter -->
                    <div class="col-lg-2 col-md-6">
                        <label for="ageFilter" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-calendar text-warning me-2"></i>Age Range
                        </label>
                        <select class="form-select form-select-lg border-2" id="ageFilter">
                            <option value="">All Ages</option>
                            <option value="calf">Calf (0-1y)</option>
                            <option value="heifer">Heifer (1-2y)</option>
                            <option value="adult">Adult (2-5y)</option>
                            <option value="mature">Mature (5+y)</option>
                        </select>
                    </div>

                    <!-- Search Box -->
                    <div class="col-lg-3 col-md-6">
                        <label for="animalSearch" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-search text-danger me-2"></i>Search
                        </label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-white border-2 border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control form-control-lg border-2 border-start-0 ps-0"
                                   id="animalSearch" placeholder="ID, name, tag...">
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

<!-- Lifecycle Tracking Table -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Lifecycle Tracking Records</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                        data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow">
                        <a class="dropdown-item" href="#" onclick="refreshLifecycleData()">Refresh Data</a>
                        <a class="dropdown-item" href="#" onclick="exportLifecycle()">Export Data</a>
                        <a class="dropdown-item" href="#" onclick="printLifecycle()">Print Report</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="lifecycleTable">
                        <thead>
                            <tr>
                                <th>Animal ID</th>
                                <th>Name/Tag</th>
                                <th>Breed</th>
                                <th>Gender</th>
                                <th>Age</th>
                                <th>Stage</th>
                                <th>Status</th>
                                <th>Weight</th>
                                <th>Health</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="lifecycleBody">
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

<!-- Add Animal Modal -->
<div class="modal fade" id="addAnimalModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Animal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addAnimalForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="animalId" class="form-label">Animal ID *</label>
                            <input type="text" class="form-control" id="animalId" name="animal_id" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tagNumber" class="form-label">Tag Number *</label>
                            <input type="text" class="form-control" id="tagNumber" name="tag_number" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="animalName" class="form-label">Name *</label>
                            <input type="text" class="form-control" id="animalName" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="breed" class="form-label">Breed *</label>
                            <select class="form-select" id="breed" name="breed" required>
                                <option value="">Select Breed</option>
                                <option value="holstein">Holstein</option>
                                <option value="jersey">Jersey</option>
                                <option value="guernsey">Guernsey</option>
                                <option value="ayrshire">Ayrshire</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="gender" class="form-label">Gender *</label>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="female">Female</option>
                                <option value="male">Male</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="birthDate" class="form-label">Birth Date *</label>
                            <input type="date" class="form-control" id="birthDate" name="birth_date" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="weight" class="form-label">Weight (kg)</label>
                            <input type="number" class="form-control" id="weight" name="weight" step="0.1" placeholder="0.0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="">Select Status</option>
                                <option value="active">Active</option>
                                <option value="sold">Sold</option>
                                <option value="deceased">Deceased</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveAnimal()">Save Animal</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Animal Modal -->
<div class="modal fade" id="editAnimalModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Animal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editAnimalForm">
                    <input type="hidden" id="editAnimalId">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editAnimalIdField" class="form-label">Animal ID *</label>
                            <input type="text" class="form-control" id="editAnimalIdField" name="animal_id" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editTagNumber" class="form-label">Tag Number *</label>
                            <input type="text" class="form-control" id="editTagNumber" name="tag_number" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editAnimalName" class="form-label">Name *</label>
                            <input type="text" class="form-control" id="editAnimalName" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editBreed" class="form-label">Breed *</label>
                            <select class="form-select" id="editBreed" name="breed" required>
                                <option value="">Select Breed</option>
                                <option value="holstein">Holstein</option>
                                <option value="jersey">Jersey</option>
                                <option value="guernsey">Guernsey</option>
                                <option value="ayrshire">Ayrshire</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editGender" class="form-label">Gender *</label>
                            <select class="form-select" id="editGender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="female">Female</option>
                                <option value="male">Male</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editBirthDate" class="form-label">Birth Date *</label>
                            <input type="date" class="form-control" id="editBirthDate" name="birth_date" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editWeight" class="form-label">Weight (kg)</label>
                            <input type="number" class="form-control" id="editWeight" name="weight" step="0.1" placeholder="0.0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editStatus" class="form-label">Status *</label>
                            <select class="form-select" id="editStatus" name="status" required>
                                <option value="">Select Status</option>
                                <option value="active">Active</option>
                                <option value="sold">Sold</option>
                                <option value="deceased">Deceased</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updateAnimal()">Update Animal</button>
            </div>
        </div>
    </div>
</div>

<!-- View Details Modal -->
<div class="modal fade" id="viewDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Animal Lifecycle Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="animalDetails">
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
    let allAnimals = [];
    let filteredAnimals = [];
    let currentPage = 1;
    const recordsPerPage = 20;

    // Load animals on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadLifecycleData();
    });

    function loadLifecycleData() {
        const tbody = document.getElementById('lifecycleBody');
        tbody.innerHTML = `
            <tr>
                <td colspan="10" class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </td>
            </tr>
        `;

        fetch('/api/animals')
            .then(response => response.json())
            .then(animals => {
                allAnimals = animals;
                filteredAnimals = animals;
                currentPage = 1;
                updateStatistics(animals);
                displayAnimals();
            })
            .catch(error => {
                console.error('Error loading lifecycle data:', error);
                tbody.innerHTML = `
                    <tr>
                        <td colspan="10" class="text-center text-danger">Error loading lifecycle data</td>
                    </tr>
                `;
            });
    }

    function updateStatistics(animals) {
        const total = animals.length;
        const active = animals.filter(a => a.status === 'active' || a.status === 'healthy').length;
        const monitoring = animals.filter(a => a.status === 'pregnant' || a.status === 'sick').length;

        let totalAge = 0;
        animals.forEach(animal => {
            const age = calculateAge(animal.birth_date);
            totalAge += age;
        });
        const avgAge = total > 0 ? (totalAge / total).toFixed(1) : 0;

        document.getElementById('total-animals').textContent = total;
        document.getElementById('active-count').textContent = active;
        document.getElementById('monitoring-count').textContent = monitoring;
        document.getElementById('avg-age').textContent = avgAge;
    }

    function displayAnimals() {
        const tbody = document.getElementById('lifecycleBody');
        tbody.innerHTML = '';

        if (filteredAnimals.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="10" class="text-center">No animals found</td>
                </tr>
            `;
            updatePaginationInfo(0, 0, 0);
            renderPaginationControls(0);
            return;
        }

        // Calculate pagination
        const totalRecords = filteredAnimals.length;
        const totalPages = Math.ceil(totalRecords / recordsPerPage);
        const startIndex = (currentPage - 1) * recordsPerPage;
        const endIndex = Math.min(startIndex + recordsPerPage, totalRecords);
        const animalsToDisplay = filteredAnimals.slice(startIndex, endIndex);

        // Display animals
        animalsToDisplay.forEach(animal => {
            const age = calculateAge(animal.birth_date);
            const stage = getLifecycleStage(age);
            const statusBadge = getStatusBadge(animal.status);
            const stageBadge = getStageBadge(stage);
            const healthStatus = animal.latest_health_record ? animal.latest_health_record.health_status : 'unknown';
            const healthBadge = getHealthBadge(healthStatus);

            const row = `
                <tr>
                    <td>${animal.animal_id}</td>
                    <td>${animal.name}<br><small class="text-muted">${animal.tag_number}</small></td>
                    <td>${capitalizeFirst(animal.breed)}</td>
                    <td>${animal.gender === 'female' ? '♀' : '♂'} ${capitalizeFirst(animal.gender)}</td>
                    <td>${age} year${age !== 1 ? 's' : ''}</td>
                    <td>${stageBadge}</td>
                    <td>${statusBadge}</td>
                    <td>${animal.weight ? animal.weight + ' kg' : '-'}</td>
                    <td>${healthBadge}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-sm btn-primary" onclick="viewDetails(${animal.id})" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-info" onclick="editAnimal(${animal.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteAnimal(${animal.id})" title="Delete">
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

    function getLifecycleStage(age) {
        if (age < 1) return 'calf';
        if (age < 2) return 'heifer';
        if (age < 5) return 'adult';
        return 'mature';
    }

    function getStageBadge(stage) {
        const badges = {
            'calf': '<span class="badge bg-info">🍼 Calf</span>',
            'heifer': '<span class="badge bg-primary">🐄 Heifer</span>',
            'adult': '<span class="badge bg-success">🐮 Adult</span>',
            'mature': '<span class="badge bg-secondary">👑 Mature</span>'
        };
        return badges[stage] || stage;
    }

    function getStatusBadge(status) {
        const badges = {
            'active': '<span class="badge bg-success">Active</span>',
            'healthy': '<span class="badge bg-success">Healthy</span>',
            'pregnant': '<span class="badge bg-info">Pregnant</span>',
            'sick': '<span class="badge bg-danger">Sick</span>',
            'dry': '<span class="badge bg-warning">Dry Period</span>',
            'sold': '<span class="badge bg-secondary">Sold</span>',
            'deceased': '<span class="badge bg-dark">Deceased</span>'
        };
        return badges[status] || '<span class="badge bg-secondary">' + status + '</span>';
    }

    function getHealthBadge(health) {
        const badges = {
            'healthy': '<span class="badge bg-success">Healthy</span>',
            'treatment': '<span class="badge bg-warning">Treatment</span>',
            'critical': '<span class="badge bg-danger">Critical</span>',
            'unknown': '<span class="badge bg-secondary">N/A</span>'
        };
        return badges[health] || '<span class="badge bg-secondary">' + health + '</span>';
    }

    function capitalizeFirst(str) {
        if (!str) return '';
        return str.charAt(0).toUpperCase() + str.slice(1);
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
        const totalPages = Math.ceil(filteredAnimals.length / recordsPerPage);
        if (page < 1 || page > totalPages) {
            return;
        }
        currentPage = page;
        displayAnimals();
        document.getElementById('lifecycleTable').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // Filter functionality
    document.getElementById('statusFilter')?.addEventListener('change', applyFilters);
    document.getElementById('genderFilter')?.addEventListener('change', applyFilters);
    document.getElementById('breedFilter')?.addEventListener('change', applyFilters);
    document.getElementById('ageFilter')?.addEventListener('change', applyFilters);
    document.getElementById('animalSearch')?.addEventListener('input', applyFilters);

    function applyFilters() {
        const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
        const genderFilter = document.getElementById('genderFilter').value.toLowerCase();
        const breedFilter = document.getElementById('breedFilter').value.toLowerCase();
        const ageFilter = document.getElementById('ageFilter').value;
        const searchTerm = document.getElementById('animalSearch').value.toLowerCase();

        filteredAnimals = allAnimals.filter(animal => {
            const animalId = animal.animal_id.toLowerCase();
            const name = animal.name.toLowerCase();
            const tagNumber = animal.tag_number.toLowerCase();
            const status = (animal.status || '').toLowerCase();
            const gender = animal.gender.toLowerCase();
            const breed = animal.breed.toLowerCase();
            const age = calculateAge(animal.birth_date);
            const stage = getLifecycleStage(age);

            // Status filter
            const matchesStatus = !statusFilter || status === statusFilter;

            // Gender filter
            const matchesGender = !genderFilter || gender === genderFilter;

            // Breed filter
            const matchesBreed = !breedFilter || breed === breedFilter;

            // Age filter
            let matchesAge = true;
            if (ageFilter) {
                matchesAge = stage === ageFilter;
            }

            // Search filter
            const matchesSearch = !searchTerm ||
                animalId.includes(searchTerm) ||
                name.includes(searchTerm) ||
                tagNumber.includes(searchTerm);

            return matchesStatus && matchesGender && matchesBreed && matchesAge && matchesSearch;
        });

        currentPage = 1;
        updateStatistics(filteredAnimals);
        displayAnimals();
    }

    function clearFilters() {
        document.getElementById('statusFilter').value = '';
        document.getElementById('genderFilter').value = '';
        document.getElementById('breedFilter').value = '';
        document.getElementById('ageFilter').value = '';
        document.getElementById('animalSearch').value = '';

        filteredAnimals = allAnimals;
        currentPage = 1;
        updateStatistics(allAnimals);
        displayAnimals();
    }

    function refreshLifecycleData() {
        currentPage = 1;
        loadLifecycleData();
    }

    function exportLifecycle() {
        alert('Export lifecycle data functionality');
    }

    function printLifecycle() {
        window.print();
    }

    function saveAnimal() {
        const form = document.getElementById('addAnimalForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const formData = {
            animal_id: document.getElementById('animalId').value,
            tag_number: document.getElementById('tagNumber').value,
            name: document.getElementById('animalName').value,
            breed: document.getElementById('breed').value,
            gender: document.getElementById('gender').value,
            birth_date: document.getElementById('birthDate').value,
            weight: document.getElementById('weight').value || null,
            status: document.getElementById('status').value
        };

        fetch('/api/animals', {
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
                alert('Animal saved successfully!');
                const modal = bootstrap.Modal.getInstance(document.getElementById('addAnimalModal'));
                modal.hide();
                form.reset();
                loadLifecycleData();
                location.reload(); // Reload to update counts
            } else {
                alert('Error saving animal: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error saving animal:', error);
            alert('Error saving animal. Please try again.');
        });
    }

    function editAnimal(id) {
        // Load animal data and open edit modal
        fetch(`/api/animals/${id}`)
            .then(response => response.json())
            .then(animal => {
                // Populate form fields
                document.getElementById('editAnimalId').value = animal.id;
                document.getElementById('editAnimalIdField').value = animal.animal_id;
                document.getElementById('editTagNumber').value = animal.tag_number;
                document.getElementById('editAnimalName').value = animal.name;
                document.getElementById('editBreed').value = animal.breed;
                document.getElementById('editGender').value = animal.gender;
                document.getElementById('editBirthDate').value = animal.birth_date.split('T')[0];
                document.getElementById('editWeight').value = animal.weight || '';
                document.getElementById('editStatus').value = animal.status;

                // Open modal
                const modal = new bootstrap.Modal(document.getElementById('editAnimalModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error loading animal:', error);
                alert('Error loading animal. Please try again.');
            });
    }

    function updateAnimal() {
        const form = document.getElementById('editAnimalForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const animalId = document.getElementById('editAnimalId').value;
        const formData = {
            animal_id: document.getElementById('editAnimalIdField').value,
            tag_number: document.getElementById('editTagNumber').value,
            name: document.getElementById('editAnimalName').value,
            breed: document.getElementById('editBreed').value,
            gender: document.getElementById('editGender').value,
            birth_date: document.getElementById('editBirthDate').value,
            weight: document.getElementById('editWeight').value || null,
            status: document.getElementById('editStatus').value
        };

        fetch(`/api/animals/${animalId}`, {
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
                alert('Animal updated successfully!');
                const modal = bootstrap.Modal.getInstance(document.getElementById('editAnimalModal'));
                modal.hide();
                loadLifecycleData();
                location.reload(); // Reload to update counts
            } else {
                alert('Error updating animal: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error updating animal:', error);
            alert('Error updating animal. Please try again.');
        });
    }

    function deleteAnimal(id) {
        if (confirm('Are you sure you want to delete this animal?')) {
            fetch(`/api/animals/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Animal deleted successfully!');
                    loadLifecycleData();
                    location.reload(); // Reload to update counts
                } else {
                    alert('Error deleting animal: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error deleting animal:', error);
                alert('Error deleting animal. Please try again.');
            });
        }
    }

    function viewDetails(id) {
        const animal = allAnimals.find(a => a.id === id);
        if (!animal) return;

        const age = calculateAge(animal.birth_date);
        const stage = getLifecycleStage(age);
        const statusBadge = getStatusBadge(animal.status);
        const stageBadge = getStageBadge(stage);
        const healthStatus = animal.latest_health_record ? animal.latest_health_record.health_status : 'unknown';
        const healthBadge = getHealthBadge(healthStatus);

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
                    <strong>Gender:</strong> ${animal.gender === 'female' ? '♀' : '♂'} ${capitalizeFirst(animal.gender)}
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Birth Date:</strong> ${new Date(animal.birth_date).toLocaleDateString()}
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Age:</strong> ${age} year${age !== 1 ? 's' : ''}
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Lifecycle Stage:</strong> ${stageBadge}
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Status:</strong> ${statusBadge}
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Weight:</strong> ${animal.weight ? animal.weight + ' kg' : 'Not recorded'}
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Health Status:</strong> ${healthBadge}
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Last Health Check:</strong> ${animal.latest_health_record ? new Date(animal.latest_health_record.check_date).toLocaleDateString() : 'N/A'}
                </div>
            </div>
        `;
        document.getElementById('animalDetails').innerHTML = details;
        const modal = new bootstrap.Modal(document.getElementById('viewDetailsModal'));
        modal.show();
    }
</script>
@endpush
