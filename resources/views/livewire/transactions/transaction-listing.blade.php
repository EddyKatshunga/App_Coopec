<div
    x-data="{ type: @entangle('typeReleve') }"
    class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6"
>

    {{-- ================= HEADER ================= --}}
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-800">
            Listing des transactions
        </h2>

        <div class="text-sm text-gray-500">
            {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    {{-- ================= FILTRES ================= --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

        {{-- Agence --}}
        @can('changer agence transactions')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Agence
                </label>
                <select
                    wire:model="agence_id"
                    class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500"
                >
                    @foreach($agences as $agence)
                        <option value="{{ $agence->id }}">
                            {{ $agence->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endcan

        {{-- Type relevé --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Type de relevé
            </label>
            <select
                wire:model="typeReleve"
                class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500"
            >
                <option value="journalier">Relevé journalier</option>
                <option value="compte">Relevé d’un compte</option>
            </select>
        </div>

        {{-- Date journalier --}}
        <div x-show="type === 'journalier'">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Date
            </label>
            <input
                type="date"
                wire:model.live="date_jour"
                class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500"
            >
        </div>

        {{-- Période compte --}}
        <div x-show="type === 'compte'" class="grid grid-cols-2 gap-2 md:col-span-2">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Début
                </label>
                <input
                    type="date"
                    wire:model.live="date_debut"
                    class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500"
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Fin
                </label>
                <input
                    type="date"
                    wire:model.live="date_fin"
                    class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500"
                >
            </div>
        </div>
    </div>

    {{-- ================= TABLE ================= --}}
    <div class="relative border rounded-lg overflow-hidden">
        <div class="max-h-[65vh] overflow-y-auto">

            <table class="min-w-full text-sm text-gray-700">
                <thead class="sticky top-0 bg-gray-100 border-b">
                    <tr class="text-left">
                        <th class="px-3 py-2">Date</th>
                        <th class="px-3 py-2">Agent</th>
                        <th class="px-3 py-2">Compte</th>
                        <th class="px-3 py-2">Type</th>
                        <th class="px-3 py-2 text-right">Montant</th>
                    </tr>
                </thead>

                <tbody class="divide-y">

                @forelse($transactions as $agentId => $dates)
                    @foreach($dates as $date => $lignes)

                        {{-- GROUPE AGENT + DATE --}}
                        <tr class="bg-gray-50">
                            <td colspan="5" class="px-3 py-2 font-semibold text-gray-800">
                                {{ $lignes[0]['agent'] ?? '—' }}
                                <span class="text-gray-500 font-normal">
                                    — {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
                                </span>
                            </td>
                        </tr>

                        {{-- LIGNES --}}
                        @foreach($lignes as $t)
                            <tr>
                                <td class="px-3 py-2">
                                    {{ \Carbon\Carbon::parse($t['date_transaction'])->format('d/m/Y') }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ $t['agent'] ?? '—' }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ $t['compte'] }}
                                </td>
                                <td class="px-3 py-2">
                                    <span class="px-2 py-0.5 rounded text-xs
                                        {{ $t['type'] === 'DEPOT'
                                            ? 'bg-green-100 text-green-700'
                                            : 'bg-red-100 text-red-700' }}">
                                        {{ $t['type'] }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-right font-mono tabular-nums">
                                    {{ number_format($t['montant'], 2) }}
                                </td>
                            </tr>
                        @endforeach

                    @endforeach
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-6 text-gray-500">
                            Aucune transaction trouvée
                        </td>
                    </tr>
                @endforelse

                </tbody>
            </table>

        </div>
    </div>

    {{-- ================= TOTAUX JOURNALIER ================= --}}
    @if($typeReleve === 'journalier')
        <div class="border-t pt-4 grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">

            <div>
                <span class="text-gray-500">Report</span>
                <div class="font-mono font-semibold">
                    {{ number_format($report, 2) }}
                </div>
            </div>

            <div>
                <span class="text-gray-500">Total dépôts</span>
                <div class="font-mono font-semibold text-green-700">
                    {{ number_format($totalDepot, 2) }}
                </div>
            </div>

            <div>
                <span class="text-gray-500">Total retraits</span>
                <div class="font-mono font-semibold text-red-700">
                    {{ number_format($totalRetrait, 2) }}
                </div>
            </div>

            <div>
                <span class="text-gray-500">Solde final</span>
                <div class="font-mono font-bold text-lg">
                    {{ number_format($soldeFinal, 2) }}
                </div>
            </div>

        </div>
    @endif

</div>
