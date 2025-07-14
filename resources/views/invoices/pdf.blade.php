<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
        }

        .header {
            margin-bottom: 40px;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
        }

        .invoice-title {
            font-size: 18px;
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        th,
        td {
            border: 1px solid #aaa;
            padding: 8px;
            text-align: left;
        }

        .total-row {
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .no-border {
            border: none;
        }

        .footer {
            position: fixed;
            bottom: 10px;
            text-align: center;
            font-size: 12px;
        }
    </style>
</head>

<body>

    <div class="header">
        <img src="{{ public_path('logo.png') }}" alt="Company Logo" style="width: 100px;">
        <div class="company-name">Your Company Name</div>
        <div>GST No: 1234ABCDE1234F1Z5</div>
        <div>Email: contact@yourcompany.com</div>
        <div>Phone: +91-9876543210</div>
    </div>

    <div class="invoice-title">Invoice</div>

    <table>
        <tr>
            <td class="no-border">
                <strong>Bill To:</strong><br>
                {{ $invoice->client->name }}<br>
                {{ $invoice->client->email ?? '' }}<br>
                {{ $invoice->client->address ?? '' }}
            </td>
            <td class="no-border text-right">
                <strong>Invoice #:</strong> {{ $invoice->invoice_number }}<br>
                <strong>Invoice Date:</strong> {{ $invoice->invoice_date }}<br>
                <strong>Due Date:</strong> {{ $invoice->due_date }}
            </td>
        </tr>
    </table>

    @if ($invoice->status === 'paid')
        <h2 style="color: green; text-align: right;">PAID</h2>
    @elseif ($invoice->status === 'overdue')
        <h2 style="color: red; text-align: right;">OVERDUE</h2>
    @endif


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

    <div class="footer">
        This is a system-generated invoice. No signature required.
    </div>

</body>

</html>
