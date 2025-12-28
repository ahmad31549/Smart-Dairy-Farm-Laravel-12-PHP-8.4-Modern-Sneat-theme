<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Milk Production Report - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #000; margin: 20px; }
        h1 { font-size: 18px; margin: 0 0 15px; text-align: center; color: #333; }
        .header-info { margin-bottom: 20px; text-align: right; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px 10px; text-align: left; vertical-align: top; }
        th { background: #f8f9fa; font-weight: bold; color: #333; text-transform: uppercase; font-size: 10px; }
        tr:nth-child(even) { background-color: #fcfcfc; }
        .quality-badge { padding: 3px 6px; border-radius: 3px; font-weight: bold; color: #fff; display: inline-block; }
        .grade-A { background: #1cc88a; }
        .grade-B { background: #4e73df; }
        .grade-C { background: #f6c23e; }
        .amount { text-align: right; }
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
    <h1>Milk Production Tracking Report</h1>
    <div class="header-info">
        Report Generated: {{ now()->format('d M Y, h:i A') }}
    </div>
    <table>
        <thead>
            <tr>
                <th colspan="7" style="background: #e3e6f0; text-align: center; border-bottom: 2px solid #ccc;">Daily Herd Summaries (Worker Reports)</th>
            </tr>
            <tr>
                <th>Date</th>
                <th>Total Quantity</th>
                <th>Milking Buffaloes</th>
                <th>Sick / Pregnant</th>
                <th>Herd Size</th>
                <th>Recorded By</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dailyRecords ?? [] as $dRecord)
            <tr>
                <td>{{ \Carbon\Carbon::parse($dRecord->date)->format('d M Y') }}</td>
                <td><strong>{{ number_format($dRecord->total_milk_quantity, 2) }} L</strong></td>
                <td>{{ $dRecord->total_buffaloes_milked }}</td>
                <td>
                    <span style="color:red">{{ $dRecord->sick_animals }}</span> / 
                    <span style="color:green">{{ $dRecord->pregnant_animals }}</span>
                </td>
                <td>{{ $dRecord->total_herd_size }}</td>
                <td>{{ $dRecord->recorder ? $dRecord->recorder->name : 'Unknown' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center;">No daily worker reports found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Individual Animal Records</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Animal ID</th>
                <th>Animal Name</th>
                <th class="amount">Morning (L)</th>
                <th class="amount">Evening (L)</th>
                <th class="amount">Total (L)</th>
                <th class="amount">Fat %</th>
                <th class="amount">Protein %</th>
                <th>Grade</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
        @php $totalLiters = 0; @endphp
        @foreach($records as $record)
            @php 
                $total = $record->morning_quantity + $record->evening_quantity;
                $totalLiters += $total;
            @endphp
            <tr>
                <td>{{ $record->production_date ? \Carbon\Carbon::parse($record->production_date)->format('d M Y') : 'N/A' }}</td>
                <td><strong>{{ $record->animal?->tag_number }}</strong></td>
                <td>{{ $record->animal?->name }}</td>
                <td class="amount">{{ number_format($record->morning_quantity, 2) }}</td>
                <td class="amount">{{ number_format($record->evening_quantity, 2) }}</td>
                <td class="amount"><strong>{{ number_format($total, 2) }}</strong></td>
                <td class="amount">{{ $record->fat_content ? number_format($record->fat_content, 2) . '%' : '-' }}</td>
                <td class="amount">{{ $record->protein_content ? number_format($record->protein_content, 2) . '%' : '-' }}</td>
                <td>
                    <span class="quality-badge grade-{{ $record->quality_grade }}">
                        Grade {{ $record->quality_grade }}
                    </span>
                </td>
                <td>{{ $record->notes ?: '-' }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background: #eee;">
                <td colspan="5" style="text-align: right;">Grand Total (Liters):</td>
                <td class="amount">{{ number_format($totalLiters, 2) }} L</td>
                <td colspan="4"></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
