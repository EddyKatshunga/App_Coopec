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
        Schema::table('clotures_comptables', function (Blueprint $table) {
            // On supprime et on recrée directement
            $table->dropColumn('statut');
        });

        Schema::table('clotures_comptables', function (Blueprint $table) {
            $table->enum('statut', ['ouverte', 'cloturee', 'verouillee'])
                ->default('ouverte')
                ->after('id'); // Optionnel : pour garder la colonne au bon endroit
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('clotures_comptables', function (Blueprint $table) {
            $table->dropColumn('statut');
        });
        
        Schema::table('clotures_comptables', function (Blueprint $table) {
            $table->enum('statut', ['ouverte', 'cloturee'])->default('ouverte');
        });
    }
};
