<div class="max-w-7xl mx-auto py-10 px-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                Gestion des agences
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Liste complète des agences enregistrées dans le système.
            </p>
        </div>

        <a href="{{ route('agences.create') }}"
           class="inline-flex items-center px-5 py-3 bg-indigo-600 hover:bg-indigo-700
                  text-white font-semibold rounded-xl shadow-lg
                  transition duration-200">
            + Nouvelle agence
        </a>
    </div>

    {{-- Barre de recherche --}}
    <div class="mb-6">
        <input type="text"
               wire:model.debounce.500ms="search"
               placeholder="Rechercher par nom, code ou ville..."
               class="w-full md:w-96 rounded-xl border-gray-300 shadow-sm
                      focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>

    {{-- Tableau --}}
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gray-50">
                    <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-4">Agence</th>
                        <th class="px-6 py-4">Localisation</th>
                        <th class="px-6 py-4">Directeur</th>
                        <th class="px-6 py-4">Solde Coffre</th>
                        <th class="px-6 py-4">Solde Épargne</th>
                        <th class="px-6 py-4">Statut</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">

                    @forelse($agences as $agence)

                        @php
                            $nonFinalisee = is_null($agence->directeur_id)
                                || $agence->solde_actuel_coffre < 0;
                        @endphp

                        <tr class="hover:bg-gray-50 transition">

                            {{-- Nom --}}
                            <td class="px-6 py-5">
                                <div class="font-semibold text-gray-800">
                                    {{ $agence->nom }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    Code: {{ $agence->code }}
                                </div>
                            </td>

                            {{-- Localisation --}}
                            <td class="px-6 py-5 text-sm text-gray-600">
                                {{ $agence->ville }}, {{ $agence->pays }}
                            </td>

                            {{-- Directeur --}}
                            <td class="px-6 py-5">
                                @if($agence->directeur)
                                    <div class="text-sm font-medium text-gray-800">
                                        {{ $agence->directeur->user->name ?? '—' }}
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold
                                                 bg-red-100 text-red-700 rounded-full">
                                        Non assigné
                                    </span>
                                @endif
                            </td>

                            {{-- Solde Coffre --}}
                            <td class="px-6 py-5 text-sm font-semibold
                                       {{ $agence->solde_actuel_coffre < 0 ? 'text-red-600' : 'text-gray-800' }}">
                                {{ number_format($agence->solde_actuel_coffre, 2, ',', ' ') }} CDF
                            </td>

                            {{-- Solde Épargne --}}
                            <td class="px-6 py-5 text-sm text-gray-800">
                                {{ number_format($agence->solde_actuel_epargne, 2, ',', ' ') }} CDF
                            </td>

                            {{-- Statut --}}
                            <td class="px-6 py-5">
                                @if($nonFinalisee)
                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold
                                                 bg-red-100 text-red-700 rounded-full">
                                        ⚠ Non finalisée
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold
                                                 bg-green-100 text-green-700 rounded-full">
                                        ✔ Active
                                    </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-5 text-right space-x-2">

                                <a href="{{ route('agences.show', $agence) }}"
                                   class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                    Voir
                                </a>

                                <a href="{{ route('agences.edit', $agence) }}"
                                   class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                                    Modifier
                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                                Aucune agence trouvée.
                            </td>
                        </tr>

                    @endforelse

                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 bg-gray-50">
            {{ $agences->links() }}
        </div>

    </div>

</div>
