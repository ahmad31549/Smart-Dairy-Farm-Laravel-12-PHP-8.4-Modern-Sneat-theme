<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Emergency Alerts Report - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #000; margin: 20px; }
        h1 { font-size: 18px; margin: 0 0 15px; text-align: center; color: #333; }
        .header-info { margin-bottom: 20px; text-align: right; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px 10px; text-align: left; vertical-align: top; }
        th { background: #f8f9fa; font-weight: bold; color: #333; text-transform: uppercase; font-size: 10px; }
        tr:nth-child(even) { background-color: #fcfcfc; }
        
        .status-badge { padding: 3px 6px; border-radius: 3px; font-weight: bold; color: #fff; font-size: 9px; display: inline-block; }
        .status-pending { background: #f6c23e; }
        .status-advised { background: #1cc88a; }
        .status-resolved { background: #858796; }
        .status-under-treatment { background: #36b9cc; }
        
        .advice-box { background: #f0fff4; border-left: 3px solid #1cc88a; padding: 5px; font-style: italic; font-size: 10px; }
        .treatment-box { background: #ebf8ff; border-left: 3px solid #4e73df; padding: 5px; white-space: pre-wrap; margin-top: 5px; font-size: 9px; }

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
    <h1>Emergency Records & Consultation History</h1>
    <div class="header-info">
        Report Generated: {{ now()->format('d M Y, h:i A') }}
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 15%">Date/Time</th>
                <th style="width: 10%">Animal</th>
                <th style="width: 25%">Issue / Situation</th>
                <th style="width: 20%">Doctor's Advice</th>
                <th style="width: 20%">Treatment History</th>
                <th style="width: 10%">Status</th>
            </tr>
        </thead>
        <tbody>
        @foreach($alerts as $alert)
            <tr>
                <td>
                    <strong>{{ $alert->created_at->format('d M Y') }}</strong><br>
                    {{ $alert->created_at->format('h:i A') }}<br>
                    <small>Reported by: {{ $alert->user?->name }}</small>
                </td>
                <td>
                    <strong>{{ $alert->animal?->animal_id ?: 'N/A' }}</strong><br>
                    {{ $alert->animal?->tag_number }}
                </td>
                <td>
                    {{ $alert->message }}
                    @if($alert->temperature)
                        <br><strong>Temp: {{ $alert->temperature }}°F</strong>
                    @endif
                </td>
                <td>
                    @if($alert->doctor_advice)
                        <div class="advice-box">
                            {{ $alert->doctor_advice }}
                        </div>
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if($alert->treatment_notes)
                        <div class="treatment-box">{{ $alert->treatment_notes }}</div>
                    @else
                        -
                    @endif
                </td>
                <td>
                    <span class="status-badge status-{{ str_replace('_', '-', $alert->status) }}">
                        {{ ucfirst(str_replace('_', ' ', $alert->status)) }}
                    </span>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
