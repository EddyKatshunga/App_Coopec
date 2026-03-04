<div class="animate-in fade-in slide-in-from-bottom-4 duration-500">
    {{-- Header de la Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 border-b pb-4">
        <div>
            <h3 class="text-2xl font-black text-gray-800">{{ $title }}</h3>
            <p class="text-sm text-gray-500 font-medium">Récapitulatif des flux par {{ strtolower($relationLabel) }}</p>
        </div>
        
        @php
            // Initialisation des totaux
            $grandTotalUSD = 0;
            $grandTotalCDF = 0;
            $grandTotalOps = 0;
            
            // Détection de la colonne de montant (Capital pour Crédit, sinon montant)
            $amountColumn = (str_contains(strtolower($title), 'crédit') && !str_contains(strtolower($title), 'remboursement')) ? 'capital' : 'montant';
            
            if($data) {
                foreach($data as $group) {
                    $grandTotalOps += $group->count();
                    $grandTotalUSD += $group->where('monnaie', 'USD')->sum($amountColumn);
                    $grandTotalCDF += $group->where('monnaie', 'CDF')->sum($amountColumn);
                }
            }
            
            // Style dynamique selon le type (Entrée vs Sortie)
            $isEntree = str_contains(strtolower($title), 'dépôt') || str_contains(strtolower($title), 'remboursement') || str_contains(strtolower($title), 'revenu');
            $themeColor = $isEntree ? 'emerald' : 'orange';
        @endphp

        <div class="flex gap-4 mt-4 md:mt-0">
            <div class="bg-{{ $themeColor }}-50 border border-{{ $themeColor }}-100 px-4 py-2 rounded-xl text-right">
                <span class="block text-[10px] font-bold text-{{ $themeColor }}-600 uppercase">Total USD</span>
                <span class="text-lg font-mono font-black text-{{ $themeColor }}-900">${{ number_format($grandTotalUSD, 2, ',', ' ') }}</span>
            </div>
            <div class="bg-{{ $themeColor }}-50 border border-{{ $themeColor }}-100 px-4 py-2 rounded-xl text-right">
                <span class="block text-[10px] font-bold text-{{ $themeColor }}-600 uppercase">Total CDF</span>
                <span class="text-lg font-mono font-black text-{{ $themeColor }}-900">{{ number_format($grandTotalCDF, 0, ',', ' ') }} <small>FC</small></span>
            </div>
        </div>
    </div>

    @if($data && count($data) > 0)
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <table class="min-w-full border-separate border-spacing-0">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest border-b">{{ $relationLabel }}</th>
                        <th class="px-6 py-4 text-center text-xs font-black text-gray-400 uppercase tracking-widest border-b">Opérations</th>
                        <th class="px-6 py-4 text-right text-xs font-black text-gray-400 uppercase tracking-widest border-b">Volume USD</th>
                        <th class="px-6 py-4 text-right text-xs font-black text-gray-400 uppercase tracking-widest border-b">Volume CDF</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($data as $id => $group)
                        @php
                            $totalUSD = $group->where('monnaie', 'USD')->sum($amountColumn);
                            $totalCDF = $group->where('monnaie', 'CDF')->sum($amountColumn);
                            $row = $group->first();
                            
                            // Résolution du nom
                            $displayName = 'Non défini';
                            if (isset($row->{$relationName})) {
                                $rel = $row->{$relationName};
                                $displayName = $rel->nom ?? $rel->name ?? $rel->libelle ?? ($rel->user->name ?? 'ID: ' . $id);
                            }
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-500 mr-3 group-hover:bg-{{ $themeColor }}-100 group-hover:text-{{ $themeColor }}-600 transition-colors">
                                        {{ substr($displayName, 0, 1) }}
                                    </div>
                                    <span class="text-sm font-bold text-gray-700">{{ $displayName }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600 font-mono">
                                    {{ $group->count() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-black font-mono {{ $totalUSD > 0 ? 'text-gray-900' : 'text-gray-300' }}">
                                    {{ number_format($totalUSD, 2, ',', ' ') }} $
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-black font-mono {{ $totalCDF > 0 ? 'text-gray-900' : 'text-gray-300' }}">
                                    {{ number_format($totalCDF, 0, ',', ' ') }} FC
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="flex flex-col items-center justify-center py-16 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200">
            <div class="p-4 bg-white rounded-full shadow-sm mb-4">
                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
            </div>
            <p class="text-gray-400 font-bold tracking-tight">Aucune opération enregistrée</p>
        </div>
    @endif
</div>