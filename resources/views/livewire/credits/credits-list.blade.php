<div class="p-4 space-y-6 bg-gray-50 min-h-screen">
    
    {{-- Section Filtres --}}
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" wire:model.live.debounce.500ms="search" placeholder="Rechercher membre ou n°..." class="input input-bordered w-full rounded-xl">
            
            <select wire:model.live="zone_id" class="select select-bordered w-full rounded-xl">
                <option value="">Toutes les zones</option>
                @foreach($zones as $zone) <option value="{{ $zone->id }}">{{ $zone->nom }}</option> @endforeach
            </select>

            <select wire:model.live="statut" class="select select-bordered w-full rounded-xl">
                <option value="">Tous les statuts</option>
                <option value="en_cours">En cours</option>
                <option value="en_retard">En retard</option>
                <option value="termine">Terminé</option>
                <option value="termine_en_retard">Terminé en retard</option>
                <option value="termine_negocie">Négocié</option>
            </select>

            <div class="flex gap-2">
                <input type="date" wire:model.live="date_debut" class="input input-bordered w-full rounded-xl text-xs">
                <input type="date" wire:model.live="date_fin" class="input input-bordered w-full rounded-xl text-xs">
            </div>
        </div>
    </div>

    {{-- Grille de Crédits --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" wire:key="credits-list-{{ auth()->id() }}">
        @forelse($credits as $credit)
            @php
                $situation = $credit->getSituationActuelle();
            @endphp
            <div 
                wire:key="credit-card-{{ $credit->id }}"
                x-data="{ isOpen: false }"
                class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden transition-all"
                :class="isOpen ? 'ring-2 ring-blue-500 shadow-md' : ''"
            >
                <div class="p-5">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 font-bold">
                                {{ strtoupper(substr($credit->user->name ?? '?', 0, 1)) }}
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 leading-tight">{{ $credit->user->name }}</h3>
                                <p class="text-[10px] font-mono text-gray-500 uppercase">{{ $credit->numero_credit }} • {{ $credit->zone->nom }}</p>
                            </div>
                        </div>
                        
                        <span class="text-[9px] px-2 py-1 rounded-full font-bold uppercase 
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

                    <div class="mt-4 flex justify-between items-end">
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Total dû (Pénalités incl.)</p>
                            <p class="text-xl font-black text-gray-900">
                                {{ number_format($situation['total_a_payer'], 2) }} <span class="text-xs font-normal">{{ $credit->monnaie }}</span>
                            </p>
                        </div>
                        <button @click="isOpen = !isOpen" type="button" class="p-2 hover:bg-gray-100 rounded-full transition-all" :class="isOpen ? 'rotate-180 text-blue-600' : 'text-gray-400'">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Détails Accordéon --}}
                <div x-show="isOpen" x-collapse x-cloak class="bg-gray-50 border-t border-gray-100 p-5 space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-white p-2 rounded-lg border border-gray-100">
                            <p class="text-[9px] text-gray-400 uppercase font-bold">Base (Cap+Int)</p>
                            <p class="text-sm font-semibold">{{ number_format($situation['reste_du_base'], 2) }}</p>
                        </div>
                        <div class="bg-white p-2 rounded-lg border border-gray-100">
                            <p class="text-[9px] text-gray-400 uppercase font-bold">Pénalités du jour</p>
                            <p class="text-sm font-semibold text-red-600">+ {{ number_format($situation['penalites_courantes'], 2) }}</p>
                        </div>
                        <div class="bg-white p-2 rounded-lg border border-gray-100">
                            <p class="text-[9px] text-gray-400 uppercase font-bold">Retard effectif</p>
                            <p class="text-sm font-semibold">{{ $situation['jours_retard_courants'] }} jours</p>
                        </div>
                        <div class="bg-white p-2 rounded-lg border border-gray-100">
                            <p class="text-[9px] text-gray-400 uppercase font-bold">Échéance prévue</p>
                            <p class="text-sm font-semibold">{{ $credit->date_fin_prevue->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    <div class="flex gap-2 pt-2">
                        <a href="{{ route('credit.show', $credit) }}" wire:navigate class="flex-1 bg-white border border-gray-200 text-gray-700 py-2 rounded-xl text-xs font-bold text-center hover:bg-gray-100 transition">
                            👁️ Voir détails
                        </a>
                        @if($situation['total_a_payer'] > 0)
                            <a href="{{ route('remboursement.create', $credit) }}" wire:navigate class="flex-1 bg-blue-600 text-white py-2 rounded-xl text-xs font-bold text-center hover:bg-blue-700 shadow-sm transition">
                                💸 Encaisser
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center bg-white rounded-2xl border-2 border-dashed border-gray-200">
                <p class="text-gray-400 font-medium">Aucun crédit ne correspond à votre recherche.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $credits->links() }}
    </div>
</div>