<div class="max-w-4xl mx-auto py-6 px-4 sm:py-10">
    <div class="bg-white shadow-xl rounded-[2rem] overflow-hidden border border-emerald-100">
        
        {{-- Header avec identité visuelle "Entrée" --}}
        <div class="px-6 py-8 sm:px-10 bg-emerald-50/50 border-b border-emerald-100">
            <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                <div class="space-y-1">
                    <h2 class="text-2xl font-black text-slate-800 tracking-tight text-emerald-700">Entrée de Fonds</h2>
                    <p class="text-sm text-slate-500 font-medium">Enregistrement d'un nouveau flux entrant au coffre.</p>
                </div>

                @if (auth()->user()->journee_ouverte)
                    <div class="inline-flex items-center px-4 py-2 bg-white border border-emerald-200 rounded-2xl shadow-sm">
                        <span class="mr-2">📅</span>
                        <span class="text-xs sm:text-sm font-bold text-emerald-700 uppercase tracking-wider">
                            {{ auth()->user()->journee_ouverte->date_cloture->isoFormat('dddd D MMMM YYYY') }}
                        </span>
                    </div>
                @else
                    <div class="inline-flex items-center px-4 py-2 bg-rose-50 border border-rose-100 rounded-2xl animate-pulse">
                        <span class="text-rose-600 font-black text-[10px] uppercase tracking-widest">⚠️ Journée non ouverte</span>
                    </div>
                @endif
            </div>
        </div>

        <form wire:submit.prevent="save" class="p-6 sm:p-10 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

                {{-- Source du Revenu --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-[0.15em] mb-2">Source / Type de revenu (*)</label>
                    <select wire:model="types_revenu_id" 
                            class="w-full h-12 px-4 bg-slate-50 border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-medium transition-all @error('types_revenu_id') border-rose-500 @enderror">
                        <option value="">-- Sélectionner la source --</option>
                        @foreach($types as $type)
                            <option value="{{ $type->id }}">{{ $type->nom }}</option>
                        @endforeach
                    </select>
                    @error('types_revenu_id') <span class="text-rose-500 text-[10px] font-bold mt-1 uppercase">{{ $message }}</span> @enderror
                </div>

                {{-- Libellé --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-[0.15em] mb-2">Libellé du revenu (*)</label>
                    <input type="text" wire:model="libelle" 
                           class="w-full h-12 px-4 bg-slate-50 border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 transition-all font-medium @error('libelle') border-rose-500 @enderror" 
                           placeholder="Ex: Versement cotisation annuelle / Vente service">
                    @error('libelle') <span class="text-rose-500 text-[10px] font-bold mt-1 uppercase">{{ $message }}</span> @enderror
                </div>

                {{-- Montant --}}
                <div class="space-y-2">
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-[0.15em] mb-2">Montant à encaisser (*)</label>
                    <div class="relative">
                        <input type="number" step="0.01" wire:model.live="montant" 
                               class="w-full h-12 pl-4 pr-12 bg-slate-50 border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 font-black text-lg text-emerald-700 @error('montant') border-rose-500 @enderror" 
                               placeholder="0.00">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-400 font-bold text-xs">
                            {{ $monnaie }}
                        </div>
                    </div>
                    @if($montant > 0)
                        <div class="p-3 bg-slate-50 rounded-lg border border-slate-100 border-l-4 border-l-emerald-500">
                            <p class="text-[10px] leading-tight text-slate-600 font-bold uppercase italic">
                                {{ money_to_words($montant, $monnaie) }}
                            </p>
                        </div>
                    @endif
                    @error('montant') <span class="text-rose-500 text-[10px] font-bold mt-1 uppercase">{{ $message }}</span> @enderror
                </div>

                {{-- Devise --}}
                <div>
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-[0.15em] mb-2">Devise (*)</label>
                    <select wire:model.live="monnaie" 
                            class="w-full h-12 px-4 bg-slate-50 border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 font-bold">
                        <option value="USD">Dollar Américain (USD)</option>
                        <option value="CDF">Franc Congolais (CDF)</option>
                    </select>
                </div>

                {{-- Référence --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-[0.15em] mb-2">Référence (Bordereau, N° Chèque, Reçu)</label>
                    <input type="text" wire:model="reference" 
                           class="w-full h-12 px-4 bg-slate-50 border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 font-medium" 
                           placeholder="Ex: BORD-2024-001">
                </div>

                {{-- Description --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-[0.15em] mb-2">Description / Observations</label>
                    <textarea wire:model="description" rows="3" 
                              class="w-full p-4 bg-slate-50 border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 transition-all font-medium" 
                              placeholder="Notes additionnelles sur l'origine des fonds..."></textarea>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-col-reverse sm:flex-row justify-end items-center gap-4 border-t border-slate-100 pt-8">
                <a href="{{ route('revenus.index') }}" wire:navigate
                   class="w-full sm:w-auto px-8 py-4 text-center text-sm font-black text-slate-400 uppercase tracking-widest hover:text-slate-600 transition-all">
                    Annuler
                </a>
                
                <button type="submit" 
                        wire:loading.attr="disabled"
                        @if(!auth()->user()->journee_ouverte) disabled @endif
                        class="w-full sm:w-auto inline-flex items-center justify-center px-10 py-4 bg-emerald-600 text-white rounded-2xl font-black uppercase text-[11px] tracking-[0.2em] shadow-xl shadow-emerald-100 hover:bg-emerald-700 active:scale-95 transition-all disabled:opacity-50">
                    
                    <span wire:loading wire:target="save" class="mr-3">
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                    
                    Confirmer l'Entrée
                </button>
            </div>
        </form>
    </div>
</div>