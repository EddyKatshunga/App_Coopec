<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    @php
        $cards = [
            [
                'label' => 'Solde Caisse CDF',
                'value' => number_format($soldesCaisse['CDF'] ?? 0, 0, ',', ' ') . ' CDF',
                'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z',
                'gradient' => 'from-emerald-400 to-teal-500',
                'bgIcon' => 'bg-emerald-100',
                'textIcon' => 'text-emerald-600'
            ],
            [
                'label' => 'Solde Caisse USD',
                'value' => number_format($soldesCaisse['USD'] ?? 0, 2, ',', ' ') . ' USD',
                'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                'gradient' => 'from-blue-400 to-cyan-500',
                'bgIcon' => 'bg-blue-100',
                'textIcon' => 'text-blue-600'
            ],
            [
                'label' => 'Dépôts Épargne',
                'value' => number_format($stats['total_depot_cdf'], 0, ',', ' ') . ' CDF',
                'subvalue' => number_format($stats['total_depot_usd'], 2, ',', ' ') . ' USD',
                'icon' => 'M12 6v6m0 0v6m0-6h6m-6 0H6',
                'gradient' => 'from-green-400 to-emerald-500',
                'bgIcon' => 'bg-green-100',
                'textIcon' => 'text-green-600'
            ],
            [
                'label' => 'Crédits Octroyés',
                'value' => number_format($stats['total_credit_cdf'], 0, ',', ' ') . ' CDF',
                'subvalue' => number_format($stats['total_credit_usd'], 2, ',', ' ') . ' USD',
                'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                'gradient' => 'from-orange-400 to-amber-500',
                'bgIcon' => 'bg-orange-100',
                'textIcon' => 'text-orange-600'
            ],
        ];
    @endphp

    @foreach($cards as $card)
        <div class="relative overflow-hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-5 group hover:shadow-md transition">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-24 h-24 bg-gradient-to-br {{ $card['gradient'] }} rounded-full opacity-10 blur-2xl group-hover:opacity-20 transition"></div>
            <div class="relative z-10 flex items-start justify-between">
                <div class="space-y-2">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ $card['label'] }}</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $card['value'] }}</p>
                    @if(isset($card['subvalue']))
                        <p class="text-sm font-medium text-gray-500">{{ $card['subvalue'] }}</p>
                    @endif
                </div>
                <div class="p-3 {{ $card['bgIcon'] }} rounded-xl flex-shrink-0">
                    <svg class="w-6 h-6 {{ $card['textIcon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}" />
                    </svg>
                </div>
            </div>
        </div>
    @endforeach
</div>