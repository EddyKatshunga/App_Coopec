@extends('pdf.layouts.main')

@section('content')

<h4>FICHE DU MEMBRE</h4>

<p><strong>Nom :</strong> {{ $membre->nom }}</p>
<p><strong>Email :</strong> {{ $membre->user->email }}</p>
<p><strong>Date adhésion :</strong> {{ $membre->date_adhesion }}</p>

<h4>Comptes épargnes</h4>

<table>
    <thead>
        <tr>
            <th>Compte</th>
            <th>N°</th>
            <th>Solde CDF</th>
            <th>Solde USD</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($membre->comptes as $compte)
            <tr>
                <td>{{ $compte->intitule }}</td>
                <td>{{ $compte->numero_compte }}</td>
                <td>{{ number_format($compte->solde_cdf, 0) }}</td>
                <td>{{ number_format($compte->solde_usd, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<p style="margin-top:10px;">
    <strong>Total épargne :</strong>
    {{ number_format($total_cdf, 0) }} CDF /
    {{ number_format($total_usd, 2) }} USD
</p>

@endsection
