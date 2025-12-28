<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Expense Report - Print</title>
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
    <h1>Expense Report</h1>
    <div class="header-info">
        Report Generated: {{ now()->format('d M Y, h:i A') }}
    </div>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Category</th>
                <th>Description</th>
                <th>Vendor</th>
                <th>Ref #</th>
                <th>Method</th>
                <th class="amount">Amount</th>
            </tr>
        </thead>
        <tbody>
        @php $total = 0; @endphp
        @foreach($expenses as $expense)
            @php $total += $expense->amount; @endphp
            <tr>
                <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $expense->category)) }}</td>
                <td>{{ $expense->description }}</td>
                <td>{{ $expense->vendor ?: '-' }}</td>
                <td>{{ $expense->receipt_number ?: '-' }}</td>
                <td>{{ ucfirst($expense->payment_method) }}</td>
                <td class="amount">${{ number_format($expense->amount, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="6" style="text-align: right;">Total Expenses:</td>
                <td class="amount">${{ number_format($total, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
