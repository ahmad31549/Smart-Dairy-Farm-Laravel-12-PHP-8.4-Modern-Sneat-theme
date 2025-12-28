@extends('layouts.app')

@section('title', 'Medical Supplies Inventory')

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
    <h1 class="h2"><i class="fas fa-medkit"></i> Medical Supplies Inventory</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportInventory()">
                <i class="fas fa-download me-1"></i>Export
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="printInventory()">
                <i class="fas fa-print me-1"></i>Print
            </button>
        </div>
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
            <i class="fas fa-plus me-1"></i>Add Item
        </button>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Items</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-items">{{ $totalItems }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-pills fa-2x text-gray-300"></i>
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
                            In Stock</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="in-stock-count">{{ $inStock }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                            Low Stock</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="low-stock-count">{{ $lowStock }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
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
                            Expiring Soon</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="expiring-count">{{ $expiringSoon }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-times fa-2x text-gray-300"></i>
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
                            <option value="in_stock">✓ In Stock</option>
                            <option value="low_stock">⚠ Low Stock</option>
                            <option value="out_of_stock">✕ Out of Stock</option>
                        </select>
                    </div>

                    <!-- Category Filter -->
                    <div class="col-lg-3 col-md-6">
                        <label for="categoryFilter" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-tag text-success me-2"></i>Category
                        </label>
                        <select class="form-select form-select-lg border-2" id="categoryFilter">
                            <option value="">All Categories</option>
                            <option value="medicine">Medicines</option>
                            <option value="vaccine">Vaccines</option>
                            <option value="antibiotic">Antibiotics</option>
                            <option value="equipment">Medical Equipment</option>
                            <option value="supplement">Supplements</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <!-- Expiry Filter -->
                    <div class="col-lg-3 col-md-6">
                        <label for="expiryFilter" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-calendar-times text-danger me-2"></i>Expiry Status
                        </label>
                        <select class="form-select form-select-lg border-2" id="expiryFilter">
                            <option value="">All Items</option>
                            <option value="expiring_soon">Expiring Soon (30 days)</option>
                            <option value="has_expiry">Has Expiry Date</option>
                            <option value="no_expiry">No Expiry Date</option>
                        </select>
                    </div>

                    <!-- Search Box -->
                    <div class="col-lg-3 col-md-6">
                        <label for="itemSearch" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-search text-warning me-2"></i>Search
                        </label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-white border-2 border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control form-control-lg border-2 border-start-0 ps-0"
                                   id="itemSearch" placeholder="Medicine name, supplier...">
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

<!-- Inventory Table -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Medical Supplies Items</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                        data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow">
                        <a class="dropdown-item" href="#" onclick="refreshInventoryData()">Refresh Data</a>
                        <a class="dropdown-item" href="#" onclick="exportInventory()">Export Data</a>
                        <a class="dropdown-item" href="#" onclick="printInventory()">Print Report</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="inventoryTable">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Category</th>
                                <th>Batch #</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Total Value</th>
                                <th>Status</th>
                                <th>Manufacturer</th>
                                <th>Expiry Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="inventoryBody">
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

<!-- Add Item Modal -->
<div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Medical Supplies Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addItemForm">
                    @csrf
                    <input type="hidden" name="inventory_type" value="medical_supplies">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="itemName" class="form-label">Item Name *</label>
                            <input type="text" class="form-control" id="itemName" name="item_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label">Category *</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="medicine">Medicines</option>
                                <option value="vaccine">Vaccines</option>
                                <option value="antibiotic">Antibiotics</option>
                                <option value="equipment">Medical Equipment</option>
                                <option value="supplement">Supplements</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="batchNumber" class="form-label">Batch Number *</label>
                            <input type="text" class="form-control" id="batchNumber" name="batch_number" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="quantity" class="form-label">Quantity *</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" step="0.01" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="unit" class="form-label">Unit *</label>
                            <select class="form-select" id="unit" name="unit" required>
                                <option value="ml">Milliliters (ml)</option>
                                <option value="tablets">Tablets</option>
                                <option value="vials">Vials</option>
                                <option value="bottles">Bottles</option>
                                <option value="boxes">Boxes</option>
                                <option value="pieces">Pieces</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="unitPrice" class="form-label">Unit Price (Rs.) *</label>
                            <input type="number" class="form-control" id="unitPrice" name="unit_price" step="0.01" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="reorderLevel" class="form-label">Reorder Level *</label>
                            <input type="number" class="form-control" id="reorderLevel" name="reorder_level" step="0.01" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="supplier" class="form-label">Supplier</label>
                            <input type="text" class="form-control" id="supplier" name="supplier">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="manufacturer" class="form-label">Manufacturer *</label>
                            <input type="text" class="form-control" id="manufacturer" name="manufacturer" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lastRestocked" class="form-label">Last Restocked</label>
                            <input type="date" class="form-control" id="lastRestocked" name="last_restocked">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="expiryDate" class="form-label">Expiry Date *</label>
                            <input type="date" class="form-control" id="expiryDate" name="expiry_date" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveItem()">Save Item</button>
            </div>
        </div>
    </div>
</div>

<!-- View Item Modal -->
<div class="modal fade" id="viewItemModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Item Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="itemDetails">
                <!-- Details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Item Modal -->
<div class="modal fade" id="editItemModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Medical Supplies Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editItemForm">
                    @csrf
                    <input type="hidden" id="editItemId">
                    <input type="hidden" name="inventory_type" value="medical_supplies">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editItemName" class="form-label">Item Name *</label>
                            <input type="text" class="form-control" id="editItemName" name="item_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editCategory" class="form-label">Category *</label>
                            <select class="form-select" id="editCategory" name="category" required>
                                <option value="">Select Category</option>
                                <option value="medicine">Medicines</option>
                                <option value="vaccine">Vaccines</option>
                                <option value="antibiotic">Antibiotics</option>
                                <option value="equipment">Medical Equipment</option>
                                <option value="supplement">Supplements</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editBatchNumber" class="form-label">Batch Number *</label>
                            <input type="text" class="form-control" id="editBatchNumber" name="batch_number" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editQuantity" class="form-label">Quantity *</label>
                            <input type="number" class="form-control" id="editQuantity" name="quantity" step="0.01" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editUnit" class="form-label">Unit *</label>
                            <select class="form-select" id="editUnit" name="unit" required>
                                <option value="ml">Milliliters (ml)</option>
                                <option value="tablets">Tablets</option>
                                <option value="vials">Vials</option>
                                <option value="bottles">Bottles</option>
                                <option value="boxes">Boxes</option>
                                <option value="pieces">Pieces</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editUnitPrice" class="form-label">Unit Price (Rs.) *</label>
                            <input type="number" class="form-control" id="editUnitPrice" name="unit_price" step="0.01" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editReorderLevel" class="form-label">Reorder Level *</label>
                            <input type="number" class="form-control" id="editReorderLevel" name="reorder_level" step="0.01" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editSupplier" class="form-label">Supplier</label>
                            <input type="text" class="form-control" id="editSupplier" name="supplier">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editManufacturer" class="form-label">Manufacturer *</label>
                            <input type="text" class="form-control" id="editManufacturer" name="manufacturer" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editLastRestocked" class="form-label">Last Restocked</label>
                            <input type="date" class="form-control" id="editLastRestocked" name="last_restocked">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editExpiryDate" class="form-label">Expiry Date *</label>
                            <input type="date" class="form-control" id="editExpiryDate" name="expiry_date" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="editNotes" class="form-label">Notes</label>
                        <textarea class="form-control" id="editNotes" name="notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updateItem()">Update Item</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Pagination variables
    let allItems = [];
    let filteredItems = [];
    let currentPage = 1;
    const recordsPerPage = 20;

    // Load inventory items on page load
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('lastRestocked').value = today;
        loadInventoryItems();
    });

    function loadInventoryItems() {
        const tbody = document.getElementById('inventoryBody');
        tbody.innerHTML = `
            <tr>
                <td colspan="10" class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </td>
            </tr>
        `;

        fetch('/api/inventory/all?type=medical_supplies')
            .then(response => response.json())
            .then(items => {
                allItems = items;
                filteredItems = items;
                currentPage = 1;
                displayItems();
            })
            .catch(error => {
                console.error('Error loading inventory items:', error);
                tbody.innerHTML = `
                    <tr>
                        <td colspan="10" class="text-center text-danger">Error loading inventory items</td>
                    </tr>
                `;
            });
    }

    function displayItems() {
        const tbody = document.getElementById('inventoryBody');
        tbody.innerHTML = '';

        if (filteredItems.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="10" class="text-center">No inventory items found</td>
                </tr>
            `;
            updatePaginationInfo(0, 0, 0);
            renderPaginationControls(0);
            return;
        }

        // Calculate pagination
        const totalRecords = filteredItems.length;
        const totalPages = Math.ceil(totalRecords / recordsPerPage);
        const startIndex = (currentPage - 1) * recordsPerPage;
        const endIndex = Math.min(startIndex + recordsPerPage, totalRecords);
        const itemsToDisplay = filteredItems.slice(startIndex, endIndex);

        // Display items
        itemsToDisplay.forEach(item => {
            const status = getStatus(item);
            const statusBadge = getStatusBadge(status);
            const totalValue = (parseFloat(item.quantity) * parseFloat(item.unit_price)).toFixed(2);
            const expiryDate = item.expiry_date ? formatDate(item.expiry_date) : '-';
            const expiryClass = item.expiry_date && new Date(item.expiry_date) <= new Date(Date.now() + 30*24*60*60*1000) ? 'text-danger fw-bold' : '';

            const row = `
                <tr>
                    <td>${item.item_name}</td>
                    <td>${formatCategory(item.category)}</td>
                    <td>${item.batch_number || '-'}</td>
                    <td>${item.quantity} ${item.unit}</td>
                    <td>Rs. ${parseFloat(item.unit_price).toFixed(2)}</td>
                    <td>Rs. ${totalValue}</td>
                    <td>${statusBadge}</td>
                    <td>${item.manufacturer || '-'}</td>
                    <td class="${expiryClass}">${expiryDate}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-sm btn-primary" onclick="viewItem(${item.id})" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-info" onclick="editItem(${item.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteItem(${item.id})" title="Delete">
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

    function getStatus(item) {
        if (parseFloat(item.quantity) <= 0) {
            return 'out_of_stock';
        } else if (parseFloat(item.quantity) <= parseFloat(item.reorder_level)) {
            return 'low_stock';
        } else {
            return 'in_stock';
        }
    }

    function getStatusBadge(status) {
        const badges = {
            'in_stock': '<span class="badge bg-success">In Stock</span>',
            'low_stock': '<span class="badge bg-warning">Low Stock</span>',
            'out_of_stock': '<span class="badge bg-danger">Out of Stock</span>'
        };
        return badges[status] || status;
    }

    function formatCategory(category) {
        return category.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
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
        const totalPages = Math.ceil(filteredItems.length / recordsPerPage);
        if (page < 1 || page > totalPages) {
            return;
        }
        currentPage = page;
        displayItems();
        document.getElementById('inventoryTable').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // Filter functionality
    document.getElementById('statusFilter')?.addEventListener('change', applyFilters);
    document.getElementById('categoryFilter')?.addEventListener('change', applyFilters);
    document.getElementById('expiryFilter')?.addEventListener('change', applyFilters);
    document.getElementById('itemSearch')?.addEventListener('input', applyFilters);

    function applyFilters() {
        const statusFilter = document.getElementById('statusFilter').value;
        const categoryFilter = document.getElementById('categoryFilter').value;
        const expiryFilter = document.getElementById('expiryFilter').value;
        const searchTerm = document.getElementById('itemSearch').value.toLowerCase();

        filteredItems = allItems.filter(item => {
            const status = getStatus(item);
            const itemName = item.item_name.toLowerCase();
            const supplier = (item.supplier || '').toLowerCase();
            const manufacturer = (item.manufacturer || '').toLowerCase();
            const category = item.category.toLowerCase();

            // Status filter
            const matchesStatus = !statusFilter || status === statusFilter;

            // Category filter
            const matchesCategory = !categoryFilter || category === categoryFilter;

            // Expiry filter
            let matchesExpiry = true;
            if (expiryFilter === 'expiring_soon') {
                matchesExpiry = item.expiry_date && new Date(item.expiry_date) <= new Date(Date.now() + 30*24*60*60*1000);
            } else if (expiryFilter === 'has_expiry') {
                matchesExpiry = item.expiry_date !== null;
            } else if (expiryFilter === 'no_expiry') {
                matchesExpiry = item.expiry_date === null;
            }

            // Search filter
            const matchesSearch = !searchTerm || itemName.includes(searchTerm) || supplier.includes(searchTerm) || manufacturer.includes(searchTerm);

            return matchesStatus && matchesCategory && matchesExpiry && matchesSearch;
        });

        currentPage = 1;
        displayItems();
    }

    function clearFilters() {
        document.getElementById('statusFilter').value = '';
        document.getElementById('categoryFilter').value = '';
        document.getElementById('expiryFilter').value = '';
        document.getElementById('itemSearch').value = '';

        filteredItems = allItems;
        currentPage = 1;
        displayItems();
    }

    function refreshInventoryData() {
        currentPage = 1;
        loadInventoryItems();
    }

    function exportInventory() {
        alert('Export inventory functionality');
    }

    function printInventory() {
        window.print();
    }

    function saveItem() {
        const form = document.getElementById('addItemForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const formData = new FormData(form);
        const data = Object.fromEntries(formData);

        fetch('/api/inventory', {
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
                alert('Item saved successfully!');
                const modal = bootstrap.Modal.getInstance(document.getElementById('addItemModal'));
                modal.hide();
                form.reset();
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to save item'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving item. Please try again.');
        });
    }

    function viewItem(id) {
        fetch(`/api/inventory/${id}`)
            .then(response => response.json())
            .then(item => {
                const status = getStatus(item);
                const statusBadge = getStatusBadge(status);
                const totalValue = (parseFloat(item.quantity) * parseFloat(item.unit_price)).toFixed(2);

                const details = `
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Item Name:</strong> ${item.item_name}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Category:</strong> ${formatCategory(item.category)}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Batch Number:</strong> ${item.batch_number || '-'}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Status:</strong> ${statusBadge}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Quantity:</strong> ${item.quantity} ${item.unit}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Unit Price:</strong> Rs. ${parseFloat(item.unit_price).toFixed(2)}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Total Value:</strong> Rs. ${totalValue}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Reorder Level:</strong> ${item.reorder_level} ${item.unit}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Supplier:</strong> ${item.supplier || '-'}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Manufacturer:</strong> ${item.manufacturer || '-'}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Last Restocked:</strong> ${item.last_restocked ? formatDate(item.last_restocked) : '-'}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Expiry Date:</strong> ${item.expiry_date ? formatDate(item.expiry_date) : '-'}
                        </div>
                        <div class="col-12 mb-3">
                            <strong>Notes:</strong><br>${item.notes || '-'}
                        </div>
                    </div>
                `;
                document.getElementById('itemDetails').innerHTML = details;
                const modal = new bootstrap.Modal(document.getElementById('viewItemModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading item details.');
            });
    }

    function editItem(id) {
        fetch(`/api/inventory/${id}`)
            .then(response => response.json())
            .then(item => {
                document.getElementById('editItemId').value = item.id;
                document.getElementById('editItemName').value = item.item_name;
                document.getElementById('editCategory').value = item.category;
                document.getElementById('editBatchNumber').value = item.batch_number || '';
                document.getElementById('editQuantity').value = item.quantity;
                document.getElementById('editUnit').value = item.unit;
                document.getElementById('editUnitPrice').value = item.unit_price;
                document.getElementById('editReorderLevel').value = item.reorder_level;
                document.getElementById('editSupplier').value = item.supplier || '';
                document.getElementById('editManufacturer').value = item.manufacturer || '';
                document.getElementById('editLastRestocked').value = item.last_restocked ? item.last_restocked.split('T')[0] : '';
                document.getElementById('editExpiryDate').value = item.expiry_date ? item.expiry_date.split('T')[0] : '';
                document.getElementById('editNotes').value = item.notes || '';

                const modal = new bootstrap.Modal(document.getElementById('editItemModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading item.');
            });
    }

    function updateItem() {
        const form = document.getElementById('editItemForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const itemId = document.getElementById('editItemId').value;
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);

        fetch(`/api/inventory/${itemId}`, {
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
                alert('Item updated successfully!');
                const modal = bootstrap.Modal.getInstance(document.getElementById('editItemModal'));
                modal.hide();
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to update item'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating item.');
        });
    }

    function deleteItem(id) {
        if (confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
            fetch(`/api/inventory/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Item deleted successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to delete item'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting item.');
            });
        }
    }
</script>
@endpush

