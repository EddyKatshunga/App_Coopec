<div class="p-4 sm:p-6 lg:p-8 bg-gray-50 min-h-screen">
    {{-- Header --}}
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
        
        {{-- Card 1: Capital Octroyé --}}
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-xs font-black uppercase text-gray-400 mb-3 tracking-wider">Capital Octroyé</p>
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-lg font-bold text-gray-900">{{ number_format($stats['USD']['capital'], 0, ',', ' ') }}</span>
                    <span class="text-[10px] font-bold bg-green-100 text-green-700 px-2 py-0.5 rounded">USD</span>
                </div>
                <div class="flex justify-between items-center border-t border-gray-50 pt-2">
                    <span class="text-lg font-bold text-gray-900">{{ number_format($stats['CDF']['capital'], 0, ',', ' ') }}</span>
                    <span class="text-[10px] font-bold bg-blue-100 text-blue-700 px-2 py-0.5 rounded">CDF</span>
                </div>
            </div>
        </div>

        {{-- Card 2: Encours (Principal + Intérêts) --}}
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-xs font-black uppercase text-gray-400 mb-3 tracking-wider">Encours Actuel</p>
            <div class="space-y-2">
                <div class="flex justify-between items-center text-indigo-600">
                    <span class="text-lg font-bold">{{ number_format($stats['USD']['encours'], 0, ',', ' ') }}</span>
                    <span class="text-[10px] font-bold border border-indigo-200 px-2 py-0.5 rounded">USD</span>
                </div>
                <div class="flex justify-between items-center text-indigo-600 border-t border-gray-50 pt-2">
                    <span class="text-lg font-bold">{{ number_format($stats['CDF']['encours'], 0, ',', ' ') }}</span>
                    <span class="text-[10px] font-bold border border-indigo-200 px-2 py-0.5 rounded">CDF</span>
                </div>
            </div>
        </div>

        {{-- Card 3: Performance (Taux moyen combiné ou par devise) --}}
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-xs font-black uppercase text-gray-400 mb-3 tracking-wider">Recouvrement</p>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase">USD</p>
                    <p class="text-xl font-black text-gray-900">{{ round($stats['USD']['taux_recouvrement'], 1) }}%</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase">CDF</p>
                    <p class="text-xl font-black text-gray-900">{{ round($stats['CDF']['taux_recouvrement'], 1) }}%</p>
                </div>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-1.5 mt-3 overflow-hidden flex">
                <div class="bg-green-500 h-full shadow-sm" style="width: {{ $stats['USD']['taux_recouvrement'] }}%"></div>
            </div>
        </div>

        {{-- Card 4: Risque --}}
        <div class="bg-red-50 p-5 rounded-2xl shadow-sm border border-red-100">
            <p class="text-xs font-black uppercase text-red-400 mb-3 tracking-wider">Portefeuille à Risque</p>
            <div class="flex items-center justify-between mb-2">
                <span class="text-2xl font-black text-red-700">{{ $stats['USD']['nb_retards'] + $stats['CDF']['nb_retards'] }}</span>
                <span class="text-[10px] font-bold text-red-500 uppercase">Dossiers</span>
            </div>
            <div class="text-[10px] text-red-600 font-bold space-y-1">
                <p>Pén. USD : {{ number_format($stats['USD']['penalites'], 2) }}</p>
                <p>Pén. CDF : {{ number_format($stats['CDF']['penalites'], 0) }}</p>
            </div>
        </div>
    </div>

    {{-- Liste des crédits --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-white">
            <h3 class="text-lg font-bold text-gray-900">Engagements de la zone</h3>
            <div class="flex gap-2">
                <span class="px-3 py-1 bg-gray-100 rounded-lg text-xs font-bold text-gray-600">Total : {{ $credits_list->count() }}</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 text-[10px] uppercase font-black text-gray-400 tracking-widest">
                        <th class="px-6 py-4">Membre & N°</th>
                        <th class="px-6 py-4">Statut</th>
                        <th class="px-6 py-4 text-right">Capital</th>
                        <th class="px-6 py-4 text-right text-indigo-600">Reste à payer</th>
                        <th class="px-6 py-4 text-center">Retard</th>
                        <th class="px-6 py-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($credits_list as $credit)
                        <tr class="hover:bg-gray-50 transition-colors group">
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-gray-900 leading-none mb-1">{{ $credit->membre->nom }}</p>
                                <p class="text-[10px] font-mono text-gray-400 uppercase">{{ $credit->numero_credit }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded text-[10px] font-black uppercase {{ 
                                    $credit->statut == 'en_retard' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600' 
                                }}">
                                    {{ str_replace('_', ' ', $credit->statut) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-bold text-gray-900">{{ number_format($credit->capital, 0) }}</span>
                                <span class="text-[9px] font-black text-gray-400 ml-1">{{ $credit->monnaie }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <p class="text-sm font-black text-indigo-600">{{ number_format($credit->reste_du, 0) }} <span class="text-[9px]">{{ $credit->monnaie }}</span></p>
                                @if($credit->penalites_courantes > 0)
                                    <p class="text-[9px] text-red-500 font-bold">+{{ number_format($credit->penalites_courantes, 0) }} pen.</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($credit->jours_retard > 0)
                                    <span class="inline-block px-2 py-1 bg-red-50 text-red-600 rounded-md text-xs font-black">{{ $credit->jours_retard }} j</span>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('credit.show', $credit) }}" class="text-gray-400 hover:text-indigo-600 transition-colors">
                                    <x-heroicon-o-arrow-right-circle class="w-6 h-6 inline" />
                                </a>
                            </td>
                        </tr>
                    @empty
                        {{-- ... (vide) --}}
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>