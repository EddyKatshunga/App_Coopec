<div class="p-8 bg-gray-50 min-h-screen">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Portefeuille par Zone</h2>
            <p class="text-slate-500 mt-1">Suivi en temps réel des engagements et recouvrements par secteur.</p>
        </div>
        
        @if($isSuperAdmin)
            <div class="flex items-center gap-3 bg-white p-2 rounded-xl shadow-sm border border-slate-200">
                <span class="text-xs font-semibold uppercase text-slate-400 pl-2">Filtrer par agence :</span>
                <select wire:model.live="selectedAgenceId" class="block w-64 rounded-lg border-0 py-1.5 text-slate-900 ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all">
                    <option value="">Toutes les agences</option>
                    @foreach($agences as $agence)
                        <option value="{{ $agence->id }}">{{ $agence->nom }}</option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>

    <div class="bg-white shadow-xl shadow-slate-200/60 rounded-2xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-widest">Zone & Responsable</th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-widest">Crédits</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-widest">Volume Engagé</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-widest">Recouvrement</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-widest">Solde Restant</th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-widest">Détails</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-100">
                    @foreach($zones as $zone)
                        <tr class="hover:bg-indigo-50/30 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-sm">
                                        {{ substr($zone->nom, 0, 2) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-slate-900">{{ $zone->nom }}</div>
                                        <div class="text-xs text-slate-500 flex items-center mt-0.5">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                            {{ $zone->gerant->nom ?? 'Non assigné' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center rounded-md bg-blue-50 px-2.5 py-1 text-sm font-bold text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                    {{ number_format($zone->nb_credits_actifs) }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="text-sm font-black text-slate-900">{{ number_format($zone->sum_engage_usd, 2) }} $</div>
                                <div class="text-[11px] text-slate-400 tabular-nums">{{ number_format($zone->sum_engage_cdf, 0, ',', ' ') }} FC</div>
                            </td>

                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="text-sm font-bold text-emerald-600">+ {{ number_format($zone->sum_rembourse_usd, 2) }} $</div>
                                <div class="text-[11px] text-emerald-500/70 tabular-nums">{{ number_format($zone->sum_rembourse_cdf, 0, ',', ' ') }} FC</div>
                            </td>

                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="text-sm font-black text-orange-600">{{ number_format($zone->sum_engage_usd - $zone->sum_rembourse_usd, 2) }} $</div>
                                <div class="text-[11px] text-orange-400 tabular-nums font-medium">{{ number_format($zone->sum_engage_cdf - $zone->sum_rembourse_cdf, 0, ',', ' ') }} FC</div>
                            </td>

                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <a href="{{ route('agences.zones.show', $zone->uuid) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-600 hover:text-white transition-all duration-200">
                                    Voir détails
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>

                <tfoot class="bg-slate-900 text-white">
                    <tr>
                        <td class="px-6 py-5 font-bold uppercase tracking-widest text-[11px]">Total Portefeuille</td>
                        <td class="px-6 py-5 text-center">
                            <span class="text-xl font-black text-indigo-400">{{ number_format($totals->total_nb) }}</span>
                        </td>
                        <td class="px-6 py-5 text-right whitespace-nowrap border-l border-slate-800">
                            <div class="text-base font-black">{{ number_format($totals->vol_usd, 2) }} $</div>
                            <div class="text-[10px] text-slate-400 font-medium">{{ number_format($totals->vol_cdf, 0, ',', ' ') }} FC</div>
                        </td>
                        <td class="px-6 py-5 text-right whitespace-nowrap">
                            <div class="text-base font-black text-emerald-400">{{ number_format($totals->enc_usd, 2) }} $</div>
                            <div class="text-[10px] text-emerald-500/50 font-medium">{{ number_format($totals->enc_cdf, 0, ',', ' ') }} FC</div>
                        </td>
                        <td class="px-6 py-5 text-center whitespace-nowrap border-l border-slate-800" colspan="2">
                            <div class="text-lg font-black text-orange-400">{{ number_format($totals->vol_usd - $totals->enc_usd, 2) }} $</div>
                            <div class="text-[10px] text-orange-300/60 font-medium">{{ number_format($totals->vol_cdf - $totals->enc_cdf, 0, ',', ' ') }} FC</div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $zones->links() }}
    </div>
</div>