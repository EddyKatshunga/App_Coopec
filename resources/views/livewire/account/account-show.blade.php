<div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between bg-white p-4 rounded-xl shadow-sm border border-slate-200 gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900">{{ $account->nom }}</h1>
            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-600 uppercase inline-block mt-1">
                Type: {{ $account->type }}
            </span>
        </div>

        <div class="flex gap-2">
            <!-- Boutons d'action rapides -->
            <a href="{{ route('accounts.print', ['account' => $account, 'agence_id' => $agence_id, 'date_debut' => $date_debut, 'date_fin' => $date_fin]) }}" 
                target="_blank"
                class="px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm font-medium hover:bg-slate-50 text-center whitespace-nowrap">
                Imprimer le PDF filtré
            </a>
        </div>
    </div>

    <!-- Dashboard Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
        @foreach(['USD', 'CDF'] as $devise)
            @php $stat = $stats[$devise]; @endphp
            <div class="bg-gradient-to-br {{ $devise == 'USD' ? 'from-blue-700 to-blue-900' : 'from-emerald-700 to-emerald-900' }} rounded-2xl p-5 sm:p-6 text-white shadow-lg">
                <div class="flex justify-between items-start mb-4">
                    <p class="opacity-80 font-medium text-sm sm:text-base">Compte en {{ $devise }}</p>
                    @if(!$stat['has_balance'])
                        <span class="bg-yellow-500/30 px-2 py-1 rounded text-xs">Agence non filtrée</span>
                    @endif
                </div>

                @if($stat['has_balance'])
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs uppercase opacity-70">Solde initial (avant le {{ \Carbon\Carbon::parse($date_debut)->format('d/m/Y') }})</p>
                            <p class="text-xl sm:text-2xl font-bold break-words">{{ number_format($stat['solde_initial'], $devise == 'USD' ? 2 : 0, ',', ' ') }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4 border-t border-white/20 pt-3">
                            <div>
                                <p class="text-xs opacity-70">Débit période</p>
                                <p class="font-semibold text-sm sm:text-base break-words">{{ number_format($stat['debit'], $devise == 'USD' ? 2 : 0, ',', ' ') }}</p>
                            </div>
                            <div>
                                <p class="text-xs opacity-70">Crédit période</p>
                                <p class="font-semibold text-sm sm:text-base break-words">{{ number_format($stat['credit'], $devise == 'USD' ? 2 : 0, ',', ' ') }}</p>
                            </div>
                        </div>
                        <div class="border-t border-white/20 pt-3">
                            <p class="text-xs uppercase opacity-70">Solde final au {{ \Carbon\Carbon::parse($date_fin)->format('d/m/Y') }}</p>
                            <p class="text-2xl sm:text-3xl font-bold break-words">{{ number_format($stat['solde_final'], $devise == 'USD' ? 2 : 0, ',', ' ') }}</p>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4 sm:py-6">
                        <svg class="w-10 h-10 sm:w-12 sm:h-12 mx-auto opacity-50 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-xs sm:text-sm">Sélectionnez une agence pour voir les soldes réels (report).</p>
                        <p class="text-xs opacity-70 mt-1">Affichage des seuls mouvements de la période.</p>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Filters Section - Responsive -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 flex flex-col sm:flex-row flex-wrap gap-3 sm:gap-4 items-stretch sm:items-end">
        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Filtrer par Agence</label>
            <select wire:model.live="agence_id" class="w-full rounded-lg border-slate-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                @foreach($agences as $agence)
                    <option value="{{ $agence->id }}">{{ $agence->nom }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-[140px]">
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Du</label>
            <input type="date" wire:model.live="date_debut" class="w-full rounded-lg border-slate-300 text-sm">
        </div>
        <div class="flex-1 min-w-[140px]">
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Au</label>
            <input type="date" wire:model.live="date_fin" class="w-full rounded-lg border-slate-300 text-sm">
        </div>
        <div class="flex-shrink-0">
            <button wire:click="$refresh" class="w-full sm:w-auto px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-lg transition flex items-center justify-center">
                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                <span class="sr-only">Rafraîchir</span>
            </button>
        </div>
    </div>

    <!-- Main Table - Très responsive avec overflow-x-auto -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-3 sm:p-4 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
            <h3 class="font-bold text-slate-800 text-sm sm:text-base">Détails des mouvements</h3>
            <div wire:loading class="text-blue-600 text-xs font-medium italic">Mise à jour en cours...</div>
        </div>
        
        <!-- Conteneur dédié au scroll horizontal pour le tableau -->
        <div class="overflow-x-auto relative">
            <div class="min-w-[720px] md:min-w-full">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                            <th class="px-3 py-3 sm:px-4 sm:py-4 font-semibold">Date & Agence</th>
                            <th class="px-3 py-3 sm:px-4 sm:py-4 font-semibold">Libellé de l'opération</th>
                            <th class="px-3 py-3 sm:px-4 sm:py-4 font-semibold text-right">Débit</th>
                            <th class="px-3 py-3 sm:px-4 sm:py-4 font-semibold text-right">Crédit</th>
                            <th class="px-3 py-3 sm:px-4 sm:py-4 font-semibold text-center">Devise</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                        @forelse($mouvements as $line)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-3 py-2 sm:px-4 sm:py-4 align-top">
                                <div class="font-medium whitespace-nowrap">{{ $line->journalEntry->date_operation->format('d/m/Y') }}</div>
                                <div class="text-xs text-slate-400 truncate max-w-[140px] sm:max-w-none">{{ $line->journalEntry->agence->nom ?? 'Siège' }}</div>
                            </td>
                            <td class="px-3 py-2 sm:px-4 sm:py-4">
                                <p class="font-medium text-slate-900 break-words">{{ $line->journalEntry->libelle }}</p>
                                <p class="text-xs text-slate-400 italic break-all">Réf: {{ $line->journalEntry->uuid }}</p>
                             </td>
                            <td class="px-3 py-2 sm:px-4 sm:py-4 text-right font-mono whitespace-nowrap {{ $line->debit > 0 ? 'text-slate-900' : 'text-slate-300' }}">
                                {{ $line->debit > 0 ? number_format($line->debit, 2) : '-' }}
                             </td>
                            <td class="px-3 py-2 sm:px-4 sm:py-4 text-right font-mono whitespace-nowrap {{ $line->credit > 0 ? 'text-slate-900' : 'text-slate-300' }}">
                                {{ $line->credit > 0 ? number_format($line->credit, 2) : '-' }}
                             </td>
                            <td class="px-3 py-2 sm:px-4 sm:py-4 text-center whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $line->monnaie == 'USD' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' }}">
                                    {{ $line->monnaie }}
                                </span>
                             </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-500 italic">
                                Aucun mouvement trouvé pour cette période ou cette agence.
                             </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="p-3 sm:p-4 bg-slate-50 border-t border-slate-200">
            {{ $mouvements->links() }}
        </div>
    </div>

    <!-- Children Accounts (Optional sidebar-like or bottom section) -->
    @if($account->children->count() > 0)
    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
        <h3 class="font-bold text-slate-800 mb-4 text-sm sm:text-base">Sous-comptes rattachés</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 sm:gap-4">
            @foreach($account->children as $child)
                <a href="{{ route('accounts.show', $child->uuid) }}" class="p-3 border border-slate-100 rounded-lg hover:border-blue-200 hover:bg-blue-50 transition flex items-center justify-between">
                    <div class="min-w-0 flex-1">
                        <span class="text-xs font-bold text-blue-600">{{ $child->numero }}</span>
                        <p class="text-sm font-medium text-slate-700 truncate">{{ $child->nom }}</p>
                    </div>
                    <svg class="w-4 h-4 text-slate-400 flex-shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"></path></svg>
                </a>
            @endforeach
        </div>
    </div>
    @endif
</div>