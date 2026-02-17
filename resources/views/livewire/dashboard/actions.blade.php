<div class="grid grid-cols-1 md:grid-cols-3 gap-4">

    {{-- =========================
        👥 MEMBRES
    ========================== --}}

    @can('membre.create')
        <x-dashboard.action
            title="Nouveau membre"
            icon="user-plus"
            route="membre.create"
        />
    @endcan

    @can('membre.update')
        <x-dashboard.action
            title="Liste des membres"
            icon="users"
            route="membre.index"
        />
    @endcan


    {{-- =========================
        💰 ÉPARGNE
    ========================== --}}

    @can('epargne.depot.create')
        <x-dashboard.action
            title="Nouveau dépôt"
            icon="cash"
            route="epargne.depot.create"
        />
    @endcan

    @can('epargne.view.transactions')
        <x-dashboard.action
            title="Transactions épargne"
            icon="list"
            route="epargne.transactions.index"
        />
    @endcan


    {{-- =========================
        💳 CRÉDIT
    ========================== --}}

    @can('credit.pret.view')
        <x-dashboard.action
            title="Liste des prêts"
            icon="document"
            route="credit.pret.index"
        />
    @endcan

    @can('credit.remboursement.view')
        <x-dashboard.action
            title="Remboursements"
            icon="list"
            route="credit.remboursement.index"
        />
    @endcan

    @can('depense.view')
        <x-dashboard.action
            title="Tableau de Bord des Depenses"
            icon="list"
            route="types-depense.index"
        />
    @endcan

</div>
