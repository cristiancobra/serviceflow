<!DOCTYPE html>
<html>

<head>
    <title>Proposta de prestação de serviço</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }

        p {
            margin-top: 20px;
            margin-bottom: 20px;
            font-size: 14px;
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

        .label {
            font-weight: bold;
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
    <div style='text-align: center; margin-bottom: 20px;'>
        <img src='{{ $proposal->account->logo }}' alt='Logo da empresa' />
    </div>

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
        <p><span class="label">Oportunidade:</span> {{ $proposal->opportunity->description }}</p>
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
        Essa proposta é válida por <span class="label"> {{ $proposal->validity_days }} </span> dias.
    </p>
</body>

</html>
