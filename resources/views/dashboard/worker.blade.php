@extends('layouts.app')

@section('title', 'Farm Worker Dashboard')

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

<!-- Operational Stats Overview -->
<div class="row mb-4">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card card-stats shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-vibrant bg-vibrant-success me-3 shadow-success">
                        <i class="fas fa-cow"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted uppercase fs-tiny fw-semibold">Total Livestock</h6>
                        <h3 class="mb-0 fw-bold text-vibrant-success">{{ $totalAnimals }}</h3>
                    </div>
                </div>
                <small class="text-success fw-medium"><i class="bx bx-check-double me-1"></i> Active in Farm</small>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card card-stats shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-vibrant bg-vibrant-info me-3 shadow-info">
                        <i class="fas fa-hand-holding-water"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted uppercase fs-tiny fw-semibold">Today's Milk</h6>
                        <h3 class="mb-0 fw-bold text-vibrant-info">{{ number_format($todayMilk, 1) }} L</h3>
                    </div>
                </div>
                <small class="text-info fw-medium"><i class="bx bx-poll me-1"></i> Production Check</small>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-12 mb-4">
        <div class="card card-stats shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-vibrant bg-vibrant-danger me-3 shadow-danger">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted uppercase fs-tiny fw-semibold">Active Alerts</h6>
                        <h3 class="mb-0 fw-bold text-vibrant-danger">{{ $pendingAlerts }}</h3>
                    </div>
                </div>
                <small class="text-danger fw-medium"><i class="bx bx-error me-1"></i> My Submissions</small>
            </div>
        </div>
    </div>
</div>

<!-- Header with Action -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="mb-1 text-theme fw-bold">Daily Operations</h4>
        <p class="text-muted small mb-0">Worker: {{ auth()->user()->name }} 👨‍🌾</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button class="btn btn-danger d-flex align-items-center gap-2 px-4 shadow-sm fw-medium" data-bs-toggle="modal" data-bs-target="#emergencyAlertModal">
            <i class="fas fa-bell fs-5"></i>
            Emergency Alert
        </button>
    </div>
</div>

<!-- Worker Quick Actions -->
<div class="row g-4 mb-4">
    <!-- Daily Milk Entry Card -->
    <div class="col-12 col-lg-6">
        <div class="card border border-light-subtle rounded-3 shadow-sm h-100 card-hover">
            <div class="card-header bg-white border-bottom border-light pt-3 pb-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-box rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="fas fa-hand-holding-water fs-5 text-primary"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-theme mb-0">Daily Milk Entry</h6>
                        <p class="text-muted small mb-0">Record today's milk production</p>
                    </div>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <form id="dailyMilkForm">
                    <div class="mb-3">
                        <label for="date" class="form-label fw-semibold small text-muted">Date</label>
                        <input type="date" class="form-control" id="date" name="date" value="{{ date('Y-m-d') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="totalMilk" class="form-label fw-semibold small text-muted">Total Milk Quantity</label>
                        <div class="input-group">
                            <input type="number" step="0.01" class="form-control" id="totalMilk" name="total_milk_quantity" placeholder="e.g. 1500.50" required>
                            <span class="input-group-text bg-light text-muted border-start-0">Liters</span>
                        </div>
                    </div>

                    <h6 class="fw-bold text-theme text-uppercase mt-4 mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px;">Herd Breakdown</h6>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small text-muted">🐂 Milking Buffaloes</label>
                            <input type="number" class="form-control" name="total_buffaloes_milked" placeholder="0" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small text-muted">🤰 Pregnant</label>
                            <input type="number" class="form-control" name="pregnant_animals" placeholder="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small text-muted">🤒 Sick</label>
                            <input type="number" class="form-control" name="sick_animals" placeholder="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small text-muted">🐃 Male / Other</label>
                            <input type="number" class="form-control" name="male_animals" placeholder="0">
                        </div>
                    </div>

                    <div class="mb-3 mt-3">
                        <label for="notes" class="form-label fw-semibold small text-muted">Notes (Optional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Any observations or notes..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 shadow-sm">
                        <i class="fas fa-save me-2 align-middle"></i>
                        <span class="fw-medium">Save Record</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div class="col-12 col-lg-6">
        <div class="d-flex flex-column gap-4 h-100">
            <!-- Livestock Management Card -->
            <div class="card border border-light-subtle rounded-3 shadow-sm card-hover">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fas fa-cow fs-4 text-success"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-theme mb-0">Manage Livestock</h6>
                            <p class="text-muted small mb-0">Register new animals</p>
                        </div>
                    </div>
                    <button class="btn btn-outline-primary rounded-pill px-4" onclick="new bootstrap.Modal('#addAnimalModal').show()">
                        <i class="fas fa-plus-circle me-1 align-middle"></i> Add New
                    </button>
                </div>
            </div>

            <!-- Doctor Advice & Alerts Card -->
            <div class="card border border-light-subtle rounded-3 shadow-sm flex-grow-1 card-hover">
                <div class="card-header bg-white border-bottom border-light pt-3 pb-2 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-stethoscope text-success fs-5"></i>
                        <div>
                            <h6 class="fw-bold text-theme mb-0">Doctor Advice & Alerts</h6>
                        </div>
                    </div>
                    <a href="{{ url('/alerts') }}" class="btn btn-sm btn-link text-decoration-none">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" id="alertsList" style="max-height: 400px; overflow-y: auto;">
                        @php
                            $myAlerts = \App\Models\EmergencyAlert::where('user_id', auth()->id())->latest()->take(5)->get();
                        @endphp

                        @forelse($myAlerts as $alert)
                            <div class="list-group-item border-bottom-0 px-4 py-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge rounded-pill px-2 py-1 small border 
                                        @if($alert->status === 'advised') bg-success-subtle text-success border-success-subtle
                                        @elseif($alert->status === 'pending') bg-warning-subtle text-warning border-warning-subtle
                                        @else bg-info-subtle text-info border-info-subtle
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $alert->status)) }}
                                    </span>
                                    <small class="text-muted" style="font-size: 0.75rem;">{{ $alert->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1 fw-medium text-dark small">{{ $alert->message }}</p>
                                
                                @if($alert->doctor_advice)
                                    <div class="mt-2 p-2 rounded-3 bg-light border border-light-subtle">
                                        <p class="mb-1 small text-dark fw-semibold">
                                            <i class="fas fa-user-md align-middle me-1 text-primary"></i>
                                            Doctor's Advice:
                                        </p>
                                        <p class="mb-0 small text-muted">{{ $alert->doctor_advice }}</p>

                                        @if($alert->status === 'advised')
                                            <button class="btn btn-sm btn-danger w-100 mt-2 rounded-2 py-1" onclick="confirmUrgentVisit({{ $alert->id }})">
                                                <small>Urgent: Call Doctor</small>
                                            </button>
                                        @elseif($alert->status === 'visit_confirmed')
                                            <div class="mt-2 text-primary fw-bold small">
                                                <i class="fas fa-walking me-1"></i>
                                                Doctor is coming...
                                            </div>
                                        @elseif($alert->status === 'on_site')
                                            <div class="mt-2 text-success fw-bold small">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                Doctor is On Site
                                            </div>
                                        @elseif($alert->status === 'under_treatment')
                                            <div class="mt-2 text-info fw-bold small">
                                                <i class="fas fa-syringe me-1"></i>
                                                Under Treatment
                                            </div>
                                            @if($alert->treatment_notes)
                                                <div class="mt-2 p-2 bg-white rounded border border-light-subtle small text-muted">
                                                    <strong>Plan:</strong> {!! nl2br(e($alert->treatment_notes)) !!}
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center p-5">
                                <div class="icon-box rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                                    <i class="fas fa-check-circle fs-2 text-success"></i>
                                </div>
                                <p class="text-muted small mb-0">No recent alerts</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Emergency Alert Modal -->
<div class="modal fade" id="emergencyAlertModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-bottom px-4 py-3 bg-danger-subtle">
                <h6 class="modal-title fw-bold text-danger">
                    <i class="fas fa-bell me-2 align-middle"></i>
                    Send Emergency Alert
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="emergencyAlertForm">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Affected Animal (Optional)</label>
                        <select class="form-select" name="animal_id">
                            <option value="">-- Select Animal --</option>
                            @foreach(\App\Models\Animal::where('status', 'active')->get() as $animal)
                                <option value="{{ $animal->id }}">{{ $animal->animal_id }} - {{ $animal->tag_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Current Temperature (°F) (Optional)</label>
                        <input type="number" step="0.1" class="form-control" name="temperature" placeholder="e.g. 102.5">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Situation Description *</label>
                        <textarea class="form-control" name="message" rows="4" required placeholder="Describe symptoms, behavior, or emergency details..."></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold small text-muted">Attach Photo (Optional)</label>
                        <input type="file" class="form-control" name="attachment" accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-danger w-100 py-2 rounded-3 shadow-sm">
                        <span class="fw-bold">Send Alert Immediately</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Animal Modal -->
<div class="modal fade" id="addAnimalModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-bottom px-4 py-3 bg-light">
                <h6 class="modal-title fw-bold text-dark">
                    <i class="fas fa-plus-circle me-2 align-middle text-accent"></i>
                    Register New Animal
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="addAnimalForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">Animal ID *</label>
                            <input type="text" class="form-control" name="animal_id" placeholder="e.g. A-101" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">Tag Number *</label>
                            <input type="text" class="form-control" name="tag_number" placeholder="e.g. 5055" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">Name *</label>
                            <input type="text" class="form-control" name="name" placeholder="e.g. Bella" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">Breed *</label>
                            <select class="form-select" name="breed" required>
                                <option value="">-- Select Breed --</option>
                                <option value="holstein">Holstein</option>
                                <option value="jersey">Jersey</option>
                                <option value="guernsey">Guernsey</option>
                                <option value="ayrshire">Ayrshire</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">Gender *</label>
                            <select class="form-select" name="gender" required>
                                <option value="female">Female</option>
                                <option value="male">Male</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">Birth Date *</label>
                            <input type="date" class="form-control" name="birth_date" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">Weight (kg)</label>
                            <input type="number" step="0.01" class="form-control" name="weight" placeholder="e.g. 450.5">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">Status *</label>
                            <select class="form-select" name="status" required>
                                <option value="active">Active (In Farm)</option>
                                <option value="sold">Sold</option>
                                <option value="deceased">Deceased</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 mt-4 rounded-3 shadow-sm">
                        <i class="fas fa-save me-2 align-middle"></i>
                        <span class="fw-bold">Register Animal</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('dailyMilkForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        // Sanitize optional numeric fields
        ['sick_animals', 'pregnant_animals', 'male_animals'].forEach(field => {
            if (data[field] === "") data[field] = 0;
        });

        fetch('/api/daily-milk-records', {
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
                alert('✅ Record saved successfully!');
                this.reset();
                document.getElementById('date').value = '{{ date('Y-m-d') }}';
            } else {
                alert('❌ Error: ' + (data.message || 'Saving failed.'));
            }
        })
        .catch(err => {
            console.error('Save error:', err);
            alert('❌ Error: ' + (err.message || 'Error communicating with server.'));
        });
    });

    document.getElementById('emergencyAlertForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        // Clean up empty animal_id so Laravel's nullable check works correctly
        if (formData.get('animal_id') === "") formData.delete('animal_id');
        if (formData.get('temperature') === "") formData.delete('temperature');

        fetch('/api/alerts', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        })
        .then(async res => {
            const contentType = res.headers.get("content-type");
            if (contentType && contentType.indexOf("application/json") !== -1) {
                const result = await res.json();
                if(!res.ok) throw result;
                return result;
            } else {
                const text = await res.text();
                throw { message: `Server Error (${res.status})`, raw: text };
            }
        })
        .then(data => {
            if(data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Alert Sent!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: data.message || 'Something went wrong!'
                });
            }
        })
        .catch(err => {
            console.error('Alert error:', err);
            let errMsg = 'Could not send alert.';
            if(err.errors) {
                errMsg = Object.values(err.errors).flat().join('\n');
            } else if(err.message) {
                errMsg = err.message;
            }
            
            let footer = '';
            if (err.raw) {
                console.log("Raw Server Response:", err.raw);
                const match = err.raw.match(/<title>(.*?)<\/title>/);
                if (match && match[1]) {
                    footer = `Details: ${match[1]}`;
                }
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Error Sending Alert',
                text: errMsg,
                footer: footer || 'Please check your connection or try again later.'
            });
        });
    });

    document.getElementById('addAnimalForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        fetch('/api/animals', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                alert('✅ Animal Registered Successfully!');
                bootstrap.Modal.getInstance(document.getElementById('addAnimalModal')).hide();
                this.reset();
            } else {
                alert('❌ Error registering animal: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('❌ Error communicating with server.');
        });
    });

    function confirmUrgentVisit(id) {
        if(!confirm('⚠️ This will alert BOTH the Doctor and the Admin. Proceed?')) return;
        fetch(`/api/alerts/${id}/request-urgent`, {
             method: 'POST',
             headers: {
                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                 'Content-Type': 'application/json'
             }
         })
         .then(res => res.json())
         .then(data => {
             alert(data.message);
             location.reload();
         })
         .catch(err => {
             console.error(err);
             alert('❌ Error sending urgent request.');
         });
    }
</script>
@endpush
