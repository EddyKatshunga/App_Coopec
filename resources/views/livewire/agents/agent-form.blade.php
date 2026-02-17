<div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow space-y-6">
    <h2 class="text-xl font-semibold">
        {{ $agent ? 'Modifier l\'agent' : 'Promouvoir un membre en agent' }}
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
            <label class="label">Rôle</label>
            <select wire:model="role" class="input" required>
                <option value="">-- Sélectionner un rôle --</option>
                @foreach($rolesDisponibles as $roleName)
                    <option value="{{ $roleName }}">
                        {{ ucfirst($roleName) }}
                    </option>
                @endforeach
            </select>
            @error('role')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('agents.index') }}" class="btn-secondary">Annuler</a>
            <button class="btn-primary">
                {{ $agent ? 'Mettre à jour' : 'Promouvoir' }}
            </button>
        </div>
    </form>
</div>