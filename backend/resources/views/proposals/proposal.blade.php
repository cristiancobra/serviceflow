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
            margin-top: 20px;
            margin-bottom: 20px;
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

        table {
            table-layout: fixed;
            width: 100%;
        }

        td {
            padding-left: 10px;
            padding-right: 10px;
            padding-top: 20px;
            padding-bottom: 20px;
            border-style: solid;
            border-width: 1px;
            border-color: lightgray;
        }

        th {
            background-color: #B1388D;
            color: white;
            padding: 10px;
            font-size: 16px;
        }

        tr {
            border-bottom-style: solid;
            border-bottom-width: 1px;
            border-bottom-color: #B1388D;
        }

        th,
        td {
            overflow: hidden;
        }

        th:first-child,
        td:first-child {
            width: 70%;
        }

        th:last-child,
        td:last-child {
            width: 30%;
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

        .total {
            font-weight: bold;
            text-align: right;
            color: white;
        }

        .money {
            text-align: right;
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
        <h1>Proposta de prestação de serviço</h1>
        <p>
            <span class="label">Proposta:</span> {{ $proposal->id }}
        </p>
        <p>
            <span class="label">Cliente:</span> {{ $proposal->opportunity->company->legal_name }}
        </p>
        <p>
            <span class="label">Data:</span> {{ $proposal->date }}
        </p>
        @if (!empty($proposal->opportunity->description))
            <p><span class="label">Oportunidade:</span> {!! $proposal->opportunity->description !!}</p>
        @endif
        <h2>SERVIÇOS:</h2>
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Preço</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($proposal->ProposalServices as $proposalItem)
                    <tr>
                        <td>{{ $proposalItem->name }}</td>
                        <td class="money">R$ {{ $proposalItem->price }}</td>
                    </tr>
                @endforeach
                <tr>
                    <th class="total">Total</th>
                    <th class="money total">R$ {{ $proposal->total_price }}</th>
                </tr>
            </tbody>
        </table>
        <p>
            <br>
            <span class="label">Validade da proposta: </span> {{ $proposal->validity_days }} dias.
        </p>
        <p style="text-align:center;padding-top:50px">
            <br>
            {{ $proposal->account->address_city }}, {{ $today }}
        </p>
    </div>
</body>

</html>
