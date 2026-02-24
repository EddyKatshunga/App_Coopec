<div class="max-w-2xl mx-auto py-8">
    <div class="bg-white shadow rounded-lg border-t-4 {{ $photo ? 'border-amber-500' : 'border-blue-600' }}">
        <div class="px-6 py-4 border-b">
            <h2 class="text-xl font-bold text-gray-800">
                {{ $photo ? 'Modifier la légende' : 'Ajouter une photo pour ' . $user->name }}
            </h2>
        </div>

        <form wire:submit.prevent="save" class="p-6 space-y-6">
            @if(!$photo)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sélectionner une image</label>
                    <input type="file" wire:model="file" 
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 @error('file') border-red-300 @enderror">
                    
                    @error('file') 
                        <span class="text-red-600 text-xs mt-1 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                            {{ $message }}
                        </span> 
                    @enderror
                    
                    @if ($file)
                        <div class="mt-4 p-2 border-2 border-dashed border-blue-200 rounded-lg inline-block">
                            <img src="{{ $file->temporaryUrl() }}" class="h-40 w-40 object-cover rounded-md shadow-sm">
                            <p class="text-[10px] text-blue-500 text-center mt-1">Aperçu du fichier</p>
                        </div>
                    @endif
                </div>
            @else
                <div class="flex flex-col items-center p-4 bg-gray-50 rounded-lg border">
                    <img src="{{ $photo->url }}" class="h-48 rounded-lg shadow-md border-4 border-white">
                    <p class="text-xs text-gray-400 mt-2">Fichier actuel : {{ $photo->original_name }}</p>
                </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700">Légende (Caption)</label>
                <input type="text" wire:model="caption" 
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('caption') border-red-300 @enderror" 
                    placeholder="Une brève description...">
                
                @error('caption') 
                    <span class="text-red-600 text-xs mt-1">{{ $message }}</span> 
                @enderror
            </div>

            <div class="flex items-center p-3 bg-gray-50 rounded-md">
                <input type="checkbox" wire:model="is_profile" id="is_profile" 
                    class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="is_profile" class="ml-2 block text-sm text-gray-900 font-medium">
                    Définir comme photo de profil
                </label>
            </div>

            <div class="flex justify-end pt-4 border-t space-x-3">
                <a href="{{ route('photos.index', ['user' => $user->id]) }}" 
                   class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 transition">
                    Annuler
                </a>
                
                <button type="submit" 
                    wire:loading.attr="disabled" 
                    class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-bold rounded-md shadow-sm text-white transition {{ $photo ? 'bg-amber-600 hover:bg-amber-700' : 'bg-blue-600 hover:bg-blue-700' }} disabled:opacity-50">
                    
                    <span wire:loading wire:target="save" class="mr-2">
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                    
                    {{ $photo ? 'Mettre à jour' : 'Téléverser la photo' }}
                </button>
            </div>
        </form>
    </div>
</div>