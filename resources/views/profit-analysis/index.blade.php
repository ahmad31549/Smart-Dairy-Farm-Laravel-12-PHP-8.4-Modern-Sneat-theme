@extends('layouts.app')

@section('title', 'Profit Analysis')

@push('styles')
<style>
    .border-left-success { border-left: 0.25rem solid var(--success-main) !important; }
    .border-left-danger { border-left: 0.25rem solid var(--danger-main) !important; }
    .border-left-info { border-left: 0.25rem solid var(--info-main) !important; }
    .border-left-warning { border-left: 0.25rem solid var(--warning-main) !important; }
    .border-left-primary { border-left: 0.25rem solid var(--primary-600) !important; }

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

    .chart-container {
        position: relative;
        height: 400px;
    }

    .stat-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .transaction-item {
        padding: 1rem;
        border-bottom: 1px solid #e3e6f0;
        transition: background-color 0.2s ease;
    }

    .transaction-item:hover {
        background-color: #f8f9fc;
    }

    .transaction-item:last-child {
        border-bottom: none;
    }

    .income-badge {
        background-color: #1cc88a;
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 0.35rem;
        font-size: 0.875rem;
    }

    .expense-badge {
        background-color: #e74a3b;
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 0.35rem;
        font-size: 0.875rem;
    }

    .profit-positive {
        color: #1cc88a;
        font-weight: bold;
    }

    .profit-negative {
        color: #e74a3b;
        font-weight: bold;
    }

    @media (max-width: 768px) {
        .chart-container {
            height: 300px;
        }
    }
</style>
@endpush

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Profit Analysis</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportProfitAnalysis()">
                <i class="fas fa-download me-1"></i>Export
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="printProfitAnalysis()">
                <i class="fas fa-print me-1"></i>Print
            </button>
        </div>
        <button type="button" class="btn btn-sm btn-primary" onclick="refreshData()">
            <i class="fas fa-sync-alt me-1"></i>Refresh
        </button>
    </div>
</div>

<!-- Summary Overview Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2 stat-card">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Income</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{ number_format($totalIncome, 2) }}</div>
                        <small class="text-muted">All time</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2 stat-card">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Total Expenses</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{ number_format($totalExpenses, 2) }}</div>
                        <small class="text-muted">All time</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-receipt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-{{ $totalProfit >= 0 ? 'success' : 'danger' }} shadow h-100 py-2 stat-card">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-{{ $totalProfit >= 0 ? 'success' : 'danger' }} text-uppercase mb-1">
                            Net Profit</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{ number_format($totalProfit, 2) }}</div>
                        <small class="text-muted">All time</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2 stat-card">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Profit Margin</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($profitMargin, 1) }}%</div>
                        <small class="text-muted">Overall</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-percentage fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Period-wise Breakdown -->
<div class="row mb-4">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Daily Summary</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-sm">Income</span>
                        <span class="text-success fw-bold">Rs. {{ number_format($dailyIncome, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-sm">Expenses</span>
                        <span class="text-danger fw-bold">Rs. {{ number_format($dailyExpenses, 2) }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Net Profit</span>
                        <span class="{{ $dailyProfit >= 0 ? 'profit-positive' : 'profit-negative' }}">
                            Rs. {{ number_format($dailyProfit, 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Monthly Summary</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-sm">Income</span>
                        <span class="text-success fw-bold">Rs. {{ number_format($monthlyIncome, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-sm">Expenses</span>
                        <span class="text-danger fw-bold">Rs. {{ number_format($monthlyExpenses, 2) }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Net Profit</span>
                        <span class="{{ $monthlyProfit >= 0 ? 'profit-positive' : 'profit-negative' }}">
                            Rs. {{ number_format($monthlyProfit, 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Yearly Summary</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-sm">Income</span>
                        <span class="text-success fw-bold">Rs. {{ number_format($yearlyIncome, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-sm">Expenses</span>
                        <span class="text-danger fw-bold">Rs. {{ number_format($yearlyExpenses, 2) }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Net Profit</span>
                        <span class="{{ $yearlyProfit >= 0 ? 'profit-positive' : 'profit-negative' }}">
                            Rs. {{ number_format($yearlyProfit, 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <div class="row g-3 align-items-end">
                    <!-- Period Filter -->
                    <div class="col-lg-4 col-md-6">
                        <label for="periodFilter" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-calendar-alt text-primary me-2"></i>Analysis Period
                        </label>
                        <select class="form-select form-select-lg border-2" id="periodFilter">
                            <option value="month">This Month</option>
                            <option value="year" selected>This Year</option>
                            <option value="all">All Time</option>
                        </select>
                    </div>

                    <!-- Chart Type -->
                    <div class="col-lg-4 col-md-6">
                        <label for="chartType" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-chart-bar text-info me-2"></i>Chart Type
                        </label>
                        <select class="form-select form-select-lg border-2" id="chartType">
                            <option value="line">Line Chart</option>
                            <option value="bar">Bar Chart</option>
                            <option value="area">Area Chart</option>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div class="col-lg-4 col-md-6">
                        <label for="yearFilter" class="form-label fw-semibold text-secondary mb-2">
                            <i class="fas fa-calendar text-success me-2"></i>Year
                        </label>
                        <select class="form-select form-select-lg border-2" id="yearFilter">
                            @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row mb-4">
    <!-- Monthly Trend Chart -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Income vs Expenses Trend</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow">
                        <a class="dropdown-item" href="#" onclick="exportChart()">Export Chart</a>
                        <a class="dropdown-item" href="#" onclick="refreshChart()">Refresh Data</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Breakdown -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Category Breakdown</h6>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Recent Transactions</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink2" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow">
                        <a class="dropdown-item" href="#" onclick="filterTransactions('all')">All Transactions</a>
                        <a class="dropdown-item" href="#" onclick="filterTransactions('income')">Income Only</a>
                        <a class="dropdown-item" href="#" onclick="filterTransactions('expense')">Expenses Only</a>
                    </div>
                </div>
            </div>
            <div class="card-body" id="transactionsContainer">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let trendChart, categoryChart;
    let transactions = [];

    document.addEventListener('DOMContentLoaded', function() {
        initializeCharts();
        loadTransactions();

        // Event listeners
        document.getElementById('periodFilter').addEventListener('change', updateCategoryChart);
        document.getElementById('chartType').addEventListener('change', updateTrendChart);
        document.getElementById('yearFilter').addEventListener('change', updateTrendChart);
    });

    function initializeCharts() {
        // Initialize Trend Chart
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        trendChart = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Income',
                    data: [],
                    borderColor: '#1cc88a',
                    backgroundColor: 'rgba(28, 200, 138, 0.1)',
                    borderWidth: 2,
                    fill: true
                }, {
                    label: 'Expenses',
                    data: [],
                    borderColor: '#e74a3b',
                    backgroundColor: 'rgba(231, 74, 59, 0.1)',
                    borderWidth: 2,
                    fill: true
                }, {
                    label: 'Profit',
                    data: [],
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    borderWidth: 2,
                    fill: true
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rs. ' + context.parsed.y.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rs. ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Initialize Category Chart
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        categoryChart = new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: [
                        '#4e73df',
                        '#1cc88a',
                        '#36b9cc',
                        '#f6c23e',
                        '#e74a3b',
                        '#858796',
                        '#5a5c69'
                    ]
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': Rs. ' + context.parsed.toFixed(2);
                            }
                        }
                    }
                }
            }
        });

        // Load initial data
        updateTrendChart();
        updateCategoryChart();
    }

    function updateTrendChart() {
        const year = document.getElementById('yearFilter').value;
        const chartType = document.getElementById('chartType').value;

        fetch(`/api/profit-analysis/monthly-data?year=${year}`)
            .then(response => response.json())
            .then(data => {
                trendChart.data.labels = data.map(d => d.month);
                trendChart.data.datasets[0].data = data.map(d => d.income);
                trendChart.data.datasets[1].data = data.map(d => d.expense);
                trendChart.data.datasets[2].data = data.map(d => d.profit);

                // Update chart type
                trendChart.config.type = chartType;
                trendChart.update();
            })
            .catch(error => {
                console.error('Error loading chart data:', error);
            });
    }

    function updateCategoryChart() {
        const period = document.getElementById('periodFilter').value;

        fetch(`/api/profit-analysis/category-breakdown?period=${period}`)
            .then(response => response.json())
            .then(data => {
                const expenses = data.expenses || [];
                categoryChart.data.labels = expenses.map(e => e.category);
                categoryChart.data.datasets[0].data = expenses.map(e => e.total);
                categoryChart.update();
            })
            .catch(error => {
                console.error('Error loading category data:', error);
            });
    }

    function loadTransactions() {
        fetch('/api/profit-analysis/transaction-history?type=all')
            .then(response => response.json())
            .then(data => {
                transactions = data;
                displayTransactions(data.slice(0, 20)); // Show first 20
            })
            .catch(error => {
                console.error('Error loading transactions:', error);
                document.getElementById('transactionsContainer').innerHTML = `
                    <div class="text-center text-danger">Error loading transactions</div>
                `;
            });
    }

    function displayTransactions(transactionList) {
        const container = document.getElementById('transactionsContainer');

        if (transactionList.length === 0) {
            container.innerHTML = `<div class="text-center text-muted">No transactions found</div>`;
            return;
        }

        container.innerHTML = transactionList.map(t => `
            <div class="transaction-item">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="${t.type}-badge">${t.type.toUpperCase()}</span>
                        <span class="ms-2 fw-bold">${t.category}</span>
                        <br>
                        <small class="text-muted">${t.description}</small>
                        <br>
                        <small class="text-muted">${formatDate(t.date)}</small>
                    </div>
                    <div class="text-end">
                        <div class="${t.type === 'income' ? 'text-success' : 'text-danger'} fw-bold">
                            Rs. ${parseFloat(t.amount).toFixed(2)}
                        </div>
                        <small class="text-muted">${t.customer || t.vendor || '-'}</small>
                    </div>
                </div>
            </div>
        `).join('');
    }

    function filterTransactions(type) {
        if (type === 'all') {
            displayTransactions(transactions.slice(0, 20));
        } else {
            const filtered = transactions.filter(t => t.type === type).slice(0, 20);
            displayTransactions(filtered);
        }
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    }

    function refreshData() {
        updateTrendChart();
        updateCategoryChart();
        loadTransactions();
    }

    function exportProfitAnalysis() {
        const params = new URLSearchParams();
        const period = document.getElementById('periodFilter').value;
        const year = document.getElementById('yearFilter').value;

        if (period) params.set('period', period);
        if (year) params.set('year', year);

        window.location.href = '/profit-analysis/export?' + params.toString();
    }

    function printProfitAnalysis() {
        const params = new URLSearchParams();
        const period = document.getElementById('periodFilter').value;
        const year = document.getElementById('yearFilter').value;

        if (period) params.set('period', period);
        if (year) params.set('year', year);

        window.open('/profit-analysis/print?' + params.toString(), '_blank');
    }

    function exportChart() {
        alert('Export chart functionality');
    }

    function refreshChart() {
        updateTrendChart();
    }
</script>
@endpush
