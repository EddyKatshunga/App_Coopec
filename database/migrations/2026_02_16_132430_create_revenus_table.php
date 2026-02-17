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
        Schema::create('revenus', function (Blueprint $table) {
            $table->id();
            // Informations financières
            $table->date('date_operation');
            $table->decimal('montant', 15, 2);
            $table->enum('monnaie', ['CDF', 'USD']); 
            $table->string('libelle'); // ex: vente carnet Epargne
            $table->string('reference')->unique()->nullable(); // Pour le suivi papier (numero reçu...)
            $table->text('description')->nullable();

            // Clés étrangères
            // 1. La catégorie de revenu
            $table->foreignId('types_revenu_id')
                ->constrained('types_revenus')
                ->onDelete('restrict');

            // 2. L'agence où l'argent entre
            $table->foreignId('agence_id')
                ->constrained('agences')
                ->onDelete('restrict');
                
            //Audit
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
        Schema::dropIfExists('revenus');
    }
};
