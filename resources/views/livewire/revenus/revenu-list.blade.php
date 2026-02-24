<div class="p-6 bg-white rounded-lg shadow">
    <div class="flex justify-between items-center mb-4">
        <input wire:model.live="search" type="text" placeholder="Rechercher un revenu..." class="border rounded px-4 py-2 w-1/3 focus:ring-green-500">
        <a href="{{ route('revenus.create') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700" wire:navigate>Nouveau Revenu</a>
    </div>

    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-100 uppercase text-xs text-gray-600">
                <th class="p-3 border">Date</th>
                <th class="p-3 border">Libellé</th>
                <th class="p-3 border">Montant</th>
                <th class="p-3 border">Type</th>
                <th class="p-3 border text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($revenus as $revenu)
                <tr class="hover:bg-gray-50">
                    <td class="p-3 border text-sm">{{ $revenu->date_operation }}</td>
                    <td class="p-3 border font-semibold text-gray-700">{{ $revenu->libelle }}</td>
                    <td class="p-3 border text-green-600 font-bold">+ {{ number_format($revenu->montant, 2) }} {{ $revenu->monnaie }}</td>
                    <td class="p-3 border text-sm">{{ $revenu->typeRevenu->nom ?? 'N/A' }}</td>
                    <td class="p-3 border text-center space-x-3">
                        <a href="{{ route('revenus.show', $revenu->id) }}" class="text-blue-500 hover:text-blue-700" wire:navigate>Détails</a>
                        <button wire:click="delete({{ $revenu->id }})" 
                                wire:confirm="Voulez-vous vraiment annuler ce revenu ?"
                                class="text-red-500 hover:text-red-700">
                            Supprimer
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-4">{{ $revenus->links() }}</div>
</div>