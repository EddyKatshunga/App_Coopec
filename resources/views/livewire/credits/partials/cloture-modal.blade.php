{{-- ================= MODAL DE CLOTURE (PURE LIVEWIRE) ================= --}}
    
    @if($showClotureModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                {{-- Overlay --}}
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" wire:click="toggleClotureModal"></div>

                {{-- Modal --}}
                <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-md p-8 relative z-10 border border-gray-100">
                    <div class="w-16 h-16 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center mb-6 mx-auto">
                        <x-heroicon-s-exclamation-triangle class="w-8 h-8"/>
                    </div>

                    <h3 class="text-xl font-black text-gray-900 mb-2 text-center">Clôturer le dossier</h3>
                    <p class="text-sm text-gray-500 text-center mb-6">Cette opération annulera définitivement le solde restant et les pénalités.</p>

                    <div class="space-y-4">
                        <div>
                            <label class="text-[10px] font-black uppercase text-gray-400 mb-2 block">Motif de clôture</label>
                            <textarea wire:model="noteCloture" rows="3" class="w-full rounded-2xl border-gray-100 bg-gray-50 focus:bg-white text-sm font-medium"></textarea>
                            @error('noteCloture') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="bg-red-50 border border-red-200 rounded-xl p-3">
                            <label class="flex items-center gap-3 text-sm font-bold text-red-700 cursor-pointer">
                                <input type="checkbox" wire:model.live="confirmCloture" class="rounded border-red-300 text-red-600 focus:ring-red-500">
                                Je confirme la clôture
                            </label>
                            @error('confirmCloture') <p class="text-[10px] text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col gap-3 pt-2">
                            <button 
                                wire:click="validerClotureForcee" 
                                wire:loading.attr="disabled"
                                @if(!$confirmCloture) disabled @endif
                                class="w-full py-4 bg-amber-600 text-white font-black rounded-2xl hover:bg-amber-700 disabled:opacity-40 shadow-lg transition">
                                <span wire:loading.remove>Confirmer la clôture</span>
                                <span wire:loading>Action en cours...</span>
                            </button>
                            <button wire:click="toggleClotureModal" class="w-full py-4 bg-white text-gray-500 font-bold rounded-2xl hover:bg-gray-50 transition">
                                Annuler
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif