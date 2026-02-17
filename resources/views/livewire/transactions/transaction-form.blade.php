<div
    x-data="{
        monnaie: @entangle('monnaie'),
        typeOp: @entangle('type_transaction'),
        soldeCDF: @entangle('solde_cdf'),
        soldeUSD: @entangle('solde_usd'),
        montant: @entangle('montant'),

        format(v) {
            return v !== null ? new Intl.NumberFormat().format(v) : '—';
        },

        get soldeInsuffisant() {
            return this.typeOp === 'RETRAIT' &&
                (
                    (this.monnaie === 'CDF' && this.montant > this.soldeCDF) ||
                    (this.monnaie === 'USD' && this.montant > this.soldeUSD)
                );
        }
    }"
    class="relative max-w-5xl mx-auto p-8 space-y-8
           rounded-3xl bg-white/60 backdrop-blur-xl
           shadow-2xl"
>
    {{-- ================= MESSAGE SUCCÈS ================= --}}
    @if (session()->has('success'))
        <div class="mb-6 rounded-lg bg-green-100 px-4 py-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif
    
    <div>
        <h3>Enregistrer un {{ $type_transaction }}</h3>
    </div>
    {{-- ================= HEADER : DATE + AGENCE ================= --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        {{-- DATE --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                📅 Date de la transaction
            </label>
            <input
                type="date"
                wire:model="date_transaction"
                max="{{ now()->toDateString() }}"
                class="w-full px-4 py-3 rounded-xl bg-white/70 backdrop-blur
                       border border-gray-200 focus:ring-2 focus:ring-blue-500"
            >
            @error('date_transaction')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- AGENCE --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                🏢 Agence
                <span class="text-xs text-orange-600 font-medium">
                    (opération sensible)
                </span>
            </label>
            <select
                wire:model="agence_id"
                class="w-full px-4 py-3 rounded-xl bg-white/70 backdrop-blur
                       border border-gray-200 focus:ring-2 focus:ring-blue-500"
            >
                @foreach($agences as $agence)
                    <option value="{{ $agence->id }}">{{ $agence->nom }}</option>
                @endforeach
            </select>
            @error('agence_id')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

    </div>

    {{-- ================= COMPTE (COMBOBOX RECHERCHE) ================= --}}
    <div class="relative">
        <label class="block text-sm font-semibold text-gray-700 mb-1">
            💳 Compte / Membre
        </label>

        <input
            type="text"
            wire:model.live.debounce.300ms="searchCompte"
            placeholder="Numéro de compte ou nom du membre"
            class="w-full px-4 py-3 rounded-xl bg-white/70 backdrop-blur
                   border border-gray-200 focus:ring-2 focus:ring-blue-500"
        >

        @error('compte_id')
            <p class="text-sm text-red-600 mt-1">
                Veuillez sélectionner un compte valide.
            </p>
        @enderror

        @if($comptes->isNotEmpty() && !$compte_id)
            <ul
                class="absolute z-30 mt-2 w-full max-h-64 overflow-auto
                       rounded-2xl bg-white shadow-xl border"
            >
                @foreach($comptes as $c)
                    <li
                        wire:click="selectCompte({{ $c->id }})"
                        class="px-5 py-3 cursor-pointer
                               hover:bg-blue-50 transition"
                    >
                        <div class="font-semibold">
                            {{ $c->numero_compte }}
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ $c->membre->nom }}
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    {{-- ================= SOLDES ================= --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <div class="p-6 rounded-2xl bg-white/70 backdrop-blur shadow-inner">
            <span class="text-xs text-gray-500 uppercase">Solde CDF</span>
            <span class="block mt-2 text-2xl font-mono tabular-nums"
                  x-text="format(soldeCDF)"></span>
        </div>

        <div class="p-6 rounded-2xl bg-white/70 backdrop-blur shadow-inner">
            <span class="text-xs text-gray-500 uppercase">Solde USD</span>
            <span class="block mt-2 text-2xl font-mono tabular-nums"
                  x-text="format(soldeUSD)"></span>
        </div>

    </div>

    {{-- ================= MONTANT ================= --}}
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">
            💰 Montant
        </label>

        <input
            type="number"
            step="0.01"
            wire:model.live="montant"
            class="w-full px-4 py-3 rounded-xl bg-white/70 backdrop-blur
                   border border-gray-200 focus:ring-2 focus:ring-blue-500
                   text-right font-mono tabular-nums"
            :class="soldeInsuffisant ? 'ring-2 ring-red-500' : ''"
        >

        <p
            x-show="soldeInsuffisant"
            x-transition
            class="text-sm text-red-600 mt-1"
        >
            🚨 Solde insuffisant pour ce retrait
        </p>

        @error('montant')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- ================= MONNAIE ================= --}}
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">
            💱 Monnaie
        </label>

        <div class="flex gap-4">
            <button
                type="button"
                @click="monnaie = 'CDF'"
                :class="monnaie === 'CDF'
                    ? 'px-6 py-2 rounded-xl bg-blue-600 text-white font-semibold'
                    : 'px-6 py-2 rounded-xl bg-gray-200 hover:bg-gray-300'"
            >
                CDF
            </button>

            <button
                type="button"
                @click="monnaie = 'USD'"
                :class="monnaie === 'USD'
                    ? 'px-6 py-2 rounded-xl bg-blue-600 text-white font-semibold'
                    : 'px-6 py-2 rounded-xl bg-gray-200 hover:bg-gray-300'"
            >
                USD
            </button>
        </div>
    </div>

    {{-- ================= AGENT COLLECTEUR ================= --}}
    <div x-show="typeOp === 'DEPOT'" x-transition>
        <label class="block text-sm font-semibold text-gray-700 mb-1">
            👤 Agent collecteur (optionnel)
        </label>
        <select
            wire:model="agent_collecteur_id"
            class="w-full px-4 py-3 rounded-xl bg-white/70 backdrop-blur
                   border border-gray-200 focus:ring-2 focus:ring-blue-500"
        >
            <option value="">— Aucun —</option>
            @foreach($agents as $agent)
                <option value="{{ $agent->id }}">{{ $agent->nom }}</option>
            @endforeach
        </select>
    </div>

    {{-- ================= BOUTON ================= --}}
    <button
        wire:click="submit"
        wire:loading.attr="disabled"
        wire:target="submit"
        class="w-full py-4 rounded-2xl
               bg-gradient-to-r from-blue-600 to-indigo-600
               text-white font-semibold tracking-wide
               shadow-lg
               hover:scale-[1.01] active:scale-95
               disabled:opacity-50
               transition-all"
    >
        <span wire:loading.remove>
            ✔ Enregistrer la transaction
        </span>
        <span wire:loading>
            ⏳ Traitement en cours…
        </span>
    </button>

</div>
