<div class="p-6 bg-white rounded-lg shadow-sm">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Journal de Caisse</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
        @if(auth()->user()->can('view-all-agencies'))
        <div>
            <label class="block text-sm font-medium text-gray-700">Agence</label>
            <select wire:model.live="agenceId" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @foreach($agences as $agence)
                    <option value="{{ $agence->id }}">{{ $agence->nom }}</option>
                @endforeach
            </select>
        </div>
        @endif

        <div>
            <label class="block text-sm font-medium text-gray-700">Du</label>
            <input type="date" wire:model.live="dateDebut" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Au</label>
            <input type="date" wire:model.live="dateFin" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        
        <div class="flex items-end">
             <button wire:click="$set('dateDebut', null); $set('dateFin', null);" class="text-sm text-red-600 hover:underline">Réinitialiser dates</button>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">{{ session('success') }}</div>
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
                @forelse($clotures as $cloture)
                <tr class="border-b border-gray-200 hover:bg-gray-50">
                    <td class="py-3 px-6 font-medium">{{ $cloture->date_cloture->format('d/m/Y') }}</td>
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
                        <a href="{{ route('clotures.show', $cloture->id) }}" wire:navigate class="text-blue-500 hover:text-blue-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-6 text-center text-gray-500">Aucune clôture trouvée pour ces critères.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">
            {{ $clotures->links() }}
        </div>
    </div>
</div>