<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            
            // --- RÉFÉRENCES ---
            $table->foreignId('compte_id')->constrained('comptes')->onDelete('restrict');
            $table->foreignId('agence_id')->constrained('agences')->onDelete('restrict');
            
            // L'agent commercial/terrain (Optionnel, uniquement pour les collectes)
            $table->foreignId('agent_collecteur_id')
                  ->nullable()
                  ->constrained('agents')
                  ->onDelete('set null');

            // --- DATES ---
            $table->date('date_transaction'); // Date de l'acte (ne peut être antérieure à la dernière date de transaction)
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            // --- CŒUR FINANCIER ---
            $table->enum('type_transaction', ['DEPOT', 'RETRAIT', 'CONTRE_PASSATION']);
            $table->decimal('montant', 15, 2);
            $table->enum('monnaie', ['CDF', 'USD']); // Alignement avec la table Comptes
            
            // Suivi du solde (La quintessence de l'audit)
            $table->decimal('solde_avant', 15, 2); //A récupérer dans la table Comptes avant l'opération
            $table->decimal('solde_apres', 15, 2);
            
            // --- GESTION DES ERREURS & STATUTS ---
            $table->enum('statut', ['VALIDE', 'ANNULE', 'REVERSAL'])->default('VALIDE');
            
            $table->foreignId('reference_annulation_id')
                  ->nullable()
                  ->constrained('transactions')
                  ->onDelete('set null');

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');

            // --- INDEXATION ---
            $table->index(['compte_id', 'created_at']);
            $table->index(['agent_collecteur_id', 'date_transaction']); // Pour les rapports de performance
            $table->index(['agence_id', 'monnaie']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};