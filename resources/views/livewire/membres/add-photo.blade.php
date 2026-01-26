<div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 space-y-4">

        <h2 class="text-lg font-semibold text-gray-800">
            Ajouter des photos – {{ $user->name ?? $user->email }}
        </h2>

        <input
            type="file"
            multiple
            wire:model="photos"
            class="block w-full text-sm text-gray-600"
        >

        @error('photos.*')
            <p class="text-sm text-red-600">{{ $message }}</p>
        @enderror

        <div class="flex justify-end gap-2 pt-4">
            <button
                wire:click="$dispatch('close-modal')"
                class="px-4 py-2 rounded-lg border"
            >
                Annuler
            </button>

            <button
                wire:click="save"
                class="px-4 py-2 rounded-lg bg-purple-600 text-white hover:bg-purple-700"
            >
                Enregistrer
            </button>
        </div>

    </div>
</div>
