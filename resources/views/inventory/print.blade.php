<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inventory Report - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #000; margin: 20px; }
        h1 { font-size: 18px; margin: 0 0 15px; text-align: center; color: #333; }
        .header-info { margin-bottom: 20px; text-align: right; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px 10px; text-align: left; vertical-align: top; }
        th { background: #f8f9fa; font-weight: bold; color: #333; text-transform: uppercase; font-size: 10px; }
        tr:nth-child(even) { background-color: #fcfcfc; }
        .status-low { color: #e74a3b; font-weight: bold; }
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
    <h1>Farm Inventory Report</h1>
    <div class="header-info">
        Report Generated: {{ now()->format('d M Y, h:i A') }}
    </div>
    <table>
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Category</th>
                <th>Batch #</th>
                <th>Quantity</th>
                <th>Unit</th>
                <th>Unit Price</th>
                <th>Total Value</th>
                <th>Supplier</th>
                <th>Expiry Date</th>
            </tr>
        </thead>
        <tbody>
        @php $grandTotal = 0; @endphp
        @foreach($items as $item)
            @php 
                $totalValue = $item->quantity * $item->unit_price;
                $grandTotal += $totalValue;
                $isLowStock = $item->quantity <= $item->reorder_level;
            @endphp
            <tr>
                <td><strong>{{ $item->item_name }}</strong></td>
                <td>{{ ucfirst(str_replace('_', ' ', $item->category)) }}</td>
                <td>{{ $item->batch_number ?: '-' }}</td>
                <td class="{{ $isLowStock ? 'status-low' : '' }}">
                    {{ number_format($item->quantity, 2) }}
                    @if($isLowStock) <br><small>(Low Stock)</small> @endif
                </td>
                <td>{{ $item->unit }}</td>
                <td class="amount">${{ number_format($item->unit_price, 2) }}</td>
                <td class="amount">${{ number_format($totalValue, 2) }}</td>
                <td>{{ $item->supplier ?: '-' }}</td>
                <td>{{ $item->expiry_date ? \Carbon\Carbon::parse($item->expiry_date)->format('d M Y') : '-' }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background: #eee;">
                <td colspan="6" style="text-align: right;">Grand Total Value:</td>
                <td class="amount">${{ number_format($grandTotal, 2) }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
