<div class="p-4 md:p-6 space-y-6 bg-gray-50 min-h-screen">

    {{-- En-tête & Stats --}}
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">📊 Comptes Épargnes</h1>
            <p class="text-gray-500 text-sm">Gestion des membres et balances de la coopérative</p>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
        <x-stat-card label="Membres" :value="$stats['membres']" icon="👥" class="bg-white border-none shadow-sm" />
        <x-stat-card label="Comptes" :value="$stats['total_comptes']" icon="💼" class="bg-white border-none shadow-sm" />
        <x-stat-card label="Total CDF" :value="number_format($stats['total_cdf'], 0, ',', ' ')" icon="💰" class="bg-white border-none shadow-sm text-green-600" />
        <x-stat-card label="Total USD" :value="number_format($stats['total_usd'], 2)" icon="💵" class="bg-white border-none shadow-sm text-blue-600" />
    </div>

    {{-- Barre de recherche --}}
    <div class="relative w-full">
        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </span>
        <input type="text" wire:model.live.debounce.500ms="search" placeholder="Rechercher un compte..." class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none">
    </div>

    {{-- Grille de Cartes --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($comptes as $compte)
            <div 
                wire:key="card-compte-{{ $compte->id }}"
                {{-- Logique d'accordéon : on écoute un événement global 'close-others' --}}
                x-data="{ 
                    isOpen: false,
                    toggle() {
                        if (!this.isOpen) {
                            $dispatch('close-others', { id: {{ $compte->id }} });
                        }
                        this.isOpen = !this.isOpen;
                    }
                }" 
                @close-others.window="if ($event.detail.id !== {{ $compte->id }}) isOpen = false"
                class="bg-white rounded-2xl shadow-sm border border-gray-100 transition-all duration-300 overflow-hidden h-fit"
                :class="isOpen ? 'ring-2 ring-blue-500 shadow-lg' : 'hover:shadow-md'"
            >
                {{-- Contenu Principal --}}
                <div class="p-5">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 font-bold flex-shrink-0">
                                {{ strtoupper(substr($compte->user->name ?? '?', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <h3 class="font-bold text-gray-900 leading-tight truncate">{{ $compte->user->name ?? '—' }}</h3>
                                <p class="text-xs font-mono text-gray-500">{{ $compte->numero_compte }}</p>
                            </div>
                        </div>
                        
                        <button 
                            type="button"
                            @click="toggle()" 
                            class="p-2 hover:bg-gray-100 rounded-full transition-all duration-200"
                            :class="isOpen ? 'rotate-180 bg-blue-50 text-blue-600' : 'text-gray-400'"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </div>

                    <div class="mt-4">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Intitulé</span>
                        <p class="text-sm font-medium text-gray-700 truncate">{{ $compte->intitule }}</p>
                    </div>
                </div>

                {{-- Détails Supplémentaires --}}
                <div 
                    x-show="isOpen" 
                    x-collapse
                    x-cloak
                    class="bg-gray-50 border-t border-gray-100 p-5 space-y-4"
                >
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white p-3 rounded-xl border border-gray-200">
                            <p class="text-[10px] text-gray-400 uppercase font-bold text-center">Solde CDF</p>
                            <p class="text-sm font-bold text-green-600 text-center">{{ number_format($compte->solde_cdf, 0, ',', ' ') }}</p>
                        </div>
                        <div class="bg-white p-3 rounded-xl border border-gray-200">
                            <p class="text-[10px] text-gray-400 uppercase font-bold text-center">Solde USD</p>
                            <p class="text-sm font-bold text-blue-600 text-center">{{ number_format($compte->solde_usd, 2) }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2 gap-2">
                        <a href="{{ route('compte.show', $compte) }}" class="flex-1 bg-white border border-gray-200 text-gray-700 py-2 rounded-lg text-[10px] font-bold text-center hover:bg-gray-50 transition uppercase">Voir</a>
                        <a href="{{ route('epargne.depot.create', $compte) }}" class="flex-1 bg-green-600 text-white py-2 rounded-lg text-[10px] font-bold text-center hover:bg-green-700 transition uppercase">Dépôt</a>
                        <a href="{{ route('epargne.retrait.create', $compte) }}" class="flex-1 bg-amber-500 text-white py-2 rounded-lg text-[10px] font-bold text-center hover:bg-amber-600 transition uppercase">Retrait</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl p-12 text-center border-2 border-dashed border-gray-200 text-gray-400">
                <div class="text-4xl mb-3">🔍</div>
                <h3 class="font-medium text-gray-600">Aucun compte trouvé</h3>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $comptes->links() }}
    </div>
</div>