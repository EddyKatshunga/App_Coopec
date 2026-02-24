<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>SYSCO</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-100">

    {{-- ===== HEADER GLOBAL ===== --}}
    @auth
        @include('layouts.navigation')
    @endauth

    {{-- ===== CONTENU PAGE ===== --}}
    <main class="py-6">
        {{ $slot }}
    </main>

    {{-- ===== FOOTER GLOBAL ===== --}}
    @auth
        @include('layouts.footer')
    @endauth

    @livewireScripts
</body>
</html>
