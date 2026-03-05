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
        Schema::table('credit_remboursements', function (Blueprint $table) {
            $table->decimal('reste_penalite', 15, 2)->default(0)->after('montant_penalite_paye');
            $table->decimal('reste_non_alloue', 15, 2)->default(0)->after('montant_capital_paye');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credit_remboursements', function (Blueprint $table) {
            $table->dropColumn(['reste_penalite', 'reste_non_alloue']);
        });
    }
};
