<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
        <div class="flex items-center p-6 bg-gradient-to-r from-blue-800 to-blue-600">
            <div class="h-24 w-24 bg-white p-1 rounded-full shadow-lg">
                <img class="h-full w-full rounded-full object-cover" 
                     src="https://ui-avatars.com/api/?name={{ urlencode($agent->nom) }}&background=random" 
                     alt="{{ $agent->nom }}">
            </div>
            <div class="ml-6">
                <h2 class="text-3xl font-bold text-white">{{ $agent->nom }}</h2>
                <p class="text-blue-100 italic">{{ $agent->email }}</p>
                <div class="mt-2 flex space-x-2">
                    <span class="px-2 py-1 bg-blue-900 text-white text-xs rounded font-bold uppercase">
                        Agent @ {{ $agent->agence->name }}
                    </span>
                    @if($agent->zone_dirige)
                        <span class="px-2 py-1 bg-green-500 text-white text-xs rounded font-bold uppercase">
                            Gérant Zone : {{ $agent->zone_dirige->name }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-6 rounded-lg shadow border-l-4 border-blue-500">
            <p class="text-sm text-gray-500 uppercase font-bold">Portefeuille</p>
            <p class="text-2xl font-black text-gray-800">{{ $agent->membres_amenes_count }} Membres</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow border-l-4 border-green-500">
            <p class="text-sm text-gray-500 uppercase font-bold">Collectes Valides</p>
            <p class="text-2xl font-black text-gray-800">{{ $agent->transactions_count }} Opérations</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow border-l-4 border-orange-500">
            <p class="text-sm text-gray-500 uppercase font-bold">Crédits Suivis</p>
            <p class="text-2xl font-black text-gray-800">{{ $agent->credits_count }} Dossiers</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow border-l-4 border-purple-500">
            <p class="text-sm text-gray-500 uppercase font-bold">Remboursements</p>
            <p class="text-2xl font-black text-gray-800">{{ $agent->remboursements_count }} Reçus</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="space-y-6">
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Infos Administratives</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">ID Agent :</dt>
                        <dd class="font-bold text-gray-800">#AG-{{ str_pad($agent->id, 4, '0', STR_PAD_LEFT) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Compte Membre :</dt>
                        <dd class="text-blue-600 italic">
                            {{ $agent->membre ? $agent->membre->num_dossier : 'Aucun lié' }}
                        </dd>
                    </div>
                    <div class="flex justify-between border-t pt-2">
                        <dt class="text-gray-500">Date d'embauche :</dt>
                        <dd class="text-gray-800">{{ $agent->created_at->format('d/m/Y') }}</dd>
                    </div>
                </dl>
            </div>

            @if($agent->agence_dirige)
            <div class="bg-amber-50 border border-amber-200 shadow rounded-lg p-6">
                <h3 class="text-amber-800 font-bold flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M5 4a1 1 0 00-2 0v7.268a2 2 0 000 3.464V16a1 1 0 102 0v-1.268a2 2 0 000-3.464V4zM11 4a1 1 0 10-2 0v1.268a2 2 0 000 3.464V16a1 1 0 102 0V8.732a2 2 0 000-3.464V4zM16 3a1 1 0 011 1v7.268a2 2 0 010 3.464V16a1 1 0 11-2 0v-1.268a2 2 0 010-3.464V4a1 1 0 011-1z"></path></svg>
                    Poste de Direction
                </h3>
                <p class="text-sm text-amber-700 mt-2">
                    Cet agent est le <strong>Chef d'Agence</strong> actuel de : <br>
                    <span class="text-lg font-black">{{ $agent->agence_dirige->name }}</span>
                </p>
            </div>
            @endif
        </div>

        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h3 class="font-bold text-gray-700 italic">Derniers membres affiliés par l'agent</h3>
                </div>
                <ul class="divide-y divide-gray-100">
                    @forelse($agent->membresAmenes()->latest()->take(5)->get() as $m)
                        <li class="px-6 py-3 flex justify-between items-center hover:bg-gray-50">
                            <div>
                                <p class="text-sm font-bold text-gray-800">{{ $m->name }}</p>
                                <p class="text-xs text-gray-500">{{ $m->num_dossier }}</p>
                            </div>
                            <span class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $m->created_at->diffForHumans() }}</span>
                        </li>
                    @empty
                        <li class="px-6 py-4 text-center text-gray-400 text-sm italic">Aucun membre affilié pour le moment.</li>
                    @endforelse
                </ul>
                @if($agent->membres_amenes_count > 5)
                    <div class="p-3 bg-gray-50 text-center text-xs text-blue-600 font-bold border-t">
                        Voir les {{ $agent->membres_amenes_count }} membres
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>