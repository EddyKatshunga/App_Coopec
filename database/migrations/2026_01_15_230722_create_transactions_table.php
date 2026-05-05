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
            $table->foreignId('journee_comptable_id')
                    ->constrained('clotures_comptables')
                    ->onDelete('restrict');
            $table->foreignId('compte_id')->constrained('comptes')->onDelete('restrict');
            $table->foreignId('agence_id')->constrained('agences')->onDelete('restrict');
            
            // L'agent commercial/terrain (Optionnel, uniquement pour les collectes)
            $table->foreignId('agent_collecteur_id')
                  ->nullable()
                  ->constrained('agents')
                  ->onDelete('set null');

            // --- DATES ---
            $table->date('date_transaction');

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
            $table->timestamps();

            // 1. Rapports par Agence (Couvre aussi les recherches par agence seule)
            $table->index(['agence_id', 'date_transaction']);

            // 2. Relevés de compte (Le plus utilisé par les clients)
            $table->index(['compte_id', 'date_transaction']); // Mieux que created_at si vous permettez des saisies rétroactives

            // 3. Audit et performance agents
            $table->index(['created_by', 'date_transaction']);

            // 4. Clôture comptable (Pour valider une journée rapidement)
            // agence_id est déjà indexé par constrained(), mais on peut l'optimiser :
            $table->index(['journee_comptable_id', 'statut']); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};