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
             * STATUT DE LA JOURNÉE
             * ========================= */
            // 'ouverte' : les opérations sont autorisées
            // 'cloturee' : plus aucune modification possible
            $table->enum('statut', ['ouverte', 'cloturee', 'verouillee'])
                ->default('ouverte');

            // Note d'explication si un écart de caisse est constaté par exemple
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
