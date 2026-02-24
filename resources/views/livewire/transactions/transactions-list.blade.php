<div class="space-y-4 p-4">
    {{-- ================= ENTÊTE & FILTRES ================= --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <span class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                </span>
                Journal des Transactions
            </h2>
            
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('comptes.index') }}" wire:navigate class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Dépôt
                </a>
                @can('epargne.retrait.create')
                <a href="{{ route('comptes.index') }}" wire:navigate class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                    Retrait
                </a>
                @endcan
            </div>
        </div>

        <hr class="my-5 border-gray-100">

        {{-- BARRE DE FILTRES --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="relative">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Rechercher membre ou compte..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>

            <select wire:model.live="type" class="border-gray-200 rounded-lg text-sm focus:ring-blue-500">
                <option value="">Tous les types</option>
                <option value="DEPOT">Dépôts</option>
                <option value="RETRAIT">Retraits</option>
            </select>

            <select wire:model.live="monnaie" class="border-gray-200 rounded-lg text-sm focus:ring-blue-500">
                <option value="">Toutes monnaies</option>
                <option value="USD">USD ($)</option>
                <option value="CDF">CDF (FC)</option>
            </select>

            <input type="date" wire:model.live="date_debut" class="border-gray-200 rounded-lg text-sm focus:ring-blue-500">
            
            <div class="flex items-center gap-2 px-2">
                <input type="checkbox" wire:model.live="all_agents" id="all_agents" class="rounded text-blue-600 focus:ring-blue-500">
                <label for="all_agents" class="text-xs font-semibold text-gray-600 uppercase">Toute l'agence</label>
            </div>
        </div>
    </div>

    {{-- ================= TABLEAU RESPONSIVE ================= --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto min-h-[400px]">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Date & Heure</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Membre</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Compte</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">Opération</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right">Montant</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right">Nouveau Solde</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($transactions as $transaction)
                        <tr class="hover:bg-blue-50/30 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($transaction->date_transaction)->format('d/m/Y') }}</span>
                                <span class="block text-xs text-gray-400">{{ \Carbon\Carbon::parse($transaction->created_at)->format('H:i') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('membre.show', $transaction->compte->membre) }}" wire:navigate class="text-sm font-bold text-blue-600 hover:underline">
                                    {{ $transaction->compte->user->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 font-mono text-sm text-gray-600">
                                {{ $transaction->compte->numero_compte }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($transaction->type_transaction === 'DEPOT')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                        DÉPÔT
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 112 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        RETRAIT
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <span class="text-sm font-black {{ $transaction->type_transaction === 'DEPOT' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ number_format($transaction->montant, 2, ',', ' ') }} {{ $transaction->monnaie }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap font-mono text-sm font-bold text-gray-900 bg-gray-50/50 group-hover:bg-transparent">
                                {{ number_format($transaction->solde_apres, 2, ',', ' ') }} <small>{{ $transaction->monnaie }}</small>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap font-mono text-sm font-bold text-gray-900 bg-gray-50/50 group-hover:bg-transparent">
                                <a href="{{ route('transaction.show', $transaction) }}" wire:navigate >Voir</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0l-8 8-8-8"></path></svg>
                                    <p class="mt-2 text-gray-500">Aucune transaction trouvée pour ces critères.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            {{ $transactions->links() }}
        </div>
    </div>
</div>