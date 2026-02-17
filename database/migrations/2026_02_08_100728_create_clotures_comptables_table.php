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
        Schema::create('clotures_comptables', function (Blueprint $table) {
            $table->id();

            $table->foreignId('agence_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->date('date_cloture');

            /** =========================
             * REPORTS (début de journée)
             * ========================= */
            $table->decimal('report_coffre_cdf', 18, 2)->default(0);
            $table->decimal('report_coffre_usd', 18, 2)->default(0);

            $table->decimal('report_epargne_cdf', 18, 2)->default(0);
            $table->decimal('report_epargne_usd', 18, 2)->default(0);

            /** =========================
             * TOTAUX DU JOUR
             * ========================= */
            $table->decimal('total_depot_cdf', 18, 2)->default(0);
            $table->decimal('total_depot_usd', 18, 2)->default(0);

            $table->decimal('total_retrait_cdf', 18, 2)->default(0);
            $table->decimal('total_retrait_usd', 18, 2)->default(0);

            $table->decimal('total_credit_cdf', 18, 2)->default(0);
            $table->decimal('total_credit_usd', 18, 2)->default(0);

            $table->decimal('total_remboursement_cdf', 18, 2)->default(0);
            $table->decimal('total_remboursement_usd', 18, 2)->default(0);

            $table->decimal('total_depense_cdf', 18, 2)->default(0);
            $table->decimal('total_depense_usd', 18, 2)->default(0);

            $table->decimal('total_revenu_cdf', 18, 2)->default(0);
            $table->decimal('total_revenu_usd', 18, 2)->default(0);

            /** =========================
             * SOLDES DE FIN DE JOURNÉE
             * ========================= */
            $table->decimal('solde_epargne_cdf', 18, 2)->default(0);
            $table->decimal('solde_epargne_usd', 18, 2)->default(0);

            $table->decimal('solde_coffre_cdf', 18, 2)->default(0);
            $table->decimal('solde_coffre_usd', 18, 2)->default(0);

            /** =========================
             * STATUT DE LA JOURNÉE
             * ========================= */
            // 'ouverte' : les opérations sont autorisées
            // 'cloturee' : plus aucune modification possible
            $table->enum('statut', ['ouverte', 'cloturee'])->default('ouverte');

            // Écart de caisse (Physique vs Théorique)
            $table->decimal('physique_coffre_cdf', 18, 2)->nullable();
            $table->decimal('physique_coffre_usd', 18, 2)->nullable();

            // Note d'explication si un écart est constaté
            $table->text('observation_cloture')->nullable();

            /** =========================
             * MÉTADONNÉES
             * ========================= */
            $table->foreignId('created_by')
                ->constrained('users');

            $table->foreignId('updated_by')
                ->constrained('users');

            $table->timestamps();

            $table->unique(['agence_id', 'date_cloture']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clotures_comptables');
    }
};
