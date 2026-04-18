<div class="space-y-4 p-4">
    {{-- ENTÊTE --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <span class="p-2 bg-red-50 text-red-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </span>
                Registre des Dépenses
            </h2>
            
            <a href="{{ route('depenses.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Nouvelle Dépense
            </a>
        </div>

        <hr class="my-5 border-gray-100">

        {{-- FILTRES --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
            <div class="relative lg:col-span-2">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Rechercher libellé ou bénéficiaire..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>

            @can('can.level6')
            <select wire:model.live="selected_agence_id" class="border-gray-200 rounded-lg text-sm focus:ring-blue-500">
                <option value="">Toutes les agences</option>
                @foreach($agences as $agence)
                    <option value="{{ $agence->id }}">{{ $agence->nom }}</option>
                @endforeach
            </select>
            @endcan

            <input type="date" wire:model.live="date_debut" class="border-gray-200 rounded-lg text-sm focus:ring-blue-500">
            <input type="date" wire:model.live="date_fin" class="border-gray-200 rounded-lg text-sm focus:ring-blue-500">

            @can('can.level4')
            <div class="flex items-center gap-2 px-3 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                <input type="checkbox" wire:model.live="all_agents" id="all_agents" class="rounded text-red-600 focus:ring-red-500">
                <label for="all_agents" class="text-[10px] font-bold text-gray-600 uppercase cursor-pointer">Toute l'agence</label>
            </div>
            @endcan
        </div>
    </div>

    {{-- TABLEAU --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Libellé & Type</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Bénéficiaire</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right">Montant</th>
                        @if($all_agents || auth()->user()->can('can.level6'))
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Auteur / Agence</th>
                        @endif
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($depenses as $depense)
                        <tr class="hover:bg-red-50/20 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($depense->date_operation)->format('d/m/Y') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-800">{{ $depense->libelle }}</div>
                                <div class="text-xs text-blue-600">{{ $depense->typeDepense->nom ?? 'Général' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600">{{ $depense->beneficiaire->user->name ?? 'Non spécifié' }}</span>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <span class="text-sm font-black text-red-600">
                                    {{ number_format($depense->montant, 2, ',', ' ') }} {{ $depense->monnaie }}
                                </span>
                            </td>

                            @if($all_agents || auth()->user()->can('can.level6'))
                            <td class="px-6 py-4 whitespace-nowrap border-l border-gray-50">
                                <div class="text-xs font-medium text-gray-700">{{ $depense->creator->name ?? 'Système' }}</div>
                                <div class="text-[10px] text-gray-400 font-bold uppercase">{{ $depense->agence->nom ?? '-' }}</div>
                            </td>
                            @endif

                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('depenses.show', $depense->id) }}" class="text-gray-400 hover:text-blue-600 transition-colors" wire:navigate>
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    @can('can.level4')
                                        @if($depense->canBeDeleted())
                                            <button wire:click="deleteRecord('App\\Models\\Depense', {{ $depense->id }})" 
                                                    wire:confirm="Supprimer cette dépense ?" 
                                                    class="text-gray-400 hover:text-red-600 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic">
                                Aucune dépense enregistrée.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-gray-50 border-t">
            {{ $depenses->links() }}
        </div>
    </div>
</div>