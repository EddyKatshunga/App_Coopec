<div class="max-w-6xl mx-auto p-6 space-y-8">

    {{-- ================= HEADER ================= --}}
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                {{ $membre->nom }}
            </h1>
            <p class="text-sm text-gray-500">
                Membre depuis le {{ $membre->date_adhesion?->format('d/m/Y') }}
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <button wire:click="telechargerFiche"
                class="px-4 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-900">
                Télécharger la fiche
            </button>

            <a href="{{ route('membre.edit', $membre) }}"
               class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                Modifier
            </a>

            <a href="{{ route('compte.create', $membre) }}"
               class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                Ajouter un compte Epargne
            </a>

            <a href="{{ route('credit.create') }}"
               class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                Ajouter un crédit
            </a>

            <button
                wire:click="$set('showAddPhotoModal', true)"
                class="px-4 py-2 rounded-lg bg-purple-600 text-white hover:bg-purple-700"
            >
                Ajouter des photos
            </button>



            <a href="{{ route('membre.index') }}"
               class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-100">
                Retour
            </a>
        </div>
    </div>

    {{-- ================= INFOS MEMBRE ================= --}}
    <div class="bg-white rounded-2xl shadow p-6 grid grid-cols-1 md:grid-cols-3 gap-6">

        <div>
            <h3 class="font-semibold text-gray-700 mb-2">Informations personnelles</h3>
            <p><strong>Sexe :</strong> {{ $membre->sexe }}</p>
            <p><strong>Né(e) le :</strong> {{ $membre->date_de_naissance?->format('d/m/Y') ?? '—' }}</p>
            <p><strong>Lieu :</strong> {{ $membre->lieu_de_naissance ?? '—' }}</p>
        </div>

        <div>
            <h3 class="font-semibold text-gray-700 mb-2">Coordonnées</h3>
            <p><strong>Email :</strong> {{ $membre->user->email }}</p>
            <p><strong>Téléphone :</strong> {{ $membre->telephone ?? '—' }}</p>
            <p><strong>Adresse :</strong> {{ $membre->adresse ?? '—' }}</p>
        </div>

        <div>
            <h3 class="font-semibold text-gray-700 mb-2">Statut</h3>
            <p><strong>Qualité :</strong> {{ $membre->qualite }}</p>
            <p><strong>Activité :</strong> {{ $membre->activites ?? '—' }}</p>
            <p>
                <strong>Rôle :</strong>
                @if ($membre->agent)
                    <span class="text-blue-600 font-semibold">Agent</span>
                @else
                    <span class="text-green-600 font-semibold">Membre</span>
                @endif
            </p>
        </div>
    </div>

    {{-- ================= STATISTIQUES ================= --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="bg-blue-50 rounded-xl p-5 shadow">
            <p class="text-sm text-gray-500">Nombre de comptes</p>
            <p class="text-3xl font-bold text-blue-700">
                {{ $membre->comptes->count() }}
            </p>
        </div>

        <div class="bg-green-50 rounded-xl p-5 shadow">
            <p class="text-sm text-gray-500">Total épargne CDF</p>
            <p class="text-2xl font-bold text-green-700">
                {{ number_format($totalSoldeCDF, 0, ',', ' ') }} CDF
            </p>
        </div>

        <div class="bg-yellow-50 rounded-xl p-5 shadow">
            <p class="text-sm text-gray-500">Total épargne USD</p>
            <p class="text-2xl font-bold text-yellow-700">
                {{ number_format($totalSoldeUSD, 2) }} USD
            </p>
        </div>

    </div>

    {{-- ================= COMPTES EPARGNES ================= --}}
    <div class="space-y-4">
        <h2 class="text-xl font-semibold text-gray-800">
            Comptes épargnes
        </h2>

        @forelse ($membre->comptes as $compte)
            <div class="bg-white shadow rounded-xl p-5 flex flex-col md:flex-row md:justify-between md:items-center gap-4">

                <div>
                    <p class="font-semibold text-gray-700">
                        {{ $compte->intitule }}
                    </p>
                    <p class="text-sm text-gray-500">
                        N° {{ $compte->numero_compte }}
                    </p>

                    <div class="mt-2 text-sm text-gray-700">
                        <span class="mr-4">
                            CDF :
                            <strong>{{ number_format($compte->solde_cdf, 0, ',', ' ') }}</strong>
                        </span>
                        <span>
                            USD :
                            <strong>{{ number_format($compte->solde_usd, 2) }}</strong>
                        </span>
                    </div>
                </div>

                <a href="{{ route('compte.show', $compte) }}"
                   class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Voir plus
                </a>

            </div>
        @empty
            <div class="bg-gray-50 p-6 rounded-xl text-center text-gray-500">
                Aucun compte épargne enregistré pour ce membre.
            </div>
        @endforelse
    </div>

    {{-- ================= MODAL AJOUT PHOTOS ================= --}}
    @if ($showAddPhotoModal)
        <livewire:membres.add-photo
            :user="$membre->user"
            wire:key="add-photo-{{ $membre->user->id }}"
            wire:on="photo-added"
        />
    @endif


</div>
