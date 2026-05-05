<div class="max-w-3xl mx-auto my-8">

    @php
        $isDepot = strtolower($type_transaction) === 'depot'; // À ajuster selon vos valeurs exactes
        $themeColor = $isDepot ? 'emerald' : 'rose';
        $focusColor = $isDepot ? 'emerald-600' : 'rose-600';
    @endphp

    {{-- Notification de succès --}}
    @if (session()->has('success'))
        <div class="mb-6 flex items-center p-4 text-sm text-green-800 border border-green-200 rounded-xl bg-green-50 animate-fade-in">
            <x-heroicon-s-check-circle class="w-5 h-5 mr-2"/>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden transition-all duration-500">
        
        {{-- Header Dynamique --}}
        <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between {{ $isDepot ? 'bg-emerald-50/50' : 'bg-rose-50/50' }}">
            <div>
                <div class="flex items-center gap-2">
                    <span class="p-1.5 rounded-lg {{ $isDepot ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600' }}">
                        @if($isDepot) <x-heroicon-s-arrow-down-left class="w-5 h-5"/> @else <x-heroicon-s-arrow-up-right class="w-5 h-5"/> @endif
                    </span>
                    <h2 class="text-xl font-black {{ $isDepot ? 'text-emerald-900' : 'text-rose-900' }} uppercase tracking-tight">
                        {{ $type_transaction }} - {{ $compte->numero_compte }}
                    </h2>
                </div>
                <p class="text-sm text-gray-500 mt-1 ml-9 font-medium">{{ $compte->user->name }}</p>
            </div>
            <div class="flex flex-col items-end">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Date comptable</span>
                <span class="font-mono font-bold text-gray-700">
                    @if (auth()->user()->journee_ouverte)
                        {{ \Carbon\Carbon::parse(auth()->user()->journee_ouverte->date_cloture)->format('d/m/Y') }}
                    @else
                        <span class="text-rose-500 italic text-xs">Journée fermée</span>
                    @endif
                </span>
            </div>
        </div>

        <form 
            wire:submit="submit" 
            wire:confirm="Confirmez-vous ce {{ strtolower($type_transaction) }} de {{ number_format($montant ?? 0, 2, ',', ' ') }} {{ $monnaie }} ?"
            class="p-8 space-y-6"
        >

            {{-- SECTION SOLDES --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="p-5 rounded-2xl bg-gray-50 border border-gray-100">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Solde actuel CDF</span>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-2xl font-black text-gray-900">{{ number_format($compte->solde_cdf, 2, ',', ' ') }}</span>
                        <span class="text-xs font-bold text-gray-400">CDF</span>
                    </div>
                </div>
                <div class="p-5 rounded-2xl bg-gray-50 border border-gray-100">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Solde actuel USD</span>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-2xl font-black text-gray-900">{{ number_format($compte->solde_usd, 2, ',', ' ') }}</span>
                        <span class="text-xs font-bold text-gray-400">USD</span>
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- SECTION MONTANT & DEVISE --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
                <div class="md:col-span-2">
                    <label class="block text-xs font-black uppercase text-gray-400 tracking-widest mb-2 ml-1">Montant de l'opération</label>
                    <div class="relative group">
                        <input
                            type="number"
                            step="0.01"
                            wire:model.live="montant"
                            class="w-full px-6 py-5 text-3xl font-black rounded-2xl bg-gray-50 border-2 transition-all outline-none
                            {{ $errors->has('montant') ? 'border-rose-500 bg-rose-50' : 'border-transparent' }} 
                            {{ $isDepot ? 'focus:border-emerald-500 focus:bg-white' : 'focus:border-rose-500 focus:bg-white' }}"
                            placeholder="0,00"
                        >
                        <div class="absolute inset-y-0 right-0 pr-6 flex items-center pointer-events-none">
                            <span class="text-xl font-black {{ $isDepot ? 'text-emerald-200 group-focus-within:text-emerald-500' : 'text-rose-200 group-focus-within:text-rose-500' }} transition-colors">
                                {{ $monnaie }}
                            </span>
                        </div>
                    </div>
                    
                    @if($montant > 0)
                        <div class="mt-2 px-2">
                            <span class="text-xs font-bold italic {{ $isDepot ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ money_to_words($montant, $monnaie) }}
                            </span>
                        </div>
                    @endif

                    @error('montant') <p class="mt-2 text-sm text-rose-600 font-bold flex items-center gap-1"> {{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-black uppercase text-gray-400 tracking-widest mb-2 ml-1">Devise</label>
                    <div class="flex p-1.5 bg-gray-100 rounded-2xl border border-gray-200">
                        <button type="button" wire:click="$set('monnaie', 'CDF')" 
                            class="flex-1 py-4 text-sm font-black rounded-xl transition-all {{ $monnaie === 'CDF' ? 'bg-white shadow-sm ring-1 ring-black/5 ' . ($isDepot ? 'text-emerald-600' : 'text-rose-600') : 'text-gray-400' }}">
                            CDF
                        </button>
                        <button type="button" wire:click="$set('monnaie', 'USD')" 
                            class="flex-1 py-4 text-sm font-black rounded-xl transition-all {{ $monnaie === 'USD' ? 'bg-white shadow-sm ring-1 ring-black/5 ' . ($isDepot ? 'text-emerald-600' : 'text-rose-600') : 'text-gray-400' }}">
                            USD
                        </button>
                    </div>
                </div>
            </div>

            {{-- BOUTON VALIDER DYNAMIQUE --}}
            <div class="pt-6">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="w-full py-5 rounded-2xl text-white text-lg font-black shadow-xl transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed
                    {{ $isDepot ? 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-200' : 'bg-rose-600 hover:bg-rose-700 shadow-rose-200' }}"
                >
                    <span wire:loading.remove wire:target="submit">VALIDER LE {{ strtoupper($type_transaction) }}</span>
                    <span wire:loading wire:target="submit" class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        TRAITEMENT...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>