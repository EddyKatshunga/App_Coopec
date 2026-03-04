<div class="p-6 bg-white shadow rounded-lg">

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <h3 class="text-xl font-bold mb-4">Octroi d'un crédit pour {{ $membre->user?->name }}</h3>
    
    <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium">Agent</label>
            <select wire:model="agent_id" class="w-full border-gray-300 rounded-md shadow-sm">
                <option value="">Sélectionner l'agent</option>
                @foreach($agents as $agent)
                    <option value="{{ $agent->id }}">{{ $agent->nom }}</option>
                @endforeach
            </select>
            @error('agent_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium">Zone</label>
            <select wire:model="zone_id" class="w-full border-gray-300 rounded-md shadow-sm">
                <option value="">Sélectionner la zone</option>
                @foreach($zones as $zone)
                    <option value="{{ $zone->id }}">{{ $zone->nom }}</option>
                @endforeach
            </select>
            @error('zone_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium">Monnaie (*)</label>
            <select wire:model.live="monnaie" class="w-full border-gray-300 rounded-md shadow-sm">
                <option value="">Sélectionner la monnaie</option>
                <option value="CDF">CDF</option>
                <option value="USD">USD</option>
            </select>
            @error('monnaie') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        {{-- Date de fin suggérée --}}
        <div>
            <label class="block text-sm font-medium">Date de fin suggérée</label>
            <input type="date" wire:model="date_fin" wire:key="date-fin-field-{{ $duree }}-{{ $unite_temps }}"
                class="w-full border-gray-300 rounded-md shadow-sm">
            <p class="text-xs text-gray-500">Modifiable selon l'accord avec le membre</p>
            @error('date_fin') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <hr class="col-span-full my-2">

        <div class="bg-blue-50 p-3 rounded-lg border border-blue-100">
            <label class="block text-sm font-medium text-blue-800">Capital</label>
            <input type="number" wire:model.live="capital" class="w-full border-gray-300 rounded-md shadow-sm">
            <p class="text-xs mt-1 text-gray-600 italic">{{ money_to_words($capital, $monnaie) }}</p>
            @error('capital') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="bg-green-50 p-3 rounded-lg border border-green-100">
            <label class="block text-sm font-medium text-green-800">Intérêt</label>
            <input type="number" wire:model.live="interet" class="w-full border-gray-300 rounded-md shadow-sm">
            <p class="text-xs mt-1 text-gray-600 italic">{{ money_to_words($interet, $monnaie) }}</p>
            @error('interet') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="bg-gray-100 p-3 rounded-lg border border-gray-200">
            <label class="block text-sm font-medium text-gray-800">Total à rembourser</label>
            <div class="text-lg font-bold py-1 border-b border-gray-300">
                {{ number_format((float)$capital + (float)$interet, 2) }} <span class="text-xs">{{ $monnaie }}</span>
            </div>
            <p class="text-xs mt-1 text-gray-600 italic">
                {{ money_to_words((float)$capital + (float)$interet, $monnaie) }}
            </p>
        </div>

        <div>
            <label class="block text-sm font-medium">Unité de temps</label>
            <select wire:model.live="unite_temps" class="w-full border-gray-300 rounded-md shadow-sm">
                <option value="jour">Jour (hors Dimanche)</option>
                <option value="semaine">Semaine</option>
                <option value="mois">Mois</option>
                <option value="annee">Année</option>
            </select>
            @error('unite_temps') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium">Durée (Échéances)</label>
            <input type="number" wire:model.live="duree" class="w-full border-gray-300 rounded-md shadow-sm">
            @error('duree') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium">Taux Pénalité Journalier (%)</label>
            <input type="number" step="0.01" wire:model="taux_penalite_journalier" class="w-full border-gray-300 rounded-md shadow-sm">
        </div>

        <hr class="col-span-full my-2">

        <div>
            <label class="block text-sm font-medium">Nom du Garant (*)</label>
            <input type="text" wire:model="garant_nom" class="w-full border-gray-300 rounded-md shadow-sm">
            @error('garant_nom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium">Téléphone du Garant</label>
            <input type="text" wire:model="garant_telephone" class="w-full border-gray-300 rounded-md shadow-sm">
            @error('garant_telephone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium">Adresse du Garant</label>
            <input type="text" wire:model="garant_adresse" class="w-full border-gray-300 rounded-md shadow-sm">
            @error('garant_adresse') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="col-span-full">
            <label class="block text-sm font-medium">Observation / Note de négociation</label>
            <textarea wire:model="observation" rows="2" class="w-full border-gray-300 rounded-md shadow-sm"></textarea>
            @error('observation') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="col-span-full mt-4" x-data="{ open: false }">
            <button type="button" @click="open = !open" class="flex items-center text-sm font-bold text-blue-600 hover:underline">
                <span x-show="!open">➕ Afficher l'échéancier prévisionnel</span>
                <span x-show="open">➖ Masquer l'échéancier prévisionnel</span>
            </button>

            <div x-show="open" x-transition class="mt-4" wire:key="echeancier-{{ $duree }}-{{ $unite_temps }}">
                @include('livewire.credits.partials.echeancier')
            </div>
        </div>

        <div class="col-span-full mt-6 border-t pt-4">
            <button type="submit" wire:loading.attr="disabled" class="bg-blue-600 text-white px-6 py-2 rounded-lg shadow font-bold hover:bg-blue-700 disabled:opacity-50 transition">
                <span wire:loading.remove>🚀 Enregistrer le crédit</span>
                <span wire:loading>Traitement en cours...</span>
            </button>
        </div>
    </form>
</div>