@extends('pdf.layouts.main')

@section('content')
<h1>Relevé du compte : {{ $compte->intitule }}</h1>
<p>Membre : {{ $membre->nom }}</p>
<p>Période : {{ $dateDebut->format('d/m/Y') }} - {{ $dateFin->format('d/m/Y') }}</p>

<h2>Soldes et totaux</h2>
<table>
    <tr>
        <td>Report CDF :</td>
        <td>{{ number_format($reportCDF, 0, ',', ' ') }} CDF</td>
    </tr>
    <tr>
        <td>Report USD :</td>
        <td>{{ number_format($reportUSD, 2) }} USD</td>
    </tr>
    <tr>
        <td>Total dépôts CDF :</td>
        <td>{{ number_format($totalDepotCDF, 0, ',', ' ') }} CDF</td>
    </tr>
    <tr>
        <td>Total retraits CDF :</td>
        <td>{{ number_format($totalRetraitCDF, 0, ',', ' ') }} CDF</td>
    </tr>
    <tr>
        <td>Total dépôts USD :</td>
        <td>{{ number_format($totalDepotUSD, 2) }} USD</td>
    </tr>
    <tr>
        <td>Total retraits USD :</td>
        <td>{{ number_format($totalRetraitUSD, 2) }} USD</td>
    </tr>
    <tr>
        <td>Solde final CDF :</td>
        <td>{{ number_format($soldeCDF, 0, ',', ' ') }} CDF</td>
    </tr>
    <tr>
        <td>Solde final USD :</td>
        <td>{{ number_format($soldeUSD, 2) }} USD</td>
    </tr>
</table>

<h2>Transactions</h2>
<table border="1" cellpadding="5">
    <thead>
        <tr>
            <th>Date</th>
            <th>Type</th>
            <th>Montant</th>
            <th>Monnaie</th>
            <th>Solde avant</th>
            <th>Solde après</th>
            <th>Statut</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transactions as $transaction)
        <tr>
            <td>{{ \Carbon\Carbon::parse($transaction->date_transaction)->format('d/m/Y') }}</td>
            <td>{{ $transaction->type_transaction }}</td>
            <td>{{ number_format($transaction->montant, 0, ',', ' ') }}</td>
            <td>{{ $transaction->monnaie }}</td>
            <td>{{ number_format($transaction->solde_avant, 0, ',', ' ') }}</td>
            <td>{{ number_format($transaction->solde_après, 0, ',', ' ') }}</td>
            <td>{{ $transaction->statut }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
