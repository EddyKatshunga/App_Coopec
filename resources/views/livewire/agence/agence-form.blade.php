<div class="max-w-4xl mx-auto py-10 px-6">

    {{-- Carte principale --}}
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">

        {{-- Header --}}
        <div class="px-8 py-6 bg-gradient-to-r from-indigo-600 to-blue-600">
            <h2 class="text-2xl font-bold text-white">
                {{ $isEdit ? 'Modifier une agence' : 'Créer une nouvelle agence' }}
            </h2>
            <p class="text-blue-100 mt-1 text-sm">
                {{ $isEdit 
                    ? 'Modifiez les informations principales de l’agence.' 
                    : 'Renseignez les informations nécessaires pour créer une nouvelle agence.' 
                }}
            </p>
        </div>

        {{-- Corps --}}
        <form wire:submit.prevent="save" class="px-8 py-8 space-y-8">

            {{-- Informations générales --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-6">
                    Informations générales
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Nom --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nom de l’agence
                        </label>
                        <input type="text"
                               wire:model.defer="nom"
                               class="w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition duration-200"
                               placeholder="Ex: Agence Goma Centre">
                        @error('nom') 
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p> 
                        @enderror
                    </div>

                    {{-- Code --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Code agence
                        </label>
                        <input type="text"
                               wire:model.defer="code"
                               class="w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition duration-200"
                               placeholder="Ex: AGM001">
                        @error('code') 
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p> 
                        @enderror
                    </div>

                    {{-- Ville --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Ville
                        </label>
                        <input type="text"
                               wire:model.defer="ville"
                               class="w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition duration-200"
                               placeholder="Ex: Goma">
                        @error('ville') 
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p> 
                        @enderror
                    </div>

                    {{-- Pays --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Pays
                        </label>
                        <input type="text"
                               wire:model.defer="pays"
                               class="w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition duration-200"
                               placeholder="Ex: RDC">
                        @error('pays') 
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p> 
                        @enderror
                    </div>

                </div>
            </div>

            {{-- Section Fonds initial --}}
            @unless($isEdit)
            <div class="border-t pt-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-6">
                    Fonds initial du coffre
                </h3>

                <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 mb-6">
                    <p class="text-sm text-blue-800 leading-relaxed">
                        Vous pouvez allouer un montant initial pour le lancement de l’agence. 
                        Ce montant sera enregistré comme <strong>solde actuel du coffre</strong>.
                        <br class="hidden md:block">
                        Le solde épargne sera initialisé automatiquement à <strong>0</strong>.
                    </p>
                </div>

                <div class="max-w-md">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Montant initial (CDF)
                    </label>
                    <input type="number"
                           wire:model.defer="solde_initial_coffre"
                           min="0"
                           step="0.01"
                           class="w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition duration-200"
                           placeholder="Ex: 5 000 000">
                    @error('solde_initial_coffre') 
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p> 
                    @enderror
                </div>
            </div>
            @endunless


            {{-- Information Directeur --}}
            <div class="border-t pt-8">

                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    Directeur de l’agence
                </h3>

                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5">
                    <p class="text-sm text-yellow-800 leading-relaxed">
                        Lors de la création d’une agence, aucun directeur ne peut être assigné immédiatement,
                        car l’agence ne possède pas encore d’agents.
                        <br><br>
                        Après la création, vous devrez :
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li>Créer au moins un agent</li>
                            <li>Attribuer le rôle <strong>Directeur</strong> à l’un des agents</li>
                        </ul>
                    </p>
                </div>

            </div>

            {{-- Boutons --}}
            <div class="border-t pt-8 flex items-center justify-between">

                <a href="{{ route('agences.index') }}"
                   class="text-gray-600 hover:text-gray-800 font-medium transition">
                    Annuler
                </a>

                <button type="submit"
                        class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 
                               text-white font-semibold rounded-xl shadow-lg 
                               transition duration-200 ease-in-out 
                               focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">

                    <svg wire:loading wire:target="save" 
                         class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" 
                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                              d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>

                    {{ $isEdit ? 'Mettre à jour l’agence' : 'Créer l’agence' }}
                </button>

            </div>

        </form>

    </div>
</div>
