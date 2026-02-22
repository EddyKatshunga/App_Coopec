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
        Schema::table('historique_roles', function (Blueprint $table) {
            // Suppression des anciennes colonnes string
            $table->dropColumn(['ancien_role', 'nouveau_role']);

            // Création des nouvelles colonnes foreignId avec contraintes
            $table->foreignId('ancien_role')
                  ->nullable()
                  ->constrained('roles')  // clé étrangère vers roles
                  ->onDelete('restrict');

            $table->foreignId('nouveau_role')
                  ->constrained('roles')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historique_roles', function (Blueprint $table) {
            // Supprimer les clés étrangères d'abord
            $table->dropForeign(['ancien_role']);
            $table->dropForeign(['nouveau_role']);

            // Supprimer les colonnes foreignId
            $table->dropColumn(['ancien_role', 'nouveau_role']);

            // Recréer les colonnes string comme à l'origine
            $table->string('ancien_role')->nullable();
            $table->string('nouveau_role');
        });
    }
};
