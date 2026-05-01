<div class="max-w-7xl mx-auto p-4 md:p-8 space-y-10 bg-gray-50 min-h-screen" 
     x-data="{ openMenu: false, showPasswordModal: false }">
    {{-- ================= HEADER DYNAMIQUE ================= --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
        <div class="flex items-center gap-5">
            <div class="relative">
                <img src="{{ $membre->user->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($membre->nom).'&color=7F9CF5&background=EBF4FF' }}" 
                     class="w-20 h-20 rounded-2xl object-cover border-4 border-white shadow-md">
                <span class="absolute -bottom-2 -right-2 px-2 py-1 {{ $membre->agent ? 'bg-amber-500' : 'bg-green-500' }} text-white text-[10px] font-black uppercase rounded-lg border-2 border-white">
                    {{ $membre->agent ? 'Agent' : 'Membre' }}
                </span>
            </div>
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">{{ $membre->nom }}</h1>
                <p class="text-sm text-gray-500 font-medium">Identifiant : <span class="text-blue-600 font-bold">#{{ $membre->numero_identification }}</span></p>
            </div>
        </div>

        <div class="flex flex-wrap gap-3">
            {{-- BOUTON TÉLÉCHARGER LA FICHE --}}
            <a href="{{ route('impressions.membre.fiche', $membre) }}" 
               class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 text-white font-bold hover:bg-emerald-700 transition shadow-lg shadow-emerald-100">
                <x-heroicon-o-arrow-down-tray class="w-5 h-5"/>
                Fiche Membre
            </a>

            {{-- CONDITION : Uniquement si l'utilisateur connecté est le propriétaire du compte --}}
            @if(auth()->id() === $membre->user_id)
                <a href="{{ route('membres.change-password', $membre) }}" 
                wire:navigate
                class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gray-100 text-gray-700 font-bold hover:bg-gray-200 transition">
                    <x-heroicon-o-key class="w-5 h-5"/>
                    Sécurité
                </a>
            @endif

            {{-- Actions d'Administration --}}
            @can('can.level4')
                <div class="h-10 w-px bg-gray-200 mx-2 hidden lg:block"></div>

                <div class="relative">
                    <button @click="openMenu = !openMenu" @click.away="openMenu = false" 
                            class="flex items-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-200">
                        Actions Gestion
                        <x-heroicon-o-chevron-down class="w-4 h-4 transition-transform" ::class="openMenu ? 'rotate-180' : ''"/>
                    </button>
                    
                    <div x-show="openMenu" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="absolute right-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50">
                        
                        @if (!$membre->agent)
                            <a href="{{ route('agent.create', $membre) }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50">
                                <x-heroicon-o-user-plus class="w-4 h-4 text-blue-500"/> Promouvoir Agent
                            </a>
                        @endif
                        
                        <a href="{{ route('membre.edit', $membre) }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50">
                            <x-heroicon-o-pencil-square class="w-4 h-4 text-gray-400"/> Modifier Profil
                        </a>
                        
                        <a href="{{ route('compte.create', $membre) }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50">
                            <x-heroicon-o-plus-circle class="w-4 h-4 text-green-500"/> Ouvrir Compte Epargne
                        </a>
                        
                        <div class="border-t border-gray-50 my-1"></div>
                        
                        <a href="{{ route('credit.pret.create', $membre) }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-indigo-600 font-bold hover:bg-indigo-50">
                            <x-heroicon-o-banknotes class="w-4 h-4"/> Accorder un Crédit
                        </a>
                    </div>
                </div>
            @endcan
        </div>
    </div>

    {{-- ================= GRID PRINCIPAL ================= --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- COLONNE GAUCHE : INFOS & STATS --}}
        <div class="lg:col-span-1 space-y-8">
            {{-- Infos Personnelles --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-6">Détails Profil</h3>
                <ul class="space-y-4">
                    <li class="flex justify-between">
                        <span class="text-gray-500 font-medium text-sm">Téléphone</span>
                        <span class="text-gray-900 font-bold text-sm">{{ $membre->telephone }}</span>
                    </li>
                    <li class="flex justify-between">
                        <span class="text-gray-500 font-medium text-sm">Qualité</span>
                        <span class="px-2 py-0.5 bg-blue-50 text-blue-600 text-xs font-bold rounded-md">{{ $membre->qualite }}</span>
                    </li>
                    <li class="flex justify-between">
                        <span class="text-gray-500 font-medium text-sm">Adhésion</span>
                        <span class="text-gray-900 font-bold text-sm">{{ $membre->date_adhesion ? $membre->date_adhesion->format('d M Y') : 'N/A' }}</span>
                    </li>
                </ul>
                <div class="mt-8 pt-6 border-t border-dashed">
                    <p class="text-xs text-gray-400 font-bold uppercase mb-2">Adresse</p>
                    <p class="text-sm text-gray-700 leading-relaxed font-medium">{{ $membre->adresse ?? 'Non renseignée' }}</p>
                </div>
            </div>

            {{-- Widget Epargne --}}
            <div class="bg-gradient-to-br from-indigo-600 to-blue-700 rounded-3xl p-6 text-white shadow-xl relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                <p class="text-indigo-100 text-xs font-bold uppercase tracking-widest relative">Épargne Consolidée</p>
                <div class="mt-4 flex flex-col gap-1 relative">
                    <span class="text-3xl font-black">{{ number_format($totalSoldeCDF ?? 0, 0, ',', ' ') }} <small class="text-sm">CDF</small></span>
                    <span class="text-xl font-bold opacity-80">{{ number_format($totalSoldeUSD ?? 0, 2, ',', ' ') }} <small class="text-sm text-white/60">USD</small></span>
                </div>
                <div class="mt-6 pt-4 border-t border-white/10 flex justify-between items-center relative">
                    <span class="text-xs text-white/60 font-medium">{{ $membre->comptes->count() }} Comptes actifs</span>
                    <x-heroicon-s-wallet class="w-5 h-5 opacity-50"/>
                </div>
            </div>
        </div>

        {{-- COLONNE DROITE : PRODUITS FINANCIERS --}}
        <div class="lg:col-span-2 space-y-10">
            
            {{-- SECTION CRÉDITS EN COURS --}}
            <section>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-black text-gray-900">Engagements Crédits</h2>
                    @php $creditsActifs = $membre->credits->whereNotIn('statut', ['termine', 'termine_en_retard', 'termine_negocie']); @endphp
                </div>

                <div class="grid grid-cols-1 gap-4">
                    @forelse ($creditsActifs as $credit)
                        <div class="bg-white border-l-4 {{ $credit->statut == 'en_retard' ? 'border-red-500' : 'border-green-500' }} rounded-2xl p-5 shadow-sm hover:shadow-md transition">
                            <div class="flex flex-col md:flex-row justify-between gap-4">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-xs font-black text-gray-400">#{{ $credit->numero_credit }}</span>
                                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-[10px] font-bold rounded uppercase">{{ $credit->statut }}</span>
                                    </div>
                                    <p class="text-lg font-black text-gray-900">
                                        {{ number_format_fr($credit->capital) }} 
                                        <span class="text-sm font-normal text-gray-500">{{ $credit->monnaie }}</span>
                                    </p>
                                </div>
                                <div class="md:text-right">
                                    <p class="text-xs text-gray-400 font-bold uppercase">Reste à payer</p>
                                    <p class="text-xl font-black {{ $credit->statut == 'en_retard' ? 'text-red-600' : 'text-gray-900' }}">
                                        {{ number_format_fr($credit->getSituationActuelle()['total_a_payer'] ?? 0) }}
                                        <small class="text-xs font-medium">{{ $credit->monnaie }}</small>
                                    </p>
                                </div>
                            </div>
                            
                            @php 
                                $pourcentage = ($credit->total > 0) ? ($credit->total_rembourse / $credit->total) * 100 : 0;
                            @endphp
                            <div class="mt-4 flex items-center gap-4">
                                <div class="flex-1 bg-gray-100 h-2 rounded-full overflow-hidden">
                                    <div class="bg-green-500 h-full transition-all duration-500" style="width: {{ min(100, $pourcentage) }}%"></div>
                                </div>
                                <span class="text-[10px] font-black text-gray-500 uppercase">{{ round($pourcentage) }}% payé</span>
                            </div>
                        </div>
                    @empty
                        <div class="bg-gray-100/50 border-2 border-dashed border-gray-200 p-8 rounded-3xl text-center">
                            <p class="text-gray-400 font-bold text-sm">Aucun engagement de crédit en cours.</p>
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- SECTION EPARGNES --}}
            <section>
                <h2 class="text-xl font-black text-gray-900 mb-4 tracking-tight">Comptes Epargnes</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($membre->comptes as $compte)
                        <a href="{{ route('compte.show', $compte) }}" class="group bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:border-blue-800 transition">
                            <div class="flex justify-between items-start mb-4">
                                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg group-hover:bg-blue-600 group-hover:text-white transition">
                                    <x-heroicon-o-credit-card class="w-5 h-5"/>
                                </div>
                                <span class="text-[10px] font-black text-gray-300 group-hover:text-blue-200 transition">N° {{ $compte->numero_compte }}</span>
                            </div>
                            <h4 class="font-bold text-gray-900 group-hover:text-blue-600 transition">{{ $compte->intitule }}</h4>
                            <div class="mt-4 grid grid-cols-2 gap-2 border-t pt-4">
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase">Solde CDF</p>
                                    <p class="text-sm font-black text-gray-700">{{ number_format($compte->solde_cdf, 0, ',', ' ') }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase">Solde USD</p>
                                    <p class="text-sm font-black text-gray-700">{{ number_format($compte->solde_usd, 2, ',', ' ') }}</p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>

            {{-- SECTION HISTORIQUE DES CRÉDITS --}}
            <section>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-black text-gray-900 tracking-tight">Historique des Crédits</h2>
                    @php $creditsArchives = $membre->credits->whereIn('statut', ['termine', 'termine_en_retard', 'termine_negocie']); @endphp
                    <span class="text-xs font-bold text-gray-400 uppercase">{{ $creditsArchives->count() }} Dossiers clôturés</span>
                </div>

                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50 border-b border-gray-100">
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">Référence & Date</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">Montant Total</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase text-center">Statut</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse ($creditsArchives as $creditAncien)
                                    <tr class="hover:bg-gray-50 transition group">
                                        <td class="px-6 py-4">
                                            <p class="text-sm font-bold text-gray-800">#{{ $creditAncien->numero_credit }}</p>
                                            <p class="text-[10px] text-gray-400 font-medium">Octroyé le {{ $creditAncien->date_credit ? $creditAncien->date_credit->format('d/m/Y') : 'N/A' }}</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-sm font-black text-gray-700">
                                                {{ number_format($creditAncien->total, 0, ',', ' ') }} 
                                                <span class="text-[10px] text-gray-400">{{ $creditAncien->monnaie }}</span>
                                            </p>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @php
                                                $badgeStyle = match($creditAncien->statut) {
                                                    'termine' => 'bg-green-100 text-green-700',
                                                    'termine_en_retard' => 'bg-orange-100 text-orange-700',
                                                    'termine_negocie' => 'bg-purple-100 text-purple-700',
                                                    default => 'bg-gray-100 text-gray-700'
                                                };
                                                $labelText = match($creditAncien->statut) {
                                                    'termine' => 'Soldé',
                                                    'termine_en_retard' => 'Soldé (Retard)',
                                                    'termine_negocie' => 'Négocié',
                                                    default => $creditAncien->statut
                                                };
                                            @endphp
                                            <span class="inline-block px-3 py-1 rounded-full text-[10px] font-black uppercase {{ $badgeStyle }}">
                                                {{ $labelText }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('credit.show', $creditAncien) }}" wire:navigate 
                                               class="inline-flex items-center gap-2 px-3 py-1.5 bg-white border border-gray-200 text-gray-600 text-xs font-bold rounded-xl hover:bg-blue-600 hover:text-white hover:border-blue-600 transition shadow-sm">
                                                Détails
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-10 text-center">
                                            <div class="flex flex-col items-center justify-center opacity-40">
                                                <x-heroicon-o-folder-open class="w-10 h-10 mb-2"/>
                                                <p class="text-xs font-bold uppercase tracking-widest">Aucun ancien crédit</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>