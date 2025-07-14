<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; color: #333; }
        .header { margin-bottom: 30px; }
        .company-name { font-size: 20px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        .total-row { font-weight: bold; }
        .text-right { text-align: right; }
    </style>
</head>
<body>

    <div class="header">
        <div class="company-name">Your Company Name</div>
        <div>Email: contact@yourcompany.com</div>
        <div>GST No: 1234ABCDE1234F1Z5</div>
    </div>

    <h3>Invoice #{{ $invoice->invoice_number }}</h3>
    <p><strong>Invoice Date:</strong> {{ $invoice->invoice_date }}<br>
       <strong>Due Date:</strong> {{ $invoice->due_date }}</p>

    <p><strong>Client:</strong><br>
       {{ $invoice->client->name }}<br>
       {{ $invoice->client->email }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Description</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>₹{{ number_format($item->unit_price, 2) }}</td>
                    <td>₹{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" class="text-right">Subtotal</td>
                <td>₹{{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="4" class="text-right">Tax ({{ $gstRate }}%)</td>
                <td>₹{{ number_format($invoice->tax, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="4" class="text-right">Total</td>
                <td>₹{{ number_format($invoice->total, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <p style="margin-top: 30px;">Thank you for your business.</p>

</body>
</html>
