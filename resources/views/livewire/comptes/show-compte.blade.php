<div class="max-w-7xl mx-auto p-6 space-y-8">

    {{-- ================= HEADER ================= --}}
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                {{ $compte->intitule }}
            </h1>
            <p class="text-sm text-gray-500">
                Compte N° {{ $compte->numero_compte }}
            </p>
            <p class="text-sm text-gray-500">
                Membre : {{ $compte->membre->nom }}
            </p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('membre.show', $compte->membre) }}"
               class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-100">
                Retour au membre
            </a>
        </div>
    </div>

    {{-- ================= SOLDES ================= --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-green-50 p-6 rounded-xl shadow">
            <p class="text-sm text-gray-500">Solde actuel CDF</p>
            <p class="text-3xl font-bold text-green-700">
                {{ number_format($compte->solde_cdf, 0, ',', ' ') }} CDF
            </p>
        </div>

        <div class="bg-yellow-50 p-6 rounded-xl shadow">
            <p class="text-sm text-gray-500">Solde actuel USD</p>
            <p class="text-3xl font-bold text-yellow-700">
                {{ number_format($compte->solde_usd, 2) }} USD
            </p>
        </div>
    </div>

    {{-- ================= STATISTIQUES ================= --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

        <div class="bg-blue-50 p-5 rounded-xl shadow">
            <p class="text-sm text-gray-500">Dépôts CDF</p>
            <p class="text-xl font-bold text-blue-700">
                {{ number_format($totalDepotCDF, 0, ',', ' ') }}
            </p>
        </div>

        <div class="bg-red-50 p-5 rounded-xl shadow">
            <p class="text-sm text-gray-500">Retraits CDF</p>
            <p class="text-xl font-bold text-red-700">
                {{ number_format($totalRetraitCDF, 0, ',', ' ') }}
            </p>
        </div>

        <div class="bg-blue-50 p-5 rounded-xl shadow">
            <p class="text-sm text-gray-500">Dépôts USD</p>
            <p class="text-xl font-bold text-blue-700">
                {{ number_format($totalDepotUSD, 2) }}
            </p>
        </div>

        <div class="bg-red-50 p-5 rounded-xl shadow">
            <p class="text-sm text-gray-500">Retraits USD</p>
            <p class="text-xl font-bold text-red-700">
                {{ number_format($totalRetraitUSD, 2) }}
            </p>
        </div>
    </div>

    {{-- ================= TRANSACTIONS ================= --}}
    <div class="bg-white rounded-2xl shadow p-6 overflow-x-auto">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">
            Historique des transactions
        </h2>

        <table class="min-w-full text-sm border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2 border">Date</th>
                    <th class="px-3 py-2 border">Type</th>
                    <th class="px-3 py-2 border">Montant</th>
                    <th class="px-3 py-2 border">Monnaie</th>
                    <th class="px-3 py-2 border">Report</th>
                    <th class="px-3 py-2 border">Solde</th>
                    <th class="px-3 py-2 border">Statut</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($compte->transactions as $transaction)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 border">
                            {{ \Carbon\Carbon::parse($transaction->date_transaction)->format('d/m/Y') }}
                        </td>

                        <td class="px-3 py-2 border font-semibold
                            {{ $transaction->type_transaction === 'DEPOT' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $transaction->type_transaction }}
                        </td>

                        <td class="px-3 py-2 border">
                            {{ number_format($transaction->montant, 0, ',', ' ') }}
                        </td>

                        <td class="px-3 py-2 border">
                            {{ $transaction->monnaie }}
                        </td>

                        <td class="px-3 py-2 border">
                            {{ number_format($transaction->solde_avant, 0, ',', ' ') }}
                        </td>

                        <td class="px-3 py-2 border">
                            {{ number_format($transaction->solde_apres, 0, ',', ' ') }}
                        </td>

                        <td class="px-3 py-2 border">
                            @if ($transaction->statut === 'ANNULE')
                                <span class="text-red-600 font-semibold">
                                    Annulée
                                </span>
                            @else
                                <span class="text-green-600 font-semibold">
                                    Valide
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-6 text-gray-500">
                            Aucune transaction enregistrée pour ce compte.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ================= ZONE RELEVE ================= --}}
    <div class="flex flex-col gap-4 mb-4">

        <div class="flex items-end gap-2">
            <div>
                <label class="block text-sm font-medium text-gray-700">Date début</label>
                <input type="date" wire:model="dateDebut" wire:change="updatePdfPreview"
                    class="border rounded px-2 py-1">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Date fin</label>
                <input type="date" wire:model="dateFin" wire:change="updatePdfPreview"
                    class="border rounded px-2 py-1">
            </div>
            <div>
                <button wire:click="downloadReleve"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Télécharger le relevé PDF
                </button>
            </div>
        </div>
    </div>

    {{-- ================= FIN ZONE RELEVE ================= --}}

</div>
