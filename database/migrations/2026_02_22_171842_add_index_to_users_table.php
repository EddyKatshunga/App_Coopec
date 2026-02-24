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
        // On ajoute uniquement l'index sur le nom (table users)
        Schema::table('users', function (Blueprint $table) {
            $table->index('name'); 
        });

        // On ajoute les index sur les colonnes existantes (table comptes)
        Schema::table('comptes', function (Blueprint $table) {
            $table->index('numero_compte');
            $table->index('intitule');
        });

        // On ajoute l'index sur le numéro d'identification (table membres)
        Schema::table('membres', function (Blueprint $table) {
            $table->index('numero_identification');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Il est important de supprimer les index si on fait un rollback
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        Schema::table('comptes', function (Blueprint $table) {
            $table->dropIndex(['numero_compte']);
            $table->dropIndex(['intitule']);
        });

        Schema::table('membres', function (Blueprint $table) {
            $table->dropIndex(['numero_identification']);
        });
    }
};