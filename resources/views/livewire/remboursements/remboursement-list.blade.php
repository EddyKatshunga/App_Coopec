<div class="space-y-4 p-4">
    {{-- ENTÊTE --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <span class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 11l3-3m0 0l3 3m-3-3v8m0-13a9 9 0 110 18 9 9 0 010-18z"></path></svg>
                </span>
                Journal des Remboursements
            </h2>
            
            <a href="{{ route('credit.pret.index') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Encaisser Remboursement
            </a>
        </div>

        <hr class="my-5 border-gray-100">

        {{-- BARRE DE FILTRES --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
            <div class="relative lg:col-span-2">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Référence crédit ou membre..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>

            <input type="date" wire:model.live="date_debut" class="border-gray-200 rounded-lg text-sm focus:ring-indigo-500">
            <input type="date" wire:model.live="date_fin" class="border-gray-200 rounded-lg text-sm focus:ring-indigo-500">

            @can('agence.view.all')
            <select wire:model.live="selected_agence_id" class="border-gray-200 rounded-lg text-sm focus:ring-indigo-500">
                <option value="">Toutes les agences</option>
                @foreach($agences as $agence)
                    <option value="{{ $agence->id }}">{{ $agence->nom }}</option>
                @endforeach
            </select>
            @endcan

            @can('agence.operations.view')
            <div class="flex items-center gap-2 px-3 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                <input type="checkbox" wire:model.live="all_agents" id="all_agents" class="rounded text-indigo-600 focus:ring-indigo-500">
                <label for="all_agents" class="text-[10px] font-bold text-gray-600 uppercase cursor-pointer">Toute l'agence</label>
            </div>
            @endcan
        </div>
    </div>

    {{-- TABLEAU RESPONSIVE --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Crédit & Membre</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right">Montant Payé</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">Mode</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right">Reste à payer</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($remboursements as $r)
                        <tr class="hover:bg-indigo-50/20 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-gray-900">{{ $r->date_paiement->format('d/m/Y') }}</span>
                                <span class="block text-[10px] text-gray-400 font-bold uppercase">{{ $r->agence->nom ?? '' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs font-mono font-bold">
                                        {{ $r->credit->numero_credit }}
                                    </span>
                                    <span class="text-sm font-bold text-gray-800">
                                        {{ $r->credit->user->name }}
                                    </span>
                                </div>
                                <div class="text-[10px] text-gray-400 mt-1 italic">Agent : {{ $r->creator->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <span class="text-sm font-black text-green-600">
                                    {{ number_format($r->montant, 2, ',', ' ') }} {{ $r->monnaie }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-600 uppercase">
                                    {{ $r->mode_paiement ?? 'Cash' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap font-mono text-sm text-gray-500">
                                {{ number_format($r->reste_du_apres, 2, ',', ' ') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('remboursement.show', $r) }}" class="text-gray-400 hover:text-indigo-600" wire:navigate>
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    @can('agent.create') 
                                        @if($r->canBeDeleted())
                                            <button wire:click="deleteRecord('App\\Models\\CreditRemboursement', {{ $r->id }})" 
                                                    wire:confirm="Supprimer ce remboursement ?" 
                                                    class="text-gray-400 hover:text-red-600 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">Aucun encaissement trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-gray-50 border-t">
            {{ $remboursements->links() }}
        </div>
    </div>
</div>