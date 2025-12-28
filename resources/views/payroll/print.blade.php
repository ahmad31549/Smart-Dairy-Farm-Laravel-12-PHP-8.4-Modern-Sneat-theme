<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payroll Report - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; color: #000; margin: 15px; }
        h1 { font-size: 16px; margin: 0 0 10px; text-align: center; color: #333; }
        .header-info { margin-bottom: 15px; text-align: right; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; vertical-align: top; }
        th { background: #f8f9fa; font-weight: bold; color: #333; text-transform: uppercase; font-size: 9px; }
        tr:nth-child(even) { background-color: #fcfcfc; }
        .amount { text-align: right; }
        .status-paid { color: #1cc88a; font-weight: bold; }
        .status-pending { color: #f6c23e; font-weight: bold; }
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
    <h1>Staff Payroll Report</h1>
    <div class="header-info">
        Report Generated: {{ now()->format('d M Y, h:i A') }}
    </div>
    <table>
        <thead>
            <tr>
                <th>Emp ID</th>
                <th>Employee Name</th>
                <th>Month</th>
                <th class="amount">Basic Salary</th>
                <th class="amount">Overtime</th>
                <th class="amount">Bonus</th>
                <th class="amount">Deductions</th>
                <th class="amount">Net Salary</th>
                <th>Status</th>
                <th>Payment Date</th>
            </tr>
        </thead>
        <tbody>
        @php $grandTotal = 0; @endphp
        @foreach($payroll as $pay)
            @php $grandTotal += $pay->net_salary; @endphp
            <tr>
                <td><strong>{{ $pay->employee?->employee_id }}</strong></td>
                <td>{{ $pay->employee?->name }}</td>
                <td>{{ \Carbon\Carbon::parse($pay->payroll_month)->format('F Y') }}</td>
                <td class="amount">Rs. {{ number_format($pay->basic_salary, 2) }}</td>
                <td class="amount">Rs. {{ number_format($pay->overtime_amount, 2) }}</td>
                <td class="amount">Rs. {{ number_format($pay->bonus, 2) }}</td>
                <td class="amount">Rs. {{ number_format($pay->deductions, 2) }}</td>
                <td class="amount"><strong>Rs. {{ number_format($pay->net_salary, 2) }}</strong></td>
                <td class="status-{{ $pay->status }}">{{ ucfirst($pay->status) }}</td>
                <td>{{ $pay->payment_date ? \Carbon\Carbon::parse($pay->payment_date)->format('d M Y') : '-' }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background: #eee;">
                <td colspan="7" style="text-align: right;">Total Net Payroll:</td>
                <td class="amount">Rs. {{ number_format($grandTotal, 2) }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
