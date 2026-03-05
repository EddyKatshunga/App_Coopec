<div class="max-w-6xl mx-auto p-6 space-y-8" wire:init="rafraichirEtat">

    {{-- ================= HEADER ================= --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Crédit #{{ $credit->numero_credit }}
            </h1>
            <p class="text-sm text-gray-600">
                Membre : {{ $credit->membre->nom ?? $credit->user->name }}
            </p>
        </div>

        <div class="flex items-center space-x-4">
            @can('agent.create') 
                @if($credit->canBeDeleted())
                    <button 
                        wire:click="deleteRecord('App\\Models\\Credit', {{ $credit->id }})"
                        wire:confirm="Êtes-vous sûr de vouloir supprimer ce crédit ?"
                        class="text-red-600 hover:text-red-900 text-sm font-medium"
                    >
                        Supprimer
                    </button>
                @endif
            @endcan

            <span class="px-3 py-1 rounded-full text-sm font-semibold
                @switch($credit->statut)
                    @case('en_cours') bg-blue-100 text-blue-800 @break
                    @case('en_retard') bg-orange-100 text-orange-800 @break
                    @case('termine') bg-green-100 text-green-800 @break
                    @case('termine_en_retard') bg-purple-100 text-purple-800 @break
                    @case('termine_negocie') bg-gray-700 text-white @break
                    @default bg-gray-100 text-gray-800
                @endswitch
            ">
                {{ ucfirst(str_replace('_', ' ', $credit->statut)) }}
            </span>
        </div>
    </div>

    {{-- ================= RÉSUMÉ FINANCIER ================= --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-white shadow rounded p-4 border-t-4 border-blue-500">
            <p class="text-sm text-gray-500 uppercase font-semibold">Échéance Finale</p>
            <p class="text-lg font-bold">{{ $credit->date_fin_prevue->format('d/m/Y') }}</p>
        </div>

        <div class="bg-white shadow rounded p-4">
            <p class="text-sm text-gray-500 uppercase font-semibold">Capital & Intérêts ({{ $credit->monnaie }})</p>
            <p class="text-lg font-bold">{{ number_format_fr($credit->total) }}</p>
        </div>

        <div class="bg-white shadow rounded p-4">
            <p class="text-sm text-gray-500 uppercase font-semibold">Pénalités (à ce jour)</p>
            <p class="text-lg font-bold text-red-600">
                {{ number_format_fr($penaliteCourante) }}
            </p>
            @if($joursRetard > 0)
                <p class="text-[10px] text-red-500 italic">+{{ $joursRetard }} jours de retard</p>
            @endif
        </div>

        <div class="bg-white shadow rounded p-4 bg-gray-50">
            <p class="text-sm text-gray-500 uppercase font-semibold">Total Remboursé</p>
            <p class="text-lg font-bold text-green-600">
                {{ number_format_fr($credit->total_rembourse) }}
            </p>
        </div>

        <div class="bg-white shadow rounded p-4 border-l-4 border-orange-500">
            <p class="text-sm text-gray-700 uppercase font-extrabold">Net à Payer</p>
            <p class="text-xl font-black text-orange-600">
                {{ number_format_fr($resteDu) }}
            </p>
        </div>
    </div>

    {{-- ================= HISTORIQUE ================= --}}
    <div class="bg-white shadow rounded p-6">
        <h2 class="text-lg font-semibold mb-6 flex items-center">
            <span class="mr-2">📊</span> Détails des Paiements Séquentiels
        </h2>

        @if($remboursements->isEmpty())
            <div class="text-center py-8 text-gray-400">
                Aucun remboursement pour le moment.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b text-gray-500 uppercase text-xs">
                            <th class="py-3 px-2">Date</th>
                            <th class="py-3 px-2 text-right">Montant Payé</th>
                            <th class="py-3 px-2 text-red-500 text-right">Pénalité</th>
                            <th class="py-3 px-2 text-orange-500 text-right">Intérêt</th>
                            <th class="py-3 px-2 text-green-500 text-right">Capital</th>
                            <th class="py-3 px-2 text-blue-600 text-right font-bold">Reste Dû</th>
                            <th class="py-3 px-2 text-blue-600 text-right font-bold">Reste Pen.</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($remboursements as $remb)
                            <tr class="hover:bg-gray-50">
                                <td class="py-4 px-2 font-medium">
                                    {{ $remb->date_paiement->format('d/m/Y') }}
                                </td>
                                <td class="py-4 px-2 text-right font-bold">
                                    {{ number_format($remb->montant, 2) }}
                                </td>
                                <td class="py-4 px-2 text-right text-red-600">
                                    {{ number_format($remb->montant_penalite_payee, 2) }}
                                </td>
                                <td class="py-4 px-2 text-right text-orange-600">
                                    {{ number_format($remb->montant_interet_payee, 2) }}
                                </td>
                                <td class="py-4 px-2 text-right text-green-600">
                                    {{ number_format($remb->montant_capital_payee, 2) }}
                                </td>
                                <td class="py-4 px-2 text-right font-mono font-semibold text-blue-700">
                                    {{ number_format($remb->reste_du_apres, 2) }}
                                </td>
                                <td class="py-4 px-2 text-right font-mono font-semibold text-blue-700">
                                    {{ number_format($remb->reste_penalite, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>