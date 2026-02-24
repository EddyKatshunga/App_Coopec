<div class="max-w-2xl mx-auto p-6 bg-white shadow-md rounded-lg">
    <div class="border-b pb-4 mb-4 flex justify-between items-center">
        <h2 class="text-xl font-bold">Détails de la Dépense #{{ $depense->id }}</h2>
        <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm">Sortie de fonds</span>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <p class="text-gray-500">Libellé</p>
            <p class="font-semibold">{{ $depense->libelle }}</p>
        </div>
        <div>
            <p class="text-gray-500">Montant</p>
            <p class="font-bold text-lg">{{ number_format($depense->montant, 2) }} {{ $depense->monnaie }}</p>
        </div>
        <div>
            <p class="text-gray-500">Type</p>
            <p>{{ $depense->typeDepense->nom ?? 'Non défini' }}</p>
        </div>
        <div>
            <p class="text-gray-500">Bénéficiaire</p>
            <p>{{ $depense->beneficiaire->nom ?? 'Anonyme' }}</p>
        </div>
        <div class="col-span-2">
            <p class="text-gray-500">Description</p>
            <p class="italic text-gray-700">{{ $depense->description ?? 'Aucune description' }}</p>
        </div>
    </div>

    <div class="mt-8">
        <a href="{{ route('depenses.index') }}" class="text-gray-600 hover:underline" wire:navigate>← Retour à la liste</a>
    </div>
</div>