<div class="max-w-3xl mx-auto py-8 px-4">
    {{-- BARRE D'ACTIONS : Navigation et Impression --}}
    <div class="flex justify-between items-center mb-6 no-print">
        <a href="{{ route('credit.show', $remboursement->credit) }}" wire:navigate class="text-gray-500 hover:text-gray-700 text-sm flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Retour au crédit
        </a>
        
        {{-- APPEL DE LA ROUTE DÉDIÉE --}}
        <a href="{{ route('remboursements.print', $remboursement) }}" 
           target="_blank" 
           class="bg-gray-800 hover:bg-black text-white px-4 py-2 rounded-md text-sm shadow flex items-center transition-all">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            Générer le reçu officiel (PDF)
        </a>
    </div>

    {{-- APERÇU ÉCRAN --}}
    <div class="bg-white shadow-xl rounded-lg overflow-hidden border border-gray-200">
        <div class="bg-gray-50 px-8 py-6 border-b flex justify-between">
            <div>
                <h2 class="text-2xl font-black text-gray-800 uppercase italic">REÇU DE PAIEMENT</h2>
                <h5 class="text-sm font-black text-gray-800 uppercase italic">{{ ($remboursement->credit->numero_credit) }}</h5>
                <p class="text-sm text-gray-500">Référence : #REM-{{ str_pad($remboursement->id, 6, '0', STR_PAD_LEFT) }}</p>
            </div>
            <div class="text-right text-sm">
                <p class="font-bold text-gray-700">Agence : {{ $remboursement->agence->nom }}</p>
                <p class="text-gray-500">Zone : {{ $remboursement->zone->nom }}</p>
                <p class="text-gray-500">Jour & Date : {{ $remboursement->date_paiement->isoFormat('dddd D MMMM YYYY') }}</p>
            </div>
        </div>

        <div class="p-8 space-y-8">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="text-xs uppercase text-gray-400 font-bold block">CLIENT (Bénéficiaire)</span>
                    <p class="text-lg font-bold text-gray-800">{{ $remboursement->credit->user->name }}</p>
                </div>
                <div class="text-right">
                    <span class="text-xs uppercase text-gray-400 font-bold block">Mode de Paiement</span>
                    <p class="font-medium text-gray-700">{{ $remboursement->mode_paiement_label }}</p>
                </div>
            </div>

            <hr class="border-gray-100">

            <div class="bg-blue-50 rounded-lg p-6">
                <h4 class="text-sm font-bold text-blue-800 uppercase mb-4">Détails de la Ventilation</h4>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 italic">Pénalités payées</span>
                        <span class="font-mono text-gray-800">{{ number_format($remboursement->montant_penalite_payee, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 italic">Intérêts payés</span>
                        <span class="font-mono text-gray-800">{{ number_format($remboursement->montant_interet_payee, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 italic">Capital remboursé</span>
                        <span class="font-mono text-gray-800">{{ number_format($remboursement->montant_capital_payee, 2) }}</span>
                    </div>
                    <div class="flex justify-between border-t border-blue-200 pt-3 text-lg font-black text-blue-900">
                        <span>TOTAL PAYÉ</span>
                        <span>{{ number_format($remboursement->montant, 2) }} {{ $remboursement->monnaie }}</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-8 text-sm">
                <div class="p-4 border rounded-md">
                    <span class="text-gray-500 block">Report avant paiement</span>
                    <span class="font-bold text-gray-700">{{ number_format($remboursement->report_avant, 2) }}</span>
                </div>
                <div class="p-4 border rounded-md bg-gray-800 text-white">
                    <span class="text-gray-400 block italic">Reste dû (Balance)</span>
                    <span class="font-bold text-xl">{{ number_format($remboursement->reste_du_apres, 2) }}</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 pt-12 text-center text-[10px] uppercase font-bold text-gray-400">
                <div>
                    <p class="mb-12 text-gray-300">Signature du Membre</p>
                    <div class="border-t border-gray-200 mx-8"></div>
                </div>
                <div>
                    <p class="mb-12 underline text-gray-400">Caisse : {{ $remboursement->agent->name }}</p>
                    <div class="border-t border-gray-200 mx-8"></div>
                </div>
            </div>
        </div>
    </div>
</div>