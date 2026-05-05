<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('account_daily_balances', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('agence_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cloture_comptable_id')  // journee comptable
                  ->constrained('clotures_comptables')
                  ->cascadeOnDelete();

            $table->string('monnaie', 3); // CDF ou USD

            // Soldes de début de journée (soit le solde final de la veille)
            $table->decimal('solde_debut', 15, 2)->default(0);

            // Totaux des mouvements du jour (débit et crédit) dans la devise
            $table->decimal('total_debit_jour', 15, 2)->default(0);
            $table->decimal('total_credit_jour', 15, 2)->default(0);

            // Solde final calculé = solde_debut + total_debit_jour - total_credit_jour
            $table->decimal('solde_fin', 15, 2)->default(0);

            $table->timestamps();

            // Unicité par (compte, agence, devise, jour)
            $table->unique(['account_id', 'agence_id', 'monnaie', 'cloture_comptable_id'], 'daily_balance_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('account_daily_balances');
    }
};