<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Pdf\MembrePdfController;
use App\Http\Controllers\Pdf\ReleveComptePdfController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\NewsController;
use App\Livewire\Comptes\CompteList;
use App\Livewire\Comptes\AddCompte;
use Illuminate\Support\Facades\Route;
use App\Livewire\Membres\ShowMembre;
use App\Livewire\Membres\AddEditMembre;
use App\Livewire\Membres\ListeMembres;
use App\Livewire\Transactions\TransactionForm;
use App\Livewire\Agents\AgentList;
use App\Livewire\Agents\AgentForm;
use App\Livewire\Comptes\ShowCompte;
use App\Livewire\Credits\CreditCreate;
use App\Livewire\Credits\CreditShow;
use App\Livewire\Credits\CreditsList;
use App\Livewire\Credits\RemboursementList;
use App\Livewire\Transactions\TransactionListing;
use App\Livewire\Transactions\TransactionsList;
use App\Livewire\Depenses\TypesDepenseForm;
use App\Livewire\Depenses\TypesDepenseShow;
use App\Livewire\Depenses\TypesDepenseList;
use App\Livewire\Agence\AgenceForm;
use App\Livewire\Agence\AgenceList;
use App\Livewire\Agence\AgenceShow;
use App\Livewire\Admin\PermissionMatrix;
use App\Livewire\Clotures\CloturesForm;
use App\Livewire\Clotures\CloturesList;
use App\Livewire\Clotures\CloturesShow;
use App\Livewire\Depenses\DepenseForm;
use App\Livewire\Depenses\DepenseList;
use App\Livewire\Depenses\DepenseShow;
use App\Livewire\Revenus\RevenuForm;
use App\Livewire\Revenus\RevenuList;
use App\Livewire\Revenus\RevenuShow;
use App\Livewire\Revenus\TypesRevenuForm;
use App\Livewire\Revenus\TypesRevenuList;
use App\Livewire\Revenus\TypesRevenuShow;
use App\Livewire\Zones\ZoneForm;
use App\Livewire\Zones\ZoneList;
use App\Livewire\Zones\ZoneShow;
use App\Models\TypesRevenu;

Route::prefix('admin')->group(function () {
    Route::get('/permissions-matrix', PermissionMatrix::class)->name('admin.permissions.matrix.index');
});

Route::get('/', [HomeController::class, 'index'])->name('public.home');
Route::get('/actualites', [NewsController::class, 'index'])->name('public.news');
Route::get('/contact', [ContactController::class, 'index'])->name('public.contact');

Route::middleware(['auth'])
    ->get('/dashboard', App\Livewire\Dashboard\Dashboard::class)
    ->name('dashboard');

/*
Route::middleware(['auth', 'permission:membre.creer'])->group(function () {
    Route::get('/membres/create', CreateMembre::class)->name('membres.create');
});*/

Route::middleware(['auth'])->group(function () {
    Route::get('/clotures/ouvrir', CloturesForm::class)->name('clotures.ouvrir');
    Route::get('/clotures/{cloture}/valider', CloturesForm::class)->name('clotures.valider');
    Route::get('/clotures/{cloture}/show', CloturesShow::class)->name('clotures.show');
    Route::get('/clotures', CloturesList::class)->name('clotures.index');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/agences/zones/', ZoneList::class)->name('agences.zones.index');
    Route::get('/agences/zones/{zoneId}/show/', ZoneShow::class)->name('agences.zones.show');
    Route::get('/agences/{agence}/zones/create', ZoneForm::class)
        ->name('agences.zones.create');
    Route::get('/agences/{agence}/zones/{zone}/edit', ZoneForm::class)
        ->name('agences.zones.edit');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/depenses/create', DepenseForm::class)->name('depenses.create');
    Route::get('/depenses/{depense}/show', DepenseShow::class)->name('depenses.show');
    Route::get('/depenses', DepenseList::class)->name('depenses.index');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/revenus/create', RevenuForm::class)->name('revenus.create');
    Route::get('/revenus/{revenu}/show', RevenuShow::class)->name('revenus.show');
    Route::get('/revenus', RevenuList::class)->name('revenus.index');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/membres', ListeMembres::class)->name('membre.index');
    Route::get('/membre/add', AddEditMembre::class)->name('profile.edit');
    Route::get('/membre/add', AddEditMembre::class)->name('membre.create');
    Route::get('/membre/{membre}', ShowMembre::class)->name('membre.show');
    Route::get('/membre/{membre}/edit', AddEditMembre::class)->name('membre.edit');
    Route::get('/pdf/membres/{membre}/fiche', MembrePdfController::class)->name('membre.fiche.pdf');

});

Route::middleware(['auth'])->group(function () {
    Route::get('/agents', AgentList::class)->name('agents.index');
    Route::get('/membres/{membre}/agent/create', AgentForm::class)->name('agent.create');
    Route::get('/agents/{agent}/edit', AgentForm::class)->name('agent.edit');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/agences', AgenceList::class)->name('agences.index');
    Route::get('/agences/create', AgenceForm::class)->name('agence.create');
    Route::get('/agences/{agence}/edit', AgenceForm::class)->name('agence.edit');
    Route::get('/agences/{agence}/show', AgenceShow::class)->name('agence.show');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/depenses/typesdepense', TypesDepenseList::class)->name('types-depense.index');
    Route::get('/depenses/typesdepense/add', TypesDepenseForm::class)->name('types-depense.create');
    Route::get('/depenses/typesdepense/{typesDepense}/edit', TypesDepenseForm::class)->name('types-depense.edit');
    Route::get('/depenses/typesdepense/{typesDepense}/show', TypesDepenseShow::class)->name('types-depense.show');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/revenus/typesrevenu', TypesRevenuList::class)->name('types-revenu.index');
    Route::get('/revenus/typesrevenu/add', TypesRevenuForm::class)->name('types-revenu.create');
    Route::get('/revenus/typesrevenu/{typesRevenu}/edit', TypesRevenuForm::class)->name('types-revenu.edit');
    Route::get('/revenus/typesrevenu/{typesRevenu}/show', TypesRevenuShow::class)->name('types-revenu.show');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/comptes', CompteList::class)->name('comptes.index');
    Route::get('/compte/{compte}', ShowCompte::class)->name('compte.show');
    Route::get('/membres/{membre}/comptes/create', AddCompte::class)->name('compte.create');
    Route::get('comptes/{compte}/releve-pdf', [ReleveComptePdfController::class, 'download'])
        ->name('compte.releve-pdf');


});

Route::middleware(['auth'])->group(function () {
    Route::get('/transaction/index', TransactionListing::class)->name('epargne.transactions.index');
    Route::get('/transaction/list', TransactionsList::class)->name('transaction.list');
    Route::get('/transaction/depot/add', TransactionForm::class)->name('epargne.depot.create');
    Route::get('/transactions/retrait/add', TransactionForm::class)
        ->defaults('type', 'RETRAIT')
        ->name('epargne.retrait.create');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/credit/pret/index', CreditsList::class)->name('credit.pret.index');
    Route::get('/credit/remboursement/index', RemboursementList::class)->name('credit.remboursement.index');
    Route::get('/membre/{membre}/credit/create', CreditCreate::class)->name('credit.pret.create');
    Route::get('/credit/{credit}/show', CreditShow::class)->name('credit.show');
});


require __DIR__.'/auth.php';
