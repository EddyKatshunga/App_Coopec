<div class="max-w-6xl mx-auto p-6 space-y-8" wire:init="rafraichirEtat">

    {{-- Messages de succès --}}
    @if (session()->has('success'))
        <div class="p-4 mb-4 text-sm text-green-800 rounded-2xl bg-green-50 border border-green-100">
            {{ session('success') }}
        </div>
    @endif

    {{-- ================= HEADER ================= --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Crédit #{{ $credit->numero_credit }}</h1>
                @include('livewire.credits.partials.status-badge')
            </div>
            <p class="text-sm text-gray-500 font-medium mt-1">
                Membre : <span class="text-blue-600 font-bold">{{ $credit->membre->nom ?? $credit->user->name }}</span>
            </p>
        </div>

        <div class="flex items-center gap-3">
            @if(!str_contains($credit->statut, 'termine'))
                @can('can.level5') 
                    <button wire:click="toggleClotureModal" class="flex items-center gap-2 px-4 py-2.5 bg-amber-50 text-amber-700 border border-amber-200 rounded-xl font-bold hover:bg-amber-100 transition shadow-sm text-sm">
                        <x-heroicon-o-lock-closed class="w-4 h-4"/>
                        Forcer Clôture
                    </button>
                @endcan
            @endif

            @can('can.level5') 
                @if($credit->canBeDeleted())
                    <button wire:click="deleteRecord('App\\Models\\Credit', {{ $credit->id }})" wire:confirm="Supprimer définitivement ce dossier ?" class="p-2.5 text-red-500 hover:bg-red-50 rounded-xl transition">
                        <x-heroicon-o-trash class="w-5 h-5"/>
                    </button>
                @endif
            @endcan
        </div>
    </div>

    {{-- Note de négociation --}}
    @if($credit->negocie)
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 flex items-start gap-3">
            <x-heroicon-s-information-circle class="w-5 h-5 text-amber-600 mt-0.5"/>
            <div>
                <p class="text-sm text-amber-900 font-bold">Crédit clôturé par négociation</p>
                <p class="text-xs text-amber-800 italic">"{{ $credit->note_negociation }}"</p>
            </div>
        </div>
    @endif

    {{-- ================= RÉSUMÉ FINANCIER ================= --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Échéance</p>
            <p class="text-lg font-bold text-gray-900">{{ $credit->date_fin_prevue->format('d/m/Y') }}</p>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Attendu</p>
            <p class="text-lg font-bold text-gray-900">{{ number_format_fr($credit->total) }} <small>{{ $credit->monnaie }}</small></p>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-[10px] font-black text-red-400 uppercase tracking-widest mb-1">Pénalités</p>
            <p class="text-lg font-bold text-red-600">{{ number_format_fr($penaliteCourante) }}</p>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 bg-green-50/30">
            <p class="text-[10px] font-black text-green-500 uppercase tracking-widest mb-1">Remboursé</p>
            <p class="text-lg font-bold text-green-600">{{ number_format_fr($credit->total_rembourse) }}</p>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border-2 border-orange-200">
            <p class="text-[10px] font-black text-orange-400 uppercase tracking-widest mb-1">Net à Payer</p>
            <p class="text-xl font-black text-orange-600">{{ number_format_fr($resteDu, 0) }}</p>
        </div>
    </div>

    {{-- ================= TABLEAU ================= --}}
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50/50">
                    <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                        <th class="py-4 px-6">Date</th>
                        <th class="py-4 px-2 text-right">Montant</th>
                        <th class="py-4 px-2 text-right">Penalités</th>
                        <th class="py-4 px-2 text-right">Intérêt</th>
                        <th class="py-4 px-2 text-right">Capital</th>
                        <th class="py-4 px-6 text-right">Reste Dû</th>
                        <th class="py-4 px-2 text-right">Reste Penalités</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($remboursements as $remb)
                        <tr class="hover:bg-gray-50/50 transition font-medium">
                            <td class="py-4 px-6 text-gray-900">{{ $remb->date_paiement->format('d/m/Y') }}</td>
                            <td class="py-4 px-2 text-right font-bold">{{ number_format_fr($remb->montant) }}</td>
                            <td class="py-4 px-2 text-right text-red-700">{{ number_format_fr($remb->montant_penalite_payee) }}</td>
                            <td class="py-4 px-2 text-right text-green-600">{{ number_format_fr($remb->montant_interet_payee) }}</td>
                            <td class="py-4 px-2 text-right text-green-600">{{ number_format_fr($remb->montant_capital_payee) }}</td>
                            <td class="py-4 px-6 text-right font-mono font-bold text-blue-700">{{ number_format_fr($remb->reste_du_apres) }}</td>
                            <td class="py-4 px-6 text-right font-mono font-bold text-red-700">{{ number_format_fr($remb->reste_penalite) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ================= MODAL DE CLOTURE (PURE LIVEWIRE) ================= --}}
    @if($showClotureModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                {{-- Overlay --}}
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" wire:click="toggleClotureModal"></div>

                {{-- Modal --}}
                <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-md p-8 relative z-10 border border-gray-100">
                    <div class="w-16 h-16 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center mb-6 mx-auto">
                        <x-heroicon-s-exclamation-triangle class="w-8 h-8"/>
                    </div>

                    <h3 class="text-xl font-black text-gray-900 mb-2 text-center">Clôture exceptionnelle</h3>
                    <p class="text-sm text-gray-500 text-center mb-6">Cette opération annulera définitivement le solde restant et les pénalités.</p>

                    <div class="space-y-4">
                        <div>
                            <label class="text-[10px] font-black uppercase text-gray-400 mb-2 block">Motif de clôture</label>
                            <textarea wire:model="noteCloture" rows="3" class="w-full rounded-2xl border-gray-100 bg-gray-50 focus:bg-white text-sm font-medium"></textarea>
                            @error('noteCloture') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="bg-red-50 border border-red-200 rounded-xl p-3">
                            <label class="flex items-center gap-3 text-sm font-bold text-red-700 cursor-pointer">
                                <input type="checkbox" wire:model.live="confirmCloture" class="rounded border-red-300 text-red-600 focus:ring-red-500">
                                Je confirme la clôture
                            </label>
                            @error('confirmCloture') <p class="text-[10px] text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col gap-3 pt-2">
                            <button 
                                wire:click="validerClotureForcee" 
                                wire:loading.attr="disabled"
                                @if(!$confirmCloture) disabled @endif
                                class="w-full py-4 bg-amber-600 text-white font-black rounded-2xl hover:bg-amber-700 disabled:opacity-40 shadow-lg transition">
                                <span wire:loading.remove>Confirmer la clôture</span>
                                <span wire:loading>Action en cours...</span>
                            </button>
                            <button wire:click="toggleClotureModal" class="w-full py-4 bg-white text-gray-500 font-bold rounded-2xl hover:bg-gray-50 transition">
                                Annuler
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>