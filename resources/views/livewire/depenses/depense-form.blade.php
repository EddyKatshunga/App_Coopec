<div class="max-w-4xl mx-auto py-8">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-800">Enregistrer une nouvelle dépense</h2>
            <p class="text-sm text-gray-600">Les fonds seront déduits du coffre dès la validation.</p>
             @if (auth()->user()->journee_ouverte)
                <h3 class="text-white font-bold text-lg"> 📅 Date d'opération : {{ \Carbon\Carbon::parse(auth()->user()->journee_ouverte->date_cloture)->format('d/m/Y') }}</h3>
            @else
                <h5>Opération Impossible, pas de date disponible</h5>
            @endif
        </div>

        <form wire:submit.prevent="save" class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Catégorie</label>
                    <select wire:model="types_depense_id" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('types_depense_id') border-red-500 @enderror">
                        <option value="">-- Sélectionner la catégorie de la dépense --</option>
                        @foreach($types as $type)
                            <option value="{{ $type->id }}">{{ $type->nom }}</option>
                        @endforeach
                    </select>
                    @error('types_depense_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Libellé de la dépense</label>
                    <input type="text" wire:model="libelle" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('libelle') border-red-500 @enderror" 
                           placeholder="Ex: Achat fournitures bureau">
                    @error('libelle') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Montant</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <input type="number" step="0.01" wire:model="montant" 
                               class="block w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('montant') border-red-500 @enderror" 
                               placeholder="0.00">
                    </div>
                    @error('montant') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Devise</label>
                    <select wire:model="monnaie" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="USD">USD ($)</option>
                        <option value="CDF">CDF (FC)</option>
                    </select>
                    @error('monnaie') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Bénéficiaire (Agent)</label>
                    <select wire:model="beneficiaire_id" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Aucun ou externe --</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}">{{ $agent->nom }}</option>
                        @endforeach
                    </select>
                    @error('beneficiaire_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="col-span-1">
                    <label class="block text-sm font-medium text-gray-700">Référence Pièce</label>
                    <input type="text" wire:model="reference" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="N° de facture ou reçu">
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Description / Justification</label>
                    <textarea wire:model="description" rows="3" 
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                              placeholder="Détails supplémentaires..."></textarea>
                </div>
            </div>

            <div class="flex justify-end space-x-3 border-t pt-6">
                <a href="{{ route('depenses.index') }}" wire:navigate
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Annuler
                </a>
                <button type="submit" 
                        wire:loading.attr="disabled"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    
                    <span wire:loading wire:target="save" class="mr-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                    
                    Enregistrer la dépense
                </button>
            </div>
        </form>
    </div>
</div>