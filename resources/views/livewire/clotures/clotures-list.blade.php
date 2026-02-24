<div class="p-6 bg-white rounded-lg shadow-sm">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Journal de Caisse</h2>
    </div>

    @if (session()->has('success'))
        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">{{ session('success') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">{{ session('error') }}</div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-600 uppercase text-sm leading-normal border-b">
                    <th class="py-3 px-6">Date</th>
                    <th class="py-3 px-6">Statut</th>
                    <th class="py-3 px-6 text-right">Report USD</th>
                    <th class="py-3 px-6 text-right">Report CDF</th>
                    <th class="py-3 px-6 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                @foreach($clotures as $cloture)
                <tr class="border-b border-gray-200 hover:bg-gray-50">
                    <td class="py-3 px-6 font-medium">{{ \Carbon\Carbon::parse($cloture->date_cloture)->format('d/m/Y') }}</td>
                    <td class="py-3 px-6">
                        @if($cloture->statut === 'ouverte')
                            <span class="bg-green-200 text-green-700 py-1 px-3 rounded-full text-xs">Ouverte</span>
                        @else
                            <span class="bg-gray-200 text-gray-700 py-1 px-3 rounded-full text-xs">Clôturée</span>
                        @endif
                    </td>
                    <td class="py-3 px-6 text-right font-mono">${{ number_format($cloture->report_coffre_usd, 2) }}</td>
                    <td class="py-3 px-6 text-right font-mono">{{ number_format($cloture->report_coffre_cdf, 2) }} FC</td>
                    <td class="py-3 px-6 text-center flex justify-center space-x-2">
                        <a href="{{ route('clotures.show', $cloture->id) }}" wire:navigate class="text-blue-500 hover:text-blue-700" title="Voir les détails">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">
            {{ $clotures->links() }}
        </div>
    </div>
</div>