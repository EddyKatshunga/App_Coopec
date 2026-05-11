<div class="p-6 bg-white rounded-2xl shadow-lg space-y-6">

    {{-- Titre et bouton d'ajout --}}
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Liste des membres</h1>
            <p class="text-sm text-gray-500">Recherche, filtrage et gestion des membres</p>
        </div>
        {{-- NOUVEAU BOUTON IMPRIMER --}}
        <a href="{{ route('membres.print-all') }}" target="_blank" class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition shadow-sm">
            <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            Imprimer la liste
        </a>
        <a href="{{ route('membre.create') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-900 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nouveau membre
        </a>
    </div>

    {{-- Section Filtres --}}
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
            
            {{-- Filtre Agence --}}
            @can('can.level4')
            <div class="sm:col-span-2 lg:col-span-1">
                <div class="flex items-center bg-gray-50 rounded-lg px-3 border border-gray-200 focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 transition-all">
                    <x-heroicon-o-building-office-2 class="w-5 h-5 text-gray-400 shrink-0" />
                    <select wire:model.live="agence_id" class="w-full bg-transparent border-none text-sm focus:ring-0 py-2.5 pr-8">
                        @foreach($agences as $agence)
                            <option value="{{ $agence->id }}">{{ $agence->nom }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @endcan

            {{-- Recherche --}}
            <div class="@can('can.level4') lg:col-span-2 @else lg:col-span-3 @endcan">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400" />
                    </span>
                    <input type="text" wire:model.live.debounce.500ms="search" 
                        placeholder="Nom, email ou N°..." 
                        class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none">
                </div>
            </div>

            {{-- Sexe --}}
            <div>
                <select wire:model.live="sexe" class="w-full bg-gray-50 border border-gray-200 rounded-lg text-sm py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none">
                    <option value="">Tous les sexes</option>
                    <option value="M">Masculin</option>
                    <option value="F">Féminin</option>
                </select>
            </div>

            {{-- Qualité --}}
            <div>
                <select wire:model.live="qualite" class="w-full bg-gray-50 border border-gray-200 rounded-lg text-sm py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none">
                    <option value="">Toutes qualités</option>
                    <option value="Auxiliaire">Auxiliaire</option>
                    <option value="Effectif">Effectif</option>
                </select>
            </div>

            {{-- Date --}}
            <div>
                <input type="date" wire:model.live="dateFrom" 
                    class="w-full bg-gray-50 border border-gray-200 rounded-lg text-sm py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none">
            </div>

            {{-- Bouton Réinitialiser --}}
            <div class="sm:col-span-2 lg:col-span-6 xl:col-span-1 xl:ml-auto">
                <button wire:click="resetFilters" 
                    class="w-full inline-flex items-center justify-center px-4 py-2.5 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                    <x-heroicon-o-arrow-path class="w-4 h-4 mr-2 text-gray-500" />
                    Effacer
                </button>
            </div>
        </div>
    </div>

    {{-- Grille de cartes --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($membres as $membre)
            <div 
                wire:key="membre-card-{{ $membre->id }}"
                x-data="{ 
                    isOpen: false,
                    toggle() {
                        if (!this.isOpen) {
                            $dispatch('close-others', { id: {{ $membre->id }} });
                        }
                        this.isOpen = !this.isOpen;
                    }
                }"
                @close-others.window="if ($event.detail.id !== {{ $membre->id }}) isOpen = false"
                class="bg-white rounded-2xl shadow-sm border border-gray-100 transition-all duration-300 overflow-hidden h-fit"
                :class="isOpen ? 'ring-2 ring-blue-500 shadow-lg' : 'hover:shadow-md'"
            >
                {{-- Contenu principal (toujours visible) --}}
                <div class="p-5">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center gap-3">
                            {{-- Avatar / initiale --}}
                            <div class="h-10 w-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 font-bold flex-shrink-0">
                                @if($membre->user?->photo_path)
                                    <img src="{{ $membre->user->photo_path }}" alt="Photo" class="h-10 w-10 rounded-full object-cover">
                                @else
                                    @php
                                        $sexe = $membre->sexe;
                                        $isHomme = $sexe === 'M';
                                        $isFemme = $sexe === 'F';
                                        $bgColor = $isHomme ? 'bg-blue-100' : ($isFemme ? 'bg-pink-100' : 'bg-gradient-to-br from-indigo-100 to-purple-100');
                                        $textColor = $isHomme ? 'text-blue-600' : ($isFemme ? 'text-pink-600' : 'text-indigo-600');
                                    @endphp
                                    <div class="h-10 w-10 rounded-full {{ $bgColor }} flex items-center justify-center">
                                        <svg class="h-6 w-6 {{ $textColor }}" fill="currentColor" viewBox="0 0 20 20">
                                            @if($isHomme)
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                                <path d="M15 4a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 11-2 0V6.414l-2.293 2.293a1 1 0 11-1.414-1.414L16.586 5H15a1 1 0 01-1-1z"/>
                                            @elseif($isFemme)
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                                <path d="M5 5a1 1 0 011-1h2a1 1 0 010 2H7v1a1 1 0 01-2 0V5z"/>
                                            @else
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                            @endif
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <h3 class="font-bold text-gray-900 leading-tight truncate">{{ $membre->nom }}</h3>
                                <p class="text-xs font-mono text-gray-500">{{ $membre->numero_identification }}</p>
                            </div>
                        </div>

                        {{-- Bouton d'ouverture/fermeture --}}
                        <button 
                            type="button"
                            @click="toggle()" 
                            class="p-2 hover:bg-gray-100 rounded-full transition-all duration-200"
                            :class="isOpen ? 'rotate-180 bg-blue-50 text-blue-600' : 'text-gray-400'"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Informations succinctes --}}
                    <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Email</span>
                            <p class="font-medium text-gray-700 truncate">{{ $membre->user->email ?? '—' }}</p>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Qualité</span>
                            <p class="font-medium text-gray-700">{{ $membre->qualite }}</p>
                        </div>
                    </div>
                </div>

                {{-- Détails supplémentaires (accordéon) --}}
                <div 
                    x-show="isOpen" 
                    x-collapse
                    x-cloak
                    class="bg-gray-50 border-t border-gray-100 p-5 space-y-4"
                >
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white p-3 rounded-xl border border-gray-200">
                            <p class="text-[10px] text-gray-400 uppercase font-bold text-center">Sexe</p>
                            <p class="text-sm font-bold text-gray-700 text-center">{{ $membre->sexe === 'M' ? 'Masculin' : 'Féminin' }}</p>
                        </div>
                        <div class="bg-white p-3 rounded-xl border border-gray-200">
                            <p class="text-[10px] text-gray-400 uppercase font-bold text-center">Adhésion</p>
                            <p class="text-sm font-bold text-gray-700 text-center">{{ $membre->date_adhesion?->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    <div class="bg-white p-3 rounded-xl border border-gray-200">
                        <p class="text-[10px] text-gray-400 uppercase font-bold text-center">Statut & Engagement</p>
                        <div class="flex flex-wrap justify-center gap-1 mt-1">
                            @if ($membre->agent)
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">Agent</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Membre</span>
                            @endif

                            {{-- Signalement du crédit en cours --}}
                            @if($membre->hasActiveCredit())
                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700 font-bold animate-pulse">
                                    ⚠️ Crédit en cours
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Actions rapides --}}
                    <div class="flex items-center justify-between pt-2 gap-2">
                        <a href="{{ route('membre.show', $membre) }}" wire:navigate class="flex-1 bg-white border border-gray-200 text-gray-700 py-2 rounded-lg text-[10px] font-bold text-center hover:bg-gray-50 transition uppercase">
                            Détails
                        </a>
                        <a href="{{ route('membre.edit', $membre) }}" wire:navigate class="flex-1 bg-amber-500 text-white py-2 rounded-lg text-[10px] font-bold text-center hover:bg-amber-600 transition uppercase">
                            Modifier
                        </a>

                        @can('can.level4')
                            @if(!$membre->hasActiveCredit())
                                {{-- Le bouton ne s'affiche que s'il n'y a pas de crédit actif --}}
                                <a href="{{ route('credit.pret.create', $membre) }}" class="flex-1 bg-green-600 text-white py-2 rounded-lg text-[10px] font-bold text-center hover:bg-green-700 transition uppercase">
                                    + Crédit
                                </a>
                            @else
                                {{-- Optionnel : Un bouton grisé ou un lien vers le crédit actuel --}}
                                <button disabled class="flex-1 bg-gray-200 text-gray-400 py-2 rounded-lg text-[10px] font-bold text-center cursor-not-allowed uppercase">
                                    Crédit Bloqué
                                </button>
                            @endif
                        @endcan
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl p-12 text-center border-2 border-dashed border-gray-200 text-gray-400">
                <div class="text-4xl mb-3">👤</div>
                <h3 class="font-medium text-gray-600">Aucun membre trouvé</h3>
                <p class="text-sm">Essayez d'ajuster vos filtres de recherche.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $membres->links() }}
    </div>
</div>