<div class="max-w-5xl mx-auto space-y-6 pb-12">
    <div class="bg-white p-6 rounded-lg shadow-sm flex justify-between items-center border-l-4 border-blue-600">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Rapport de la Journée - Agence : {{$cloture->agence->nom}}</h1>
            <p class="text-gray-500">Date : {{ $cloture->date_cloture->isoFormat('dddd D MMMM YYYY') }}</p>
        </div>
        <div class="flex items-center space-x-4">
            <span class="px-4 py-2 {{ $cloture->statut === 'ouverte' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }} rounded-full font-bold uppercase text-sm">
                {{ $cloture->statut }}
            </span>

            @if($cloture->statut === 'ouverte')
                {{-- Dropdown si ouverte --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" 
                            @click.away="open = false" 
                            class="flex items-center gap-2 bg-slate-900 text-white px-5 py-2.5 rounded-xl font-semibold hover:bg-slate-800 transition-all duration-200 shadow-sm active:scale-95">
                        <span>Actions</span>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="open" 
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-[-10px]"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-[-10px]"
                        class="absolute right-0 mt-3 w-64 bg-white border border-slate-200 rounded-2xl shadow-2xl z-50 overflow-hidden"
                        style="display: none;">
                        
                        <div class="p-2 space-y-1">
                            <a href="{{ route('depenses.create') }}" 
                            class="flex items-center gap-3 px-3 py-2.5 text-slate-600 hover:bg-slate-50 hover:text-indigo-600 rounded-lg transition-colors group">
                                <div class="p-1.5 bg-slate-100 group-hover:bg-indigo-100 rounded-md transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium">Ajouter une dépense</span>
                            </a>

                            <a href="{{ route('revenus.create') }}" 
                            class="flex items-center gap-3 px-3 py-2.5 text-slate-600 hover:bg-slate-50 hover:text-emerald-600 rounded-lg transition-colors group">
                                <div class="p-1.5 bg-slate-100 group-hover:bg-emerald-100 rounded-md transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium">Ajouter un revenu</span>
                            </a>
                        </div>

                        <div class="border-t border-slate-100"></div>

                        <div class="p-2">
                            <a href="{{ route('clotures.valider', $cloture) }}" 
                            class="flex items-center gap-3 px-3 py-2.5 bg-rose-50 text-rose-700 hover:bg-rose-600 hover:text-white rounded-lg transition-all group">
                                <div class="p-1.5 bg-rose-100 group-hover:bg-rose-500 rounded-md transition-colors text-rose-600 group-hover:text-white">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <span class="text-sm font-bold uppercase tracking-tight">Clôturer la journée</span>
                            </a>
                        </div>
                    </div>
                </div>
            @else
                {{-- Dropdown pour les rapports si clôturée --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.away="open = false" class="flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-700 transition shadow-sm">
                        Imprimer les Relevés
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div x-show="open" x-transition class="absolute right-0 mt-2 w-72 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden">
                        {{-- Bouton Rapport Global --}}
                        <a href="{{ route('impressions.rapport.journalier', $cloture) }}" target="_blank" 
                        class="flex items-center bg-gray-800 text-white px-4 py-2 rounded-lg font-bold hover:bg-black transition shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Rapport de la Journée
                        </a>
                        <a href="{{ route('impressions.releve', [$cloture, 'type' => 'epargne']) }}" target="_blank" class="block px-4 py-3 hover:bg-blue-50 border-b border-gray-50 group">
                            <div class="text-sm font-bold text-gray-700 group-hover:text-blue-700">Relevé des Épargnes</div>
                            <div class="text-[10px] text-gray-400">Dépôts: ${{ number_format_fr($cloture->total_depot_usd) }} | Retraits: ${{ number_format_fr($cloture->total_retrait_usd) }}</div>
                        </a>
                        <a href="{{ route('impressions.releve', [$cloture, 'type' => 'remboursements']) }}" target="_blank" class="block px-4 py-3 hover:bg-indigo-50 border-b border-gray-50 group">
                            <div class="text-sm font-bold text-gray-700 group-hover:text-indigo-700">Relevé des Remboursements</div>
                            <div class="text-[10px] text-gray-400">Total: ${{ number_format_fr($cloture->total_remboursement_usd) }} ({{ $cloture->remboursements->count() }} op.)</div>
                        </a>
                        <a href="{{ route('impressions.releve', [$cloture, 'type' => 'credits']) }}" target="_blank" class="block px-4 py-3 hover:bg-purple-50 group">
                            <div class="text-sm font-bold text-gray-700 group-hover:text-purple-700">Relevé des Crédits</div>
                            <div class="text-[10px] text-gray-400">Octroyés: ${{ number_format_fr($cloture->total_credit_usd) }} ({{ $cloture->credits->count() }} op.)</div>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-sm border-t-4 border-green-500">
            <h3 class="text-lg font-bold text-green-700 border-b pb-2 mb-4 uppercase">Entrées (+)</h3>
            <table class="w-full text-sm">
                <tr><td class="py-2 text-green-600 font-bold">Report Coffre</td><td class="text-right font-mono font-bold">${{ number_format_fr($cloture->report_coffre_usd) }}</td><td class="text-right font-mono font-bold">{{ number_format_fr($cloture->report_coffre_cdf) }} FC</td></tr>
                <tr><td class="py-2 text-green-600 font-bold">Report Epargne</td><td class="text-right font-mono font-bold">${{ number_format_fr($cloture->report_epargne_usd) }}</td><td class="text-right font-mono font-bold">{{ number_format_fr($cloture->report_epargne_cdf) }} FC</td></tr>
                <tr class="border-b"><td class="py-2">Revenus</td><td class="text-right font-mono">${{ number_format_fr($cloture->revenus()->where('monnaie', 'USD')->sum('montant')) }}</td><td class="text-right font-mono">{{ number_format_fr($cloture->revenus()->where('monnaie', 'CDF')->sum('montant')) }} FC</td></tr>
                <tr class="border-b"><td class="py-2">Dépôts Épargne</td><td class="text-right font-mono">${{ number_format_fr($cloture->depots()->where('monnaie', 'USD')->sum('montant')) }}</td><td class="text-right font-mono">{{ number_format_fr($cloture->depots()->where('monnaie', 'CDF')->sum('montant')) }} FC</td></tr>
                <tr class="border-b"><td class="py-2 text-indigo-600 font-semibold">Remboursements</td><td class="text-right font-mono text-indigo-600 font-bold">${{ number_format_fr($cloture->remboursements()->where('monnaie', 'USD')->sum('montant')) }}</td><td class="text-right font-mono text-indigo-600 font-bold">{{ number_format_fr($cloture->remboursements()->where('monnaie', 'CDF')->sum('montant')) }} FC</td></tr>
            </table>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border-t-4 border-red-500">
            <h3 class="text-lg font-bold text-red-700 border-b pb-2 mb-4 uppercase">Sorties (-)</h3>
            <table class="w-full text-sm">
                <tr class="border-b"><td class="py-2">Dépenses</td><td class="text-right font-mono">${{ number_format_fr($cloture->depenses()->where('monnaie', 'USD')->sum('montant')) }}</td><td class="text-right font-mono">{{ number_format_fr($cloture->depenses()->where('monnaie', 'CDF')->sum('montant')) }} FC</td></tr>
                <tr class="border-b"><td class="py-2">Retraits Épargne</td><td class="text-right font-mono">${{ number_format_fr($cloture->retraits()->where('monnaie', 'USD')->sum('montant')) }}</td><td class="text-right font-mono">{{ number_format_fr($cloture->retraits()->where('monnaie', 'CDF')->sum('montant')) }} FC</td></tr>
                <tr class="border-b"><td class="py-2 text-purple-600 font-semibold">Crédits Octroyés</td><td class="text-right font-mono text-purple-600 font-bold">${{ number_format_fr($cloture->credits()->where('monnaie', 'USD')->sum('capital')) }}</td><td class="text-right font-mono text-purple-600 font-bold">{{ number_format_fr($cloture->credits()->where('monnaie', 'CDF')->sum('capital')) }} FC</td></tr>
                <tr><td class="py-2 text-red-600 font-bold">Solde Final Epargne</td><td class="text-right font-mono font-bold">${{ $cloture->statut === 'ouverte' ? number_format_fr($cloture->agence->solde_actuel_epargne_usd) : number_format_fr($cloture->solde_epargne_usd) }}</td><td class="text-right font-mono font-bold">{{ $cloture->statut === 'ouverte' ? number_format_fr($cloture->agence->solde_actuel_epargne_cdf) : number_format_fr($cloture->solde_epargne_cdf) }} FC</td></tr>
                <tr><td class="py-2 text-red-600 font-bold">Solde Final Coffre</td><td class="text-right font-mono font-bold">${{ $cloture->statut === 'ouverte' ? number_format_fr($cloture->agence->solde_actuel_coffre_usd) : number_format_fr($cloture->solde_coffre_usd) }}</td><td class="text-right font-mono font-bold">{{ $cloture->statut === 'ouverte' ? number_format_fr($cloture->agence->solde_actuel_coffre_cdf) : number_format_fr($cloture->solde_coffre_cdf) }} FC</td></tr>
            </table>
        </div>
    </div>

    <div class="space-y-6">
        {{-- Section Revenus & Dépenses --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-5 rounded-lg shadow-sm">
                <h4 class="font-bold text-gray-700 mb-3 border-l-4 border-green-400 pl-2">Détail des Revenus</h4>
                @foreach($revenusGroupes as $typeId => $monnaies)
                    <div class="mb-2 p-2 bg-gray-50 rounded">
                        <span class="text-xs font-bold uppercase text-gray-500">{{ $monnaies->first()->first()->typeRevenu->nom ?? 'Autre' }}</span>
                        @foreach($monnaies as $monnaie => $items)
                            <div class="flex justify-between text-sm font-mono">
                                <span>{{ $monnaie }} ({{ $items->count() }} OP.)</span>
                                <span>{{ number_format_fr($items->sum('montant')) }}</span>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>

            <div class="bg-white p-5 rounded-lg shadow-sm">
                <h4 class="font-bold text-gray-700 mb-3 border-l-4 border-red-400 pl-2">Détail des Dépenses</h4>
                @foreach($depensesGroupes as $typeId => $monnaies)
                    <div class="mb-2 p-2 bg-gray-50 rounded">
                        <span class="text-xs font-bold uppercase text-gray-500">{{ $monnaies->first()->first()->typeDepense->nom ?? 'Autre' }}</span>
                        @foreach($monnaies as $monnaie => $items)
                            <div class="flex justify-between text-sm font-mono text-red-600">
                                <span>{{ $monnaie }} ({{ $items->count() }} OP.)</span>
                                <span>{{ number_format_fr($items->sum('montant')) }}</span>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Section Epargne --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-5 rounded-lg shadow-sm">
                <h4 class="font-bold text-gray-700 mb-3 border-l-4 border-blue-400 pl-2">Dépôts par Agent</h4>
                @foreach($depotsGroupes as $agentId => $monnaies)
                    <div class="mb-2 p-2 bg-gray-50 rounded">
                        <span class="text-xs font-bold text-blue-700">{{ $monnaies->first()->first()->agent_collecteur->user->name ?? 'N/A' }}</span>
                        @foreach($monnaies as $monnaie => $items)
                            <div class="flex justify-between text-sm font-mono">
                                <span>{{ $monnaie }} ({{ $items->count() }} OP.)</span>
                                <span>{{ number_format_fr($items->sum('montant')) }}</span>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>

            <div class="bg-white p-5 rounded-lg shadow-sm">
                <h4 class="font-bold text-gray-700 mb-3 border-l-4 border-orange-400 pl-2">Retraits par Caissier</h4>
                @foreach($retraitsGroupes as $userId => $monnaies)
                    <div class="mb-2 p-2 bg-gray-50 rounded">
                        <span class="text-xs font-bold text-orange-700">{{ $monnaies->first()->first()->creator->name ?? 'N/A' }}</span>
                        @foreach($monnaies as $monnaie => $items)
                            <div class="flex justify-between text-sm font-mono text-orange-600">
                                <span>{{ $monnaie }} ({{ $items->count() }} OP.)</span>
                                <span>{{ number_format_fr($items->sum('montant')) }}</span>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>

        {{-- NOUVELLE SECTION DISSOCIÉE : Remboursements vs Crédits --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Bloc Remboursements --}}
            <div class="bg-white p-5 rounded-lg shadow-sm">
                <h4 class="font-bold text-gray-700 mb-3 border-l-4 border-indigo-500 pl-2 text-indigo-700">Remboursements par Zone</h4>
                @forelse($remboursementsGroupes as $zoneId => $monnaies)
                    <div class="mb-2 p-2 bg-gray-50 rounded">
                        <span class="text-xs font-bold text-indigo-800 uppercase">{{ $monnaies->first()->first()->zone->nom ?? 'Inconnue' }}</span>
                        @foreach($monnaies as $monnaie => $items)
                            <div class="flex justify-between text-sm font-mono text-indigo-600">
                                <span>{{ $monnaie }} ({{ $items->count() }} OP.)</span>
                                <span>{{ number_format_fr($items->sum('montant')) }}</span>
                            </div>
                        @endforeach
                    </div>
                @empty
                    <p class="text-xs text-gray-400 italic">Aucun remboursement enregistré.</p>
                @endforelse
            </div>

            {{-- Bloc Crédits --}}
            <div class="bg-white p-5 rounded-lg shadow-sm">
                <h4 class="font-bold text-gray-700 mb-3 border-l-4 border-purple-500 pl-2 text-purple-700">Crédits Octroyés par Zone</h4>
                @forelse($creditsGroupes as $zoneId => $monnaies)
                    <div class="mb-2 p-2 bg-gray-50 rounded">
                        <span class="text-xs font-bold text-purple-800 uppercase">{{ $monnaies->first()->first()->zone->nom ?? 'Inconnue' }}</span>
                        @foreach($monnaies as $monnaie => $items)
                            <div class="space-y-1 mt-1 border-t border-gray-100 pt-1">
                                <div class="flex justify-between text-xs font-bold text-gray-500 italic">
                                    <span>{{ $monnaie }} ({{ $items->count() }} OP.)</span>
                                    <span>Capital + Intérêt</span>
                                </div>
                                <div class="flex justify-between text-sm font-mono text-purple-600">
                                    <span>Capital</span>
                                    <span>{{ number_format_fr($items->sum('capital')) }}</span>
                                </div>
                                <div class="flex justify-between text-xs font-mono text-gray-500">
                                    <span>Intérêt Prévu</span>
                                    <span>{{ number_format_fr($items->sum('interet')) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @empty
                    <p class="text-xs text-gray-400 italic">Aucun crédit octroyé.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Observations (Inchangé) --}}
    @if($cloture->observation_cloture)
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
        <h4 class="text-yellow-800 font-bold">Observation de clôture :</h4>
        <p class="text-yellow-700 mt-1 italic">{{ $cloture->observation_cloture }}</p>
    </div>
    @endif
</div>