@extends('layouts.app')

@section('title', 'Emergency Alert Records')

@push('styles')
<style>
    .glass-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 40%, #312e81 100%);
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }
    .glass-header::before {
        content: '';
        position: absolute;
        top: -10%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 70%);
        filter: blur(40px);
        pointer-events: none;
    }
    .glass-header::after {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%);
        pointer-events: none;
    }
    
    .stats-card-mini {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        padding: 15px;
        transition: transform 0.3s ease;
    }
    .stats-card-mini:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.1);
    }

    .alert-table thead th {
        background-color: #f1f5f9;
        text-transform: uppercase;
        font-size: 0.72rem;
        letter-spacing: 0.08em;
        font-weight: 800;
        color: #475569;
        border-top: none;
        border-bottom: 2px solid #e2e8f0;
    }

    .alert-row {
        transition: all 0.2s ease;
    }
    .alert-row:hover {
        background-color: #f8fafc !important;
        transform: scale(1.002);
    }

    .animal-badge {
        background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
        color: white;
        padding: 4px 12px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.85rem;
        box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.2);
    }

    .status-pill {
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.75rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .advice-box {
        background-color: #f0fdf4;
        border-left: 4px solid #22c55e;
        padding: 12px;
        border-radius: 0 8px 8px 0;
        font-size: 0.875rem;
        color: #166534;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }

    .search-input-group {
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        border-radius: 10px;
        overflow: hidden;
    }
    .search-input-group .form-control {
        border: none;
        padding: 12px 20px;
    }
    .search-input-group .input-group-text {
        background: white;
        border: none;
        color: #94a3b8;
    }

    /* Animations */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-up {
        animation: fadeInUp 0.5s ease-out forwards;
    }
</style>
@endpush

@section('content')
<!-- Re-styled Header -->
<div class="row mb-5 animate-up">
    <div class="col-12">
        <div class="glass-header rounded-4 p-4 p-md-5 text-white shadow-lg">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-style2 mb-2">
                            <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}" class="text-white-50">Dashboard</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">Emergency Alerts</li>
                        </ol>
                    </nav>
                    <h1 class="text-white fw-extra-bold mb-2 display-6">Emergency Records <span class="badge bg-danger fs-6 align-middle ms-2">Live</span></h1>
                    <p class="text-white-50 mb-0 fs-5 lh-base">
                        Real-time tracking of veterinary consultations, critical animal health reports, and treatments.
                    </p>
                </div>
                <div class="col-lg-6 mt-4 mt-lg-0">
                    <div class="row g-3">
                        <div class="col-4">
                            <div class="stats-card-mini text-center">
                                <h3 class="fw-bold mb-0 text-white">{{ $totalCount }}</h3>
                                <small class="text-white-50">Total Logs</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stats-card-mini text-center">
                                <h3 class="fw-bold mb-0 text-warning">{{ $pendingCount }}</h3>
                                <small class="text-white-50">Pending</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stats-card-mini text-center">
                                <h3 class="fw-bold mb-0 text-success">{{ $resolvedCount }}</h3>
                                <small class="text-white-50">Resolved</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-3 border-top border-white border-opacity-10 d-flex gap-2">
                <button class="btn btn-primary shadow-sm px-4" onclick="exportAlerts()">
                    <i class="bx bxs-download me-2"></i>Export Report
                </button>
                <button class="btn btn-outline-light px-4" onclick="printAlerts()">
                    <i class="bx bxs-printer me-2"></i>Print Live View
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Controls & Filters -->
<div class="row mb-4 animate-up" style="animation-delay: 0.1s;">
    <div class="col-md-6">
        <div class="input-group search-input-group">
            <span class="input-group-text"><i class="bx bx-search fs-4"></i></span>
            <input type="text" class="form-control" id="alertSearch" placeholder="Search by Animal ID, situational symptoms, or date..." onkeyup="filterTable()">
        </div>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <div class="btn-group shadow-sm" role="group">
            <button type="button" class="btn btn-white fw-semibold active" onclick="filterByStatus('all')">All</button>
            <button type="button" class="btn btn-white fw-semibold" onclick="filterByStatus('pending')">Pending</button>
            <button type="button" class="btn btn-white fw-semibold" onclick="filterByStatus('advised')">Advised</button>
            <button type="button" class="btn btn-white fw-semibold" onclick="filterByStatus('resolved')">Resolved</button>
        </div>
    </div>
</div>

<!-- Main Table Card -->
<div class="row animate-up" style="animation-delay: 0.2s;">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 alert-table" id="alertsTable">
                        <thead>
                            <tr>
                                <th class="px-4 py-3">Occurrence Time</th>
                                <th class="px-4 py-3">Animal Identity</th>
                                <th class="px-4 py-3">Situational Logs</th>
                                <th class="px-4 py-3">Medical Advice</th>
                                <th class="px-4 py-3">Current Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($alerts as $alert)
                            <tr class="alert-row" data-status="{{ $alert->status }}">
                                <td class="px-4 py-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded-3 p-2 me-3">
                                            <i class="bx bx-calendar-event fs-4 text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $alert->created_at->format('d M Y') }}</div>
                                            <div class="text-muted small">{{ $alert->created_at->format('h:i A') }} ({{ $alert->created_at->diffForHumans() }})</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    @if($alert->animal)
                                        <div class="d-flex flex-column gap-1">
                                            <span class="animal-badge w-fit-content">{{ $alert->animal->animal_id }}</span>
                                            <span class="text-muted small fw-medium"><i class="bx bx-purchase-tag-alt me-1"></i>{{ $alert->animal->tag_number }}</span>
                                        </div>
                                    @else
                                        <span class="badge bg-label-secondary">Not Linked</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    <div style="max-width: 320px;">
                                        <p class="mb-2 text-dark font-weight-medium lh-base">{{ $alert->message }}</p>
                                        @if($alert->temperature)
                                            <div class="d-inline-flex align-items-center badge bg-label-warning px-2 py-1">
                                                <i class="bx bx-thermometer me-1"></i> {{ $alert->temperature }}°F
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    @if($alert->doctor_advice)
                                        <div class="advice-box">
                                            <div class="fw-bold mb-1 small uppercase text-success" style="letter-spacing: 0.05em;">Doctor's Note</div>
                                            <div class="lh-sm text-dark italic">{!! nl2br(e($alert->doctor_advice)) !!}</div>
                                        </div>
                                    @else
                                        <div class="text-muted small text-center bg-light rounded-3 p-3">
                                            <i class="bx bx-error-circle d-block fs-4 mb-1"></i>
                                            Awaiting doctor feedback
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    @php
                                        $statusClass = [
                                            'pending' => 'bg-label-warning',
                                            'forwarded_to_doctor' => 'bg-label-primary',
                                            'advised' => 'bg-label-success',
                                            'resolved' => 'bg-label-secondary',
                                            'visit_confirmed' => 'bg-label-info',
                                            'on_site' => 'bg-label-dark',
                                            'under_treatment' => 'bg-label-info',
                                        ][$alert->status] ?? 'bg-label-secondary';
                                        
                                        $statusIcon = [
                                            'pending' => 'bx-time-five',
                                            'forwarded_to_doctor' => 'bx-forward',
                                            'advised' => 'bx-check-double',
                                            'resolved' => 'bx-check-circle',
                                            'visit_confirmed' => 'bx-calendar-check',
                                            'on_site' => 'bx-map-pin',
                                            'under_treatment' => 'bx-plus-medical',
                                        ][$alert->status] ?? 'bx-info-circle';
                                    @endphp
                                    <span class="status-pill {{ $statusClass }}">
                                        <i class="bx {{ $statusIcon }}"></i>
                                        {{ strtoupper(str_replace('_', ' ', $alert->status)) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="py-5">
                                        <i class="bx bx-folder-open text-muted mb-3" style="font-size: 5rem; opacity: 0.3;"></i>
                                        <h4 class="text-muted fw-bold">No Emergency Logs Found</h4>
                                        <p class="text-muted">Currently there are no registered emergency records in the system.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-0 py-4 px-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <div class="text-muted small">
                        Showing {{ $alerts->firstItem() ?? 0 }} to {{ $alerts->lastItem() ?? 0 }} of {{ $alerts->total() }} emergency records
                    </div>
                    <div class="pagination-wrapper">
                        {{ $alerts->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function exportAlerts() {
        window.location.href = '/alerts/export';
    }

    function printAlerts() {
        window.open('/alerts/print', '_blank');
    }

    function filterTable() {
        let input = document.getElementById("alertSearch");
        let filter = input.value.toLowerCase();
        let table = document.getElementById("alertsTable");
        let tr = table.getElementsByTagName("tr");

        for (let i = 1; i < tr.length; i++) {
            let td = tr[i].getElementsByTagName("td");
            let rowText = tr[i].textContent.toLowerCase();
            if (rowText.includes(filter)) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }

    function filterByStatus(status) {
        // Update active button
        const buttons = document.querySelectorAll('.btn-group .btn');
        buttons.forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Find and activate the clicked button
        event.target.classList.add('active');

        let table = document.getElementById("alertsTable");
        let tr = table.getElementsByClassName("alert-row");

        for (let i = 0; i < tr.length; i++) {
            let rowStatus = tr[i].getAttribute("data-status");
            
            if (status === 'all') {
                tr[i].style.display = "";
            } else if (rowStatus === status) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
        
        // Show toast notification
        const statusLabels = {
            'all': 'All Alerts',
            'pending': 'Pending Alerts',
            'advised': 'Advised Alerts',
            'resolved': 'Resolved Alerts'
        };
        
        Toast.fire({
            icon: 'info',
            title: `Showing: ${statusLabels[status] || status}`
        });
    }
</script>
@endpush
