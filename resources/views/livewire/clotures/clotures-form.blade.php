<div>
    @if (session()->has('error'))
        <div class="max-w-4xl mx-auto mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    @if($isOuverture)
        <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden mt-10">
            <div class="bg-blue-600 p-6 text-center">
                <h2 class="text-3xl font-bold text-white">Ouverture de Caisse</h2>
                <p class="text-blue-100 mt-2">Date du jour : {{ now()->format('d/m/Y') }}</p>
            </div>
            
            <div class="p-8">
                <h3 class="text-lg text-gray-700 font-semibold mb-6 border-b pb-2">Reports de la veille (Soldes Initiaux)</h3>
                
                <div class="grid grid-cols-2 gap-6 mb-8">
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <span class="block text-sm text-gray-500 font-medium uppercase">Report USD</span>
                        <span class="block text-2xl font-mono font-bold text-gray-900">${{ number_format($reportVeilleUsd, 2) }}</span>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <span class="block text-sm text-gray-500 font-medium uppercase">Report CDF</span>
                        <span class="block text-2xl font-mono font-bold text-gray-900">{{ number_format($reportVeilleCdf, 2) }} FC</span>
                    </div>
                </div>

                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-8">
                    <p class="text-sm text-yellow-700">
                        En ouvrant la journée, le système vous autorisera à enregistrer de nouvelles transactions (Dépôts, Retraits, Crédits, etc.). Assurez-vous que le montant physique dans le coffre correspond bien aux reports affichés ci-dessus.
                    </p>
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="{{ route('clotures.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">
                        Annuler
                    </a>
                    <button wire:click="validerOuverture" wire:loading.attr="disabled" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold shadow-lg flex items-center transition-colors">
                        <span wire:loading.remove>Ouvrir la journée</span>
                        <span wire:loading>Ouverture en cours...</span>
                    </button>
                </div>
            </div>
        </div>

    @else
        <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gray-50 border-b p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold text-gray-800">Clôture : {{ \Carbon\Carbon::parse($cloture->date_cloture)->format('d/m/Y') }}</h2>
                    <span class="text-blue-600 font-bold text-lg">{{ round(($step / $totalSteps) * 100) }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-500 ease-in-out" style="width: {{ ($step / $totalSteps) * 100 }}%"></div>
                </div>
                <p class="text-sm text-red-500 mt-3 font-medium flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    Confirmez-vous toutes ces transactions ? En cas d'erreur, veuillez corriger dans les modules respectifs car la clôture est définitive.
                </p>
            </div>

            <div class="p-8">
                @if($step == 1)
                    @include('livewire.clotures.partials.step-table', [
                        'title' => 'Dépôts Épargne', 
                        'data' => $this->depots, 
                        'relationName' => 'agentCollecteur', 
                        'relationLabel' => 'Agent Collecteur'
                    ])
                @endif

                @if($step == 2)
                    @include('livewire.clotures.partials.step-table', [
                        'title' => 'Retraits Épargne', 
                        'data' => $this->retraits, 
                        'relationName' => 'agentCollecteur', 
                        'relationLabel' => 'Agent Collecteur'
                    ])
                @endif

                @if($step == 3)
                    @include('livewire.clotures.partials.step-table', [
                        'title' => 'Déblocages Crédits', 
                        'data' => $this->credits, 
                        'relationName' => 'zone', 
                        'relationLabel' => 'Zone'
                    ])
                @endif

                @if($step == 4)
                    @include('livewire.clotures.partials.step-table', [
                        'title' => 'Remboursements Crédits', 
                        'data' => $this->remboursements, 
                        'relationName' => 'zone', 
                        'relationLabel' => 'Zone'
                    ])
                @endif

                @if($step == 5)
                    @include('livewire.clotures.partials.step-table', [
                        'title' => 'Revenus', 
                        'data' => $this->revenus, 
                        'relationName' => 'typeRevenu', 
                        'relationLabel' => 'Type de Revenu'
                    ])
                @endif

                @if($step == 6)
                    @include('livewire.clotures.partials.step-table', [
                        'title' => 'Dépenses', 
                        'data' => $this->depenses, 
                        'relationName' => 'typeDepense', 
                        'relationLabel' => 'Type de Dépense'
                    ])
                @endif

                @if($step == 7)
                    <div class="animate-fade-in-up">
                        <h3 class="text-xl font-bold text-gray-800 mb-6 border-b pb-2">Récapitulatif & Validation</h3>
                        
                        <div class="bg-blue-50 p-4 rounded-lg mb-6 flex justify-between items-center border border-blue-100">
                            <div>
                                <p class="text-sm text-blue-600 font-bold uppercase tracking-wider">Solde Théorique Actuel</p>
                                <p class="text-xs text-blue-500">Report de la veille inclus</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-mono font-bold text-blue-900">${{ number_format($cloture->solde_coffre_usd ?? 0, 2) }}</p>
                                <p class="text-lg font-mono font-bold text-blue-900">{{ number_format($cloture->solde_coffre_cdf ?? 0, 2) }} FC</p>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox" wire:model="ecart_constate" class="form-checkbox h-5 w-5 text-red-600 border-gray-300 rounded focus:ring-red-500">
                                <span class="text-gray-700 font-medium">Je constate un écart avec le solde physique en caisse</span>
                            </label>
                        </div>

                        @if($ecart_constate)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-red-50 p-6 rounded-lg border border-red-100 mb-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Solde Physique (USD)</label>
                                    <input type="number" step="0.01" wire:model.defer="physique_coffre_usd" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                    @error('physique_coffre_usd') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Solde Physique (CDF)</label>
                                    <input type="number" step="1" wire:model.defer="physique_coffre_cdf" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                    @error('physique_coffre_cdf') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-span-1 md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Observation (Justification de l'écart)</label>
                                    <textarea wire:model.defer="observation_cloture" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"></textarea>
                                    @error('observation_cloture') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <div class="bg-gray-50 border-t p-6 flex justify-between items-center">
                @if($step > 1)
                    <button wire:click="previousStep" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-white transition-colors">
                        Précédent
                    </button>
                @else
                    <div></div> 
                @endif

                @if($step < $totalSteps)
                    <button wire:click="nextStep" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold shadow transition-colors">
                        Suivant
                    </button>
                @else
                    <button wire:click.prevent="validerCloture" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-bold shadow-lg flex items-center transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        Verrouiller la Clôture
                    </button>
                @endif
            </div>
        </div>
    @endif
</div>