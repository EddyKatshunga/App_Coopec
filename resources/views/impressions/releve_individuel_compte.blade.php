@extends('impressions.layout')

@section('title', $titre)

@php
    // Calcul des totaux pour le résumé en haut
    $totalDepots = $items->where('type_transaction', 'DEPOT')->sum('montant');
    $totalRetraits = $items->where('type_transaction', 'RETRAIT')->sum('montant');
    
    // On récupère la première et la dernière transaction pour montrer l'évolution du solde
    $premierSolde = $items->first()->solde_avant ?? 0;
    $dernierSolde = $items->last()->solde_apres ?? 0;
@endphp

@section('content')
    {{-- Entête du Relevé --}}
    <div class="flex justify-between items-start border-b-2 border-gray-800 pb-4 mb-6">
        <div>
            <h1 class="text-2xl font-black uppercase text-gray-800">{{ $titre }}</h1>
            <p class="text-sm text-gray-600">Période du <strong>{{ \Carbon\Carbon::parse($filtres['debut'])->format('d/m/Y') }}</strong> au <strong>{{ \Carbon\Carbon::parse($filtres['fin'])->format('d/m/Y') }}</strong></p>
            <p class="text-[10px] text-gray-500 mt-1 italic">Filtres appliqués : Monnaie [{{ $filtres['monnaie'] }}] </p>
        </div>
        <div class="text-right">
            <h2 class="text-lg font-bold text-blue-900">{{ $compte->intitule }}</h2>
            <p class="text-sm font-mono text-gray-700">N° Compte : {{ $compte->numero_compte }}</p>
            <p class="text-xs text-gray-500">Membre : {{ $compte->membre->nom }} {{ $compte->membre->prenom }}</p>
        </div>
    </div>

    {{-- Résumé financier de la période --}}
    <div class="grid grid-cols-4 gap-4 mb-8">
        <div class="bg-gray-50 border p-3 rounded text-center">
            <span class="block text-[9px] uppercase text-gray-500 font-bold">Solde au {{ \Carbon\Carbon::parse($filtres['debut'])->format('d/m/Y') }}</span>
            <span class="text-sm font-mono font-black">{{ number_format_fr($premierSolde) }}</span>
        </div>
        <div class="bg-green-50 border border-green-200 p-3 rounded text-center text-green-800">
            <span class="block text-[9px] uppercase font-bold text-green-600">Total Dépôts (+)</span>
            <span class="text-sm font-mono font-black">+ {{ number_format_fr($totalDepots) }}</span>
        </div>
        <div class="bg-red-50 border border-red-200 p-3 rounded text-center text-red-800">
            <span class="block text-[9px] uppercase font-bold text-red-600">Total Retraits (-)</span>
            <span class="text-sm font-mono font-black">- {{ number_format_fr($totalRetraits) }}</span>
        </div>
        <div class="bg-blue-900 border p-3 rounded text-center text-white">
            <span class="block text-[9px] uppercase font-bold opacity-80 text-white">Solde au {{ \Carbon\Carbon::parse($filtres['fin'])->format('d/m/Y') }}</span>
            <span class="text-sm font-mono font-black text-white">{{ number_format_fr($dernierSolde) }}</span>
        </div>
    </div>

    {{-- Tableau des Transactions --}}
    <table class="w-full text-[10px] table-custom mb-10">
        <thead class="bg-gray-100 text-gray-800 uppercase">
            <tr>
                <th class="w-20">Date</th>
                <th class="w-16">Heure</th>
                <th class="text-left">Désignation / Opération</th>
                <th class="w-10 text-center">Devise</th>
                <th class="text-right w-24">Débit (Out)</th>
                <th class="text-right w-24">Crédit (In)</th>
                <th class="text-right w-28 bg-gray-200">Solde Progressif</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
                @php $isDepot = $item->type_transaction === 'DEPOT'; @endphp
                <tr class="{{ $loop->even ? 'bg-gray-50' : '' }}">
                    <td class="text-center font-mono">{{ \Carbon\Carbon::parse($item->date_transaction)->format('d/m/Y') }}</td>
                    <td class="text-center text-gray-500 italic">{{ \Carbon\Carbon::parse($item->created_at)->format('H:i') }}</td>
                    <td>
                        <span class="font-bold text-gray-700">{{ $isDepot ? 'Versement espèces' : 'Retrait espèces' }}</span>
                        @if($item->agent_collecteur)
                            <div class="text-[8px] text-gray-500 italic">Collecté par : {{ $item->agent_collecteur->user->name }}</div>
                        @endif
                    </td>
                    <td class="text-center font-bold">{{ $item->monnaie }}</td>
                    <td class="text-right font-mono text-red-700">
                        {{ !$isDepot ? number_format_fr($item->montant) : '' }}
                    </td>
                    <td class="text-right font-mono text-green-700 font-bold">
                        {{ $isDepot ? number_format_fr($item->montant) : '' }}
                    </td>
                    <td class="text-right font-mono font-black bg-gray-100">
                        {{ number_format_fr($item->solde_apres) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-10 text-gray-400 italic">
                        Aucun mouvement enregistré pour cette période avec les filtres sélectionnés.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pied de page certifié --}}
    <div class="mt-8 flex justify-between items-end">
        <div class="text-[9px] text-gray-500">
            <p>Imprimé le : {{ now()->format('d/m/Y H:i:s') }}</p>
            <p>Ce relevé est une copie certifiée conforme des écritures en nos livres.</p>
        </div>
        <div class="w-48 text-center border-t border-gray-800 pt-2">
            <p class="text-[10px] font-black uppercase">Le Gérant / Signature</p>
            <div class="h-16"></div> {{-- Espace pour le cachet --}}
        </div>
    </div>
@endsection