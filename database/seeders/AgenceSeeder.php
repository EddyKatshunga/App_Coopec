<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AgenceSeeder extends Seeder
{
    public function run(): void
    {
        $userId = 1;

        /**
         * ===========================
         * AGENCE
         * ===========================
         */
        DB::table('agences')->updateOrInsert(
            ['nom' => 'Agence Principale Kikwit'],
            [
                'uuid' => (string) Str::ulid(),
                'code' => 'KKT-001',
                'ville' => 'Kikwit',
                'pays' => 'RDC',
                'created_by' => $userId,
                'updated_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}