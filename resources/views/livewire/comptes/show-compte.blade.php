<div class="max-w-7xl mx-auto p-4 md:p-6 space-y-6 animate-fadeIn">

    {{-- ================= HEADER ================= --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div class="flex items-center gap-4">
                <div class="h-14 w-14 bg-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-10V4m0 10V4m-4 10h.01M9 17h.01M9 14h.01M12 17h.01M12 14h.01M15 17h.01M15 14h.01M12 11h.01M12 7h.01M15 11h.01M15 7h.01"></path></svg>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-gray-900 leading-tight">{{ $compte->intitule }}</h1>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1 text-sm text-gray-500">
                        <span class="flex items-center gap-1"><b class="text-gray-700">N°:</b> {{ $compte->numero_compte }}</span>
                        <span class="h-1 w-1 bg-gray-300 rounded-full hidden md:block"></span>
                        <span class="flex items-center gap-1"><b class="text-gray-700">Propriétaire:</b> {{ $compte->membre->nom }}</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ url()->previous() }}"
                   class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-50 border border-gray-200 rounded-xl hover:bg-gray-100 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Retour
                </a>
            </div>
        </div>
    </div>

    {{-- ================= SOLDES ACTUELS ================= --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="relative overflow-hidden bg-emerald-600 rounded-2xl p-6 shadow-xl shadow-emerald-100 text-white">
            <div class="relative z-10">
                <p class="text-emerald-100 text-xs font-bold uppercase tracking-wider">Solde Global CDF</p>
                <p class="text-3xl font-black mt-1">{{ number_format($compte->solde_cdf, 0, ',', ' ') }} <span class="text-lg font-normal">CDF</span></p>
            </div>
            <svg class="absolute -right-4 -bottom-4 w-32 h-32 text-emerald-500 opacity-20" fill="currentColor" viewBox="0 0 20 20"><path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path></svg>
        </div>

        <div class="relative overflow-hidden bg-amber-500 rounded-2xl p-6 shadow-xl shadow-amber-100 text-white">
            <div class="relative z-10">
                <p class="text-amber-100 text-xs font-bold uppercase tracking-wider">Solde Global USD</p>
                <p class="text-3xl font-black mt-1">{{ number_format($compte->solde_usd, 2) }} <span class="text-lg font-normal">USD</span></p>
            </div>
            <svg class="absolute -right-4 -bottom-4 w-32 h-32 text-amber-400 opacity-20" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a8 8 0 100 16 8 8 0 000-16zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"></path></svg>
        </div>
    </div>

    {{-- ================= ZONE FILTRES (Look Modernisé) ================= --}}
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
            <div class="space-y-1">
                <label class="text-[10px] font-black text-gray-400 uppercase ml-1">Date Début</label>
                <input type="date" wire:model.live="dateDebut" class="w-full border-gray-200 rounded-xl text-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-black text-gray-400 uppercase ml-1">Date Fin</label>
                <input type="date" wire:model.live="dateFin" class="w-full border-gray-200 rounded-xl text-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-black text-gray-400 uppercase ml-1">Monnaie</label>
                <select wire:model.live="monnaie" class="w-full border-gray-200 rounded-xl text-sm focus:ring-blue-500">
                    <option value="">Toutes</option>
                    <option value="USD">USD ($)</option>
                    <option value="CDF">CDF (FC)</option>
                </select>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('impressions.releve.compte', ['compte' => $compte, 'debut' => $dateDebut, 'fin' => $dateFin, 'monnaie' => $monnaie]) }}" 
                   target="_blank"
                   class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-900 text-white rounded-xl font-bold text-sm hover:bg-black transition-all shadow-lg shadow-gray-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Relevé PDF
                </a>
            </div>
        </div>
    </div>

    {{-- ================= STATS RÉACTIVES (Petits Plus) ================= --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border-b-4 border-blue-500 shadow-sm">
            <p class="text-[10px] font-bold text-gray-400 uppercase italic">Flux Dépôts CDF</p>
            <p class="text-lg font-black text-blue-600">{{ number_format($stats['depot_cdf'], 0, ',', ' ') }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border-b-4 border-red-500 shadow-sm">
            <p class="text-[10px] font-bold text-gray-400 uppercase italic">Flux Retraits CDF</p>
            <p class="text-lg font-black text-red-600">{{ number_format($stats['retrait_cdf'], 0, ',', ' ') }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border-b-4 border-blue-500 shadow-sm">
            <p class="text-[10px] font-bold text-gray-400 uppercase italic">Flux Dépôts USD</p>
            <p class="text-lg font-black text-blue-600">{{ number_format($stats['depot_usd'], 2) }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border-b-4 border-red-500 shadow-sm">
            <p class="text-[10px] font-bold text-gray-400 uppercase italic">Flux Retraits USD</p>
            <p class="text-lg font-black text-red-600">{{ number_format($stats['retrait_usd'], 2) }}</p>
        </div>
    </div>

    {{-- ================= TABLEAU DES TRANSACTIONS ================= --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50 flex justify-between items-center">
            <h2 class="text-lg font-bold text-gray-800">Historique Périodique</h2>
            <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-[10px] font-bold uppercase">{{ $transactions->count() }} Opération(s)</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase tracking-wider">Détails Date</th>
                        <th class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase tracking-wider">Type / Sens</th>
                        <th class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase tracking-wider text-right">Mouvement</th>
                        <th class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase tracking-wider text-right">Progressif</th>
                        <th class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase tracking-wider text-center">Validation</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($transactions as $transaction)
                        <tr class="group hover:bg-blue-50/30 transition-colors">
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-gray-700">{{ \Carbon\Carbon::parse($transaction->date_transaction)->translatedFormat('d M Y') }}</p>
                                <p class="text-[10px] text-gray-400 font-mono">{{ $transaction->created_at->format('H:i') }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    @if($transaction->type_transaction === 'DEPOT')
                                        <span class="flex items-center justify-center h-6 w-6 rounded-full bg-emerald-100 text-emerald-600">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                        </span>
                                        <span class="text-xs font-black text-emerald-700">ENTRÉE</span>
                                    @else
                                        <span class="flex items-center justify-center h-6 w-6 rounded-full bg-red-100 text-red-600">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                                        </span>
                                        <span class="text-xs font-black text-red-700">SORTIE</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <p class="text-sm font-black text-gray-800">
                                    {{ number_format_fr($transaction->montant, $transaction->monnaie) }}
                                    <span class="text-[10px] font-normal text-gray-400">{{ $transaction->monnaie }}</span>
                                </p>
                                <p class="text-[10px] text-gray-400 uppercase italic">Ref: {{ $transaction->id }}</p>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <p class="text-sm font-bold text-gray-900">{{ number_format_fr($transaction->solde_apres, $transaction->monnaie) }}</p>
                                <p class="text-[9px] text-gray-400 uppercase">Solde après op.</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($transaction->statut === 'ANNULE')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black bg-red-100 text-red-800 border border-red-200">ANNULÉE</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200">CONFIRMÉE</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="h-16 w-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 text-gray-300">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <p class="text-gray-500 font-medium">Aucun mouvement trouvé pour cette période.</p>
                                    <p class="text-gray-400 text-xs">Ajustez vos filtres pour voir plus de résultats.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>