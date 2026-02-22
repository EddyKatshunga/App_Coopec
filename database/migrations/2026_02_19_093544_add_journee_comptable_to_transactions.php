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
        // Pour chaque table de transaction
        $tables = ['revenus', 'depenses', 'credits', 'credit_remboursements', 'transactions'];
        
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->foreignId('journee_comptable_id')
                    ->after('id')
                    ->constrained('clotures_comptables')
                    ->onDelete('restrict');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['revenus', 'depenses', 'credits', 'credit_remboursements', 'transactions'];
        
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropForeign(['journee_comptable_id']);
                $table->dropColumn('journee_comptable_id');
            });
        }
    }
};
