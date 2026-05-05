<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journee_comptable_id')
                    ->constrained('clotures_comptables')
                    ->onDelete('restrict');
            $table->date('date_credit');
            $table->string('numero_credit')->unique();
            $table->foreignId('user_id')
                  ->after('membre_id')
                  ->constrained()
                  ->onDelete('restrict');
            $table->foreignId('membre_id')->constrained()->cascadeOnDelete();
            $table->foreignId('agence_id')
                  ->constrained('agences')
                  ->onDelete('restrict');
            $table->foreignId('zone_id')->constrained();

            $table->decimal('capital', 15, 2);
            $table->decimal('interet', 15, 2);
            $table->enum('monnaie', ['CDF', 'USD'])->default('CDF');
            $table->decimal('taux_penalite_journalier', 5, 2); // %

            $table->enum('unite_temps', ['jour', 'semaine', 'mois', 'annee']);
            $table->unsignedInteger('duree');

            $table->date('date_fin_prevue');
            $table->decimal('total_remboursement', 15, 2)->default(0.00);

            // Garant
            $table->string('garant_nom');
            $table->string('garant_adresse')->nullable();
            $table->string('garant_telephone')->nullable();

            // Gestion humaine
            $table->boolean('negocie')->default(false);
            $table->text('note_negociation')->nullable();
            $table->date('date_cloture_forcee')->nullable();
            $table->string('statut')->default('en_cours');
            $table->text('observation')->nullable();
            $table->foreignId('agent_id') //L'agent ayant validé le Crédit
                  ->constrained()
                  ->onDelete('restrict');

            $table->timestamps();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');

            // 🔍 INDEX OPTIMISÉS POUR RECHERCHES
            $table->index('date_credit');
            $table->index('date_fin_prevue'); // Crucial pour lister les retards de paiement !
            $table->index(['agence_id', 'zone_id']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credits');
    }
};
