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
            <a href="{{ route('impressions.zones.index', ['agence_id' => $selectedAgenceId]) }}" target="_blank" class="...">
                <x-heroicon-o-printer class="w-5 h-5" /> Imprimer
            </a>
        </div>
    </div>

    {{-- Filtre Agence (si super‑utilisateur) --}}
    @can('can.level6')
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
        <label class="text-xs font-black text-gray-400 uppercase tracking-wider">Agence</label>
        <select wire:model.live="selectedAgenceId" class="w-64 rounded-xl border-gray-200 text-sm focus:ring-indigo-500">
            <option value="">Toutes les agences</option>
            @foreach($agences as $agence)
                <option value="{{ $agence->id }}">{{ $agence->nom }}</option>
            @endforeach
        </select>
        <div class="flex-1"></div>
        <button wire:click="$refresh" class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition">
            <x-heroicon-o-arrow-path class="w-5 h-5" />
        </button>
    </div>
    @endcan

    {{-- Cartes de synthèse globale (instantané actif) --}}
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
            <p class="text-[10px] text-gray-400 mt-3 font-medium">Crédits en cours & en retard</p>
        </div>

        {{-- Exposition totale (capital + intérêts) --}}
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
            <p class="text-[10px] text-gray-400 mt-3 font-medium">Capital + intérêts à recouvrer</p>
        </div>

        {{-- Nombre de crédits actifs --}}
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-xs font-black uppercase text-gray-400 mb-3 tracking-wider">Crédits actifs</p>
            <div class="flex items-center justify-between">
                <span class="text-2xl font-black text-gray-900">{{ $statsGlobales['credits_actifs'] }}</span>
                <span class="text-[10px] font-bold text-gray-500 uppercase">Dossiers</span>
            </div>
            <p class="text-[10px] text-gray-400 mt-3 font-medium">En cours ou en retard</p>
        </div>

        {{-- Crédits en retard --}}
        <div class="bg-red-50 p-5 rounded-2xl shadow-sm border border-red-100">
            <p class="text-xs font-black uppercase text-red-400 mb-3 tracking-wider">Crédits en retard</p>
            <div class="flex items-center justify-between">
                <span class="text-2xl font-black text-red-700">{{ $statsGlobales['credits_retard'] }}</span>
                <span class="text-[10px] font-bold text-red-500 uppercase">Dossiers</span>
            </div>
            <p class="text-[10px] text-red-400 mt-3 font-medium">Dépassement date d'échéance</p>
        </div>
    </div>

    {{-- Tableau des zones (indicateurs actifs) --}}
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
                        @php
                            $capitalCdf = $zone->capital_actif_cdf;
                            $capitalUsd = $zone->capital_actif_usd;
                            $expoCdf    = $zone->exposition_cdf;
                            $expoUsd    = $zone->exposition_usd;
                            $risqueCdf  = $zone->taux_risque_cdf;
                            $risqueUsd  = $zone->taux_risque_usd;
                            $retardCdf  = $zone->credits_retard_actifs_cdf;
                            $retardUsd  = $zone->credits_retard_actifs_usd;
                        @endphp
                        <tr class="hover:bg-indigo-50/30 transition-colors group">
                            <td class="p-4">
                                <div class="font-bold text-gray-900">{{ $zone->nom }}</div>
                                <div class="text-xs text-gray-500 flex items-center mt-1">
                                    <x-heroicon-s-user class="w-3 h-3 mr-1" />
                                    {{ $zone->gerant->nom ?? 'Non assigné' }}
                                </div>
                                <div class="text-[10px] text-gray-400 mt-0.5">Code: {{ $zone->code }}</div>
                            </td>

                            {{-- Capital actif --}}
                            <td class="p-4 border-l border-gray-50 bg-blue-50/10">
                                <div class="flex flex-col items-center">
                                    <span class="text-sm font-black text-gray-800">{{ number_format($capitalUsd, 0) }} $</span>
                                    <span class="text-xs text-blue-500 font-bold">{{ number_format($capitalCdf, 0) }} FC</span>
                                </div>
                            </td>

                            {{-- Exposition --}}
                            <td class="p-4 border-l border-gray-50 bg-indigo-50/10">
                                <div class="flex flex-col items-center">
                                    <span class="text-sm font-black text-indigo-700">{{ number_format($expoUsd, 0) }} $</span>
                                    <span class="text-xs text-indigo-500 font-bold">{{ number_format($expoCdf, 0) }} FC</span>
                                </div>
                            </td>

                            {{-- Taux de risque --}}
                            <td class="p-4 border-l border-gray-50 text-center">
                                <div class="flex flex-col items-center space-y-1">
                                    <div class="flex items-center gap-1">
                                        <span class="text-sm font-bold @if($risqueUsd > 50) text-red-600 @elseif($risqueUsd > 20) text-amber-600 @else text-green-600 @endif">
                                            {{ $risqueUsd }}%
                                        </span>
                                        <span class="text-[9px] text-gray-400">USD</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <span class="text-sm font-bold @if($risqueCdf > 50) text-red-600 @elseif($risqueCdf > 20) text-amber-600 @else text-green-600 @endif">
                                            {{ $risqueCdf }}%
                                        </span>
                                        <span class="text-[9px] text-gray-400">CDF</span>
                                    </div>
                                </div>
                            </td>

                            {{-- Retards --}}
                            <td class="p-4 border-l border-gray-50 text-center">
                                @if($retardUsd + $retardCdf > 0)
                                    <span class="inline-block px-2 py-1 bg-red-50 text-red-600 rounded-md text-xs font-black">
                                        {{ $retardUsd + $retardCdf }}
                                    </span>
                                    <div class="text-[9px] text-gray-400 mt-1">
                                        USD: {{ $retardUsd }} | CDF: {{ $retardCdf }}
                                    </div>
                                @else
                                    <span class="text-gray-300 text-xs">—</span>
                                @endif
                            </td>

                            <td class="p-4 text-right">
                                <a href="{{ route('agences.zones.show', $zone->uuid) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-lg text-xs font-bold hover:bg-indigo-100 transition-colors">
                                    <x-heroicon-o-chart-bar class="w-4 h-4" />
                                    Détails
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-12 text-center text-gray-400 italic">
                                Aucune zone trouvée pour cette agence.
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