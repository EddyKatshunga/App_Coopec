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
            $table->foreignId('credit_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('agent_id')
                ->constrained()->nullable();

            /* ================= DONNÉES PAIEMENT ================= */
            $table->date('date_paiement');

            // Montant total versé par le membre
            $table->decimal('montant', 15, 2);

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
