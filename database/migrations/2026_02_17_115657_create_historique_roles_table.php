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
        Schema::create('historique_roles', function (Blueprint $table) {
            $table->id();

            // L'utilisateur concerné
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Détails du rôle
            $table->string('ancien_role')->nullable();
            $table->string('nouveau_role');

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
        Schema::dropIfExists('historique_roles');
    }
};
