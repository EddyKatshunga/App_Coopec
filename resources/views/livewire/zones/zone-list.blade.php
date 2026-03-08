{{-- views/livewire/zones/zone-list.blade.php --}}
<div class="space-y-6">
    {{-- Header & Filtre --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">Zones Géographiques</h2>
            <p class="text-sm text-gray-500">Visualisation des secteurs de l'agence</p>
        </div>

        @can('can.level6')
            <div class="flex items-center space-x-2">
                <x-heroicon-o-building-office-2 class="w-5 h-5 text-gray-400" />
                <select wire:model.live="selectedAgenceId" class="rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
                     <option value="">-- Choisir une agence --</option>
                    @foreach($agences as $agence)
                        <option value="{{ $agence->id }}">{{ $agence->nom }}</option>
                    @endforeach
                </select>
            </div>
        @endcan
    </div>

    {{-- Grille de Cartes --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($zones as $zone)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300 overflow-hidden group">
                <div class="p-5">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300">
                            <x-heroicon-o-map-pin class="w-6 h-6" />
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                            {{ $zone->code }}
                        </span>
                    </div>

                    <h3 class="text-lg font-bold text-gray-900 truncate mb-1">{{ $zone->nom }}</h3>
                    
                    <div class="flex items-center text-sm text-gray-500 mb-4">
                        <x-heroicon-s-user-circle class="w-4 h-4 mr-2 text-gray-400" />
                        <span class="italic">
                            {{ $zone->gerant->nom ?? 'Aucun gérant assigné' }}
                        </span>
                    </div>

                    <div class="pt-4 border-t border-gray-50 flex items-center justify-between">
                        <div class="flex -space-x-2">
                            {{-- Placeholder pour des stats rapides si besoin --}}
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Secteur actif</span>
                        </div>
                        
                        <div class="flex gap-2">
                            <a href="{{ route('agences.zones.edit', $zone->uuid) }}" class="p-2 text-gray-400 hover:text-indigo-600 transition-colors" title="Modifier">
                                <x-heroicon-o-pencil-square class="w-5 h-5" />
                            </a>
                            <a href="{{ route('agences.zones.show', $zone->uuid) }}" 
                               class="inline-flex items-center px-3 py-2 text-sm font-semibold text-indigo-700 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors group/btn">
                                Voir plus
                                <x-heroicon-m-arrow-right class="w-4 h-4 ml-1 transform group-hover/btn:translate-x-1 transition-transform" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 bg-white rounded-xl border-2 border-dashed border-gray-200 flex flex-col items-center">
                <x-heroicon-o-map class="w-12 h-12 text-gray-300 mb-3" />
                <p class="text-gray-500">Aucune zone trouvée pour cette agence.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $zones->links() }}
    </div>

</div>