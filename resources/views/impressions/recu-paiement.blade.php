@extends('impressions.layout')

@section('title', 'Reçu de Paiement - #REM-' . str_pad($remboursement->id, 6, '0', STR_PAD_LEFT))

@section('content')
    <div class="border-2 border-gray-800 rounded-lg overflow-hidden mb-6">
        {{-- En-tête du reçu --}}
        <div class="bg-gray-100 px-6 py-4 border-b-2 border-gray-800 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-black text-gray-800 uppercase italic tracking-widest">Reçu de Paiement</h2>
                <p class="text-sm font-mono mt-1">Référence : #REM-{{ str_pad($remboursement->id, 6, '0', STR_PAD_LEFT) }}</p>
            </div>
            <div class="text-right text-xs">
                <p class="font-bold uppercase">Agence : {{ $remboursement->agence->nom }}</p>
                <p>Zone : {{ $remboursement->zone->nom }}</p>
                <p class="mt-1">Date : <span class="font-bold">{{ $remboursement->date_paiement->isoFormat('dddd D MMMM YYYY') }}</span></p>
            </div>
        </div>

        <div class="p-6">
            {{-- Infos Bénéficiaire --}}
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <span class="text-[10px] uppercase text-gray-500 font-bold block">Membre (Bénéficiaire)</span>
                    <p class="text-lg font-bold text-gray-900 uppercase">{{ $remboursement->credit->user->name }}</p>
                </div>
                <div class="text-right">
                    <span class="text-[10px] uppercase text-gray-500 font-bold block">Mode de Paiement</span>
                    <p class="font-medium text-gray-900">{{ $remboursement->mode_paiement_label }}</p>
                </div>
            </div>

            {{-- Ventilation du paiement --}}
            <div class="border border-gray-300 rounded p-4 mb-6 bg-gray-50">
                <h4 class="text-xs font-bold text-gray-800 uppercase mb-3 border-b border-gray-300 pb-1">Détails de la Ventilation</h4>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="italic">Pénalités payées</span>
                        <span class="font-mono">{{ number_format($remboursement->montant_penalite_payee, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="italic">Intérêts payés</span>
                        <span class="font-mono">{{ number_format($remboursement->montant_interet_payee, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="italic">Capital remboursé</span>
                        <span class="font-mono">{{ number_format($remboursement->montant_capital_payee, 2) }}</span>
                    </div>
                    <div class="flex justify-between border-t-2 border-gray-800 pt-2 mt-2 text-lg font-black uppercase">
                        <span>Total Payé</span>
                        <span>{{ number_format($remboursement->montant, 2) }} {{ $remboursement->monnaie }}</span>
                    </div>
                </div>
            </div>

            {{-- Soldes (Avant / Après) --}}
            <div class="grid grid-cols-2 gap-6 text-sm mb-12">
                <div class="p-3 border border-gray-300 rounded text-center">
                    <span class="text-[10px] uppercase text-gray-500 block mb-1">Report avant paiement</span>
                    <span class="font-bold">{{ number_format($remboursement->report_avant, 2) }} {{ $remboursement->monnaie }}</span>
                </div>
                <div class="p-3 border-2 border-gray-800 rounded bg-gray-100 text-center">
                    <span class="text-[10px] uppercase font-bold block mb-1">Reste dû (Balance)</span>
                    <span class="font-black text-lg">{{ number_format($remboursement->reste_du_apres, 2) }} {{ $remboursement->monnaie }}</span>
                </div>
            </div>

            {{-- Signatures --}}
            <div class="grid grid-cols-2 gap-4 text-center text-xs uppercase font-bold text-gray-600">
                <div>
                    <p class="mb-16 underline">Signature du Membre</p>
                </div>
                <div>
                    <p class="mb-16 underline">Caisse : {{ $remboursement->agent->name }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection