<!DOCTYPE html>
<html>

<head>
    <title>Proposta de prestação de serviço</title>
    <style>
        @page {
            margin: 3cm 2cm;
            font-family: 'Roboto', sans-serif;
        }

        p {
            font-size: 14px;
            line-height: 1.5;
        }

        h1 {
            text-align: center;
            color: #B1388D;
            margin-bottom: 60px;
            font-size: 22px;
        }

        h2 {
            padding-top: 60px;
            color: #B1388D;
            font-size: 16px;
        }

        .icons {
            width: 12px;
            height: 12px;
            margin-left: 12px;
            margin-right: 5px;
        }

        .content {
            position: relative;
            top: 0cm;
            margin-bottom: 2cm;
            line-height: 2;
        }

        .footer {
            position: fixed;
            bottom: -2.5cm;
            left: 0cm;
            right: 0cm;
            height: 2cm;
            text-align: center;
            border-top-style: solid;
            border-top-width: 1px;
            border-top-color: #B1388D;
            padding-top: 0px;
            line-height: 1.6;
            font-size: 11px
        }

        .footer .label {
            margin-right: 4px;
            margin-left: 10px;
        }

        .label {
            font-weight: bold;
        }

        .logo {
            width: 25%;
        }

        .header {
            position: fixed;
            top: -2cm;
            left: 0cm;
            right: 0cm;
            height: 2cm;
            text-align: right;
        }

        .table-container {
            overflow: hidden;
            border-radius: 20px;
            border: 1px solid #B1388D;
            margin-top: 30px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background-color: #B1388D;
            color: white;
            font-size: 16px;
        }

        th {
            border: 1px solid #B1388D;
            padding: 6px;
            padding-bottom: 16px;
        }

        td {
            border: 1px solid gray;
            padding: 6px;
            padding-bottom: 16px;
        }

        .table-footer td {
            border: none;
            font-size: 16px;
            font-weight: 700;
        }

        .total-label {
            width: 13cm;
        }

        .table-footer .total-label {
            text-align: right;
            color: #B1388D;
        }

        .table-footer .total-invoice {
            text-align: right;
            background-color: #B1388D;
            color: white;
            padding-right: 10px;
            border-radius: 16px;
        }
    </style>
</head>

<body>
    <header class="header">
        <img class="logo" src="{{ $logo }}" alt="Logo da empresa" />
    </header>
    <footer class="footer">
        <span class="label">{{ $proposal->account->name }}</span>
        <br>
        <span class="label">CNPJ</span>{{ $proposal->account->cnpj }}
        <span class="label">Insc. Municipal</span>{{ $proposal->account->inscricao_municipal }}
        <img class="icons" src="{{ $whatsappIcon }}" alt="whatsapp-icon">{{ $proposal->account->phone }}
        <img class="icons" src="{{ $emailIcon }}" alt="email-icon">{{ $proposal->account->email }}
        <br>
        {{ $proposal->account->address }}
    </footer>
    <div class="content">
        <div>
            <h1>Proposta de prestação de serviço</h1>
        </div>
        <div>
            <p>
                <span class="label">Núm. Proposta:</span> {{ $proposal->id }}
            </p>
            <p>
                <span class="label">Para:</span>
                @if ($proposal->opportunity->company->business_name)
                    {{ $proposal->opportunity->company->business_name }}
                @elseif ($proposal->opportunity->company->legal_name)
                    {{ $proposal->opportunity->company->legal_name }}
                @else
                    {{ $proposal->opportunity->contact->name }}
                @endif
            </p>
            @if (!empty($proposal->opportunity->description))
            <br>
                <p><span class="label">Detalhamento:</span> {!! $proposal->opportunity->description !!}</p>
            @endif
        </div>
        <div>
            <h2>SERVIÇOS:</h2>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Qtde</th>
                        <th>Preço</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($proposal->ProposalServices as $proposalItem)
                        <tr>
                            <td>{{ $proposalItem->name }}</td>
                            <td style="text-align: center">{{ $proposalItem->quantity }}</td>
                            <td style="text-align: right">R$ {{ $proposalItem->price }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="table-footer">
            <table>
                <tr>
                    <td class="total-label">Total:</td>
                    <td class="total-invoice">R$ {{ $proposal->total_price }}</td>
                </tr>
            </table>
        </div>
        <div>
            <p>
                <br>
                <span class="label">Validade da proposta: </span> {{ $proposal->validity_days }} dias.
            </p>
            <p style="text-align:center;padding-top:50px">
                <br>
                {{ $proposal->account->address_city }}, {{ $today }}
            </p>
        </div>
    </div>
</body>

</html>
