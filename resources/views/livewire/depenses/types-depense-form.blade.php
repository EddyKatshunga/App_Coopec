<div class="max-w-xl mx-auto bg-white p-6 rounded shadow">

    <h2 class="text-xl font-bold mb-4">
        {{ $typesDepense ? 'Modifier le type de dépense' : 'Ajouter un type de dépense' }}
    </h2>

    <form wire:submit.prevent="save" class="space-y-4">

        <div>
            <label class="block font-medium">Nom</label>
            <input type="text" wire:model.defer="nom"
                   class="w-full border rounded px-3 py-2">
            @error('nom') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block font-medium">Code comptable</label>
            <input type="text" wire:model.defer="code_comptable"
                   class="w-full border rounded px-3 py-2">
            @error('code_comptable') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="flex justify-end">
            <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Enregistrer
            </button>
        </div>

    </form>
</div>
