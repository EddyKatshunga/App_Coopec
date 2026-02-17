<div class="max-w-5xl mx-auto p-6 space-y-8">
    <a href="{{ route('credits.index') }}">Liste des crédits</a>
    <h1 class="text-2xl font-bold text-gray-800">
        ➕ Octroi d'un Crédit pour {{ $membre->nom }}
    </h1>

    @if (session()->has('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-6">

        {{-- ================= INFORMATIONS GÉNÉRALES ================= --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="label">Date crédit</label>
                <input type="date" wire:model="date_credit" class="input">
                @error('date_credit') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="label">Zone</label>
                <select wire:model="zone_id" class="input">
                    <option value="">-- choisir --</option>
                    @foreach($zones as $zone)
                        <option value="{{ $zone->id }}">{{ $zone->nom }}</option>
                    @endforeach
                </select>
                @error('zone_id') <span class="error">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- ================= CONDITIONS FINANCIÈRES ================= --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="label">Capital</label>
                <input type="number" wire:model="capital" class="input">
                @error('capital') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="label">Intérêt</label>
                <input type="number" wire:model="interet" class="input">
                @error('interet') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="label">Taux pénalité / jour (%)</label>
                <input type="number" step="0.01" wire:model="taux_penalite_journalier" class="input">
                @error('taux_penalite_journalier') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="label">Durée</label>
                <div class="flex gap-2">
                    <input type="number" wire:model="duree" class="input w-1/2">
                    <select wire:model="unite_temps" class="input w-1/2">
                        <option value="jour">Jour</option>
                        <option value="semaine">Semaine</option>
                        <option value="mois">Mois</option>
                        <option value="annee">Année</option>
                    </select>
                </div>
                @error('duree') <span class="error">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- ================= DATE DE FIN ================= --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="label">Date de fin confirmée *</label>
                <input 
                    type="date" 
                    wire:model="date_fin_confirmee" 
                    class="input"
                >
                @error('date_fin_confirmee') <span class="error">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- ================= GARANT ================= --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="label">Nom du garant</label>
                <input type="text" wire:model="garant_nom" class="input">
                @error('garant_nom') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="label">Adresse du garant</label>
                <input type="text" wire:model="garant_adresse" class="input">
            </div>

            <div>
                <label class="label">Téléphone du garant</label>
                <input type="text" wire:model="garant_telephone" class="input">
            </div>
        </div>

        {{-- ================= ACTIONS ================= --}}
        <div class="flex justify-end gap-4">
            <a href="{{ route('credits.index') }}"
               class="px-4 py-2 rounded border text-gray-600">
                Annuler
            </a>

            <button type="submit"
                class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Enregistrer le crédit
            </button>
        </div>
    </form>
</div>