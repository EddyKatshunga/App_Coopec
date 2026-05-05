@extends('impressions.layout')

@section('title', 'Relevé des Crédits Octroyés')

@section('content')
    <div class="text-center mb-6">
        <h1 class="text-xl font-black border-b-2 border-purple-800 inline-block px-4 pb-1 uppercase text-purple-900">RELEVÉ DES CRÉDITS OCTROYÉS</h1>
        <p class="text-sm text-gray-600 mt-2">Agence : <strong>{{ $cloture->agence->nom }}</strong> | Date : <strong>{{ $cloture->date_cloture->format('d/m/Y') }}</strong></p>
    </div>

    {{-- Section des Totaux avant le tableau --}}
    <div class="grid grid-cols-2 gap-6 mb-6">
        <div class="border border-purple-100 rounded-lg p-3 bg-purple-50/30">
            <h3 class="text-[10px] font-black uppercase text-purple-900 mb-2 border-b border-purple-200">Cumul Crédits (USD)</h3>
            <div class="flex justify-between text-xs"><span>Capital :</span> <span class="font-mono font-bold">{{ number_format_fr($items->where('monnaie', 'USD')->sum('capital')) }}</span></div>
            <div class="flex justify-between text-xs py-0.5 text-gray-500"><span>Intérêts prévus :</span> <span class="font-mono font-bold">{{ number_format_fr($items->where('monnaie', 'USD')->sum('interet')) }}</span></div>
            <div class="flex justify-between text-sm pt-1 border-t border-purple-200 font-black text-purple-700"><span>Total à Recouvrer :</span> <span class="font-mono">${{ number_format_fr($cloture->total_credit_usd + $items->where('monnaie', 'USD')->sum('interet')) }}</span></div>
        </div>

        <div class="border border-gray-100 rounded-lg p-3 bg-gray-50/50">
            <h3 class="text-[10px] font-black uppercase text-gray-700 mb-2 border-b border-gray-200">Cumul Crédits (CDF)</h3>
            <div class="flex justify-between text-xs"><span>Capital :</span> <span class="font-mono font-bold">{{ number_format_fr($items->where('monnaie', 'CDF')->sum('capital')) }}</span></div>
            <div class="flex justify-between text-xs py-0.5 text-gray-500"><span>Intérêts prévus :</span> <span class="font-mono font-bold">{{ number_format_fr($items->where('monnaie', 'CDF')->sum('interet')) }}</span></div>
            <div class="flex justify-between text-sm pt-1 border-t border-gray-200 font-black"><span>Total à Recouvrer :</span> <span class="font-mono">{{ number_format_fr($cloture->total_credit_cdf + $items->where('monnaie', 'CDF')->sum('interet')) }} FC</span></div>
        </div>
    </div>

    {{-- Tableau Unique des Crédits --}}
    <table class="w-full text-[10px] table-custom mb-8">
        <thead class="bg-purple-900 text-white uppercase font-bold">
            <tr>
                <th class="w-16">Heure</th>
                <th class="text-left">Réf / Bénéficiaire</th>
                <th class="text-left w-24">Zone</th>
                <th class="w-12 text-center">Devise</th>
                <th class="text-right w-24">Capital</th>
                <th class="text-right w-20">Intérêt</th>
                <th class="text-right w-24">Date Fin</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $credit)
            <tr class="{{ $loop->even ? 'bg-purple-50/20' : '' }}">
                <td class="text-center font-mono">{{ $credit->created_at->format('H:i') }}</td>
                <td>
                    <div class="font-bold text-gray-800 uppercase">{{ $credit->user->name ?? 'N/A' }}</div>
                    <div class="text-[8px] text-gray-500 font-mono">Code: {{ $credit->numero_credit ?? 'N/A' }}</div>
                </td>
                <td class="uppercase text-gray-600">{{ $credit->zone->nom ?? 'N/A' }}</td>
                <td class="text-center font-bold">{{ $credit->monnaie }}</td>
                <td class="text-right font-mono font-bold">{{ number_format_fr($credit->capital) }}</td>
                <td class="text-right font-mono italic text-purple-700">{{ number_format_fr($credit->interet) }}</td>
                <td class="text-right font-mono font-bold text-gray-600">
                    {{ $credit->date_fin_prevue ? $credit->date_fin_prevue->format('d/m/Y') : 'N/A' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-8 italic text-gray-400">Aucun crédit octroyé ce jour.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot class="bg-gray-100 font-bold border-t-2 border-purple-900">
            <tr>
                <td colspan="4" class="text-right p-2">TOTAUX CUMULÉS DU JOUR (USD + CDF)</td>
                <td class="text-right font-mono p-2">VOIR RÉSUMÉ HAUT</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

    <div class="grid grid-cols-3 gap-8 text-center mt-12">
        <div class="border-t border-gray-400 pt-2"><p class="font-bold text-[10px] uppercase">Agent de Crédit</p></div>
        <div class="border-t border-gray-400 pt-2"><p class="font-bold text-[10px] uppercase">Comptabilité</p></div>
        <div class="border-t border-gray-400 pt-2"><p class="font-bold text-[10px] uppercase">Chef d'Agence / Gérance</p></div>
    </div>
@endsection