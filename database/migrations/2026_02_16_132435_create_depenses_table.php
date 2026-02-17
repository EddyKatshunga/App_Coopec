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
        Schema::create('depenses', function (Blueprint $table) {
            $table->id();
            // Informations financières
            $table->date('date_operation'); 
            $table->decimal('montant', 15, 2);
            $table->enum('monnaie', ['CDF', 'USD']);
            $table->string('libelle');
            $table->string('reference')->unique()->nullable();
            $table->text('description')->nullable();

            // Clés étrangères
            // 1. La catégorie liée
            $table->foreignId('types_depense_id')
                ->constrained('types_depenses')
                ->onDelete('restrict');

            // 2. Le bénéficiaire (Agent qui a reçu l'argent pour effectuer la dépense)
            $table->foreignId('beneficiaire_id')
                ->constrained('agents')
                ->onDelete('restrict');

            // 4. L'agence
            $table->foreignId('agence_id')
                ->constrained('agences')
                ->onDelete('restrict');

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
        Schema::dropIfExists('depenses');
    }
};
