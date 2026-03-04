<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
        <h3 class="text-lg font-bold text-gray-800">Historique des Remboursements</h3>
        <input type="text" wire:model.live="search" placeholder="Rechercher..." class="text-sm border-gray-300 rounded-md">
    </div>

    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50 text-xs uppercase text-gray-500 font-semibold">
            <tr>
                <th class="px-6 py-3">Date</th>
                <th class="px-6 py-3">Crédit / Membre</th>
                <th class="px-6 py-3">Montant</th>
                <th class="px-6 py-3">Mode</th>
                <th class="px-6 py-3">Reste dû après</th>
                <th class="px-6 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($remboursements as $r)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-sm font-medium">
                        {{ $r->date_paiement->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-bold text-blue-700">{{ $r->credit->reference }}</div>
                        <div class="text-xs text-gray-500">{{ $r->credit->user->name }}</div>
                    </td>
                    <td class="px-6 py-4 font-bold text-green-600">
                        {{ number_format($r->montant, 2, ',', ' ') }} <span class="text-[10px]">{{ $r->monnaie }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-[10px] rounded-full bg-blue-100 text-blue-700 font-bold uppercase">
                            {{ $r->mode_paiement_label }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ number_format($r->reste_du_apres, 2, ',', ' ') }}
                    </td>
                    {{-- Boutons Détails et Suppression --}}
                    <td class="px-6 py-4 text-right flex justify-end gap-3">
                        <a href="{{ route('remboursement.show', $r) }}" wire:navigate class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                            Détails
                        </a>

                        {{-- 1. Vérification Permission Spatie --}}
                        @can('agent.create') 
                            {{-- 2. Vérification Journée Ouverte via le trait du modèle --}}
                            @if($r->canBeDeleted())
                                <button 
                                    wire:click="deleteRecord('App\\Models\\CreditRemboursement', {{ $r->id }})"
                                    wire:confirm="Êtes-vous sûr de vouloir supprimer définitivement ce remboursement ?"
                                    class="text-red-600 hover:text-red-900 text-sm font-medium"
                                >
                                    Supprimer
                                </button>
                            @endif
                        @endcan
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-gray-400">Aucun remboursement trouvé.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="px-6 py-4 border-t">
        {{ $remboursements->links() }}
    </div>
</div>