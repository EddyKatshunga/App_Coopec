<div class="max-w-7xl mx-auto py-8">
    <div class="mb-8 bg-white p-4 rounded-xl shadow-sm border border-slate-200 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Suivi des Dettes et Créances</h2>
            <p class="text-sm text-slate-500">Analyse de toutes les dettes et créances par Agence et par Devise</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            @if($isSuperAdmin)
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase">Agence</label>
                    <select wire:model.live="agence_id" class="text-sm border-slate-200 rounded-lg">
                        @foreach($agences as $agence)
                            <option value="{{ $agence->id }}">{{ $agence->nom }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="flex items-center gap-2">
                <label class="text-xs font-bold text-slate-500 uppercase">Du</label>
                <input type="date" wire:model.live="date_debut" class="text-sm border-slate-200 rounded-lg">
            </div>
            <div class="flex items-center gap-2">
                <label class="text-xs font-bold text-slate-500 uppercase">Au</label>
                <input type="date" wire:model.live="date_fin" class="text-sm border-slate-200 rounded-lg">
            </div>
            <div class="flex items-center gap-2 pl-4 border-l border-slate-200">
                <label class="text-xs font-bold text-slate-500 uppercase">Devise</label>
                <select wire:model.live="monnaie" class="text-sm border-slate-200 rounded-lg font-bold">
                    <option value="USD">USD</option>
                    <option value="CDF">CDF</option>
                </select>
            </div>
        </div>
    </div>

    <!-- KPIs Résumé avec reports -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        {{-- Dettes --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="bg-rose-50 px-6 py-4 border-b border-rose-100">
                <h3 class="text-sm font-bold text-rose-800 uppercase">Dettes Fournisseurs (40)</h3>
            </div>
            <div class="p-6 space-y-4">
                @if($data['dettes']['has_balance'])
                    <div>
                        <p class="text-xs text-slate-500 uppercase">Solde initial (avant le {{ \Carbon\Carbon::parse($date_debut)->format('d/m/Y') }})</p>
                        <p class="text-2xl font-bold text-slate-800">{{ number_format($data['dettes']['solde_initial'], 2, ',', ' ') }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-slate-500">+ Nouvelles dettes</p>
                            <p class="font-semibold text-rose-600">{{ number_format($data['dettes']['nouvelles'], 2, ',', ' ') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500">- Dettes payées</p>
                            <p class="font-semibold text-emerald-600">{{ number_format($data['dettes']['payees'], 2, ',', ' ') }}</p>
                        </div>
                    </div>
                    <div class="pt-2 border-t border-slate-100">
                        <p class="text-xs text-slate-500 uppercase">Solde final au {{ \Carbon\Carbon::parse($date_fin)->format('d/m/Y') }}</p>
                        <p class="text-3xl font-bold text-slate-900">{{ number_format($data['dettes']['solde_final'], 2, ',', ' ') }}</p>
                    </div>
                @else
                    <div class="text-center py-4 text-slate-400">
                        <svg class="w-10 h-10 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <p class="text-sm">Sélectionnez une agence pour voir le solde reporté.</p>
                        <p class="text-xs">Affichage des seuls mouvements de la période.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Créances --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="bg-emerald-50 px-6 py-4 border-b border-emerald-100">
                <h3 class="text-sm font-bold text-emerald-800 uppercase">Créances Clients (45)</h3>
            </div>
            <div class="p-6 space-y-4">
                @if($data['creances']['has_balance'])
                    <div>
                        <p class="text-xs text-slate-500 uppercase">Solde initial (avant le {{ \Carbon\Carbon::parse($date_debut)->format('d/m/Y') }})</p>
                        <p class="text-2xl font-bold text-slate-800">{{ number_format($data['creances']['solde_initial'], 2, ',', ' ') }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-slate-500">+ Nouvelles créances</p>
                            <p class="font-semibold text-emerald-600">{{ number_format($data['creances']['nouvelles'], 2, ',', ' ') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500">- Encaissements</p>
                            <p class="font-semibold text-rose-600">{{ number_format($data['creances']['encaissees'], 2, ',', ' ') }}</p>
                        </div>
                    </div>
                    <div class="pt-2 border-t border-slate-100">
                        <p class="text-xs text-slate-500 uppercase">Solde final au {{ \Carbon\Carbon::parse($date_fin)->format('d/m/Y') }}</p>
                        <p class="text-3xl font-bold text-slate-900">{{ number_format($data['creances']['solde_final'], 2, ',', ' ') }}</p>
                    </div>
                @else
                    <div class="text-center py-4 text-slate-400">
                        <svg class="w-10 h-10 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <p class="text-sm">Sélectionnez une agence pour voir le solde reporté.</p>
                        <p class="text-xs">Affichage des seuls mouvements de la période.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Position Nette (optionnel) --}}
    @if($data['dettes']['has_balance'] && $data['creances']['has_balance'])
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-8">
        <h3 class="text-sm font-bold text-slate-500 uppercase mb-2">Conclusion</h3>
        <p class="text-xs text-slate-500 mt-2">
            @if($data['position_nette'] > 0)
                L'entreprise a plus de créances que de dettes.
            @elseif($data['position_nette'] < 0)
                L'entreprise a plus de dettes que de créances.
            @else
                Position à l'équilibre parfait.
            @endif
        </p>
    </div>
    @endif

    {{-- Détails des mouvements (Tableaux) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Tableau Dettes --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="bg-rose-50 px-4 py-3 border-b border-rose-100">
                <h4 class="font-bold text-rose-800">Mouvements de la période - Dettes</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500 font-semibold">
                        <tr><th class="px-4 py-3">Date</th><th class="px-4 py-3">Libellé</th><th class="px-4 py-3 text-right">Augmentation</th><th class="px-4 py-3 text-right">Paiement</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($data['dettes']['lignes'] as $ligne)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">{{ $ligne->journalEntry->date_operation->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 font-medium">{{ $ligne->journalEntry->libelle }}</td>
                                <td class="px-4 py-3 text-right text-rose-600">{{ $ligne->credit > 0 ? number_format($ligne->credit, 2, ',', ' ') : '-' }}</td>
                                <td class="px-4 py-3 text-right text-emerald-600">{{ $ligne->debit > 0 ? number_format($ligne->debit, 2, ',', ' ') : '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-slate-400">Aucun mouvement</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tableau Créances --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="bg-emerald-50 px-4 py-3 border-b border-emerald-100">
                <h4 class="font-bold text-emerald-800">Mouvements de la période - Créances</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500 font-semibold">
                        <tr><th class="px-4 py-3">Date</th><th class="px-4 py-3">Libellé</th><th class="px-4 py-3 text-right">Augmentation</th><th class="px-4 py-3 text-right">Encaissement</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($data['creances']['lignes'] as $ligne)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">{{ $ligne->journalEntry->date_operation->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 font-medium">{{ $ligne->journalEntry->libelle }}</td>
                                <td class="px-4 py-3 text-right text-emerald-600">{{ $ligne->debit > 0 ? number_format($ligne->debit, 2, ',', ' ') : '-' }}</td>
                                <td class="px-4 py-3 text-right text-rose-600">{{ $ligne->credit > 0 ? number_format($ligne->credit, 2, ',', ' ') : '-' }}</td>
                             </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-slate-400">Aucun mouvement</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>