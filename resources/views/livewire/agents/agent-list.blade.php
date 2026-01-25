<div class="bg-white p-6 rounded-lg shadow space-y-4">

    {{-- Filtres --}}
    <div class="flex flex-wrap gap-4 items-center">
        <input wire:model.debounce.300ms="search"
               placeholder="Rechercher un agent..."
               class="input w-64">

        <select wire:model="statut" class="input w-40">
            <option value="">Tous statuts</option>
            <option value="actif">Actifs</option>
            <option value="inactif">Inactifs</option>
        </select>

        <select wire:model="agence_id" class="input w-48">
            <option value="">Toutes agences</option>
            @foreach(\App\Models\Agence::all() as $agence)
                <option value="{{ $agence->id }}">{{ $agence->nom }}</option>
            @endforeach
        </select>

        <a href="{{ route('agent.add') }}" class="btn-primary ml-auto">
            + Nouvel agent
        </a>
    </div>

    {{-- Table --}}
    <table class="w-full text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2 text-left">Agent</th>
                <th>Agence</th>
                <th>Statut</th>
                <th>Rôles</th>
                <th class="text-right">Actions</th>
            </tr>
        </thead>

        <tbody>
        @foreach($agents as $agent)
            <tr class="border-t">
                <td class="p-2">
                    <div class="font-medium">{{ $agent->membre->nom }}</div>
                    <div class="text-xs text-gray-500">
                        {{ $agent->membre->user->email }}
                    </div>
                </td>

                <td>{{ $agent->agence->nom ?? '-' }}</td>

                <td>
                    <span class="px-2 py-1 rounded text-xs
                        {{ $agent->statut === 'actif'
                            ? 'bg-green-100 text-green-700'
                            : 'bg-red-100 text-red-700' }}">
                        {{ ucfirst($agent->statut) }}
                    </span>
                </td>

                <td class="text-xs">
                    {{ $agent->membre->user->roles->pluck('name')->implode(', ') }}
                </td>

                <td class="text-right space-x-2">
                    <a href="{{ route('agent.edit', $agent) }}"
                       class="text-blue-600 hover:underline">
                        Voir
                    </a>

                    <a href="{{ route('agent.edit', $agent) }}"
                       class="text-green-600 hover:underline">
                        Modifier
                    </a>

                    <button wire:click="toggleStatut({{ $agent->id }})"
                            class="text-orange-600 hover:underline">
                        {{ $agent->statut === 'actif' ? 'Désactiver' : 'Activer' }}
                    </button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $agents->links() }}
</div>