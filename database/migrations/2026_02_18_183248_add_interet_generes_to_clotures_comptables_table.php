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
        Schema::table('clotures_comptables', function (Blueprint $table) {
            //Ajout de la colonne après une colonne existante (optionnel)
            $table->decimal('total_interet_generes_cdf', 18, 2)->default(0)->after('total_credit_usd');
			$table->decimal('total_interet_generes_usd', 18, 2)->default(0)->after('total_interet_generes_cdf');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clotures_comptables', function (Blueprint $table) {
            $table->dropColumn('total_interet_generes_cdf');
			$table->dropColumn('total_interet_generes_usd');
        });
    }
};
