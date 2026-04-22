@extends('impressions.layout') {{-- Utilise votre layout document SYSCO --}}

@section('title', 'Dossier Crédit ' . $credit->numero_credit)

@section('content')
    <div class="space-y-6">
        {{-- Titre du Document --}}
        <div class="text-center border-b-2 border-gray-800 pb-2 mb-6">
            <h1 class="text-2xl font-black uppercase">Fiche de Suivi de Crédit</h1>
            <p class="text-sm font-bold">N° Dossier : {{ $credit->numero_credit }}</p>
        </div>

        {{-- Infos Membre & Garant --}}
        <div class="grid grid-cols-2 gap-8">
            <div class="space-y-1">
                <h3 class="text-xs font-black uppercase text-gray-400 border-b">Bénéficiaire</h3>
                <p class="text-sm font-bold">{{ $credit->membre->nom ?? $credit->user->name }}</p>
                <p class="text-xs text-gray-600">ID : {{ $credit->membre->code_membre ?? 'N/A' }}</p>
                <p class="text-xs text-gray-600">Zone : {{ $credit->zone->nom ?? 'Non définie' }}</p>
            </div>
            <div class="space-y-1">
                <h3 class="text-xs font-black uppercase text-gray-400 border-b">Garantie / Garant</h3>
                <p class="text-sm font-bold">{{ $credit->garant_nom }}</p>
                <p class="text-xs text-gray-600">Tél : {{ $credit->garant_telephone }}</p>
                <p class="text-xs text-gray-600">Adresse : {{ $credit->garant_adresse }}</p>
            </div>
        </div>

        {{-- Détails Financiers --}}
        <div class="mt-8">
            <h3 class="text-xs font-black uppercase text-gray-400 mb-2">Paramètres du Crédit</h3>
            <table class="w-full text-xs table-custom">
                <tr class="bg-gray-50">
                    <th class="text-left">Capital</th>
                    <th class="text-left">Intérêt</th>
                    <th class="text-left">Total dû</th>
                    <th class="text-left">Durée</th>
                    <th class="text-left">Échéance Finale</th>
                </tr>
                <tr>
                    <td class="font-bold">{{ number_format_fr($credit->capital) }} {{ $credit->monnaie }}</td>
                    <td>{{ number_format_fr($credit->interet) }} {{ $credit->monnaie }}</td>
                    <td class="font-bold text-blue-800">{{ number_format_fr($credit->total) }} {{ $credit->monnaie }}</td>
                    <td>{{ $credit->duree }} ({{ $credit->unite_temps }})</td>
                    <td class="font-bold text-red-600">{{ $credit->date_fin_prevue->format('d/m/Y') }}</td>
                </tr>
            </table>
        </div>

        {{-- Historique des Remboursements --}}
        @if($credit->remboursements->count() > 0)
            <div class="mt-8">
                <h3 class="text-xs font-black uppercase text-gray-400 mb-2">Historique des Paiements Effectués</h3>
                <table class="w-full text-[10px] table-custom">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th>Date</th>
                            <th class="text-right">Montant Payé</th>
                            <th class="text-right">Ventilation Int./Cap.</th>
                            <th class="text-right">Pénalités</th>
                            <th class="text-right">Solde Restant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($credit->remboursements as $remb)
                            <tr>
                                <td>{{ $remb->date_paiement->format('d/m/Y') }}</td>
                                <td class="font-bold text-right">{{ number_format_fr($remb->montant) }}</td>
                                <td class="text-right">
                                    {{ number_format_fr($remb->montant_capital_payee) }} / {{ number_format_fr($remb->montant_interet_payee) }}
                                </td>
                                <td class="text-right text-red-600">{{ number_format_fr($remb->montant_penalite_payee) }}</td>
                                <td class="font-bold text-right">{{ number_format_fr($remb->reste_du_apres) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-100 font-bold">
                        <tr>
                            <td class="text-right">TOTAL</td>
                            <td class="text-right">{{ number_format_fr($credit->total_rembourse) }}</td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="mt-8 p-4 border-2 border-dashed border-gray-200 text-center">
                <p class="text-xs text-gray-400 uppercase font-bold">Aucun remboursement effectué à ce jour</p>
            </div>
        @endif

        {{-- Résumé Final --}}
        <div class="mt-8 flex justify-end">
            <div class="w-1/2 space-y-2 border-t-2 border-gray-800 pt-2">
                <div class="flex justify-between text-xs">
                    <span>Total Remboursé :</span>
                    <span class="font-bold">{{ number_format_fr($credit->total_rembourse) }}</span>
                </div>
                <div class="flex justify-between text-sm font-black border-t pt-1">
                    <span>RESTE À PAYER :</span>
                    <span class="text-orange-600">{{ number_format_fr($credit->reste_du) }} {{ $credit->monnaie }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection