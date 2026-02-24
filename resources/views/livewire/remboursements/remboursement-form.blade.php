<div class="max-w-4xl mx-auto py-8">
    @php
        $date_credit = auth()->user()->journee_ouverte->date_cloture ?? 'Attention! Pas de journée ouvrable';
    @endphp

    @if (session()->has('error'))
        <div class="mb-4 mx-4 mt-4 rounded-lg bg-green-100 px-4 py-3 text-green-800 text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Alertes d'erreurs globales --}}
    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded shadow-sm">
            <div class="flex">
                <div class="ml-3">
                    <p class="text-sm text-red-700 font-bold">Veuillez corriger les erreurs suivantes :</p>
                    <ul class="list-disc list-inside text-xs text-red-600 mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="bg-blue-700 px-6 py-4">
            <h3 class="text-white font-bold text-lg">Nouveau Remboursement - {{ $credit->user->name }}</h3>
            <p class="text-blue-100 text-sm italic">Crédit Ref: {{ $credit->numero_credit }}</p>
            <h3 class="text-white font-bold text-lg"> 📅 Date d'opération : {{ Carbon\Carbon::parse($date_credit)->format('d/m/Y') }}</h3>
        </div>

        <form wire:submit.prevent="save" class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="col-span-1">
                <label class="block text-sm font-medium text-gray-700">Montant à payer ({{ $credit->monnaie }})</label>
                <input type="number" step="0.01" wire:model="montant" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('montant') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Mode de paiement</label>
                <select wire:model="mode_paiement" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <option value="cash">Espèces (Cash)</option>
                    <option value="mpesa">M-Pesa</option>
                    <option value="airtel">Airtel Money</option>
                    <option value="banque">Virement Bancaire</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Perçu par l'agent</label>
                <select wire:model="agent_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @foreach($agents as $agent)
                        <option value="{{ $agent->id }}">{{ $agent->user->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-span-full flex justify-end space-x-3 pt-4 border-t">
                <button type="button" class="text-gray-600 hover:underline">Annuler</button>
                <button type="submit" wire:loading.attr="disabled" class="bg-blue-600 text-white px-6 py-2 rounded shadow hover:bg-blue-700">
                    <span wire:loading class="mr-2">...</span>
                    Valider le remboursement
                </button>
            </div>
        </form>
    </div>
</div>