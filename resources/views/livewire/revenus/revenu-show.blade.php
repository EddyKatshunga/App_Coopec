<div class="max-w-2xl mx-auto p-6 bg-white shadow-lg rounded-lg border-l-8 border-green-500">
    <div class="border-b pb-4 mb-6 flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800 italic uppercase">Fiche de Revenu</h2>
        <span class="text-gray-400 text-xs italic">Ref: {{ $revenu->reference ?? 'N/A' }}</span>
    </div>

    <div class="space-y-4">
        <div class="flex justify-between border-b border-dashed py-2">
            <span class="text-gray-500">Libellé :</span>
            <span class="font-bold text-gray-800">{{ $revenu->libelle }}</span>
        </div>
        <div class="flex justify-between border-b border-dashed py-2">
            <span class="text-gray-500">Montant Reçu :</span>
            <span class="font-bold text-green-600 text-xl">{{ number_format($revenu->montant, 2) }} {{ $revenu->monnaie }}</span>
        </div>
        <div class="flex justify-between border-b border-dashed py-2">
            <span class="text-gray-500">Catégorie :</span>
            <span>{{ $revenu->typeRevenu->nom ?? 'Standard' }}</span>
        </div>
        <div class="flex justify-between border-b border-dashed py-2">
            <span class="text-gray-500">Date Opération :</span>
            <span>{{ $revenu->date_operation }}</span>
        </div>
        <div class="py-4 text-gray-700">
            <p class="text-xs text-gray-400 uppercase font-bold mb-1">Observation :</p>
            <p class="bg-gray-50 p-3 rounded italic">{{ $revenu->description ?: 'Aucune note particulière.' }}</p>
        </div>
    </div>

    <div class="mt-10 flex justify-between">
        <a href="{{ route('revenus.index') }}" class="text-green-600 font-bold hover:underline">← Retour à la liste</a>
        <button onclick="window.print()" class="text-gray-400 hover:text-black">Imprimer</button>
    </div>
</div>