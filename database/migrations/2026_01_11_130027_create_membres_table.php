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
            $table->string('numero_identification')->unique(); // Le numéro d'identification, doit être unique
            $table->enum('qualite', ['Effectif', 'Auxiliaire']);
            $table->enum('sexe', ['M', 'F']);
            
            $table->string('lieu_de_naissance');
            $table->date('date_de_naissance');
            $table->text('adresse');
            $table->string('telephone')->nullable();
            $table->string('activites');
            $table->string('adresse_activite');
            $table->date('date_adhesion');
            $table->foreignId('agent_parrain_id')
                ->nullable()
                ->constrained('agents')
                ->onDelete('set null');

            // 🔐 Audit
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
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
