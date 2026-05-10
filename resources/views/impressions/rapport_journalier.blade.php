@extends('impressions.layout')

@section('title', 'Rapport Journalier - ' . $cloture->date_cloture->format('d/m/Y'))

@section('content')
    {{-- Header --}}
    <div class="flex justify-between items-center border-b-4 border-gray-900 pb-4 mb-6">
        <div>
            <h1 class="text-2xl font-black text-gray-900">RAPPORT JOURNALIER DE CAISSE</h1>
            <p class="text-sm font-bold text-blue-800 uppercase italic">Agence : {{ $cloture->agence->nom }}</p>
        </div>
        <div class="text-right">
            <div class="text-xl font-mono font-bold bg-gray-900 text-white px-3 py-1 rounded">
                {{ $cloture->date_cloture->isoFormat('DD MMMM YYYY') }}
            </div>
            <p class="text-[10px] text-gray-500 mt-1 uppercase">Statut : <b>{{ $cloture->statut }}</b></p>
        </div>
    </div>

    {{-- Section 1 : Situation des Coffres (Le Coeur du métier) --}}
    <div class="mb-8">
        <h2 class="text-xs font-black bg-gray-100 p-2 border-l-4 border-gray-900 uppercase mb-3">I. Situation des Flux & Disponibilités</h2>
        <table class="w-full text-xs table-auto border-collapse">
            <thead>
                <tr class="bg-gray-800 text-white">
                    <th class="p-2 text-left">DESIGNATION</th>
                    <th class="p-2 text-right">MONTANT USD</th>
                    <th class="p-2 text-right">MONTANT CDF</th>
                </tr>
            </thead>
            <tbody class="border border-gray-300">
                <tr class="bg-emerald-50 font-bold border-b border-gray-200">
                    <td class="p-2 italic">A. TOTAL ENTRÉES (Reports + Flux)</td>
                    <td class="p-2 text-right">{{ number_format_fr($cloture->report_coffre_usd + $cloture->total_depot_usd + $cloture->total_remboursement_usd + $cloture->total_revenu_usd) }}</td>
                    <td class="p-2 text-right">{{ number_format_fr($cloture->report_coffre_cdf + $cloture->total_depot_cdf + $cloture->total_remboursement_cdf + $cloture->total_revenu_cdf) }}</td>
                </tr>
                <tr class="bg-red-50 font-bold border-b border-gray-200">
                    <td class="p-2 italic text-red-700 font-bold tracking-tighter uppercase leading-none">B. TOTAL SORTIES (Flux + Crédits + Frais)</td>
                    <td class="p-2 text-right text-red-700">{{ number_format_fr($cloture->total_retrait_usd + $cloture->total_credit_usd + $cloture->total_depense_usd) }}</td>
                    <td class="p-2 text-right text-red-700">{{ number_format_fr($cloture->total_retrait_cdf + $cloture->total_credit_cdf + $cloture->total_depense_cdf) }}</td>
                </tr>
                <tr class="bg-blue-900 text-white font-black text-sm">
                    <td class="p-3">SOLDE NET EN COFFRE (A - B)</td>
                    <td class="p-3 text-right">{{ number_format_fr($cloture->solde_coffre_usd) }} $</td>
                    <td class="p-3 text-right">{{ number_format_fr($cloture->solde_coffre_cdf) }} FC</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Section 3 : Performance Crédit & Épargne --}}
    <div class="grid grid-cols-2 gap-8 mb-10">
        <div class="p-3 bg-indigo-50 rounded-lg border border-indigo-100">
            <h3 class="text-[10px] font-black text-indigo-800 uppercase mb-2">Activité Crédit (Recouvrement)</h3>
            <p class="text-[9px] text-gray-600 mb-2">Remboursements encaissés ce jour :</p>
            <div class="flex justify-between font-mono font-bold text-sm text-indigo-900 border-t border-indigo-200 pt-2">
                <span>{{ number_format_fr($cloture->total_remboursement_usd) }} $</span>
                <span>{{ number_format_fr($cloture->total_remboursement_cdf) }} FC</span>
            </div>
        </div>
        <div class="p-3 bg-purple-50 rounded-lg border border-purple-100">
            <h3 class="text-[10px] font-black text-purple-800 uppercase mb-2">Production Crédit (Octroi)</h3>
            <p class="text-[9px] text-gray-600 mb-2">Prêts décaissés ce jour :</p>
            <div class="flex justify-between font-mono font-bold text-sm text-purple-900 border-t border-purple-200 pt-2">
                <span>{{ number_format_fr($cloture->total_credit_usd) }} $</span>
                <span>{{ number_format_fr($cloture->total_credit_cdf) }} FC</span>
            </div>
        </div>
    </div>

    {{-- Observations --}}
    @if($cloture->observation_cloture)
    <div class="mb-10 p-3 bg-yellow-50 border border-yellow-200 rounded">
        <p class="text-[8px] font-black uppercase text-yellow-600 mb-1">Note de l'agent de clôture :</p>
        <p class="text-[10px] italic text-gray-700">{{ $cloture->observation_cloture }}</p>
    </div>
    @endif

    {{-- Zone des Signatures --}}
    <div class="mt-20">
        <div class="grid grid-cols-3 gap-4 text-center">
            <div>
                <p class="text-[10px] font-black uppercase underline">Le Caissier / Agent</p>
                <div class="mt-12 text-[9px] italic">{{ Auth::user()->name }}</div>
            </div>
            <div>
                <p class="text-[10px] font-black uppercase underline">Comptabilité / Audit</p>
            </div>
            <div>
                <p class="text-[10px] font-black uppercase underline">La Gérance</p>
            </div>
        </div>
    </div>

    <div class="mt-10 pt-4 border-t border-dotted border-gray-300 text-center">
        <p class="text-[7px] text-gray-400 uppercase tracking-widest">Fin du rapport de clôture - {{ config('app.name') }} - Système Automatisé</p>
    </div>
@endsection