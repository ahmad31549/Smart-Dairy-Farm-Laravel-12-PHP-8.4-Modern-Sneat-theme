<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vaccination History - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #000; margin: 20px; }
        h1 { font-size: 18px; margin: 0 0 15px; text-align: center; color: #333; }
        .header-info { margin-bottom: 20px; text-align: right; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px 10px; text-align: left; vertical-align: top; }
        th { background: #f8f9fa; font-weight: bold; color: #333; text-transform: uppercase; font-size: 10px; }
        tr:nth-child(even) { background-color: #fcfcfc; }
        .status-badge { padding: 3px 6px; border-radius: 3px; font-weight: bold; color: #fff; display: inline-block; }
        .status-completed { background: #1cc88a; }
        .status-pending { background: #f6c23e; }
        .status-overdue { background: #e74a3b; }
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
    <h1>Vaccination History Records</h1>
    <div class="header-info">
        Report Generated: {{ now()->format('d M Y, h:i A') }}
    </div>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Animal ID</th>
                <th>Name/Tag</th>
                <th>Vaccine Name</th>
                <th>Batch #</th>
                <th>Status</th>
                <th>Next Due</th>
                <th>Veterinarian</th>
            </tr>
        </thead>
        <tbody>
        @foreach($vaccinations as $vaccination)
            @php
                $animal = $vaccination->animal;
                $statusClass = 'status-' . strtolower($vaccination->status);
            @endphp
            <tr>
                <td>{{ $vaccination->date_administered ? \Carbon\Carbon::parse($vaccination->date_administered)->format('d M Y') : 'N/A' }}</td>
                <td><strong>{{ $animal?->animal_id }}</strong></td>
                <td>{{ $animal?->name }}<br><small style="color: #666;">{{ $animal?->tag_number }}</small></td>
                <td>{{ $vaccination->vaccine_name }}</td>
                <td>{{ $vaccination->batch_number ?: '-' }}</td>
                <td>
                    <span class="status-badge {{ $statusClass }}">
                        {{ ucfirst($vaccination->status) }}
                    </span>
                </td>
                <td>{{ $vaccination->next_due_date ? \Carbon\Carbon::parse($vaccination->next_due_date)->format('d M Y') : 'N/A' }}</td>
                <td>{{ $vaccination->veterinarian ?: '-' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
