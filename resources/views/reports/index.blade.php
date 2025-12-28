@extends('layouts.app')

@section('title', 'Reports & Analytics')

@push('styles')
<style>
    .border-left-primary { border-left: 0.25rem solid var(--primary-600) !important; }
    .border-left-success { border-left: 0.25rem solid var(--success-main) !important; }
    .border-left-warning { border-left: 0.25rem solid var(--warning-main) !important; }
    .border-left-info { border-left: 0.25rem solid var(--info-main) !important; }
    .border-left-danger { border-left: 0.25rem solid var(--danger-main) !important; }

    .report-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
        border: none;
    }

    .report-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .report-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
    }

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

    .report-result {
        background-color: #f8f9fc;
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-top: 1rem;
    }

    .stat-item {
        padding: 1rem;
        border-radius: 0.5rem;
        background: white;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        margin-bottom: 1rem;
    }

    .stat-label {
        font-size: 0.875rem;
        color: #858796;
        margin-bottom: 0.25rem;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: bold;
        color: #5a5c69;
    }

    .table-report {
        background: white;
        border-radius: 0.5rem;
        overflow: hidden;
    }

    @media (max-width: 768px) {
        .report-icon {
            font-size: 2rem;
        }
    }
</style>
@endpush

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Reports & Analytics</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportAllReports()">
                <i class="fas fa-download me-1"></i>Export All
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="printReports()">
                <i class="fas fa-print me-1"></i>Print
            </button>
        </div>
    </div>
</div>

<!-- Report Type Cards -->
<div class="row mb-4">
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card report-card shadow border-left-primary" onclick="selectReport('animal_health')">
            <div class="card-body text-center">
                <i class="fas fa-heartbeat report-icon text-primary"></i>
                <h5 class="font-weight-bold">Animal Health Report</h5>
                <p class="text-muted mb-0">Health records, checkups, and treatments</p>
                <div class="mt-3">
                    <span class="badge bg-primary">{{ $totalHealthRecords }} Records</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card report-card shadow border-left-success" onclick="selectReport('milk_production')">
            <div class="card-body text-center">
                <i class="fas fa-tint report-icon text-success"></i>
                <h5 class="font-weight-bold">Milk Production Report</h5>
                <p class="text-muted mb-0">Daily production and quality analysis</p>
                <div class="mt-3">
                    <span class="badge bg-success">{{ $totalAnimals }} Animals</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card report-card shadow border-left-info" onclick="selectReport('employee')">
            <div class="card-body text-center">
                <i class="fas fa-users report-icon text-info"></i>
                <h5 class="font-weight-bold">Employee Report</h5>
                <p class="text-muted mb-0">Attendance, payroll, and performance</p>
                <div class="mt-3">
                    <span class="badge bg-info">{{ $totalEmployees }} Employees</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card report-card shadow border-left-warning" onclick="selectReport('financial')">
            <div class="card-body text-center">
                <i class="fas fa-dollar-sign report-icon text-warning"></i>
                <h5 class="font-weight-bold">Financial Report</h5>
                <p class="text-muted mb-0">Income, expenses, and profit analysis</p>
                <div class="mt-3">
                    <span class="badge bg-warning">{{ $totalIncome }} Income | {{ $totalExpenses }} Expenses</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card report-card shadow border-left-danger" onclick="selectReport('inventory')">
            <div class="card-body text-center">
                <i class="fas fa-boxes report-icon text-danger"></i>
                <h5 class="font-weight-bold">Inventory Report</h5>
                <p class="text-muted mb-0">Stock levels and supply management</p>
                <div class="mt-3">
                    <span class="badge bg-danger">Stock Status</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card report-card shadow border-left-primary" onclick="selectReport('comprehensive')">
            <div class="card-body text-center">
                <i class="fas fa-chart-pie report-icon text-primary"></i>
                <h5 class="font-weight-bold">Comprehensive Report</h5>
                <p class="text-muted mb-0">Complete farm operations overview</p>
                <div class="mt-3">
                    <span class="badge bg-primary">All Modules</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Report Generation Form -->
<div class="row mb-4" id="reportGenerationSection" style="display: none;">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white py-3">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-file-alt me-2"></i>Generate Report
                </h6>
            </div>
            <div class="card-body p-4">
                <form id="reportGenerationForm">
                    <div class="row g-3 align-items-end">
                        <!-- Report Type (Hidden) -->
                        <input type="hidden" id="reportType" name="report_type">

                        <!-- Report Name Display -->
                        <div class="col-12 mb-3">
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Selected Report:</strong> <span id="selectedReportName">None</span>
                            </div>
                        </div>

                        <!-- Start Date -->
                        <div class="col-lg-3 col-md-6">
                            <label for="startDate" class="form-label fw-semibold text-secondary mb-2">
                                <i class="fas fa-calendar-alt text-primary me-2"></i>Start Date
                            </label>
                            <input type="date" class="form-control form-control-lg border-2" id="startDate" name="start_date">
                        </div>

                        <!-- End Date -->
                        <div class="col-lg-3 col-md-6">
                            <label for="endDate" class="form-label fw-semibold text-secondary mb-2">
                                <i class="fas fa-calendar-alt text-success me-2"></i>End Date
                            </label>
                            <input type="date" class="form-control form-control-lg border-2" id="endDate" name="end_date">
                        </div>

                        <!-- Report Format -->
                        <div class="col-lg-3 col-md-6">
                            <label for="reportFormat" class="form-label fw-semibold text-secondary mb-2">
                                <i class="fas fa-file-alt text-info me-2"></i>Format
                            </label>
                            <select class="form-select form-select-lg border-2" id="reportFormat" name="format">
                                <option value="summary">Summary</option>
                                <option value="detailed">Detailed</option>
                            </select>
                        </div>

                        <!-- Generate Button -->
                        <div class="col-lg-3 col-md-6">
                            <button type="button" class="btn btn-primary btn-lg w-100" onclick="generateReport()">
                                <i class="fas fa-cogs me-2"></i>Generate Report
                            </button>
                        </div>
                    </div>

                    <!-- Quick Date Filters -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary mb-2">Quick Filters:</label>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('today')">Today</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('week')">This Week</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('month')">This Month</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('year')">This Year</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearDateRange()">Clear</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Report Results -->
<div class="row" id="reportResultsSection" style="display: none;">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-bar me-2"></i>Report Results
                </h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow">
                        <a class="dropdown-item" href="#" onclick="downloadReport('pdf')">
                            <i class="fas fa-file-pdf me-2"></i>Download PDF
                        </a>
                        <a class="dropdown-item" href="#" onclick="downloadReport('excel')">
                            <i class="fas fa-file-excel me-2"></i>Download Excel
                        </a>
                        <a class="dropdown-item" href="#" onclick="downloadReport('csv')">
                            <i class="fas fa-file-csv me-2"></i>Download CSV
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#" onclick="printReport()">
                            <i class="fas fa-print me-2"></i>Print Report
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="reportContent">
                    <!-- Report content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Spinner -->
<div class="text-center my-5" id="loadingSpinner" style="display: none;">
    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
        <span class="visually-hidden">Loading...</span>
    </div>
    <p class="mt-3 text-muted">Generating report...</p>
</div>
@endsection

@push('scripts')
<script>
    let currentReportType = null;

    function selectReport(reportType) {
        currentReportType = reportType;
        document.getElementById('reportType').value = reportType;

        // Map report types to display names
        const reportNames = {
            'animal_health': 'Animal Health Report',
            'milk_production': 'Milk Production Report',
            'employee': 'Employee Report',
            'financial': 'Financial Report',
            'inventory': 'Inventory Report',
            'comprehensive': 'Comprehensive Report'
        };

        document.getElementById('selectedReportName').textContent = reportNames[reportType];

        // Show report generation section
        document.getElementById('reportGenerationSection').style.display = 'block';
        document.getElementById('reportResultsSection').style.display = 'none';

        // Scroll to form
        document.getElementById('reportGenerationSection').scrollIntoView({ behavior: 'smooth' });

        // Set default date range to current month
        setDateRange('month');
    }

    function setDateRange(period) {
        const today = new Date();
        let startDate, endDate;

        switch (period) {
            case 'today':
                startDate = endDate = today.toISOString().split('T')[0];
                break;
            case 'week':
                const weekStart = new Date(today);
                weekStart.setDate(today.getDate() - today.getDay());
                startDate = weekStart.toISOString().split('T')[0];
                endDate = today.toISOString().split('T')[0];
                break;
            case 'month':
                startDate = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
                endDate = today.toISOString().split('T')[0];
                break;
            case 'year':
                startDate = new Date(today.getFullYear(), 0, 1).toISOString().split('T')[0];
                endDate = today.toISOString().split('T')[0];
                break;
        }

        document.getElementById('startDate').value = startDate;
        document.getElementById('endDate').value = endDate;
    }

    function clearDateRange() {
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
    }

    function generateReport() {
        if (!currentReportType) {
            alert('Please select a report type first');
            return;
        }

        const formData = {
            report_type: currentReportType,
            start_date: document.getElementById('startDate').value,
            end_date: document.getElementById('endDate').value,
            format: document.getElementById('reportFormat').value
        };

        // Show loading spinner
        document.getElementById('loadingSpinner').style.display = 'block';
        document.getElementById('reportResultsSection').style.display = 'none';

        fetch('/api/reports/generate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('loadingSpinner').style.display = 'none';

            if (data.success) {
                displayReportResults(data);
                document.getElementById('reportResultsSection').style.display = 'block';
                document.getElementById('reportResultsSection').scrollIntoView({ behavior: 'smooth' });
            } else {
                alert('Error generating report: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('loadingSpinner').style.display = 'none';
            alert('Error generating report. Please try again.');
        });
    }

    function displayReportResults(data) {
        const reportContent = document.getElementById('reportContent');
        let html = '';

        // Report Header
        html += `
            <div class="report-result">
                <div class="row mb-4">
                    <div class="col-12">
                        <h4 class="font-weight-bold text-primary">${data.report_type.replace('_', ' ').toUpperCase()}</h4>
                        <p class="text-muted mb-0">
                            <i class="fas fa-calendar me-2"></i>
                            Period: ${data.start_date || 'All Time'} to ${data.end_date || 'Present'}
                        </p>
                        <p class="text-muted">
                            <i class="fas fa-file-alt me-2"></i>
                            Format: ${data.format.charAt(0).toUpperCase() + data.format.slice(1)}
                        </p>
                    </div>
                </div>
        `;

        // Summary Statistics
        const summary = data.data;
        html += '<div class="row">';

        // Generate stat cards based on report type
        Object.keys(summary).forEach(key => {
            if (typeof summary[key] !== 'object' && key !== 'records' && key !== 'employees' && key !== 'items' && key !== 'income_records' && key !== 'expense_records') {
                const label = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                const value = typeof summary[key] === 'number'
                    ? (key.includes('amount') || key.includes('salary') || key.includes('income') || key.includes('expense') || key.includes('profit') || key.includes('value')
                        ? 'Rs. ' + summary[key].toFixed(2).toLocaleString()
                        : summary[key].toLocaleString())
                    : summary[key];

                html += `
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stat-item">
                            <div class="stat-label">${label}</div>
                            <div class="stat-value">${value}</div>
                        </div>
                    </div>
                `;
            }
        });

        html += '</div>';

        // Detailed Records (if available)
        if (data.format === 'detailed' && (summary.records || summary.employees || summary.items)) {
            html += '<div class="mt-4">';
            html += '<h5 class="font-weight-bold mb-3">Detailed Records</h5>';
            html += '<div class="table-responsive table-report">';
            html += '<table class="table table-bordered table-hover mb-0">';

            // Table headers based on report type
            if (summary.records) {
                html += '<thead><tr><th>Animal ID</th><th>Name</th><th>Breed</th><th>Status</th><th>Date</th><th>Veterinarian</th></tr></thead><tbody>';
                summary.records.forEach(record => {
                    html += `<tr>
                        <td>${record.animal_id}</td>
                        <td>${record.animal_name}</td>
                        <td>${record.breed}</td>
                        <td><span class="badge bg-${record.health_status === 'healthy' ? 'success' : record.health_status === 'treatment' ? 'warning' : 'danger'}">${record.health_status}</span></td>
                        <td>${record.check_date}</td>
                        <td>${record.veterinarian || '-'}</td>
                    </tr>`;
                });
            } else if (summary.employees) {
                html += '<thead><tr><th>Employee ID</th><th>Name</th><th>Position</th><th>Department</th><th>Salary</th><th>Status</th></tr></thead><tbody>';
                summary.employees.forEach(emp => {
                    html += `<tr>
                        <td>${emp.employee_id}</td>
                        <td>${emp.name}</td>
                        <td>${emp.position}</td>
                        <td>${emp.department}</td>
                        <td>Rs. ${parseFloat(emp.salary).toFixed(2)}</td>
                        <td><span class="badge bg-${emp.status === 'active' ? 'success' : 'secondary'}">${emp.status}</span></td>
                    </tr>`;
                });
            } else if (summary.items) {
                html += '<thead><tr><th>Item Name</th><th>Category</th><th>Quantity</th><th>Unit Price</th><th>Total Value</th><th>Status</th></tr></thead><tbody>';
                summary.items.forEach(item => {
                    html += `<tr>
                        <td>${item.item_name}</td>
                        <td>${item.category}</td>
                        <td>${item.quantity} ${item.unit}</td>
                        <td>Rs. ${parseFloat(item.unit_price).toFixed(2)}</td>
                        <td>Rs. ${parseFloat(item.total_value).toFixed(2)}</td>
                        <td><span class="badge bg-${item.status === 'In Stock' ? 'success' : 'warning'}">${item.status}</span></td>
                    </tr>`;
                });
            }

            html += '</tbody></table></div></div>';
        }

        // Category Breakdowns (for financial and other reports)
        if (summary.income_by_source || summary.expenses_by_category || summary.by_department) {
            html += '<div class="mt-4"><h5 class="font-weight-bold mb-3">Category Breakdown</h5><div class="row">';

            if (summary.income_by_source) {
                html += '<div class="col-md-6"><h6>Income by Source</h6><ul class="list-group">';
                Object.entries(summary.income_by_source).forEach(([key, value]) => {
                    html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                        ${key.replace('_', ' ').toUpperCase()}
                        <span class="badge bg-success">Rs. ${parseFloat(value).toFixed(2)}</span>
                    </li>`;
                });
                html += '</ul></div>';
            }

            if (summary.expenses_by_category) {
                html += '<div class="col-md-6"><h6>Expenses by Category</h6><ul class="list-group">';
                Object.entries(summary.expenses_by_category).forEach(([key, value]) => {
                    html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                        ${key}
                        <span class="badge bg-danger">Rs. ${parseFloat(value).toFixed(2)}</span>
                    </li>`;
                });
                html += '</ul></div>';
            }

            html += '</div></div>';
        }

        html += '</div>';
        reportContent.innerHTML = html;
    }

    function downloadReport(format) {
        if (!currentReportType) {
            alert('Please generate a report first');
            return;
        }

        const params = new URLSearchParams({
            report_type: currentReportType,
            start_date: document.getElementById('startDate').value,
            end_date: document.getElementById('endDate').value,
            format: document.getElementById('reportFormat').value
        });

        window.location.href = '/reports/export?' + params.toString();
    }

    function printReport() {
        if (!currentReportType) {
            alert('Please generate a report first');
            return;
        }

        const params = new URLSearchParams({
            report_type: currentReportType,
            start_date: document.getElementById('startDate').value,
            end_date: document.getElementById('endDate').value,
            format: document.getElementById('reportFormat').value
        });

        window.open('/reports/print?' + params.toString(), '_blank');
    }

    function exportAllReports() {
        if (!currentReportType) {
            alert('Please generate a report first');
            return;
        }

        downloadReport('pdf');
    }

    function printReports() {
        if (!currentReportType) {
            alert('Please generate a report first');
            return;
        }

        printReport();
    }
</script>
@endpush
