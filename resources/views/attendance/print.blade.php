<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Attendance Report - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #000; margin: 20px; }
        h1 { font-size: 18px; margin: 0 0 15px; text-align: center; color: #333; }
        .header-info { margin-bottom: 20px; text-align: right; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px 10px; text-align: left; vertical-align: top; }
        th { background: #f8f9fa; font-weight: bold; color: #333; text-transform: uppercase; font-size: 10px; }
        tr:nth-child(even) { background-color: #fcfcfc; }
        .status-badge { padding: 3px 6px; border-radius: 3px; font-weight: bold; color: #fff; display: inline-block; }
        .status-present { background: #1cc88a; }
        .status-absent { background: #e74a3b; }
        .status-late { background: #f6c23e; }
        .status-leave { background: #4e73df; }
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
    <h1>Staff Attendance Report</h1>
    <div class="header-info">
        Report Generated: {{ now()->format('d M Y, h:i A') }}
    </div>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Employee ID</th>
                <th>Name</th>
                <th>Department</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Status</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
        @foreach($attendance as $record)
            @php
                $employee = $record->employee;
                $statusClass = 'status-' . strtolower($record->status);
            @endphp
            <tr>
                <td>{{ $record->attendance_date ? $record->attendance_date->format('d M Y') : 'N/A' }}</td>
                <td><strong>{{ $employee?->employee_id }}</strong></td>
                <td>{{ $employee?->name }}</td>
                <td>{{ ucfirst($employee?->department) }}</td>
                <td>{{ $record->check_in ? $record->check_in->format('H:i') : '-' }}</td>
                <td>{{ $record->check_out ? $record->check_out->format('H:i') : '-' }}</td>
                <td>
                    <span class="status-badge {{ $statusClass }}">
                        {{ ucfirst($record->status) }}
                    </span>
                </td>
                <td>{{ $record->notes ?: '-' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
