<div class="max-w-5xl mx-auto p-6 space-y-8">
    @php
        $date_credit = auth()->user()->journee_ouverte->date_cloture ?? now()->format('Y-m-d');
    @endphp

    @if (session()->has('error'))
        <div class="mb-4 mx-4 mt-4 rounded-lg bg-green-100 px-4 py-3 text-green-800 text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">
            ➕ Octroi d'un Crédit pour <span class="text-blue-600">{{ $membre->nom }}</span>
        </h1>
        <div class="text-sm font-medium bg-gray-100 px-3 py-1 rounded-full text-gray-600 border border-gray-200">
            📅 Date d'opération : {{ Carbon\Carbon::parse($date_credit)->format('d/m/Y') }}
        </div>
    </div>

    {{-- Alertes d'erreurs globales --}}
    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded shadow-sm">
            <div class="flex">
                <div class="ml-3">
                    <p class="text-sm text-red-700 font-bold">Veuillez corriger les erreurs suivantes :</p>
                    <ul class="list-disc list-inside text-xs text-red-600 mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-6">
        
        {{-- SECTION 1 : ADMINISTRATIVE --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-1">
                <label class="block text-sm font-bold text-gray-700 uppercase tracking-tight">Zone d'affectation</label>
                <select wire:model="zone_id" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Choisir la zone --</option>
                    @foreach($zones as $zone)
                        <option value="{{ $zone->id }}">{{ $zone->nom }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1">
                <label class="block text-sm font-bold text-gray-700 uppercase tracking-tight">Agent Approbateur</label>
                <select wire:model="agent_id" class="w-full border-gray-300 rounded-lg">
                    <option value="">-- Choisir l'agent --</option>
                    @foreach($agents as $agent)
                        <option value="{{ $agent->id }}">{{ $agent->user->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- SECTION 2 : CONDITIONS FINANCIÈRES --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <label class="block text-sm font-bold text-gray-700 uppercase tracking-tight">Monnaie</label>
                <select wire:model.live="monnaie" class="w-full border-gray-300 rounded-lg">
                    <option value="">-- Choisir la monnaie --</option>
                    <option value="CDF">CDF</option>
                    <option value="USD">USD</option>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-1">
                    <label class="block text-sm font-bold text-gray-700 uppercase tracking-tight">Capital à prêter</label>
                    <div class="relative">
                        <input type="number" wire:model.live="capital" class="w-full border-gray-300 rounded-lg pl-4 pr-12 py-3 font-bold text-lg">
                        <span class="absolute right-4 top-3.5 text-gray-400 font-bold text-sm">{{$monnaie}}</span>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block text-sm font-bold text-gray-700 uppercase tracking-tight">Intérêt Total</label>
                    <div class="relative">
                        <input type="number" wire:model.live="interet" class="w-full border-gray-300 rounded-lg pl-4 pr-12 py-3 font-bold text-lg text-green-600">
                        <span class="absolute right-4 top-3.5 text-gray-400 font-bold text-sm">{{$monnaie}}</span>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block text-sm font-bold text-gray-700 uppercase tracking-tight">Pénalité Jour (%)</label>
                    <input type="number" step="0.01" wire:model="taux_penalite_journalier" class="w-full border-gray-300 rounded-lg py-3">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                {{-- Design Durée & Unité optimisé --}}
                <div class="space-y-1">
                    <label class="block text-sm font-bold text-gray-700 uppercase tracking-tight">Durée du crédit</label>
                    <div class="flex shadow-sm">
                        <input type="number" wire:model.live="duree" 
                               class="w-1/2 border-gray-300 rounded-l-lg focus:ring-blue-500 focus:border-blue-500 py-3" 
                               placeholder="Ex: 12">
                        <select wire:model.live="unite_temps" 
                                class="w-1/2 border-gray-300 border-l-0 rounded-r-lg bg-gray-50 font-medium text-gray-600">
                            <option value="jour">Jours</option>
                            <option value="semaine">Semaines</option>
                            <option value="mois">Mois</option>
                            <option value="annee">Années</option>
                        </select>
                    </div>
                </div>

                {{-- Date de fin (Modifiable) --}}
                <div class="space-y-1">
                    <label class="block text-sm font-bold text-gray-700 uppercase tracking-tight text-blue-600">
                        📅 Date de Fin (Calculée ou Confirmée)
                    </label>
                    <input type="date" wire:model="date_fin" 
                           class="w-full border-blue-200 bg-blue-50 rounded-lg py-3 font-bold text-blue-800 focus:ring-blue-500">
                </div>
            </div>

            {{-- Échéancier inséré ici --}}
            <x-credits.echeancier />
        </div>

        {{-- SECTION 3 : GARANT --}}
        <div class="bg-gray-50 p-6 rounded-xl border border-dashed border-gray-300 space-y-4">
            <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest">Informations du Garant</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="space-y-1">
                    <input type="text" wire:model="garant_nom" placeholder="Nom complet du garant" class="w-full border-gray-300 rounded-lg">
                </div>
                <div class="space-y-1">
                    <input type="text" wire:model="garant_adresse" placeholder="Adresse physique" class="w-full border-gray-300 rounded-lg">
                </div>
                <div class="space-y-1">
                    <input type="text" wire:model="garant_telephone" placeholder="N° de téléphone" class="w-full border-gray-300 rounded-lg">
                </div>
            </div>
        </div>

        <div class="space-y-1">
            <label class="block text-sm font-bold text-gray-700 uppercase tracking-tight">Observation / Note particulière</label>
            <textarea wire:model="observation" rows="2" class="w-full border-gray-300 rounded-lg placeholder-gray-300" placeholder="Informations complémentaires..."></textarea>
        </div>

        {{-- ACTIONS --}}
        <div class="flex justify-between items-center bg-white p-4 rounded-xl shadow-inner border border-gray-100">
            <a href="{{ route('credit.pret.index') }}" wire:navigate class="text-sm font-bold text-gray-400 hover:text-red-500 uppercase tracking-widest transition-colors">
                ❌ Annuler
            </a>
            <button type="submit" 
                    wire:loading.attr="disabled"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-10 py-4 rounded-xl font-black uppercase tracking-widest shadow-xl transition-all hover:scale-105 disabled:opacity-50">
                <span wire:loading.remove>🚀 Valider le Crédit</span>
                <span wire:loading>Traitement en cours...</span>
            </button>
        </div>
    </form>
</div>