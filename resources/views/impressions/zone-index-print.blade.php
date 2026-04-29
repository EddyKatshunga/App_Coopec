@extends('impressions.layout')

@section('title', 'Situation des crédits - ' . $agence->nom)

@section('content')
<div class="print-container">
    {{-- En-tête spécifique au document --}}
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold uppercase tracking-tight">Situation générale des crédits</h1>
        <h2 class="text-lg font-semibold text-gray-700">Agence : {{ $agence->nom }}</h2>
        <p class="text-sm text-gray-500">Arrêté au {{ now()->format('d/m/Y à H:i') }}</p>
        <p class="text-sm text-gray-600">Chef d'agence : {{ $agence->chefAgence?->nom ?? 'Non renseigné' }}</p>
    </div>

    {{-- Cartes de synthèse (adaptées pour l'impression) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        @foreach(['USD' => 'text-emerald-700', 'CDF' => 'text-blue-700'] as $devise => $color)
            @php $s = $statsGlobales[$devise]; @endphp
            <div class="border border-gray-300 rounded-lg p-4 bg-gray-50">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase">{{ $devise }} - Portefeuille</p>
                        <p class="text-2xl font-black {{ $color }}">{{ number_format($s->capital_interet_total, 2) }}</p>
                    </div>
                    <div class="px-2 py-1 bg-white rounded text-xs font-bold">{{ $devise }}</div>
                </div>
                <p class="text-sm mt-2">Reste à recouvrer : <strong>{{ number_format($s->reste_a_recouvrer, 2) }}</strong></p>
                <div class="flex gap-2 mt-2 text-xs">
                    <span class="px-2 py-1 bg-gray-200 rounded">{{ $s->nombre_credits_actifs }} dossiers actifs</span>
                    <span class="px-2 py-1 bg-red-100 text-red-700 rounded">{{ $s->nombre_retards }} retards</span>
                </div>
            </div>
        @endforeach
        <div class="border border-gray-300 rounded-lg p-4 bg-gray-800 text-white">
            <p class="text-xs font-bold uppercase">Dossiers clôturés</p>
            <p class="text-3xl font-black">{{ $statsGlobales['termine'] }}</p>
            <p class="text-xs mt-1">avec succès</p>
        </div>
    </div>

    {{-- Tableau des zones --}}
    <div class="mt-8">
        <h3 class="text-md font-bold uppercase border-b border-gray-400 pb-2 mb-4">Détail par zone</h3>
        <table class="w-full text-sm border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border border-gray-300 px-3 py-2 text-left">Zone / Code</th>
                    <th class="border border-gray-300 px-3 py-2 text-left">Gérant</th>
                    <th class="border border-gray-300 px-3 py-2 text-center">Dossiers</th>
                    <th class="border border-gray-300 px-3 py-2 text-right">Prêté (USD)</th>
                    <th class="border border-gray-300 px-3 py-2 text-right">Prêté (CDF)</th>
                    <th class="border border-gray-300 px-3 py-2 text-right">Récupéré (USD)</th>
                    <th class="border border-gray-300 px-3 py-2 text-right">Récupéré (CDF)</th>
                    <th class="border border-gray-300 px-3 py-2 text-right">Reste (USD)</th>
                    <th class="border border-gray-300 px-3 py-2 text-right">Reste (CDF)</th>
                    <th class="border border-gray-300 px-3 py-2 text-center">Retards</th>
                </tr>
            </thead>
            <tbody>
                @forelse($zones as $zone)
                    @php
                        $usd = $zone->stats_usd;
                        $cdf = $zone->stats_cdf;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-300 px-3 py-2">
                            <strong>{{ $zone->nom }}</strong><br>
                            <span class="text-xs text-gray-500">{{ $zone->code }}</span>
                        </td>
                        <td class="border border-gray-300 px-3 py-2">{{ $zone->gerant?->nom ?? '—' }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-center">
                            {{ $zone->actifs_count }} / {{ $zone->total_credits_count }}
                        </td>
                        <td class="border border-gray-300 px-3 py-2 text-right">{{ number_format($usd->total_prete, 2) }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-right">{{ number_format($cdf->total_prete, 0, ',', ' ') }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-right">{{ number_format($usd->total_recupere, 2) }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-right">{{ number_format($cdf->total_recupere, 0, ',', ' ') }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-right font-bold text-red-700">{{ number_format($usd->reste_a_recouvrer, 2) }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-right font-bold text-red-700">{{ number_format($cdf->reste_a_recouvrer, 0, ',', ' ') }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-center">
                            @if($zone->retards_count > 0)
                                <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs">{{ $zone->retards_count }}</span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="border border-gray-300 px-3 py-8 text-center text-gray-500">
                            Aucune zone enregistrée pour cette agence.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pied de page spécifique --}}
    <div class="mt-6 text-xs text-gray-500 text-center border-t pt-2">
        <p>Document établi par {{ auth()->user()->name }} le {{ now()->format('d/m/Y H:i') }}</p>
        <p>{{ config('app.nom_entreprise') }} – Solidarité et développement</p>
    </div>
</div>
@endsection