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
        Schema::create('types_revenus', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->unique(); // ex: Vente de carnets, Frais de dossier, Dons
            $table->string('code_comptable')->nullable(); // Important pour la balance
            $table->boolean('est_actif')->default(true);

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
        Schema::dropIfExists('types_revenus');
    }
};
