<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Bienvenue')</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-50">

<nav class="bg-white shadow">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
        <div class="space-x-4">
            <a href="{{ route('public.home') }}">Accueil</a>
            <a href="{{ route('public.news') }}">Actualités</a>
            <a href="{{ route('public.contact') }}">Contact</a>
        </div>

        <div>
            <a href="{{ route('login') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Se connecter
            </a>
        </div>
    </div>
</nav>

<main>
    @yield('content')
</main>

</body>
</html>
