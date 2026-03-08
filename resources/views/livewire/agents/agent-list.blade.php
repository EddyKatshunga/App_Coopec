<div class="space-y-6">
    {{-- Header & Filtres --}}
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="p-3 bg-indigo-600 rounded-lg text-white shadow-indigo-200 shadow-lg">
                <x-heroicon-s-users class="w-6 h-6" />
            </div>
            <div>
                <h2 class="text-2xl font-black text-gray-900 leading-none">Agents</h2>
                <p class="text-sm text-gray-500 mt-1">Équipe de l'agence</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            {{-- Recherche --}}
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-600">
                    <x-heroicon-o-magnifying-glass class="w-5 h-5 transition-colors" />
                </div>
                <input wire:model.live.debounce.300ms="search" 
                       type="text" 
                       placeholder="Rechercher un membre..." 
                       class="pl-10 pr-4 py-2.5 bg-gray-50 border-none rounded-xl focus:ring-2 focus:ring-indigo-500 w-full sm:w-64 text-sm transition-all shadow-sm">
            </div>

            {{-- Filtre Agence (Level 6 uniquement) --}}
            @can('can.level6')
                <div class="flex items-center bg-gray-50 rounded-xl px-3 border border-transparent focus-within:border-indigo-500 transition-all">
                    <x-heroicon-o-building-office-2 class="w-5 h-5 text-gray-400" />
                    <select wire:model.live="agence_id" class="bg-transparent border-none text-sm focus:ring-0 py-2.5 pr-8">
                        <option value="">-- Choisir une agence --</option>
                        @foreach($agences as $agence)
                            <option value="{{ $agence->id }}">{{ $agence->nom }}</option>
                        @endforeach
                    </select>
                </div>
            

            <a href="{{ route('membre.index') }}" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition-all shadow-md shadow-indigo-100">
                <x-heroicon-s-plus class="w-5 h-5 mr-2" />
                Nouveau
            </a>
            @endcan
        </div>
    </div>

    {{-- Grille d'Agents --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($agents as $agent)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden group">
                <div class="p-6">
                    {{-- Avatar & Rôle --}}
                    <div class="flex justify-between items-start mb-6">
                        <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-xl font-bold shadow-lg shadow-indigo-100">
                            {{ substr($agent->membre->user->name ?? 'A', 0, 1) }}
                        </div>
                        <div class="flex flex-col items-end">
                            @foreach($agent->membre->user->roles as $role)
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-100 mb-1">
                                    {{ $role->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    {{-- Info --}}
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-gray-900 group-hover:text-indigo-600 transition-colors truncate">
                            {{ $agent->membre->user->name }}
                        </h3>
                        <div class="flex items-center text-sm text-gray-500 mt-1">
                            <x-heroicon-o-envelope class="w-4 h-4 mr-2" />
                            <span class="truncate">{{ $agent->membre->user->email }}</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-400 mt-1">
                            <x-heroicon-o-building-office class="w-4 h-4 mr-2" />
                            <span>{{ $agent->agence->nom ?? '—' }}</span>
                        </div>
                    </div>

                    {{-- Action --}}
                    <div class="pt-4 border-t border-gray-50">
                        <a href="{{ route('agent.show', $agent->uuid ?? $agent->id) }}" 
                           class="w-full flex items-center justify-center px-4 py-2.5 bg-gray-50 text-gray-700 font-bold rounded-xl hover:bg-indigo-600 hover:text-white transition-all group/btn" 
                           wire:navigate>
                            Profil complet
                            <x-heroicon-m-chevron-right class="w-4 h-4 ml-2 transform group-hover/btn:translate-x-1 transition-transform" />
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 bg-white rounded-2xl border-2 border-dashed border-gray-200 flex flex-col items-center">
                <div class="p-4 bg-gray-50 rounded-full mb-4">
                    <x-heroicon-o-user-group class="w-12 h-12 text-gray-300" />
                </div>
                <h3 class="text-lg font-bold text-gray-900">Aucun agent trouvé</h3>
                <p class="text-gray-500">Essayez de modifier vos critères de recherche.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-8">
        {{ $agents->links() }}
    </div>
</div>