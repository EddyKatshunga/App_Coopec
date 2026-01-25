<div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow space-y-6">
    <h2 class="text-xl font-semibold">
        {{ $agent ? 'Modifier l’agent' : 'Promouvoir un membre en agent' }}
    </h2>

    <form wire:submit.prevent="save" class="space-y-4">

        <div>
            <label class="label">Membre</label>
            <select wire:model="membre_id" class="input" @if($agent) disabled @endif>
                <option value="">-- Sélectionner --</option>
                @foreach($membres as $membre)
                    <option value="{{ $membre->id }}">
                        {{ $membre->nom }} - {{ $membre->numero_identification }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="label">Agence</label>
            <select wire:model="agence_id" class="input">
                <option value="">-- Sélectionner --</option>
                @foreach($agences as $agence)
                    <option value="{{ $agence->id }}">{{ $agence->nom }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="label">Statut</label>
            <select wire:model="statut" class="input">
                <option value="actif">Actif</option>
                <option value="inactif">Inactif</option>
            </select>
        </div>

        <div>
            <label class="label">Rôles</label>
            <div class="grid grid-cols-2 gap-2">
                @foreach($rolesDisponibles as $role)
                    <label class="flex items-center gap-2">
                        <input type="checkbox" wire:model="roles" value="{{ $role }}">
                        <span>{{ ucfirst($role) }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('agents.index') }}" class="btn-secondary">Annuler</a>
            <button class="btn-primary">
                {{ $agent ? 'Mettre à jour' : 'Promouvoir' }}
            </button>
        </div>
    </form>
</div>