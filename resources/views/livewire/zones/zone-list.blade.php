<div class="space-y-6 p-4 sm:p-6 lg:p-8 bg-gray-50 min-h-screen">
    {{-- En‑tête --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 flex items-center">
                <x-heroicon-s-globe-alt class="w-8 h-8 text-indigo-600 mr-3" />
                Tableau de bord – Portefeuille Actif
            </h1>
            <p class="text-sm text-gray-500 mt-1">Supervision par zone · Crédits en cours et en retard</p>
        </div>
        <div>
            <a href="{{ route('impressions.zones.index', ['agence_id' => $selectedAgenceId]) }}" 
               target="_blank" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-xl text-sm font-bold text-gray-700 hover:bg-gray-50 transition shadow-sm">
                <x-heroicon-o-printer class="w-5 h-5 text-gray-500" /> 
                Imprimer le rapport
            </a>
        </div>
    </div>

    {{-- Filtre Agence (si super‑utilisateur) --}}
    @can('can.level6')
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
        <div class="flex items-center gap-3">
            <label class="text-xs font-black text-gray-400 uppercase tracking-wider">Filtrer par Agence</label>
            <select wire:model.live="selectedAgenceId" class="w-64 rounded-xl border-gray-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Toutes les agences</option>
                @foreach($agences as $agence)
                    <option value="{{ $agence->id }}">{{ $agence->nom }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1"></div>
        <button wire:click="$refresh" class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition" title="Actualiser les données">
            <x-heroicon-o-arrow-path class="w-5 h-5" />
        </button>
    </div>
    @endcan

    {{-- Cartes de synthèse globale --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Capital actif total --}}
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-xs font-black uppercase text-gray-400 mb-3 tracking-wider">Capital actif</p>
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-lg font-bold text-gray-900">{{ number_format($statsGlobales['capital']['USD'], 0, ',', ' ') }}</span>
                    <span class="text-[10px] font-bold bg-green-100 text-green-700 px-2 py-0.5 rounded">USD</span>
                </div>
                <div class="flex justify-between items-center border-t border-gray-50 pt-2">
                    <span class="text-lg font-bold text-gray-900">{{ number_format($statsGlobales['capital']['CDF'], 0, ',', ' ') }}</span>
                    <span class="text-[10px] font-bold bg-blue-100 text-blue-700 px-2 py-0.5 rounded">CDF</span>
                </div>
            </div>
            <p class="text-[10px] text-gray-400 mt-3 font-medium italic">Somme des capitaux non soldés</p>
        </div>

        {{-- Exposition totale --}}
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-xs font-black uppercase text-gray-400 mb-3 tracking-wider">Exposition actuelle</p>
            <div class="space-y-2">
                <div class="flex justify-between items-center text-indigo-600">
                    <span class="text-lg font-bold">{{ number_format($statsGlobales['exposition']['USD'], 0, ',', ' ') }}</span>
                    <span class="text-[10px] font-bold border border-indigo-200 px-2 py-0.5 rounded">USD</span>
                </div>
                <div class="flex justify-between items-center text-indigo-600 border-t border-gray-50 pt-2">
                    <span class="text-lg font-bold">{{ number_format($statsGlobales['exposition']['CDF'], 0, ',', ' ') }}</span>
                    <span class="text-[10px] font-bold border border-indigo-200 px-2 py-0.5 rounded">CDF</span>
                </div>
            </div>
            <p class="text-[10px] text-gray-400 mt-3 font-medium italic">Reste à recouvrer (Cap + Int)</p>
        </div>

        {{-- Nombre de crédits actifs --}}
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-xs font-black uppercase text-gray-400 mb-3 tracking-wider">Portefeuille actif</p>
            <div class="flex items-end justify-between">
                <span class="text-3xl font-black text-gray-900 leading-none">{{ $statsGlobales['credits_actifs'] }}</span>
                <span class="text-xs font-bold text-gray-500 uppercase pb-1">Dossiers</span>
            </div>
            <div class="w-full bg-gray-100 h-1.5 rounded-full mt-4">
                <div class="bg-indigo-500 h-1.5 rounded-full" style="width: 100%"></div>
            </div>
        </div>

        {{-- Crédits en retard --}}
        <div class="bg-red-50 p-5 rounded-2xl shadow-sm border border-red-100">
            <p class="text-xs font-black uppercase text-red-400 mb-3 tracking-wider">Crédits en retard</p>
            <div class="flex items-end justify-between">
                <span class="text-3xl font-black text-red-700 leading-none">{{ $statsGlobales['credits_retard'] }}</span>
                <span class="text-xs font-bold text-red-500 uppercase pb-1">Dossiers</span>
            </div>
            <p class="text-[10px] text-red-400 mt-4 font-bold flex items-center gap-1">
                <x-heroicon-s-exclamation-triangle class="w-3 h-3" />
                Date d'échéance dépassée
            </p>
        </div>
    </div>

    {{-- Tableau des zones --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="p-4 text-xs font-black text-gray-400 uppercase tracking-widest">Zone & Gérant</th>
                        <th class="p-4 text-xs font-black text-blue-600 uppercase tracking-widest text-center border-l border-gray-100">Capital actif</th>
                        <th class="p-4 text-xs font-black text-indigo-600 uppercase tracking-widest text-center border-l border-gray-100">Exposition</th>
                        <th class="p-4 text-xs font-black text-amber-600 uppercase tracking-widest text-center border-l border-gray-100">Taux de risque</th>
                        <th class="p-4 text-xs font-black text-red-600 uppercase tracking-widest text-center border-l border-gray-100">Retards</th>
                        <th class="p-4 text-xs font-black text-gray-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($zones as $zone)
                        <tr class="hover:bg-indigo-50/30 transition-colors group">
                            <td class="p-4">
                                <div class="font-bold text-gray-900 group-hover:text-indigo-700 transition-colors">{{ $zone->nom }}</div>
                                <div class="text-xs text-gray-500 flex items-center mt-1">
                                    <x-heroicon-s-user class="w-3 h-3 mr-1 text-gray-400" />
                                    {{ $zone->gerant->nom ?? 'Non assigné' }}
                                </div>
                                <div class="text-[10px] text-gray-400 mt-0.5">Code: {{ $zone->code }}</div>
                            </td>

                            {{-- Capital actif (via accesseurs) --}}
                            <td class="p-4 border-l border-gray-50 bg-blue-50/5">
                                <div class="flex flex-col items-center">
                                    <span class="text-sm font-black text-gray-800">{{ number_format($zone->capital_actif_usd, 0, ',', ' ') }} $</span>
                                    <span class="text-[11px] text-blue-500 font-bold">{{ number_format($zone->capital_actif_cdf, 0, ',', ' ') }} FC</span>
                                </div>
                            </td>

                            {{-- Exposition --}}
                            <td class="p-4 border-l border-gray-50 bg-indigo-50/5">
                                <div class="flex flex-col items-center">
                                    <span class="text-sm font-black text-indigo-700">{{ number_format($zone->exposition_usd, 0, ',', ' ') }} $</span>
                                    <span class="text-[11px] text-indigo-500 font-bold">{{ number_format($zone->exposition_cdf, 0, ',', ' ') }} FC</span>
                                </div>
                            </td>

                            {{-- Taux de risque (accesseurs) --}}
                            <td class="p-4 border-l border-gray-50 text-center">
                                <div class="flex flex-col items-center space-y-1">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[10px] text-gray-400 font-bold">USD</span>
                                        <span class="text-sm font-black @if($zone->taux_risque_usd > 25) text-red-600 @elseif($zone->taux_risque_usd > 10) text-amber-600 @else text-green-600 @endif">
                                            {{ number_format($zone->taux_risque_usd, 1) }}%
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[10px] text-gray-400 font-bold">CDF</span>
                                        <span class="text-sm font-black @if($zone->taux_risque_cdf > 25) text-red-600 @elseif($zone->taux_risque_cdf > 10) text-amber-600 @else text-green-600 @endif">
                                            {{ number_format($zone->taux_risque_cdf, 1) }}%
                                        </span>
                                    </div>
                                </div>
                            </td>

                            {{-- Retards --}}
                            <td class="p-4 border-l border-gray-50 text-center">
                                @php $totalRetards = $zone->credits_retard_actifs_usd + $zone->credits_retard_actifs_cdf; @endphp
                                @if($totalRetards > 0)
                                    <span class="inline-block px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-black shadow-sm">
                                        {{ $totalRetards }}
                                    </span>
                                    <div class="text-[9px] text-red-400 mt-1.5 font-bold uppercase tracking-tighter">
                                        $ : {{ $zone->credits_retard_actifs_usd }} | FC : {{ $zone->credits_retard_actifs_cdf }}
                                    </div>
                                @else
                                    <div class="flex flex-col items-center opacity-20">
                                        <x-heroicon-o-check-circle class="w-5 h-5 text-green-500" />
                                        <span class="text-[10px] font-bold">A JOUR</span>
                                    </div>
                                @endif
                            </td>

                            <td class="p-4 text-right">
                                <a href="{{ route('agences.zones.show', $zone->uuid) }}"
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold hover:bg-indigo-700 transition-all shadow-md hover:shadow-lg active:scale-95">
                                    <x-heroicon-s-chart-bar class="w-4 h-4" />
                                    Détails
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-20 text-center">
                                <div class="flex flex-col items-center">
                                    <x-heroicon-o-folder-open class="w-12 h-12 text-gray-200 mb-3" />
                                    <p class="text-gray-400 font-medium italic">Aucune zone n'a été trouvée pour les critères sélectionnés.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $zones->links() }}
    </div>
</div>