@extends('impressions.layout')

@section('title', $titre)

@php
    $monnaieFiltre = $filtres['monnaie'];
    
    // Fonction helper pour extraire les soldes spécifiques
    $getPremierSolde = function($devise) use ($items) {
        return $items->where('monnaie', $devise)->first()->solde_avant ?? 0;
    };
    
    $getDernierSolde = function($devise) use ($items) {
        return $items->where('monnaie', $devise)->last()->solde_apres ?? 0;
    };

    // Préparation des devises à afficher dans le résumé
    $devisesAffichage = ($monnaieFiltre == 'USD' || $monnaieFiltre == 'CDF') 
                        ? [$monnaieFiltre] 
                        : ['USD', 'CDF'];
@endphp

@section('content')
    {{-- Entête du Relevé --}}
    <div class="flex justify-between items-start border-b-2 border-gray-800 pb-4 mb-4">
        <div>
            <h1 class="text-2xl font-black uppercase text-gray-800">{{ $titre }}</h1>
            <p class="text-sm text-gray-600">Période du <strong>{{ \Carbon\Carbon::parse($filtres['debut'])->format('d/m/Y') }}</strong> au <strong>{{ \Carbon\Carbon::parse($filtres['fin'])->format('d/m/Y') }}</strong></p>
            <p class="text-[10px] text-gray-500 mt-1 italic uppercase">Option monnaie : {{ $monnaieFiltre ?: 'Multidevise (Toutes)' }}</p>
        </div>
        <div class="text-right">
            <h2 class="text-lg font-bold text-blue-900">{{ $compte->intitule }}</h2>
            <p class="text-sm font-mono text-gray-700 uppercase">N° {{ $compte->numero_compte }}</p>
            <p class="text-xs text-gray-500">Membre : {{ $compte->membre->nom }} {{ $compte->membre->prenom }}</p>
        </div>
    </div>

    {{-- Résumé financier Dynamique --}}
    <div class="space-y-2 mb-8">
        @foreach($devisesAffichage as $devise)
            @php
                $depots = $items->where('monnaie', $devise)->where('type_transaction', 'DEPOT')->sum('montant');
                $retraits = $items->where('monnaie', $devise)->where('type_transaction', 'RETRAIT')->sum('montant');
                $initial = $getPremierSolde($devise);
                $final = $getDernierSolde($devise);
            @endphp
            
            {{-- On n'affiche la ligne que s'il y a eu des mouvements ou si c'est la monnaie filtrée --}}
            @if($items->where('monnaie', $devise)->count() > 0 || $monnaieFiltre == $devise)
                <div class="grid grid-cols-4 gap-2">
                    <div class="bg-gray-50 border p-2 rounded">
                        <span class="block text-[8px] uppercase text-gray-400 font-bold">Initial ({{ $devise }})</span>
                        <span class="text-xs font-mono font-bold">{{ number_format_fr($initial) }}</span>
                    </div>
                    <div class="bg-green-50/50 border border-green-100 p-2 rounded text-green-700">
                        <span class="block text-[8px] uppercase font-bold text-green-500">Dépôts (+)</span>
                        <span class="text-xs font-mono font-bold">+ {{ number_format_fr($depots) }}</span>
                    </div>
                    <div class="bg-red-50/50 border border-red-100 p-2 rounded text-red-700">
                        <span class="block text-[8px] uppercase font-bold text-red-500">Retraits (-)</span>
                        <span class="text-xs font-mono font-bold">- {{ number_format_fr($retraits) }}</span>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 p-2 rounded text-blue-900">
                        <span class="block text-[8px] uppercase font-bold text-blue-400">Final ({{ $devise }})</span>
                        <span class="text-xs font-mono font-black">{{ number_format_fr($final) }}</span>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    {{-- Tableau des Transactions --}}
    <table class="w-full text-[10px] table-custom mb-10">
        <thead class="bg-gray-800 text-white uppercase">
            <tr>
                <th class="w-20 p-2">Date</th>
                <th class="text-left p-2">Libellé de l'opération</th>
                <th class="w-12 text-center p-2">Devise</th>
                <th class="text-right w-24 p-2">Débit (Sortie)</th>
                <th class="text-right w-24 p-2">Crédit (Entrée)</th>
                <th class="text-right w-28 p-2 bg-gray-700">Solde Progressif</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
                @php $isDepot = $item->type_transaction === 'DEPOT'; @endphp
                <tr class="{{ $loop->even ? 'bg-gray-50' : '' }} border-b">
                    <td class="text-center font-mono py-2">
                        {{ \Carbon\Carbon::parse($item->date_transaction)->format('d/m/Y') }}
                        <div class="text-[8px] text-gray-400">{{ \Carbon\Carbon::parse($item->created_at)->format('H:i') }}</div>
                    </td>
                    <td class="py-2">
                        <div class="font-bold text-gray-700">{{ $isDepot ? 'VERSEMENT ESPÈCES' : 'RETRAIT ESPÈCES' }}</div>
                        <div class="text-[8px] text-gray-500 italic">Réf: #{{ $item->id }} | Par: {{ $item->creator->name ?? 'Système' }}</div>
                    </td>
                    <td class="text-center font-black py-2">{{ $item->monnaie }}</td>
                    <td class="text-right font-mono text-red-600 py-2">
                        {{ !$isDepot ? number_format_fr($item->montant) : '-' }}
                    </td>
                    <td class="text-right font-mono text-green-600 font-bold py-2">
                        {{ $isDepot ? number_format_fr($item->montant) : '-' }}
                    </td>
                    <td class="text-right font-mono font-black bg-gray-100/50 py-2">
                        {{ number_format_fr($item->solde_apres) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-12 text-gray-400 italic border">
                        Aucun mouvement enregistré pour cette sélection.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Footer de certification --}}
    <div class="mt-auto pt-10 flex justify-between items-start">
        <div class="text-[8px] text-gray-400 leading-tight">
            <p>Document généré le {{ now()->format('d/m/Y à H:i') }}</p>
            <p>L'intégrité de ce relevé peut être vérifiée auprès de notre service comptabilité.</p>
        </div>
        <div class="grid grid-cols-2 gap-12 text-center">
            <div class="w-32">
                <p class="text-[9px] font-bold uppercase mb-12">Client</p>
                <div class="border-t border-gray-300 pt-1 text-[8px]">Signature</div>
            </div>
            <div class="w-32">
                <p class="text-[9px] font-bold uppercase mb-12">Pour l'Institution</p>
                <div class="border-t border-gray-300 pt-1 text-[8px]">Cachet et Signature</div>
            </div>
        </div>
    </div>
@endsection