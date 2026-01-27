<div class="max-w-6xl mx-auto p-6 space-y-8" wire:init="rafraichirEtat">

    {{-- ================= HEADER ================= --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Crédit #{{ $credit->numero_credit }}
            </h1>
            <p class="text-sm text-gray-600">
                Membre : {{ $credit->membre->nom }} {{ $credit->membre->postnom }}
            </p>
        </div>

        <span class="px-3 py-1 rounded-full text-sm font-semibold
            @switch($credit->statut)
                @case('en_cours') bg-blue-100 text-blue-800 @break
                @case('en_retard') bg-orange-100 text-orange-800 @break
                @case('retard_penalite') bg-red-100 text-red-800 @break
                @case('termine') bg-green-100 text-green-800 @break
                @default bg-gray-100 text-gray-800
            @endswitch
        ">
            {{ ucfirst(str_replace('_', ' ', $credit->statut)) }}
        </span>
    </div>

    {{-- ================= RÉSUMÉ FINANCIER ================= --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white shadow rounded p-4">
            <p class="text-sm text-gray-500">Capital</p>
            <p class="text-xl font-bold">{{ number_format($credit->capital, 2) }}</p>
        </div>

        <div class="bg-white shadow rounded p-4">
            <p class="text-sm text-gray-500">Intérêt</p>
            <p class="text-xl font-bold">{{ number_format($credit->interet, 2) }}</p>
        </div>

        <div class="bg-white shadow rounded p-4">
            <p class="text-sm text-gray-500">Pénalités (aujourd’hui)</p>
            <p class="text-xl font-bold text-red-600">
                {{ number_format($penaliteCourante, 2) }}
            </p>
            @if($joursRetard > 0)
                <p class="text-xs text-gray-500">
                    {{ $joursRetard }} jour(s) de retard
                </p>
            @endif
        </div>

        <div class="bg-white shadow rounded p-4">
            <p class="text-sm text-gray-500">Reste dû</p>
            <p class="text-xl font-bold">
                {{ number_format($resteDu, 2) }}
            </p>
        </div>
    </div>

    {{-- ================= BOUTON AJOUTER REMBOURSEMENT ================= --}}
    <div>
        @livewire('credits.credit-add-remboursement', ['credit' => $credit])
    </div>

    {{-- ================= TIMELINE REMBOURSEMENTS ================= --}}
    <div class="bg-white shadow rounded p-6">
        <h2 class="text-lg font-semibold mb-4">
            📆 Historique des remboursements
        </h2>

        @if($remboursements->isEmpty())
            <p class="text-gray-500 italic">
                Aucun remboursement enregistré.
            </p>
        @else
            <ol class="relative border-l border-gray-200 space-y-6">
                @foreach($remboursements as $remboursement)
                    <li class="ml-6">
                        <span class="absolute -left-3 flex items-center
                            justify-center w-6 h-6 bg-blue-600 rounded-full
                            text-white text-xs">
                            💰
                        </span>

                        <div class="flex justify-between items-center">
                            <div>
                                <p class="font-semibold">
                                    {{ $remboursement->date_paiement->format('d/m/Y') }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    {{ $remboursement->mode_paiement_label }}
                                    — Agent :
                                    {{ $remboursement->agent->name }}
                                </p>
                            </div>

                            <p class="font-bold">
                                {{ number_format($remboursement->montant, 2) }}
                            </p>
                        </div>

                        <div class="grid grid-cols-3 gap-2 mt-2 text-sm">
                            <span class="text-red-600">
                                Pénalité :
                                {{ number_format($remboursement->montant_penalite_payee, 2) }}
                            </span>
                            <span class="text-orange-600">
                                Intérêt :
                                {{ number_format($remboursement->montant_interet_payee, 2) }}
                            </span>
                            <span class="text-green-600">
                                Capital :
                                {{ number_format($remboursement->montant_capital_payee, 2) }}
                            </span>
                        </div>

                        <p class="text-xs text-gray-500 mt-1">
                            Reste dû après paiement :
                            {{ number_format($remboursement->reste_du_apres, 2) }}
                        </p>
                    </li>
                @endforeach
            </ol>
        @endif
    </div>
</div>

{{-- ================= SCRIPT POUR RAFRAÎCHIR TIMELINE ================= --}}
<script>
    Livewire.on('remboursementAdded', () => {
        Livewire.emit('rafraichirEtat');
    });
</script>