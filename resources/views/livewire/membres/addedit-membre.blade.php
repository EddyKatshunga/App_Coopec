<div class="max-w-6xl mx-auto p-6 bg-white rounded-2xl shadow-lg">

    {{-- ================= TITRE ================= --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800">
            {{ $isEdit ? 'Modifier le membre' : 'Ajouter un nouveau membre' }}
        </h1>
        <p class="text-sm text-gray-500">
            {{ $isEdit
                ? 'Mise à jour des informations du membre'
                : 'Enregistrement d’un membre et création automatique du compte utilisateur'
            }}
        </p>
    </div>

    {{-- ================= MESSAGE SUCCÈS ================= --}}
    @if (session()->has('success'))
        <div class="mb-6 rounded-lg bg-green-100 px-4 py-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-10">

        {{-- ================= INFOS UTILISATEUR ================= --}}
        <div>
            <h2 class="mb-4 text-lg font-semibold text-gray-700 border-b pb-2">
                Informations utilisateur
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Nom complet
                    </label>
                    <input type="text" wire:model.defer="nom_complet"
                        class="mt-1 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    @error('nom_complet')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Email
                    </label>
                    <input type="email" wire:model.defer="email"
                        class="mt-1 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    @error('email')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Mot de passe
                    </label>
                    <input type="password" wire:model.defer="password"
                        class="mt-1 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    @error('password')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror

                    @if($isEdit)
                        <p class="text-xs text-gray-500 mt-1">
                            Laissez vide pour conserver le mot de passe actuel
                        </p>
                    @endif
                </div>

            </div>
        </div>

        {{-- ================= INFOS PERSONNELLES ================= --}}
        <div>
            <h2 class="mb-4 text-lg font-semibold text-gray-700 border-b pb-2">
                Informations personnelles
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Numéro d’identification
                    </label>
                    <input type="text" pattern="[A-Za-z][0-9]{6}"
                        wire:model.defer="numero_identification"
                        @disabled($isEdit)
                        class="mt-1 w-full rounded-lg border-gray-300
                               focus:border-blue-500 focus:ring-blue-500
                               disabled:bg-gray-100 disabled:cursor-not-allowed">
                    @error('numero_identification')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror

                    @if($isEdit)
                        <p class="text-xs text-gray-500 mt-1">
                            Ce champ ne peut pas être modifié
                        </p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Sexe
                    </label>
                    <select wire:model.defer="sexe"
                        class="mt-1 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">-- Choisir --</option>
                        <option value="M">Masculin</option>
                        <option value="F">Féminin</option>
                    </select>
                    @error('sexe')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Date de naissance
                    </label>
                    <input type="date" wire:model.defer="date_de_naissance"
                        class="mt-1 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    @error('date_de_naissance')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Lieu de naissance
                    </label>
                    <input type="text" wire:model.defer="lieu_de_naissance"
                        class="mt-1 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    @error('lieu_de_naissance')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </div>

        {{-- ================= COORDONNÉES ================= --}}
        <div>
            <h2 class="mb-4 text-lg font-semibold text-gray-700 border-b pb-2">
                Coordonnées
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Adresse
                    </label>
                    <input type="text" wire:model.defer="adresse"
                        class="mt-1 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Téléphone
                    </label>
                    <input type="text" wire:model.defer="telephone"
                        class="mt-1 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

            </div>
        </div>

        {{-- ================= ACTIVITÉ ================= --}}
        <div>
            <h2 class="mb-4 text-lg font-semibold text-gray-700 border-b pb-2">
                Activité professionnelle
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Activité
                    </label>
                    <input type="text" wire:model.defer="activites"
                        class="mt-1 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Adresse de l’activité
                    </label>
                    <input type="text" wire:model.defer="adresse_activite"
                        class="mt-1 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

            </div>
        </div>

        {{-- ================= ADHÉSION ================= --}}
        <div>
            <h2 class="mb-4 text-lg font-semibold text-gray-700 border-b pb-2">
                Adhésion
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Qualité
                    </label>
                    <input type="text" wire:model.defer="qualite"
                        class="mt-1 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Date d’adhésion
                    </label>
                    <input type="date" wire:model.defer="date_adhesion"
                        class="mt-1 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

            </div>
        </div>

        {{-- ================= ACTIONS ================= --}}
        <div class="flex justify-end gap-4 border-t pt-6">

            <a href="{{ route('membre.index') }}" wire:navigate
               class="px-6 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100">
                Annuler
            </a>

            <button type="submit"
                wire:loading.attr="disabled"
                wire:target="save"
                class="px-8 py-2 rounded-lg bg-blue-600 text-white font-semibold
                       hover:bg-blue-700 focus:ring-4 focus:ring-blue-200
                       disabled:opacity-60 disabled:cursor-not-allowed">

                <span wire:loading.remove wire:target="save">
                    {{ $isEdit ? 'Mettre à jour le membre' : 'Enregistrer le membre' }}
                </span>

                <span wire:loading wire:target="save">
                    Traitement en cours...
                </span>
            </button>

        </div>

    </form>
</div>
