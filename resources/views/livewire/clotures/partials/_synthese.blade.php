<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
        </svg>
        <h3 class="font-semibold text-gray-800">Synthèse des opérations</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="bg-gradient-to-r from-gray-50 to-slate-50">
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Opération</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Total CDF</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Total USD</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Nombre</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @php
                    $operations = [
                        ['label' => 'Dépôts épargne', 'cdf' => $stats['total_depot_cdf'], 'usd' => $stats['total_depot_usd'], 'count' => $stats['nb_transactions'], 'icon' => 'M12 6v6m0 0v6m0-6h6m-6 0H6', 'color' => 'emerald'],
                        ['label' => 'Retraits épargne', 'cdf' => $stats['total_retrait_cdf'], 'usd' => $stats['total_retrait_usd'], 'count' => '—', 'icon' => 'M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'rose'],
                        ['label' => 'Crédits octroyés', 'cdf' => $stats['total_credit_cdf'], 'usd' => $stats['total_credit_usd'], 'count' => $stats['nb_credits'], 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'amber'],
                        ['label' => 'Remboursements', 'cdf' => $stats['total_remboursement_cdf'], 'usd' => $stats['total_remboursement_usd'], 'count' => $stats['nb_remboursements'], 'icon' => 'M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z', 'color' => 'indigo'],
                    ];
                @endphp

                @foreach($operations as $op)
                    <tr class="hover:bg-{{ $op['color'] }}-50/30 transition duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-{{ $op['color'] }}-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-{{ $op['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $op['icon'] }}" />
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-gray-700">{{ $op['label'] }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-gray-800">
                            {{ number_format($op['cdf'], 0, ',', ' ') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-gray-800">
                            {{ number_format($op['usd'], 2, ',', ' ') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-medium text-gray-500">
                            {{ $op['count'] }}
                        </td>
                    </tr>
                @endforeach

                {{-- Ligne Total --}}
                <tr class="bg-gray-50/80 font-bold">
                    <td class="px-6 py-4 text-sm text-gray-700">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Total général
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-right font-bold text-gray-800">
                        {{ number_format(
                            ($stats['total_depot_cdf'] ?? 0) - ($stats['total_retrait_cdf'] ?? 0) + ($stats['total_credit_cdf'] ?? 0) + ($stats['total_remboursement_cdf'] ?? 0),
                            0, ',', ' '
                        ) }}
                    </td>
                    <td class="px-6 py-4 text-sm text-right font-bold text-gray-800">
                        {{ number_format(
                            ($stats['total_depot_usd'] ?? 0) - ($stats['total_retrait_usd'] ?? 0) + ($stats['total_credit_usd'] ?? 0) + ($stats['total_remboursement_usd'] ?? 0),
                            2, ',', ' '
                        ) }}
                    </td>
                    <td class="px-6 py-4"></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>