<div class="max-w-xl mx-auto bg-white p-6 rounded shadow">

    <h2 class="text-xl font-bold mb-4">Détails du type de dépense</h2>

    <div class="space-y-3">
        <div>
            <strong>Nom :</strong>
            <div>{{ $typesDepense->nom }}</div>
        </div>

        <div>
            <strong>Code comptable :</strong>
            <div>{{ $typesDepense->code_comptable }}</div>
        </div>

        <div>
            <strong>Enregistré par :</strong>
            <div>{{ $typesDepense->created_by }}</div>
        </div>

        <div>
            <strong>Enregistré le :</strong>
            <div>{{ $typesDepense->created_at }}</div>
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('types-depense.index') }}"
           class="text-blue-600">
            ← Retour à la liste
        </a>
    </div>
</div>
