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
        Schema::table('agences', function (Blueprint $table) {
            // Clé étrangère vers la table agents
            // On utilise nullable() au cas où une agence n'a pas encore de directeur affecté
            $table->foreignId('chef_agence_id')
                  ->nullable()
                  ->constrained('agents')
                  ->onDelete('set null');

            // Soldes financiers (Précision de 15 chiffres dont 2 après la virgule)
            $table->decimal('solde_actuel_coffre_cdf', 15, 2)->default(0)->check('solde_actuel_coffre_cdf >= 0');
            $table->decimal('solde_actuel_coffre_usd', 15, 2)->default(0)->check('solde_actuel_coffre_usd >= 0');
            $table->decimal('solde_actuel_epargne_cdf', 15, 2)->default(0)->check('solde_actuel_epargne_cdf >= 0');
            $table->decimal('solde_actuel_epargne_usd', 15, 2)->default(0)->check('solde_actuel_epargne_usd >= 0');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agences', function (Blueprint $table) {
            // Il faut supprimer la contrainte avant la colonne
            $table->dropForeign(['chef_agence_id']);
            $table->dropColumn(['directeur_id', 'solde_actuel_coffre_cdf', 'solde_actuel_coffre_usd', 'solde_actuel_epargne_cdf', 'solde_actuel_epargne_usd']);
        });
    }
};
