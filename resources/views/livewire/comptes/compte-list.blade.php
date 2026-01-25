<div class="p-6 space-y-6">

    {{-- En-tête --}}
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">📊 Comptes Épargnes</h1>
            <p class="text-gray-500 text-sm">
                Tableau de bord des comptes d’épargne de la coopérative
            </p>
        </div>

        <div class="flex gap-2">
            <input
                type="text"
                wire:model.debounce.500ms="search"
                placeholder="🔍 Numéro ou intitulé du compte"
                class="border rounded-lg px-3 py-2 text-sm focus:ring focus:ring-blue-200"
            >
        </div>
    </div>

    {{-- Statistiques --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
        <x-stat-card label="Membres" :value="$stats['membres']" icon="👥" />
        <x-stat-card label="Comptes" :value="$stats['total_comptes']" icon="💼" />
        <x-stat-card
            label="Total CDF"
            :value="number_format($stats['total_cdf'], 0, ',', ' ') . ' CDF'"
            icon="💰"
        />
        <x-stat-card
            label="Total USD"
            :value="number_format($stats['total_usd'], 2) . ' $'"
            icon="💵"
        />
    </div>

    {{-- Tableau des comptes --}}
    <div class="bg-white rounded-xl shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left">Membre</th>
                    <th class="px-4 py-3 text-left">Intitulé</th>
                    <th class="px-4 py-3 text-left">N° Compte</th>
                    <th class="px-4 py-3 text-right">Solde CDF</th>
                    <th class="px-4 py-3 text-right">Solde USD</th>
                    <th class="px-4 py-3 text-center">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($comptes as $compte)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-2">
                            <div class="font-semibold text-gray-800">
                                {{ $compte->membre->user->name ?? '—' }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $compte->membre->numero_identification }}
                            </div>
                        </td>

                        <td class="px-4 py-2">
                            {{ $compte->intitule }}
                        </td>

                        <td class="px-4 py-2 font-mono text-gray-700">
                            {{ $compte->numero_compte }}
                        </td>

                        <td class="px-4 py-2 text-right text-green-700 font-semibold">
                            {{ number_format($compte->solde_cdf, 0, ',', ' ') }} CDF
                        </td>

                        <td class="px-4 py-2 text-right text-blue-700 font-semibold">
                            {{ number_format($compte->solde_usd, 2) }} $
                        </td>

                        <td class="px-4 py-2 text-center space-x-2">
                            <a href="{{ route('compte.show', $compte) }}"
                               class="text-blue-600 hover:underline"
                               title="Voir le compte">
                                👁
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-6 text-gray-500">
                            Aucun compte trouvé
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="p-4">
            {{ $comptes->links() }}
        </div>
    </div>

</div>
