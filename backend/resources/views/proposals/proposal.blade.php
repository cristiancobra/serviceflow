<div style='text-align: center; margin-bottom: 20px;'>
    <img src='{{ $proposal->account->logo }}' alt='Logo da empresa' />
</div>

<h1>Proposta {{ $proposal->id }}</h1>
<p>Cliente: {{ $proposal->opportunity->company->legal_name }}</p>
<p>Data: {{ $proposal->date }}</p>
<h2>Itens</h2>
<ul>
    @foreach ($proposal->ProposalServices as $proposalItem)
        <li>{{ $proposalItem->name }}: {{ $proposalItem->price }}</li>
    @endforeach
</ul>
