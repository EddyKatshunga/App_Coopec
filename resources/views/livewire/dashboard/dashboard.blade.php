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

    {{-- ACTIONS --}}
    <livewire:dashboard.actions />

</div>
