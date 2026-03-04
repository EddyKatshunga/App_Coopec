<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    protected array $tables = [
        'agences', 'agents', 'clotures_comptables', 'comptes',
        'credits', 'credit_remboursements', 'depenses', 'membres',
        'photos', 'revenus', 'transactions', 'types_depenses',
        'types_revenus', 'zones',
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            // 1. Remplir les colonnes UUID/ULID vides si elles existent
            // On génère un ULID pour chaque ligne qui n'en a pas encore
            $rows = DB::table($tableName)->whereNull('uuid')->get();
            
            foreach ($rows as $row) {
                DB::table($tableName)
                    ->where('id', $row->id)
                    ->update(['uuid' => (string) Str::ulid()]);
            }

            // 2. Modifier la colonne pour la rendre obligatoire
            Schema::table($tableName, function (Blueprint $table) {
                $table->ulid('uuid')->nullable(false)->change();
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->ulid('uuid')->nullable()->change();
            });
        }
    }
};