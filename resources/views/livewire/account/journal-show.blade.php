<div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-5xl mx-auto">
        <a href="{{ route('journal.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 mb-6">
            <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Retour au journal
        </a>

        <div class="bg-white rounded-t-xl border border-gray-200 p-6 border-b-0">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-1">{{ $entry->libelle }}</h1>
                    <div class="text-sm text-gray-500 font-mono">Réf: {{ $entry->uuid }}</div>
                </div>
                <div class="text-right">
                    <div class="text-lg font-bold text-gray-900">{{ $entry->date_operation?->format('d/m/Y') }}</div>
                    <div class="text-sm text-gray-500">{{ $entry->agence->nom ?? 'Siège' }}</div>
                </div>
            </div>

            @if($this->sourceOperation)
                <div class="mt-6 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-50 text-indigo-700">
                    Source : {{ $this->sourceOperation['type'] }} #{{ $this->sourceOperation['id'] }}
                </div>
            @endif
        </div>

        <div class="bg-white border border-gray-200 rounded-b-xl overflow-hidden shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Compte</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Débit</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Crédit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($entry->lines as $line)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-mono text-sm font-bold text-indigo-600">{{ $line->account->numero }}</div>
                                <div class="text-sm text-gray-600">{{ $line->account->nom }}</div>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap font-medium text-gray-900">
                                {{ $line->debit > 0 ? number_format($line->debit, 2, ',', ' ') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap font-medium text-gray-900">
                                {{ $line->credit > 0 ? number_format($line->credit, 2, ',', ' ') : '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 border-t-2 border-gray-300">
                    <tr>
                        <td class="px-6 py-4 text-right font-bold text-gray-700 uppercase text-sm">Totaux de contrôle</td>
                        <td class="px-6 py-4 text-right font-bold text-gray-900">
                            {{ number_format($entry->lines->sum('debit'), 2, ',', ' ') }}
                        </td>
                        <td class="px-6 py-4 text-right font-bold text-gray-900">
                            {{ number_format($entry->lines->sum('credit'), 2, ',', ' ') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
            
            @if($entry->lines->sum('debit') !== $entry->lines->sum('credit'))
                <div class="p-4 bg-red-50 text-red-700 text-sm font-semibold text-center border-t border-red-200">
                    ⚠️ Attention : Cette écriture est déséquilibrée.
                </div>
            @endif
        </div>
    </div>
</div>