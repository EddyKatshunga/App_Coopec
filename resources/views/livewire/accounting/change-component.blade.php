{{-- resources/views/livewire/accounting/change-component.blade.php --}}
<div class="p-6 sm:p-8 bg-gray-50 min-h-screen">
    <div class="max-w-3xl mx-auto">
        <!-- En-tête avec infos Agence/Journée -->
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Opération de change</h1>
                <p class="text-sm text-slate-500">Conversion de devises à la caisse</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="bg-white px-4 py-2 rounded-lg shadow-sm border border-slate-200">
                    <span class="text-xs font-semibold uppercase text-slate-400 block">Agence</span>
                    <span class="text-sm font-medium text-slate-700">{{ $agence->nom ?? 'N/A' }}</span>
                </div>
                <div class="bg-indigo-50 px-4 py-2 rounded-lg border border-indigo-100">
                    <span class="text-xs font-semibold uppercase text-indigo-400 block">Date Comptable</span>
                    <span class="text-sm font-medium text-indigo-700">{{ $journee->date_cloture->isoFormat('dddd D MMMM YYYY') ?? 'Opération Impossible' }}</span>
                </div>
            </div>
        </div>

        <!-- Alertes de succès -->
        @if (session()->has('message'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                 class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 rounded-r-lg flex justify-between items-center shadow-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span class="font-medium">{{ session('message') }}</span>
                </div>
                <button @click="show = false" class="text-emerald-500 hover:text-emerald-700">&times;</button>
            </div>
        @endif

        <!-- Formulaire Principal -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <form wire:submit.prevent="save" class="p-6 md:p-8 space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Devise source -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Devise source</label>
                        <div class="inline-flex p-1 bg-slate-100 rounded-xl w-full">
                            <button type="button" wire:click="setSource('USD')" 
                                    class="flex-1 py-2 text-sm font-medium rounded-lg transition-all {{ $devise_source === 'USD' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500 hover:text-slate-700' }}">
                                USD
                            </button>
                            <button type="button" wire:click="setSource('CDF')" 
                                    class="flex-1 py-2 text-sm font-medium rounded-lg transition-all {{ $devise_source === 'CDF' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500 hover:text-slate-700' }}">
                                CDF
                            </button>
                        </div>
                        @error('devise_source') <span class="text-rose-500 text-xs mt-1 italic">{{ $message }}</span> @enderror
                    </div>

                    <!-- Devise cible (affichage sélection automatique) -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Devise cible</label>
                        <div class="inline-flex p-1 bg-slate-100 rounded-xl w-full opacity-75">
                            <div class="flex-1 py-2 text-sm font-medium rounded-lg bg-white shadow-sm text-indigo-600 text-center">
                                {{ $devise_cible }}
                            </div>
                        </div>
                        @error('devise_cible') <span class="text-rose-500 text-xs mt-1 italic">{{ $message }}</span> @enderror
                    </div>

                    <!-- Montant à convertir -->
                    <div>
                        <label for="montant_source" class="block text-sm font-semibold text-slate-700 mb-2">Montant à convertir</label>
                        <div class="relative">
                            <input type="number" step="0.01" wire:model.live="montant_source" id="montant_source" placeholder="0.00"
                                   class="w-full rounded-xl border-slate-300 pl-4 pr-12 py-3 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-all">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                <span class="text-slate-400 font-medium text-sm">{{ $devise_source }}</span>
                            </div>
                        </div>
                        @error('montant_source') <span class="text-rose-500 text-xs mt-1 italic">{{ $message }}</span> @enderror
                    </div>

                    <!-- Taux de change -->
                    <div>
                        <label for="taux" class="block text-sm font-semibold text-slate-700 mb-2">Taux de change</label>
                        <div class="relative">
                            <input type="number" step="0.0001" wire:model.live="taux" id="taux" placeholder="0.0000"
                                   class="w-full rounded-xl border-slate-300 pl-4 pr-24 py-3 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-all">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                <span class="text-slate-400 font-medium text-sm">{{ $devise_source }}/{{ $devise_cible }}</span>
                            </div>
                        </div>
                        @error('taux') <span class="text-rose-500 text-xs mt-1 italic">{{ $message }}</span> @enderror
                    </div>

                    <!-- Montant reçu (calculé, affiché en lecture seule) -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Montant reçu ({{ $devise_cible }})</label>
                        <div class="relative">
                            <input type="text" readonly 
                                   value="{{ $montant_source && $taux ? number_format($montant_source * $taux, 2, ',', ' ') : '0,00' }}"
                                   class="w-full rounded-xl bg-slate-50 border-slate-200 pl-4 pr-4 py-3 text-slate-600 font-medium shadow-sm">
                        </div>
                    </div>
                </div>

                <!-- Bouton d'action -->
                <div class="pt-4">
                    <button type="submit" wire:loading.attr="disabled"
                            class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-4 px-6 rounded-xl shadow-lg shadow-slate-200 transition-all transform active:scale-[0.98] flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="save">Effectuer le change</span>
                        <span wire:loading wire:target="save" class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Traitement en cours...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>