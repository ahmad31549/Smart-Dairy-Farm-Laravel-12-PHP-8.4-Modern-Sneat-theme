<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profit Analysis Report - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #000; margin: 20px; }
        h1 { font-size: 20px; margin: 0 0 10px; text-align: center; color: #333; }
        .subtitle { text-align: center; color: #666; margin-bottom: 20px; }
        .header-info { margin-bottom: 20px; text-align: right; color: #666; }
        
        .summary-box { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .summary-item { text-align: center; padding: 15px; border: 1px solid #ddd; width: 22%; border-radius: 5px; }
        .summary-item h3 { margin: 0 0 5px; font-size: 12px; color: #666; text-transform: uppercase; }
        .summary-item .value { font-size: 16px; font-weight: bold; }
        .income-val { color: #1cc88a; }
        .expense-val { color: #e74a3b; }
        .profit-val { color: #4e73df; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px 10px; text-align: left; vertical-align: top; }
        th { background: #f8f9fa; font-weight: bold; color: #333; text-transform: uppercase; font-size: 10px; }
        tr:nth-child(even) { background-color: #fcfcfc; }
        
        .amount { text-align: right; font-family: monospace; }
        .type-badge { padding: 3px 6px; border-radius: 3px; font-weight: bold; color: #fff; font-size: 9px; }
        .income-badge { background: #1cc88a; }
        .expense-badge { background: #e74a3b; }
        
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
    <h1>Financial Profit Analysis Report</h1>
    <div class="subtitle">
        Period: {{ ucfirst($period) }} {{ $period === 'year' ? $year : '' }}
    </div>
    <div class="header-info">
        Report Generated: {{ now()->format('d M Y, h:i A') }}
    </div>

    <div class="summary-box">
        <div class="summary-item">
            <h3>Total Income</h3>
            <div class="value income-val">Rs. {{ number_format($income, 2) }}</div>
        </div>
        <div class="summary-item">
            <h3>Total Expenses</h3>
            <div class="value expense-val">Rs. {{ number_format($expenses, 2) }}</div>
        </div>
        <div class="summary-item">
            <h3>Net Profit</h3>
            <div class="value profit-val">Rs. {{ number_format($profit, 2) }}</div>
        </div>
        <div class="summary-item">
            <h3>Profit Margin</h3>
            <div class="value">{{ number_format($margin, 1) }}%</div>
        </div>
    </div>

    <h2>Transactions History</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Category/Source</th>
                <th>Description</th>
                <th>Entity (Customer/Vendor)</th>
                <th class="amount">Amount</th>
            </tr>
        </thead>
        <tbody>
        @php
            $allTransactions = collect();
            foreach($incomeTransactions as $t) {
                $allTransactions->push([
                    'date' => $t->income_date,
                    'type' => 'income',
                    'category' => $t->source,
                    'description' => $t->description,
                    'entity' => $t->customer,
                    'amount' => $t->amount
                ]);
            }
            foreach($expenseTransactions as $t) {
                $allTransactions->push([
                    'date' => $t->expense_date,
                    'type' => 'expense',
                    'category' => $t->category,
                    'description' => $t->description,
                    'entity' => $t->vendor,
                    'amount' => $t->amount
                ]);
            }
            $sortedTransactions = $allTransactions->sortByDesc('date');
        @endphp

        @foreach($sortedTransactions as $t)
            <tr>
                <td>{{ \Carbon\Carbon::parse($t['date'])->format('d M Y') }}</td>
                <td>
                    <span class="type-badge {{ $t['type'] }}-badge">
                        {{ strtoupper($t['type']) }}
                    </span>
                </td>
                <td>{{ $t['category'] }}</td>
                <td>{{ $t['description'] ?: '-' }}</td>
                <td>{{ $t['entity'] ?: '-' }}</td>
                <td class="amount {{ $t['type'] === 'income' ? 'income-val' : 'expense-val' }}">
                    {{ $t['type'] === 'income' ? '+' : '-' }} Rs. {{ number_format($t['amount'], 2) }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
