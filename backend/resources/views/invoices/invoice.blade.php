<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Fatura {{ $invoice->id }}</title>
    <style>
        @page {
            margin: 3cm 2cm;
            font-family: 'Roboto', sans-serif;
        }

        p {
            font-size: 14px;
            line-height: 1.5;
            margin: 4px 0;
        }

        h1 {
            text-align: center;
            color: #B1388D;
            margin-bottom: 30px;
            font-size: 22px;
        }

        h2 {
            padding-top: 20px;
            color: #B1388D;
            font-size: 16px;
        }

        .icons {
            width: 12px;
            height: 12px;
            margin-left: 12px;
            margin-right: 5px;
        }

        .header {
            position: fixed;
            top: -2cm;
            left: 0cm;
            right: 0cm;
            height: 2cm;
            text-align: right;
        }

        .logo {
            width: 25%;
        }

        .footer {
            position: fixed;
            bottom: -2.5cm;
            left: 0cm;
            right: 0cm;
            height: 2cm;
            text-align: center;
            border-top: 1px solid #B1388D;
            line-height: 1.6;
            font-size: 11px;
        }

        .footer .label {
            margin-right: 4px;
            margin-left: 10px;
        }

        .label {
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .services thead {
            background-color: #B1388D;
            color: white;
        }

        .services th {
            border: 1px solid #B1388D;
            font-size: 14px;
        }

        .services td {
            border: 1px solid gray;
            padding: 2px 20px;
            font-size: 14px;
        }

        .totals {
            margin-top: 20px;
        }

        .totals td {
            font-size: 14px;
            padding: 3px 10px;
            text-align: right;
        }

        .totals .amount {
            width: 4cm;
        }

        .totals .balance td {
            font-weight: 700;
            color: #B1388D;
        }

        .totals .balance .amount {
            background-color: #B1388D;
            color: white;
            border-radius: 16px;
        }

        .pix {
            margin-top: 30px;
            border: 1px solid #B1388D;
            border-radius: 16px;
        }

        .pix table {
            table-layout: fixed;
        }

        .pix td {
            vertical-align: top;
            padding: 12px;
        }

        .pix .qr {
            width: 4.8cm;
        }

        .pix .details {
            width: 11.2cm;
        }

        .pix .qr img {
            width: 4.6cm;
            height: 4.6cm;
        }

        .pix-title {
            color: #B1388D;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .pix-code {
            font-family: monospace;
            font-size: 9px;
            word-wrap: break-word;
            word-break: break-all;
            background-color: #f3f3f3;
            padding: 6px;
            border-radius: 6px;
        }

        .muted {
            font-size: 11px;
            color: #666;
        }
    </style>
</head>

<body>
    <header class="header">
        @if ($logo)
            <img class="logo" src="{{ $logo }}" alt="Logo da empresa" />
        @endif
    </header>
    <footer class="footer">
        <span class="label">{{ $account->name }}</span>
        <br>
        @if ($account->cnpj)
            <span class="label">CNPJ</span>{{ $account->cnpj }}
        @endif
        @if ($account->inscricao_municipal)
            <span class="label">Insc. Municipal</span>{{ $account->inscricao_municipal }}
        @endif
        @if ($account->phone && $whatsappIcon)
            <img class="icons" src="{{ $whatsappIcon }}" alt="whatsapp-icon">{{ $account->phone }}
        @endif
        @if ($account->email && $emailIcon)
            <img class="icons" src="{{ $emailIcon }}" alt="email-icon">{{ $account->email }}
        @endif
        @if ($account->address)
            <br>
            {{ $account->address }}
        @endif
    </footer>

    <div>
        <h1>Fatura</h1>

        <p><span class="label">Núm. Fatura:</span> {{ $invoice->id }}</p>
        @if ($customerName)
            <p><span class="label">Para:</span> {{ $customerName }}</p>
        @endif
        @if ($invoice->name)
            <p><span class="label">Referente a:</span> {{ $invoice->name }}</p>
        @endif
        @if ($invoice->proposal)
            <p><span class="label">Proposta:</span> {{ $invoice->proposal->id }}</p>
        @endif
        @if ($invoice->installment_quantity > 1)
            <p><span class="label">Parcela:</span> {{ $invoice->installment_number }} de {{ $invoice->installment_quantity }}</p>
        @endif
        @if ($invoice->fiscal_invoice_number)
            <p><span class="label">Nota fiscal:</span> {{ $invoice->fiscal_invoice_number }}</p>
        @endif
        <p><span class="label">Vencimento:</span> {{ $dateDue }}</p>

        @if ($invoice->proposal && $invoice->proposal->proposalServices->isNotEmpty())
            <h2>SERVIÇOS DA PROPOSTA:</h2>
            <table class="services">
                <thead>
                    <tr>
                        <th>Nome</th>
                        @if ($isVisibleQuantity)
                            <th>Qtde</th>
                        @endif
                        <th>Preço</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoice->proposal->proposalServices as $proposalItem)
                        <tr>
                            <td>{{ $proposalItem->name }}</td>
                            @if ($isVisibleQuantity)
                                <td style="text-align: center">{{ $proposalItem->quantity }}</td>
                                <td style="text-align: right">R$ {{ number_format($proposalItem->price, 2, ',', '.') }}</td>
                            @else
                                <td style="text-align: right">R$ {{ number_format($proposalItem->total_price, 2, ',', '.') }}</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @php
            $totalPaid = (float) $invoice->total_paid;
            $balance = (float) $invoice->price - $totalPaid;
        @endphp
        <table class="totals">
            <tr>
                <td>Valor da fatura:</td>
                <td class="amount">R$ {{ number_format($invoice->price, 2, ',', '.') }}</td>
            </tr>
            @if ($totalPaid > 0)
                <tr>
                    <td>Já pago:</td>
                    <td class="amount">R$ {{ number_format($totalPaid, 2, ',', '.') }}</td>
                </tr>
            @endif
            <tr class="balance">
                <td>Total a pagar:</td>
                <td class="amount">R$ {{ number_format(max($balance, 0), 2, ',', '.') }}</td>
            </tr>
        </table>

        @if ($pix)
            <div class="pix">
                <table>
                    <tr>
                        <td class="qr">
                            <img src="{{ $pix['qr_code'] }}" alt="QR Code Pix">
                        </td>
                        <td class="details">
                            <div class="pix-title">Pague com Pix</div>
                            <p>Abra o app do seu banco, escolha <b>Pix &gt; Ler QR Code</b> e aponte para o código ao lado.</p>
                            <p><span class="label">Valor:</span> R$ {{ number_format($pix['amount'], 2, ',', '.') }}</p>
                            <p><span class="label">Chave Pix:</span> {{ $pix['pix_key'] }}</p>
                            <p class="muted">Ou use o Pix copia e cola:</p>
                            <div class="pix-code">{{ $pix['payload'] }}</div>
                        </td>
                    </tr>
                </table>
            </div>
        @endif

        @if ($account->address_city)
            <p style="text-align:center;padding-top:40px">
                {{ $account->address_city }}, {{ $today }}
            </p>
        @endif
    </div>
</body>

</html>
