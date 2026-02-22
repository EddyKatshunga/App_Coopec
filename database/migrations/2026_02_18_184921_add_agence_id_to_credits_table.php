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
        Schema::table('credits', function (Blueprint $table) {
            // Ajoute la colonne agence_id en tant que clé étrangère
            // Elle fait référence à la colonne 'id' de la table 'agences'
            $table->foreignId('agence_id')
                  ->after('zone_id')
                  ->nullable()
                  ->constrained('agences')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credits', function (Blueprint $table) {
            //
        });
    }
};
