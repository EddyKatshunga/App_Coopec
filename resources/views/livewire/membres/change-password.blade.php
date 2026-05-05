<div class="max-w-2xl mx-auto p-6 md:p-8">
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 md:p-8">
        
        <div class="flex items-center gap-3 mb-8">
            <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                <x-heroicon-o-lock-closed class="w-6 h-6"/>
            </div>
            <div>
                <h3 class="text-xl font-black text-gray-900">Code de sécurité</h3>
                <h4 class="text-x font-black text-gray-900">{{$membre->user->name}}</h4>
                <p class="text-xs text-gray-400 font-medium italic">Utilisez un code numérique à 6 chiffres</p>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl text-sm font-bold flex items-center gap-2">
                <x-heroicon-s-check-circle class="w-5 h-5"/>
                {{ session('success') }}
            </div>
        @endif

        <form wire:submit.prevent="updatePassword" class="space-y-8">
            
            {{-- Champ : Code actuel --}}
            <div class="space-y-3">
                <label class="block text-xs font-black uppercase text-gray-400 tracking-widest">Entrez votre mot de passe</label>
                <input type="password" 
                       inputmode="numeric"
                       wire:model="current_password"
                       placeholder="••••••"
                       class="w-full text-center tracking-[1em] text-xl font-bold py-4 rounded-2xl border-gray-100 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition">
                @error('current_password') <span class="text-red-500 text-[10px] font-black mt-1 ml-2 block italic">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Champ : Nouveau code --}}
                <div class="space-y-3">
                    <label class="block text-xs font-black uppercase text-gray-400 tracking-widest">Nouveau code</label>
                    <div class="relative group">
                        <input type="password" 
                               inputmode="numeric"
                               maxlength="6"
                               wire:model="password"
                               placeholder="000000"
                               class="w-full text-center tracking-[1em] text-xl font-bold py-4 rounded-2xl border-gray-100 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition">
                        
                        {{-- Visualisation des 6 cases (Décoratif) --}}
                        <div class="flex justify-center gap-2 mt-2">
                            @for($i = 0; $i < 6; $i++)
                                <div class="w-3 h-1 rounded-full {{ strlen($password) > $i ? 'bg-blue-500' : 'bg-gray-200' }}"></div>
                            @endfor
                        </div>
                    </div>
                    @error('password') <span class="text-red-500 text-[10px] font-black mt-1 ml-2 block italic">{{ $message }}</span> @enderror
                </div>

                {{-- Champ : Confirmation --}}
                <div class="space-y-3">
                    <label class="block text-xs font-black uppercase text-gray-400 tracking-widest">Confirmation</label>
                    <input type="password" 
                           inputmode="numeric"
                           maxlength="6"
                           wire:model="password_confirmation"
                           placeholder="000000"
                           class="w-full text-center tracking-[1em] text-xl font-bold py-4 rounded-2xl border-gray-100 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition">
                    <div class="flex justify-center gap-2 mt-2">
                        @for($i = 0; $i < 6; $i++)
                            <div class="w-3 h-1 rounded-full {{ strlen($password_confirmation) > $i ? 'bg-emerald-500' : 'bg-gray-200' }}"></div>
                        @endfor
                    </div>
                </div>
            </div>

            <div class="pt-4 flex gap-3">
                <a href="{{ route('membre.show', $membre) }}" class="flex-1 text-center py-4 bg-gray-100 text-gray-700 rounded-2xl font-bold hover:bg-gray-200 transition">
                    Annuler
                </a>
                <button type="submit" 
                        wire:loading.attr="disabled"
                        class="flex-1 py-4 bg-gray-900 text-white rounded-2xl font-black hover:bg-blue-600 transition-all duration-300 shadow-xl shadow-gray-200 disabled:opacity-50">
                    <span wire:loading.remove>Valider le code</span>
                    <span wire:loading class="flex items-center justify-center gap-2">
                         <svg class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>