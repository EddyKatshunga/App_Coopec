<div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8 space-y-8">
    {{-- Carte principale --}}
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        {{-- En-tête avec dégradé --}}
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-5 sm:px-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="text-2xl font-bold text-white tracking-tight">
                        {{ $isOuverture ? 'Ouverture d\'une nouvelle journée' : 'Clôture de la journée' }}
                    </h3>
                    <p class="mt-1 text-indigo-100 text-sm font-medium flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Agence : <strong>{{ $agence->nom }}</strong>
                    </p>
                </div>
                @if(!$isOuverture)
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/20 backdrop-blur-sm rounded-full text-white text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ $cloture->date_cloture->format('d/m/Y') }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-semibold
                            {{ $cloture->statut === 'ouverte' ? 'bg-emerald-400/30 text-emerald-100' : 'bg-gray-400/30 text-gray-200' }}">
                            <span class="w-2 h-2 rounded-full {{ $cloture->statut === 'ouverte' ? 'bg-emerald-300 animate-pulse' : 'bg-gray-300' }}"></span>
                            {{ $cloture->statut }}
                        </span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Corps de la carte --}}
        <div class="p-6 sm:p-8 space-y-8">
            {{-- Checklist de vérifications --}}
            <div class="bg-blue-50/60 border border-blue-100 rounded-xl p-5">
                <h5 class="flex items-center gap-2 text-blue-800 font-semibold text-lg mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Vérifications préalables
                </h5>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($checklist as $key => $item)
                        <div class="flex items-center gap-3">
                            @if($item['ok'])
                                <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @endif
                            <span class="text-sm text-gray-700">{{ $item['message'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            @if(!$isOuverture)
                {{-- Grille de statistiques enrichie --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    {{-- Carte : Écritures comptables --}}
                    <div class="relative overflow-hidden bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition p-5 group">
                        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full opacity-10 blur-2xl group-hover:opacity-20 transition"></div>
                        <div class="relative z-10 space-y-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white shadow-md">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Écritures comptables</p>
                                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $statistiques['nb_ecritures'] }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Carte : Dépôts (CDF + USD) --}}
                    <div class="relative overflow-hidden bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition p-5 group">
                        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-full opacity-10 blur-2xl group-hover:opacity-20 transition"></div>
                        <div class="relative z-10 space-y-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-lg flex items-center justify-center text-white shadow-md">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Dépôts</p>
                                <p class="text-lg font-bold text-gray-800">{{ number_format($statistiques['depots']['CDF'], 0, ',', ' ') }} CDF</p>
                                <p class="text-sm text-gray-600">{{ number_format($statistiques['depots']['USD'], 0, ',', ' ') }} USD</p>
                            </div>
                        </div>
                    </div>

                    {{-- Carte : Retraits --}}
                    <div class="relative overflow-hidden bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition p-5 group">
                        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-gradient-to-br from-amber-500 to-amber-600 rounded-full opacity-10 blur-2xl group-hover:opacity-20 transition"></div>
                        <div class="relative z-10 space-y-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-amber-600 rounded-lg flex items-center justify-center text-white shadow-md">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Retraits</p>
                                <p class="text-lg font-bold text-gray-800">{{ number_format($statistiques['retraits']['CDF'], 0, ',', ' ') }} CDF</p>
                                <p class="text-sm text-gray-600">{{ number_format($statistiques['retraits']['USD'], 0, ',', ' ') }} USD</p>
                            </div>
                        </div>
                    </div>

                    {{-- Carte : Crédits octroyés --}}
                    <div class="relative overflow-hidden bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition p-5 group">
                        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full opacity-10 blur-2xl group-hover:opacity-20 transition"></div>
                        <div class="relative z-10 space-y-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center text-white shadow-md">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Crédits octroyés</p>
                                <p class="text-lg font-bold text-gray-800">{{ number_format($statistiques['credits']['CDF'], 0, ',', ' ') }} CDF</p>
                                <p class="text-sm text-gray-600">{{ number_format($statistiques['credits']['USD'], 0, ',', ' ') }} USD</p>
                            </div>
                        </div>
                    </div>

                    {{-- Carte : Remboursements --}}
                    <div class="relative overflow-hidden bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition p-5 group">
                        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-full opacity-10 blur-2xl group-hover:opacity-20 transition"></div>
                        <div class="relative z-10 space-y-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-lg flex items-center justify-center text-white shadow-md">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Remboursements</p>
                                <p class="text-lg font-bold text-gray-800">{{ number_format($statistiques['remboursements']['CDF'], 0, ',', ' ') }} CDF</p>
                                <p class="text-sm text-gray-600">{{ number_format($statistiques['remboursements']['USD'], 0, ',', ' ') }} USD</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tableau des mouvements par devise (débit/crédit comptable) --}}
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h5 class="font-semibold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                            </svg>
                            Balance comptable par devise
                        </h5>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Devise</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Débit total</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Crédit total</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Solde</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($statistiques['stats_devises'] as $devise => $stats)
                                    @php
                                        $solde = $stats['debit'] - $stats['credit'];
                                        $soldeClass = $solde >= 0 ? 'text-red-600' : 'text-emerald-600';
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $devise }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-red-600 font-semibold">{{ number_format($stats['debit'], 2, ',', ' ') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-emerald-600 font-semibold">{{ number_format($stats['credit'], 2, ',', ' ') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold {{ $soldeClass }}">{{ number_format($solde, 2, ',', ' ') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Formulaire de clôture --}}
                <form wire:submit.prevent="cloturer" class="space-y-5" wire:confirm="Etes-vous sûr de cloturer cette journée ?">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Observation (justification d'un éventuel écart)</label>
                        <textarea wire:model="donneesPhysiques.observation" rows="4"
                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition resize-none"
                            placeholder="Saisissez vos observations..."></textarea>
                    </div>
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-red-500 to-rose-600 text-white font-semibold rounded-xl shadow-lg hover:from-red-600 hover:to-rose-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                        {{ !$verificationsOk ? 'disabled' : '' }}>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Confirmer la clôture
                    </button>
                </form>
            @else
                {{-- Bouton d'ouverture --}}
                <div class="flex justify-center pt-4">
                    <button wire:click="ouvrir" wire:confirm="Etes-vous sûr d'ouvrir cette journée ?"
                        class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold text-lg rounded-2xl shadow-xl hover:from-emerald-600 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                        {{ !$verificationsOk ? 'disabled' : '' }}>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Ouvrir la journée comptable
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- Message de succès --}}
    @if(session()->has('message'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 flex items-center gap-3 animate-fadeInUp shadow-sm"
             x-data="{ show: true }" x-show="show" x-transition.opacity.duration.500ms>
            <svg class="w-6 h-6 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm font-medium text-emerald-800 flex-1">{{ session('message') }}</p>
            <button @click="show = false" class="text-emerald-400 hover:text-emerald-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif
</div>