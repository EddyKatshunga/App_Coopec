{{-- resources/views/livewire/admin/performance-suivi.blade.php --}}
<div class="p-6 sm:p-8 bg-gray-50 min-h-screen">
    <div class="max-w-6xl mx-auto">
        <!-- En-tête (inchangé) -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Suivi des performances par agence</h1>
            <p class="text-sm text-slate-500">Analyse des collectes d'épargne, octroi de crédits, recouvrement et parrainage</p>
        </div>

        <!-- Filtres (inchangés) -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 mb-8">
            <form wire:submit.prevent="$refresh" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Agence</label>
                    <select wire:model="agence_id" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">-- Toutes les agences --</option>
                        @foreach($agences as $agence)
                            <option value="{{ $agence->id }}">{{ $agence->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Date début</label>
                    <input type="date" wire:model="date_debut" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Date fin</label>
                    <input type="date" wire:model="date_fin" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-6 rounded-xl shadow transition">
                        Appliquer les filtres
                    </button>
                </div>
            </form>
        </div>

        @if(!$agence_id)
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg">
                <p class="text-yellow-800">Veuillez sélectionner une agence pour afficher les performances.</p>
            </div>
        @else
            <div class="space-y-10">
                <!-- 1. Agents collecteurs d'épargne (CDF et USD séparés) -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-4">
                        <h2 class="text-xl font-semibold text-white flex items-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Top collecteurs d'épargne
                        </h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Agent</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Total CDF</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Total USD</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Nb opérations</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @forelse($topAgentsEpargne as $item)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-800">{{ $item['agent']?->nom ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-mono">{{ number_format($item['total_cdf'], 2, ',', ' ') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-mono">{{ number_format($item['total_usd'], 2, ',', ' ') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">{{ $item['nbre_ops'] }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-6 py-8 text-center text-slate-500">Aucune transaction d'épargne sur cette période.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 2. Zones octroi de crédits (capital et intérêts séparés en CDF/USD) -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                        <h2 class="text-xl font-semibold text-white flex items-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Top zones octroi de crédits
                        </h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Zone</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Chef de zone</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase" colspan="2">Capital octroyé</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase" colspan="2">Intérêts prévus</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Nb crédits</th>
                                </tr>
                                <tr class="bg-slate-50">
                                    <th colspan="2"></th>
                                    <th class="px-3 py-1 text-right text-xs text-slate-400">CDF</th>
                                    <th class="px-3 py-1 text-right text-xs text-slate-400">USD</th>
                                    <th class="px-3 py-1 text-right text-xs text-slate-400">CDF</th>
                                    <th class="px-3 py-1 text-right text-xs text-slate-400">USD</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @forelse($topZonesCredits as $item)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-800">{{ $item['zone']?->nom ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">{{ $item['chef']?->nom ?? 'Non défini' }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-right font-mono">{{ number_format($item['capital_cdf'], 2, ',', ' ') }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-right font-mono">{{ number_format($item['capital_usd'], 2, ',', ' ') }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-right font-mono">{{ number_format($item['interets_cdf'], 2, ',', ' ') }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-right font-mono">{{ number_format($item['interets_usd'], 2, ',', ' ') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">{{ $item['nbre_credits'] }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="px-6 py-8 text-center text-slate-500">Aucun crédit octroyé sur cette période.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 3. Zones recouvrement -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
                        <h2 class="text-xl font-semibold text-white flex items-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                            Top zones recouvrement
                        </h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Zone</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Chef de zone</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Total remboursé CDF</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Total remboursé USD</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Nb remboursements</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @forelse($topZonesRemboursements as $item)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-800">{{ $item['zone']?->nom ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">{{ $item['chef']?->nom ?? 'Non défini' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-mono">{{ number_format($item['total_cdf'], 2, ',', ' ') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-mono">{{ number_format($item['total_usd'], 2, ',', ' ') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">{{ $item['nbre_remboursements'] }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-6 py-8 text-center text-slate-500">Aucun remboursement enregistré sur cette période.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 4. Top agents parrainage (inchangé) -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
                        <h2 class="text-xl font-semibold text-white flex items-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            Top agents parrainage (membres amenés)
                        </h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Agent</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Nouveaux membres</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @forelse($topAgentsParrainage as $item)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-800">{{ $item['agent']?->nom ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-mono">{{ $item['total_membres'] }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="px-6 py-8 text-center text-slate-500">Aucune adhésion de membre sur cette période.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>