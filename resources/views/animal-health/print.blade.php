<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Animal Health - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #000; }
        h1 { font-size: 18px; margin: 0 0 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 6px 8px; text-align: left; }
        th { background: #f0f0f0; }
        @media print { @page { margin: 12mm; } }
    </style>
    <script>
        window.addEventListener('load', function() { window.print(); });
    </script>
</head>
<body>
    <h1>Animal Health Records</h1>
    <table>
        <thead>
            <tr>
                <th>Animal ID</th>
                <th>Name</th>
                <th>Tag</th>
                <th>Breed</th>
                <th>Age</th>
                <th>Status</th>
                <th>Check Date</th>
                <th>Next Check</th>
                <th>Veterinarian</th>
                <th>Temp</th>
            </tr>
        </thead>
        <tbody>
        @foreach($records as $record)
            @php
                $animal = $record->animal;
                $age = ($animal && $animal->birth_date) ? \Carbon\Carbon::now()->diffInYears($animal->birth_date) : '';
            @endphp
            <tr>
                <td>{{ $animal?->animal_id }}</td>
                <td>{{ $animal?->name }}</td>
                <td>{{ $animal?->tag_number }}</td>
                <td>{{ $animal?->breed }}</td>
                <td>{{ $age }}</td>
                <td>{{ ucfirst($record->health_status) }}</td>
                <td>{{ \Carbon\Carbon::parse($record->check_date)->toDateString() }}</td>
                <td>{{ $record->next_check_date ? \Carbon\Carbon::parse($record->next_check_date)->toDateString() : '' }}</td>
                <td>{{ $record->veterinarian }}</td>
                <td>{{ $record->temperature }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>


