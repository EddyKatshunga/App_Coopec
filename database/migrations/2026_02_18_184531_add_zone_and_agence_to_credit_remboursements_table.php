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
        Schema::table('credit_remboursements', function (Blueprint $table) {
            // Ajout de la colonne zone_id
            $table->foreignId('zone_id')->after('agent_id')
                  ->nullable() // Rendre nullable si la zone n'est pas obligatoire
                  ->constrained('zones')
                  ->onDelete('restrict'); // Optionnel: supprime les remboursements si la zone est supprimée

            // Ajout de la colonne agence_id
            $table->foreignId('agence_id')->after('zone_id')
                  ->nullable() // Rendre nullable si l'agence n'est pas obligatoire
                  ->constrained('agences')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credit_remboursements', function (Blueprint $table) {
            // Suppression des contraintes et des colonnes lors du rollback
            $table->dropForeign(['zone_id']);
            $table->dropColumn('zone_id');
            
            $table->dropForeign(['agence_id']);
            $table->dropColumn('agence_id');
        });
    }
};
