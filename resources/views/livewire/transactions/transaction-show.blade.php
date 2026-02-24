<div class="max-w-5xl mx-auto p-4 sm:p-6">
    {{-- Contrôles d'interface (cachés à l'impression) --}}
    <div class="flex justify-between items-center mb-6 no-print bg-gray-50 p-4 rounded-2xl border border-gray-100">
        <a href="{{ route('epargne.transactions.index') }}" wire:navigate class="flex items-center gap-2 text-gray-600 hover:text-black font-bold text-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Retour
        </a>
        <button onclick="printDocument()" class="bg-gray-900 text-white px-6 py-2.5 rounded-xl font-black text-xs shadow-xl hover:bg-black transition transform active:scale-95 flex items-center gap-3">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            IMPRIMER LE REÇU (A5)
        </button>
    </div>

    {{-- APPEL AU LAYOUT UNIFORME --}}
    <x-print-layout 
        title="REÇU DE {{ $transaction->type_transaction }}" 
        reference="REF #{{ $transaction->id }}"
        size="A5"
        filename="Transaction_{{ $transaction->id }}_{{ Str::slug($transaction->compte->user->name) }}"
    >
        {{-- CORPS DU REÇU - Espacements très réduits --}}
        <div class="grid grid-cols-3 gap-2 mb-3">
            <div class="col-span-2 space-y-1">
                <div>
                    <h3 class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Titulaire</h3>
                    <p class="text-base font-bold text-gray-900 leading-tight">{{ $transaction->compte->user->name }}</p>
                    <p class="text-xs font-mono text-blue-700 font-bold">{{ $transaction->compte->numero_compte }}</p>
                </div>
                <div>
                    <h3 class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Détails</h3>
                    <p class="text-[10px] text-gray-600 font-medium">{{ $transaction->libelle ?? 'Opération standard' }}</p>
                    <p class="text-[7px] text-gray-400 italic">Agent : {{ $transaction->agent_collecteur->user->name }}</p>
                </div>
            </div>
            <div class="flex flex-col items-end justify-start">
                <div class="bg-white p-0.5 border border-gray-100 rounded mb-0.5">
                    {!! QrCode::size(60)->margin(0)->generate(route('transaction.show', $transaction->id)) !!}
                </div>
                <span class="text-[6px] font-bold text-gray-400 uppercase">Scan</span>
            </div>
        </div>

        {{-- SECTION FINANCIÈRE --}}
        <div class="border-y-2 border-gray-900 py-2 mb-2">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-[7px] font-black text-gray-400 uppercase">Montant</p>
                    <p class="text-3xl font-black leading-none {{ $transaction->type_transaction === 'DEPOT' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $transaction->type_transaction === 'DEPOT' ? '+' : '-' }} {{ number_format($transaction->montant, 2, ',', ' ') }}
                        <span class="text-sm">{{ $transaction->monnaie }}</span>
                    </p>
                </div>
                <div class="text-right space-y-0.5">
                    <p class="text-[8px] font-bold text-gray-500">Préc. : {{ number_format($transaction->solde_avant, 2) }}</p>
                    <p class="text-[10px] font-black bg-gray-100 px-2 py-0.5 rounded">NOUVEAU : {{ number_format($transaction->solde_apres, 2) }} {{ $transaction->monnaie }}</p>
                </div>
            </div>
        </div>

        {{-- SIGNATURES --}}
        <div class="grid grid-cols-2 gap-6 pt-2">
            <div class="border-t border-gray-900 pt-1 text-center">
                <p class="text-[8px] font-black uppercase">Le Client</p>
            </div>
            <div class="border-t border-gray-900 pt-1 text-center">
                <p class="text-[8px] font-black uppercase">Le Caissier (Cachet)</p>
            </div>
        </div>
    </x-print-layout>
</div>