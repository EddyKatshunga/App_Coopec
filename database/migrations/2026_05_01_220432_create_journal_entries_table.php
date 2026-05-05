<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->date('date_operation');
            $table->string('libelle');

            $table->foreignId('agence_id')->constrained()->cascadeOnDelete();

            // Lien avec journée comptable
            $table->foreignId('journee_comptable_id')
                ->constrained('clotures_comptables')
                ->nullOnDelete();
            $table->timestamps();

            $table->index(['agence_id', 'date_operation']);
            $table->index(['agence_id', 'journee_comptable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
