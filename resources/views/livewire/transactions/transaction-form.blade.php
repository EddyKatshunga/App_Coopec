<div class="max-w-3xl mx-auto my-8">
    {{-- Notification de succès --}}
    @if (session()->has('success'))
        <div class="mb-6 flex items-center p-4 text-sm text-green-800 border border-green-200 rounded-xl bg-green-50">
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        
        {{-- Header --}}
        <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <div>
                <h2 class="text-xl font-bold text-gray-800 uppercase tracking-tight">
                    {{ $type_transaction }} - {{ $compte->numero_compte }}
                </h2>
                <p class="text-sm text-gray-500">{{ $compte->user->name }}</p>
            </div>
            <div class="flex flex-col items-end">
                <span class="text-xs font-bold text-gray-400 uppercase">Date</span>
                <span class="font-mono text-gray-700">
                    @if (auth()->user()->journee_ouverte)
                        {{ \Carbon\Carbon::parse(auth()->user()->journee_ouverte->date_cloture)->format('d/m/Y') }}
                    @else
                        Opération Impossible, pas de date disponible
                    @endif
                    
                </span>
            </div>
        </div>

        <div class="p-8 space-y-6">

            {{-- SECTION SOLDES --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="p-5 rounded-2xl bg-gray-50 border border-gray-200">
                    <span class="text-[10px] font-black text-gray-400 uppercase">Solde CDF</span>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-2xl font-black text-gray-900">{{ number_format($compte->solde_cdf, 2, ',', ' ') }}</span>
                        <span class="text-xs font-bold text-gray-500 uppercase">cdf</span>
                    </div>
                </div>
                <div class="p-5 rounded-2xl bg-gray-50 border border-gray-200">
                    <span class="text-[10px] font-black text-gray-400 uppercase">Solde USD</span>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-2xl font-black text-gray-900">{{ number_format($compte->solde_usd, 2, ',', ' ') }}</span>
                        <span class="text-xs font-bold text-gray-500 uppercase">usd</span>
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- SECTION MONTANT & DEVISE --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Montant</label>
                    <div class="relative">
                        <input
                            type="number"
                            step="0.01"
                            wire:model.live="montant"
                            class="w-full px-5 py-4 text-2xl font-black rounded-2xl bg-gray-50 border-2 {{ $errors->has('montant') ? 'border-red-500 bg-red-50' : 'border-transparent' }} focus:bg-white focus:border-blue-600 focus:ring-0 transition-all"
                            placeholder="0,00"
                        >
                        <span class="text-lg font-bold text-gray-400">{{ money_to_words($montant, $monnaie) }}</span>
                        <div class="absolute inset-y-0 right-0 pr-6 flex items-center pointer-events-none">
                            <span class="text-lg font-bold text-gray-400">{{ $monnaie }}</span>
                        </div>
                    </div>
                    @error('montant') <p class="mt-2 text-sm text-red-600 font-bold">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Devise</label>
                    <div class="flex p-1.5 bg-gray-100 rounded-2xl border border-gray-200">
                        <button type="button" wire:click="$set('monnaie', 'CDF')" class="flex-1 py-3 text-sm font-black rounded-xl transition-all {{ $monnaie === 'CDF' ? 'bg-white text-blue-600 shadow-md' : 'text-gray-500' }}">CDF</button>
                        <button type="button" wire:click="$set('monnaie', 'USD')" class="flex-1 py-3 text-sm font-black rounded-xl transition-all {{ $monnaie === 'USD' ? 'bg-white text-blue-600 shadow-md' : 'text-gray-500' }}">USD</button>
                    </div>
                </div>
            </div>

            {{-- AGENT (DEPOT UNIQUEMENT) --}}
            @if($type_transaction === 'DEPOT')
            <div class="animate-fade-in">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Agent Collecteur</label>
                <select wire:model="agent_collecteur_id" class="w-full px-5 py-4 rounded-2xl bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none appearance-none font-medium">
                    <option value="">-- Aucun --</option>
                    @foreach($agents as $agent)
                        <option value="{{ $agent->id }}">{{ $agent->user->name }}</option>
                    @endforeach
                </select>
                @error('agent_collecteur_id') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            @endif

            {{-- BOUTON VALIDER --}}
            <div class="pt-6">
                <button
                    wire:click="submit"
                    wire:loading.attr="disabled"
                    class="w-full py-5 rounded-2xl bg-gray-900 text-white text-lg font-bold shadow-2xl hover:bg-black transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span wire:loading.remove wire:target="submit">VALIDER L'OPÉRATION</span>
                    <span wire:loading wire:target="submit">TRAITEMENT EN COURS...</span>
                </button>
            </div>

        </div>
    </div>
</div>