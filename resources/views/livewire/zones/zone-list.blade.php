<div class="space-y-6">
    {{-- Filtres Avancés --}}
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col lg:flex-row lg:items-end gap-4">
        <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
            @can('can.level6')
            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-wider mb-2">Agence</label>
                <select wire:model.live="selectedAgenceId" class="w-full rounded-xl border-gray-200 text-sm focus:ring-indigo-500">
                    <option value="">Sélectionner une agence</option>
                    @foreach($agences as $agence)
                        <option value="{{ $agence->id }}">{{ $agence->nom }}</option>
                    @endforeach
                </select>
            </div>
            @endcan

            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-wider mb-2">Date Début</label>
                <input type="date" wire:model.live="dateDebut" class="w-full rounded-xl border-gray-200 text-sm focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-wider mb-2">Date Fin</label>
                <input type="date" wire:model.live="dateFin" class="w-full rounded-xl border-gray-200 text-sm focus:ring-indigo-500">
            </div>
        </div>
        
        <div class="flex gap-2">
            <button class="bg-gray-100 text-gray-600 px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-gray-200 transition-all">
                <x-heroicon-o-arrow-path class="w-5 h-5" />
            </button>
        </div>
    </div>

    {{-- Tableau de Performance --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="p-4 text-xs font-black text-gray-400 uppercase tracking-widest">Zone & Gérant</th>
                        <th class="p-4 text-xs font-black text-blue-600 uppercase tracking-widest text-center border-l border-gray-100">Capital Octroyé</th>
                        <th class="p-4 text-xs font-black text-purple-600 uppercase tracking-widest text-center border-l border-gray-100">Intérêts Générés</th>
                        <th class="p-4 text-xs font-black text-green-600 uppercase tracking-widest text-center border-l border-gray-100">Remboursements</th>
                        <th class="p-4 text-xs font-black text-gray-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($zones as $zone)
                        <tr class="hover:bg-indigo-50/30 transition-colors group">
                            <td class="p-4">
                                <div class="font-bold text-gray-900">{{ $zone->nom }}</div>
                                <div class="text-xs text-gray-500 flex items-center mt-1">
                                    <x-heroicon-s-user class="w-3 h-3 mr-1" />
                                    {{ $zone->gerant->nom ?? 'Non assigné' }}
                                </div>
                            </td>

                            {{-- Capital --}}
                            <td class="p-4 border-l border-gray-50 bg-blue-50/10">
                                <div class="flex flex-col items-center">
                                    <span class="text-sm font-black text-gray-800 font-mono">{{ number_format_fr($zone->total_capital_usd, 'USD') }} $</span>
                                    <span class="text-sm text-blue-500 font-bold font-mono">{{ number_format_fr($zone->total_capital_cdf, 'CDF') }} FC</span>
                                </div>
                            </td>

                            {{-- Intérêts --}}
                            <td class="p-4 border-l border-gray-50 bg-purple-50/10">
                                <div class="flex flex-col items-center">
                                    <span class="text-sm font-black text-gray-800 font-mono">{{ number_format_fr($zone->total_interet_usd, 'USD') }} $</span>
                                    <span class="text-sm text-purple-500 font-bold font-mono">{{ number_format_fr($zone->total_interet_cdf, 'CDF') }} FC</span>
                                </div>
                            </td>

                            {{-- Remboursements --}}
                            <td class="p-4 border-l border-gray-50 bg-green-50/10">
                                <div class="flex flex-col items-center">
                                    <span class="text-sm font-black text-green-700 font-mono">{{ number_format_fr($zone->total_rembourse_usd, 'USD') }} $</span>
                                    <span class="text-sm text-green-500 font-bold font-mono">{{ number_format_fr($zone->total_rembourse_cdf, 'CDF') }} FC</span>
                                </div>
                            </td>

                            <td class="p-4 text-right">
                                <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('agences.zones.show', $zone->uuid) }}" class="p-2 bg-white border border-gray-200 rounded-lg shadow-sm hover:text-indigo-600">
                                        <x-heroicon-o-chart-bar class="w-5 h-5" />
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-12 text-center text-gray-400 italic">
                                Aucune donnée disponible pour cette période.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div>{{ $zones->links() }}</div>
</div>