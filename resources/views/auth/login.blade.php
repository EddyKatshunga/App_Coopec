<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Connexion | {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob { animation: blob 7s infinite; }
        .animation-delay-2000 { animation-delay: 2s; }
        .animation-delay-4000 { animation-delay: 4s; }
        
        /* Breakpoint personnalisé pour très petits écrans */
        @media (min-width: 480px) {
            .xs\:inline { display: inline; }
            .xs\:flex { display: flex; }
            .xs\:hidden { display: none; }
            .xs\:items-center { align-items: center; }
            .xs\:space-x-2 > :not([hidden]) ~ :not([hidden]) {
                --tw-space-x-reverse: 0;
                margin-right: calc(0.5rem * var(--tw-space-x-reverse));
                margin-left: calc(0.5rem * calc(1 - var(--tw-space-x-reverse)));
            }
        }
    </style>
</head>
<body class="font-sans text-gray-900 antialiased bg-white">

    <div class="min-h-screen flex flex-col lg:flex-row">
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-700">
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
                <div class="absolute top-0 -left-4 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
                <div class="absolute top-0 -right-4 w-72 h-72 bg-indigo-500 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
                <div class="absolute -bottom-8 left-20 w-72 h-72 bg-pink-500 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
            </div>
            
            <div class="relative flex items-center justify-center p-12 text-white w-full">
                <div class="max-w-md space-y-8">
                    <div class="flex justify-center lg:justify-start">
                        <div class="w-20 h-20 bg-white/10 backdrop-blur-lg rounded-2xl flex items-center justify-center border border-white/20">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                    </div>
                    
                    <h2 class="text-4xl lg:text-5xl font-bold leading-tight">
                        Bienvenue sur<br>{{ config('app.name') }}
                    </h2>
                    
                    <p class="text-lg text-indigo-100 leading-relaxed">
                        Accédez à votre espace de travail sécurisé. Votre identifiant (email ou numéro) et votre mot de passe sont requis pour la protection du système.
                    </p>
                    
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

        <div class="relative flex flex-col justify-center w-full lg:w-1/2 px-4 sm:px-6 lg:px-8 xl:px-12 py-8 sm:py-12 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-50 via-white to-purple-50">
                <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 1px 1px, #6366f1 1px, transparent 0); background-size: 40px 40px;"></div>
                
                <div class="absolute top-20 -right-20 w-80 h-80 bg-indigo-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>
                <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-purple-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse animation-delay-1000"></div>
                
                <svg class="absolute bottom-0 left-0 w-full h-32 text-indigo-50 opacity-30" preserveAspectRatio="none" viewBox="0 0 1440 120">
                    <path fill="currentColor" d="M0,32L48,37.3C96,43,192,53,288,58.7C384,64,480,64,576,58.7C672,53,768,43,864,48C960,53,1056,75,1152,80C1248,85,1344,75,1392,69.3L1440,64L1440,120L1392,120C1344,120,1248,120,1152,120C1056,120,960,120,864,120C768,120,672,120,576,120C480,120,384,120,288,120C192,120,96,120,48,120L0,120Z"></path>
                </svg>
            </div>

            <div class="relative z-10 max-w-md mx-auto w-full">
                <div class="lg:hidden mb-6 text-center">
                    <div class="inline-flex items-center space-x-2 bg-white/80 backdrop-blur-sm px-4 py-2 rounded-full shadow-lg border border-indigo-100">
                        <span class="text-sm font-medium text-indigo-700">{{ config('app.name') }}</span>
                        <span class="text-indigo-300">•</span>
                        <span class="text-sm text-indigo-600">{{ config('app.nom_entreprise', 'Notre Entreprise') }}</span>
                    </div>
                </div>

                <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-2xl p-6 sm:p-8 border border-white/20">
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-2xl shadow-lg mb-4 overflow-hidden border border-gray-100">
                            <img src="{{ asset('images/logo1.png') }}" alt="Logo {{ config('app.name') }}" class="w-12 h-12 object-contain">
                        </div>
                        <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                            Connexion
                        </h1>
                        <p class="text-gray-500 mt-2">Veuillez entrer vos accès pour continuer</p>
                    </div>

                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-600">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" autocomplete="off" class="space-y-5 sm:space-y-6">
                        @csrf

                        <div>
                            <label for="login" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                                Numéro d'identification
                            </label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 group-focus-within:text-indigo-500 transition duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <input 
                                    id="login" 
                                    name="login" 
                                    type="text" 
                                    required 
                                    autofocus
                                    class="w-full pl-10 pr-4 py-3 text-sm sm:text-base rounded-xl border-gray-200 bg-white/50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition duration-200 shadow-sm"
                                    value="{{ old('login') }}"
                                >
                            </div>
                            @error('login')
                                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1 sm:mb-2">
                                <label for="password" class="block text-xs sm:text-sm font-medium text-gray-700">
                                    Mot de passe
                                </label>
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
                                    class="w-full pl-10 pr-12 py-3 text-sm sm:text-base rounded-xl border-gray-200 bg-white/50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition duration-200 shadow-sm"
                                    placeholder="••••••••"
                                >
                                <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-indigo-600 focus:outline-none transition duration-200">
                                    <svg id="eye-icon" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg id="eye-slash-icon" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.978 9.978 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-semibold py-3 px-4 rounded-xl transition duration-300 transform hover:scale-[1.02] active:scale-[0.98] shadow-lg shadow-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 text-sm sm:text-base">
                            Se connecter
                        </button>
                    </form>

                    <p class="mt-8 text-center text-sm text-gray-500">
                        En vous connectant, vous acceptez nos 
                        <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">conditions d'utilisation</a>
                    </p>
                </div>
            </div>
            
            <div class="absolute bottom-4 w-full text-center lg:hidden z-10 px-4">
                <p class="text-xs text-gray-500">© {{ date('Y') }} {{ config('app.nom_entreprise', 'Notre Entreprise') }}. Tous droits réservés.</p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeSlashIcon = document.getElementById('eye-slash-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeSlashIcon.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeSlashIcon.classList.add('hidden');
            }
        }
    </script>
</body>
</html>