<div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow space-y-6">
    <div class="border-b pb-4">
        <h2 class="text-xl font-bold text-gray-800">
            {{ $agent ? 'Modifier l\'agent' : 'Promouvoir en agent' }}
        </h2>
        <p class="text-sm text-gray-600">Membre : <strong>{{ $membre->nom ?? 'N/A' }}</strong></p>
    </div>

    <form wire:submit.prevent="save" class="space-y-5">
        
        <div>
            <label class="block text-sm font-medium text-gray-700">Agence d'affectation</label>
            @php
                // Vérification si le rôle actuel interdit le changement d'agence
                $roleActuel = $agent ? $agent->user->getRoleNames()->first() : null;
                $isLocked = in_array($roleActuel, ['chef_agence', 'agent_credit']);
            @endphp

            @if($isLocked)
                <div class="mt-1 p-2 bg-gray-100 border rounded text-gray-600 text-sm">
                    Agence : {{ $agent->agence->nom }} 
                    <span class="block text-xs italic">(Modification impossible pour le rôle {{ $roleActuel }})</span>
                </div>
                <input type="hidden" wire:model="agence_id">
            @else
                <select wire:model="agence_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">-- Choisir une agence --</option>
                    @foreach($agences as $agence)
                        <option value="{{ $agence->id }}">{{ $agence->nom }}</option>
                    @endforeach
                </select>
            @endif
            @error('agence_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Rôle attribué dans le système</label>
            <select wire:model.live="role_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">-- Sélectionner un rôle --</option>
                @foreach($rolesDisponibles as $r)
                    <option value="{{ $r->name }}">{{ strtoupper($r->name) }}</option>
                @endforeach
            </select>
            @error('role_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t">
            <a href="{{ route('agents.index') }}" wire:navigate class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                Annuler
            </a>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ $agent ? 'Mettre à jour l\'agent' : 'Confirmer la promotion' }}
            </button>
        </div>
    </form>
</div>