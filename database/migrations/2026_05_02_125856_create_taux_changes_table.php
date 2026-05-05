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
        Schema::create('taux_changes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->date('date_application');
            
            $table->decimal('taux_achat', 15, 4); // Le taux auquel la banque/bureau achète les devises
            $table->decimal('taux_vente', 15, 4); // Le taux auquel la banque/bureau vend les devises
            $table->decimal('taux_moyen', 15, 4)->storedAs('(taux_achat + taux_vente) / 2'); // Utile pour la valorisation comptable
            
            $table->boolean('est_actif')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->index('date_application');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taux_changes');
    }
};
