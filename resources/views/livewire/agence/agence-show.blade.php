<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-8">
    
    {{-- BARRE DE NAVIGATION & STATUT --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <a href="{{ url()->previous() }}" wire:navigate class="text-xs font-bold uppercase tracking-wider text-blue-600 hover:text-blue-800 transition">
                    ← Retour
                </a>
            </nav>
            <div class="flex items-center space-x-3">
                <h2 class="text-3xl font-black text-slate-900 tracking-tight">{{ $agence->nom }}</h2>
                @if($agence->journeeOuverte())
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 animate-pulse">
                        <span class="w-2 h-2 mr-1.5 bg-emerald-500 rounded-full"></span> Opérationnelle
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-500">
                        <span class="w-2 h-2 mr-1.5 bg-slate-400 rounded-full"></span> Fermée
                    </span>
                @endif
            </div>
            <p class="text-slate-500 text-sm mt-1">{{ $agence->ville }}, {{ $agence->pays }} | Code: <span class="font-mono font-bold">{{ $agence->code ?? 'N/A' }}</span></p>
        </div>

        <div class="flex flex-wrap gap-3">
            @can('can.level6')
            <a href="{{ route('agences.zones.create', $agence->uuid) }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-xl font-bold text-sm shadow-sm hover:bg-slate-50 transition">
                <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Nouvelle Zone
            </a>
            <a href="{{ route('membre.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-xl font-bold text-sm shadow-sm hover:bg-slate-50 transition">
                <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Nouvell Agent
            </a>    
            @endcan

            @if (!$agence->journeeOuverte())
                {{-- Cas : Journée fermée --}}
                <a href="{{ route('clotures.ouvrir', $agence) }}" class="inline-flex items-center px-6 py-2 bg-emerald-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-emerald-200 hover:bg-emerald-700 transition active:scale-95">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Démarrer les activités
                </a>
            @else
                {{-- Cas : Journée ouverte --}}
                <a href="{{ route('clotures.show', $agence->journeeOuverte()) }}" class="inline-flex items-center px-6 py-2 bg-blue-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-blue-200 hover:bg-blue-700 transition active:scale-95">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Voir la journée en cours
                </a>
            @endif

        </div>
    </div>

    {{-- SECTION FINANCIÈRE : LES SOLDES --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-slate-900 rounded-3xl p-8 text-white relative overflow-hidden shadow-2xl">
            <div class="relative z-10">
                <div class="flex justify-between items-start">
                    <span class="text-slate-400 text-xs font-bold uppercase tracking-widest">Trésorerie Coffre (Cash)</span>
                    <svg class="w-8 h-8 text-slate-700" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/><path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/></svg>
                </div>
                <div class="mt-6 space-y-4">
                    <div>
                        <p class="text-3xl font-mono font-bold tracking-tighter">{{ number_format_fr($agence->solde_actuel_coffre_cdf, 'CDF') }} <span class="text-blue-400 text-sm">CDF</span></p>
                    </div>
                    <div class="pt-4 border-t border-slate-800">
                        <p class="text-2xl font-mono font-bold tracking-tighter">{{ number_format_fr($agence->solde_actuel_coffre_usd, 'USD') }} <span class="text-emerald-400 text-sm">USD</span></p>
                    </div>
                </div>
            </div>
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-slate-800 rounded-full opacity-50"></div>
        </div>

        <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-xl relative overflow-hidden">
            <div class="relative z-10">
                <div class="flex justify-between items-start">
                    <span class="text-slate-500 text-xs font-bold uppercase tracking-widest text-blue-600">Encours Épargne</span>
                    <svg class="w-8 h-8 text-blue-100" fill="currentColor" viewBox="0 0 20 20"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/></svg>
                </div>
                <div class="mt-6 space-y-4">
                    <div>
                        <p class="text-3xl font-mono font-bold text-slate-800 tracking-tighter">{{ number_format_fr($agence->solde_actuel_epargne_cdf, 'CDF') }} <span class="text-slate-400 text-sm italic">CDF</span></p>
                    </div>
                    <div class="pt-4 border-t border-slate-50">
                        <p class="text-2xl font-mono font-bold text-slate-800 tracking-tighter">{{ number_format_fr($agence->solde_actuel_epargne_usd, 'USD') }} <span class="text-slate-400 text-sm italic">USD</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CARTE : SITUATION DES CRÉDITS --}}
    <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-xl relative overflow-hidden">
        <div class="relative z-10">
            <div class="flex justify-between items-start">
                <span class="text-slate-500 text-xs font-bold uppercase tracking-widest text-amber-600">Portefeuille Crédit</span>
                
                <a href="{{ route('agences.zones.index') }}" class="flex items-center gap-1.5 text-[10px] font-black uppercase tracking-wider text-slate-400 hover:text-amber-600 transition-colors group">
                    <span>Voir tous les détails</span>
                    <svg xmlns="http://w3.org" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 transform group-hover:translate-x-0.5 transition-transform">
                        <path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" />
                    </svg>
                </a>
            </div>

            {{-- Devise CDF --}}
            <div class="mt-6 space-y-3">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase mb-1">CDF</p>
                    <div class="flex justify-between text-sm">
                        <div>
                            <p class="text-sm font-bold text-slate-800">
                                {{ number_format_fr($bilanCdf->capital_interet_total, 'CDF') }}
                                <span class="text-xs text-slate-400">déhors</span>
                            </p>
                            <p class="text-xs text-slate-500">
                                Recouvré : {{ number_format_fr($bilanCdf->montant_recupere, 'CDF') }}
                            </p>
                            <p class="text-xs text-red-500 font-medium">
                                Reste : {{ number_format_fr($bilanCdf->reste_a_recouvrer, 'CDF') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600">
                                {{ $bilanCdf->nombre_credits_actifs }} actif(s)
                            </span>
                            @if($bilanCdf->nombre_retards > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700 mt-1">
                                    {{ $bilanCdf->nombre_retards }} retard(s)
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Devise USD --}}
                <div class="pt-4 border-t border-slate-100">
                    <p class="text-xs font-bold text-slate-400 uppercase mb-1">USD</p>
                    <div class="flex justify-between text-sm">
                        <div>
                            <p class="text-sm font-bold text-slate-800">
                                {{ number_format_fr($bilanUsd->capital_interet_total, 'USD') }}
                                <span class="text-xs text-slate-400">déhors</span>
                            </p>
                            <p class="text-xs text-slate-500">
                                Recouvré : {{ number_format_fr($bilanUsd->montant_recupere, 'USD') }}
                            </p>
                            <p class="text-xs text-red-500 font-medium">
                                Reste : {{ number_format_fr($bilanUsd->reste_a_recouvrer, 'USD') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600">
                                {{ $bilanUsd->nombre_credits_actifs }} actif(s)
                            </span>
                            @if($bilanUsd->nombre_retards > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700 mt-1">
                                    {{ $bilanUsd->nombre_retards }} retard(s)
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Décoration en fond --}}
        <div class="absolute -left-10 -bottom-10 w-32 h-32 bg-amber-50 rounded-full opacity-50"></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- COLONNE GAUCHE : DIRECTION ET AGENTS --}}
        <div class="lg:col-span-1 space-y-8">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-bold text-slate-800 tracking-tight text-lg italic">Direction</h3>
                        @can('can.level6')
                        <button wire:click="ouvrirModal" class="p-2 text-slate-400 hover:text-red-600 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </button>
                        @endcan
                    </div>
                    
                    @if($agence->chefAgence)
                        <div class="flex items-center space-x-4">
                            <div class="h-14 w-14 rounded-full bg-blue-600 flex items-center justify-center text-white font-black text-xl border-4 border-blue-50 shadow-inner">
                                {{ substr($agence->chefAgence->user->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900 leading-none">{{ $agence->chefAgence->user->name }}</p>
                                <p class="text-xs text-slate-500 mt-1 italic">Chef d'Agence principal</p>
                            </div>
                        </div>
                    @else
                        <div class="p-4 bg-amber-50 border border-amber-100 rounded-xl text-center">
                            <p class="text-xs font-bold text-amber-700 uppercase">Poste Vacant</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-50 bg-slate-50/50">
                    <h3 class="font-bold text-slate-800 text-sm uppercase tracking-widest">Équipe Terrain</h3>
                </div>
                <ul class="divide-y divide-slate-100">
                    @forelse($agence->agents as $agent)
                        <li class="px-6 py-4 hover:bg-slate-50 transition flex items-center justify-between">
                            <div>
                                <p class="text-sm font-bold text-slate-800">{{ $agent->user->name }}</p>
                                <div class="flex gap-1 mt-1">
                                    @foreach($agent->user->roles as $role)
                                        <span class="text-[10px] font-bold px-1.5 py-0.5 bg-slate-100 text-slate-500 rounded uppercase tracking-tighter">{{ $role->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                            <a href="{{ route('agent.show', $agent) }}">
                                <svg class="w-4 h-4 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                            </a>
                        </li>
                    @empty
                        <li class="p-6 text-center text-slate-400 text-sm italic italic">Aucun agent</li>
                    @endforelse
                </ul>
            </div>
        </div>

        {{-- COLONNE DROITE : HISTORIQUE DES JOURNÉES --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between bg-white">
                    <h3 class="text-lg font-black text-slate-800 tracking-tight">Historique de l'Agence</h3>
                    <a href="{{ route('clotures.index') }}" class="text-xs font-bold text-slate-400 hover:text-indigo-600 transition-colors uppercase tracking-widest">
                        Tout voir
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] font-black uppercase tracking-widest text-slate-400 bg-slate-50/50">
                                <th class="px-8 py-4">Date</th>
                                <th class="px-4 py-4">Entrées (+)</th>
                                <th class="px-4 py-4 text-blue-600">Sortie (-)</th>
                                <th class="px-4 py-4">Reste Coffre</th>
                                <th class="px-8 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($clotures_journalieres as $item)
                                <tr class="group hover:bg-blue-50/30 transition">
                                    <td class="px-8 py-5">
                                        <span class="text-sm font-bold text-slate-700">{{ $item->date_cloture->isoFormat('ddd D MMMM YYYY') }}</span>
                                    </td>
                                    {{-- Total Entrée --}}
                                    <td class="px-4 py-5">
                                        <div class="text-xs font-semibold text-emerald-700">
                                            CDF {{ number_format_fr($item->total_entre_cdf, 'CDF') }}
                                        </div>
                                        <div class="text-xs font-semibold text-emerald-600">
                                            USD {{ number_format_fr($item->total_entre_usd, 'USD') }}
                                        </div>
                                    </td>
                                    {{-- Total Sortie --}}
                                    <td class="px-4 py-5">
                                        <div class="text-xs font-semibold text-red-700">
                                            CDF {{ number_format_fr($item->total_sortie_cdf, 'CDF') }}
                                        </div>
                                        <div class="text-xs font-semibold text-red-600">
                                            USD {{ number_format_fr($item->total_sortie_usd, 'USD') }}
                                        </div>
                                    </td>
                                    {{-- Solde Journée --}}
                                    <td class="px-4 py-5">
                                        <div class="text-sm font-black text-slate-800">
                                            CDF {{ number_format_fr($item->solde_coffre_cdf, 'CDF') }}
                                        </div>
                                        <div class="text-xs font-semibold text-slate-600">
                                            USD {{ number_format_fr($item->solde_coffre_usd, 'USD') }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 italic mt-1">{{ $item->statut }}</div>
                                    </td>
                                    <td class="px-8 py-5 text-right">
                                        <a href="{{ route('clotures.show', $item) }}" wire:navigate class="inline-flex items-center px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:border-blue-500 hover:text-blue-600 transition">
                                            Détails
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-12 text-center">
                                        <div class="text-slate-300">Aucune activité enregistrée.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL : RE-STYLISÉ --}}
    @if($showModal)
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" wire:click="$set('showModal', false)"></div>
            
            <div class="relative bg-white rounded-[2rem] shadow-2xl w-full max-w-lg overflow-hidden border border-slate-100 transform transition-all">
                <div class="bg-red-600 p-6 text-white text-center">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h3 class="text-xl font-black tracking-tight">Alerte Gouvernance</h3>
                    <p class="text-red-100 text-sm">Modification de la hiérarchie de l'agence</p>
                </div>

                <div class="p-8 space-y-6">
                    <div class="p-4 bg-slate-50 rounded-2xl flex items-center space-x-4 border border-slate-100">
                        <div class="h-10 w-10 bg-slate-200 rounded-full flex items-center justify-center font-bold text-slate-600 italic">
                            {{ substr($agence->chefAgence->user->name ?? '?', 0, 1) }}
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Sortant</p>
                            <p class="text-sm font-bold text-slate-700">{{ $agence->chefAgence->user->name ?? 'Inconnu' }}</p>
                        </div>
                    </div>

                    <div class="space-y-4 font-inter">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2 ml-1 italic">Nouveau Chef d'Agence</label>
                            <select wire:model="nouveauDirecteurId" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold focus:bg-white focus:ring-2 focus:ring-red-500 transition">
                                <option value="">-- Sélectionner l'agent --</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2 ml-1 italic">Signature Autorité (Mot de passe)</label>
                            <input type="password" wire:model="motDePasse" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:ring-2 focus:ring-red-500 transition">
                        </div>

                        <label class="flex items-start space-x-3 p-3 rounded-xl hover:bg-slate-50 transition cursor-pointer">
                            <input type="checkbox" wire:model="confirmation" class="mt-1 rounded border-slate-300 text-red-600 focus:ring-red-500">
                            <span class="text-xs text-slate-500 leading-relaxed font-medium">Je comprends que l'ancien chef d'agence perdra ses droits de signature immédiate au profit du nouvel agent.</span>
                        </label>
                    </div>

                    <div class="flex space-x-3 pt-4">
                        <button wire:click="$set('showModal', false)" class="flex-1 px-4 py-3 bg-slate-100 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-200 transition">Annuler</button>
                        <button wire:click="changerDirecteur" class="flex-2 px-8 py-3 bg-red-600 text-white rounded-xl font-bold text-sm hover:bg-red-700 shadow-lg shadow-red-200 transition active:scale-95">Confirmer le Transfert</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>