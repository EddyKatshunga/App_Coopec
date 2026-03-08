<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    
    {{-- 1. SOLDE TOTAL ÉPARGNE --}}
    <div class="relative overflow-hidden bg-white p-6 rounded-3xl shadow-sm border border-gray-100 group">
        <div class="absolute top-0 right-0 -mr-4 -mt-4 w-24 h-24 bg-indigo-50 rounded-full transition-transform group-hover:scale-110"></div>
        
        <div class="relative">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-2 bg-indigo-600 rounded-lg text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="Path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider">Épargne Totale</h3>
            </div>
            
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-gray-900">{{ number_format_fr($solde_total_cdf, 'CDF') }}</span>
                <span class="text-sm font-bold text-gray-400">FC</span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-gray-900">{{ number_format_fr($solde_total_usd, 'USD') }}</span>
                <span class="text-sm font-bold text-gray-400">$</span>
            </div>
            <p class="mt-2 text-xs text-indigo-600 font-semibold italic">Disponibilité immédiate</p>
        </div>
    </div>

    {{-- 2. ÉTAT DU CRÉDIT --}}
    <div class="relative overflow-hidden bg-white p-6 rounded-3xl shadow-sm border border-gray-100 group">
        @if($prochaine_echeance)
            <div class="absolute top-0 right-0 -mr-4 -mt-4 w-24 h-24 bg-red-50 rounded-full"></div>
            <div class="relative">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-orange-500 rounded-lg text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider">Prochain Remboursement</h3>
                </div>
                
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-gray-900">{{ number_format($prochaine_echeance->montant_echeance, 0, ',', ' ') }}</span>
                    <span class="text-sm font-bold text-gray-400">FC</span>
                </div>
                <p class="mt-2 text-xs text-red-600 font-bold uppercase tracking-tighter">
                    À payer avant le {{ \Carbon\Carbon::parse($prochaine_echeance->date_echeance)->format('d/m/Y') }}
                </p>
            </div>
        @else
            <div class="flex flex-col items-center justify-center h-full py-4 text-center">
                <div class="h-12 w-12 bg-green-50 text-green-500 rounded-full flex items-center justify-center mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <p class="text-sm font-bold text-gray-400 uppercase">Aucun prêt en cours</p>
            </div>
        @endif
    </div>

    {{-- 3. WIDGET DE BIENVENUE / CONSEIL --}}
    <div class="bg-gradient-to-br from-indigo-600 to-purple-700 p-6 rounded-3xl shadow-lg text-white">
        <h3 class="text-lg font-bold mb-2">Conseil du jour 💡</h3>
        <p class="text-indigo-100 text-sm leading-relaxed">
            "Épargner régulièrement, même de petites sommes, est le meilleur moyen de financer vos projets futurs sans stress."
        </p>
        <button class="mt-4 text-xs bg-white/20 hover:bg-white/30 transition px-4 py-2 rounded-xl font-bold uppercase tracking-widest">
            En savoir plus
        </button>
    </div>

</div>