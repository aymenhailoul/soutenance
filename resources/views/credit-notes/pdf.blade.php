<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Avoir #{{ $creditNote->credit_note_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 14px;
            color: #333;
            line-height: 1.5;
        }
        .header {
            width: 100%;
            margin-bottom: 40px;
            border-bottom: 2px solid #c0392b;
            padding-bottom: 20px;
        }
        .company-info {
            float: left;
        }
        .invoice-info {
            float: right;
            text-align: right;
        }
        .avoir-badge {
            background-color: #c0392b;
            color: white;
            padding: 5px 15px;
            border-radius: 5px;
            display: inline-block;
            margin-bottom: 10px;
        }
        .reference-info {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
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
        .credit-total {
            color: #c0392b;
            font-weight: bold;
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
        .reason-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
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
            <div class="avoir-badge">AVOIR</div>
            <p><strong>N°:</strong> {{ $creditNote->credit_note_number }}<br>
            <strong>Date:</strong> {{ $creditNote->credit_date->format('d/m/Y') }}</p>
        </div>
    </div>

    <div class="reference-info">
        <strong>⚠️ Document de crédit référençant:</strong><br>
        Facture N° <strong>{{ $creditNote->invoice->invoice_number }}</strong> 
        du {{ $creditNote->invoice->invoice_date->format('d/m/Y') }}
    </div>

    <div class="client-info">
        <h3 style="margin-top: 0; margin-bottom: 10px;">Client:</h3>
        <strong>{{ $creditNote->client->name }} {{ $creditNote->client->prenom }}</strong><br>
        @if($creditNote->client->phone) Tél: {{ $creditNote->client->phone }}<br> @endif
        @if($creditNote->client->car_brand) Voiture: {{ $creditNote->client->car_brand }} @endif
        @if($creditNote->client->matricule) ({{ $creditNote->client->matricule }}) @endif
    </div>

    @if($creditNote->reason)
    <div class="reason-box">
        <strong>Motif du retour:</strong><br>
        {{ $creditNote->reason }}
    </div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th style="width: 40%">Produit / Service Retourné</th>
                <th class="text-center">Qté</th>
                <th class="text-right">Prix Unit.</th>
                <th class="text-right">Remise</th>
                <th class="text-right">Crédit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($creditNote->items as $item)
            <tr>
                <td>
                    <b>{{ $item->product->name }}</b>
                    <br><small style="color: #777;">{{ $item->product->type }}</small>
                </td>
                <td class="text-left">{{ $item->quantity }}</td>
                <td class="text-left">{{ number_format($item->unit_price, 2) }} DH</td>
                <td class="text-left">{{ number_format($item->discount, 2) }} DH</td>
                <td class="text-left credit-total">
                    -{{ number_format(($item->quantity * $item->unit_price) - $item->discount, 2) }} DH
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
                        {{ number_format($creditNote->items->sum(function($item) { return $item->quantity * $item->unit_price; }), 2) }} DH
                    </td>
                </tr>
                <tr>
                    <td><strong>Remise Total:</strong></td>
                    <td class="text-right" style="color: green">
                        - {{ number_format($creditNote->items->sum('discount'), 2) }} DH
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><div style="border-bottom: 2px solid #eee; margin: 10px 0;"></div></td>
                </tr>
                <tr>
                    <td><strong style="font-size: 16px;">Total Crédit:</strong></td>
                    <td class="text-right">
                        <strong style="font-size: 16px; color: #c0392b;">-{{ number_format($creditNote->total_amount, 2) }} DH</strong>
                    </td>
                </tr>
            </table>
        </div>
    </div>

</body>
</html>
