<div class="p-4 space-y-6 bg-gray-50 min-h-screen">
    
    {{-- SECTION FILTRES --}}
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <span class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </span>
                Gestion des Crédits
            </h2>
            <a href="{{ route('membre.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-bold transition shadow-sm flex items-center gap-2">
                + Nouvel Octroi
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div class="lg:col-span-2">
                <input type="text" wire:model.live.debounce.500ms="search" placeholder="Membre ou n° de dossier..." class="w-full border-gray-200 rounded-xl text-sm focus:ring-blue-500">
            </div>
            
            @can('can.level6')
            <select wire:model.live="selected_agence_id" class="border-gray-200 rounded-xl text-sm focus:ring-blue-500">
                <option value="">Toutes les agences</option>
                @foreach($agences as $agence) <option value="{{ $agence->id }}">{{ $agence->nom }}</option> @endforeach
            </select>
            @endcan

            <select wire:model.live="zone_id" class="border-gray-200 rounded-xl text-sm focus:ring-blue-500">
                <option value="">Toutes les zones</option>
                @foreach($zones as $zone) <option value="{{ $zone->id }}">{{ $zone->nom }}</option> @endforeach
            </select>

            <select wire:model.live="devise" class="border-gray-200 rounded-xl text-sm focus:ring-blue-500">
                <option value="">Toutes les devises</option>
                <option value="USD">USD</option>
                <option value="CDF">CDF</option>
            </select>

            <select wire:model.live="actif" class="border-gray-200 rounded-xl text-sm focus:ring-blue-500">
                <option value="">📋 Tous les crédits</option>
                <option value="1">🟢 Actifs (en cours / retard)</option>
                <option value="0">⚪ Inactifs (terminés)</option>
            </select>

            <select wire:model.live="statut" class="border-gray-200 rounded-xl text-sm focus:ring-blue-500">
                <option value="">📂 Tous les statuts</option>
                <option value="en_cours">⏳ En cours</option>
                <option value="en_retard">⚠️ En retard</option>
                <option value="termine">✅ Terminé</option>
                <option value="termine_en_retard">🟣 Terminé (retard)</option>
                <option value="termine_negocie">🤝 Négocié</option>
            </select>

            @can('can.level4')
            <div class="flex items-center gap-2 px-3 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                <input type="checkbox" wire:model.live="all_agents" id="all_agents_credit" class="rounded text-blue-600 focus:ring-blue-500">
                <label for="all_agents_credit" class="text-[10px] font-bold text-gray-600 uppercase cursor-pointer leading-tight">Voir toute l'agence</label>
            </div>
            @endcan
        </div>
    </div>

    {{-- STATISTIQUES --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wider font-bold">Nombre de crédits</p>
                <p class="text-2xl font-black text-gray-900">{{ $stats['total'] }}</p>
            </div>
            <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wider font-bold">Capital restant dû</p>
                <p class="text-2xl font-black text-gray-900">{{ number_format($stats['total_capital_restant'], 2, ',', ' ') }} <span class="text-sm font-normal text-gray-400">CDF</span></p>
            </div>
            <div class="p-2 bg-amber-50 rounded-lg text-amber-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 flex items-center justify-between">
            <div class="p-2 bg-red-50 rounded-lg text-red-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    {{-- TABLEAU DES CRÉDITS --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Membre</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">N° Crédit</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Zone / Agence</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Capital initial / Intérêt</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Échéance</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Remboursé</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Reste à payer</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Statut</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($credits as $credit)
                        @php $situation = $credit->getSituationActuelle(); @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            {{-- Membre --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="flex-shrink-0 h-8 w-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 font-black text-sm">
                                        {{ strtoupper(substr($credit->user->name ?? '?', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900 truncate max-w-[120px]">{{ $credit->user->name }}</div>
                                        <div class="text-[10px] text-gray-400 truncate max-w-[120px]">{{ $credit->creator->name ?? '—' }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- N° Crédit --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="text-xs font-mono font-bold text-blue-700 bg-blue-50 px-2 py-1 rounded-lg">
                                    {{ $credit->numero_credit }}
                                </span>
                            </td>

                            {{-- Zone / Agence --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $credit->zone->nom }}</div>
                                <div class="text-xs text-gray-400">{{ $credit->agence->nom }}</div>
                            </td>

                            {{-- Capital initial & Intérêt --}}
                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                <div class="text-sm font-semibold text-gray-900">
                                    {{ number_format($credit->capital, 2, ',', ' ') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    + {{ number_format($credit->interet, 2, ',', ' ') }} int.
                                </div>
                                <div class="text-[10px] text-gray-400 mt-0.5">
                                    Total: {{ number_format($credit->total, 2, ',', ' ') }} {{ $credit->monnaie }}
                                </div>
                            </td>

                            {{-- Échéance --}}
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                {{ $credit->date_fin_prevue->format('d/m/Y') }}
                                @if($situation['jours_retard_courants'] > 0)
                                    <span class="block text-xs text-red-500 font-medium">+{{ $situation['jours_retard_courants'] }}j</span>
                                @endif
                            </td>

                            {{-- Total déjà payé --}}
                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                                {{ number_format($credit->total_remboursement, 2, ',', ' ') }}
                                <span class="text-gray-400 text-xs">{{ $credit->monnaie }}</span>
                            </td>

                            {{-- Total dû (avec pénalités) --}}
                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                <div class="text-sm font-black text-gray-900">
                                    {{ number_format($credit->reste_du, 2, ',', ' ') }}
                                    <span class="text-gray-400 text-xs font-normal">{{ $credit->monnaie }}</span>
                                </div>
                                @if($situation['penalites_courantes'] > 0)
                                    <div class="text-[10px] text-red-600 font-medium">
                                        + {{ number_format($situation['penalites_courantes'], 2, ',', ' ') }} pén.
                                    </div>
                                @else
                                    <div class="text-[10px] text-gray-400">—</div>
                                @endif
                            </td>

                            {{-- Statut --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide
                                    @switch($credit->statut)
                                        @case('en_cours') bg-blue-100 text-blue-800 @break
                                        @case('en_retard') bg-red-100 text-red-800 @break
                                        @case('termine') bg-green-100 text-green-800 @break
                                        @case('termine_en_retard') bg-purple-100 text-purple-800 @break
                                        @case('termine_negocie') bg-gray-700 text-white @break
                                        @default bg-gray-100 text-gray-800
                                    @endswitch">
                                    {{ str_replace('_', ' ', $credit->statut) }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('credit.show', $credit) }}" 
                                       class="inline-flex items-center px-2 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-bold text-gray-700 hover:bg-gray-100 transition shadow-sm"
                                       title="Voir détails">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    @if(in_array($credit->statut, ['en_cours', 'en_retard']))
                                        <a href="{{ route('remboursement.create', $credit) }}" 
                                           class="inline-flex items-center px-2 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-bold hover:bg-blue-700 shadow-sm transition"
                                           title="Encaisser">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-16 text-center">
                                <div class="text-gray-300 mb-4">
                                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-500 font-bold">Aucun dossier de crédit trouvé</p>
                                <p class="text-gray-400 text-sm">Ajustez vos filtres ou créez un nouvel octroi.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $credits->links() }}
    </div>
</div>