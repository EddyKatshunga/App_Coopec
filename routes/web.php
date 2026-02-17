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
    Route::get('/membres', ListeMembres::class)->name('membre.index');
    Route::get('/membre/add', AddEditMembre::class)->name('profile.edit');
    Route::get('/membre/add', AddEditMembre::class)->name('membre.create');
    Route::get('/membre/{membre}', ShowMembre::class)->name('membre.show');
    Route::get('/membre/{membre}/edit', AddEditMembre::class)->name('membre.edit');
    Route::get('/pdf/membres/{membre}/fiche', MembrePdfController::class)
    ->name('membre.fiche.pdf');

});

Route::middleware(['auth'])->group(function () {
    Route::get('/agents', AgentList::class)->name('agents.index');
    Route::get('/agent/add', AgentForm::class)->name('agent.create');
    Route::get('/agent/{agent}/edit', AgentForm::class)->name('agent.edit');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/depenses/typesdepense', TypesDepenseList::class)->name('types-depense.index');
    Route::get('/depenses/typesdepense/add', TypesDepenseForm::class)->name('types-depense.create');
    Route::get('/depenses/typesdepense/{typesdepense}/edit', TypesDepenseForm::class)->name('types-depense.edit');
    Route::get('/depenses/typesdepense/{typesdepense}/show', TypesDepenseShow::class)->name('types-depense.show');
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
