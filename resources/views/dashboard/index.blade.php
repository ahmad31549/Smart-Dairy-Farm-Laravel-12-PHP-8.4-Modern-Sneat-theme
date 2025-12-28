@extends('layouts.app')

@section('title', 'Admin Dashboard - Dairy Farm Management')

@section('content')
<div class="row">
    <!-- Welcome Card -->
    <div class="col-lg-12 mb-4 order-0">
      <div class="card">
        <div class="d-flex align-items-end row">
          <div class="col-sm-7">
            <div class="card-body">
              <h5 class="card-title text-primary">Welcome Back, {{ auth()->user()->name ?? 'Manager' }}! 🎉</h5>
              <p class="mb-4">
                You have <span class="fw-bold">{{ count($pendingAlerts) ?? 0 }}</span> pending emergency alerts. Check the new updates in your dashboard.
              </p>
              <a href="{{ count($pendingAlerts) > 0 ? '#alerts-section' : url('/alerts') }}" class="btn btn-sm btn-outline-primary">View Alerts</a>
            </div>
          </div>
          <div class="col-sm-5 text-center text-sm-left">
            <div class="card-body pb-0 px-0 px-md-4">
              <img
                src="{{ asset('assets/sneat/img/dairy-welcome.png') }}"
                height="140"
                alt="Smart Dairy Welcome"
                data-app-dark-img="dairy-welcome.png"
                data-app-light-img="dairy-welcome.png"
              />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Stats -->
    <div class="col-lg-12 col-md-12 order-1">
      <div class="row">
        <!-- Total Livestock -->
        <div class="col-lg-3 col-md-6 col-12 mb-4">
          <div class="card card-stats shadow-sm border-0 h-100">
            <div class="card-body">
              <div class="card-title d-flex align-items-start justify-content-between mb-4">
                <div class="avatar-vibrant bg-vibrant-success shadow-success">
                    <i class="fas fa-cow"></i>
                </div>
                <div class="dropdown">
                  <button
                    class="btn p-0"
                    type="button"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                  >
                    <i class="bx bx-dots-vertical-rounded"></i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="javascript:void(0);">View More</a>
                  </div>
                </div>
              </div>
              <span class="text-muted d-block mb-1 fw-medium uppercase fs-tiny">Total Livestock</span>
              <h3 class="card-title mb-2 fw-bold">{{ $totalAnimals ?? 0 }}</h3>
              <small class="text-vibrant-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> +2.5%</small>
            </div>
          </div>
        </div>

        <!-- Daily Milk -->
        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card card-stats shadow-sm border-0 h-100">
              <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between mb-4">
                  <div class="avatar-vibrant bg-vibrant-info shadow-info">
                      <i class="fas fa-vial"></i>
                  </div>
                </div>
                <span class="text-muted d-block mb-1 fw-medium uppercase fs-tiny">Daily Milk</span>
                <h3 class="card-title mb-2 fw-bold text-vibrant-info">{{ number_format($dailyMilk ?? 0) }}</h3>
                <small class="text-muted fw-medium small">Liters (Today)</small>
              </div>
            </div>
        </div>

        <!-- Active Staff -->
        @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'admin' || auth()->user()->role === 'manager')
        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card card-stats shadow-sm border-0 h-100">
              <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between mb-4">
                  <div class="avatar-vibrant bg-vibrant-warning shadow-warning">
                      <i class="fas fa-user-tie"></i>
                  </div>
                </div>
                <span class="text-muted d-block mb-1 fw-medium uppercase fs-tiny">Active Staff</span>
                <h3 class="card-title mb-2 fw-bold text-vibrant-warning">{{ $activeEmployees ?? 0 }}</h3>
                <small class="text-muted fw-medium small">On-site now</small>
              </div>
            </div>
        </div>
        @endif

        <!-- Revenue -->
        @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'admin')
        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card card-stats shadow-sm border-0 h-100">
              <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between mb-4">
                  <div class="avatar-vibrant bg-vibrant-primary shadow-primary">
                      <i class="fas fa-coins"></i>
                  </div>
                </div>
                <span class="text-muted d-block mb-1 fw-medium uppercase fs-tiny">Revenue</span>
                <h3 class="card-title mb-2 fw-bold text-vibrant-primary">Rs {{ number_format($monthlyRevenue ?? 0) }}</h3>
                <small class="text-muted fw-medium small">Total Monthly</small>
              </div>
            </div>
        </div>
        @endif
      </div>
    </div>
</div>

<div class="row">
    <!-- Milk Production Chart -->
    <div class="col-md-6 col-lg-8 mb-4 order-0">
      <div class="card h-100">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h5 class="card-title m-0 me-2">Milk Production Analytics</h5>
          <a href="{{ url('/milk-tracking') }}" class="btn btn-sm btn-outline-primary fw-medium">View Report</a>
        </div>
        <div class="card-body">
             <div id="milkProductionChart" style="min-height: 320px;"></div>
        </div>
      </div>
    </div>

    <!-- Health Status -->
    <div class="col-md-6 col-lg-4 order-1 mb-4">
      <div class="card h-100">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h5 class="card-title m-0 me-2">Herd Health Status</h5>
        </div>
        <div class="card-body">
            <div id="healthStatusChart" style="min-height: 200px;"></div>
        </div>
      </div>
    </div>
</div>

<!-- Pending Alerts -->
@if(isset($pendingAlerts) && count($pendingAlerts) > 0)
<div class="row" id="alerts-section">
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header bg-vibrant-danger d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-2 animate-pulse"></i>
                <h5 class="card-title mb-0 text-white fw-bold">Emergency Alerts</h5>
            </div>
            <div class="table-responsive text-nowrap">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Alert</th>
                    <th>Time</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach($pendingAlerts as $alert)
                    <tr>
                      <td><i class="fas fa-exclamation-triangle text-danger me-3"></i> <strong>{{ Str::limit($alert->message, 80) }}</strong></td>
                      <td>{{ $alert->created_at->diffForHumans() }}</td>
                      <td>
                        <div class="d-flex gap-2">
                          <button class="btn btn-sm btn-outline-info" onclick="showAlertDetails('{{ addslashes($alert->message) }}')">View</button>
                          <button class="btn btn-sm btn-outline-primary" onclick="forwardAlert({{ $alert->id }})">Forward</button>
                        </div>
                      </td>
                    </tr>
                    @endforeach
                </tbody>
              </table>
            </div>
          </div>
    </div>
</div>
@endif

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-none bg-transparent border-0 mb-4">
            <div class="card-header px-0 pt-0 d-flex align-items-center justify-content-between">
                <h5 class="mb-0 fw-bold">Quick Management</h5>
            </div>
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <a href="{{ url('/animal-health') }}" class="card card-stats border-0 h-100 text-center p-3 text-decoration-none">
                        <div class="avatar-vibrant bg-vibrant-danger mx-auto mb-3 shadow-danger">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <h6 class="mb-1 fw-bold text-dark">Health Check</h6>
                        <small class="text-muted">Monitor Vitals</small>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="{{ url('/milk-tracking') }}" class="card card-stats border-0 h-100 text-center p-3 text-decoration-none">
                        <div class="avatar-vibrant bg-vibrant-info mx-auto mb-3 shadow-info">
                            <i class="fas fa-vial"></i>
                        </div>
                        <h6 class="mb-1 fw-bold text-dark">Record Milk</h6>
                        <small class="text-muted">Track Yield</small>
                    </a>
                </div>
                @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'admin' || auth()->user()->role === 'manager')
                <div class="col-6 col-md-3">
                    <a href="{{ url('/attendance') }}" class="card card-stats border-0 h-100 text-center p-3 text-decoration-none">
                        <div class="avatar-vibrant bg-vibrant-warning mx-auto mb-3 shadow-warning">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <h6 class="mb-1 fw-bold text-dark">Attendance</h6>
                        <small class="text-muted">Staff Presence</small>
                    </a>
                </div>
                @endif
                <div class="col-6 col-md-3">
                    <a href="{{ url('/expenses') }}" class="card card-stats border-0 h-100 text-center p-3 text-decoration-none">
                        <div class="avatar-vibrant bg-vibrant-primary mx-auto mb-3 shadow-primary">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <h6 class="mb-1 fw-bold text-dark">Expenses</h6>
                        <small class="text-muted">Manage Costs</small>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function forwardAlert(id) {
        Swal.fire({
            title: 'Forward Alert?',
            text: 'This will forward the alert to the Veterinary Doctor',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#696cff',
            cancelButtonColor: '#8592a3',
            confirmButtonText: 'Yes, Forward',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Forwarding...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch('/api/alerts/' + id + '/forward', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    Swal.close();
                    if(data.success) {
                        Toast.fire({
                            icon: 'success',
                            title: 'Alert Forwarded!',
                            text: 'The veterinary doctor has been notified'
                        });
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Failed to forward alert'
                        });
                    }
                })
                .catch(err => {
                    Swal.close();
                    console.error('Forward error:', err);
                    Toast.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to forward alert. Please try again.'
                    });
                });
            }
        });
    }

    function showAlertDetails(message) {
        Swal.fire({
            title: 'Alert Details',
            html: '<div style="text-align: left; padding: 10px;">' + message + '</div>',
            icon: 'info',
            confirmButtonColor: '#696cff',
            confirmButtonText: 'Close'
        });
    }

document.addEventListener('DOMContentLoaded', function() {
    // Milk Production Chart
    var milkData = {!! json_encode($milkData ?? [0,0,0,0,0,0,0]) !!};
    var milkLabels = {!! json_encode($milkLabels ?? ['Day 1','Day 2','Day 3','Day 4','Day 5','Day 6','Day 7']) !!};

    var milkOptions = {
        series: [{ name: 'Milk (L)', data: milkData }],
        chart: {
            type: 'area',
            height: 320,
            fontFamily: 'Public Sans, sans-serif', // Sneat font
            toolbar: { show: false },
            zoom: { enabled: false }
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2, colors: ['#696cff'] }, // Sneat Primary
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.1,
                opacityTo: 0.0,
                stops: [0, 100],
                colorStops: [{ offset: 0, color: '#696cff', opacity: 0.1 }, { offset: 100, color: '#696cff', opacity: 0 }]
            }
        },
        xaxis: {
            categories: milkLabels,
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: { style: { colors: '#a1acb8', fontSize: '13px' } }
        },
        yaxis: {
            labels: {
                style: { colors: '#a1acb8', fontSize: '13px' },
                formatter: (value) => { return value }
            }
        },
        grid: {
            borderColor: '#e2e8f0',
            strokeDashArray: 4,
            padding: { top: 0, right: 0, bottom: 0, left: 10 }
        },
        theme: { mode: 'light' },
        tooltip: { theme: 'light' }
    };
    if(document.querySelector("#milkProductionChart")) {
        new ApexCharts(document.querySelector("#milkProductionChart"), milkOptions).render();
    }

    // Health Status Chart
    var healthyCount = {{ $healthyAnimals ?? 0 }};
    var treatmentCount = {{ $underTreatment ?? 0 }};
    var sickCount = {{ $sickAnimals ?? 0 }};
    var totalHealth = healthyCount + treatmentCount + sickCount;

    var healthOptions = {
        series: [healthyCount, treatmentCount, sickCount],
        chart: {
            type: 'donut',
            height: 250,
            fontFamily: 'Public Sans, sans-serif',
        },
        labels: ['Healthy', 'Treatment', 'Sick'],
        colors: ['#71dd37', '#696cff', '#ff3e1d'], // Sneat Success, Primary, Danger
        legend: {
            position: 'bottom', 
            fontSize: '13px',
            markers: { radius: 12 }
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '65%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total',
                            color: '#a1acb8',
                            formatter: function () { return totalHealth; }
                        }
                    }
                }
            }
        },
        dataLabels: { enabled: false },
        stroke: { show: false }
    };
    if(document.querySelector("#healthStatusChart")) {
        new ApexCharts(document.querySelector("#healthStatusChart"), healthOptions).render();
    }
});
</script>
@endpush
