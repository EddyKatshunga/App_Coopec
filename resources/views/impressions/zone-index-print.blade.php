{{-- resources/views/impressions/zone-index-print.blade.php --}}
@extends('impressions.layout')

@section('title', 'Liste des Zones - ' . $agence->nom)

@section('content')
<div class="print-container">
    {{-- En-tête du document --}}
    <div class="mb-6">
        <h1 class="text-2xl font-black text-gray-800 uppercase tracking-wide">Situation Générale des Zones</h1>
        <p class="text-sm text-gray-600">Agence : {{ $agence->nom }} | Période : Portefeuille actif au {{ now()->format('d/m/Y') }}</p>
    </div>

    {{-- Tableau des zones --}}
    <table class="w-full text-sm border-collapse table-custom">
        <thead class="bg-gray-100">
            <tr>
                <th class="text-left py-2 px-3 border">Zone</th>
                <th class="text-left py-2 px-3 border">Gérant</th>
                <th class="text-right py-2 px-3 border">Capital actif (USD/CDF)</th>
                <th class="text-right py-2 px-3 border">Exposition (USD/CDF)</th>
                <th class="text-right py-2 px-3 border">Taux de risque</th>
                <th class="text-right py-2 px-3 border">Crédits en retard</th>
            </tr>
        </thead>
        <tbody>
            @forelse($zones as $zone)
            <tr class="border-b">
                <td class="py-2 px-3 border">
                    <div class="font-bold">{{ $zone->nom }}</div>
                    <div class="text-xs text-gray-500">{{ $zone->code }}</div>
                </td>
                <td class="py-2 px-3 border">{{ $zone->gerant->nom ?? '—' }}</td>
                <td class="py-2 px-3 border text-right">
                    {{ number_format($zone->capital_actif_usd, 0) }} $<br>
                    <span class="text-xs text-gray-600">{{ number_format($zone->capital_actif_cdf, 0) }} FC</span>
                </td>
                <td class="py-2 px-3 border text-right">
                    {{ number_format($zone->exposition_usd, 0) }} $<br>
                    <span class="text-xs text-gray-600">{{ number_format($zone->exposition_cdf, 0) }} FC</span>
                </td>
                <td class="py-2 px-3 border text-right">
                    <span class="@if($zone->taux_risque_usd > 50) text-red-600 @elseif($zone->taux_risque_usd > 20) text-yellow-600 @else text-green-600 @endif">
                        {{ $zone->taux_risque_usd }}% USD
                    </span><br>
                    <span class="text-xs @if($zone->taux_risque_cdf > 50) text-red-600 @elseif($zone->taux_risque_cdf > 20) text-yellow-600 @else text-green-600 @endif">
                        {{ $zone->taux_risque_cdf }}% CDF
                    </span>
                </td>
                <td class="py-2 px-3 border text-right">
                    {{ $zone->credits_retard_actifs_usd + $zone->credits_retard_actifs_cdf }} dossiers
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center py-6 text-gray-500">Aucune zone trouvée</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- Résumé global --}}
    <div class="mt-8 grid grid-cols-4 gap-4">
        <div class="border p-4 rounded">
            <p class="text-xs uppercase tracking-wide text-gray-500">Capital actif total</p>
            <p class="text-xl font-bold">{{ number_format($statsGlobales['capital']['USD'], 0) }} $</p>
            <p class="text-sm">{{ number_format($statsGlobales['capital']['CDF'], 0) }} FC</p>
        </div>
        <div class="border p-4 rounded">
            <p class="text-xs uppercase tracking-wide text-gray-500">Exposition totale</p>
            <p class="text-xl font-bold">{{ number_format($statsGlobales['exposition']['USD'], 0) }} $</p>
            <p class="text-sm">{{ number_format($statsGlobales['exposition']['CDF'], 0) }} FC</p>
        </div>
        <div class="border p-4 rounded">
            <p class="text-xs uppercase tracking-wide text-gray-500">Crédits actifs</p>
            <p class="text-xl font-bold">{{ $statsGlobales['credits_actifs'] }}</p>
        </div>
        <div class="border p-4 rounded">
            <p class="text-xs uppercase tracking-wide text-gray-500">Crédits en retard</p>
            <p class="text-xl font-bold text-red-600">{{ $statsGlobales['credits_retard'] }}</p>
        </div>
    </div>

    <div class="mt-6 text-xs text-gray-500 italic">
        * Les montants sont exprimés en devise locale (CDF) et en dollars américains (USD).
    </div>
</div>
@endsection