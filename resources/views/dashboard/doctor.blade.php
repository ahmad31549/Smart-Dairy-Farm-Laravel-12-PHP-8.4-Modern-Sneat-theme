@extends('layouts.app')

@section('title', 'Veterinary Dashboard')

@section('content')
<style>
/* Enforce 2-Color Theme & Hover Effects */
:root {
    --theme-primary: #0f172a; /* Dark Slate */
    --theme-accent: #2563eb;  /* Royal Blue */
    --theme-bg-hover: #f8fafc;
}

.card-hover {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid #e2e8f0;
}

.card-hover:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
    border-color: var(--theme-accent) !important;
}

.icon-box {
    background-color: #eff6ff; /* Light Blue */
    color: var(--theme-accent);
    transition: all 0.3s ease;
}

.card-hover:hover .icon-box {
    background-color: var(--theme-accent);
    color: white;
}

.text-theme {
    color: var(--theme-primary);
}

.text-accent {
    color: var(--theme-accent);
}
</style>

<!-- Quick Stats Overview -->
<div class="row mb-4">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card card-stats shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-vibrant bg-vibrant-danger me-3 shadow-danger">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted uppercase fs-tiny fw-semibold">Active Cases</h6>
                        <h3 class="mb-0 fw-bold text-vibrant-danger">{{ $alerts->where('status', '!=', 'resolved')->count() }}</h3>
                    </div>
                </div>
                <small class="text-danger fw-medium"><i class="bx bx-error me-1"></i> Requires Immediate Action</small>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card card-stats shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-vibrant bg-vibrant-warning me-3 shadow-warning">
                        <i class="fas fa-truck-medical"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted uppercase fs-tiny fw-semibold">Confirmed Visits</h6>
                        <h3 class="mb-0 fw-bold text-vibrant-warning">{{ $alerts->where('status', 'visit_confirmed')->count() }}</h3>
                    </div>
                </div>
                <small class="text-warning fw-medium"><i class="bx bx-time me-1"></i> On Way to Farm</small>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-12 mb-4">
        <div class="card card-stats shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-vibrant bg-vibrant-success me-3 shadow-success">
                        <i class="fas fa-notes-medical"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted uppercase fs-tiny fw-semibold">Resolved (Last 7 Days)</h6>
                        <h3 class="mb-0 fw-bold text-vibrant-success">{{ $alerts->where('status', 'resolved')->where('updated_at', '>=', now()->subDays(7))->count() }}</h3>
                    </div>
                </div>
                <small class="text-success fw-medium"><i class="bx bx-check-circle me-1"></i> Successfully Treated</small>
            </div>
        </div>
    </div>
</div>

<!-- Emergency Consultations Table -->
<div class="row">
    <div class="col-12">
        <div class="card border border-light-subtle rounded-3 shadow-sm card-hover">
            <div class="card-header bg-white border-bottom border-light pt-3 pb-2">
                <div class="d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:bell-bing-bold" class="text-danger fs-5"></iconify-icon>
                    <h6 class="fw-bold text-theme mb-0">Emergency Consultations</h6>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="text-muted small text-uppercase">
                                <th class="ps-4 border-bottom-0">Date/Time</th>
                                <th class="border-bottom-0">Reported By</th>
                                <th class="border-bottom-0">Animal ID</th>
                                <th class="border-bottom-0">Symptoms/Message</th>
                                <th class="border-bottom-0">Status</th>
                                <th class="pe-4 border-bottom-0 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @forelse($alerts as $alert)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="fw-medium text-dark">{{ $alert->created_at->format('d M, h:i A') }}</div>
                                    <small class="text-muted">{{ $alert->created_at->diffForHumans() }}</small>
                                </td>
                                <td class="py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="icon-box rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;">
                                            <span class="fw-bold small">{{ substr($alert->user->name ?? 'U', 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <p class="mb-0 fw-medium text-dark small">{{ $alert->user->name ?? 'Unknown' }}</p>
                                            <small class="text-muted" style="font-size: 0.75rem;">Worker</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">
                                    @if($alert->animal)
                                        <span class="badge bg-light text-dark border border-secondary-subtle rounded-pill px-2 py-1">
                                            {{ $alert->animal->animal_id }}
                                        </span>
                                    @else
                                        <span class="badge bg-light text-secondary border rounded-pill px-2 py-1">N/A</span>
                                    @endif
                                </td>
                                <td class="py-3">
                                    <p class="mb-0 small text-dark text-truncate" style="max-width: 250px;">{{ $alert->message }}</p>
                                    @if($alert->image_path)
                                        <a href="{{ asset('storage/' . $alert->image_path) }}" target="_blank" class="text-decoration-none small text-accent mt-1 d-inline-block">
                                            <iconify-icon icon="solar:gallery-bold" class="align-middle me-1"></iconify-icon> View Image
                                        </a>
                                    @endif
                                </td>
                                <td class="py-3">
                                    @if($alert->status === 'forwarded_to_doctor' || $alert->status === 'pending')
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2">Action Required</span>
                                    @elseif($alert->status === 'advised')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2">Advised</span>
                                    @elseif($alert->status === 'visit_confirmed')
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2">Visit Confirmed</span>
                                    @elseif($alert->status === 'on_site')
                                        <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2">On Site</span>
                                    @elseif($alert->status === 'under_treatment')
                                        <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2">Under Treatment</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2">{{ ucfirst(str_replace('_', ' ', $alert->status)) }}</span>
                                    @endif
                                </td>
                                <td class="pe-4 py-3 text-end">
                                    @if($alert->status === 'forwarded_to_doctor' || $alert->status === 'pending')
                                        <div class="d-flex gap-2 justify-content-end">
                                            @if(str_contains($alert->message, 'Worker Request') || str_contains($alert->message, 'URGENT'))
                                                <button class="btn btn-sm btn-outline-success rounded-pill px-3" onclick="confirmVisit({{ $alert->id }})">
                                                    I'm Coming
                                                </button>
                                            @endif
                                            <button class="btn btn-sm btn-primary rounded-pill px-3" onclick="openAdviceModal({{ $alert->id }})">
                                                Give Advice
                                            </button>
                                        </div>
                                    @elseif($alert->status === 'visit_confirmed')
                                        <button class="btn btn-sm btn-warning text-dark rounded-pill px-3" onclick="checkIn({{ $alert->id }})">
                                            Mark Arrived
                                        </button>
                                    @elseif($alert->status === 'on_site')
                                        <button class="btn btn-sm btn-info text-white rounded-pill px-3" onclick="openTreatmentModal({{ $alert->id }})">
                                            Record Treatment
                                        </button>
                                    @elseif($alert->status === 'under_treatment')
                                        <div class="d-flex gap-2 justify-content-end">
                                            <button class="btn btn-sm btn-info text-white rounded-pill px-3" onclick="openTreatmentModal({{ $alert->id }})">
                                                Add Tx
                                            </button>
                                            <button class="btn btn-sm btn-success text-white rounded-pill px-3" onclick="resolveAlert({{ $alert->id }})">
                                                Complete
                                            </button>
                                        </div>
                                    @elseif($alert->status === 'advised')
                                        <button class="btn btn-sm btn-success text-white rounded-pill px-3" onclick="resolveAlert({{ $alert->id }})">
                                            Mark Resolved
                                        </button>
                                    @else
                                        <button class="btn btn-sm btn-light text-muted border rounded-pill px-3" disabled>Completed</button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="icon-box rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                                        <iconify-icon icon="solar:check-circle-bold" class="fs-2"></iconify-icon>
                                    </div>
                                    <p class="text-muted small mb-0">No pending consultations</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Advice Modal -->
<div class="modal fade" id="adviceModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-bottom px-4 py-3 bg-light">
                <h6 class="modal-title fw-bold text-dark">
                    <iconify-icon icon="solar:chat-line-bold" class="me-2 align-middle text-accent"></iconify-icon>
                    Provide Medical Advice
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="adviceForm">
                    <input type="hidden" id="alertId" name="alert_id">
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Diagnosis / Observation</label>
                        <input type="text" class="form-control" name="diagnosis" placeholder="e.g. Mild Fever, Indigestion">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Prescription & Advice *</label>
                        <textarea class="form-control" name="custom_advice" rows="5" required placeholder="Medicine Name - Dosage - Duration&#10;e.g. Paracetamol - 500mg - 3 days&#10;Keep animal in shade."></textarea>
                        <div class="form-text small">List medicines and care instructions clearly.</div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 rounded-3">
                        <span class="fw-medium">Send Prescription</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Treatment Modal -->
<div class="modal fade" id="treatmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-bottom px-4 py-3 bg-light">
                <h6 class="modal-title fw-bold text-dark">
                    <iconify-icon icon="solar:clipboard-add-bold" class="me-2 align-middle text-info"></iconify-icon>
                    Record Treatment Details
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="treatmentAlertId">
                <div id="treatmentRows"></div>
                <button type="button" class="btn btn-outline-secondary btn-sm px-3 mt-3" onclick="addTreatmentRow()">
                    <iconify-icon icon="solar:add-circle-bold" class="me-1 align-middle"></iconify-icon> Add Another Entry
                </button>
            </div>
            <div class="modal-footer border-top-0 px-4 pb-4">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="submitTreatment()">
                    Save All Treatments
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openAdviceModal(id) {
        document.getElementById('alertId').value = id;
        new bootstrap.Modal(document.getElementById('adviceModal')).show();
    }

    function openTreatmentModal(id) {
        document.getElementById('treatmentAlertId').value = id;
        document.getElementById('treatmentRows').innerHTML = '';
        addTreatmentRow();
        new bootstrap.Modal(document.getElementById('treatmentModal')).show();
    }

    function addTreatmentRow() {
        const container = document.getElementById('treatmentRows');
        const index = container.children.length;
        
        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        const defaultDate = now.toISOString().slice(0, 16);

        const rowHtml = `
            <div class="treatment-row border border-light-subtle rounded-3 p-3 mb-3 position-relative bg-light">
                ${index > 0 ? '<button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="this.parentElement.remove()"></button>' : ''}
                <div class="mb-2">
                    <label class="form-label fw-semibold small text-muted">Date & Time</label>
                    <input type="datetime-local" class="form-control form-control-sm t-date" value="${defaultDate}">
                </div>
                <div class="mb-0">
                    <label class="form-label fw-semibold small text-muted">Treatment / Medicine / Injection</label>
                    <textarea class="form-control form-control-sm t-note" rows="2" placeholder="e.g. Injection A (5ml)..."></textarea>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', rowHtml);
    }

    function submitTreatment() {
        const id = document.getElementById('treatmentAlertId').value;
        const rows = document.querySelectorAll('.treatment-row');
        let combinedNotes = '';
        let hasData = false;

        rows.forEach(row => {
            const dateVal = row.querySelector('.t-date').value;
            const noteVal = row.querySelector('.t-note').value.trim();

            if (dateVal && noteVal) {
                try {
                    const dateObj = new Date(dateVal);
                    if (isNaN(dateObj.getTime())) {
                        throw new Error('Invalid date');
                    }
                    
                    const dateStr = dateObj.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' }) + 
                                    ', ' + dateObj.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                    
                    combinedNotes += `📅 Date: ${dateStr}\n💊 Treatment: ${noteVal}\n\n`;
                    hasData = true;
                } catch (e) {
                    console.error("Date parsing error:", e);
                    combinedNotes += `📅 Date: ${dateVal}\n💊 Treatment: ${noteVal}\n\n`;
                    hasData = true;
                }
            }
        });

        if(!hasData) {
            alert('Please enter at least one treatment detail (Date and Note).');
            return;
        }

        combinedNotes = combinedNotes.trim();

        fetch(`/api/alerts/${id}/treatment`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ 
                treatment_notes: combinedNotes,
                treatment_date: 'multiple'
            })
        })
        .then(async res => {
            const result = await res.json();
            if(!res.ok) throw result;
            return result;
        })
        .then(data => {
            if(data.success) {
                alert('✅ Treatments Recorded Successfully!');
                location.reload();
            } else {
                alert('❌ Error saving: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('❌ Error: ' + (err.message || 'Connection failed.'));
        });
    }

    function resolveAlert(id) {
        if(!confirm('Are you sure the treatment is complete and you want to close this case?')) return;

        fetch(`/api/alerts/${id}/resolve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(async res => {
            const result = await res.json();
            if(!res.ok) throw result;
            return result;
        })
        .then(data => {
            if(data.success) {
                alert('✅ Case marked as resolved.');
                location.reload();
            } else {
                alert('❌ Error: ' + (data.message || 'Action failed.'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('❌ Error: ' + (err.message || 'Connection failed.'));
        });
    }

    function confirmVisit(id) {
        if(!confirm('Confirm that you are going to visit the farm?')) return;
        
        fetch(`/api/alerts/${id}/confirm-visit`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(async res => {
            const result = await res.json();
            if(!res.ok) throw result;
            return result;
        })
        .then(data => {
            if(data.success) {
                alert('✅ Visit Confirmed!');
                location.reload();
            } else {
                alert('❌ Error: ' + (data.message || 'Action failed.'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('❌ Error: ' + (err.message || 'Connection failed.'));
        });
    }

    function checkIn(id) {
        if(!confirm('Confirm you have arrived at the farm?')) return;
        
        fetch(`/api/alerts/${id}/check-in`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(async res => {
            const result = await res.json();
            if(!res.ok) throw result;
            return result;
        })
        .then(data => {
            if(data.success) {
                alert('✅ Checked In Successfully!');
                location.reload();
            } else {
                alert('❌ Error: ' + (data.message || 'Action failed.'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('❌ Error: ' + (err.message || 'Connection failed.'));
        });
    }

    document.getElementById('adviceForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('alertId').value;
        const formData = new FormData(this);
        
        const diagnosis = formData.get('diagnosis');
        const customAdvice = formData.get('custom_advice');
        const combinedAdvice = `[Diagnosis: ${diagnosis}] \n\n${customAdvice}`;
        
        const data = {
            advice: combinedAdvice
        };

        fetch(`/api/alerts/${id}/advise`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        })
        .then(async res => {
            const result = await res.json();
            if(!res.ok) throw result;
            return result;
        })
        .then(data => {
            if(data.success) {
                alert('✅ Advice Sent Successfully!');
                location.reload();
            } else {
                alert('❌ Error: ' + (data.message || 'Action failed.'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('❌ Error: ' + (err.message || 'Connection failed.'));
        });
    });
</script>

@endpush
