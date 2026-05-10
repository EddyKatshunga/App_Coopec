<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Les tables à modifier
     */
    protected $tables = [
        'credits', 
        'credit_remboursements', 
        'transactions'
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreignId('journal_entry_id')
                    ->after('id')
                    ->constrained()
                    ->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                // Il faut d'abord supprimer la clé étrangère avant la colonne
                $table->dropForeign([ 'journal_entry_id' ]);
                $table->dropColumn('journal_entry_id');
            });
        }
    }
};