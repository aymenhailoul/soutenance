<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Facture #{{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 14px;
            color: #333;
            line-height: 1.5;
        }
        .header {
            width: 100%;
            margin-bottom: 40px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 20px;
        }
        .company-info {
            float: left;
        }
        .invoice-info {
            float: right;
            text-align: right;
        }
        .client-info {
            margin-top: 30px;
            margin-bottom: 30px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .table th {
            background-color: #f4f4f4;
            padding: 10px;
            text-align: left;
            border-bottom: 2px solid #ddd;
        }
        .table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-section {
            width: 300px;
            float: right;
        }
        .total-row {
            padding: 5px 0;
            display: flex;
            justify-content: space-between;
        }
        .grand-total {
            font-size: 18px;
            font-weight: bold;
            border-top: 2px solid #333;
            padding-top: 10px;
            margin-top: 10px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>
    <div class="header clearfix">
        <div class="company-info">
            <h1 style="margin: 0; color: #333;">Business Manager</h1>
            <p>123 Business Street<br>Casablanca, Morocco<br>+212 600 000 000</p>
        </div>
        <div class="invoice-info">
            <h2 style="margin: 0; color: #555;">FACTURE</h2>
            <p><strong>N°:</strong> {{ $invoice->invoice_number }}<br>
            <strong>Date:</strong> {{ $invoice->invoice_date->format('d/m/Y') }}<br>
            <strong>Statut:</strong> {{ $invoice->status }}</p>
        </div>
    </div>

    <div class="client-info">
        <h3 style="margin-top: 0; margin-bottom: 10px;">Facturé à:</h3>
        <strong>{{ $invoice->client->name }} {{ $invoice->client->prenom }}</strong><br>
        @if($invoice->client->phone) Tél: {{ $invoice->client->phone }}<br> @endif
        @if($invoice->client->car_brand) Voiture: {{ $invoice->client->car_brand }} @endif
        @if($invoice->client->matricule) ({{ $invoice->client->matricule }}) @endif
    </div>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 40%">Produit / Service</th>
                <th class="text-center">Qté</th>
                <th class="text-right">Prix Unit.</th>
                <th class="text-right">Remise</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>
                    <b>{{ $item->product->name }}</b>
                    <br><small style="color: #777;">{{ $item->product->type }}</small>
                </td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->unit_price, 2) }} DH</td>
                <td class="text-right">{{ number_format($item->discount, 2) }} DH</td>
                <td class="text-right">
                    {{ number_format(($item->quantity * $item->unit_price) - $item->discount, 2) }} DH
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="clearfix">
        <div class="total-section">
            <table style="width: 100%">
                <tr>
                    <td><strong>Sous-total:</strong></td>
                    <td class="text-right">
                        {{ number_format($invoice->items->sum(function($item) { return $item->quantity * $item->unit_price; }), 2) }} DH
                    </td>
                </tr>
                <tr>
                    <td><strong>Remise Total:</strong></td>
                    <td class="text-right" style="color: red">
                        - {{ number_format($invoice->items->sum('discount'), 2) }} DH
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><div style="border-bottom: 2px solid #eee; margin: 10px 0;"></div></td>
                </tr>
                <tr>
                    <td><strong style="font-size: 16px;">Total à Payer:</strong></td>
                    <td class="text-right">
                        <strong style="font-size: 16px;">{{ number_format($invoice->total_amount, 2) }} DH</strong>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="footer">
        <p>Merci pour votre confiance !</p>
    </div>
</body>
</html>
