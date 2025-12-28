@extends('layouts.app')

@section('title', 'Inventory Management')

@push('styles')
<style>
    .border-left-primary { border-left: 0.25rem solid var(--primary-600) !important; }
    .border-left-success { border-left: 0.25rem solid var(--success-main) !important; }
    .border-left-warning { border-left: 0.25rem solid var(--warning-main) !important; }
    .border-left-info { border-left: 0.25rem solid var(--info-main) !important; }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-boxes"></i> Inventory Management</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportInventory()">
                <i class="fas fa-download me-1"></i>Export
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="printInventory()">
                <i class="fas fa-print me-1"></i>Print
            </button>
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
            <i class="fas fa-plus me-1"></i>Add Item
        </button>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card card-stats shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-vibrant bg-vibrant-primary me-3 shadow-primary">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted uppercase fs-tiny fw-semibold">Total Items</h6>
                        <h3 class="mb-0 fw-bold text-vibrant-primary">0</h3>
                    </div>
                </div>
                <small class="text-primary fw-medium"><i class="bx bx-check-double me-1"></i> Full Inventory</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stats shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-vibrant bg-vibrant-success me-3 shadow-success">
                        <i class="fas fa-warehouse"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted uppercase fs-tiny fw-semibold">In Stock</h6>
                        <h3 class="mb-0 fw-bold text-vibrant-success">0</h3>
                    </div>
                </div>
                <small class="text-success fw-medium"><i class="bx bx-check-circle me-1"></i> Available Now</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stats shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-vibrant bg-vibrant-warning me-3 shadow-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted uppercase fs-tiny fw-semibold">Low Stock</h6>
                        <h3 class="mb-0 fw-bold text-vibrant-warning">0</h3>
                    </div>
                </div>
                <small class="text-warning fw-medium"><i class="bx bx-alarm-exclamation me-1"></i> Needs Reorder</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stats shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-vibrant bg-vibrant-info me-3 shadow-info">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted uppercase fs-tiny fw-semibold">Total Value</h6>
                        <h3 class="mb-0 fw-bold text-vibrant-info">$0</h3>
                    </div>
                </div>
                <small class="text-info fw-medium"><i class="bx bx-trending-up me-1"></i> Estimated Assets</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Inventory Items</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Item Name</th>
                                <th>Category</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="7" class="text-center">No items found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Inventory Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addItemForm">
                    <div class="mb-3">
                        <label class="form-label">Item Name</label>
                        <input type="text" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" required>
                            <option value="">Select Category</option>
                            <option value="feed">Feed & Nutrition</option>
                            <option value="medicine">Medicine & Health</option>
                            <option value="equipment">Equipment</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Unit</label>
                            <select class="form-select" required>
                                <option value="kg">Kilograms</option>
                                <option value="liters">Liters</option>
                                <option value="pieces">Pieces</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Save Item</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function exportInventory() {
        window.location.href = '/inventory/export';
    }

    function printInventory() {
        window.open('/inventory/print', '_blank');
    }

    function saveInventoryItem() {
        const form = document.getElementById('addItemForm');
        if (form.checkValidity()) {
            alert('Inventory item saved successfully!');
            const modal = bootstrap.Modal.getInstance(document.getElementById('addItemModal'));
            modal.hide();
            form.reset();
        } else {
            form.reportValidity();
        }
    }
</script>
@endpush
