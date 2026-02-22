<div class="max-w-4xl mx-auto py-6">
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="bg-indigo-900 p-6 text-white flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold uppercase tracking-widest">{{ $zone->nom }}</h1>
                <p class="opacity-75">Agence : {{ $zone->agence->nom }} | Code : {{ $zone->code }}</p>
            </div>
            <div class="text-right">
                <span class="block text-xs uppercase opacity-60 font-bold">Gérant Responsable</span>
                <span class="text-lg">{{ $zone->gerant->nom ?? 'Non assigné' }}</span>
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-100">
                <span class="block text-indigo-600 text-sm font-bold uppercase">Crédits Actifs</span>
                <span class="text-3xl font-black">{{ $zone->credits->count() }}</span>
            </div>
            </div>

        <div class="px-6 py-4 bg-gray-50 border-t flex justify-between">
            <a href="{{ route('agences.zones.index', $zone->agence_id) }}" class="text-indigo-600 hover:underline">← Liste des zones</a>
            <span class="text-gray-400 text-xs italic">Dernière modification par : {{ $zone->updated_by ?? 'Système' }}</span>
        </div>
    </div>
</div>