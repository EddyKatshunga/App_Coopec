<div class="space-y-8">

    {{-- NIVEAU 6+ : ADMINISTRATION & CONFIGURATION --}}
    @can('can.level4')
    <section>
        <h2 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-4 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-red-500"></span> Administration Système
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <x-dashboard.action title="Agents" icon="users" route="agents.index" size="sm" />
            <x-dashboard.action title="Zones de Crédits" icon="map" route="agences.zones.index" size="sm" />
            @can('can.level6')
            <x-dashboard.action title="Agences" icon="office-building" route="agences.index" size="sm" />
            <x-dashboard.action title="Types Dépenses" icon="cog" route="types-depense.index" size="sm" />
            <x-dashboard.action title="Types Revenus" icon="cog" route="types-revenu.index" size="sm" />
            <x-dashboard.action title="Historique Roles" icon="clock" route="historiquesroles.index" size="sm" />
            @endcan
            @can('canTransact')
            <x-dashboard.action title="Mon Agence" icon="office-building" route="agence.show" :routeParams="['agence' => auth()->user()->agence]" size="sm" />
            @endcan
        </div>
    </section>
    @endcan

    {{-- NIVEAU 4-5 : GESTION DE CAISSE, REVENUS & DEPENSES --}}
    @can('can.level3')
    <section>
        <h2 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-4 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-orange-500"></span> Gestion Caisse & Flux
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @can('can.level3')
                 <x-dashboard.action title="Effectuer un Retrait" icon="minus-circle" route="comptes.index" />
            @endcan

            @can('canTransact')
            @can('can.level3')
                <x-dashboard.action title="Saisir une Dépense" icon="trending-down" route="depenses.create" />
                <x-dashboard.action title="Saisir un Revenu" icon="trending-up" route="revenus.create" />
            @endcan
            @endcan

            @can('can.level4')
                <x-dashboard.action title="Voir les journées précédentes" icon="lock-closed" route="clotures.index" />
                @can('canTransact')
                <x-dashboard.action title="Situation de la journée" icon="lock-closed" route="clotures.show" :routeParams="['cloture' => auth()->user()->journee_ouverte]"/>
                @endcan 
            @endcan
        </div>
    </section>
    @endcan

    {{-- NIVEAU 1-3 : AGENTS DE TERRAIN / COLLECTE --}}
    @can('can.level1')
    <section>
        <h2 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-4 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-green-500"></span> Opérations de Terrain
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @can('can.level4')
                <x-dashboard.action title="Nouveau membre" icon="user-plus" route="membre.create" />
            @endcan

            @can('canTransact')
                @can('can.level1')
                    <x-dashboard.action title="Ajouter un Dépot" icon="cash" route="comptes.index" />
                @endcan

                @can('can.level2')
                    <x-dashboard.action title="Ajouter un Remboursement" icon="cash" route="credit.pret.index" />
                @endcan

                @can('can.level4')
                    <x-dashboard.action title="Liste des membres" icon="document-add" route="membre.index" />
                @endcan
             @endcan
        </div>
    </section>
    @endcan
    
    {{-- NIVEAU 0 : ACTIONS PERSONNELLES (Tout utilisateur/membre) --}}
    <section>
        <h2 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-4 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-blue-500"></span> Espace Personnel
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @can('can.level0')
                <x-dashboard.action
                    title="Mon Dossier & Comptes"
                    icon="user-circle"
                    route="membre.show"
                    :routeParams="['membre' => auth()->user()->membre->uuid ?? '']"
                    color="blue"
                />
            @endcan
            @if(auth()->user()->agent)
                <x-dashboard.action
                    title="Mes performences"
                    icon="user-circle"
                    route="agent.show"
                    :routeParams="['agent' => auth()->user()->agent]"
                    color="blue"
                />
            @endif
        </div>
    </section>

</div>