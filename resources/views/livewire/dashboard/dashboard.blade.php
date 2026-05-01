<div class="p-6 space-y-8 bg-gray-50 min-h-screen">
    
    {{-- 1. HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b pb-6">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Tableau de bord</h1>
            <p class="text-gray-500 font-medium">
                Bienvenue
                • {{ auth()->user()->name }}
            </p>
        </div>
        
        {{-- Widget rapide (ex: date ou solde caisse si agent) --}}
        <div class="bg-white px-4 py-2 rounded-xl shadow-sm border font-bold text-sm text-gray-600">
            {{ now()->translatedFormat('d F Y') }}
        </div>
    </div>

    {{-- 3. ZONE DE STATISTIQUES (Optionnel, selon le niveau) --}}
    @if(auth()->user()->agent)
        <livewire:dashboard.stats-agent-mini />
    @else
        <livewire:dashboard.stats-membre-mini />
    @endif

</div>