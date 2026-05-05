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
        // Ajout de la contrainte sur la table membres
        Schema::table('membres', function (Blueprint $table) {
            $table->foreign('agence_id')
                  ->references('id')
                  ->on('agences')
                  ->onDelete('restrict'); 
        });

        // Ajout de la contrainte sur la table comptes
        Schema::table('comptes', function (Blueprint $table) {
            $table->foreign('agence_id')
                  ->references('id')
                  ->on('agences')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('membres', function (Blueprint $table) {
            $table->dropForeign(['agence_id']);
        });

        Schema::table('comptes', function (Blueprint $table) {
            $table->dropForeign(['agence_id']);
        });
    }
};