<div class="max-w-5xl mx-auto space-y-6 pb-12">
    <div class="bg-white p-6 rounded-lg shadow-sm flex justify-between items-center border-l-4 border-blue-600">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Rapport de la Journée - Agence : {{$cloture->agence->nom}}</h1>
            <p class="text-gray-500">Date : {{ $cloture->date_cloture->format('d/m/Y') }}</p>
        </div>
        <div class="flex items-center space-x-4">
            <span class="px-4 py-2 {{ $cloture->statut === 'ouverte' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }} rounded-full font-bold uppercase text-sm">
                {{ $cloture->statut }}
            </span>
            @if($cloture->statut === 'ouverte')
                <a href="{{ route('clotures.valider', $cloture) }}" wire:navigate class="flex items-center text-red-600 hover:text-red-800 font-bold bg-red-50 px-4 py-2 rounded-lg transition">
                    Clôturer la Journée 
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-sm border-t-4 border-green-500">
            <h3 class="text-lg font-bold text-green-700 border-b pb-2 mb-4 uppercase">Entrées (+)</h3>
            <table class="w-full text-sm">
                <tr class="border-b"><td class="py-2">Report Coffre</td><td class="text-right font-mono">${{ number_format_fr($cloture->report_coffre_usd) }}</td><td class="text-right font-mono">{{ number_format_fr($cloture->report_coffre_cdf) }} FC</td></tr>
                <tr class="border-b"><td class="py-2">Revenus</td><td class="text-right font-mono">${{ number_format_fr($cloture->total_revenu_usd) }}</td><td class="text-right font-mono">{{ number_format_fr($cloture->total_revenu_cdf) }} FC</td></tr>
                <tr class="border-b"><td class="py-2">Dépôts Épargne</td><td class="text-right font-mono">${{ number_format_fr($cloture->total_depot_usd) }}</td><td class="text-right font-mono">{{ number_format_fr($cloture->total_depot_cdf) }} FC</td></tr>
                <tr><td class="py-2 text-green-600 font-bold">Remboursements</td><td class="text-right font-mono font-bold">${{ number_format_fr($cloture->total_remboursement_usd) }}</td><td class="text-right font-mono font-bold">{{ number_format_fr($cloture->total_remboursement_cdf) }} FC</td></tr>
            </table>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border-t-4 border-red-500">
            <h3 class="text-lg font-bold text-red-700 border-b pb-2 mb-4 uppercase">Sorties (-)</h3>
            <table class="w-full text-sm">
                <tr class="border-b"><td class="py-2">Dépenses</td><td class="text-right font-mono">${{ number_format_fr($cloture->total_depense_usd) }}</td><td class="text-right font-mono">{{ number_format_fr($cloture->total_depense_cdf) }} FC</td></tr>
                <tr class="border-b"><td class="py-2">Retraits Épargne</td><td class="text-right font-mono">${{ number_format_fr($cloture->total_retrait_usd) }}</td><td class="text-right font-mono">{{ number_format_fr($cloture->total_retrait_cdf) }} FC</td></tr>
                <tr><td class="py-2 text-red-600 font-bold">Crédits Octroyés</td><td class="text-right font-mono font-bold">${{ number_format_fr($cloture->total_credit_usd) }}</td><td class="text-right font-mono font-bold">{{ number_format_fr($cloture->total_credit_cdf) }} FC</td></tr>
            </table>
        </div>
    </div>

    <div class="space-y-6">
        
        {{-- Section Revenus & Dépenses --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Revenus par Type --}}
            <div class="bg-white p-5 rounded-lg shadow-sm">
                <h4 class="font-bold text-gray-700 mb-3 border-l-4 border-green-400 pl-2">Détail des Revenus</h4>
                @foreach($revenusGroupes as $typeId => $monnaies)
                    <div class="mb-2 p-2 bg-gray-50 rounded">
                        <span class="text-xs font-bold uppercase text-gray-500">{{ $monnaies->first()->first()->typeRevenu->nom ?? 'Autre' }}</span>
                        @foreach($monnaies as $monnaie => $items)
                            <div class="flex justify-between text-sm font-mono">
                                <span>{{ $monnaie }}</span>
                                <span>{{ number_format_fr($items->sum('montant')) }}</span>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>

            {{-- Dépenses par Type --}}
            <div class="bg-white p-5 rounded-lg shadow-sm">
                <h4 class="font-bold text-gray-700 mb-3 border-l-4 border-red-400 pl-2">Détail des Dépenses</h4>
                @foreach($depensesGroupes as $typeId => $monnaies)
                    <div class="mb-2 p-2 bg-gray-50 rounded">
                        <span class="text-xs font-bold uppercase text-gray-500">{{ $monnaies->first()->first()->typeDepense->nom ?? 'Autre' }}</span>
                        @foreach($monnaies as $monnaie => $items)
                            <div class="flex justify-between text-sm font-mono text-red-600">
                                <span>{{ $monnaie }}</span>
                                <span>{{ number_format_fr($items->sum('montant')) }}</span>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Section Epargne (Dépôts & Retraits) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-5 rounded-lg shadow-sm">
                <h4 class="font-bold text-gray-700 mb-3 border-l-4 border-blue-400 pl-2">Dépôts par Agent</h4>
                @foreach($depotsGroupes as $agentId => $monnaies)
                    <div class="mb-2 p-2 bg-gray-50 rounded">
                        <span class="text-xs font-bold text-blue-700">{{ $monnaies->first()->first()->agent_collecteur->user->name ?? 'N/A' }}</span>
                        @foreach($monnaies as $monnaie => $items)
                            <div class="flex justify-between text-sm font-mono">
                                <span>{{ $monnaie }}</span>
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
                                <span>{{ $monnaie }}</span>
                                <span>{{ number_format_fr($items->sum('montant')) }}</span>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Section Crédits & Zones (Capital vs Intérêts) --}}
        <div class="bg-white p-5 rounded-lg shadow-sm">
            <h4 class="font-bold text-gray-700 mb-4 border-l-4 border-purple-500 pl-2 text-center uppercase">Performance par Zone</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- On boucle sur les zones qui ont soit des crédits soit des remboursements --}}
                @foreach($creditsGroupes as $zoneId => $monnaies)
                    <div class="p-3 border rounded bg-indigo-50">
                        <p class="font-bold text-indigo-800 text-center border-b border-indigo-200 mb-2 uppercase text-xs">
                            Zone: {{ $monnaies->first()->first()->zone->nom ?? 'Inconnue' }}
                        </p>
                        @foreach($monnaies as $monnaie => $items)
                            <div class="text-xs space-y-1">
                                <p class="font-bold text-gray-600 underline">Monnaie: {{ $monnaie }}</p>
                                <div class="flex justify-between italic">
                                    <span>Crédits (Cap.):</span>
                                    <span>{{ number_format_fr($items->sum('capital')) }}</span>
                                </div>
                                <div class="flex justify-between text-purple-700">
                                    <span>Intérêts Gén.:</span>
                                    <span>{{ number_format_fr($items->sum('interet')) }}</span>
                                </div>
                                {{-- Affichage des remboursements pour la même zone/monnaie si dispo --}}
                                @if(isset($remboursementsGroupes[$zoneId][$monnaie]))
                                    <div class="flex justify-between text-green-700 font-bold pt-1 border-t border-indigo-100">
                                        <span>Rembours.:</span>
                                        <span>{{ number_format_fr($remboursementsGroupes[$zoneId][$monnaie]->sum('montant')) }}</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- Observations --}}
    @if($cloture->observation_cloture)
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
        <h4 class="text-yellow-800 font-bold">Observation de clôture :</h4>
        <p class="text-yellow-700 mt-1 italic">{{ $cloture->observation_cloture }}</p>
    </div>
    @endif
</div>