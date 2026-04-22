{{-- resources/views/impressions/zone-show-print.blade.php --}}
@extends('impressions.layout')

@section('title', 'Fiche Zone - ' . $zone->nom)

@section('content')
<div class="print-container">
    {{-- En-tête avec informations clés --}}
    <div class="mb-6">
        <div class="flex justify-between items-end border-double-bottom pb-3">
            <div>
                <h1 class="text-2xl font-black text-gray-800 uppercase">Fiche de Suivi Zone</h1>
                <p class="text-lg font-bold text-indigo-700">{{ $zone->nom }} <span class="text-sm font-normal text-gray-500">({{ $zone->code }})</span></p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-500">Gérant : {{ $zone->gerant->nom ?? 'Non assigné' }}</p>
                <p class="text-xs text-gray-500">Agence : {{ $zone->agence->nom }}</p>
                <p class="text-xs text-gray-500">Dernière activité : {{ $zone->derniere_activite_at ? $zone->derniere_activite_at->format('d/m/Y') : '—' }}</p>
            </div>
        </div>
    </div>

    {{-- Indicateurs de performance (KPIs) --}}
    <div class="grid grid-cols-2 gap-6 mb-8">
        <div>
            <h3 class="font-bold text-gray-700 border-b pb-1 mb-3">Portefeuille CDF</h3>
            <table class="w-full text-sm">
                <tr><td class="py-1">Capital actif :</td><td class="text-right font-bold">{{ number_format($dashboard['CDF']['capital'], 0) }} FC</td></tr>
                <tr><td class="py-1">Intérêts actifs :</td><td class="text-right font-bold">{{ number_format($dashboard['CDF']['interet'], 0) }} FC</td></tr>
                <tr><td class="py-1 border-t">Exposition :</td><td class="text-right font-bold border-t">{{ number_format($dashboard['CDF']['exposition'], 0) }} FC</td></tr>
                <tr><td class="py-1">Crédits en retard :</td><td class="text-right">{{ $dashboard['CDF']['credits_retard'] }} dossier(s)</td></tr>
                <tr><td class="py-1">Taux de risque :</td><td class="text-right">{{ $dashboard['CDF']['taux_risque'] }}%</td></tr>
                <tr><td class="py-1">Niveau de risque :</td><td class="text-right font-bold uppercase">{{ $dashboard['CDF']['niveau_risque'] }}</td></tr>
            </table>
        </div>
        <div>
            <h3 class="font-bold text-gray-700 border-b pb-1 mb-3">Portefeuille USD</h3>
            <table class="w-full text-sm">
                <tr><td class="py-1">Capital actif :</td><td class="text-right font-bold">{{ number_format($dashboard['USD']['capital'], 2) }} $</td></tr>
                <tr><td class="py-1">Intérêts actifs :</td><td class="text-right font-bold">{{ number_format($dashboard['USD']['interet'], 2) }} $</td></tr>
                <tr><td class="py-1 border-t">Exposition :</td><td class="text-right font-bold border-t">{{ number_format($dashboard['USD']['exposition'], 2) }} $</td></tr>
                <tr><td class="py-1">Crédits en retard :</td><td class="text-right">{{ $dashboard['USD']['credits_retard'] }} dossier(s)</td></tr>
                <tr><td class="py-1">Taux de risque :</td><td class="text-right">{{ $dashboard['USD']['taux_risque'] }}%</td></tr>
                <tr><td class="py-1">Niveau de risque :</td><td class="text-right font-bold uppercase">{{ $dashboard['USD']['niveau_risque'] }}</td></tr>
            </table>
        </div>
    </div>

    {{-- Synthèse globale --}}
    <div class="bg-gray-50 p-4 rounded border mb-8">
        <div class="flex justify-around">
            <div><span class="text-xs uppercase">Total crédits actifs :</span> <span class="font-bold text-lg">{{ $dashboard['global']['credits_actifs'] }}</span></div>
            <div><span class="text-xs uppercase">Exposition totale :</span> <span class="font-bold text-lg">{{ number_format($dashboard['global']['total_exposition'], 0) }} (CDF+USD)</span></div>
        </div>
    </div>

    {{-- Liste des crédits actifs --}}
    <h3 class="font-bold text-gray-700 border-b pb-1 mb-3">Crédits actifs (en cours / en retard)</h3>
    @if($credits_list->count())
    <table class="w-full text-sm table-custom">
        <thead class="bg-gray-100">
            <tr>
                <th class="text-left py-1 px-2 border">N° Crédit</th>
                <th class="text-left py-1 px-2 border">Membre</th>
                <th class="text-center py-1 px-2 border">Monnaie</th>
                <th class="text-right py-1 px-2 border">Capital</th>
                <th class="text-right py-1 px-2 border">Reste à payer</th>
                <th class="text-center py-1 px-2 border">Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($credits_list as $credit)
            <tr>
                <td class="py-1 px-2 border">{{ $credit->numero_credit }}</td>
                <td class="py-1 px-2 border">{{ $credit->membre->nom }}</td>
                <td class="py-1 px-2 border text-center">{{ $credit->monnaie }}</td>
                <td class="py-1 px-2 border text-right">{{ number_format($credit->capital, $credit->monnaie == 'USD' ? 2 : 0) }}</td>
                <td class="py-1 px-2 border text-right">{{ number_format($credit->reste_du, $credit->monnaie == 'USD' ? 2 : 0) }}</td>
                <td class="py-1 px-2 border text-center">{{ str_replace('_', ' ', $credit->statut) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p class="text-gray-500 italic">Aucun crédit actif dans cette zone.</p>
    @endif

    {{-- Observations --}}
    <div class="mt-8 text-xs text-gray-500 border-t pt-4">
        <p><strong>Note :</strong> Ce document reflète la situation du portefeuille actif (crédits non soldés). Les montants en USD sont donnés à titre indicatif.</p>
    </div>
</div>
@endsection