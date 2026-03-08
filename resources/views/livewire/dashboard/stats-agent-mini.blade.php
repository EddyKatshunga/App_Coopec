<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
        <a href='{{ route('epargne.transactions.index')}}'>
            <p class="text-xs font-bold text-gray-400 uppercase">Epargnes collectés (Aujourd'hui)</p>
        </a>
        <p class="text-2xl font-black text-green-600">{{ number_format_fr($collecte_jour_cdf, 'CDF') }} <span class="text-xs">FC</span></p>
         <p class="text-2xl font-black text-green-600">{{ number_format_fr($collecte_jour_usd, 'USD') }} <span class="text-xs">$</span></p>
    </div>
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
        <p class="text-xs font-bold text-gray-400 uppercase">Inscriptions (Aujourd'hui)</p>
        <p class="text-2xl font-black text-blue-600">{{ $nouveaux_membres }}</p>
    </div>
</div>