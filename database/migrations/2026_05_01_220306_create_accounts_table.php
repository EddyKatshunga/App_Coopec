<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->string('numero')->unique(); // ex: 571, 701
            $table->string('nom');

            $table->enum('type', [
                'actif',
                'passif',
                'charge',
                'produit'
            ]);
            $table->foreignId('parent_id')->nullable()->constrained('accounts');
            $table->boolean('est_actif')->default(true);
            $table->timestamps();

            $table->index('nom');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};