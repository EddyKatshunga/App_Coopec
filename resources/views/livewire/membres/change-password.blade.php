<div class="max-w-2xl mx-auto p-6 md:p-8"> {{-- L'UNIQUE PARENT --}}
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 md:p-8">
        
        <div class="flex items-center gap-3 mb-6">
            <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                <x-heroicon-o-shield-check class="w-6 h-6"/>
            </div>
            <h3 class="text-xl font-black text-gray-900">Sécurité du compte</h3>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl text-sm font-bold flex items-center gap-2">
                <x-heroicon-s-check-circle class="w-5 h-5"/>
                {{ session('success') }}
            </div>
        @endif

        <form wire:submit.prevent="updatePassword" class="space-y-6">
            {{-- Champ : Mot de passe actuel --}}
            <div wire:key="container-current">
                <label for="current_password" class="block text-xs font-black uppercase text-gray-400 mb-2 tracking-widest text-gray-400">Ancien mot de passe</label>
                <div class="relative">
                    <input type="password" 
                           id="current_password"
                           wire:model="current_password"
                           class="w-full pl-4 pr-12 py-3 rounded-2xl border-gray-100 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition text-sm font-medium">
                    
                    <button type="button" 
                            onclick="const el = document.getElementById('current_password'); el.type = el.type === 'password' ? 'text' : 'password';" 
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-600 transition-colors">
                        <x-heroicon-o-eye class="w-5 h-5"/>
                    </button>
                </div>
                @error('current_password') <span class="text-red-500 text-[10px] font-black mt-1 ml-2 block italic">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Champ : Nouveau mot de passe --}}
                <div wire:key="container-new">
                    <label for="password" class="block text-xs font-black uppercase text-gray-400 mb-2 tracking-widest text-gray-400">Nouveau mot de passe</label>
                    <div class="relative">
                        <input type="password" 
                               id="password"
                               wire:model="password"
                               class="w-full pl-4 pr-12 py-3 rounded-2xl border-gray-100 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition text-sm font-medium">
                        
                        <button type="button" 
                                onclick="const el = document.getElementById('password'); el.type = el.type === 'password' ? 'text' : 'password';" 
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-600 transition-colors">
                            <x-heroicon-o-eye class="w-5 h-5"/>
                        </button>
                    </div>
                    @error('password') <span class="text-red-500 text-[10px] font-black mt-1 ml-2 block italic">{{ $message }}</span> @enderror
                </div>

                {{-- Champ : Confirmation --}}
                <div wire:key="container-confirm">
                    <label for="password_confirmation" class="block text-xs font-black uppercase text-gray-400 mb-2 tracking-widest text-gray-400">Confirmation</label>
                    <div class="relative">
                        <input type="password" 
                               id="password_confirmation"
                               wire:model="password_confirmation"
                               class="w-full pl-4 pr-12 py-3 rounded-2xl border-gray-100 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition text-sm font-medium">
                        
                        <button type="button" 
                                onclick="const el = document.getElementById('password_confirmation'); el.type = el.type === 'password' ? 'text' : 'password';" 
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-600 transition-colors">
                            <x-heroicon-o-eye class="w-5 h-5"/>
                        </button>
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
                    <span wire:loading.remove>Mettre à jour</span>
                    <span wire:loading class="flex items-center justify-center gap-2">
                         <svg class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Mise à jour...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>