<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    @php
        $user = Auth::user();
        $agence = $user->agent?->agence;
        $journeeOuverte = auth()->user()->journee_ouverte;
    @endphp

    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo (toujours visible) -->
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 sm:space-x-3">
                    <img src="{{ asset('images/logo1.png') }}" alt="COOPEC KIKWIT" class="h-8 sm:h-10 w-auto">
                    
                    <!-- Noms d'entreprise (cachés sur mobile) -->
                    <div class="hidden sm:flex sm:items-center sm:space-x-2">
                        <span class="text-sm sm:text-base font-semibold text-gray-900">COOPEC KIKWIT</span>
                        <span class="text-gray-400 hidden lg:inline">|</span>
                        <span class="text-xs sm:text-sm font-medium text-indigo-600 bg-indigo-50 px-2 py-1 rounded-full hidden lg:inline-block">
                            SYSCO
                        </span>
                    </div>
                </a>
            </div>

            <!-- Status Journée - Centré sur desktop, adapté sur mobile -->
            <div class="flex items-center flex-1 justify-center">
                @if($journeeOuverte)
                    <!-- Version desktop complète -->
                    <div class="hidden sm:flex items-center space-x-2 bg-green-50 px-4 py-1.5 rounded-lg border border-green-200">
                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                        <span class="text-sm font-medium text-green-700">
                            Date des Opérations - {{ \Carbon\Carbon::parse($journeeOuverte->date_cloture)->format('d/m/Y') }}
                        </span>
                    </div>
                    
                    <!-- Version mobile compacte -->
                    <div class="flex sm:hidden items-center space-x-1 bg-green-50 px-2 py-1 rounded-full border border-green-200">
                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                        <span class="text-xs font-medium text-green-700">{{ \Carbon\Carbon::parse($journeeOuverte->date_cloture)->format('d/m/Y') }}</span>
                    </div>
                @else
                    <!-- Version desktop complète -->
                    <div class="hidden sm:flex items-center space-x-2 bg-red-50 px-4 py-1.5 rounded-lg border border-red-200">
                        <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                        <span class="text-sm font-medium text-red-700">Aucune journée ouverte</span>
                    </div>
                    
                    <!-- Version mobile compacte -->
                    <div class="flex sm:hidden items-center space-x-1 bg-red-50 px-2 py-1 rounded-full border border-red-200">
                        <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                        <span class="text-xs font-medium text-red-700">Fermé</span>
                    </div>
                @endif
            </div>

            <!-- Photo de profil et menu (toujours visible) -->
            <div class="flex items-center space-x-2 sm:space-x-4">
                <!-- Photo de profil (toujours visible) -->
                <div class="flex items-center">
                    @if($user->photo_path)
                        <img src="{{ $user->photo_path }}" alt="Photo de profil" class="h-8 w-8 sm:h-9 sm:w-9 rounded-full object-cover border-2 border-indigo-100 cursor-pointer" @click="open = ! open">
                    @else
                        <div class="h-8 w-8 sm:h-9 sm:w-9 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center border-2 border-indigo-200 cursor-pointer" @click="open = ! open">
                            <svg class="h-4 w-4 sm:h-5 sm:w-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    @endif
                </div>

                <!-- Menu desktop (visible uniquement sur desktop) -->
                <div class="hidden sm:block">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition duration-150 ease-in-out">
                                <span>{{ $user->name }}</span>
                                <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <!-- Infos utilisateur -->
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $agence ? 'Agence : '.$agence->nom : 'Aucune agence' }}</p>
                                <p class="text-xs mt-1 {{ $journeeOuverte ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $journeeOuverte ? '✅ Session active' : '⛔ Session inactive' }}
                                </p>
                            </div>
                            
                            <!-- Lien de déconnexion -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    <div class="flex items-center space-x-2">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        <span>{{ __('Déconnexion') }}</span>
                                    </div>
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>

                <!-- Hamburger (mobile uniquement) -->
                <div class="flex sm:hidden">
                    <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (Mobile) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <!-- Lien Dashboard -->
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="flex items-center space-x-2 py-3">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>{{ __('Tableau de bord') }}</span>
            </x-responsive-nav-link>
        </div>

        <!-- Informations utilisateur complètes -->
        <div class="pt-4 pb-3 border-t border-gray-200">
            <div class="px-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        @if($user->photo_path)
                            <img src="{{ $user->photo_path }}" alt="Photo" class="h-10 w-10 rounded-full object-cover">
                        @else
                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center">
                                <svg class="h-6 w-6 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="ml-3">
                        <div class="font-medium text-base text-gray-800">{{ $user->name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ $user->email }}</div>
                    </div>
                </div>

                <!-- Détails dans le menu mobile -->
                <div class="mt-3 space-y-2">
                    <div class="text-xs text-gray-500">
                        {{ $agence ? 'Agence : '.$agence->nom : 'Aucune agence' }}
                    </div>
                    @if($journeeOuverte)
                        <div class="text-xs text-green-600">
                            ✅ Date des opérations -{{ \Carbon\Carbon::parse($journeeOuverte->date_cloture)->format('d/m/Y') }}
                        </div>
                    @else
                        <div class="text-xs text-red-600">
                            ⛔ Aucune journée ouverte
                        </div>
                    @endif
                </div>
            </div>

            <!-- Déconnexion mobile -->
            <div class="mt-3 space-y-1">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();"
                            class="flex items-center space-x-2 py-3 text-red-600 hover:text-red-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>{{ __('Déconnexion') }}</span>
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>