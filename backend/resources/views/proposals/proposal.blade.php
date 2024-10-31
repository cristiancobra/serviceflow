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
            /* margin-top: 20px;
            margin-bottom: 20px; */
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

        .table {
            /* display: table; */
            width: 100%;
        }

        .table-row {
            width: 100%;
            display: block;
        }

        .table-head {
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .table-footer {
            margin-top: 30px;
        }

        .table-column-name {
            display: block;
            float: left;
            padding: 6px;
            width: 80%;
            border-left-style: none;
            border-right-style: none;
            border-top-style: none;
            border-bottom-style: solid;
            border-bottom-width: 1px;
            border-bottom-color: gray;
        }

        .table-column-money {
            display: block;
            float: left;
            padding: 6px;
            text-align: right;
            width: 20%;
            border-left-style: none;
            border-right-style: none;
            border-top-style: none;
            border-bottom-style: solid;
            border-bottom-width: 1px;
            border-bottom-color: gray;
        }

        .table-column-total-label {
            color: #B1388D;
            background-color: white;
            font-size: 16px;
            display: block;
            float: left;
            text-align: right;
            padding: 6px;
            padding-top: 0px;
            width: 80%;
            font-weight: 700;
        }

        .table-column-total-invoice {
            background-color: #B1388D;
            color: white;
            font-size: 16px;
            text-align: right;
            display: block;
            float: left;
            padding-right: 10px;
            width: 20%;
            border-radius: 20px;
            font-weight: 700;
        }

        /* Clear floats after each row */
        .table-row::after {
            content: "";
            display: table;
            clear: both;
        }

        .table-header-name {
            background-color: #B1388D;
            color: white;
            font-size: 16px;
            display: block;
            float: left;
            text-align: center;
            width: 80%;
            border-radius: 20px 0px 0px 20px;
            font-weight: 700;
        }

        .table-header-price {
            background-color: #B1388D;
            color: white;
            font-size: 16px;
            text-align: center;
            display: block;
            float: left;
            width: 24%;
            border-radius: 0px 20px 20px 0px;
            font-weight: 700;
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
            <span class="label">Data:</span> {{ $proposalDate }}
        </p>
        @if (!empty($proposal->opportunity->description))
            <p><span class="label">Oportunidade:</span> {!! $proposal->opportunity->description !!}</p>
        @endif
        <h2>SERVIÇOS:</h2>
        <div class="table">
            <div class="table-head">
                <div class="table-row">
                    <div class="table-header-name">Nome</div>
                    <div class="table-header-price">Preço</div>
                </div>
            </div>
            <div class="table-body">
                @foreach ($proposal->ProposalServices as $proposalItem)
                    <div class="table-row">
                        <div class="table-column-name">{{ $proposalItem->name }}</div>
                        <div class="table-column-money">R$ {{ $proposalItem->price }}</div>
                    </div>
                @endforeach
            </div>
            <div class="table-footer">
                <div class="table-row">
                    <div class="table-column-total-label">Total:</div>
                    <div class="table-column-total-invoice">R$ {{ $proposal->total_price }}</div>
                </div>
            </div>
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
