<div class="max-w-2xl mx-auto py-8">
    <div class="bg-white shadow rounded-lg border-t-4 {{ $zone && $zone->exists ? 'border-orange-500' : 'border-indigo-600' }}">
        
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800">
                @if($zone && $zone->exists)
                    Modifier la Zone : <span class="text-orange-600">{{ $zone->nom }}</span>
                @else
                    Nouvelle Zone pour {{ $agence->nom }}
                @endif
            </h2>
            @if($zone && $zone->exists)
                <span class="text-[10px] bg-orange-100 text-orange-700 px-2 py-1 rounded-full uppercase font-black">Mode Édition</span>
            @endif
        </div>

        <form wire:submit.prevent="save" class="p-6 space-y-5">
            
            <div class="bg-gray-50 p-3 rounded-md border border-gray-100">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Agence de rattachement</label>
                <p class="text-sm font-semibold text-gray-700">{{ $agence->nom }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Code de la Zone</label>
                <input type="text" wire:model="code" 
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" 
                       placeholder="Ex: Z-EST-01">
                @error('code') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Nom de la Zone</label>
                <input type="text" wire:model="nom" 
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" 
                       placeholder="Ex: Secteur Nord-Kivu">
                @error('nom') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Assigner un Gérant de Zone</label>
                <select wire:model="gerant_id" 
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">-- Sélectionner un agent éligible --</option>
                    @foreach($gerants as $gerant)
                        <option value="{{ $gerant->id }}">{{ $gerant->nom }}</option>
                    @endforeach
                </select>
                <p class="mt-2 text-xs text-gray-400 italic">
                    <svg class="inline w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                    Seuls les agents (non chefs) de l'agence <strong>{{ $agence->nom }}</strong> sont listés.
                </p>
                @error('gerant_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end pt-4 border-t space-x-3">
                <a href="{{ route('agences.zones.index', $agence->id) }}" 
                   class="px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-md transition">
                    Annuler
                </a>
                
                <button type="submit" 
                        wire:loading.attr="disabled"
                        class="px-6 py-2 text-sm font-bold text-white rounded shadow-sm transition flex items-center {{ $zone && $zone->exists ? 'bg-orange-600 hover:bg-orange-700' : 'bg-indigo-600 hover:bg-indigo-700' }}">
                    
                    <span wire:loading wire:target="save" class="mr-2">
                        <svg class="animate-spin h-4 w-4 text-white" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>

                    {{ $zone && $zone->exists ? 'Mettre à jour la Zone' : 'Enregistrer la Zone' }}
                </button>
            </div>
        </form>
    </div>
</div>