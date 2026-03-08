@extends('impressions.layout')

@section('title', 'Rapport Périodique - ' . $agence->nom)

@section('content')
    <div class="text-center mb-8">
        <h1 class="text-2xl font-black text-gray-800 border-b-2 border-double inline-block px-4 pb-1 uppercase">
            {{ $titre }}
        </h1>
        <p class="mt-2 text-sm font-bold text-gray-600">
            Agence : {{ $agence->nom }} | Période du {{ $filtres['debut'] }} au {{ $filtres['fin'] }}
        </p>
    </div>

    {{-- Tableau des données --}}
    <table class="w-full text-[10px] table-custom border-collapse">
        <thead class="bg-gray-100 uppercase font-bold text-gray-700">
            <tr>
                <th rowspan="2" class="text-left">Date</th>
                <th colspan="2">Épargne (Dépôts)</th>
                <th colspan="2">Épargne (Retraits)</th>
                <th colspan="2">Crédits (Octrois)</th>
                <th colspan="2">Remboursements</th>
                <th colspan="2">Dépenses</th>
            </tr>
            <tr>
                <th>USD</th><th>CDF</th>
                <th>USD</th><th>CDF</th>
                <th>USD</th><th>CDF</th>
                <th>USD</th><th>CDF</th>
                <th>USD</th><th>CDF</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clotures as $cloture)
                <tr>
                    <td class="font-bold">{{ $cloture->date_cloture->format('d/m/Y') }}</td>
                    <td class="text-right">{{ number_format($cloture->total_depot_usd, 2) }}</td>
                    <td class="text-right">{{ number_format($cloture->total_depot_cdf, 0) }}</td>
                    <td class="text-right text-red-600">{{ number_format($cloture->total_retrait_usd, 2) }}</td>
                    <td class="text-right text-red-600">{{ number_format($cloture->total_retrait_cdf, 0) }}</td>
                    <td class="text-right">{{ number_format($cloture->total_credit_usd, 2) }}</td>
                    <td class="text-right">{{ number_format($cloture->total_credit_cdf, 0) }}</td>
                    <td class="text-right text-green-600">{{ number_format($cloture->total_remboursement_usd, 2) }}</td>
                    <td class="text-right text-green-600">{{ number_format($cloture->total_remboursement_cdf, 0) }}</td>
                    <td class="text-right font-semibold">{{ number_format($cloture->total_depense_usd, 2) }}</td>
                    <td class="text-right font-semibold">{{ number_format($cloture->total_depense_cdf, 0) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="bg-gray-800 text-white font-bold">
            <tr>
                <td class="uppercase">TOTAUX</td>
                <td class="text-right">{{ number_format($totaux['depot_usd'], 2) }}</td>
                <td class="text-right">{{ number_format($totaux['depot_cdf'], 0) }}</td>
                <td class="text-right">{{ number_format($totaux['retrait_usd'], 2) }}</td>
                <td class="text-right">{{ number_format($totaux['retrait_cdf'], 0) }}</td>
                <td class="text-right">{{ number_format($totaux['credit_usd'], 2) }}</td>
                <td class="text-right">{{ number_format($totaux['credit_cdf'], 0) }}</td>
                <td class="text-right">{{ number_format($totaux['rembourse_usd'], 2) }}</td>
                <td class="text-right">{{ number_format($totaux['rembourse_cdf'], 0) }}</td>
                <td class="text-right">{{ number_format($totaux['depense_usd'], 2) }}</td>
                <td class="text-right">{{ number_format($totaux['depense_cdf'], 0) }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- Signature --}}
    <div class="mt-12 grid grid-cols-3 gap-8 text-center text-xs uppercase font-bold">
        <div>
            <p class="mb-12 underline">Le Comptable</p>
            <p>{{ auth()->user()->name }}</p>
        </div>
        <div>
            <p class="mb-12 underline">Chef d'Agence</p>
            <p>..................................</p>
        </div>
        <div>
            <p class="mb-12 underline">Direction Générale</p>
            <p>..................................</p>
        </div>
    </div>
@endsection