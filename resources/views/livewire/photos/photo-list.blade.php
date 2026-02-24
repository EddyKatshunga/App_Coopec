<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Galerie de {{ $user->name }}</h2>
        <a href="{{ route('photos.create', $user->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded">Ajouter une Photo</a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        @foreach($photos as $photo)
            <div class="relative group bg-white p-2 rounded-lg shadow-sm border">
                <img src="{{ $photo->url }}" class="h-40 w-full object-cover rounded shadow-inner">
                
                @if($photo->is_profile)
                    <span class="absolute top-4 left-4 bg-green-500 text-white text-[10px] px-2 py-1 rounded-full font-bold shadow-sm">Profil</span>
                @endif

                <div class="mt-2 flex justify-between">
                    <a href="{{ route('photos.show', $photo->id) }}" class="text-xs text-gray-500 hover:text-blue-600">Voir</a>
                    <div class="flex space-x-2">
                         <a href="{{ route('photos.edit', [$user->id, $photo->id]) }}" class="text-xs text-amber-600">Éditer</a>
                         <button wire:click="delete({{ $photo->id }})" wire:confirm="Supprimer ?" class="text-xs text-red-600">Suppr.</button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-6">{{ $photos->links() }}</div>
</div>