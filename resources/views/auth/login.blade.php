<x-guest-layout>
    <!-- En-tête avec logo, noms et bouton retour -->
    <header class="fixed top-0 left-0 right-0 bg-white/80 backdrop-blur-md border-b border-gray-200/80 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo et noms de l'entreprise/application -->
                <div class="flex items-center space-x-3">
                    <a href="{{ url('/') }}" class="flex items-center space-x-3">
                        <!-- Logo -->
                        <img src="{{ asset('images/logo1.png') }}" alt="COOPEC KIKWIT" class="h-8 sm:h-10 w-auto">
                        
                        <!-- Noms de l'entreprise et l'application (cachés sur très petit écran) -->
                        <div class="hidden xs:flex xs:items-center xs:space-x-2">
                            <span class="text-sm sm:text-base font-semibold text-gray-900">COOPEC KIKWIT</span>
                            <span class="text-gray-400 hidden sm:inline">|</span>
                            <span class="text-xs sm:text-sm font-medium text-indigo-600 bg-indigo-50 px-2 py-1 rounded-full hidden sm:inline-block">
                                SYSCO
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Bouton retour à l'accueil -->
                <a href="{{ route('public.home') }}" class="inline-flex items-center px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition duration-200">
                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="hidden xs:inline">Page d'accueil</span>
                    <span class="xs:hidden">Accueil</span>
                </a>
            </div>
        </div>
    </header>

    <div class="min-h-screen flex flex-col lg:flex-row pt-16">
        <!-- Section gauche - Hero visuel (caché sur mobile, visible sur tablette et desktop) -->
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden">
            <!-- Fond avec dégradé moderne (inchangé) -->
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-700">
                <!-- Motif géométrique -->
                <div class="absolute inset-0 opacity-10">
                    <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                        <defs>
                            <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                                <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/>
                            </pattern>
                        </defs>
                        <rect width="100%" height="100%" fill="url(#grid)" />
                    </svg>
                </div>
                <!-- Cercles flous -->
                <div class="absolute top-0 -left-4 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
                <div class="absolute top-0 -right-4 w-72 h-72 bg-indigo-500 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
                <div class="absolute -bottom-8 left-20 w-72 h-72 bg-pink-500 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
            </div>
            
            <!-- Contenu (inchangé) -->
            <div class="relative flex items-center justify-center p-12 text-white w-full">
                <div class="max-w-md space-y-8">
                    <!-- Icône de sécurité moderne -->
                    <div class="flex justify-center lg:justify-start">
                        <div class="w-20 h-20 bg-white/10 backdrop-blur-lg rounded-2xl flex items-center justify-center border border-white/20">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                    </div>
                    
                    <h2 class="text-4xl lg:text-5xl font-bold leading-tight">
                        Bienvenue sur<br>SYSCO
                    </h2>
                    
                    <p class="text-lg text-indigo-100 leading-relaxed">
                        Accédez à votre espace de travail sécurisé. Votre numéro d'identification et votre mot de passe sont recquis pour la protection du système.
                    </p>
                    
                    <!-- Statistiques ou features -->
                    <div class="grid grid-cols-2 gap-4 pt-6">
                        <div class="bg-white/5 backdrop-blur-lg rounded-xl p-4 border border-white/10">
                            <div class="text-2xl font-bold">100%</div>
                            <div class="text-sm text-indigo-200">Sécurisé</div>
                        </div>
                        <div class="bg-white/5 backdrop-blur-lg rounded-xl p-4 border border-white/10">
                            <div class="text-2xl font-bold">24/7</div>
                            <div class="text-sm text-indigo-200">Disponible</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section droite - Formulaire avec fond amélioré -->
        <div class="relative flex flex-col justify-center w-full lg:w-1/2 px-4 sm:px-6 lg:px-8 xl:px-12 py-8 sm:py-12 overflow-hidden">
            <!-- Fond avec effet de verre dépoli et motifs -->
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-50 via-white to-purple-50">
                <!-- Motif de points subtils -->
                <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 1px 1px, #6366f1 1px, transparent 0); background-size: 40px 40px;"></div>
                
                <!-- Cercles flous décoratifs -->
                <div class="absolute top-20 -right-20 w-80 h-80 bg-indigo-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>
                <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-purple-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse animation-delay-1000"></div>
                
                <!-- Vagues décoratives -->
                <svg class="absolute bottom-0 left-0 w-full h-32 text-indigo-50 opacity-30" preserveAspectRatio="none" viewBox="0 0 1440 120">
                    <path fill="currentColor" d="M0,32L48,37.3C96,43,192,53,288,58.7C384,64,480,64,576,58.7C672,53,768,43,864,48C960,53,1056,75,1152,80C1248,85,1344,75,1392,69.3L1440,64L1440,120L1392,120C1344,120,1248,120,1152,120C1056,120,960,120,864,120C768,120,672,120,576,120C480,120,384,120,288,120C192,120,96,120,48,120L0,120Z"></path>
                </svg>
            </div>

            <!-- Conteneur du formulaire avec effet de verre -->
            <div class="relative z-10 max-w-md mx-auto w-full">
                <!-- Badge d'application pour mobile (visible uniquement sur mobile) -->
                <div class="lg:hidden mb-6 text-center">
                    <div class="inline-flex items-center space-x-2 bg-white/80 backdrop-blur-sm px-4 py-2 rounded-full shadow-lg border border-indigo-100">
                        <span class="text-sm font-medium text-indigo-700">SYSCO</span>
                        <span class="text-indigo-300">•</span>
                        <span class="text-sm text-indigo-600">COOPEC KIKWIT</span>
                    </div>
                </div>

                <!-- Carte du formulaire avec effet de verre -->
                <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-2xl p-6 sm:p-8 border border-white/20">
                    <!-- En-tête du formulaire avec icône -->
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl shadow-lg mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                            Connexion
                        </h1>
                        <p class="text-gray-500 mt-2">Veuillez entrer vos accès pour continuer</p>
                    </div>

                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}" autocomplete="off" class="space-y-5 sm:space-y-6">
                        @csrf

                        <!-- Champ Identification -->
                        <div>
                            <label for="numero_identification" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                                Numéro d'identification
                            </label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 group-focus-within:text-indigo-500 transition duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                    </svg>
                                </div>
                                <input 
                                    id="numero_identification" 
                                    name="numero_identification" 
                                    type="text" 
                                    required 
                                    class="w-full pl-10 pr-4 py-3 text-sm sm:text-base rounded-xl border-gray-200 bg-white/50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition duration-200 shadow-sm"
                                    placeholder="ID-8829"
                                    value="{{ old('numero_identification') }}"
                                >
                            </div>
                            <x-input-error :messages="$errors->get('numero_identification')" class="mt-2" />
                        </div>

                        <!-- Champ Mot de passe -->
                        <div>
                            <div class="flex items-center justify-between mb-1 sm:mb-2">
                                <label for="password" class="block text-xs sm:text-sm font-medium text-gray-700">
                                    Mot de passe
                                </label>
                                @if (Route::has('password.request'))
                                    <a class="text-xs sm:text-sm font-medium text-indigo-600 hover:text-indigo-500 transition duration-200" href="{{ route('password.request') }}">
                                        Mot de passe oublié ?
                                    </a>
                                @endif
                            </div>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 group-focus-within:text-indigo-500 transition duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <input 
                                    id="password" 
                                    type="password" 
                                    name="password" 
                                    required 
                                    class="w-full pl-10 pr-4 py-3 text-sm sm:text-base rounded-xl border-gray-200 bg-white/50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition duration-200 shadow-sm"
                                    placeholder="••••••••"
                                >
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Bouton Connexion -->
                        <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-semibold py-3 px-4 rounded-xl transition duration-300 transform hover:scale-[1.02] active:scale-[0.98] shadow-lg shadow-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 text-sm sm:text-base">
                            Se connecter
                        </button>
                    </form>

                    <!-- Footer du formulaire -->
                    <p class="mt-8 text-center text-sm text-gray-500">
                        En vous connectant, vous acceptez nos 
                        <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">conditions d'utilisation</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer - Tous droits réservés -->
    <footer class="bg-white/80 backdrop-blur-sm border-t border-gray-200 py-4 sm:py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-center space-y-3 sm:space-y-0">
                <!-- Copyright -->
                <div class="text-xs sm:text-sm text-gray-500 text-center sm:text-left">
                    © {{ date('Y') }} COOPEC KIKWIT. Tous droits réservés.
                </div>
                
                <!-- Liens légaux -->
                <div class="flex space-x-4 sm:space-x-6">
                    <a href="#" class="text-xs sm:text-sm text-gray-500 hover:text-indigo-600 transition duration-200">
                        Mentions légales
                    </a>
                    <a href="#" class="text-xs sm:text-sm text-gray-500 hover:text-indigo-600 transition duration-200">
                        Politique de confidentialité
                    </a>
                    <a href="#" class="text-xs sm:text-sm text-gray-500 hover:text-indigo-600 transition duration-200">
                        CGU
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Styles pour les animations et classes supplémentaires -->
    <style>
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob {
            animation: blob 7s infinite;
        }
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        .animation-delay-4000 {
            animation-delay: 4s;
        }
        
        /* Breakpoint personnalisé pour très petits écrans */
        @media (min-width: 480px) {
            .xs\:inline {
                display: inline;
            }
            .xs\:flex {
                display: flex;
            }
            .xs\:hidden {
                display: none;
            }
            .xs\:items-center {
                align-items: center;
            }
            .xs\:space-x-2 > :not([hidden]) ~ :not([hidden]) {
                --tw-space-x-reverse: 0;
                margin-right: calc(0.5rem * var(--tw-space-x-reverse));
                margin-left: calc(0.5rem * calc(1 - var(--tw-space-x-reverse)));
            }
        }
    </style>
</x-guest-layout>