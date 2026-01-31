<div class="grid grid-cols-1 md:grid-cols-4 gap-4">

    @can('membre.view.epargne')
        <x-dashboard.stat
            title="Solde Épargne"
            :value="auth()->user()->membre->solde_epargne ?? '—'"
        />
    @endcan

    @can('credit.pret.view')
        <x-dashboard.stat
            title="Prêts actifs"
            :value="$pretCount ?? 0"
        />
    @endcan

    @can('epargne.view.transactions')
        <x-dashboard.stat
            title="Transactions"
            :value="$transactionCount ?? 0"
        />
    @endcan

    @can('depense.view')
        <x-dashboard.stat
            title="Dépenses"
            :value="$depenseCount ?? 0"
        />
    @endcan

</div>
