<div>
    @can('agence.view.all')
        <div class="bg-white p-4 rounded shadow">
            <label class="block text-sm font-semibold mb-1">
                Agence active
            </label>

            <select wire:model="agenceId" class="w-full border rounded p-2">
                @foreach($agences as $agence)
                    <option value="{{ $agence->id }}">
                        {{ $agence->nom }}
                    </option>
                @endforeach
            </select>
        </div>
    @endcan
</div>
