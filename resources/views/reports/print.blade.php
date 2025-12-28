<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ ucwords(str_replace('_', ' ', $reportType)) }} Report - Print</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            font-size: 11px; 
            color: #000;
            margin: 0;
            padding: 15px;
        }
        .report-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        h1 { 
            font-size: 20px; 
            margin: 0 0 8px;
            text-transform: uppercase;
        }
        .report-info {
            font-size: 10px;
            color: #666;
            margin-bottom: 5px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }
        .stat-box {
            border: 1px solid #ddd;
            padding: 8px;
            background: #f9f9f9;
        }
        .stat-label {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .stat-value {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }
        table { 
            width: 100%; 
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td { 
            border: 1px solid #333; 
            padding: 5px 6px; 
            text-align: left;
            font-size: 10px;
        }
        th { 
            background: #e0e0e0;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-info { background: #d1ecf1; color: #0c5460; }
        .category-breakdown {
            margin-top: 20px;
            page-break-inside: avoid;
        }
        .breakdown-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        .breakdown-section {
            border: 1px solid #ddd;
            padding: 10px;
        }
        .breakdown-title {
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 11px;
        }
        .breakdown-item {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            border-bottom: 1px solid #eee;
        }
        @media print { 
            @page { 
                margin: 12mm;
                size: A4 landscape;
            }
            body {
                padding: 0;
            }
        }
    </style>
    <script>
        window.addEventListener('load', function() { 
            window.print(); 
        });
    </script>
</head>
<body>
    <div class="report-header">
        <h1>{{ ucwords(str_replace('_', ' ', $reportType)) }} Report</h1>
        <div class="report-info">
            @if($startDate && $endDate)
                Period: {{ $startDate }} to {{ $endDate }}
            @else
                Period: All Time
            @endif
            | Format: {{ ucfirst($format) }} | Generated: {{ date('Y-m-d H:i:s') }}
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="summary-grid">
        @foreach($data as $key => $value)
            @if(!is_array($value) && !is_object($value) && $key !== 'records' && $key !== 'employees' && $key !== 'items' && $key !== 'income_records' && $key !== 'expense_records')
                <div class="stat-box">
                    <div class="stat-label">{{ ucwords(str_replace('_', ' ', $key)) }}</div>
                    <div class="stat-value">
                        @if(is_numeric($value) && (str_contains($key, 'amount') || str_contains($key, 'salary') || str_contains($key, 'income') || str_contains($key, 'expense') || str_contains($key, 'profit') || str_contains($key, 'value')))
                            Rs. {{ number_format($value, 2) }}
                        @elseif(is_numeric($value))
                            {{ number_format($value) }}
                        @else
                            {{ $value }}
                        @endif
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <!-- Animal Health Detailed Records -->
    @if($format === 'detailed' && isset($data['records']) && $reportType === 'animal_health')
        <h3 style="margin-top: 20px; font-size: 14px;">Detailed Health Records</h3>
        <table>
            <thead>
                <tr>
                    <th>Animal ID</th>
                    <th>Name</th>
                    <th>Breed</th>
                    <th>Status</th>
                    <th>Check Date</th>
                    <th>Symptoms</th>
                    <th>Treatment</th>
                    <th>Veterinarian</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['records'] as $record)
                    <tr>
                        <td>{{ $record['animal_id'] ?? '-' }}</td>
                        <td>{{ $record['animal_name'] ?? '-' }}</td>
                        <td>{{ $record['breed'] ?? '-' }}</td>
                        <td>
                            <span class="badge badge-{{ $record['health_status'] === 'healthy' ? 'success' : ($record['health_status'] === 'treatment' ? 'warning' : 'danger') }}">
                                {{ ucfirst($record['health_status']) }}
                            </span>
                        </td>
                        <td>{{ $record['check_date'] ?? '-' }}</td>
                        <td>{{ $record['symptoms'] ?? '-' }}</td>
                        <td>{{ $record['treatment'] ?? '-' }}</td>
                        <td>{{ $record['veterinarian'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Milk Production Detailed Records -->
    @if($format === 'detailed' && isset($data['records']) && $reportType === 'milk_production')
        <h3 style="margin-top: 20px; font-size: 14px;">Detailed Production Records</h3>
        <table>
            <thead>
                <tr>
                    <th>Animal ID</th>
                    <th>Name</th>
                    <th>Date</th>
                    <th>Session</th>
                    <th>Quantity (L)</th>
                    <th>Quality</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['records'] as $record)
                    <tr>
                        <td>{{ $record->animal_id ?? '-' }}</td>
                        <td>{{ $record->name ?? '-' }}</td>
                        <td>{{ $record->production_date ?? '-' }}</td>
                        <td>{{ ucfirst($record->session ?? '-') }}</td>
                        <td>{{ number_format($record->quantity ?? 0, 2) }}</td>
                        <td>{{ $record->quality ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Employee Detailed Records -->
    @if($format === 'detailed' && isset($data['employees']))
        <h3 style="margin-top: 20px; font-size: 14px;">Employee Details</h3>
        <table>
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Department</th>
                    <th>Salary</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['employees'] as $emp)
                    <tr>
                        <td>{{ $emp['employee_id'] ?? '-' }}</td>
                        <td>{{ $emp['name'] ?? '-' }}</td>
                        <td>{{ $emp['position'] ?? '-' }}</td>
                        <td>{{ $emp['department'] ?? '-' }}</td>
                        <td>Rs. {{ number_format($emp['salary'] ?? 0, 2) }}</td>
                        <td>
                            <span class="badge badge-{{ $emp['status'] === 'active' ? 'success' : 'warning' }}">
                                {{ ucfirst($emp['status']) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Inventory Detailed Records -->
    @if($format === 'detailed' && isset($data['items']))
        <h3 style="margin-top: 20px; font-size: 14px;">Inventory Items</h3>
        <table>
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total Value</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['items'] as $item)
                    <tr>
                        <td>{{ $item['item_name'] ?? '-' }}</td>
                        <td>{{ $item['category'] ?? '-' }}</td>
                        <td>{{ $item['quantity'] ?? 0 }} {{ $item['unit'] ?? '' }}</td>
                        <td>Rs. {{ number_format($item['unit_price'] ?? 0, 2) }}</td>
                        <td>Rs. {{ number_format($item['total_value'] ?? 0, 2) }}</td>
                        <td>
                            <span class="badge badge-{{ $item['status'] === 'In Stock' ? 'success' : 'warning' }}">
                                {{ $item['status'] ?? '-' }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Category Breakdowns -->
    @if(isset($data['income_by_source']) || isset($data['expenses_by_category']) || isset($data['by_department']) || isset($data['by_category']))
        <div class="category-breakdown">
            <h3 style="font-size: 14px; margin-bottom: 10px;">Category Breakdown</h3>
            <div class="breakdown-grid">
                @if(isset($data['income_by_source']))
                    <div class="breakdown-section">
                        <div class="breakdown-title">Income by Source</div>
                        @foreach($data['income_by_source'] as $source => $amount)
                            <div class="breakdown-item">
                                <span>{{ ucwords(str_replace('_', ' ', $source)) }}</span>
                                <strong>Rs. {{ number_format($amount, 2) }}</strong>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if(isset($data['expenses_by_category']))
                    <div class="breakdown-section">
                        <div class="breakdown-title">Expenses by Category</div>
                        @foreach($data['expenses_by_category'] as $category => $amount)
                            <div class="breakdown-item">
                                <span>{{ ucwords($category) }}</span>
                                <strong>Rs. {{ number_format($amount, 2) }}</strong>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if(isset($data['by_department']))
                    <div class="breakdown-section">
                        <div class="breakdown-title">Employees by Department</div>
                        @foreach($data['by_department'] as $dept => $count)
                            <div class="breakdown-item">
                                <span>{{ ucwords($dept) }}</span>
                                <strong>{{ $count }}</strong>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if(isset($data['by_category']))
                    <div class="breakdown-section">
                        <div class="breakdown-title">Items by Category</div>
                        @foreach($data['by_category'] as $category => $count)
                            <div class="breakdown-item">
                                <span>{{ ucwords($category) }}</span>
                                <strong>{{ $count }}</strong>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif
</body>
</html>