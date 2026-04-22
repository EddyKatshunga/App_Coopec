<div class="p-4 sm:p-6 lg:p-8 bg-gray-50 min-h-screen">
    {{-- En-tête --}}
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
        <div class="flex items-center bg-white p-3 rounded-xl shadow-sm border border-gray-100">
            <a href="{{ route('impressions.zones.show', ['zone' => $zone->uuid]) }}" target="_blank" class="...">
                <x-heroicon-o-printer class="w-5 h-5" /> Imprimer
            </a>
        </div>
    </div>

    {{-- Grille KPIs : Focus sur le Portefeuille Actif --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        {{-- Card 1: Capital Actif --}}
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-xs font-black uppercase text-gray-400 mb-3 tracking-wider">Capital Actif</p>
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-lg font-bold text-gray-900">{{ number_format($dashboard['USD']['capital'], 0, ',', ' ') }}</span>
                    <span class="text-[10px] font-bold bg-green-100 text-green-700 px-2 py-0.5 rounded">USD</span>
                </div>
                <div class="flex justify-between items-center border-t border-gray-50 pt-2">
                    <span class="text-lg font-bold text-gray-900">{{ number_format($dashboard['CDF']['capital'], 0, ',', ' ') }}</span>
                    <span class="text-[10px] font-bold bg-blue-100 text-blue-700 px-2 py-0.5 rounded">CDF</span>
                </div>
            </div>
            <p class="text-[10px] text-gray-400 mt-3 font-medium">Crédits en cours & en retard</p>
        </div>

        {{-- Card 2: Exposition (Encours Actif Total) --}}
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-xs font-black uppercase text-gray-400 mb-3 tracking-wider">Exposition Actuelle</p>
            <div class="space-y-2">
                <div class="flex justify-between items-center text-indigo-600">
                    <span class="text-lg font-bold">{{ number_format($dashboard['USD']['exposition'], 0, ',', ' ') }}</span>
                    <span class="text-[10px] font-bold border border-indigo-200 px-2 py-0.5 rounded">USD</span>
                </div>
                <div class="flex justify-between items-center text-indigo-600 border-t border-gray-50 pt-2">
                    <span class="text-lg font-bold">{{ number_format($dashboard['CDF']['exposition'], 0, ',', ' ') }}</span>
                    <span class="text-[10px] font-bold border border-indigo-200 px-2 py-0.5 rounded">CDF</span>
                </div>
            </div>
            <p class="text-[10px] text-gray-400 mt-3 font-medium">Capital + Intérêts à recouvrer</p>
        </div>

        {{-- Card 3: Taux de Risque (Crédits en Retard / Total Actif) --}}
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-xs font-black uppercase text-gray-400 mb-3 tracking-wider">Taux de Risque</p>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase">USD</p>
                    <p class="text-xl font-black text-gray-900">{{ $dashboard['USD']['taux_risque'] }}%</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase">CDF</p>
                    <p class="text-xl font-black text-gray-900">{{ $dashboard['CDF']['taux_risque'] }}%</p>
                </div>
            </div>
            {{-- Jauge de risque pour USD (exemple) --}}
            <div class="w-full bg-gray-100 rounded-full h-1.5 mt-3 overflow-hidden">
                <div class="h-full shadow-sm transition-all @if($dashboard['USD']['taux_risque'] > 50) bg-red-500 @elseif($dashboard['USD']['taux_risque'] > 20) bg-yellow-500 @else bg-green-500 @endif" 
                     style="width: {{ $dashboard['USD']['taux_risque'] }}%">
                </div>
            </div>
            <p class="text-[10px] text-gray-400 mt-3 font-medium">% de dossiers actifs en retard</p>
        </div>

        {{-- Card 4: Niveau de Risque & Retards --}}
        <div class="bg-red-50 p-5 rounded-2xl shadow-sm border border-red-100">
            <p class="text-xs font-black uppercase text-red-400 mb-3 tracking-wider">Portefeuille à Risque</p>
            <div class="flex items-center justify-between mb-2">
                <span class="text-2xl font-black text-red-700">
                    {{ $dashboard['USD']['credits_retard'] + $dashboard['CDF']['credits_retard'] }}
                </span>
                <span class="text-[10px] font-bold text-red-500 uppercase">Dossiers en retard</span>
            </div>
            <div class="text-[11px] font-bold space-y-1.5 mt-2">
                <div class="flex justify-between">
                    <span>Risque USD :</span>
                    <span class="uppercase @if($dashboard['USD']['niveau_risque'] == 'élevé') text-red-600 @elseif($dashboard['USD']['niveau_risque'] == 'moyen') text-yellow-600 @else text-green-600 @endif">
                        {{ $dashboard['USD']['niveau_risque'] }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span>Risque CDF :</span>
                    <span class="uppercase @if($dashboard['CDF']['niveau_risque'] == 'élevé') text-red-600 @elseif($dashboard['CDF']['niveau_risque'] == 'moyen') text-yellow-600 @else text-green-600 @endif">
                        {{ $dashboard['CDF']['niveau_risque'] }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Synthèse Globale --}}
    <div class="mb-6 flex flex-wrap items-center justify-between bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="px-4 py-2 bg-indigo-50 rounded-lg">
                <span class="text-xs font-black text-indigo-600 uppercase">Crédits Actifs</span>
                <p class="text-2xl font-bold text-gray-900">{{ $dashboard['global']['credits_actifs'] }}</p>
            </div>
            <div class="px-4 py-2 bg-gray-50 rounded-lg">
                <span class="text-xs font-black text-gray-500 uppercase">Exposition Totale</span>
                <p class="text-lg font-bold text-gray-800">
                    {{ number_format($dashboard['global']['total_exposition'], 0, ',', ' ') }} 
                    <span class="text-xs font-normal text-gray-500">(CDF + USD)</span>
                </p>
            </div>
        </div>
        <div class="text-xs text-gray-400">
            Dernière activité : {{ $zone->derniere_activite_at ? $zone->derniere_activite_at->diffForHumans() : 'Jamais' }}
        </div>
    </div>

    {{-- Liste des Crédits Actifs (En cours / En retard) --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-white">
            <h3 class="text-lg font-bold text-gray-900">Crédits Actifs de la zone</h3>
            <div class="flex gap-2">
                <span class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-xs font-bold">
                    {{ $credits_list->count() }} dossier(s)
                </span>
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
                                <span @class([
                                    'px-2 py-1 rounded text-[10px] font-black uppercase',
                                    'bg-red-100 text-red-700' => $credit->statut === 'en_retard',
                                    'bg-amber-100 text-amber-700' => $credit->statut === 'en_cours',
                                ])>
                                    {{ str_replace('_', ' ', $credit->statut) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-bold text-gray-900">{{ number_format($credit->capital, 0) }}</span>
                                <span class="text-[9px] font-black text-gray-400 ml-1">{{ $credit->monnaie }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <p class="text-sm font-black text-indigo-600">
                                    {{ number_format($credit->reste_du, 0) }} 
                                    <span class="text-[9px]">{{ $credit->monnaie }}</span>
                                </p>
                                @if($credit->penalites_courantes > 0)
                                    <p class="text-[9px] text-red-500 font-bold">
                                        +{{ number_format($credit->penalites_courantes, 0) }} pen.
                                    </p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($credit->jours_retard > 0)
                                    <span class="inline-block px-2 py-1 bg-red-50 text-red-600 rounded-md text-xs font-black">
                                        {{ $credit->jours_retard }} j
                                    </span>
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
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                Aucun crédit actif (en cours ou en retard) dans cette zone.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>