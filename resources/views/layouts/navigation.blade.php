<nav x-data="{ open: false, scrolled: false }" 
     @scroll.window="scrolled = (window.pageYOffset > 10)"
     :class="{ 'bg-white/80 backdrop-blur-lg shadow-sm': scrolled, 'bg-white': !scrolled }"
     class="sticky top-0 z-50 border-b border-slate-200/80 transition-all duration-300">
    
    @php
        $user = Auth::user();
        $agence = $user->agent?->agence;
        $journeeOuverte = auth()->user()->journee_ouverte;
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 lg:h-20 transition-all duration-300">
            
            {{-- LOGO & BRANDING --}}
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 shrink-0 group">
                    <div class="relative flex items-center justify-center h-10 w-10 rounded-xl bg-gradient-to-tr from-indigo-50 to-white border border-indigo-100 shadow-sm group-hover:shadow-md transition-shadow">
                        <img src="{{ asset('images/logo1.png') }}" alt="{{ config('app.nom_entreprise') }}" class="h-7 w-auto transition-transform group-hover:scale-105">
                    </div>
                    <div class="hidden xl:flex xl:flex-col justify-center">
                        <span class="text-base font-bold bg-clip-text text-transparent bg-gradient-to-r from-slate-900 to-slate-700 tracking-tight">
                            {{ config('app.name') }}
                        </span>
                        <span class="text-[10px] font-semibold text-indigo-600 tracking-wider uppercase">
                            {{ config('app.nom_entreprise') }}
                        </span>
                    </div>
                </a>

                {{-- DESKTOP MENU --}}
                <div class="hidden lg:flex lg:items-center lg:ml-10 lg:space-x-1">
                    
                    <a href="{{ route('dashboard') }}" class="px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        Dashboard
                    </a>

                    {{-- MENU : ADMINISTRATION (Niveau 6+) --}}
                    @can('can.level4')
                    <div x-data="{ dropdownOpen: false }" @click.outside="dropdownOpen = false" class="relative">
                        <button @click="dropdownOpen = !dropdownOpen" :class="{ 'bg-slate-50 text-slate-900': dropdownOpen, 'text-slate-600 hover:bg-slate-50 hover:text-slate-900': !dropdownOpen }" class="flex items-center px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors">
                            <span class="w-2 h-2 rounded-full bg-rose-500 mr-2.5 shadow-[0_0_8px_rgba(244,63,94,0.5)]"></span>
                            Administration
                            <svg class="ml-1.5 h-4 w-4 transition-transform duration-200" :class="{'rotate-180': dropdownOpen}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </button>
                        
                        <div x-show="dropdownOpen" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                             class="absolute left-0 mt-2 w-56 rounded-2xl shadow-xl bg-white ring-1 ring-slate-900/5 py-2 z-50" style="display: none;">
                            <a href="{{ route('agents.index') }}" class="block px-5 py-2.5 text-sm text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors">Agents</a>
                            <a href="{{ route('agences.zones.index') }}" class="block px-5 py-2.5 text-sm text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors">Zones de Crédits</a>
                            
                            @can('can.level8')
                            <div class="h-px bg-slate-100 my-2"></div>
                            <a href="{{ route('agences.index') }}" class="block px-5 py-2.5 text-sm text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors">Agences</a>
                            <a href="{{ route('types-depense.index') }}" class="block px-5 py-2.5 text-sm text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors">Types Dépenses</a>
                            <a href="{{ route('types-revenu.index') }}" class="block px-5 py-2.5 text-sm text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors">Types Revenus</a>
                            <a href="{{ route('historiquesroles.index') }}" class="block px-5 py-2.5 text-sm text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors">Historique Rôles</a>
                            @endcan
                            
                            @can('can.level5')
                            <div class="h-px bg-slate-100 my-2"></div>
                            <a href="{{ route('agence.show', ['agence' => auth()->user()->agence ?? \App\Models\Agence::first()]) }}" class="block px-5 py-2.5 text-sm text-indigo-600 font-semibold hover:bg-indigo-50 transition-colors">Agence</a>
                            @endcan
                        </div>
                    </div>
                    @endcan

                    {{-- MENU : GESTION DE CAISSE (Niveau 3-5) --}}
                    @can('can.level3')
                    <div x-data="{ dropdownOpen: false }" @click.outside="dropdownOpen = false" class="relative">
                        <button @click="dropdownOpen = !dropdownOpen" :class="{ 'bg-slate-50 text-slate-900': dropdownOpen, 'text-slate-600 hover:bg-slate-50 hover:text-slate-900': !dropdownOpen }" class="flex items-center px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors">
                            <span class="w-2 h-2 rounded-full bg-amber-500 mr-2.5 shadow-[0_0_8px_rgba(245,158,11,0.5)]"></span>
                            Caisse & Flux
                            <svg class="ml-1.5 h-4 w-4 transition-transform duration-200" :class="{'rotate-180': dropdownOpen}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </button>
                        <div x-show="dropdownOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" class="absolute left-0 mt-2 w-64 rounded-2xl shadow-xl bg-white ring-1 ring-slate-900/5 py-2 z-50" style="display: none;">
                            <a href="{{ route('comptes.index') }}" class="block px-5 py-2.5 text-sm text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors">Effectuer un Retrait</a>
                            
                            @can('canTransact')
                                <a href="{{ route('depenses.create') }}" class="block px-5 py-2.5 text-sm text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors">Saisir une Dépense</a>
                                <a href="{{ route('revenus.create') }}" class="block px-5 py-2.5 text-sm text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors">Saisir un Revenu</a>
                            @endcan

                            @can('can.level4')
                                <div class="h-px bg-slate-100 my-2"></div>
                                <a href="{{ route('clotures.index') }}" class="block px-5 py-2.5 text-sm text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors">Journées précédentes</a>
                                @can('canTransact')
                                    @if($journeeOuverte)
                                        <a href="{{ route('clotures.show', ['cloture' => $journeeOuverte]) }}" class="block px-5 py-2.5 text-sm text-amber-600 font-semibold hover:bg-amber-50 transition-colors">Situation de la journée</a>
                                    @endif
                                @endcan
                            @endcan
                        </div>
                    </div>
                    @endcan

                    {{-- MENU : OPERATIONS TERRAIN (Niveau 1-3) --}}
                    @can('can.level1')
                    <div x-data="{ dropdownOpen: false }" @click.outside="dropdownOpen = false" class="relative">
                        <button @click="dropdownOpen = !dropdownOpen" :class="{ 'bg-slate-50 text-slate-900': dropdownOpen, 'text-slate-600 hover:bg-slate-50 hover:text-slate-900': !dropdownOpen }" class="flex items-center px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2.5 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                            Opérations Terrain
                            <svg class="ml-1.5 h-4 w-4 transition-transform duration-200" :class="{'rotate-180': dropdownOpen}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </button>
                        <div x-show="dropdownOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" class="absolute left-0 mt-2 w-64 rounded-2xl shadow-xl bg-white ring-1 ring-slate-900/5 py-2 z-50" style="display: none;">
                            @can('can.level4')
                                <a href="{{ route('membre.create') }}" class="block px-5 py-2.5 text-sm text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors">Nouveau membre</a>
                            @endcan

                            @can('can.level1')
                                <a href="{{ route('comptes.index') }}" class="block px-5 py-2.5 text-sm text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors">Comptes Epargnes</a>
                                @can('can.level2')
                                    <a href="{{ route('credit.pret.index') }}" class="block px-5 py-2.5 text-sm text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors">Credits</a>
                                @endcan
                                @can('can.level4')
                                    <div class="h-px bg-slate-100 my-2"></div>
                                    <a href="{{ route('membre.index') }}" class="block px-5 py-2.5 text-sm text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors">Liste des membres</a>
                                @endcan
                            @endcan
                        </div>
                    </div>
                    @endcan

                    {{-- MENU : PERSONNEL (Tout le monde) --}}
                    <div x-data="{ dropdownOpen: false }" @click.outside="dropdownOpen = false" class="relative">
                        <button @click="dropdownOpen = !dropdownOpen" :class="{ 'bg-slate-50 text-slate-900': dropdownOpen, 'text-slate-600 hover:bg-slate-50 hover:text-slate-900': !dropdownOpen }" class="flex items-center px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors">
                            <span class="w-2 h-2 rounded-full bg-blue-500 mr-2.5 shadow-[0_0_8px_rgba(59,130,246,0.5)]"></span>
                            Personnel
                            <svg class="ml-1.5 h-4 w-4 transition-transform duration-200" :class="{'rotate-180': dropdownOpen}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </button>
                        <div x-show="dropdownOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" class="absolute left-0 mt-2 w-56 rounded-2xl shadow-xl bg-white ring-1 ring-slate-900/5 py-2 z-50" style="display: none;">
                            @if(auth()->user()->membre)
                                <a href="{{ route('membre.show', ['membre' => auth()->user()->membre->uuid ?? '']) }}" class="block px-5 py-2.5 text-sm text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors">Mon Dossier & Comptes</a>
                            @endif
                            @if(auth()->user()->agent)
                                <a href="{{ route('agent.show', ['agent' => auth()->user()->agent]) }}" class="block px-5 py-2.5 text-sm text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors">Mes performances</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT SECTION : STATUS & PROFILE --}}
            <div class="flex items-center space-x-3 sm:space-x-5">
                
                {{-- STATUS BADGE --}}
                @if (!auth()->user()->hasRole('niveau 0'))
                <div class="flex items-center">
                    @if($journeeOuverte)
                        <div class="flex items-center space-x-2 bg-emerald-50/80 px-3 py-1.5 rounded-xl border border-emerald-100/50 backdrop-blur-sm shadow-sm">
                            <span class="relative flex h-2.5 w-2.5">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                            </span>
                            <span class="hidden lg:inline text-xs font-bold text-emerald-700 uppercase tracking-wide">Ouvert - {{ \Carbon\Carbon::parse($journeeOuverte->date_cloture)->format('d/m/Y') }}</span>
                            <span class="lg:hidden text-xs font-bold text-emerald-700">{{ \Carbon\Carbon::parse($journeeOuverte->date_cloture)->format('d/m/Y') }}</span>
                        </div>
                    @else
                        <div class="flex items-center space-x-2 bg-rose-50/80 px-3 py-1.5 rounded-xl border border-rose-100/50 backdrop-blur-sm shadow-sm">
                            <span class="w-2.5 h-2.5 bg-rose-500 rounded-full shadow-[0_0_5px_rgba(244,63,94,0.5)]"></span>
                            <span class="hidden lg:inline text-xs font-bold text-rose-700 uppercase tracking-wide">Aucune journée</span>
                            <span class="lg:hidden text-xs font-bold text-rose-700">Fermé</span>
                        </div>
                    @endif
                </div>
                @endif

                {{-- USER MENU (Desktop) --}}
                <div class="hidden lg:block">
                    <x-dropdown align="right" width="64">
                        <x-slot name="trigger">
                            <button class="flex items-center p-1 rounded-full bg-white border border-slate-200 shadow-sm hover:shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                @if($user->photo_path)
                                    <img src="{{ $user->photo_path }}" alt="Profil" class="h-9 w-9 rounded-full object-cover">
                                @else
                                    <div class="h-9 w-9 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center text-white shadow-inner">
                                        <span class="text-sm font-bold">{{ substr($user->name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <span class="ml-3 mr-2 text-sm font-semibold text-slate-700">{{ explode(' ', $user->name)[0] }}</span>
                                <svg class="mr-2 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50 rounded-t-2xl">
                                <p class="text-sm font-bold text-slate-900">{{ $user->name }}</p>
                                <p class="text-xs font-medium text-slate-500 truncate mt-0.5">{{ $user->email }}</p>
                                @if (!auth()->user()->hasRole('niveau 0'))
                                    <div class="mt-3 inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-indigo-100 text-indigo-800">
                                        {{ $agence ? 'Agence : '.$agence->nom : 'Aucune agence' }}
                                    </div>
                                @endif
                            </div>
                            <div class="p-2">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="flex items-center w-full px-3 py-2 text-sm font-medium text-rose-600 hover:bg-rose-50 rounded-xl transition-colors">
                                        <svg class="mr-3 h-5 w-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                        {{ __('Déconnexion') }}
                                    </a>
                                </form>
                            </div>
                        </x-slot>
                    </x-dropdown>
                </div>

                {{-- HAMBURGER BUTTON (Mobile) --}}
                <div class="flex lg:hidden items-center">
                    <button @click="open = ! open" class="relative inline-flex items-center justify-center p-2.5 rounded-xl text-slate-500 hover:text-slate-900 hover:bg-slate-100 focus:outline-none transition-colors">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex transition-all duration-200 origin-center" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden transition-all duration-200 origin-center" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MOBILE MENU OVERLAY --}}
    <div x-show="open" 
         x-collapse 
         class="lg:hidden bg-slate-50 border-t border-slate-200 shadow-inner max-h-[85vh] overflow-y-auto overscroll-contain" 
         style="display: none;">
        
        <div class="p-4 space-y-4">
            
            {{-- Dashboard Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-base font-semibold {{ request()->routeIs('dashboard') ? 'text-indigo-600 bg-indigo-50/50' : 'text-slate-700 active:bg-slate-50' }}">
                    <svg class="mr-3 h-5 w-5 {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                    Tableau de bord
                </a>
            </div>

            {{-- Administration Card --}}
            @can('can.level4')
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-4 py-2 bg-slate-50/50 border-b border-slate-100 text-[11px] font-black uppercase tracking-wider text-slate-500 flex items-center">
                    <span class="w-2 h-2 rounded-full bg-rose-500 mr-2"></span> Administration
                </div>
                <div class="py-1">
                    <a href="{{ route('agents.index') }}" class="block px-4 py-2.5 text-sm font-medium text-slate-700 active:bg-slate-50">Agents</a>
                    <a href="{{ route('agences.zones.index') }}" class="block px-4 py-2.5 text-sm font-medium text-slate-700 active:bg-slate-50">Zones de Crédits</a>
                    @can('can.level8')
                        <a href="{{ route('agences.index') }}" class="block px-4 py-2.5 text-sm font-medium text-slate-700 active:bg-slate-50">Agences</a>
                        <a href="{{ route('types-depense.index') }}" class="block px-4 py-2.5 text-sm font-medium text-slate-700 active:bg-slate-50">Types Dépenses</a>
                        <a href="{{ route('types-revenu.index') }}" class="block px-4 py-2.5 text-sm font-medium text-slate-700 active:bg-slate-50">Types Revenus</a>
                        <a href="{{ route('historiquesroles.index') }}" class="block px-4 py-2.5 text-sm font-medium text-slate-700 active:bg-slate-50">Historique Rôles</a>
                    @endcan
                    @can('can.level5')
                        <a href="{{ route('agence.show', ['agence' => auth()->user()->agence ?? \App\Models\Agence::first()]) }}" class="block px-4 py-2.5 text-sm font-bold text-indigo-600 bg-indigo-50/30">Agence</a>
                    @endcan
                </div>
            </div>
            @endcan

            {{-- Caisse Card --}}
            @can('can.level3')
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-4 py-2 bg-slate-50/50 border-b border-slate-100 text-[11px] font-black uppercase tracking-wider text-slate-500 flex items-center">
                    <span class="w-2 h-2 rounded-full bg-amber-500 mr-2"></span> Caisse & Flux
                </div>
                <div class="py-1">
                    <a href="{{ route('comptes.index') }}" class="block px-4 py-2.5 text-sm font-medium text-slate-700 active:bg-slate-50">Effectuer un Retrait</a>
                    @can('canTransact')
                        <a href="{{ route('depenses.create') }}" class="block px-4 py-2.5 text-sm font-medium text-slate-700 active:bg-slate-50">Saisir une Dépense</a>
                        <a href="{{ route('revenus.create') }}" class="block px-4 py-2.5 text-sm font-medium text-slate-700 active:bg-slate-50">Saisir un Revenu</a>
                    @endcan
                    @can('can.level4')
                        <a href="{{ route('clotures.index') }}" class="block px-4 py-2.5 text-sm font-medium text-slate-700 active:bg-slate-50">Journées précédentes</a>
                        @can('canTransact')
                            @if($journeeOuverte)
                                <a href="{{ route('clotures.show', ['cloture' => $journeeOuverte]) }}" class="block px-4 py-2.5 text-sm font-bold text-amber-600 bg-amber-50/30">Situation de la journée</a>
                            @endif
                        @endcan
                    @endcan
                </div>
            </div>
            @endcan

            {{-- Terrain Card --}}
            @can('can.level1')
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-4 py-2 bg-slate-50/50 border-b border-slate-100 text-[11px] font-black uppercase tracking-wider text-slate-500 flex items-center">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2"></span> Opérations Terrain
                </div>
                <div class="py-1">
                    @can('can.level4')
                        <a href="{{ route('membre.create') }}" class="block px-4 py-2.5 text-sm font-medium text-slate-700 active:bg-slate-50">Nouveau membre</a>
                    @endcan
                    @can('canTransact')
                        <a href="{{ route('comptes.index') }}" class="block px-4 py-2.5 text-sm font-medium text-slate-700 active:bg-slate-50">Ajouter un Dépôt</a>
                        @can('can.level2')
                            <a href="{{ route('credit.pret.index') }}" class="block px-4 py-2.5 text-sm font-medium text-slate-700 active:bg-slate-50">Ajouter un Remboursement</a>
                        @endcan
                        @can('can.level4')
                            <a href="{{ route('membre.index') }}" class="block px-4 py-2.5 text-sm font-medium text-slate-700 active:bg-slate-50">Liste des membres</a>
                        @endcan
                    @endcan
                </div>
            </div>
            @endcan

            {{-- Personnel Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-4 py-2 bg-slate-50/50 border-b border-slate-100 text-[11px] font-black uppercase tracking-wider text-slate-500 flex items-center">
                    <span class="w-2 h-2 rounded-full bg-blue-500 mr-2"></span> Personnel
                </div>
                <div class="py-1">
                    @if(auth()->user()->membre)
                        <a href="{{ route('membre.show', ['membre' => auth()->user()->membre->uuid ?? '']) }}" class="block px-4 py-2.5 text-sm font-medium text-slate-700 active:bg-slate-50">Mon Dossier & Comptes</a>
                    @endif
                    @if(auth()->user()->agent)
                        <a href="{{ route('agent.show', ['agent' => auth()->user()->agent]) }}" class="block px-4 py-2.5 text-sm font-medium text-slate-700 active:bg-slate-50">Mes performances</a>
                    @endif
                </div>
            </div>

            {{-- Profile Mobile Footer --}}
            <div class="mt-6 mb-4 bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
                <div class="flex items-center">
                    @if($user->photo_path)
                        <img src="{{ $user->photo_path }}" alt="Photo" class="h-12 w-12 rounded-full object-cover shadow-sm ring-2 ring-white">
                    @else
                        <div class="h-12 w-12 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center text-white shadow-inner ring-2 ring-white">
                            <span class="text-lg font-bold">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                    @endif
                    <div class="ml-4 flex-1">
                        <div class="font-bold text-base text-slate-900">{{ $user->name }}</div>
                        <div class="font-medium text-sm text-slate-500">{{ $user->email }}</div>
                    </div>
                </div>
                
                @if (!auth()->user()->hasRole('niveau 0'))
                    <div class="mt-3 px-3 py-2 bg-indigo-50 rounded-xl text-sm font-bold text-indigo-700 text-center">
                        {{ $agence ? 'Agence : '.$agence->nom : 'Aucune agence' }}
                    </div>
                @endif
                
                <form method="POST" action="{{ route('logout') }}" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center py-2.5 px-4 bg-rose-50 text-rose-700 font-bold rounded-xl hover:bg-rose-100 transition-colors">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                        Déconnexion
                    </button>
                </form>
            </div>

        </div>
    </div>
</nav>