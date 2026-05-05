<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between bg-white p-4 rounded-xl shadow-sm border border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">{{ $account->nom }}</h1>
            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-600 uppercase">
                Type: {{ $account->type }}
            </span>
        </div>

        <div class="mt-4 md:mt-0 flex gap-2">
            <!-- Boutons d'action rapides -->
            <a href="{{ route('accounts.print', ['account' => $account, 'agence_id' => $agence_id, 'date_debut' => $date_debut, 'date_fin' => $date_fin]) }}" 
                target="_blank"
                class="px-4 py-2 bg-white border border-slate-300 rounded-lg text-sm font-medium hover:bg-slate-50">
                Imprimer le PDF filtré
            </a>
        </div>
    </div>

    <!-- Dashboard Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach(['USD', 'CDF'] as $devise)
            @php $stat = $stats[$devise]; @endphp
            <div class="bg-gradient-to-br {{ $devise == 'USD' ? 'from-blue-700 to-blue-900' : 'from-emerald-700 to-emerald-900' }} rounded-2xl p-6 text-white shadow-lg">
                <div class="flex justify-between items-start mb-4">
                    <p class="opacity-80 font-medium">Compte en {{ $devise }}</p>
                    @if(!$stat['has_balance'])
                        <span class="bg-yellow-500/30 px-2 py-1 rounded text-xs">Agence non filtrée</span>
                    @endif
                </div>

                @if($stat['has_balance'])
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs uppercase opacity-70">Solde initial (avant le {{ \Carbon\Carbon::parse($date_debut)->format('d/m/Y') }})</p>
                            <p class="text-2xl font-bold">{{ number_format($stat['solde_initial'], $devise == 'USD' ? 2 : 0, ',', ' ') }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4 border-t border-white/20 pt-3">
                            <div>
                                <p class="text-xs opacity-70">Débit période</p>
                                <p class="font-semibold">{{ number_format($stat['debit'], $devise == 'USD' ? 2 : 0, ',', ' ') }}</p>
                            </div>
                            <div>
                                <p class="text-xs opacity-70">Crédit période</p>
                                <p class="font-semibold">{{ number_format($stat['credit'], $devise == 'USD' ? 2 : 0, ',', ' ') }}</p>
                            </div>
                        </div>
                        <div class="border-t border-white/20 pt-3">
                            <p class="text-xs uppercase opacity-70">Solde final au {{ \Carbon\Carbon::parse($date_fin)->format('d/m/Y') }}</p>
                            <p class="text-3xl font-bold">{{ number_format($stat['solde_final'], $devise == 'USD' ? 2 : 0, ',', ' ') }}</p>
                        </div>
                    </div>
                @else
                    <div class="text-center py-6">
                        <svg class="w-12 h-12 mx-auto opacity-50 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm">Sélectionnez une agence pour voir les soldes réels (report).</p>
                        <p class="text-xs opacity-70 mt-1">Affichage des seuls mouvements de la période.</p>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Filters Section -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Filtrer par Agence</label>
            <select wire:model.live="agence_id" class="w-full rounded-lg border-slate-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                @foreach($agences as $agence)
                    <option value="{{ $agence->id }}">{{ $agence->nom }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Du</label>
            <input type="date" wire:model.live="date_debut" class="rounded-lg border-slate-300 text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Au</label>
            <input type="date" wire:model.live="date_fin" class="rounded-lg border-slate-300 text-sm">
        </div>
        <button wire:click="$refresh" class="p-2 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
            <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
        </button>
    </div>

    <!-- Main Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-800">Détails des mouvements</h3>
            <div wire:loading class="text-blue-600 text-xs font-medium italic">Mise à jour en cours...</div>
        </div>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                    <th class="p-4 font-semibold">Date & Agence</th>
                    <th class="p-4 font-semibold">Libellé de l'opération</th>
                    <th class="p-4 font-semibold text-right">Débit</th>
                    <th class="p-4 font-semibold text-right">Crédit</th>
                    <th class="p-4 font-semibold text-center">Devise</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                @forelse($mouvements as $line)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="p-4">
                        <div class="font-medium">{{ $line->journalEntry->date_operation->format('d/m/Y') }}</div>
                        <div class="text-xs text-slate-400">{{ $line->journalEntry->agence->nom ?? 'Siège' }}</div>
                    </td>
                    <td class="p-4">
                        <p class="font-medium text-slate-900">{{ $line->journalEntry->libelle }}</p>
                        <p class="text-xs text-slate-400 italic">Réf: {{ $line->journalEntry->uuid }}</p>
                    </td>
                    <td class="p-4 text-right font-mono {{ $line->debit > 0 ? 'text-slate-900' : 'text-slate-300' }}">
                        {{ $line->debit > 0 ? number_format($line->debit, 2) : '-' }}
                    </td>
                    <td class="p-4 text-right font-mono {{ $line->credit > 0 ? 'text-slate-900' : 'text-slate-300' }}">
                        {{ $line->credit > 0 ? number_format($line->credit, 2) : '-' }}
                    </td>
                    <td class="p-4 text-center">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $line->monnaie == 'USD' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' }}">
                            {{ $line->monnaie }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-slate-500 italic">
                        Aucun mouvement trouvé pour cette période ou cette agence.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 bg-slate-50 border-t border-slate-200">
            {{ $mouvements->links() }}
        </div>
    </div>

    <!-- Children Accounts (Optional sidebar-like or bottom section) -->
    @if($account->children->count() > 0)
    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
        <h3 class="font-bold text-slate-800 mb-4">Sous-comptes rattachés</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($account->children as $child)
                <a href="{{ route('accounts.show', $child->uuid) }}" class="p-3 border border-slate-100 rounded-lg hover:border-blue-200 hover:bg-blue-50 transition flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-blue-600">{{ $child->numero }}</span>
                        <p class="text-sm font-medium text-slate-700">{{ $child->nom }}</p>
                    </div>
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"></path></svg>
                </a>
            @endforeach
        </div>
    </div>
    @endif
</div>