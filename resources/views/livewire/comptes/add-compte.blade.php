<div class="max-w-3xl mx-auto p-6">

    <div class="bg-white rounded-xl shadow p-6 space-y-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                ➕ Ajouter un compte épargne
            </h1>
            <p class="text-gray-500 text-sm">
                Création d’un compte supplémentaire pour le membre
            </p>
        </div>

        @if (session()->has('success'))
            <div class="bg-green-100 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        <form wire:submit.prevent="save" class="space-y-5">

            {{-- Intitulé --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Intitulé du compte
                </label>
                <input
                    type="text"
                    wire:model.defer="intitule"
                    placeholder="Ex: Épargne scolaire, Épargne business…"
                    class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring focus:ring-blue-200"
                >
                @error('intitule')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            {{-- Info membre --}}
            <div class="bg-gray-50 border rounded-lg p-4 text-sm text-gray-600">
                🔒 Le numéro de compte est généré automatiquement<br>
                Format : <strong>N°Identification-XX</strong>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-3">
                <a href="{{ route('membre.show', $membre_id) }}"
                   class="px-4 py-2 border rounded-lg text-gray-700">
                    Annuler
                </a>

                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg">
                    💾 Créer le compte
                </button>
            </div>

        </form>

    </div>

</div>
