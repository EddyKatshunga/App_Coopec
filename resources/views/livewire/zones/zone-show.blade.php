<div class="p-4 md:p-8 bg-gray-50 min-h-screen">
    {{-- Fil d'Ariane & En-tête --}}
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3 text-sm font-medium">
            <li class="inline-flex items-center">
                <a href="{{ route('agences.zones.index') }}" class="text-gray-500 hover:text-blue-600 transition-colors">Zones</a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/></svg>
                    <span class="text-gray-800 font-bold">{{ $zone->nom }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="flex flex-col lg:flex-row lg:items-end justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Zone : {{ $zone->nom }}</h1>
            <div class="flex items-center mt-2 text-gray-500 italic">
                <div class="h-2 w-2 rounded-full bg-green-500 mr-2"></div>
                Gérée par : <span class="font-semibold text-gray-700 ml-1">{{ $zone->gerant?->nom ?? 'Non assigné' }}</span>
            </div>
        </div>
        <div class="flex items-center">
            <span class="px-4 py-2 bg-gray-800 text-white text-xs font-bold rounded-xl shadow-lg border border-gray-700">
                AGENCE : {{ $zone->agence->nom }}
            </span>
        </div>
    </div>

    {{-- Synthèse Financière (Cartes avec Jauges) --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-10">
        @foreach(['USD' => ['data' => $this->statsUsd, 'color' => 'emerald'], 'CDF' => ['data' => $this->statsCdf, 'color' => 'blue']] as $devise => $config)
            @php $data = $config['data']; $color = $config['color']; @endphp
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 relative overflow-hidden group">
                <div class="absolute top-0 left-0 w-1.5 h-full bg-{{ $color }}-500"></div>
                
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Portefeuille {{ $devise }}</p>
                        <h3 class="text-3xl font-black text-gray-800 tracking-tight">
                            {{ number_format($data->total_prete, 2) }} <span class="text-lg text-gray-400 font-medium">{{ $devise }}</span>
                        </h3>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold text-gray-400 mb-1 italic uppercase">Progression</p>
                        <span class="text-2xl font-black text-{{ $color }}-600">{{ $data->taux_recouvrement }}%</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                        <p class="text-[10px] font-bold text-gray-400 uppercase">Récupéré</p>
                        <p class="text-sm font-bold text-gray-700">{{ number_format($data->total_recupere, 2) }}</p>
                    </div>
                    <div class="bg-red-50 rounded-xl p-3 border border-red-100">
                        <p class="text-[10px] font-bold text-red-400 uppercase">Reste à payer</p>
                        <p class="text-sm font-black text-red-600">{{ number_format($data->reste_a_recouvrer, 2) }}</p>
                    </div>
                </div>

                {{-- Barre de progression custom --}}
                <div class="relative w-full h-3 bg-gray-100 rounded-full overflow-hidden">
                    <div class="absolute top-0 left-0 h-full bg-{{ $color }}-500 transition-all duration-1000 shadow-[0_0_10px_rgba(0,0,0,0.1)]" 
                         style="width: {{ $data->taux_recouvrement }}%">
                    </div>
                </div>
                <p class="mt-2 text-right text-[10px] font-bold text-gray-400 uppercase">Taux de recouvrement actuel</p>
            </div>
        @endforeach
    </div>

    {{-- Liste des Crédits Actifs --}}
    <div class="bg-white rounded-3xl border border-gray-100 shadow-xl overflow-hidden">
        <div class="px-6 py-6 border-b border-gray-50 flex flex-col md:flex-row justify-between items-center gap-4 bg-white">
            <h5 class="text-lg font-black text-gray-800 flex items-center">
                Crédits en cours
                <span class="ml-3 px-2.5 py-0.5 bg-blue-100 text-blue-600 rounded-full text-xs font-bold">{{ $this->credits->total() }}</span>
            </h5>
            <div class="relative w-full md:w-72">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search" 
                       class="block w-full pl-10 pr-4 py-2 border-none bg-gray-100 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all" 
                       placeholder="Rechercher un membre...">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100">
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">N° Crédit</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Membre & Agent</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Total du prêt</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Remboursements</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Échéance & Statut</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($this->credits as $credit)
                    <tr class="hover:bg-gray-50/50 transition-colors {{ $credit->estEnRetard() ? 'bg-orange-50/30' : '' }}">
                        <td class="px-6 py-4">
                            <span class="text-xs font-mono font-bold text-gray-500 bg-gray-100 px-2 py-1 rounded">#{{ $credit->numero_credit }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-gray-800">{{ $credit->membre->nom }}</div>
                            <div class="text-[10px] text-gray-400 flex items-center mt-0.5 uppercase tracking-tighter font-semibold">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
                                {{ $credit->agent?->nom }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <div class="flex items-baseline gap-1">
                                    <span class="text-sm font-black text-gray-900">
                                        {{ number_format($credit->total, 2, ',', ' ') }}
                                    </span>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                        {{ $credit->monnaie }}
                                    </span>
                                </div>
                                
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="text-[10px] font-medium text-gray-500 bg-gray-50 px-1.5 py-0.5 rounded-md border border-gray-100">
                                        {{ number_format($credit->capital, 2, ',', ' ') }} <span class="text-gray-300 mx-0.5">+</span> {{ number_format($credit->interet, 2, ',', ' ') }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <div class="text-sm font-semibold text-green-600">
                                    {{ number_format($credit->total_remboursement, 2) }}
                                </div>
                                <div class="text-[10px] font-bold text-gray-400">
                                    sur {{ number_format($credit->total, 2) }}
                                </div>
                                <div class="mt-1 w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-green-500 h-full rounded-full" 
                                         style="width: {{ $credit->total > 0 ? ($credit->total_remboursement / $credit->total) * 100 : 0 }}%">
                                    </div>
                                </div>
                                <div class="text-[9px] font-bold text-red-500 mt-1">
                                    Reste: {{ number_format($credit->reste_du, 2) }}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <div class="inline-flex flex-col items-center">
                                    <span class="text-xs font-bold {{ $credit->date_fin_prevue->isPast() ? 'text-red-600 bg-red-100' : 'text-gray-700 bg-gray-100' }} px-2 py-1 rounded-md">
                                        {{ $credit->date_fin_prevue->format('d/m/Y') }}
                                    </span>
                                </div>
                                
                                @if($credit->estEnRetard())
                                    <div class="inline-flex items-center px-3 py-1 bg-red-600 text-white rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm">
                                        <span class="mr-1.5 flex h-1.5 w-1.5 rounded-full bg-white animate-ping"></span>
                                        En retard
                                    </div>
                                @else
                                    <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-[10px] font-black uppercase tracking-widest">
                                        En cours
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('credit.show', $credit->uuid) }}" 
                               class="inline-flex items-center justify-center h-9 w-9 bg-white border border-gray-200 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all shadow-sm group">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="h-16 w-16 bg-gray-50 rounded-full flex items-center justify-center text-gray-300 mb-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
                                </div>
                                <p class="text-gray-400 italic">Aucun crédit actif trouvé dans cette zone.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-5 bg-gray-50/30 border-t border-gray-50">
            {{ $this->credits->links() }}
        </div>
    </div>
</div>