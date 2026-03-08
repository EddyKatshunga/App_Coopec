<div class="p-4 sm:p-6 lg:p-8 bg-gray-50 min-h-screen">
    {{-- Header avec Fil d'ariane --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2">
                <a href="{{ url()->previous() }}" class="hover:text-indigo-600" wire:navigate>Retour</a>
                <span class="mx-2">/</span>
                <span class="text-gray-900 font-medium">Détails Zone</span>
            </nav>
            <h1 class="text-3xl font-extrabold text-gray-900 flex items-center">
                <x-heroicon-s-map-pin class="w-8 h-8 text-indigo-600 mr-3" />
                {{ $zone->nom }} 
                <span class="ml-4 text-sm font-mono bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full uppercase">
                    {{ $zone->code }}
                </span>
            </h1>
        </div>

        <div class="flex items-center bg-white p-3 rounded-xl shadow-sm border border-gray-100">
            <div class="mr-4 text-right">
                <p class="text-xs text-gray-500 uppercase font-semibold">Gérant de zone</p>
                <p class="text-sm font-bold text-gray-900">{{ $zone->gerant->nom ?? 'Non assigné' }}</p>
            </div>
            <div class="h-10 w-10 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold">
                {{ substr($zone->gerant->nom ?? 'Z', 0, 1) }}
            </div>
        </div>
    </div>

    {{-- Grille de Statistiques (KPIs) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- Card 1: Portefeuille --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-sm font-medium text-gray-500 mb-1">Capital Octroyé</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_capital'], 0, ',', ' ') }} <span class="text-xs text-gray-400">USD/CDF</span></p>
                <div class="mt-2 flex items-center text-xs text-green-600 font-semibold">
                    <x-heroicon-m-arrow-trending-up class="w-4 h-4 mr-1" />
                    +{{ $zone->credits->count() }} Crédits au total
                </div>
            </div>
            <x-heroicon-o-banknotes class="absolute -right-2 -bottom-2 w-24 h-24 text-gray-50 opacity-50" />
        </div>

        {{-- Card 2: Encours --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500 mb-1">Encours Actuel</p>
            <p class="text-2xl font-bold text-indigo-600">{{ number_format($stats['total_encours'], 0, ',', ' ') }}</p>
            <p class="text-xs text-gray-400 mt-2">Principal + Intérêts restants</p>
        </div>

        {{-- Card 3: Performance --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500 mb-1">Taux Recouvrement</p>
            <div class="flex items-end justify-between">
                <p class="text-2xl font-bold text-gray-900">{{ round($stats['taux_recouvrement'], 1) }}%</p>
                <span class="text-xs font-bold {{ $stats['taux_recouvrement'] > 80 ? 'text-green-500' : 'text-orange-500' }}">
                    Objectif 95%
                </span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2 mt-3">
                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $stats['taux_recouvrement'] }}%"></div>
            </div>
        </div>

        {{-- Card 4: Risque --}}
        <div class="bg-red-50 p-6 rounded-2xl shadow-sm border border-red-100">
            <p class="text-sm font-medium text-red-600 mb-1">Portefeuille à Risque</p>
            <p class="text-2xl font-bold text-red-700">{{ $stats['nb_retards'] }} Dossiers</p>
            <p class="text-xs text-red-500 mt-2 font-medium">Pénalités : {{ number_format($stats['total_penalites'], 2) }} USD</p>
        </div>
    </div>

    {{-- Liste des crédits de la zone --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-900 text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600">
                Détails des Engagements
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50 text-xs uppercase font-bold text-gray-500">
                        <th class="px-6 py-4">Membre & N° Crédit</th>
                        <th class="px-6 py-4">Statut</th>
                        <th class="px-6 py-4 text-right">Capital</th>
                        <th class="px-6 py-4 text-right">Reste à payer</th>
                        <th class="px-6 py-4 text-center">Retard</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($credits_list as $credit)
                        <tr class="hover:bg-indigo-50/30 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded bg-gray-100 flex items-center justify-center mr-3 group-hover:bg-white transition-colors">
                                        <x-heroicon-o-user class="w-4 h-4 text-gray-400" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900 leading-none mb-1">{{ $credit->membre->nom }} {{ $credit->membre->prenom }}</p>
                                        <p class="text-xs font-mono text-gray-400">{{ $credit->numero_credit }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statutClasses = [
                                        'en_cours' => 'bg-blue-100 text-blue-700',
                                        'en_retard' => 'bg-red-100 text-red-700 animate-pulse',
                                        'termine' => 'bg-green-100 text-green-700',
                                        'termine_en_retard' => 'bg-orange-100 text-orange-700',
                                    ][$credit->statut] ?? 'bg-gray-100 text-gray-700';
                                @endphp
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider {{ $statutClasses }}">
                                    {{ str_replace('_', ' ', $credit->statut) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-semibold text-sm">
                                {{ number_format($credit->capital, 0) }} <span class="text-[10px] text-gray-400">{{ $credit->monnaie }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <p class="text-sm font-bold text-gray-900">{{ number_format($credit->reste_du, 0) }}</p>
                                @if($credit->penalites_courantes > 0)
                                    <p class="text-[10px] text-red-500">+{{ number_format($credit->penalites_courantes, 2) }} pen.</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($credit->jours_retard > 0)
                                    <span class="text-sm font-bold text-red-600">{{ $credit->jours_retard }} j</span>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('credit.show', $credit) }}" class="inline-flex items-center justify-center p-2 rounded-lg bg-gray-50 text-gray-400 hover:bg-indigo-600 hover:text-white transition-all shadow-sm">
                                    <x-heroicon-o-eye class="w-5 h-5" />
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <x-heroicon-o-document-magnifying-glass class="w-12 h-12 text-gray-200 mb-2" />
                                    <p class="text-gray-400 italic">Aucun crédit enregistré dans cette zone.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>