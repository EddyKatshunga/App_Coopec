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

            $table->date('date_credit');
            $table->string('numero_credit')->unique();

            $table->foreignId('membre_id')->constrained()->cascadeOnDelete();
            $table->foreignId('zone_id')->constrained();

            $table->decimal('capital', 15, 2);
            $table->decimal('interet', 15, 2);
            $table->decimal('taux_penalite_journalier', 5, 2); // %

            $table->enum('unite_temps', ['jour', 'semaine', 'mois', 'annee']);
            $table->unsignedInteger('duree');

            $table->date('date_fin_prevue');

            // Garant
            $table->string('garant_nom');
            $table->string('garant_adresse')->nullable();
            $table->string('garant_telephone')->nullable();

            // Gestion humaine
            $table->boolean('negocie')->default(false);
            $table->text('note_negociation')->nullable();
            $table->date('date_cloture_forcee')->nullable();

            $table->timestamps();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
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
