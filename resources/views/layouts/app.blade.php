<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- Theme color pour une belle intégration PWA sur mobile --}}
    <meta name="theme-color" content="#ffffff">

    <title>{{ config('app.name', 'SYSCO') }}</title>

    {{-- Fonts : Optionnel mais recommandé d'utiliser Inter ou Poppins pour cet UI --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#4A90E2">


    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans text-slate-900 bg-slate-50/50 min-h-screen flex flex-col selection:bg-indigo-100 selection:text-indigo-900">

    {{-- ===== HEADER GLOBAL ===== --}}
    @auth
        @include('layouts.navigation')
    @endauth

    {{-- Conteneur d'alertes global (Façon Toasts) --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6 z-40 relative">
        @if (session()->has('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                 x-transition:enter="transform ease-out duration-300 transition"
                 x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                 x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="flex items-center p-4 bg-white border border-emerald-200 rounded-2xl shadow-lg mb-4 relative overflow-hidden">
                <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-emerald-500"></div>
                <div class="flex-shrink-0 text-emerald-500 ml-2">
                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                </div>
                <div class="ml-4 flex-1">
                    <p class="font-bold text-slate-900">Succès</p>
                    <p class="font-medium text-slate-600 text-sm mt-0.5">{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="ml-4 flex-shrink-0 text-slate-400 hover:text-slate-600 bg-slate-50 hover:bg-slate-100 rounded-full p-1.5 transition-colors">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                </button>
            </div>
        @endif

        @if (session()->has('error'))
            <div x-data="{ show: true }" x-show="show"
                 x-transition:enter="transform ease-out duration-300 transition"
                 x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                 x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                 class="flex items-center p-4 bg-white border border-rose-200 rounded-2xl shadow-lg mb-4 relative overflow-hidden">
                <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-rose-500"></div>
                <div class="flex-shrink-0 text-rose-500 ml-2">
                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                </div>
                <div class="ml-4 flex-1">
                    <p class="font-bold text-slate-900">Erreur</p>
                    <p class="font-medium text-slate-600 text-sm mt-0.5">{{ session('error') }}</p>
                </div>
                <button @click="show = false" class="ml-4 flex-shrink-0 text-slate-400 hover:text-slate-600 bg-slate-50 hover:bg-slate-100 rounded-full p-1.5 transition-colors">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                </button>
            </div>
        @endif
    </div>

    {{-- ===== CONTENU PAGE ===== --}}
    <main class="flex-1 py-4 sm:py-8">
        {{ $slot }}
    </main>

    {{-- ===== FOOTER GLOBAL ===== --}}
    @auth
        <div class="mt-auto">
            @include('layouts.footer')
        </div>
    @endauth

    @livewireScripts
</body>
</html>