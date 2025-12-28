<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Milk Quality Analysis - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; color: #000; margin: 15px; }
        h1 { font-size: 16px; margin: 0 0 10px; text-align: center; color: #333; }
        .header-info { margin-bottom: 15px; text-align: right; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; vertical-align: top; }
        th { background: #f8f9fa; font-weight: bold; color: #333; text-transform: uppercase; font-size: 9px; }
        tr:nth-child(even) { background-color: #fcfcfc; }
        .grade-A { color: #1cc88a; font-weight: bold; }
        .result-Passed { background: #d4edda; color: #155724; }
        .result-Failed { background: #f8d7da; color: #721c24; }
        @media print { 
            @page { margin: 8mm; size: landscape; }
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
    <h1>Milk Quality Analysis Report</h1>
    <div class="header-info">
        Report Generated: {{ now()->format('d M Y, h:i A') }}
    </div>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Batch #</th>
                <th>Animal/Tag</th>
                <th>Fat %</th>
                <th>Prot %</th>
                <th>Lac %</th>
                <th>pH</th>
                <th>Temp</th>
                <th>SCC</th>
                <th>Grade</th>
                <th>Result</th>
                <th>Tested By</th>
            </tr>
        </thead>
        <tbody>
        @foreach($tests as $test)
            @php $animal = $test->animal; @endphp
            <tr>
                <td>{{ \Carbon\Carbon::parse($test->test_date)->format('d M Y') }}</td>
                <td><strong>{{ $test->batch_number }}</strong></td>
                <td>{{ $animal ? $animal->name . ' (' . $animal->tag_number . ')' : 'Batch' }}</td>
                <td>{{ number_format($test->fat_content, 2) }}</td>
                <td>{{ number_format($test->protein_content, 2) }}</td>
                <td>{{ $test->lactose_content ? number_format($test->lactose_content, 2) : '-' }}</td>
                <td>{{ number_format($test->ph_level, 2) }}</td>
                <td>{{ number_format($test->temperature, 1) }}°C</td>
                <td>{{ $test->somatic_cell_count ? number_format($test->somatic_cell_count) : '-' }}</td>
                <td class="grade-{{ $test->quality_grade }}">{{ $test->quality_grade }}</td>
                <td class="result-{{ $test->test_result }}">{{ $test->test_result }}</td>
                <td>{{ $test->tested_by ?: '-' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
