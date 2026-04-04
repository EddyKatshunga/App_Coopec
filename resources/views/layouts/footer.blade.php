@props(['showLogout' => true])

<footer {{ $attributes->merge(['class' => 'bg-white border-t border-gray-200 py-4 sm:py-6 mt-auto']) }}>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Version mobile (colonnes) -->
        <div class="flex flex-col sm:hidden space-y-4">
            <!-- Logo et identité -->
            <div class="flex items-center justify-center space-x-2">
                <img src="{{ asset('images/logo1.png') }}" alt="{{config('app.nom_entreprise')}}" class="h-8 w-auto">
                <div class="flex flex-col items-start">
                    <span class="text-sm font-semibold text-gray-900">{{config('app.nom_entreprise')}}</span>
                    <span class="text-xs text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">{{config('app.name')}}</span>
                </div>
            </div>

            <!-- Copyright -->
            <div class="text-center">
                <p class="text-xs text-gray-500">
                    © {{ date('Y') }} {{config('app.nom_entreprise')}}. Tous droits réservés.
                </p>
            </div>

            <!-- Bouton déconnexion (mobile) -->
            @auth
                @if($showLogout)
                    <div class="flex justify-center">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center space-x-2 px-4 py-2 bg-red-50 text-red-600 rounded-lg text-sm font-medium hover:bg-red-100 transition duration-200">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span>Déconnexion</span>
                            </button>
                        </form>
                    </div>
                @endif
            @endauth
        </div>

        <!-- Version tablette/desktop (ligne) -->
        <div class="hidden sm:flex sm:flex-col md:flex-row justify-between items-center space-y-3 md:space-y-0">
            <!-- Logo et identité -->
            <div class="flex items-center space-x-3">
                <img src="{{ asset('images/logo1.png') }}" alt="{{config('app.nom_entreprise')}}" class="h-10 w-auto">
                <div>
                    <span class="text-sm font-semibold text-gray-900 block">{{config('app.nom_entreprise')}}</span>
                    <span class="text-xs text-indigo-600">{{config('app.name')}} - Système de Coopérative</span>
                </div>
            </div>

            <!-- Copyright -->
            <div class="text-center">
                <p class="text-sm text-gray-500">
                    © {{ date('Y') }} {{config('app.nom_entreprise')}}. Tous droits réservés.
                </p>
            </div>

            <!-- Bouton déconnexion (desktop) -->
            @auth
                @if($showLogout)
                    <div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center space-x-2 px-4 py-2 bg-red-50 text-red-600 rounded-lg text-sm font-medium hover:bg-red-100 transition duration-200">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span>Déconnexion</span>
                            </button>
                        </form>
                    </div>
                @endif
            @endauth
        </div>
    </div>
</footer>