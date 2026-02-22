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

    @can('agent.create')
        <x-dashboard.action
            title="Tableau de Bord des Depenses"
            icon="list"
            route="types-depense.index"
        />
    @endcan

     @can('agent.create')
        <x-dashboard.action
            title="Tableau de Bord des Revenus"
            icon="list"
            route="types-revenu.index"
        />
    @endcan

    @can('agent.create')
        <x-dashboard.action
            title="Gestion des Agents"
            icon="list"
            route="agents.index"
        />
    @endcan

    @can('agent.create')
        <x-dashboard.action
            title="Gestion des Agences"
            icon="list"
            route="agences.index"
        />
    @endcan

    @can('agent.create')
        <x-dashboard.action
            title="Gestion des Permissions"
            icon="list"
            route="admin.permissions.matrix.index"
        />
    @endcan

    @can('agent.create')
        <x-dashboard.action
            title="Gestion des Clotures Journalières"
            icon="list"
            route="clotures.index"
        />
    @endcan

    @can('agent.create')
        <x-dashboard.action
            title="Gestion des Depenses"
            icon="list"
            route="depenses.index"
        />
    @endcan

        @can('agent.create')
        <x-dashboard.action
            title="Gestion des Revenus"
            icon="list"
            route="revenus.index"
        />
    @endcan

</div>
