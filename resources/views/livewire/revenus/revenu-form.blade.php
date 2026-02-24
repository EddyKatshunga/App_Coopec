<div class="max-w-4xl mx-auto py-8">
    <div class="bg-white shadow-md rounded-lg overflow-hidden border-t-4 border-green-500">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h2 class="text-xl font-bold text-gray-800">Enregistrer une Entrée de Fonds</h2>
             @if (auth()->user()->journee_ouverte)
                <h3 class="text-white font-bold text-lg"> 📅 Date d'opération : {{ \Carbon\Carbon::parse(auth()->user()->journee_ouverte->date_cloture)->format('d/m/Y') }}</h3>
            @else
                <h5>Opération Impossible, pas de date disponible</h5>
            @endif
        </div>

        <form wire:submit.prevent="save" class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Source / Type de revenu</label>
                    <select wire:model="types_revenu_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">-- Sélectionner --</option>
                        @foreach($types as $type)
                            <option value="{{ $type->id }}">{{ $type->nom }}</option>
                        @endforeach
                    </select>
                    @error('types_revenu_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Libellé du revenu</label>
                    <input type="text" wire:model="libelle" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    @error('libelle') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Montant</label>
                    <input type="number" step="0.01" wire:model="montant" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @error('montant') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Devise</label>
                    <select wire:model="monnaie" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="USD">USD ($)</option>
                        <option value="CDF">CDF (FC)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Référence (Bordereau...)</label>
                    <input type="text" wire:model="reference" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea wire:model="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                </div>
            </div>

            <div class="flex justify-end space-x-3 border-t pt-6">
                <a href="{{ route('revenus.index') }}" class="px-4 py-2 border rounded-md text-gray-600 hover:bg-gray-50" wire:navigate>Annuler</a>
                <button type="submit" wire:loading.attr="disabled" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 flex items-center">
                    <span wire:loading wire:target="save" class="mr-2 animate-spin text-white">...</span>
                    Confirmer l'Entrée
                </button>
            </div>
        </form>
    </div>
</div>