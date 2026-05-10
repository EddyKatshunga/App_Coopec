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

                {{-- Badge de statut --}}
                <div class="flex flex-col gap-1 ml-16">
                    <div class="flex items-center gap-3">
                        @switch($cloture->statut)
                            @case('ouverte')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-400/30 backdrop-blur-sm rounded-full text-emerald-100 text-xs font-semibold">
                                    <span class="w-2 h-2 bg-emerald-300 rounded-full animate-pulse"></span>
                                    Ouverte
                                </span>
                                @break
                            @case('cloturee')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-400/30 backdrop-blur-sm rounded-full text-blue-100 text-xs font-semibold">
                                    <span class="w-2 h-2 bg-blue-300 rounded-full"></span>
                                    Clôturée
                                </span>
                                @break
                            @case('verrouillee')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-400/30 backdrop-blur-sm rounded-full text-gray-200 text-xs font-semibold">
                                    <span class="w-2 h-2 bg-gray-300 rounded-full"></span>
                                    Verrouillée
                                </span>
                                @break
                            @default
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-400/30 backdrop-blur-sm rounded-full text-gray-200 text-xs font-semibold">
                                    <span class="w-2 h-2 bg-gray-300 rounded-full"></span>
                                    {{ ucfirst($cloture->statut) }}
                                </span>
                        @endswitch
                    </div>

                    {{-- Messages explicatifs selon le statut --}}
                    <div class="text-xs text-indigo-200 max-w-md">
                        @switch($cloture->statut)
                            @case('ouverte')
                                <p>✓ La journée est ouverte : vous pouvez enregistrer des opérations (dépôts, retraits, crédits, etc.).</p>
                                <p>🔒 Une fois vos saisies terminées, <strong>verrouillez la journée</strong> pour empêcher toute modification avant la clôture définitive.</p>
                                @break
                            @case('verrouillee')
                                <p>🔐 La journée est verrouillée : plus aucune écriture ne peut être ajoutée, modifiée ou supprimée.</p>
                                <p>▶️ Vous pouvez la <strong>clôturer définitivement</strong> (cette action est irréversible et génère les écritures de résultat).</p>
                                <p>🔓 Si vous devez apporter des corrections, vous pouvez la <strong>rouvrir</strong> (retour à l’état ouvert).</p>
                                @break
                            @case('cloturee')
                                <p>✅ La journée est définitivement clôturée. Les soldes sont figés et les comptes de résultat sont transférés.</p>
                                <p>📄 Vous pouvez imprimer les rapports et relevés ci-dessous.</p>
                                @break
                        @endswitch
                    </div>
                </div>
            </div>

            {{-- Boutons d'actions selon le statut --}}
            <div class="flex-shrink-0 flex gap-3 flex-wrap">
                @switch($cloture->statut)
                    @case('ouverte')
                        {{-- Dropdown actions comptables --}}
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" @click.away="open = false"
                                    class="inline-flex items-center gap-2 px-6 py-3 bg-white text-slate-700 font-semibold rounded-xl hover:bg-slate-50 shadow-sm transition transform hover:scale-105">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Actions comptables
                                <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="open" x-transition
                                class="absolute right-0 mt-3 w-80 bg-white border border-gray-200 rounded-2xl shadow-2xl z-50 overflow-hidden">
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
                            </div>
                        </div>

                        {{-- Bouton Verrouiller la journée (direct) --}}
                        <button wire:click="verrouillerJournee"
                                wire:confirm="Êtes-vous sûr de vouloir verrouiller cette journée ? Plus aucune écriture ne pourra être ajoutée ou modifiée."
                                class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-bold rounded-xl hover:from-amber-600 hover:to-orange-700 shadow-md transition transform hover:scale-105">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            Verrouiller la journée
                        </button>
                    @break

                    @case('verrouillee')
                        {{-- Dropdown impressions --}}
                        @include('livewire.clotures.partials._impressions_dropdown')

                        {{-- Bouton Clôturer (route) --}}
                        <a href="{{ route('clotures.valider', $cloture) }}"
                           class="inline-flex items-center gap-2 px-6 py-3 bg-rose-600 text-white font-semibold rounded-xl hover:bg-rose-700 shadow-sm transition transform hover:scale-105">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Clôturer définitivement
                        </a>

                        {{-- Bouton Rouvrir --}}
                        <button wire:click="reopenJournee"
                                wire:confirm="Attention : rouvrir cette journée permettra à nouveau les modifications. Continuer ?"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-amber-600 text-white font-semibold rounded-xl hover:bg-amber-700 shadow-sm transition transform hover:scale-105">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Rouvrir la journée
                        </button>
                        @break

                    @case('cloturee')
                        @include('livewire.clotures.partials._impressions_dropdown')
                        @break

                    @default
                        {{-- aucun bouton --}}
                @endswitch
            </div>
        </div>
    </div>
</div>