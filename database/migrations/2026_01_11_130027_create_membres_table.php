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
        Schema::create('membres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('agence_id')->nullable();
            $table->string('numero_identification')->unique();
            $table->string('qualite')->default('Auxiliaire'); //Auxiliaire ou Effectif
            $table->enum('sexe', ['M', 'F']);
            $table->string('lieu_de_naissance')->nullable();
            $table->date('date_de_naissance')->nullable();
            $table->text('adresse')->nullable();
            $table->string('telephone')->nullable();
            $table->string('activites')->nullable();
            $table->string('adresse_activite')->nullable();
            $table->date('date_adhesion')->nullable();
            $table->unsignedBigInteger('agent_parrain_id')->nullable();

            // 🔐 Audit
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            
            $table->index('qualite');
            
            // Index pour les recherches par date d'adhésion (souvent utilisé avec les filtres)
            $table->index('date_adhesion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membres');
    }
};
