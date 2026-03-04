<div class="p-6 bg-white rounded-lg shadow">
    <div class="flex justify-between items-center mb-4">
        <input wire:model.live="search" type="text" placeholder="Rechercher un libellé..." class="border rounded px-4 py-2 w-1/3">
        <a href="{{ route('depenses.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded" wire:navigate>Nouvelle Dépense</a>
    </div>

    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-100">
                <th class="p-3 border">Date</th>
                <th class="p-3 border">Libellé</th>
                <th class="p-3 border">Montant</th>
                <th class="p-3 border">Type</th>
                <th class="p-3 border text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($depenses as $depense)
                <tr class="hover:bg-gray-50">
                    <td class="p-3 border">{{ $depense->date_operation }}</td>
                    <td class="p-3 border font-semibold">{{ $depense->libelle }}</td>
                    <td class="p-3 border text-red-600">{{ number_format($depense->montant, 2) }} {{ $depense->monnaie }}</td>
                    <td class="p-3 border">{{ $depense->typeDepense->nom ?? 'N/A' }}</td>
                    <td class="p-3 border text-center space-x-2">
                        <a href="{{ route('depenses.show', $depense->id) }}" class="text-blue-500 hover:underline" wire:navigate>Détails</a>
                        @can('agent.create') 
                            {{-- 2. Vérification Journée Ouverte via le trait du modèle --}}
                            @if($depense->canBeDeleted())
                                <button 
                                    wire:click="deleteRecord('App\\Models\\Depense', {{ $depense->id }})"
                                    wire:confirm="Êtes-vous sûr de vouloir supprimer définitivement cette dépense ?"
                                    class="text-red-600 hover:text-red-900 text-sm font-medium"
                                >
                                    Supprimer
                                </button>
                            @endif
                        @endcan
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-4">{{ $depenses->links() }}</div>
</div>