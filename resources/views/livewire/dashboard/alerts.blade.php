<div class="space-y-3">

    @can('epargne.correct')
        <x-dashboard.alert
            type="warning"
            message="Des opérations d’épargne nécessitent une correction"
            route="transaction.list"
        />
    @endcan

    @can('credit.cloturer')
        <x-dashboard.alert
            type="danger"
            message="Crédits arrivés à échéance non clôturés"
            route="credits.index"
        />
    @endcan

</div>
