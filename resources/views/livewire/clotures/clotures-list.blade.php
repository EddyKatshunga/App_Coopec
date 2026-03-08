<div class="space-y-6 max-w-[1600px] mx-auto">
    {{-- Header & Filtres --}}
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <h2 class="text-2xl font-black text-gray-900">Rapport Périodique</h2>
                <p class="text-sm text-gray-500">Analyse des flux financiers par agence</p>
            </div>

            <div class="flex flex-wrap items-center gap-4">
                @can('can.level6')
                <div class="flex items-center bg-gray-50 px-3 py-1 rounded-xl border border-gray-200">
                    <x-heroicon-o-building-office class="w-5 h-5 text-gray-400 mr-2" />
                    <select wire:model.live="agenceId" class="bg-transparent border-none text-sm focus:ring-0 font-bold py-2">
                        <option value="">-- Choisir une agence --</option>
                        @foreach($agences as $agence)
                            <option value="{{ $agence->id }}">{{ $agence->nom }}</option>
                        @endforeach
                    </select>
                </div>
                @endcan

                <div class="flex items-center bg-gray-50 px-4 py-2 rounded-xl border border-gray-200 space-x-2">
                    <x-heroicon-o-calendar class="w-5 h-5 text-gray-400" />
                    <input type="date" wire:model.live="dateDebut" class="bg-transparent border-none text-sm focus:ring-0 p-0 w-32">
                    <span class="text-gray-400 font-bold">→</span>
                    <input type="date" wire:model.live="dateFin" class="bg-transparent border-none text-sm focus:ring-0 p-0 w-32">
                </div>

                @if($agenceId)
                <a href="{{ route('impressions.periodique', ['agenceId' => $agenceId, 'debut' => $dateDebut, 'fin' => $dateFin]) }}" 
                target="_blank"
                class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition-all shadow-md">
                    <x-heroicon-o-arrow-down-tray class="w-5 h-5 mr-2" />
                    Relevé Périodique
                </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Dashboard Périodique (Stats agrégées) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Épargne --}}
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 border-l-4 border-l-blue-500">
            <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Flux Épargne</p>
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500 italic">Dépôts</span>
                    <span class="font-bold text-green-600 font-mono">+{{ number_format($stats->depot_usd, 2) }} $</span>
                </div>
                <div class="flex justify-between items-center border-b border-gray-50 pb-2">
                    <span class="text-sm text-gray-500 italic">Retraits</span>
                    <span class="font-bold text-red-500 font-mono">-{{ number_format($stats->retrait_usd, 2) }} $</span>
                </div>
                <div class="flex justify-between items-center pt-1">
                    <span class="text-xs text-gray-400">Net CDF</span>
                    <span class="text-xs font-bold text-gray-600 font-mono">{{ number_format($stats->depot_cdf - $stats->retrait_cdf, 0) }} FC</span>
                </div>
            </div>
        </div>

        {{-- Crédits --}}
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 border-l-4 border-l-purple-500">
            <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Portefeuille Crédit</p>
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500 italic">Octroyés</span>
                    <span class="font-bold text-gray-900 font-mono">{{ number_format($stats->credit_usd, 0) }} $</span>
                </div>
                <div class="flex justify-between items-center border-b border-gray-50 pb-2">
                    <span class="text-sm text-gray-500 italic">Recouvrés</span>
                    <span class="font-bold text-indigo-600 font-mono">{{ number_format($stats->rembourse_usd, 0) }} $</span>
                </div>
                <div class="text-[10px] text-center text-gray-400 uppercase mt-2">Période sélectionnée</div>
            </div>
        </div>

        {{-- Dépenses --}}
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 border-l-4 border-l-orange-400">
            <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Charges & Dépenses</p>
            <div class="flex flex-col items-center justify-center h-20">
                <span class="text-2xl font-black text-orange-600 font-mono">{{ number_format($stats->depense_usd, 2) }} $</span>
                <span class="text-sm text-orange-400 font-mono">{{ number_format($stats->depense_cdf, 0) }} FC</span>
            </div>
        </div>

        {{-- État Journées --}}
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 p-5 rounded-2xl shadow-lg text-white">
            <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Activité</p>
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm text-gray-300 italic">Journées traitées</span>
                <span class="text-xl font-bold">{{ $clotures->total() }}</span>
            </div>
            <div class="w-full bg-gray-700 h-1.5 rounded-full mt-4">
                <div class="bg-indigo-500 h-1.5 rounded-full w-full"></div>
            </div>
            <p class="text-[10px] text-gray-500 mt-2">Données extraites en temps réel</p>
        </div>
    </div>

    {{-- Tableau --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50/50 text-[11px] uppercase font-black text-gray-500 tracking-wider">
                    <th class="px-6 py-4">Date de clôture</th>
                    <th class="px-6 py-4">Statut</th>
                    <th class="px-6 py-4 text-right">Mouvements USD (Net)</th>
                    <th class="px-6 py-4 text-right">Mouvements CDF (Net)</th>
                    <th class="px-6 py-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($clotures as $cloture)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 capitalize font-bold text-gray-900">
                            {{ $cloture->date_cloture->isoFormat('DD MMMM YYYY') }}
                        </td>
                        <td class="px-6 py-4">
                            @if($cloture->statut === 'ouverte')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-2 animate-pulse"></span> Ouverte
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                    Clôturée
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="text-sm font-mono font-bold text-gray-900">
                                {{ number_format($cloture->solde_coffre_usd, 2) }} $
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="text-sm font-mono text-gray-600">
                                {{ number_format($cloture->solde_coffre_cdf, 0) }} FC
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('clotures.show', $cloture) }}" wire:navigate class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors inline-block">
                                <x-heroicon-o-eye class="w-5 h-5" />
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center">
                            <x-heroicon-o-document-magnifying-glass class="w-12 h-12 text-gray-300 mx-auto mb-4" />
                            <p class="text-gray-500">Aucune donnée historique sur cette période.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 bg-gray-50/30 border-t border-gray-100">
            {{ $clotures->links() }}
        </div>
    </div>
</div>