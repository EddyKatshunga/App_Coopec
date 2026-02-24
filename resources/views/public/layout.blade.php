<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Bienvenue')</title>
    @vite('resources/css/app.css')
    @stack('styles')
</head>
<body class="bg-gray-50 font-sans antialiased">

<nav class="bg-white shadow-lg sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo / Brand -->
            <div class="flex-shrink-0">
                <a href="{{ route('public.home') }}" class="text-2xl font-bold text-blue-600 tracking-tight hover:text-blue-700 transition">
                    COOPEC KIKWIT
                </a>
            </div>

            <!-- Desktop Navigation (hidden on mobile) -->
            <div class="hidden md:flex md:items-center md:space-x-1">
                <a href="{{ route('public.home') }}" class="text-gray-700 hover:text-blue-600 hover:bg-blue-50 px-4 py-2 rounded-lg text-sm font-medium transition">Accueil</a>
                <a href="{{ route('public.news') }}" class="text-gray-700 hover:text-blue-600 hover:bg-blue-50 px-4 py-2 rounded-lg text-sm font-medium transition">Actualités</a>
                <a href="{{ route('public.contact') }}" class="text-gray-700 hover:text-blue-600 hover:bg-blue-50 px-4 py-2 rounded-lg text-sm font-medium transition">Contact</a>
            </div>

            <!-- Desktop Login Button (hidden on mobile) -->
            <div class="hidden md:flex md:items-center">
                <a href="{{ route('login') }}" 
                   class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 hover:shadow-md transition-all duration-200 transform hover:-translate-y-0.5">
                    Se connecter
                </a>
            </div>

            <!-- Mobile menu button (hamburger) -->
            <div class="flex md:hidden">
                <button type="button" id="mobile-menu-button" class="text-gray-600 hover:text-blue-600 focus:outline-none p-2" aria-label="Menu">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu (hidden by default) -->
    <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-100 shadow-inner">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 space-y-1">
            <a href="{{ route('public.home') }}" class="block text-gray-700 hover:text-blue-600 hover:bg-blue-50 px-4 py-3 rounded-lg text-base font-medium transition">Accueil</a>
            <a href="{{ route('public.news') }}" class="block text-gray-700 hover:text-blue-600 hover:bg-blue-50 px-4 py-3 rounded-lg text-base font-medium transition">Actualités</a>
            <a href="{{ route('public.contact') }}" class="block text-gray-700 hover:text-blue-600 hover:bg-blue-50 px-4 py-3 rounded-lg text-base font-medium transition">Contact</a>
            <div class="pt-3 pb-2">
                <a href="{{ route('login') }}" 
                   class="block w-full text-center bg-blue-600 text-white px-4 py-3 rounded-lg text-base font-semibold hover:bg-blue-700 hover:shadow-md transition">
                    Se connecter
                </a>
            </div>
        </div>
    </div>
</nav>

<main>
    @yield('content')
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const button = document.getElementById('mobile-menu-button');
        const menu = document.getElementById('mobile-menu');
        
        if (button && menu) {
            button.addEventListener('click', function() {
                menu.classList.toggle('hidden');
                // Animation d'ouverture/fermeture (optionnel)
                if (!menu.classList.contains('hidden')) {
                    menu.style.maxHeight = menu.scrollHeight + 'px';
                } else {
                    menu.style.maxHeight = '0';
                }
            });
            
            // Optionnel : fermer le menu quand on clique sur un lien
            const links = menu.querySelectorAll('a');
            links.forEach(link => {
                link.addEventListener('click', () => {
                    menu.classList.add('hidden');
                });
            });
        }
    });
</script>

@stack('scripts')
</body>
</html>