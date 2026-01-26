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
use App\Livewire\Transactions\TransactionListing;
use App\Livewire\Transactions\TransactionsList;

Route::get('/', [HomeController::class, 'index'])->name('public.home');
Route::get('/actualites', [NewsController::class, 'index'])->name('public.news');
Route::get('/contact', [ContactController::class, 'index'])->name('public.contact');

// Admin
Route::middleware(['auth', 'role:pca'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
});

/*
Route::middleware(['auth', 'permission:membre.creer'])->group(function () {
    Route::get('/membres/create', CreateMembre::class)->name('membres.create');
});*/

Route::middleware(['auth'])->group(function () {
    Route::get('/membres', ListeMembres::class)->name('membre.index');
    Route::get('/membre/add', AddEditMembre::class)->name('membre.add');
    Route::get('/membre/{membre}', ShowMembre::class)->name('membre.show');
    Route::get('/membre/{membre}/edit', AddEditMembre::class)->name('membre.edit');
    Route::get('/pdf/membres/{membre}/fiche', MembrePdfController::class)
    ->name('membre.fiche.pdf');

});

Route::middleware(['auth'])->group(function () {
    Route::get('/agents', AgentList::class)->name('agents.index');
    Route::get('/agent/add', AgentForm::class)->name('agent.add');
    Route::get('/agent/{membre}/edit', AgentForm::class)->name('agent.edit');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/comptes', CompteList::class)->name('comptes.index');
    Route::get('/compte/{compte}', ShowCompte::class)->name('compte.show');
    Route::get('/membres/{membre}/comptes/create', AddCompte::class)->name('compte.create');
    Route::get('comptes/{compte}/releve-pdf', [ReleveComptePdfController::class, 'download'])
        ->name('compte.releve-pdf');


});

Route::middleware(['auth'])->group(function () {
    Route::get('/transaction/index', TransactionListing::class)->name('transaction.index');
    Route::get('/transaction/list', TransactionsList::class)->name('transaction.list');
    Route::get('/transaction/depot/add', TransactionForm::class)->name('transaction.depot');
    // Retrait
    Route::get('/transactions/retrait/add', TransactionForm::class)
        ->defaults('type', 'RETRAIT')
        ->name('transaction.retrait');
});


require __DIR__.'/auth.php';
