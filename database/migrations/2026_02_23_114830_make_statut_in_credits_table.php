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
            $table->enum('statut', ['en_cours', 'en_retard', 'termine_en_retard', 'termine', 'termine_negocie'])
                    ->default('en_cours');
            $table->foreignId('agent_id') //L'agent ayant validé le Crédit
                  ->constrained()
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credits', function (Blueprint $table) {
            $table->dropColumn(['statut']);
             // Supprimer les clés étrangères d'abord
            $table->dropForeign(['agent_id']);
            $table->dropColumn(['agent_id']);
        });
    }
};
