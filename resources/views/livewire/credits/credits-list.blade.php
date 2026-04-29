<div class="max-w-7xl mx-auto p-4 md:p-6 space-y-6">
    {{-- En-tête --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-8">
        <div class="space-y-1">
            <h1 class="text-2xl md:text-3xl font-black text-gray-900 tracking-tight flex items-center">
                Répertoire des crédits
            </h1>
            <p class="text-sm md:text-base text-gray-500 font-medium">
                Sélectionnez un crédit pour enregistrer un <span class="text-blue-600 font-bold text-sm italic">remboursement</span> ou consulter les détails.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('membre.index') }}" 
            class="inline-flex items-center px-5 py-2.5 bg-gray-900 text-white rounded-xl text-sm font-bold hover:bg-blue-600 transition-all duration-300 shadow-lg shadow-gray-200 group">
                <div class="bg-white/20 rounded-lg p-1 mr-2 group-hover:rotate-90 transition-transform duration-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
                Nouveau crédit
            </a>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 md:p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Recherche -->
            <div class="sm:col-span-2">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-wider mb-1">Recherche</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                    <input type="text" wire:model.live.debounce.300ms="search" 
                           class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="N° crédit ou nom du membre...">
                </div>
            </div>

            <!-- Statut -->
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-wider mb-1">Statut</label>
                <select wire:model.live="statut" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="en_cours">En cours</option>
                    <option value="termine">Terminé</option>
                </select>
            </div>

            <!-- Agence (visible pour les super admins) -->
            @if(count($agences) > 0)
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-wider mb-1">Agence</label>
                <select wire:model.live="selected_agence_id" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm">
                    <option value="">Toutes les agences</option>
                    @foreach($agences as $agence)
                        <option value="{{ $agence->id }}">{{ $agence->nom }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <!-- Zone (dépend de l'agence) -->
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-wider mb-1">Zone</label>
                <select wire:model.live="selected_zone_id" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm" 
                        {{ $zones->isEmpty() ? 'disabled' : '' }}>
                    <option value="">Toutes les zones</option>
                    @foreach($zones as $zone)
                        <option value="{{ $zone->id }}">{{ $zone->nom }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Devise -->
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-wider mb-1">Devise</label>
                <select wire:model.live="monnaie" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm">
                    <option value="">Toutes</option>
                    <option value="USD">USD</option>
                    <option value="CDF">CDF</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Liste des crédits --}}
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100">
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">N° Crédit</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Membre</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Agent</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Zone</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Montant total</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Remboursé</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Échéance</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Statut</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($credits as $credit)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="text-xs font-mono font-bold text-gray-600 bg-gray-100 px-2 py-1 rounded">#{{ $credit->numero_credit }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-800">{{ $credit->membre?->nom }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-600">{{ $credit->agent?->nom ?? '—' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-600">{{ $credit->zone?->nom ?? '—' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">
                                    {{ number_format($credit->total, 2, ',', ' ') }}
                                    <span class="text-xs font-medium text-gray-500">{{ $credit->monnaie }}</span>
                                </div>
                                <div class="text-[10px] text-gray-400">
                                    Capital: {{ number_format($credit->capital, 2, ',', ' ') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-green-600">
                                    {{ number_format($credit->total_remboursement, 2, ',', ' ') }}
                                </div>
                                <div class="w-24 mt-1 bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-green-500 h-full rounded-full" 
                                         style="width: {{ $credit->total > 0 ? ($credit->total_remboursement / $credit->total) * 100 : 0 }}%">
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm {{ $credit->date_fin_prevue->isPast() && $credit->statut === 'en_cours' ? 'text-red-600 font-bold' : 'text-gray-700' }}">
                                    {{ $credit->date_fin_prevue->format('d/m/Y') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($credit->statut === 'en_cours')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                        En cours
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        Terminé
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('credit.show', $credit->uuid) }}" 
                                   class="inline-flex items-center justify-center h-8 w-8 bg-white border border-gray-200 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all shadow-sm"
                                   title="Voir les détails">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                    <p class="text-gray-400 italic">Aucun crédit ne correspond aux filtres sélectionnés.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 bg-gray-50/30 border-t border-gray-100">
            {{ $credits->links() }}
        </div>
    </div>
</div>