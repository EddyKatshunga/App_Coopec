<div class="p-6 bg-white rounded-2xl shadow-lg space-y-6">
    {{-- ================= MESSAGE SUCCÈS ================= --}}
    @if (session()->has('success'))
        <div class="mb-6 rounded-lg bg-green-100 px-4 py-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif
    
    {{-- Titre --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Liste des membres</h1>
        <p class="text-sm text-gray-500">Recherche, filtrage et gestion des membres</p>
        <a href="{{ route('membre.add') }}">Ajouter un nouveau membre</a>
    </div>

    {{-- Filtres --}}
    <div class="grid grid-cols-1 md:grid-cols-6 gap-4 bg-gray-50 p-4 rounded-xl">

        <div class="md:col-span-2">
            <input
                type="text"
                wire:model.live="search"
                placeholder="🔍 Nom, email ou N° identification"
                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
            >
        </div>

        <div>
            <select wire:model.live="sexe"
                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                <option value="">Sexe</option>
                <option value="M">Masculin</option>
                <option value="F">Féminin</option>
            </select>
        </div>

         <div>
            <select wire:model.live="qualite"
                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                <option value="">Qualité</option>
                <option value="Auxiliaire">Auxiliaire</option>
                <option value="Effectif">Effectif</option>
            </select>
        </div>

        <div>
            <input type="date" wire:model="dateFrom"
                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
        </div>

        <div>
            <button wire:click="resetFilters"
                class="w-full rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100">
                Réinitialiser
            </button>
        </div>
    </div>

    {{-- Tableau --}}
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100 text-sm text-gray-600">
                <tr>
                    <th class="px-4 py-3 text-left">Numéro ID</th>
                    <th class="px-4 py-3 text-left">Nom</th>
                    <th class="px-4 py-3 text-left">Email</th>
                    <th class="px-4 py-3">Sexe</th>
                    <th class="px-4 py-3">Qualité</th>
                    <th class="px-4 py-3">Adhésion</th>
                    <th class="px-4 py-3 text-center">Statut</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100 text-sm">
                @forelse ($membres as $membre)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800">
                            {{ $membre->numero_identification }}
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-800">
                            {{ $membre->nom }}
                        </td>
                        <td class="px-4 py-3">
                            {{ $membre->user->email ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            {{ $membre->sexe }}
                        </td>
                        <td class="px-4 py-3">
                            {{ $membre->qualite }}
                        </td>
                        <td class="px-4 py-3">
                            {{ $membre->date_adhesion?->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if ($membre->agent)
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">
                                    Agent
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">
                                    Membre
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <a href="{{ route('membre.show', $membre) }}"
                               class="text-blue-600 hover:underline">
                                Voir
                            </a>
                            <a href="{{ route('membre.edit', $membre) }}"
                               class="text-gray-600 hover:underline">
                                Modifier
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                            Aucun membre trouvé
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div>
        {{ $membres->links() }}
    </div>

</div>
