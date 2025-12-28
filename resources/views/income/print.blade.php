<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Income Report - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #000; margin: 20px; }
        h1 { font-size: 18px; margin: 0 0 15px; text-align: center; color: #333; }
        .header-info { margin-bottom: 20px; text-align: right; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px 10px; text-align: left; vertical-align: top; }
        th { background: #f8f9fa; font-weight: bold; color: #333; text-transform: uppercase; font-size: 10px; }
        tr:nth-child(even) { background-color: #fcfcfc; }
        .total-row { font-weight: bold; background: #eee !important; }
        .amount { text-align: right; }
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
    <h1>Income Report</h1>
    <div class="header-info">
        Report Generated: {{ now()->format('d M Y, h:i A') }}
    </div>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Source</th>
                <th>Description</th>
                <th>Customer</th>
                <th>Qty/Unit</th>
                <th class="amount">Amount</th>
            </tr>
        </thead>
        <tbody>
        @php $total = 0; @endphp
        @foreach($incomes as $income)
            @php $total += $income->amount; @endphp
            <tr>
                <td>{{ \Carbon\Carbon::parse($income->income_date)->format('d M Y') }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $income->source)) }}</td>
                <td>{{ $income->description }}</td>
                <td>{{ $income->customer ?: '-' }}</td>
                <td>{{ $income->quantity ? $income->quantity . ' ' . $income->unit : '-' }}</td>
                <td class="amount">${{ number_format($income->amount, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" style="text-align: right;">Total Income:</td>
                <td class="amount">${{ number_format($total, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
