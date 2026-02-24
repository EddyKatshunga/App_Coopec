<div class="max-w-4xl mx-auto py-6">
    <div class="bg-black rounded-lg overflow-hidden shadow-2xl">
        <div class="p-2 flex justify-between items-center bg-gray-900 text-white text-sm">
            <span>{{ $photo->original_name }}</span>
            <a href="{{ route('photos.index', $photo->user_id) }}" class="hover:text-gray-400">Fermer ✕</a>
        </div>
        
        <img src="{{ $photo->url }}" class="w-full max-h-[70vh] object-contain mx-auto">

        <div class="p-6 bg-white">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">{{ $photo->caption ?? 'Sans légende' }}</h3>
                    <p class="text-sm text-gray-500 italic">Ajoutée le {{ $photo->created_at->format('d/m/Y à H:i') }}</p>
                </div>
                <div class="text-right text-xs text-gray-400">
                    <p>Format: {{ $photo->mime_type }}</p>
                    <p>Taille: {{ round($photo->size / 1024, 2) }} KB</p>
                </div>
            </div>
        </div>
    </div>
</div>