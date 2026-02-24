<div class="p-6 bg-white rounded shadow">

    <div class="flex justify-between mb-4">
        <h2 class="text-xl font-bold">Liste des types de dépenses</h2>
        <a href="{{ route('types-depense.create') }}" wire:navigate
           class="bg-green-600 text-white px-4 py-2 rounded">
            Nouveau
        </a>
    </div>

    @if(session()->has('success'))
        <div class="bg-green-100 text-green-700 p-2 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    <table class="w-full border">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2 border">Nom</th>
                <th class="p-2 border">Code comptable</th>
                <th class="p-2 border">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($typesDepenses as $type)
                <tr>
                    <td class="p-2 border">{{ $type->nom }}</td>
                    <td class="p-2 border">{{ $type->code_comptable }}</td>
                    <td class="p-2 border flex gap-2">
                        <a href="{{ route('types-depense.show', $type) }}"
                           class="text-blue-600">Voir</a>

                        <a href="{{ route('types-depense.edit', $type) }}"
                           class="text-yellow-600">Modifier</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $typesDepenses->links() }}
    </div>
</div>
