<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" @click.away="open = false"
            class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 shadow-sm transition transform hover:scale-105">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
        </svg>
        Imprimer les Relevés
        <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div x-show="open" x-transition
         class="absolute right-0 mt-3 w-96 bg-white border border-gray-200 rounded-2xl shadow-2xl z-50 overflow-hidden">
        @php
            $rapports = [
                [
                    'route' => 'impressions.rapport.journalier',
                    'params' => $cloture,
                    'label' => 'Rapport de la Journée',
                    'desc' => 'Écritures complètes, soldes, synthèse',
                    'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                    'color' => 'blue'
                ],
                [
                    'route' => 'impressions.releve',
                    'params' => [$cloture, 'type' => 'epargne'],
                    'label' => 'Relevé des Épargnes',
                    'desc' => 'Dépôts: ' . number_format($stats['total_depot_cdf'], 0, ',', ' ') . ' CDF / ' . number_format($stats['total_depot_usd'], 2, ',', ' ') . ' USD',
                    'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                    'color' => 'emerald'
                ],
                [
                    'route' => 'impressions.releve',
                    'params' => [$cloture, 'type' => 'remboursements'],
                    'label' => 'Relevé des Remboursements',
                    'desc' => 'Total: ' . number_format($stats['total_remboursement_cdf'], 0, ',', ' ') . ' CDF (' . $stats['nb_remboursements'] . ' op.)',
                    'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
                    'color' => 'indigo'
                ],
                [
                    'route' => 'impressions.releve',
                    'params' => [$cloture, 'type' => 'credits'],
                    'label' => 'Relevé des Crédits',
                    'desc' => 'Octroyés: ' . number_format($stats['total_credit_cdf'], 0, ',', ' ') . ' CDF (' . $stats['nb_credits'] . ' op.)',
                    'icon' => 'M12 6v6m0 0v6m0-6h6m-6 0H6',
                    'color' => 'purple'
                ],
            ];
        @endphp
        @foreach($rapports as $rapport)
            <a href="{{ route($rapport['route'], $rapport['params']) }}" target="_blank"
               class="flex items-start gap-4 px-4 py-3.5 hover:bg-{{ $rapport['color'] }}-50 border-b border-gray-100 last:border-b-0 group transition">
                <div class="p-2 bg-gray-100 group-hover:bg-{{ $rapport['color'] }}-100 rounded-lg transition-colors flex-shrink-0">
                    <svg class="w-5 h-5 text-gray-500 group-hover:text-{{ $rapport['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $rapport['icon'] }}" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-700 group-hover:text-{{ $rapport['color'] }}-700">{{ $rapport['label'] }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $rapport['desc'] }}</p>
                </div>
            </a>
        @endforeach
    </div>
</div>