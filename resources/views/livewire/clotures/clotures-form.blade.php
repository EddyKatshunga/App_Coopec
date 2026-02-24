<div>
    {{-- Messages d'alerte --}}
    @if (session()->has('error'))
        <div class="max-w-4xl mx-auto mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg shadow-sm border border-red-200">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    {{-- ETAT : OUVERTURE DE JOURNEE --}}
    @if($isOuverture)
        <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden mt-10 border border-gray-100">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-8 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-white bg-opacity-20 rounded-full mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <h2 class="text-3xl font-bold text-white">Ouverture de Caisse</h2>
                <p class="text-blue-100 mt-2 font-medium">Session du {{ now()->translatedFormat('d F Y') }}</p>
            </div>
            
            <div class="p-8">
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-6 border-b pb-2">Reports de la veille (Soldes Initiaux)</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-blue-50 p-5 rounded-2xl border border-blue-100">
                        <span class="block text-xs text-blue-500 font-bold uppercase tracking-tight">Report USD</span>
                        <span class="block text-3xl font-mono font-black text-blue-900 mt-1">${{ number_format($reportVeilleUsd, 2, ',', ' ') }}</span>
                    </div>
                    <div class="bg-indigo-50 p-5 rounded-2xl border border-indigo-100">
                        <span class="block text-xs text-indigo-500 font-bold uppercase tracking-tight">Report CDF</span>
                        <span class="block text-3xl font-mono font-black text-indigo-900 mt-1">{{ number_format($reportVeilleCdf, 0, ',', ' ') }} <small class="text-lg">FC</small></span>
                    </div>
                </div>

                <div class="flex items-start bg-amber-50 border-l-4 border-amber-400 p-4 mb-8 rounded-r-lg">
                    <svg class="w-6 h-6 text-amber-500 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                    <p class="text-sm text-amber-800 leading-relaxed">
                        <strong>Attention :</strong> En validant l'ouverture, vous confirmez que le montant physique présent dans le coffre ce matin correspond exactement aux reports calculés ci-dessus.
                    </p>
                </div>

                <div class="flex items-center justify-between pt-4 border-t">
                    <a href="{{ route('clotures.index') }}" wire:navigate class="text-gray-500 hover:text-gray-700 font-semibold transition">
                        Retour à la liste
                    </a>
                    
                    <button wire:click="validerOuverture" 
                            wire:loading.attr="disabled" 
                            class="px-8 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-bold shadow-lg shadow-blue-200 flex items-center transition-all active:scale-95">
                        <span wire:loading.remove class="flex items-center">
                            Confirmer l'ouverture
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </span>
                        <span wire:loading class="flex items-center">
                            <svg class="animate-spin h-5 w-5 mr-3 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Traitement...
                        </span>
                    </button>
                </div>
            </div>
        </div>

    {{-- ETAT : CLÔTURE DE JOURNEE (WIZARD) --}}
    @else
        <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            {{-- Header Wizard --}}
            <div class="bg-gray-50 border-b p-6">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Clôture de Journée</h2>
                        <p class="text-sm text-gray-500">
                            Date comptable : 
                            {{-- Ajout d'une condition de sécurité pour éviter le null error --}}
                            @if($cloture)
                                {{ \Carbon\Carbon::parse($cloture->date_cloture)->translatedFormat('d F Y') }}
                            @else
                                {{ now()->translatedFormat('d F Y') }}
                            @endif
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="text-blue-600 font-black text-2xl">{{ round(($step / $totalSteps) * 100) }}%</span>
                        <p class="text-xs uppercase tracking-widest text-gray-400 font-bold">Progression</p>
                    </div>
                </div>
                
                {{-- Barre de progression --}}
                <div class="w-full bg-gray-200 rounded-full h-3 mb-2 shadow-inner">
                    <div class="bg-blue-600 h-3 rounded-full transition-all duration-700 ease-out shadow-lg" 
                         style="width: {{ ($step / $totalSteps) * 100 }}%"></div>
                </div>
                
                <div class="flex items-center text-amber-600 bg-amber-50 p-3 rounded-lg border border-amber-100 mt-4">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    <p class="text-xs font-semibold leading-tight">
                        Vérifiez chaque étape avec soin. Une fois la clôture verrouillée, les transactions de cette journée ne seront plus modifiables.
                    </p>
                </div>
            </div>

            {{-- Contenu du Wizard --}}
            <div class="p-8 min-h-[400px]">
                @if($step == 1)
                    @include('livewire.clotures.partials.step-table', ['title' => 'Dépôts Épargne', 'data' => $this->depots, 'relationName' => 'agent_collecteur', 'relationLabel' => 'Agent Collecteur'])
                @endif

                @if($step == 2)
                    @include('livewire.clotures.partials.step-table', ['title' => 'Retraits Épargne', 'data' => $this->retraits, 'relationName' => 'creator', 'relationLabel' => 'Effectué par'])
                @endif

                @if($step == 3)
                    @include('livewire.clotures.partials.step-table', ['title' => 'Déblocages Crédits', 'data' => $this->credits, 'relationName' => 'zone', 'relationLabel' => 'Zone'])
                @endif

                @if($step == 4)
                    @include('livewire.clotures.partials.step-table', ['title' => 'Remboursements Crédits', 'data' => $this->remboursements, 'relationName' => 'zone', 'relationLabel' => 'Zone'])
                @endif

                @if($step == 5)
                    @include('livewire.clotures.partials.step-table', ['title' => 'Revenus', 'data' => $this->revenus, 'relationName' => 'typeRevenu', 'relationLabel' => 'Type de Revenu'])
                @endif

                @if($step == 6)
                    @include('livewire.clotures.partials.step-table', ['title' => 'Dépenses', 'data' => $this->depenses, 'relationName' => 'typeDepense', 'relationLabel' => 'Type de Dépense'])
                @endif

                @if($step == 7)
                    <div class="animate-in fade-in slide-in-from-bottom-4 duration-500">
                        <h3 class="text-xl font-bold text-gray-800 mb-6 border-b pb-2">Récapitulatif & Validation Physique</h3>
                        
                        <div class="bg-blue-600 rounded-2xl p-6 mb-8 text-white shadow-xl shadow-blue-100 flex justify-between items-center">
                            <div>
                                <p class="text-blue-100 text-xs font-bold uppercase tracking-widest mb-1">Solde Théorique Final</p>
                                <p class="text-sm opacity-80 italic">Calculé selon les flux enregistrés</p>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-mono font-black">${{ number_format($cloture->solde_coffre_usd ?? 0, 2, ',', ' ') }}</p>
                                <p class="text-2xl font-mono font-black">{{ number_format($cloture->solde_coffre_cdf ?? 0, 0, ',', ' ') }} FC</p>
                            </div>
                        </div>

                        <div class="bg-gray-50 border border-gray-200 rounded-2xl p-6">
                            <label class="flex items-center space-x-4 cursor-pointer group">
                                <div class="relative">
                                    <input type="checkbox" wire:model="ecart_constate" class="peer hidden">
                                    <div class="w-6 h-6 border-2 border-gray-300 rounded peer-checked:bg-red-600 peer-checked:border-red-600 transition-all"></div>
                                    <svg class="absolute top-1 left-1 w-4 h-4 text-white opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <span class="text-gray-700 font-bold group-hover:text-red-600 transition-colors">Je constate un écart avec le solde physique présent en caisse</span>
                            </label>

                            @if($ecart_constate)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 pt-6 border-t border-gray-200 animate-in zoom-in-95 duration-300">
                                    <div>
                                        <label class="block text-xs font-black text-gray-500 uppercase mb-2">Solde Physique Réel (USD)</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-gray-400">$</span>
                                            <input type="number" step="0.01" wire:model.defer="physique_coffre_usd" class="pl-7 block w-full rounded-xl border-gray-300 bg-white shadow-sm focus:ring-red-500 focus:border-red-500 font-mono font-bold text-red-600">
                                        </div>
                                        @error('physique_coffre_usd') <span class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-black text-gray-500 uppercase mb-2">Solde Physique Réel (CDF)</label>
                                        <div class="relative">
                                            <input type="number" step="1" wire:model.defer="physique_coffre_cdf" class="block w-full rounded-xl border-gray-300 bg-white shadow-sm focus:ring-red-500 focus:border-red-500 font-mono font-bold text-red-600">
                                            <span class="absolute right-3 top-2 text-gray-400">FC</span>
                                        </div>
                                        @error('physique_coffre_cdf') <span class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-span-1 md:col-span-2">
                                        <label class="block text-xs font-black text-gray-500 uppercase mb-2">Justification de l'écart constaté</label>
                                        <textarea wire:model.defer="observation_cloture" rows="3" placeholder="Expliquez ici la raison de la différence..." class="block w-full rounded-xl border-gray-300 bg-white shadow-sm focus:ring-red-500 focus:border-red-500"></textarea>
                                        @error('observation_cloture') <span class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            {{-- Footer Wizard --}}
            <div class="bg-gray-100 border-t p-6 flex justify-between items-center">
                @if($step > 1)
                    <button wire:click="previousStep" class="px-6 py-2 bg-white border border-gray-300 rounded-xl text-gray-600 hover:bg-gray-50 font-bold transition-all active:scale-95">
                        Précédent
                    </button>
                @else
                    <a href="{{ route('clotures.index') }}" wire:navigate class="text-gray-400 hover:text-gray-600 text-sm font-bold">Abandonner la clôture</a>
                @endif

                @if($step < $totalSteps)
                    <button wire:click="nextStep" class="px-8 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-bold shadow-lg shadow-blue-100 transition-all active:scale-95">
                        Suivant
                    </button>
                @else
                    <button wire:click.prevent="validerCloture" 
                            onclick="return confirm('Êtes-vous sûr ? Cette action verrouillera définitivement la journée.')"
                            class="px-8 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 font-black shadow-xl shadow-red-200 flex items-center transition-all active:scale-95">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        Verrouiller la Clôture
                    </button>
                @endif
            </div>
        </div>
    @endif
</div>