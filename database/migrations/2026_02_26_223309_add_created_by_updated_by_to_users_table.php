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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()
                  ->constrained('users')
                  ->onDelete('restrict');
                  
            $table->foreignId('updated_by')->nullable()
                  ->constrained('users')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Supprimer les clés étrangères
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            
            // Supprimer les colonnes
            $table->dropColumn(['created_by', 'updated_by']);
        });
    }
};
