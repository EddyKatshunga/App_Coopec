<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold">
                Tableau de bord
            </h1>
            <p class="text-gray-600">
                Bienvenue {{ auth()->user()->name }}
            </p>
        </div>
    </div>

    {{-- Sélecteur d’agence --}}
    <livewire:dashboard.agence-selector />

    {{-- ALERTES --}}
    <livewire:dashboard.alerts />

    {{-- STATS --}}
    <livewire:dashboard.stats />

    {{-- ACTIONS --}}
    <livewire:dashboard.actions />

</div>
