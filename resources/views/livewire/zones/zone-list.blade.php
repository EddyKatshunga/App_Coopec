<div class="p-6 bg-white rounded-lg shadow">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Liste des zones</h2>
            <p class="text-sm text-gray-500">Gestion des secteurs géographiques</p>
        </div>
    </div>

    <input wire:model.live="search" type="text" placeholder="Rechercher une zone..." class="mb-4 border rounded px-4 py-2 w-full md:w-1/3 focus:ring-indigo-500">

    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50 uppercase text-xs font-semibold text-gray-600">
            <tr>
                <th class="p-3 border">Code</th>
                <th class="p-3 border">Nom de la Zone</th>
                <th class="p-3 border">Gérant actuel</th>
                <th class="p-3 border text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($zones as $zone)
                <tr class="hover:bg-gray-50">
                    <td class="p-3 border font-mono text-indigo-600">{{ $zone->code }}</td>
                    <td class="p-3 border font-medium">{{ $zone->nom }}</td>
                    <td class="p-3 border italic text-gray-600">
                        {{ $zone->gerant->nom ?? 'Aucun gérant assigné' }}
                    </td>
                    <td class="p-3 border text-center space-x-3">
                        <a href="{{ route('agences.zones.show', $zone->id) }}" class="text-indigo-600 hover:underline">Détails</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-4">{{ $zones->links() }}</div>
</div>