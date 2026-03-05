<div class="p-4 space-y-6 bg-gray-50 min-h-screen">
    
    {{-- SECTION FILTRES --}}
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <span class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
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
            
            @can('agence.view.all')
            <select wire:model.live="selected_agence_id" class="border-gray-200 rounded-xl text-sm focus:ring-blue-500">
                <option value="">Toutes les agences</option>
                @foreach($agences as $agence) <option value="{{ $agence->id }}">{{ $agence->nom }}</option> @endforeach
            </select>
            @endcan

            <select wire:model.live="zone_id" class="border-gray-200 rounded-xl text-sm focus:ring-blue-500">
                <option value="">Toutes les zones</option>
                @foreach($zones as $zone) <option value="{{ $zone->id }}">{{ $zone->nom }}</option> @endforeach
            </select>

            <select wire:model.live="statut" class="border-gray-200 rounded-xl text-sm focus:ring-blue-500">
                <option value="">Tous les statuts</option>
                <option value="en_cours">En cours</option>
                <option value="en_retard">⚠️ En retard</option>
                <option value="termine">✅ Terminé</option>
                <option value="termine_en_retard">🟣 Terminé (retard)</option>
                <option value="termine_negocie">🤝 Négocié</option>
            </select>

            @can('agence.operations.view')
            <div class="flex items-center gap-2 px-3 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                <input type="checkbox" wire:model.live="all_agents" id="all_agents_credit" class="rounded text-blue-600 focus:ring-blue-500">
                <label for="all_agents_credit" class="text-[10px] font-bold text-gray-600 uppercase cursor-pointer leading-tight">Voir toute l'agence</label>
            </div>
            @endcan
        </div>
    </div>

    {{-- GRILLE DES CRÉDITS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" wire:key="credits-grid">
        @forelse($credits as $credit)
            @php $situation = $credit->getSituationActuelle(); @endphp
            <div 
                wire:key="card-{{ $credit->id }}"
                x-data="{ isOpen: false }"
                class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden transition-all hover:shadow-md"
                :class="isOpen ? 'ring-2 ring-blue-500' : ''"
            >
                <div class="p-5">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center gap-3">
                            <div class="h-12 w-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 font-black text-lg">
                                {{ strtoupper(substr($credit->user->name ?? '?', 0, 1)) }}
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 leading-tight">{{ $credit->user->name }}</h3>
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-mono text-blue-600 font-bold uppercase">{{ $credit->numero_credit }}</span>
                                    <span class="text-[10px] text-gray-400 font-medium uppercase">{{ $credit->zone->nom }} • {{ $credit->agence->nom }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex flex-col items-end gap-1">
                            <span class="text-[9px] px-2 py-1 rounded-lg font-bold uppercase 
                                @switch($credit->statut)
                                    @case('en_cours') bg-blue-100 text-blue-700 @break
                                    @case('en_retard') bg-red-100 text-red-700 @break
                                    @case('termine') bg-green-100 text-green-700 @break
                                    @case('termine_en_retard') bg-purple-100 text-purple-700 @break
                                    @case('termine_negocie') bg-gray-700 text-white @break
                                    @default bg-gray-100 text-gray-700
                                @endswitch">
                                {{ str_replace('_', ' ', $credit->statut) }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-between items-end">
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest mb-1">Total restant dû</p>
                            <p class="text-2xl font-black text-gray-900 tracking-tight">
                                {{ number_format($situation['total_a_payer'], 2, ',', ' ') }} <span class="text-xs font-bold text-gray-400">{{ $credit->monnaie }}</span>
                            </p>
                        </div>
                        <button @click="isOpen = !isOpen" type="button" class="p-2 hover:bg-gray-50 rounded-lg transition-all border border-transparent" :class="isOpen ? 'rotate-180 bg-blue-50 text-blue-600' : 'text-gray-300'">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </div>
                </div>

                {{-- DÉTAILS ACCORDÉON --}}
                <div x-show="isOpen" x-collapse x-cloak class="bg-gray-50 border-t border-gray-100 p-5 space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-white p-3 rounded-xl border border-gray-200/50 shadow-sm text-center">
                            <p class="text-[9px] text-gray-400 uppercase font-bold mb-1">Principal + Int</p>
                            <p class="text-sm font-bold text-gray-700">{{ number_format($situation['reste_du_base'], 2, ',', ' ') }}</p>
                        </div>
                        <div class="bg-white p-3 rounded-xl border border-gray-200/50 shadow-sm text-center">
                            <p class="text-[9px] text-gray-400 uppercase font-bold mb-1">Pénalités</p>
                            <p class="text-sm font-bold {{ $situation['penalites_courantes'] > 0 ? 'text-red-600' : 'text-gray-700' }}">
                                + {{ number_format($situation['penalites_courantes'], 2, ',', ' ') }}
                            </p>
                        </div>
                        <div class="bg-white p-3 rounded-xl border border-gray-200/50 shadow-sm text-center">
                            <p class="text-[9px] text-gray-400 uppercase font-bold mb-1">Retard</p>
                            <p class="text-sm font-bold {{ $situation['jours_retard_courants'] > 0 ? 'text-orange-600' : 'text-gray-700' }}">
                                {{ $situation['jours_retard_courants'] }} jours
                            </p>
                        </div>
                        <div class="bg-white p-3 rounded-xl border border-gray-200/50 shadow-sm text-center">
                            <p class="text-[9px] text-gray-400 uppercase font-bold mb-1">Échéance</p>
                            <p class="text-sm font-bold text-gray-700">{{ $credit->date_fin_prevue->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    <div class="flex gap-2 pt-2">
                        <a href="{{ route('credit.show', $credit) }}" class="flex-1 bg-white border border-gray-200 text-gray-700 py-2.5 rounded-xl text-[11px] font-black uppercase text-center hover:bg-gray-100 transition shadow-sm">
                            Détails complets
                        </a>
                        @if($situation['total_a_payer'] > 0)
                            <a href="{{ route('remboursement.create', $credit) }}" class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl text-[11px] font-black uppercase text-center hover:bg-blue-700 shadow-md transition">
                                Encaisser
                            </a>
                        @endif
                    </div>
                    <div class="text-center">
                        <p class="text-[9px] text-gray-400 italic">Octroyé par {{ $credit->creator->name ?? 'Inconnu' }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center bg-white rounded-3xl border-2 border-dashed border-gray-200">
                <div class="text-gray-300 mb-4">
                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <p class="text-gray-500 font-bold">Aucun dossier de crédit trouvé</p>
                <p class="text-gray-400 text-sm">Ajustez vos filtres ou créez un nouvel octroi.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $credits->links() }}
    </div>
</div>