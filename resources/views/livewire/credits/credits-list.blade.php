<div class="space-y-4">
    {{-- ================= FILTRES ================= --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

        <input type="text" wire:model.debounce.500ms="search" placeholder="Recherche numéro ou membre"
               class="input input-bordered w-full">

        <select wire:model="zone_id" class="select select-bordered w-full">
            <option value="">Toutes les zones</option>
            @foreach($zones as $zone)
                <option value="{{ $zone->id }}">{{ $zone->nom }}</option>
            @endforeach
        </select>

        <select wire:model="membre_id" class="select select-bordered w-full">
            <option value="">Tous les membres</option>
            @foreach($membres as $membre)
                <option value="{{ $membre->id }}">{{ $membre->name }}</option>
            @endforeach
        </select>

        <select wire:model="statut" class="select select-bordered w-full">
            <option value="">Tous les statuts</option>
            <option value="en_cours">En cours</option>
            <option value="en_retard">En retard</option>
            <option value="retard_penalite">En retard pénalité</option>
            <option value="termine">Terminé</option>
            <option value="termine_en_retard">Terminé en retard</option>
            <option value="termine_negocie">Terminé négocié</option>
        </select>
    </div>

    {{-- ================= DATE ET NÉGOCIATION ================= --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <input type="date" wire:model="date_debut" class="input input-bordered w-full" placeholder="Date début">
        <input type="date" wire:model="date_fin" class="input input-bordered w-full" placeholder="Date fin">
        <select wire:model="negocie" class="select select-bordered w-full">
            <option value="">Tous</option>
            <option value="1">Négocié</option>
            <option value="0">Non négocié</option>
        </select>
    </div>

    {{-- ================= TABLEAU ================= --}}
    <div class="overflow-x-auto">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Numéro Crédit</th>
                    <th>Membre</th>
                    <th>Zone</th>
                    <th>Date Crédit</th>
                    <th>Total</th>
                    <th>Reste dû</th>
                    <th>Statut</th>
                    <th>Négocié</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($credits as $credit)
                    <tr>
                        <td>{{ $credit->id }}</td>
                        <td>{{ $credit->numero_credit }}</td>
                        <td>{{ $credit->membre->name }}</td>
                        <td>{{ $credit->zone->nom }}</td>
                        <td>{{ $credit->date_credit->format('d/m/Y') }}</td>
                        <td>{{ number_format($credit->total, 2) }}</td>
                        <td>{{ number_format($credit->reste_du, 2) }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $credit->statut)) }}</td>
                        <td>{{ $credit->negocie ? 'Oui' : 'Non' }}</td>
                        <td>
                            <a href="{{ route('credit.show', $credit) }}" class="btn btn-sm btn-primary">Voir</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">Aucun crédit trouvé.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ================= PAGINATION ================= --}}
    <div class="mt-4">
        {{ $credits->links() }}
    </div>
</div>