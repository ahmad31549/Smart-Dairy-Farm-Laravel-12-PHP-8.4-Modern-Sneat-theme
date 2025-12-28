<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employee Report - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #000; margin: 20px; }
        h1 { font-size: 18px; margin: 0 0 15px; text-align: center; color: #333; }
        .header-info { margin-bottom: 20px; text-align: right; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px 10px; text-align: left; vertical-align: top; }
        th { background: #f8f9fa; font-weight: bold; color: #333; text-transform: uppercase; font-size: 10px; }
        tr:nth-child(even) { background-color: #fcfcfc; }
        .status-active { color: #1cc88a; font-weight: bold; }
        .status-inactive { color: #f6c23e; font-weight: bold; }
        .status-terminated { color: #e74a3b; font-weight: bold; }
        @media print { 
            @page { margin: 10mm; size: landscape; }
            body { margin: 0; }
            table { border: 1px solid #000; }
            th, td { border: 1px solid #000; }
        }
    </style>
    <script>
        window.addEventListener('load', function() { 
            if (!window.location.search.includes('pdf')) {
                window.print(); 
            }
        });
    </script>
</head>
<body>
    <h1>Employee List Report</h1>
    <div class="header-info">
        Report Generated: {{ now()->format('d M Y, h:i A') }}
    </div>
    <table>
        <thead>
            <tr>
                <th>Employee ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Position</th>
                <th>Department</th>
                <th>Hire Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        @foreach($employees as $employee)
            <tr>
                <td><strong>{{ $employee->employee_id }}</strong></td>
                <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
                <td>{{ $employee->email }}</td>
                <td>{{ $employee->phone ?: '-' }}</td>
                <td>{{ ucwords(str_replace('-', ' ', $employee->position)) }}</td>
                <td>{{ ucfirst($employee->department) }}</td>
                <td>{{ $employee->hire_date ? \Carbon\Carbon::parse($employee->hire_date)->format('d M Y') : '-' }}</td>
                <td class="status-{{ $employee->status }}">{{ ucfirst($employee->status) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
