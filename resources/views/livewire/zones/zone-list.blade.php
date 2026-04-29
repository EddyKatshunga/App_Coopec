<div class="p-4 md:p-8 bg-gray-50 min-h-screen">
    {{-- Sélecteur pour SuperAdmin --}}
    @if($isSuperAdmin)
        <div class="mb-8 bg-white rounded-2xl border border-gray-100 shadow-sm p-6 transition-all hover:shadow-md">
            <div class="max-w-xs">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Sélectionner une Agence</label>
                <div class="relative">
                    <select wire:model.live="selectedAgenceId" 
                        class="block w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3 text-gray-700 focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors">
                        <option value="">Choisir une agence...</option>
                        @foreach($agences as $ag)
                            <option value="{{ $ag->id }}">{{ $ag->nom }} ({{ $ag->ville }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif

    @if($agenceActuelle)
        {{-- En-tête du Dashboard --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Situation Générale</h2>
                <p class="text-gray-500 text-sm font-medium">Agence : <span class="text-blue-600">{{ $agenceActuelle->nom }}</span></p>
            </div>
            
            <div class="flex items-center gap-3">
                <div class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-700 rounded-full text-sm font-bold border border-blue-100 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Chef : {{ $agenceActuelle->chefAgence?->nom ?? 'N/A' }}
                </div>
                
                {{-- Bouton d'impression PDF --}}
                <a href="{{ route('impressions.zones.index', ['agence_id' => $agenceActuelle->id]) }}" 
                target="_blank"
                class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-full text-sm font-bold hover:bg-gray-900 transition shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Imprimer la situation générale
                </a>
            </div>
        </div>

        {{-- Cartes de Statistiques Globales --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            @foreach(['USD' => ['color' => 'text-emerald-600', 'bg' => 'bg-emerald-50', 'icon' => '$'], 'CDF' => ['color' => 'text-blue-600', 'bg' => 'bg-blue-50', 'icon' => 'FC']] as $devise => $style)
                @php $s = $statsGlobales[$devise]; @endphp
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:translate-y-[-4px] transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-black text-gray-400 uppercase tracking-wider">{{ $devise }} - Portefeuille</span>
                        <div class="p-2 {{ $style['bg'] }} {{ $style['color'] }} rounded-lg font-bold text-xs uppercase">
                            {{ $devise }}
                        </div>
                    </div>
                    <h3 class="text-2xl font-black {{ $style['color'] }} mb-1">
                        {{ number_format($s->capital_interet_total, 2) }}
                    </h3>
                    <p class="text-gray-500 text-xs mb-4">Reste : <span class="font-bold text-gray-700">{{ number_format($s->reste_a_recouvrer, 2) }}</span></p>
                    
                    <div class="flex gap-2">
                        <span class="px-2.5 py-1 bg-gray-100 text-gray-600 rounded-md text-[10px] font-bold uppercase">{{ $s->nombre_credits_actifs }} Dossiers</span>
                        <span class="px-2.5 py-1 bg-red-100 text-red-600 rounded-md text-[10px] font-bold uppercase">{{ $s->nombre_retards }} Retards</span>
                    </div>
                </div>
            @endforeach

            <div class="bg-gray-900 rounded-2xl p-6 shadow-lg sm:col-span-2 lg:col-span-1 flex flex-col justify-center border border-gray-800">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Historique</span>
                <h3 class="text-3xl font-black text-white mb-1">{{ $statsGlobales['termine'] }}</h3>
                <p class="text-gray-400 text-xs">Dossiers clôturés avec succès</p>
            </div>
        </div>

        {{-- Tableau des Zones --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-50 flex justify-between items-center">
                <h5 class="font-bold text-gray-800">Détails par Zones</h5>
                <span class="text-xs bg-gray-100 text-gray-500 px-3 py-1 rounded-full font-medium">{{ $zones->total() }} Zones au total</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Zone / Code</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Gérant</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">Dossiers</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right">Total prêté</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right">Total récupéré</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right">Reste à recouvrer</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">Statut</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($zones as $zone)
                            <tr class="hover:bg-blue-50/30 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-800 group-hover:text-blue-600 transition-colors">{{ $zone->nom }}</div>
                                    <div class="text-[10px] font-mono text-gray-400 uppercase">{{ $zone->code }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center mr-3 text-gray-500 font-bold text-xs">
                                            {{ substr($zone->gerant?->nom ?? 'N', 0, 1) }}
                                        </div>
                                        {{ $zone->gerant?->nom ?? 'Non assigné' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-sm font-bold text-gray-700">{{ $zone->actifs_count }}</span>
                                    <span class="text-gray-400 text-xs">/ {{ $zone->total_credits_count }}</span>
                                </td>

                                {{-- Colonne : Total prêté (USD + CDF) --}}
                                <td class="px-6 py-4 text-right">
                                    <div class="space-y-1">
                                        <div class="text-sm font-black text-emerald-600">
                                            {{ number_format($zone->stats_usd->total_prete, 2) }} $
                                        </div>
                                        <div class="text-sm font-black text-blue-600">
                                            {{ number_format($zone->stats_cdf->total_prete, 0, ',', ' ') }} FC
                                        </div>
                                    </div>
                                </td>

                                {{-- Colonne : Total récupéré (USD + CDF) --}}
                                <td class="px-6 py-4 text-right">
                                    <div class="space-y-1">
                                        <div class="text-sm font-semibold text-emerald-600">
                                            {{ number_format($zone->stats_usd->total_recupere, 2) }} $
                                        </div>
                                        <div class="text-sm font-semibold text-blue-600">
                                            {{ number_format($zone->stats_cdf->total_recupere, 0, ',', ' ') }} FC
                                        </div>
                                    </div>
                                </td>

                                {{-- Colonne : Reste à recouvrer (USD + CDF) --}}
                                <td class="px-6 py-4 text-right">
                                    <div class="space-y-1">
                                        <div class="text-sm font-black text-orange-600">
                                            {{ number_format($zone->stats_usd->reste_a_recouvrer, 2) }} $
                                        </div>
                                        <div class="text-sm font-black text-red-600">
                                            {{ number_format($zone->stats_cdf->reste_a_recouvrer, 0, ',', ' ') }} FC
                                        </div>
                                    </div>
                                </td>

                                {{-- Statut (basé uniquement sur les retards) --}}
                                <td class="px-6 py-4 text-center">
                                    @if($zone->retards_count > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-600 animate-pulse">
                                            {{ $zone->retards_count }} retards
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-600">
                                            Correct
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('agences.zones.show', $zone->uuid) }}" 
                                        class="inline-flex items-center px-4 py-2 border border-blue-600 text-blue-600 text-xs font-bold rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                        Détails
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-400 italic">
                                    Aucune zone enregistrée pour cette agence.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-50">
                {{ $zones->links() }}
            </div>
        </div>
    @else
        <div class="bg-blue-50 border border-blue-100 p-6 rounded-2xl flex items-center">
            <div class="h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center mr-4 text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <p class="text-blue-700 font-medium">Veuillez sélectionner une agence dans le menu ci-dessus pour afficher les indicateurs de performance.</p>
        </div>
    @endif
</div>