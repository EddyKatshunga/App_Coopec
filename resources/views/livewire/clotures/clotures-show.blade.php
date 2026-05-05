<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-8">
    {{-- En-tête avec statut et actions --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-visible">
        <div class="bg-gradient-to-r from-slate-800 to-slate-700 px-6 py-5 sm:px-8 rounded-t-2xl">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                {{-- Informations de la journée --}}
                <div class="space-y-2">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/10 backdrop-blur-sm rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-white tracking-tight">
                                Journée du {{ $cloture->date_cloture->format('d/m/Y') }}
                            </h1>
                            <p class="text-indigo-100 text-sm font-medium mt-0.5">
                                Agence : <strong>{{ $cloture->agence->nom }}</strong>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 ml-16">
                        @if($cloture->statut === 'ouverte')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-400/30 backdrop-blur-sm rounded-full text-emerald-100 text-xs font-semibold">
                                <span class="w-2 h-2 bg-emerald-300 rounded-full animate-pulse"></span>
                                Ouverte
                            </span>
                        @elseif($cloture->statut === 'cloturee')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-400/30 backdrop-blur-sm rounded-full text-blue-100 text-xs font-semibold">
                                <span class="w-2 h-2 bg-blue-300 rounded-full"></span>
                                Clôturée
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-400/30 backdrop-blur-sm rounded-full text-gray-200 text-xs font-semibold">
                                <span class="w-2 h-2 bg-gray-300 rounded-full"></span>
                                Verrouillée
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Boutons d'actions selon le statut --}}
                <div class="flex-shrink-0">
                    @if($cloture->statut === 'ouverte')
                        {{-- Dropdown actions comptables --}}
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open"
                                    @click.away="open = false"
                                    class="inline-flex items-center gap-2 px-6 py-3 bg-white text-slate-700 font-semibold rounded-xl hover:bg-slate-50 shadow-sm transition transform hover:scale-105">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Actions comptables
                                <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                                 class="absolute right-0 mt-3 w-80 bg-white border border-gray-200 rounded-2xl shadow-2xl z-50 overflow-hidden"
                                 >

                                {{-- Liste des opérations comptables --}}
                                <div class="p-2 space-y-1">
                                    @php
                                        $operations = [
                                            ['route' => 'accounting.charge', 'label' => 'Charge (dépense)', 'color' => 'red', 'icon' => 'M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z'],
                                            ['route' => 'accounting.produit', 'label' => 'Produit (revenu)', 'color' => 'emerald', 'icon' => 'M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z'],
                                            ['route' => 'accounting.immobilisation', 'label' => 'Achat immobilisation', 'color' => 'indigo', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                                            ['route' => 'accounting.avance', 'label' => 'Avance sur salaire', 'color' => 'orange', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                                            ['route' => 'accounting.change', 'label' => 'Change de devises', 'color' => 'purple', 'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4'],
                                            ['route' => 'accounting.reglement', 'label' => 'Règlement (Dette/Créance)', 'color' => 'teal', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                                            ['route' => 'accounting.transfert', 'label' => 'Transfert des fonds', 'color' => 'teal', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                                        ];
                                    @endphp

                                    @foreach($operations as $op)
                                        <a href="{{ route($op['route']) }}"
                                           class="flex items-center gap-3 px-3 py-2.5 text-slate-600 hover:bg-{{ $op['color'] }}-50 hover:text-{{ $op['color'] }}-600 rounded-xl transition-all group">
                                            <div class="p-2 bg-slate-100 group-hover:bg-{{ $op['color'] }}-100 rounded-lg transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $op['icon'] }}" />
                                                </svg>
                                            </div>
                                            <span class="text-sm font-medium">{{ $op['label'] }}</span>
                                        </a>
                                    @endforeach
                                </div>

                                {{-- Bouton de clôture --}}
                                <div class="border-t border-gray-100 p-2">
                                    <a href="{{ route('clotures.valider', $cloture) }}"
                                       class="flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-rose-500 to-pink-600 text-white font-bold rounded-xl hover:from-rose-600 hover:to-pink-700 transition shadow-lg shadow-rose-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                        Clôturer la journée
                                    </a>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Dropdown impressions pour journée clôturée --}}
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open"
                                    @click.away="open = false"
                                    class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 shadow-sm transition transform hover:scale-105">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                Imprimer les Relevés
                                <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                                 class="absolute right-0 mt-3 w-96 bg-white border border-gray-200 rounded-2xl shadow-2xl z-50 overflow-hidden"
                                 >

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
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Cartes des soldes et indicateurs --}}
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

    {{-- Tableau synthèse des opérations --}}
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
</div>