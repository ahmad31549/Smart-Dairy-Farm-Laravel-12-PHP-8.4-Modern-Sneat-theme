<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Accounts Report - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #000; margin: 20px; }
        h1 { font-size: 18px; margin: 0 0 15px; text-align: center; color: #333; }
        .header-info { margin-bottom: 20px; text-align: right; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px 10px; text-align: left; vertical-align: top; }
        th { background: #f8f9fa; font-weight: bold; color: #333; text-transform: uppercase; font-size: 10px; }
        tr:nth-child(even) { background-color: #fcfcfc; }
        .status-active { color: #1cc88a; font-weight: bold; }
        .status-pending { color: #f6c23e; font-weight: bold; }
        .status-rejected { color: #e74a3b; font-weight: bold; }
        @media print { 
            @page { margin: 10mm; size: portrait; }
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
    <h1>System User Accounts Report</h1>
    <div class="header-info">
        Report Generated: {{ now()->format('d M Y, h:i A') }}
    </div>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Farm Name</th>
                <th>Status</th>
                <th>Joined Date</th>
            </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <td><strong>{{ $user->name }}</strong></td>
                <td>{{ $user->email }}</td>
                <td>{{ ucwords($user->role) }}</td>
                <td>{{ $user->farm_name ?: 'N/A' }}</td>
                <td class="status-{{ $user->status }}">
                    {{ ucfirst($user->status) }}
                </td>
                <td>{{ $user->created_at->format('d M Y') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
