<div class="space-y-4 p-4">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <span class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                </span>
                Journal des Transactions 
                @if($all_agents) <span class="text-xs font-normal bg-blue-100 text-blue-600 px-2 py-1 rounded">Vue Agence</span> @endif
            </h2>
            
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('comptes.index') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Dépôt
                </a>
                @can('epargne.retrait.create')
                <a href="{{ route('comptes.index') }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                    Retrait
                </a>
                @endcan
            </div>
        </div>

        <hr class="my-5 border-gray-100">

        {{-- BARRE DE FILTRES --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
            {{-- Recherche --}}
            <div class="relative lg:col-span-2">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Membre, compte..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>

            {{-- Filtre Agence (Niveau 3 seulement) --}}
            @can('agence.view.all')
            <select wire:model.live="selected_agence_id" class="border-gray-200 rounded-lg text-sm">
                <option value="">Toutes les agences</option>
                @foreach($agences as $agence)
                    <option value="{{ $agence->id }}">{{ $agence->nom }}</option>
                @endforeach
            </select>
            @endcan

            <select wire:model.live="type" class="border-gray-200 rounded-lg text-sm">
                <option value="">Tous types</option>
                <option value="DEPOT">Dépôts</option>
                <option value="RETRAIT">Retraits</option>
            </select>

            <input type="date" wire:model.live="date_debut" class="border-gray-200 rounded-lg text-sm">
            <input type="date" wire:model.live="date_fin" class="border-gray-200 rounded-lg text-sm">

            {{-- Toggle Toute l'agence (Niveau 2 et 3) --}}
            @can('agence.operations.view')
            <div class="flex items-center gap-2 px-2 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                <input type="checkbox" wire:model.live="all_agents" id="all_agents" class="rounded text-blue-600">
                <label for="all_agents" class="text-[10px] font-bold text-gray-600 uppercase cursor-pointer">Toute l'agence</label>
            </div>
            @endcan
        </div>
    </div>

    {{-- TABLEAU (Reste identique mais avec l'affichage de l'auteur si all_agents est actif) --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Détails Membre</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">Opération</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right">Montant</th>
                        @if($all_agents || auth()->user()->can('agence.view.all'))
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
                                <div class="text-sm font-bold text-blue-600">{{ $transaction->compte->user->name }}</div>
                                <div class="text-xs font-mono text-gray-500">{{ $transaction->compte->numero_compte }}</div>
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
                            
                            @if($all_agents || auth()->user()->can('agence.view.all'))
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs font-medium text-gray-700">{{ $transaction->creator->name ?? 'N/A' }}</div>
                                <div class="text-[10px] text-gray-400">{{ $transaction->agence->nom ?? '-' }}</div>
                            </td>
                            @endif

                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('transaction.show', $transaction) }}" class="text-blue-600 hover:text-blue-800 text-sm font-bold">Voir</a>
                                    @can('agent.create') 
                                        @if($transaction->canBeDeleted())
                                            <button wire:click="deleteRecord('App\\Models\\Transaction', {{ $transaction->id }})" 
                                                    wire:confirm="Supprimer cette opération ?" 
                                                    class="text-red-600 hover:text-red-800 text-sm font-medium">
                                                Supprimer
                                            </button>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        {{-- État vide --}}
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-gray-50">
            {{ $transactions->links() }}
        </div>
    </div>
</div>