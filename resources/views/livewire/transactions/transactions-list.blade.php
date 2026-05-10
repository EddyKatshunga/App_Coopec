<div class="space-y-4 p-4">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <span class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                    <!-- icône -->
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                </span>
                Journal des Transactions
            </h2>
            
            <div class="flex flex-wrap gap-2">
                @can('can.level1')
                <a href="{{ route('comptes.index') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Dépôt
                </a>
                @endcan
                @can('can.level3')
                <a href="{{ route('comptes.index') }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                    Retrait
                </a>
                @endcan
            </div>
        </div>

        <hr class="my-5 border-gray-100">

        {{-- BARRE DE FILTRES --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
            {{-- Filtre Agence (Niveau 6 uniquement, obligatoire) --}}
            @can('can.level6')
            <select wire:model.live="selected_agence_id" class="border-gray-200 rounded-lg text-sm" required>
                @foreach($agences as $agence)
                    <option value="{{ $agence->id }}">{{ $agence->nom }}</option>
                @endforeach
            </select>
            @endcan
            
            {{-- Recherche --}}
            <div class="relative lg:col-span-2">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Membre, compte..."
                       class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            {{-- Type --}}
            <select wire:model.live="type" class="border-gray-200 rounded-lg text-sm">
                <option value="">Tous types</option>
                <option value="DEPOT">Dépôts</option>
                <option value="RETRAIT">Retraits</option>
            </select>

            {{-- Monnaie --}}
            <select wire:model.live="monnaie" class="border-gray-200 rounded-lg text-sm">
                <option value="">Toutes les monnaies</option>
                @foreach($monnaies as $monnaie)
                    <option value="{{ $monnaie }}">{{ $monnaie }}</option>
                @endforeach
            </select>

            {{-- Dates --}}
            <input type="date" wire:model.live="date_debut" class="border-gray-200 rounded-lg text-sm">
            <input type="date" wire:model.live="date_fin" class="border-gray-200 rounded-lg text-sm">

            {{-- Agent créateur (Niveau 4+) --}}
            @can('can.level4')
            <select wire:model.live="selected_creator_id" class="border-gray-200 rounded-lg text-sm">
                <option value="">Tous les agents</option>
                @foreach($availableCreators as $creator)
                    <option value="{{ $creator->id }}">{{ $creator->name }}</option>
                @endforeach
            </select>
            @endcan
        </div>
    </div>

    {{-- TABLEAU --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Détails Membre</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">Opération</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right">Montant</th>
                        @if(auth()->user()->can('can.level4') || auth()->user()->can('can.level6'))
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Agent / Agence</th>
                        @endif
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($transactions as $transaction)
                        <tr class="hover:bg-blue-50/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium">{{ \Carbon\Carbon::parse($transaction->date_transaction)->format('d/m/Y') }}</span>
                                <span class="block text-xs text-gray-400">{{ $transaction->created_at->format('H:i') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('compte.show', $transaction->compte->uuid) }}" class="block hover:bg-gray-50">
                                    <div class="text-sm font-bold text-blue-600">{{ $transaction->compte->user->name }}</div>
                                    <div class="text-xs font-mono text-gray-500">{{ $transaction->compte->numero_compte }}</div>
                                </a>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black {{ $transaction->type_transaction === 'DEPOT' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $transaction->type_transaction }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="text-sm font-black {{ $transaction->type_transaction === 'DEPOT' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ number_format($transaction->montant, 2, ',', ' ') }} {{ $transaction->monnaie }}
                                </div>
                                <div class="text-[10px] text-gray-400">Solde : {{ number_format($transaction->solde_apres, 2, ',', ' ') }}</div>
                            </td>
                            
                            @if(auth()->user()->can('can.level4') || auth()->user()->can('can.level6'))
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs font-medium text-gray-700">{{ $transaction->creator->name ?? 'N/A' }}</div>
                                <div class="text-[10px] text-gray-400">{{ $transaction->agence->nom ?? '-' }}</div>
                            </td>
                            @endif

                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('transaction.show', $transaction) }}" class="text-blue-600 hover:text-blue-800 text-sm font-bold">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    @can('can.level4')
                                        @if($transaction->canBeDeleted())
                                            <button wire:click="deleteRecord('App\\Models\\Transaction', {{ $transaction->id }})"
                                                    wire:confirm="Supprimer cette opération ?"
                                                    class="text-red-600 hover:text-red-800 text-sm font-medium">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-gray-500">Aucune transaction trouvée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-gray-50">
            {{ $transactions->links() }}
        </div>
    </div>
</div>