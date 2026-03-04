<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'agences',
            'agents',
            'clotures_comptables',
            'comptes',
            'credits',
            'credit_remboursements',
            'depenses',
            'membres',
            'photos',
            'revenus',
            'transactions',
            'types_depenses',
            'types_revenus',
            'zones',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->ulid('uuid')->unique()->nullable()->after('id');
            });
        }
    }

    public function down(): void
    {
        $tables = [
            'agences',
            'agents',
            'clotures_comptables',
            'comptes',
            'credits',
            'credit_remboursements',
            'depenses',
            'membres',
            'photos',
            'revenus',
            'transactions',
            'types_depenses',
            'types_revenus',
            'zones',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('uuid');
            });
        }
    }
};