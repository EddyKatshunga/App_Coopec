<div class="max-w-5xl mx-auto bg-slate-50 p-0 shadow-2xl rounded-2xl overflow-hidden border border-slate-200">
    <div class="bg-white border-b border-slate-200 px-8 py-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight">Octroi de crédit</h3>
                <p class="text-slate-500 text-sm">Configuration du dossier pour <span class="font-semibold text-blue-600">{{ $membre->user?->name }}</span></p>
            </div>
            <div class="hidden md:block">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    Nouveau Dossier
                </span>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="save" class="p-8">
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0 text-red-400">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">Veuillez corriger les erreurs dans le formulaire.</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="space-y-8">
            <section>
                <div class="flex items-center mb-4 text-slate-400 group">
                    <span class="text-xs font-bold uppercase tracking-widest mr-2">01. Attribution</span>
                    <div class="h-px flex-1 bg-slate-200"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-1">
                        <label class="block text-sm font-semibold text-slate-700 uppercase tracking-wider">Agent</label>
                        <select wire:model="agent_id" class="block w-full rounded-lg border-slate-200 bg-white px-4 py-3 text-slate-700 transition focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Sélectionner l'agent</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}">{{ $agent->nom }}</option>
                            @endforeach
                        </select>
                        @error('agent_id') <span class="text-red-500 text-[10px] font-bold uppercase">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="block text-sm font-semibold text-slate-700 uppercase tracking-wider">Zone</label>
                        <select wire:model="zone_id" class="block w-full rounded-lg border-slate-200 bg-white px-4 py-3 text-slate-700 transition focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Sélectionner la zone</option>
                            @foreach($zones as $zone)
                                <option value="{{ $zone->id }}">{{ $zone->nom }}</option>
                            @endforeach
                        </select>
                        @error('zone_id') <span class="text-red-500 text-[10px] font-bold uppercase">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="block text-sm font-semibold text-slate-700 uppercase tracking-wider">Monnaie</label>
                        <div class="flex rounded-lg shadow-sm">
                            <select wire:model.live="monnaie" class="block w-full rounded-lg border-slate-200 bg-white px-4 py-3 text-slate-700 transition focus:border-blue-500 focus:ring-blue-500">
                                <option value="CDF">CDF - Franc Congolais</option>
                                <option value="USD">USD - Dollar Américain</option>
                            </select>
                        </div>
                    </div>
                </div>
            </section>

            <section class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <div class="flex items-center mb-6 text-slate-400">
                    <span class="text-xs font-bold uppercase tracking-widest mr-2 text-blue-600">02. Calcul des Échéances</span>
                    <div class="h-px flex-1 bg-blue-50"></div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="lg:col-span-1">
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Capital</label>
                        <div class="relative">
                            <input type="number" wire:model.live="capital" class="block w-full rounded-lg border-slate-200 bg-slate-50 font-mono font-bold text-lg px-4 py-3 pr-12 focus:bg-white focus:ring-2 focus:ring-blue-500">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm">{{ $monnaie }}</span>
                        </div>
                        <p class="mt-2 text-[10px] text-slate-400 italic leading-tight">{{ money_to_words($capital, $monnaie) }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Intérêt</label>
                        <div class="relative">
                            <input type="number" wire:model.live="interet" class="block w-full rounded-lg border-slate-200 bg-slate-50 font-mono font-bold text-lg px-4 py-3 pr-12 focus:bg-white focus:ring-2 focus:ring-green-500 text-green-700">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm">{{ $monnaie }}</span>
                        </div>
                        <p class="mt-2 text-[10px] text-slate-400 italic leading-tight">{{ money_to_words($interet, $monnaie) }}</p>
                    </div>

                    <div class="bg-slate-900 rounded-lg p-4 flex flex-col justify-center shadow-lg">
                        <span class="text-slate-400 text-[10px] uppercase font-bold mb-1 tracking-tighter text-center">À rembourser</span>
                        <div class="text-white text-xl font-black text-center">
                            {{ number_format((float)$capital + (float)$interet, 2) }} 
                            <span class="text-blue-400 text-xs ml-1">{{ $monnaie }}</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1 tracking-tight">Pénalité Jour (%)</label>
                        <input type="number" step="0.01" wire:model="taux_penalite_journalier" class="block w-full rounded-lg border-slate-200 px-4 py-3 text-slate-700 focus:ring-red-500 focus:border-red-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6 pt-6 border-t border-slate-100">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Unité de temps</label>
                        <select wire:model.live="unite_temps" class="mt-1 block w-full rounded-lg border-slate-200 px-4 py-3 transition focus:ring-blue-500">
                            <option value="jour text-sm">Jour (Lun-Sam)</option>
                            <option value="semaine">Semaine</option>
                            <option value="mois">Mois</option>
                            <option value="annee">Année</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Durée (Nombre)</label>
                        <input type="number" wire:model.live="duree" class="mt-1 block w-full rounded-lg border-slate-200 px-4 py-3 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Date de fin suggérée</label>
                        <input type="date" wire:model="date_fin" class="mt-1 block w-full rounded-lg border-slate-200 px-4 py-3 bg-blue-50 font-semibold focus:ring-blue-500">
                    </div>
                </div>
            </section>

            <section>
                <div class="flex items-center mb-4 text-slate-400">
                    <span class="text-xs font-bold uppercase tracking-widest mr-2">03. Informations du Garant</span>
                    <div class="h-px flex-1 bg-slate-200"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-1">
                        <label class="block text-sm font-semibold text-slate-600">Nom Complet</label>
                        <input type="text" wire:model="garant_nom" placeholder="Ex: Jean Dupont" class="block w-full rounded-lg border-slate-200 px-4 py-2.5">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-sm font-semibold text-slate-600">Téléphone</label>
                        <input type="text" wire:model="garant_telephone" class="block w-full rounded-lg border-slate-200 px-4 py-2.5">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-sm font-semibold text-slate-600">Adresse Résidentielle</label>
                        <input type="text" wire:model="garant_adresse" class="block w-full rounded-lg border-slate-200 px-4 py-2.5">
                    </div>
                </div>
            </section>

            <div class="space-y-1">
                <label class="block text-sm font-semibold text-slate-700">Notes de négociation & Observations</label>
                <textarea wire:model="observation" rows="3" class="block w-full rounded-xl border-slate-200 px-4 py-3 placeholder:text-slate-300 focus:ring-blue-500" placeholder="Précisez ici les particularités de l'accord..."></textarea>
            </div>

            <div x-data="{ open: false }" class="border border-slate-200 rounded-xl bg-white overflow-hidden shadow-sm">
                <button type="button" @click="open = !open" class="w-full flex items-center justify-between p-4 hover:bg-slate-50 transition">
                    <div class="flex items-center space-x-2 text-slate-700">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span class="font-bold text-sm tracking-tight">Consulter l'échéancier prévisionnel</span>
                    </div>
                    <span class="transition-transform duration-300" :class="open ? 'rotate-180' : ''">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </span>
                </button>

                <div x-show="open" x-collapse x-transition class="p-6 bg-slate-50 border-t border-slate-100" wire:key="echeancier-{{ $duree }}-{{ $unite_temps }}">
                    @include('livewire.credits.partials.echeancier')
                </div>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-slate-200">
                <button type="reset" class="px-6 py-2.5 text-sm font-bold text-slate-500 hover:text-slate-700 transition">
                    Réinitialiser
                </button>
                <button type="submit" wire:loading.attr="disabled" class="relative group overflow-hidden bg-blue-600 text-white px-8 py-3 rounded-xl shadow-lg shadow-blue-200 font-bold hover:bg-blue-700 transition-all active:scale-95 disabled:opacity-70">
                    <span wire:loading.remove class="flex items-center">
                        Valider le Dossier
                        <svg class="ml-2 w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </span>
                    <span wire:loading class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Traitement...
                    </span>
                </button>
            </div>
        </div>
    </form>
</div>