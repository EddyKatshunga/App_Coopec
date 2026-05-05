<div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Journal Comptable</h1>
            <p class="text-sm text-gray-600">Consultez l'historique de toutes les écritures financières.</p>
        </div>

        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-end gap-4">
                
                @if($isSuperAdmin)
                <div class="w-full sm:w-auto">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Agence</label>
                    <select wire:model.live="agence_id" class="block w-full sm:w-64 rounded-lg border-gray-300 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach($agences as $agence)
                            <option value="{{ $agence->id }}">{{ $agence->nom }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <div class="flex-1 sm:flex-none">
                        <input type="date" wire:model.live="date_debut" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    </div>
                    <span class="text-gray-500 text-sm">à</span>
                    <div class="flex-1 sm:flex-none">
                        <input type="date" wire:model.live="date_fin" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    </div>
                </div>

            </div>
        </div>

        <div class="bg-white shadow-sm border border-gray-200 rounded-xl overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Libellé & Réf</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Agence</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Mouvement Total</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($entries as $entry)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                {{ $entry->date_operation->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 font-semibold">{{ $entry->libelle }}</div>
                                <div class="text-xs text-gray-400 font-mono mt-1">{{ substr($entry->uuid, 0, 8) }}...</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $entry->agence->nom ?? 'Siège' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-700">
                                {{ number_format($entry->lines->sum('debit'), 2, ',', ' ') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <a href="{{ route('journal.show', $entry->uuid) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                    Voir détails &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                Aucune écriture comptable trouvée.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $entries->links() }}</div>
    </div>
</div>