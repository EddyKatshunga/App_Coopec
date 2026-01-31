<div class="grid grid-cols-1 md:grid-cols-3 gap-4">

    @can('epargne.depot.create')
        <x-dashboard.action
            title="Nouveau dépôt"
            icon="cash"
            route="epargne.depot.create"
        />
    @endcan

    @can('epargne.retrait.create')
        <x-dashboard.action
            title="Nouveau retrait"
            icon="arrow-down"
            route="epargne.retrait.create"
        />
    @endcan

    @can('credit.pret.create')
        <x-dashboard.action
            title="Enregistrer un prêt"
            icon="document"
            route="credit.pret.create"
        />
    @endcan

    @can('credit.remboursement.create')
        <x-dashboard.action
            title="Remboursement"
            icon="refresh"
            route="credit.remboursement.create"
        />
    @endcan

    @can('membre.create')
        <x-dashboard.action
            title="Nouveau membre"
            icon="user-plus"
            route="membre.create"
        />
    @endcan

</div>
