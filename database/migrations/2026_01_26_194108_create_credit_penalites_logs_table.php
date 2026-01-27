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
        Schema::create('credit_penalites_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('credit_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->integer('jours_retard');
            $table->decimal('base_penalisee', 15, 2);
            $table->decimal('montant_penalite', 15, 2);

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_penalites_logs');
    }
};
