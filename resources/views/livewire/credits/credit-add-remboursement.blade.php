<div>
    {{-- Bouton pour ouvrir modal --}}
    <button wire:click="openModal"
        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
        Ajouter remboursement
    </button>

    {{-- Modal --}}
    <div x-data="{ open: @entangle('showModal') }"
         x-show="open"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
         style="display: none;">
        <div @click.away="open = false" class="bg-white rounded shadow-lg w-full max-w-md p-6">

            <h2 class="text-lg font-bold mb-4">Ajouter un remboursement</h2>

            <form wire:submit.prevent="save" class="space-y-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700">Date de paiement</label>
                    <input type="date" wire:model="date_paiement"
                        class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200">
                    @error('date_paiement') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Montant</label>
                    <input type="number" step="0.01" wire:model="montant"
                        class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200">
                    @error('montant') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Mode de paiement</label>
                    <select wire:model="mode_paiement" class="w-full border rounded px-3 py-2">
                        <option value="cash">Espèces</option>
                        <option value="mpesa">M-Pesa</option>
                        <option value="airtel">Airtel Money</option>
                        <option value="banque">Virement bancaire</option>
                    </select>
                    @error('mode_paiement') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" @click="open = false"
                        class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100">
                        Annuler
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Enregistrer
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
