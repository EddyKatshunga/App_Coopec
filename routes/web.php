<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CreditPrintController;
use App\Http\Controllers\ImpressionController;
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
use App\Livewire\Transactions\TransactionListing;
use App\Livewire\Transactions\TransactionsList;
use App\Livewire\Agence\AgenceForm;
use App\Livewire\Agence\AgenceList;
use App\Livewire\Agence\AgenceShow;
use App\Livewire\Admin\PermissionMatrix;
use App\Livewire\Agents\AgentShow;
use App\Livewire\Clotures\CloturesForm;
use App\Livewire\Clotures\CloturesList;
use App\Livewire\Clotures\CloturesShow;
use App\Livewire\Historiques\HistoriqueRoleDashboard;
use App\Livewire\Membres\ChangePassword;
use App\Livewire\Zones\ZoneForm;
use App\Livewire\Zones\ZoneList;
use App\Livewire\Zones\ZoneShow;
use App\Livewire\Photos\{PhotoList, PhotoForm, PhotoShow};
use App\Livewire\Remboursements\RemboursementForm;
use App\Livewire\Remboursements\RemboursementList;
use App\Livewire\Remboursements\RemboursementShow;
use App\Livewire\Transactions\TransactionShow;
use App\Models\CreditRemboursement;
use App\Models\HistoriqueRole;
use Livewire\Livewire;

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
    Route::get('/users/{user}/photos', PhotoList::class)->name('photos.index');
    Route::get('/users/{user}/photos/create', PhotoForm::class)->name('photos.create');
    Route::get('/users/{user}/photos/{photo}/edit', PhotoForm::class)->name('photos.edit');
    Route::get('/photos/{id}', PhotoShow::class)->name('photos.show');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/clotures/ouverture/{agence}', CloturesForm::class)->name('clotures.ouvrir');
    Route::get('/clotures/edit/{cloture}', CloturesForm::class)->name('clotures.valider');
    Route::get('/clotures/{cloture}/show', CloturesShow::class)->name('clotures.show');
    Route::get('/clotures', CloturesList::class)->name('clotures.index');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/agences/zones/', ZoneList::class)->name('agences.zones.index');
    Route::get('/agences/zones/{zone}/show/', ZoneShow::class)->name('agences.zones.show');
    Route::get('/agences/{agenceUuid}/zones/create', ZoneForm::class)->name('agences.zones.create');
    Route::get('/agences/zones/{zoneUuid}/edit', ZoneForm::class)->name('agences.zones.edit');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/membres/export/pdf', [MembrePdfController::class, 'index'])->name('membres.pdf.index');
    Route::get('/membres/{membre}/pdf', [MembrePdfController::class, 'fiche'])->name('membres.pdf.fiche');
    Route::get('/membres', ListeMembres::class)->name('membre.index');
    Route::get('/membre/add', AddEditMembre::class)->name('profile.edit');
    Route::get('/membre/add', AddEditMembre::class)->name('membre.create');
    Route::get('/membre/{membre}', ShowMembre::class)->name('membre.show');
    Route::get('/membre/{membre}/edit', AddEditMembre::class)->name('membre.edit');
    Route::get('membres/{membre}/securite', ChangePassword::class)->name('membres.change-password');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/agents', AgentList::class)->name('agents.index');
    Route::get('/membres/{membre}/agent/create', AgentForm::class)->name('agent.create');
    Route::get('/agents/{agent}/edit', AgentForm::class)->name('agent.edit');
    Route::get('/agents/{agent}/show', AgentShow::class)->name('agent.show');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/agences', AgenceList::class)->name('agences.index');
    Route::get('/agences/create', AgenceForm::class)->name('agence.create');
    Route::get('/agences/{agence}/edit', AgenceForm::class)->name('agence.edit');
    Route::get('/agences/{agence}/show', AgenceShow::class)->name('agence.show');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/accounts', App\Livewire\Account\AccountIndex::class)->name('accounts.index');
    Route::get('/compte-resultat', App\Livewire\Account\IncomeStatement::class)->name('compte.resultat.index');
    Route::get('/compte-tiers', App\Livewire\Account\TiersDashboard::class)->name('compte.tiers.index');
    Route::get('/accounts/create', App\Livewire\Account\AccountForm::class)->name('accounts.create');
    Route::get('/journal', App\Livewire\Account\JournalIndex::class)->name('journal.index');
    Route::get('/journal/{entry}/show', App\Livewire\Account\JournalShow::class)->name('journal.show');
    Route::get('/accounts/{account}/edit', App\Livewire\Account\AccountForm::class)->name('accounts.edit');
    Route::get('/accounts/{account}/show', App\Livewire\Account\AccountShow::class)->name('accounts.show');
});

Route::middleware(['auth'])->prefix('accounting')->name('accounting.')->group(function () {
    Route::get('/avance', App\Livewire\Accounting\AvanceSalaireComponent::class)->name('avance');
    Route::get('/change', App\Livewire\Accounting\ChangeComponent::class)->name('change');
    Route::get('/immobilisation', App\Livewire\Accounting\ImmobilisationComponent::class)->name('immobilisation');
    Route::get('/reglement', App\Livewire\Accounting\ReglementTiersComponent::class)->name('reglement');
    Route::get('/charge', App\Livewire\Accounting\ChargeComponent::class)->name('charge');
    Route::get('/produit', App\Livewire\Accounting\ProduitComponent::class)->name('produit');
    Route::get('/transfert', App\Livewire\Accounting\TransfertComponent::class)->name('transfert');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/comptes', CompteList::class)->name('comptes.index');
    Route::get('/compte/{compte}', ShowCompte::class)->name('compte.show');
    Route::get('/membres/{membre}/comptes/create', AddCompte::class)->name('compte.create');
    Route::get('comptes/{compte}/releve-pdf', [ReleveComptePdfController::class, 'download'])
        ->name('compte.releve-pdf');


});

Route::middleware(['auth'])->group(function () {
    Route::get('/transaction/index', TransactionSList::class)->name('epargne.transactions.index');
    Route::get('/transaction/list', TransactionsList::class)->name('transaction.list');
    Route::get('/transaction/{transaction}/show', TransactionShow::class)->name('transaction.show');
    Route::get('/compte/{compte}/transaction/depot/create', TransactionForm::class)
        ->name('epargne.depot.create'); // Le type sera 'DEPOT' par défaut dans le mount
    Route::get('/compte/{compte}/transaction/retrait/create', TransactionForm::class)
        ->name('epargne.retrait.create')
        ->defaults('type', 'RETRAIT');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/credit/pret/index', CreditsList::class)->name('credit.pret.index');
    Route::get('/membre/{membre}/credit/create', CreditCreate::class)->name('credit.pret.create');
    Route::get('/credit/{credit}/show', CreditShow::class)->name('credit.show');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/credit/{credit}/remboursement/create', RemboursementForm::class)->name('remboursement.create');
    Route::get('/remboursement/{remboursement}/show', RemboursementShow::class)->name('remboursement.show');
    Route::get('/remboursements', RemboursementList::class)->name('remboursements.index');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/historiquesroles', HistoriqueRoleDashboard::class)->name('historiquesroles.index');
});

Route::get('/membres/imprimer-liste', function() {
    $membres = \App\Models\Membre::with('user', 'agent')->get();
    $statsSexe = $membres->groupBy('sexe')->map->count();
    return view('impressions.liste-membres', [
        'membres' => $membres,
        'nbHommes' => $statsSexe->get('M', 0),
        'nbFemmes' => $statsSexe->get('F', 0),
    ]);
})->name('membres.print-all');

Route::get('/remboursements/{remboursement}/imprimer', function (CreditRemboursement $remboursement) {
    $remboursement->load(['credit.membre', 'agence', 'zone', 'agent']);
    return view('impressions.recu-paiement', compact('remboursement'));
})->name('remboursements.print');

Route::get('/impressions/releve/{cloture}/{type}', [ImpressionController::class, 'releve'])
    ->name('impressions.releve');

Route::get('/impressions/releve-compte/{compte}/{debut?}/{fin?}/{monnaie?}', [ImpressionController::class, 'releveIndividuel'])
    ->name('impressions.releve.compte');
Route::get('/impressions/rapport-journalier/{cloture}', [ImpressionController::class, 'rapportJournalier'])
    ->name('impressions.rapport.journalier');
Route::get('/impressions/membre/{membre}', [ImpressionController::class, 'ficheMembre'])
    ->name('impressions.membre.fiche');
Route::get('/impressions/periodique/{agenceId}/{debut}/{fin}', [ImpressionController::class, 'rapportPeriodique'])
    ->name('impressions.periodique');
Route::get('/credits/{credit:uuid}/print', function (App\Models\Credit $credit) {
    return view('impressions.credit-dossier', compact('credit'));
})->name('credit.print')->middleware(['auth']);
Route::get('/impressions/zones', [CreditPrintController::class, 'index'])->name('impressions.zones.index');
Route::get('/impressions/zones/{zone:uuid}', [CreditPrintController::class, 'show'])->name('impressions.zones.show');
    
require __DIR__.'/auth.php';
