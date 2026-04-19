<div class="max-w-5xl mx-auto bg-slate-50 p-0 shadow-2xl rounded-2xl overflow-hidden border border-slate-200">
    {{-- Header --}}
    <div class="bg-white border-b border-slate-200 px-8 py-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight">Octroi de crédit</h3>
                <p class="text-slate-500 text-sm">Dossier pour <span class="font-semibold text-blue-600">{{ $membre->user?->name }}</span></p>
            </div>
            <div class="hidden md:flex items-center space-x-2">
                <span class="text-xs text-slate-400 font-medium italic">Date crédit : {{ \Carbon\Carbon::parse($date_credit)->format('d/m/Y') }}</span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Nouveau Dossier</span>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="save" class="p-8">
        <div class="space-y-8">
            {{-- Section 01 : Attribution --}}
            <section>
                <div class="flex items-center mb-4 text-slate-400 group">
                    <span class="text-xs font-bold uppercase tracking-widest mr-2">01. Attribution</span>
                    <div class="h-px flex-1 bg-slate-200"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-1">
                        <label class="block text-sm font-semibold text-slate-700">Agent</label>
                        <select wire:model.live="agent_id" class="block w-full rounded-lg px-4 py-3 {{ $errors->has('agent_id') ? 'border-red-500 bg-red-50' : 'border-slate-200' }}">
                            <option value="">Sélectionner</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}">{{ $agent->nom }}</option>
                            @endforeach
                        </select>
                        @error('agent_id') <span class="text-red-500 text-[10px] font-bold uppercase">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="block text-sm font-semibold text-slate-700">Zone</label>
                        <select wire:model.live="zone_id" class="block w-full rounded-lg px-4 py-3 {{ $errors->has('zone_id') ? 'border-red-500 bg-red-50' : 'border-slate-200' }}">
                            <option value="">Sélectionner</option>
                            @foreach($zones as $zone)
                                <option value="{{ $zone->id }}">{{ $zone->nom }}</option>
                            @endforeach
                        </select>
                        @error('zone_id') <span class="text-red-500 text-[10px] font-bold uppercase">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="block text-sm font-semibold text-slate-700">Monnaie</label>
                        <select wire:model.live="monnaie" class="block w-full rounded-lg border-slate-200 px-4 py-3 font-bold text-blue-600">
                            <option value="CDF">CDF - Franc Congolais</option>
                            <option value="USD">USD - Dollar Américain</option>
                        </select>
                    </div>
                </div>
            </section>

            {{-- Section 02 : Montants du Crédit (Finances) --}}
            <section class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm relative overflow-hidden">
                <div class="flex items-center mb-6 text-slate-400">
                    <span class="text-xs font-bold uppercase tracking-widest mr-2 text-emerald-600">02. Montants du Crédit</span>
                    <div class="h-px flex-1 bg-emerald-50"></div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Capital</label>
                        <input type="number" wire:model.live.debounce.500ms="capital" class="block w-full rounded-lg font-mono font-bold text-lg px-4 py-3 border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-emerald-500">
                        @error('capital') <p class="text-red-500 text-[10px] font-bold uppercase mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Intérêt</label>
                        <input type="number" wire:model.live.debounce.500ms="interet" class="block w-full rounded-lg font-mono font-bold text-lg px-4 py-3 border-slate-200 bg-slate-50 text-green-700 focus:ring-2 focus:ring-green-500">
                    </div>

                    <div class="bg-slate-900 rounded-lg p-4 flex flex-col justify-center shadow-inner">
                        <span class="text-slate-400 text-[10px] uppercase font-bold mb-1 text-center tracking-widest">Total dû</span>
                        <div class="text-white text-xl font-black text-center">
                            {{ number_format((float)$capital + (float)$interet, 2) }} <span class="text-emerald-400 text-xs">{{ $monnaie }}</span>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Section 03 : Paramètres d'Échéances (Temps) --}}
            <section class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm relative overflow-hidden">
                <div class="flex items-center mb-6 text-slate-400">
                    <span class="text-xs font-bold uppercase tracking-widest mr-2 text-blue-600">03. Paramètres d'Échéances</span>
                    <div class="h-px flex-1 bg-blue-50"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Unité de temps</label>
                        <select wire:model.blur="unite_temps" class="block w-full rounded-lg border-slate-200 px-4 py-3 font-semibold focus:ring-blue-500">
                            <option value="jour">Jour</option>
                            <option value="semaine">Semaine</option>
                            <option value="mois">Mois</option>
                            <option value="annee">Année</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Durée (Nombre)</label>
                        <input type="number" wire:model.blur="duree" class="block w-full rounded-lg border-slate-200 px-4 py-3 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Date de fin</label>
                        <div class="relative">
                            <input type="date" 
                                wire:model.live="date_fin" 
                                wire:key="date-fin-field-{{ $unite_temps }}-{{ $duree }}"
                                class="block w-full rounded-lg px-4 py-3 font-bold transition-all
                                {{ $errors->has('date_fin') ? 'bg-red-50 border-red-500 text-red-600 focus:ring-red-500' : 'bg-blue-50 border-blue-200 text-blue-900 focus:ring-blue-500' }}">
                            
                            {{-- Indicateur visuel --}}
                            <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                @error('date_fin')
                                    <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                                @else
                                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                @enderror
                            </div>
                        </div>
                        <div class="mt-2 text-[10px] font-bold uppercase tracking-tighter text-slate-400">
                            @error('date_fin')
                                <span class="text-red-500">{{ $message }}</span>
                            @else
                                <span class="text-green-600">Date de remboursement cohérente</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </section>

            {{-- Section 04 : Garant --}}
            <section>
                <div class="flex items-center mb-4 text-slate-400">
                    <span class="text-xs font-bold uppercase tracking-widest mr-2">04. Informations du Garant</span>
                    <div class="h-px flex-1 bg-slate-200"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-1">
                        <label class="block text-sm font-semibold text-slate-600">Nom Complet</label>
                        <input type="text" wire:model.blur="garant_nom" class="block w-full rounded-lg border-slate-200 px-4 py-2.5">
                        @error('garant_nom') <span class="text-red-500 text-[10px] font-bold uppercase">{{ $message }}</span> @enderror
                    </div>
                    <div class="space-y-1">
                        <label class="block text-sm font-semibold text-slate-600">Téléphone</label>
                        <input type="text" wire:model.blur="garant_telephone" class="block w-full rounded-lg border-slate-200 px-4 py-2.5">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-sm font-semibold text-slate-600">Adresse Résidentielle</label>
                        <input type="text" wire:model.blur="garant_adresse" class="block w-full rounded-lg border-slate-200 px-4 py-2.5">
                    </div>
                </div>
            </section>

            {{-- Échéancier Prévisionnel --}}
            <div x-data="{ open: false }" class="border border-slate-200 rounded-xl bg-white overflow-hidden shadow-sm">
                <button type="button" @click="open = !open" class="w-full flex items-center justify-between p-4 hover:bg-slate-50 transition">
                    <div class="flex items-center space-x-2 text-slate-700 font-bold text-sm uppercase tracking-tight">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span>Consulter l'échéancier prévisionnel</span>
                    </div>
                    <span class="transition-transform duration-300" :class="open ? 'rotate-180' : ''">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </span>
                </button>
                <div x-show="open" x-collapse class="p-6 bg-slate-50 border-t border-slate-100" wire:key="echeancier-{{ $duree }}-{{ $unite_temps }}">
                    @include('livewire.credits.partials.echeancier')
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-slate-200">
                <button type="reset" class="px-6 py-2.5 text-sm font-bold text-slate-400 hover:text-slate-600 transition">Réinitialiser</button>
                <button type="submit" wire:loading.attr="disabled" 
                    class="relative bg-blue-600 text-white px-10 py-3 rounded-xl shadow-lg shadow-blue-200 font-bold hover:bg-blue-700 transition active:scale-95 disabled:opacity-50">
                    <span wire:loading.remove>Valider le Dossier</span>
                    <span wire:loading>Traitement...</span>
                </button>
            </div>
        </div>
    </form>
</div>