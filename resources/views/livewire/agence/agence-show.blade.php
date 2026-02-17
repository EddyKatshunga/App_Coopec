<div class="max-w-7xl mx-auto py-10 px-6">

    {{-- Header --}}
    <div class="flex justify-between items-start mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                {{ $agence->nom }}
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Code: {{ $agence->code }} • {{ $agence->ville }}, {{ $agence->pays }}
            </p>
        </div>

        <a href="{{ route('agences.index') }}"
           class="text-gray-600 hover:text-gray-900 font-medium">
            ← Retour
        </a>
    </div>

    {{-- Cartes Soldes --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">

        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
            <p class="text-sm text-gray-500">Solde actuel coffre</p>
            <p class="text-2xl font-bold mt-2
                      {{ $agence->solde_actuel_coffre < 0 ? 'text-red-600' : 'text-gray-800' }}">
                {{ number_format($agence->solde_actuel_coffre, 2, ',', ' ') }} CDF
            </p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
            <p class="text-sm text-gray-500">Solde total épargne</p>
            <p class="text-2xl font-bold mt-2 text-gray-800">
                {{ number_format($agence->solde_actuel_epargne, 2, ',', ' ') }} CDF
            </p>
        </div>

    </div>

    {{-- Directeur --}}
    <div class="bg-white shadow-xl rounded-2xl p-6 border border-gray-100 mb-10">

        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-800">
                Direction
            </h3>

            <button wire:click="ouvrirModal"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white
                           rounded-xl font-semibold shadow transition">
                Changer le directeur
            </button>
        </div>

        @if($agence->directeur)
            <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                <p class="text-sm text-green-800">
                    Directeur actuel :
                    <strong>{{ $agence->directeur->user->name }}</strong>
                </p>
            </div>
        @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                <p class="text-sm text-yellow-800">
                    Aucun directeur assigné.
                </p>
            </div>
        @endif

    </div>

    {{-- Liste Agents --}}
    <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden">

        <div class="px-6 py-4 border-b bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">
                Agents de l’agence
            </h3>
        </div>

        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-6 py-4 text-left">Nom</th>
                    <th class="px-6 py-4 text-left">Email</th>
                    <th class="px-6 py-4 text-left">Rôle</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($agence->agents as $agent)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-800">
                            {{ $agent->user->name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $agent->user->email }}
                        </td>
                        <td class="px-6 py-4">
                            @foreach($agent->user->roles as $role)
                                <span class="inline-flex px-3 py-1 text-xs font-semibold
                                             bg-indigo-100 text-indigo-700 rounded-full mr-1">
                                    {{ ucfirst($role->name) }}
                                </span>
                            @endforeach
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                            Aucun agent enregistré.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>

    {{-- MODAL --}}
    @if($showModal)
    <div class="fixed inset-0 flex items-center justify-center z-50">

        <div class="absolute inset-0 bg-black opacity-50"></div>

        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-8 relative z-10">

            <h3 class="text-xl font-bold text-gray-800 mb-4">
                Confirmation du changement de directeur
            </h3>

            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                <p class="text-sm text-red-800">
                    ⚠ Cette opération est sensible.
                    <br><br>
                    • L’ancien directeur deviendra <strong>Superviseur</strong>.
                    <br>
                    • Le nouvel agent sélectionné deviendra <strong>Directeur</strong>.
                </p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Sélectionner le nouvel directeur
                </label>

                <select wire:model="nouveauDirecteurId"
                        class="w-full rounded-xl border-gray-300 shadow-sm
                               focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <option value="">-- Choisir un agent --</option>
                    @foreach($agence->agents as $agent)
                        <option value="{{ $agent->id }}">
                            {{ $agent->user->name }}
                        </option>
                    @endforeach
                </select>

                @error('nouveauDirecteurId')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="flex items-center space-x-2">
                    <input type="checkbox"
                           wire:model="confirmation"
                           class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500">
                    <span class="text-sm text-gray-700">
                        Je confirme vouloir effectuer cette opération.
                    </span>
                </label>

                @error('confirmation')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-3">
                <button wire:click="$set('showModal', false)"
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300
                               rounded-xl font-medium">
                    Annuler
                </button>

                <button wire:click="changerDirecteur"
                        class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white
                               rounded-xl font-semibold shadow transition">
                    Confirmer
                </button>
            </div>

        </div>
    </div>
    @endif

</div>
