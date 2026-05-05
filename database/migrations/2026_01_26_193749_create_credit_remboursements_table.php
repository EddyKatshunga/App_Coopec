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
        Schema::create('credit_remboursements', function (Blueprint $table) {
            $table->id();

            /* ================= RELATIONS ================= */
            $table->foreignId('journee_comptable_id')
                    ->constrained('clotures_comptables')
                    ->onDelete('restrict');
            $table->foreignId('credit_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('agent_id')
                ->constrained()->nullable();
            
            $table->foreignId('zone_id')->after('agent_id')
                  ->nullable()
                  ->constrained('zones')
                  ->onDelete('restrict'); 

            // Ajout de la colonne agence_id
            $table->foreignId('agence_id')->after('zone_id')
                  ->nullable()
                  ->constrained('agences')
                  ->onDelete('restrict');

            /* ================= DONNÉES PAIEMENT ================= */
            $table->date('date_paiement');

            // Montant total versé par le membre
            $table->decimal('montant', 15, 2);
            $table->enum('monnaie', ['CDF', 'USD']);

            /* ================= VENTILATION FINANCIÈRE ================= */
            // Indispensable pour audit & pénalités
            $table->decimal('montant_penalite_payee', 15, 2)->default(0);
            $table->decimal('montant_interet_payee', 15, 2)->default(0);
            $table->decimal('montant_capital_payee', 15, 2)->default(0);

            /* ================= SNAPSHOT COMPTABLE ================= */
            // État AVANT paiement
            $table->decimal('report_avant', 15, 2);

            // État APRÈS paiement (capital + intérêt + pénalités)
            $table->decimal('reste_du_apres', 15, 2);
            $table->decimal('reste_penalite', 15, 2)->default(0)->after('montant_penalite_payee');
            $table->decimal('reste_non_alloue', 15, 2)->default(0)->after('montant_capital_payee');

            /* ================= MÉTADONNÉES ================= */
            $table->enum('mode_paiement', [
                'cash',
                'mpesa',
                'airtel',
                'banque'
            ]);
            
            $table->string('reference_paiement', 50)->nullable();

            $table->timestamps();

            $table->foreignId('created_by')
                ->constrained('users');

            $table->foreignId('updated_by')
                ->constrained('users');

            $table->index(['agence_id', 'date_paiement']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_remboursements');
    }
};
