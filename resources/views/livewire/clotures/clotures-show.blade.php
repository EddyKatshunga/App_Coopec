<div class="max-w-5xl mx-auto space-y-6">
    <div class="bg-white p-6 rounded-lg shadow-sm flex justify-between items-center border-l-4 border-blue-600">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Evolution de la Journée</h1>
            <p class="text-gray-500">Date : {{ \Carbon\Carbon::parse($cloture->date_cloture)->format('d/m/Y') }}</p>
        </div>
        <div>
            <span class="px-4 py-2 bg-gray-100 text-gray-800 rounded-full font-bold uppercase text-sm">
                {{ $cloture->statut }}
            </span>
        </div>
        <div>
        @if($cloture->statut === 'ouverte')
            <a href="{{ route('clotures.valider', $cloture) }}" class="text-red-500 hover:text-red-700" title="Clôturer la journée">
                Cloturer la Journée <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </a>
        @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h3 class="text-lg font-bold text-green-700 border-b pb-2 mb-4">Entrées de fonds (+)</h3>
            <table class="w-full text-sm">
                <tr class="border-b"><td class="py-2">Report de la veille</td><td class="text-right font-mono">${{ $cloture->report_coffre_usd }}</td><td class="text-right font-mono">{{ $cloture->report_coffre_cdf }} FC</td></tr>
                <tr class="border-b"><td class="py-2">Revenus</td><td class="text-right font-mono">${{ $cloture->total_revenu_usd }}</td><td class="text-right font-mono">{{ $cloture->total_revenu_cdf }} FC</td></tr>
                <tr class="border-b"><td class="py-2">Dépôts Épargne</td><td class="text-right font-mono">${{ $cloture->total_depot_usd }}</td><td class="text-right font-mono">{{ $cloture->total_depot_cdf }} FC</td></tr>
                <tr><td class="py-2">Remboursements Crédits</td><td class="text-right font-mono">${{ $cloture->total_remboursement_usd }}</td><td class="text-right font-mono">{{ $cloture->total_remboursement_cdf }} FC</td></tr>
            </table>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h3 class="text-lg font-bold text-red-700 border-b pb-2 mb-4">Sorties de fonds (-)</h3>
            <table class="w-full text-sm">
                <tr class="border-b"><td class="py-2">Dépenses</td><td class="text-right font-mono">${{ $cloture->total_depense_usd }}</td><td class="text-right font-mono">{{ $cloture->total_depense_cdf }} FC</td></tr>
                <tr class="border-b"><td class="py-2">Retraits Épargne</td><td class="text-right font-mono">${{ $cloture->total_retrait_usd }}</td><td class="text-right font-mono">{{ $cloture->total_retrait_cdf }} FC</td></tr>
                <tr><td class="py-2">Déblocages Crédits</td><td class="text-right font-mono">${{ $cloture->total_credit_usd }}</td><td class="text-right font-mono">{{ $cloture->total_credit_cdf }} FC</td></tr>
            </table>
        </div>
    </div>

    <div class="bg-gray-800 text-white p-6 rounded-lg shadow-sm">
        <h3 class="text-xl font-bold mb-6 text-center border-b border-gray-600 pb-2">Rapprochement de Caisse (Théorique vs Physique)</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 divide-x divide-gray-600">
            <div class="px-4">
                <h4 class="text-center text-blue-300 font-bold mb-4 uppercase tracking-widest">Devise USD</h4>
                <div class="flex justify-between mb-2"><span>Solde Théorique (Système)</span> <span class="font-mono text-lg">${{ number_format($cloture->solde_coffre_usd, 2) }}</span></div>
                <div class="flex justify-between mb-2"><span>Solde Physique (Compté)</span> <span class="font-mono text-lg text-yellow-400">${{ number_format($cloture->physique_coffre_usd, 2) }}</span></div>
                
                @php $ecartUsd = $cloture->physique_coffre_usd - $cloture->solde_coffre_usd; @endphp
                <div class="flex justify-between mt-4 pt-4 border-t border-gray-600">
                    <span>Écart</span> 
                    <span class="font-mono font-bold text-lg {{ $ecartUsd == 0 ? 'text-green-400' : 'text-red-400' }}">
                        ${{ number_format($ecartUsd, 2) }}
                    </span>
                </div>
            </div>

            <div class="px-4">
                <h4 class="text-center text-blue-300 font-bold mb-4 uppercase tracking-widest">Devise CDF</h4>
                <div class="flex justify-between mb-2"><span>Solde Théorique (Système)</span> <span class="font-mono text-lg">{{ number_format($cloture->solde_coffre_cdf, 2) }} FC</span></div>
                <div class="flex justify-between mb-2"><span>Solde Physique (Compté)</span> <span class="font-mono text-lg text-yellow-400">{{ number_format($cloture->physique_coffre_cdf, 2) }} FC</span></div>
                
                @php $ecartCdf = $cloture->physique_coffre_cdf - $cloture->solde_coffre_cdf; @endphp
                <div class="flex justify-between mt-4 pt-4 border-t border-gray-600">
                    <span>Écart</span> 
                    <span class="font-mono font-bold text-lg {{ $ecartCdf == 0 ? 'text-green-400' : 'text-red-400' }}">
                        {{ number_format($ecartCdf, 2) }} FC
                    </span>
                </div>
            </div>
        </div>
    </div>

    @if($cloture->observation_cloture)
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
        <h4 class="text-yellow-800 font-bold">Observation du caissier :</h4>
        <p class="text-yellow-700 mt-1">{{ $cloture->observation_cloture }}</p>
    </div>
    @endif
</div>