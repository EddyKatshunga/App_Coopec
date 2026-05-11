<div class="p-6 space-y-6 bg-slate-50 min-h-screen">
    <!-- Header & Filtres -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Compte de Résultat</h1>
                <p class="text-sm text-slate-500">Francs Congolais (CDF)</p>
            </div>
            
            <div class="flex flex-wrap items-end gap-3">
                @can('can.level6')
                <div class="w-48">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Agence</label>
                    <select wire:model.live="agence_id" class="w-full rounded-lg border-slate-200 text-sm focus:ring-indigo-500">
                        @foreach($agences as $agence)
                            <option value="{{ $agence->id }}">{{ $agence->nom }}</option>
                        @endforeach
                    </select>
                </div>
                @endcan
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Du</label>
                    <input type="date" wire:model.live="date_debut" class="rounded-lg border-slate-200 text-sm">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Au</label>
                    <input type="date" wire:model.live="date_fin" class="rounded-lg border-slate-200 text-sm">
                </div>
            </div>
        </div>
    </div>

    <!-- Résumé du Résultat Net Global -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm overflow-hidden relative">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-bold text-slate-700 text-lg">Performance Globale</h3>
            <span class="text-xs font-mono font-bold px-3 py-1 bg-slate-100 rounded-full text-slate-600">CDF</span>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-center">
            <!-- Total Produits -->
            <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-100">
                <p class="text-xs text-emerald-600 uppercase font-bold tracking-wider mb-1">Total Produits</p>
                <p class="text-2xl font-bold text-emerald-700">{{ number_format($this->resultat['produits'], 0, '.', ' ') }} FC</p>
            </div>

            <!-- Total Charges -->
            <div class="p-4 bg-rose-50 rounded-xl border border-rose-100">
                <p class="text-xs text-rose-600 uppercase font-bold tracking-wider mb-1">Total Charges</p>
                <p class="text-2xl font-bold text-rose-700">{{ number_format($this->resultat['charges'], 0, '.', ' ') }} FC</p>
            </div>

            <!-- Résultat Net de la période -->
            <div class="text-right border-l-2 border-slate-100 pl-6">
                <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider mb-1">Résultat Net (période)</p>
                <p class="text-4xl font-black {{ $this->resultat['net_periode'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                    {{ number_format($this->resultat['net_periode'], 0, '.', ' ') }}
                </p>
                <p class="text-sm mt-1 {{ $this->resultat['net_periode'] >= 0 ? 'text-emerald-500' : 'text-rose-500' }} font-bold">
                    {{ $this->resultat['net_periode'] >= 0 ? 'EXCÉDENT' : 'DÉFICIT' }}
                </p>
            </div>

            <!-- Résultat antérieur + cumul -->
            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Résultat antérieur</p>
                <p class="text-lg font-bold text-slate-700">{{ number_format($this->resultat['resultat_antérieur'], 0, '.', ' ') }} FC</p>
                <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider mt-2">Cumul (antérieur + période)</p>
                <p class="text-xl font-black {{ $this->resultat['cumul'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                    {{ number_format($this->resultat['cumul'], 0, '.', ' ') }} FC
                </p>
            </div>
        </div>

        <!-- Barre de progression comparative -->
        <div class="mt-6 h-3 w-full bg-slate-100 rounded-full flex overflow-hidden">
            @php 
                $total = $this->resultat['produits'] + $this->resultat['charges'];
                $percP = $total > 0 ? ($this->resultat['produits'] / $total) * 100 : 0;
            @endphp
            <div class="bg-emerald-500 h-full transition-all duration-500" style="width: {{ $percP }}%"></div>
            <div class="bg-rose-400 h-full transition-all duration-500" style="width: {{ 100 - $percP }}%"></div>
        </div>
    </div>

    <!-- Détails des Comptes -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Colonne PRODUITS -->
        <div class="space-y-4">
            <div class="flex items-center gap-2 px-2">
                <div class="w-2 h-6 bg-emerald-500 rounded-full"></div>
                <h2 class="font-bold text-slate-800 uppercase text-sm tracking-widest">Produits</h2>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="p-4 text-left font-semibold text-slate-500">Compte</th>
                            <th class="p-4 text-right font-semibold text-slate-500">Montant (CDF)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($comptesProduits as $row)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-4">
                                <span class="block font-bold text-indigo-600">{{ $row->account->numero }}</span>
                                <span class="text-xs text-slate-500">{{ $row->account->nom }}</span>
                            </td>
                            <td class="p-4 text-right font-mono font-medium text-slate-700">
                                {{ number_format(($row->total_credit), 0, '.', ' ') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="p-4 text-center text-slate-400">Aucun produit sur la période</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Colonne CHARGES -->
        <div class="space-y-4">
            <div class="flex items-center gap-2 px-2">
                <div class="w-2 h-6 bg-rose-500 rounded-full"></div>
                <h2 class="font-bold text-slate-800 uppercase text-sm tracking-widest">Charges</h2>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="p-4 text-left font-semibold text-slate-500">Compte</th>
                            <th class="p-4 text-right font-semibold text-slate-500">Montant (CDF)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($comptesCharges as $row)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-4">
                                <span class="block font-bold text-rose-600">{{ $row->account->numero }}</span>
                                <span class="text-xs text-slate-500">{{ $row->account->nom }}</span>
                            </td>
                            <td class="p-4 text-right font-mono font-medium text-slate-700">
                                {{ number_format(($row->total_debit), 0, '.', ' ') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="p-4 text-center text-slate-400">Aucune charge sur la période</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>