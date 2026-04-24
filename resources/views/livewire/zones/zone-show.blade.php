<div class="space-y-8 pb-10">
    {{-- En-tête de la Zone (Style Carte de présentation) --}}
    <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-2xl p-6 md:p-8 shadow-lg text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-5 rounded-full blur-2xl"></div>
        <div class="relative z-10 flex flex-col md:flex-row justify-between md:items-center gap-4">
            <div>
                <div class="flex items-center space-x-3 mb-1">
                    <span class="px-3 py-1 bg-slate-700/50 text-slate-300 text-xs font-semibold tracking-wider rounded-full ring-1 ring-slate-600">
                        ZONE {{ $zone->code }}
                    </span>
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight text-white">{{ $zone->nom }}</h1>
            </div>
            
            @if($zone->gerant)
                <div class="flex items-center space-x-3 bg-slate-800/50 p-3 rounded-xl ring-1 ring-slate-700/50 backdrop-blur-sm">
                    <div class="h-10 w-10 rounded-full bg-slate-600 flex items-center justify-center text-slate-300 font-bold">
                        {{ substr($zone->gerant->nom, 0, 2) }}
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-medium uppercase tracking-wide">Gérant responsable</p>
                        <p class="text-sm font-semibold text-slate-100">{{ $zone->gerant->nom }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Cartes des KPIs par Devise --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach(['CDF', 'USD'] as $devise)
            @php
                $colorTheme = $devise === 'CDF' ? 'blue' : 'emerald';
                $ringColor = $devise === 'CDF' ? 'ring-blue-100' : 'ring-emerald-100';
                $iconBg = $devise === 'CDF' ? 'bg-blue-50 text-blue-600' : 'bg-emerald-50 text-emerald-600';
            @endphp
            <div class="bg-white rounded-2xl p-6 shadow-sm ring-1 ring-slate-200/60 hover:shadow-md transition-shadow duration-300">
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 rounded-lg {{ $iconBg }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h2 class="text-xl font-bold text-slate-800">Portefeuille {{ $devise }}</h2>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold 
                        @if(($dashboard[$devise]['niveau_risque'] ?? 'faible') === 'faible') bg-emerald-100 text-emerald-700
                        @elseif(($dashboard[$devise]['niveau_risque'] ?? 'moyen') === 'moyen') bg-amber-100 text-amber-700
                        @else bg-rose-100 text-rose-700 @endif">
                        Risque {{ ucfirst($dashboard[$devise]['niveau_risque'] ?? 'faible') }} ({{ $dashboard[$devise]['taux_risque'] ?? 0 }}%)
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-slate-50 p-4 rounded-xl">
                        <p class="text-xs text-slate-500 font-medium mb-1">Dossiers Actifs</p>
                        <p class="text-2xl font-bold text-slate-800">{{ $dashboard[$devise]['nb_credits'] }}</p>
                    </div>
                    <div class="bg-rose-50 p-4 rounded-xl ring-1 ring-rose-100">
                        <p class="text-xs text-rose-600 font-medium mb-1">Dossiers en Souffrance</p>
                        <p class="text-2xl font-bold text-rose-700">{{ $dashboard[$devise]['nb_retard'] }}</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-slate-100">
                        <span class="text-sm text-slate-500">Volume engagé (Cap + Int)</span>
                        <span class="text-sm font-semibold text-slate-800">{{ number_format($dashboard[$devise]['total_octroye'] ?? ($dashboard[$devise]['encours'] ?? 0), 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-100">
                        <span class="text-sm text-slate-500">Montants encaissés</span>
                        <span class="text-sm font-semibold text-emerald-600">{{ number_format($dashboard[$devise]['rembourse'], 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-100">
                        <span class="text-sm text-slate-500">Reste à percevoir</span>
                        <span class="text-sm font-semibold text-slate-800">{{ number_format($dashboard[$devise]['reste_a_recouvrer'] ?? (($dashboard[$devise]['encours'] ?? 0) - $dashboard[$devise]['rembourse']), 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-rose-500 font-medium">Créances en retard</span>
                        <span class="text-sm font-bold text-rose-600">{{ number_format($dashboard[$devise]['montant_retard'], 2) }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Synthèse Globale Consolidée --}}
    <div class="bg-indigo-900 rounded-2xl p-6 shadow-md text-indigo-50 flex flex-col md:flex-row justify-between items-center gap-6">
        <div>
            <h2 class="text-lg font-semibold text-indigo-200 mb-1">Exposition Globale du Portefeuille</h2>
            <p class="text-3xl font-bold text-white">{{ number_format($dashboard['global']['exposition'], 2) }} <span class="text-lg font-medium text-indigo-300">Total</span></p>
        </div>
        <div class="flex gap-6 w-full md:w-auto">
            <div class="bg-indigo-800/50 p-4 rounded-xl flex-1 text-center border border-indigo-700/50">
                <p class="text-xs text-indigo-300 uppercase tracking-wider mb-1">Remboursé</p>
                <p class="text-xl font-bold text-emerald-400">{{ number_format($dashboard['global']['rembourse'] ?? 0, 2) }}</p>
            </div>
            <div class="bg-indigo-800/50 p-4 rounded-xl flex-1 text-center border border-indigo-700/50">
                <p class="text-xs text-indigo-300 uppercase tracking-wider mb-1">Reste Net</p>
                <p class="text-xl font-bold text-white">{{ number_format($dashboard['global']['reste_a_recouvrer'] ?? 0, 2) }}</p>
            </div>
        </div>
    </div>

    {{-- Liste des Crédits Actifs --}}
    <div class="bg-white shadow-sm ring-1 ring-slate-200/60 rounded-2xl overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center">
            <h3 class="font-bold text-lg text-slate-800">Registre des Crédits Actifs</h3>
            <span class="bg-slate-100 text-slate-600 py-1 px-3 rounded-full text-xs font-bold">{{ $credits_list->count() }} dossiers</span>
        </div>

        @if($credits_list->isEmpty())
            <div class="p-8 text-center text-slate-500">
                <svg class="mx-auto h-12 w-12 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p>Aucun crédit actif enregistré pour cette zone.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left whitespace-nowrap">
                    <thead class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-xs font-semibold">
                        <tr>
                            <th scope="col" class="px-6 py-4">N° Dossier</th>
                            <th scope="col" class="px-6 py-4">Membre</th>
                            <th scope="col" class="px-6 py-4 text-right">Capital</th>
                            <th scope="col" class="px-6 py-4 text-right">Remboursé</th>
                            <th scope="col" class="px-6 py-4 text-right">Reste dû</th>
                            <th scope="col" class="px-6 py-4 text-center">Statut</th>
                            <th scope="col" class="px-6 py-4 text-right">Retard (Jrs)</th>
                            <th scope="col" class="px-6 py-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($credits_list as $credit)
                            {{-- Ligne cliquable avec pointeur et micro-interaction --}}
                            <tr 
                                onclick="window.location.href='{{ route('credit.show', $credit->uuid) }}'" 
                                class="hover:bg-slate-50 transition-colors duration-150 cursor-pointer group"
                            >
                                <td class="px-6 py-4 font-medium text-slate-900">
                                    {{ $credit->numero_credit }}
                                    <span class="ml-2 text-xs text-slate-400 font-normal">{{ $credit->monnaie }}</span>
                                </td>
                                <td class="px-6 py-4 text-slate-600">{{ $credit->membre?->nom }}</td>
                                <td class="px-6 py-4 text-right font-medium text-slate-700">{{ number_format($credit->capital, 2) }}</td>
                                <td class="px-6 py-4 text-right text-emerald-600 font-medium">{{ number_format($credit->total_rembourse, 2) }}</td>
                                <td class="px-6 py-4 text-right font-bold text-slate-800">{{ number_format($credit->reste_du, 2) }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border
                                        @switch($credit->statut)
                                            @case('en_cours') bg-blue-50 text-blue-700 border-blue-200 @break
                                            @case('en_retard') bg-rose-50 text-rose-700 border-rose-200 shadow-sm shadow-rose-100 @break
                                            @default bg-slate-100 text-slate-700 border-slate-200
                                        @endswitch">
                                        @if($credit->statut === 'en_cours') En cours
                                        @elseif($credit->statut === 'en_retard') En souffrance
                                        @else {{ ucfirst(str_replace('_', ' ', $credit->statut)) }}
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($credit->jours_retard > 0)
                                        <span class="text-rose-600 font-bold">{{ $credit->jours_retard }} jrs</span>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right text-slate-400 group-hover:text-blue-600 transition-colors">
                                    <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>