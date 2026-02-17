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
        Schema::create('agence_direction_histories', function (Blueprint $table) {
            $table->id();
            // L'agence concernée
            $table->foreignId('agence_id')->constrained('agences')->onDelete('cascade');

            // L'ancien directeur (peut être null si c'est la création de l'agence)
            $table->foreignId('ancien_directeur_id')
                  ->nullable()
                  ->constrained('agents')
                  ->onDelete('set null');

            // Le nouveau directeur
            $table->foreignId('nouveau_directeur_id')
                  ->constrained('agents')
                  ->onDelete('cascade');

            $table->foreignId('created_by')
                ->constrained('users');

            $table->foreignId('updated_by')
                ->constrained('users');
                
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agence_direction_histories');
    }
};
