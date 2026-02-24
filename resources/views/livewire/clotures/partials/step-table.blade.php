<div class="animate-fade-in-up">
    <h3 class="text-xl font-bold text-gray-800 mb-6 border-b pb-2">{{ $title }}</h3>
    
    @php
        $grandTotalUSD = 0;
        $grandTotalCDF = 0;
        $totalOps = 0;
    @endphp

    @if(count($data) > 0)
        <div class="overflow-hidden border border-gray-200 rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $relationLabel }}</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Opérations</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total USD</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total CDF</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($data as $id => $group)
                        @php
                            // Le groupe contient une ou deux lignes (une pour USD, une pour CDF si mixtes)
                            $ops = $group->sum('nbre_operations');
                            $usd = $group->where('monnaie', 'USD')->sum('total_montant');
                            $cdf = $group->where('monnaie', 'CDF')->sum('total_montant');
                            
                            $grandTotalUSD += $usd;
                            $grandTotalCDF += $cdf;
                            $totalOps += $ops;
                            
                            // Récupération du nom (ex: $group->first()->agentCollecteur->nom)
                            // On utilise un accesseur dynamique basé sur le nom de la relation passée en paramètre
                            $entityName = $group->first()->{$relationName}->nom ?? $group->first()->{$relationName}->name ??
                                            $group->first()->{$relationName}->user->name ?? 'Inconnu';
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $entityName }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">{{ $ops }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-mono text-gray-700">${{ number_format($usd, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-mono text-gray-700">{{ number_format($cdf, 2) }} FC</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-100 font-bold">
                    <tr>
                        <td class="px-6 py-4 uppercase text-sm text-gray-800">Total Général</td>
                        <td class="px-6 py-4 text-center text-sm text-gray-800">{{ $totalOps }}</td>
                        <td class="px-6 py-4 text-right text-sm font-mono text-blue-800">${{ number_format($grandTotalUSD, 2) }}</td>
                        <td class="px-6 py-4 text-right text-sm font-mono text-blue-800">{{ number_format($grandTotalCDF, 2) }} FC</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @else
        <div class="bg-gray-50 text-center py-8 rounded-lg border border-dashed border-gray-300">
            <p class="text-gray-500">Aucune transaction de ce type enregistrée aujourd'hui.</p>
        </div>
    @endif
</div>