<div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8 space-y-8">

    {{-- ================= HEADER PROFESSIONNEL ================= --}}
    <div class="relative overflow-hidden bg-white rounded-[2.5rem] shadow-xl border border-gray-100">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-64 h-64 bg-blue-50 rounded-full opacity-50 blur-3xl"></div>
        
        <div class="relative flex flex-col md:flex-row items-center p-8">
            <div class="relative group">
                <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full blur opacity-25 group-hover:opacity-50 transition duration-1000"></div>
                <img class="relative h-28 w-28 rounded-full object-cover border-4 border-white shadow-md" 
                    src="{{ $agent->user->photo_path ?? 'https://ui-avatars.com/api/?name=' . urlencode($agent->nom) . '&background=0D47A1&color=fff&bold=true' }}"
                    alt="{{ $agent->nom }}">
                <div class="absolute bottom-1 right-1 h-6 w-6 bg-green-500 border-4 border-white rounded-full"></div>
            </div>

            <div class="mt-6 md:mt-0 md:ml-8 flex-1 text-center md:text-left">
                <div class="flex flex-col md:flex-row md:items-center gap-3">
                    <a href="{{ route('membre.show', $agent->membre) }}">
                        <h1 class="text-3xl font-black text-gray-900 tracking-tight">{{ $agent->nom }}</h1>
                    </a>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-blue-100 text-blue-700 uppercase">
                        {{ $agent->agence->nom }}
                    </span>
                </div>
                <p class="text-gray-500 font-medium mt-1">{{ $agent->email }} • <span class="italic text-gray-400">Agent depuis {{ $agent->created_at->format('M Y') }}</span></p>
                
                <div class="mt-4 flex flex-wrap justify-center md:justify-start gap-2">
                    @if($agent->zone_dirige)
                        <a href="{{ route('agences.zones.show', $agent->zone_dirige) }}">
                            <span class="px-4 py-1.5 bg-indigo-600 text-white text-[10px] rounded-xl font-black uppercase shadow-sm">
                                Gérant Zone : {{ $agent->zone_dirige->nom }}
                            </span>
                        </a>
                    @endif
                    @if($agent->agence_dirige)
                        <a href="{{ route('agence.show', $agent->agence_dirige) }}">
                            <span class="px-4 py-1.5 bg-amber-500 text-white text-[10px] rounded-xl font-black uppercase shadow-sm">
                                Chef d'Agence {{ $agent->agence->nom }}
                            </span>
                        </a>
                    @endif
                </div>
            </div>

            @can('can.level6')
            @if(!$agent->user->hasAnyRole(['niveau 2', 'niveau 5']))
            <div class="mt-6 md:mt-0 flex gap-3">
                <a href="{{ route('agent.edit', $agent) }}"
                   class="flex items-center gap-2 px-6 py-3 bg-gray-900 text-white rounded-2xl font-bold hover:bg-gray-800 transition shadow-lg active:scale-95">
                    <x-heroicon-o-pencil-square class="w-5 h-5"/>
                    Modification Agence/Niveau
                </a>
            </div>
            @endif
            @endcan
        </div>
    </div>

    {{-- ================= ACTIONS RAPIDES (Quick Actions) ================= --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <button class="flex flex-col items-center p-4 bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-md hover:border-blue-200 transition group">
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-3 group-hover:bg-blue-600 group-hover:text-white transition">
                <x-heroicon-o-plus-circle class="w-6 h-6"/>
            </div>
            <span class="text-xs font-black text-gray-700 uppercase tracking-wider">Nouveau Dépôt</span>
        </button>

        <button class="flex flex-col items-center p-4 bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-md hover:border-green-200 transition group">
            <div class="w-12 h-12 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center mb-3 group-hover:bg-green-600 group-hover:text-white transition">
                <x-heroicon-o-banknotes class="w-6 h-6"/>
            </div>
            <span class="text-xs font-black text-gray-700 uppercase tracking-wider">Remboursement</span>
        </button>

        <button class="flex flex-col items-center p-4 bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-md hover:border-purple-200 transition group">
            <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center mb-3 group-hover:bg-purple-600 group-hover:text-white transition">
                <x-heroicon-o-user-plus class="w-6 h-6"/>
            </div>
            <span class="text-xs font-black text-gray-700 uppercase tracking-wider">Affilier Membre</span>
        </button>

        <button class="flex flex-col items-center p-4 bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-md hover:border-orange-200 transition group">
            <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center mb-3 group-hover:bg-orange-600 group-hover:text-white transition">
                <x-heroicon-o-document-plus class="w-6 h-6"/>
            </div>
            <span class="text-xs font-black text-gray-700 uppercase tracking-wider">Demande Crédit</span>
        </button>
    </div>

    {{-- ================= KPI PERFORMANCE GRID ================= --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <a href="{{ route('membre.index') }}" class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 hover:shadow-xl transition relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition duration-500">
                <x-heroicon-o-users class="w-32 h-32"/>
            </div>
            <div class="flex items-center gap-4 mb-4">
                <div class="p-3 bg-blue-50 text-blue-600 rounded-2xl"><x-heroicon-o-users class="w-6 h-6"/></div>
                <h3 class="text-[10px] font-black uppercase text-gray-400 tracking-[0.2em]">Portefeuille</h3>
            </div>
            <p class="text-3xl font-black text-gray-900">{{ number_format($agent->membres_amenes_count) }}</p>
            <p class="text-xs font-bold text-blue-600 mt-1 italic">Membres affiliés</p>
        </a>

        <a href="{{ route('epargne.transactions.index') }}" class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 hover:shadow-xl transition relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition duration-500">
                <x-heroicon-o-arrows-right-left class="w-32 h-32"/>
            </div>
            <div class="flex items-center gap-4 mb-4">
                <div class="p-3 bg-green-50 text-green-600 rounded-2xl"><x-heroicon-o-arrows-right-left class="w-6 h-6"/></div>
                <h3 class="text-[10px] font-black uppercase text-gray-400 tracking-[0.2em]">Collectes</h3>
            </div>
            <p class="text-3xl font-black text-gray-900">{{ number_format($agent->transactions_count) }}</p>
            <p class="text-xs font-bold text-green-600 mt-1 italic">Opérations effectuées</p>
        </a>

        <a href="{{ route('credit.pret.index') }}" class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 hover:shadow-xl transition relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition duration-500">
                <x-heroicon-o-document-chart-bar class="w-32 h-32"/>
            </div>
            <div class="flex items-center gap-4 mb-4">
                <div class="p-3 bg-orange-50 text-orange-600 rounded-2xl"><x-heroicon-o-document-chart-bar class="w-6 h-6"/></div>
                <h3 class="text-[10px] font-black uppercase text-gray-400 tracking-[0.2em]">Encours Suivi</h3>
            </div>
            <p class="text-3xl font-black text-gray-900">{{ number_format($agent->credits_count) }}</p>
            <p class="text-xs font-bold text-orange-600 mt-1 italic">Dossiers de crédit</p>
        </a>

        <a href="{{ route('remboursements.index') }}" class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 hover:shadow-xl transition relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition duration-500">
                <x-heroicon-o-currency-dollar class="w-32 h-32"/>
            </div>
            <div class="flex items-center gap-4 mb-4">
                <div class="p-3 bg-purple-50 text-purple-600 rounded-2xl"><x-heroicon-o-currency-dollar class="w-6 h-6"/></div>
                <h3 class="text-[10px] font-black uppercase text-gray-400 tracking-[0.2em]">Recouvrement</h3>
            </div>
            <p class="text-3xl font-black text-gray-900">{{ number_format($agent->remboursements_count) }}</p>
            <p class="text-xs font-bold text-purple-600 mt-1 italic">Reçus validés</p>
        </a>
    </div>

    {{-- ================= MAIN CONTENT ================= --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- COLONNE GAUCHE : INFOS & STATUT --}}
        <div class="space-y-8">
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8">
                <h3 class="text-lg font-black text-gray-900 mb-6 flex items-center justify-between">
                    Administration
                    <x-heroicon-o-identification class="w-5 h-5 text-gray-400"/>
                </h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-2xl">
                        <span class="text-xs font-bold text-gray-500 uppercase">Matricule</span>
                        <span class="text-sm font-black text-gray-800">#AG-{{ str_pad($agent->id, 4, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-2xl">
                        <span class="text-xs font-bold text-gray-500 uppercase">Dossier Membre</span>
                        <span class="text-sm font-black text-blue-600 underline">
                            {{ $agent->membre ? $agent->membre->num_dossier : 'N/A' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- COLONNE DROITE : ACTIVITÉ RÉCENTE --}}
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-black text-gray-900">Affiliations Récentes</h3>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1">Nouveaux membres parrainés</p>
                    </div>
                    <a href="{{ route('membre.index') }}" class="text-xs font-black text-blue-600 hover:text-blue-700 underline uppercase tracking-wider transition">
                        Voir tout
                    </a>
                </div>
                
                <div class="divide-y divide-gray-50">
                    @forelse($agent->membresAmenes()->latest()->take(6)->get() as $m)
                        <div class="px-8 py-4 flex items-center justify-between hover:bg-gray-50 transition cursor-pointer group">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center font-black text-gray-500 group-hover:bg-blue-100 group-hover:text-blue-600 transition">
                                    {{ substr($m->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-black text-gray-800 group-hover:text-blue-700 transition">{{ $m->name }}</p>
                                    <p class="text-[10px] text-gray-400 font-bold">{{ $m->num_dossier }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">{{ $m->created_at->diffForHumans() }}</span>
                                <div class="flex gap-1 mt-1 justify-end">
                                    <div class="h-1 w-4 bg-blue-400 rounded-full"></div>
                                    <div class="h-1 w-1 bg-gray-200 rounded-full"></div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-20 text-center">
                            <x-heroicon-o-face-frown class="w-12 h-12 text-gray-200 mx-auto mb-3"/>
                            <p class="text-sm text-gray-400 font-bold italic">Aucune activité enregistrée.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            
        </div>
    </div>
</div>