@extends('impressions.layout_landscape')

@section('title', 'Rapport Périodique - ' . $agence->nom)

@section('content')
    <div class="text-center mb-6">
        <h1 class="text-2xl font-black text-gray-800 border-b-2 border-double inline-block px-4 pb-1 uppercase">
            {{ $titre }}
        </h1>
        <p class="mt-2 text-sm font-bold text-gray-600">
            Agence : {{ $agence->nom }} | Période du {{ $filtres['debut'] }} au {{ $filtres['fin'] }}
        </p>
    </div>

    {{-- Tableau des données Exhaustif --}}
    <table class="w-full text-[9px] table-custom border-collapse">
        <thead class="bg-gray-100 uppercase font-bold text-gray-700 text-center">
            {{-- Ligne des grands groupes --}}
            <tr>
                <th rowspan="2" class="text-left align-middle w-16">Date</th>
                
                {{-- Groupe ENTRÉES --}}
                <th colspan="6" class="border-l-2 border-gray-400 bg-green-50 text-green-800">ENTRÉES (Mouvements Créditeurs)</th>
                
                {{-- Groupe SORTIES --}}
                <th colspan="6" class="border-l-2 border-gray-400 bg-red-50 text-red-800">SORTIES (Mouvements Débiteurs)</th>
                
                {{-- Groupe CAISSE --}}
                <th colspan="4" class="border-l-2 border-gray-400 bg-blue-50 text-blue-800">CAISSE (Fin de journée)</th>
                
                <th rowspan="2" class="border-l-2 border-gray-400 align-middle">Statut</th>
            </tr>
            
            {{-- Ligne des sous-catégories --}}
            <tr class="bg-gray-50">
                {{-- Entrées --}}
                <th class="border-l-2 border-gray-400" colspan="2">Dépôts</th>
                <th colspan="2">Rembours.</th>
                <th colspan="2">Revenus</th>
                
                {{-- Sorties --}}
                <th class="border-l-2 border-gray-400" colspan="2">Retraits</th>
                <th colspan="2">Crédits (Octrois)</th>
                <th colspan="2">Dépenses</th>
                
                {{-- Caisse --}}
                <th class="border-l-2 border-gray-400" colspan="2">Solde Théorique</th>
                <th colspan="2">Solde Physique</th>
            </tr>

            {{-- Ligne des Devises --}}
            <tr class="bg-gray-200 text-[8px]">
                <th></th>
                {{-- Entrées --}}
                <th class="border-l-2 border-gray-400">USD</th><th>CDF</th>
                <th>USD</th><th>CDF</th>
                <th>USD</th><th>CDF</th>
                {{-- Sorties --}}
                <th class="border-l-2 border-gray-400">USD</th><th>CDF</th>
                <th>USD</th><th>CDF</th>
                <th>USD</th><th>CDF</th>
                {{-- Caisse --}}
                <th class="border-l-2 border-gray-400">USD</th><th>CDF</th>
                <th>USD</th><th>CDF</th>
                <th class="border-l-2 border-gray-400"></th>
            </tr>
        </thead>
        
        <tbody>
            @foreach($clotures as $cloture)
                <tr class="even:bg-gray-50 hover:bg-yellow-50">
                    <td class="font-bold text-center">{{ $cloture->date_cloture->format('d/m/y') }}</td>
                    
                    {{-- Entrées --}}
                    <td class="text-right border-l-2 border-gray-400">{{ number_format($cloture->total_depot_usd, 2) }}</td>
                    <td class="text-right">{{ number_format($cloture->total_depot_cdf, 0) }}</td>
                    <td class="text-right">{{ number_format($cloture->total_remboursement_usd, 2) }}</td>
                    <td class="text-right">{{ number_format($cloture->total_remboursement_cdf, 0) }}</td>
                    <td class="text-right text-green-700 font-semibold">{{ number_format($cloture->total_revenu_usd, 2) }}</td>
                    <td class="text-right text-green-700 font-semibold">{{ number_format($cloture->total_revenu_cdf, 0) }}</td>
                    
                    {{-- Sorties --}}
                    <td class="text-right border-l-2 border-gray-400">{{ number_format($cloture->total_retrait_usd, 2) }}</td>
                    <td class="text-right">{{ number_format($cloture->total_retrait_cdf, 0) }}</td>
                    <td class="text-right">{{ number_format($cloture->total_credit_usd, 2) }}</td>
                    <td class="text-right">{{ number_format($cloture->total_credit_cdf, 0) }}</td>
                    <td class="text-right text-red-700 font-semibold">{{ number_format($cloture->total_depense_usd, 2) }}</td>
                    <td class="text-right text-red-700 font-semibold">{{ number_format($cloture->total_depense_cdf, 0) }}</td>
                    
                    {{-- Caisse --}}
                    <td class="text-right border-l-2 border-gray-400 font-bold">{{ number_format($cloture->solde_coffre_usd, 2) }}</td>
                    <td class="text-right font-bold">{{ number_format($cloture->solde_coffre_cdf, 0) }}</td>
                    <td class="text-right bg-blue-50">{{ number_format($cloture->physique_coffre_usd, 2) }}</td>
                    <td class="text-right bg-blue-50">{{ number_format($cloture->physique_coffre_cdf, 0) }}</td>
                    
                    {{-- Statut --}}
                    <td class="text-center border-l-2 border-gray-400 font-bold {{ $cloture->estCloturee() ? 'text-green-600' : 'text-orange-500' }}">
                        {{ strtoupper($cloture->statut) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        
        <tfoot class="bg-gray-800 text-white font-bold">
            <tr>
                <td class="uppercase text-center">TOTAUX</td>
                
                {{-- Entrées Totaux --}}
                <td class="text-right border-l-2 border-gray-400">{{ number_format($totaux['depot_usd'], 2) }}</td>
                <td class="text-right">{{ number_format($totaux['depot_cdf'], 0) }}</td>
                <td class="text-right">{{ number_format($totaux['rembourse_usd'], 2) }}</td>
                <td class="text-right">{{ number_format($totaux['rembourse_cdf'], 0) }}</td>
                <td class="text-right">{{ number_format($totaux['revenu_usd'], 2) }}</td>
                <td class="text-right">{{ number_format($totaux['revenu_cdf'], 0) }}</td>
                
                {{-- Sorties Totaux --}}
                <td class="text-right border-l-2 border-gray-400">{{ number_format($totaux['retrait_usd'], 2) }}</td>
                <td class="text-right">{{ number_format($totaux['retrait_cdf'], 0) }}</td>
                <td class="text-right">{{ number_format($totaux['credit_usd'], 2) }}</td>
                <td class="text-right">{{ number_format($totaux['credit_cdf'], 0) }}</td>
                <td class="text-right">{{ number_format($totaux['depense_usd'], 2) }}</td>
                <td class="text-right">{{ number_format($totaux['depense_cdf'], 0) }}</td>
                
                {{-- Caisse Totaux (Vides car on n'additionne pas des soldes) --}}
                <td class="border-l-2 border-gray-400 bg-gray-700" colspan="5">
                    <span class="text-[8px] font-normal italic px-2 text-gray-300">Les soldes ne sont pas cumulables</span>
                </td>
            </tr>
        </tfoot>
    </table>

    {{-- Signature --}}
    <div class="mt-8 grid grid-cols-3 gap-8 text-center text-[11px] uppercase font-bold">
        <div>
            <p class="mb-10 underline">Le Comptable</p>
            <p>{{ auth()->user()->name }}</p>
        </div>
        <div>
            <p class="mb-10 underline">Chef d'Agence</p>
            <p>..................................</p>
        </div>
        <div>
            <p class="mb-10 underline">Direction Générale</p>
            <p>..................................</p>
        </div>
    </div>
@endsection