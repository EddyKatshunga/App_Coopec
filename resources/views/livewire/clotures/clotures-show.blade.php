<!---ressources>views>livewire>clotures>clotures-show.blade.php--->
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-8">
    {{-- En-tête avec statut et actions --}}
    @include('livewire.clotures.partials._header')

    {{-- Cartes des soldes et indicateurs --}}
    @include('livewire.clotures.partials._cards')

    {{-- Tableau synthèse des opérations --}}
    @include('livewire.clotures.partials._synthese')
</div>