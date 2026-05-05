<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('journal_entry_lines', function (Blueprint $table) {
            $table->id();

            $table->foreignId('journal_entry_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('account_id')
                ->constrained('accounts');

            // Débit / Crédit
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);

            // Multi-devise
            $table->string('monnaie', 3); // USD, CDF, double devise parfaitement gérable par filtrage
            $table->decimal('taux_change', 15, 6)->nullable();

            // Montant converti (très important pour reporting)
            $table->decimal('montant_base', 15, 2)->nullable();

            $table->timestamps();

            $table->index(['account_id', 'monnaie']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entry_lines');
    }
};
