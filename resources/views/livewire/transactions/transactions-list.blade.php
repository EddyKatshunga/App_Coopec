<div class="bg-white rounded-xl shadow overflow-x-auto">
    <div
        x-data
        x-init="
            $nextTick(() => {
                $el.scrollTop = $el.scrollHeight
            })
        "
        x-on:livewire:update.window="
            $nextTick(() => {
                $el.scrollTop = $el.scrollHeight
            })
        "
        class="bg-white rounded-xl shadow overflow-y-auto"
    >

    {{-- ================= MESSAGE SUCCÈS ================= --}}
    @if (session()->has('success'))
        <div class="mb-4 mx-4 mt-4 rounded-lg bg-green-100 px-4 py-3 text-green-800 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- ================= ACTIONS ================= --}}
    <div class="flex flex-wrap gap-2 px-4 pb-3">
        <a href="{{ route('transaction.depot') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 transition">
            ➕ Ajouter un dépôt
        </a>

        <a href="{{ route('transaction.retrait') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 transition">
            ➖ Ajouter un retrait
        </a>
    </div>

    {{-- ================= TABLE ================= --}}
    <table class="min-w-full text-sm">
        <thead class="bg-gray-100 text-gray-700 uppercase text-xs sticky top-0 z-10">
            <tr>
                <th class="px-4 py-3 text-left">Date transaction</th>
                <th class="px-4 py-3 text-left">Nom du membre</th>
                <th class="px-4 py-3 text-left">Numéro compte</th>
                <th class="px-4 py-3 text-left">Type opération</th>
                <th class="px-4 py-3 text-center">Monnaie</th>
                <th class="px-4 py-3 text-right">Montant</th>
                <th class="px-4 py-3 text-right">Report</th>
                <th class="px-4 py-3 text-right">Solde</th>
                <th class="px-4 py-3 text-right">Agence</th>
            </tr>
        </thead>

        <tbody class="divide-y">
            @forelse ($transactions as $transaction)
                <tr class="hover:bg-gray-50 transition">

                    {{-- Date --}}
                    <td class="px-4 py-3 text-gray-700">
                        {{ \Carbon\Carbon::parse($transaction->date_transaction)->format('d/m/Y') }}
                    </td>

                    {{-- Nom du membre --}}
                    <td class="px-4 py-3 font-medium text-gray-800">
                        {{ $transaction->compte->nom }}
                    </td>

                    {{-- Compte --}}
                    <td class="px-4 py-3 font-medium text-gray-800">
                        {{ $transaction->compte->numero_compte }}
                    </td>

                    {{-- Type --}}
                    <td class="px-4 py-3 font-semibold
                        {{ $transaction->type_transaction === 'DEPOT'
                            ? 'text-green-600'
                            : 'text-red-600' }}">
                        {{ strtoupper($transaction->type_transaction) }}
                    </td>

                    {{-- Monnaie --}}
                    <td class="px-4 py-3 text-center font-medium text-gray-700">
                        {{ $transaction->monnaie }}
                    </td>

                    {{-- Montant --}}
                    <td class="px-4 py-3 text-right font-bold
                        {{ $transaction->type_transaction === 'DEPOT'
                            ? 'text-green-600'
                            : 'text-red-600' }}">
                        {{ number_format($transaction->montant, 2, ',', ' ') }}
                    </td>

                    {{-- Solde avant --}}
                    <td class="px-4 py-3 text-right text-gray-600">
                        {{ number_format($transaction->solde_avant, 2, ',', ' ') }}
                    </td>

                    {{-- Solde après --}}
                    <td class="px-4 py-3 text-right font-semibold text-gray-900">
                        {{ number_format($transaction->solde_apres, 2, ',', ' ') }}
                    </td>

                    {{-- Agence --}}
                    <td class="px-4 py-3 text-right font-semibold text-gray-900">
                        {{ $transaction->agence->nom }}
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                        Aucune transaction enregistrée
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
