@extends('impressions.layout')

@section('title', $titre)

@section('content')
    <div class="text-center mb-6">
        <h1 class="text-xl font-black border-b-2 border-gray-800 inline-block px-4 pb-1 uppercase">{{ $titre }}</h1>
        <p class="text-sm text-gray-600 mt-2">Agence : <strong>{{ $cloture->agence->nom }}</strong> | Date : <strong>{{ $cloture->date_cloture->format('d/m/Y') }}</strong></p>
    </div>

    {{-- Section des Totaux avant le tableau --}}
    <div class="grid grid-cols-2 gap-6 mb-6">
        <div class="border-2 border-gray-100 rounded-lg p-3 bg-gray-50/50">
            <h3 class="text-[11px] font-black uppercase text-blue-900 mb-2 border-b border-blue-200">Résumé USD</h3>
            <div class="flex justify-between text-xs"><span>Report :</span> <span class="font-mono font-bold">{{ number_format_fr($cloture->report_epargne_usd) }}</span></div>
            <div class="flex justify-between text-xs py-1"><span>Total Depots :</span> <span class="font-mono font-bold">{{ number_format_fr($cloture->total_depot_usd) }}</span></div>
            <div class="flex justify-between text-xs py-1"><span>Total Retraits :</span> <span class="font-mono font-bold">{{ number_format_fr($cloture->total_retrait_usd) }}</span></div>
            <div class="flex justify-between text-sm pt-1 border-t-2 border-white font-black text-blue-700"><span>Solde Final :</span> <span class="font-mono font-black">${{ number_format_fr($cloture->solde_epargne_usd) }}</span></div>
        </div>

        <div class="border-2 border-gray-100 rounded-lg p-3 bg-gray-50/50">
            <h3 class="text-[11px] font-black uppercase text-green-900 mb-2 border-b border-green-200">Résumé CDF</h3>
            <div class="flex justify-between text-xs"><span>Report :</span> <span class="font-mono font-bold">{{ number_format_fr($cloture->report_epargne_cdf) }}</span></div>
            <div class="flex justify-between text-xs py-1"><span>Total Depots :</span> <span class="font-mono font-bold">{{ number_format_fr($cloture->total_depot_cdf) }}</span></div>
            <div class="flex justify-between text-xs py-1"><span>Total Retraits :</span> <span class="font-mono font-bold">{{ number_format_fr($cloture->total_retrait_cdf) }}</span></div>
            <div class="flex justify-between text-sm pt-1 border-t-2 border-white font-black text-green-700"><span>Solde Final :</span> <span class="font-mono font-black">{{ number_format_fr($cloture->solde_epargne_cdf) }} FC</span></div>
        </div>
    </div>

    {{-- Tableau Unique Chronologique --}}
    <table class="w-full text-[10px] table-custom mb-8">
        <thead class="bg-gray-800 text-white uppercase">
            <tr>
                <th class="w-16">Heure</th>
                <th class="text-left">Client / Compte</th>
                <th class="w-20">Mouvement</th>
                <th class="text-left">Agent/Caissier</th>
                <th class="w-12">Devise</th>
                <th class="text-right w-24">Montant</th>
                <th class="text-right w-24 bg-gray-700">Solde après</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
                @php 
                    $isDepot = str_contains(strtolower($item->type_transaction), 'depot');
                @endphp
                <tr class="{{ $loop->even ? 'bg-gray-50' : '' }}">
                    <td class="text-center font-mono">{{ $item->created_at->format('H:i') }}</td>
                    <td>
                        <div class="font-bold text-gray-800 uppercase">{{ $item->compte->user->name }}</div>
                        <div class="text-[8px] text-gray-500 font-mono">{{ $item->compte->numero_compte }}</div>
                    </td>
                    <td class="text-center">
                        <span class="px-2 py-0.5 rounded-sm font-black text-[8px] {{ $isDepot ? 'bg-green-100 text-green-800 border border-green-300' : 'bg-red-100 text-red-800 border border-red-300' }}">
                            {{ $isDepot ? 'DÉPÔT' : 'RETRAIT' }}
                        </span>
                    </td>
                    <td class="text-gray-600">{{ $item->agent_collecteur->user->name ?? $item->creator->name }}</td>
                    <td class="text-center font-bold">{{ $item->monnaie }}</td>
                    <td class="text-right font-mono font-bold {{ $isDepot ? 'text-green-700' : 'text-red-700' }}">
                        {{ $isDepot ? '+' : '-' }}{{ number_format_fr($item->montant) }}
                    </td>
                    <td class="text-right font-mono font-bold bg-gray-50 text-gray-700">
                        {{ number_format_fr($item->solde_apres) }}
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center py-8 text-gray-400 italic">Aucune transaction enregistrée pour cette journée.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection