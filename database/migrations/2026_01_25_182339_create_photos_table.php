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
        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('path'); // Chemin vers la photo
            $table->string('original_name')->nullable(); // Nom original du fichier
            $table->string('mime_type')->nullable(); // Type MIME
            $table->unsignedBigInteger('size')->nullable(); // Taille en bytes
            $table->boolean('is_profile')->default(false); // Si c'est la photo de profil
            $table->string('disk')->default('public'); // Disque de stockage (public, s3, etc.)
            $table->text('caption')->nullable(); // Légende/description
            $table->timestamps();
            
            // Index pour optimiser les recherches
            $table->index(['user_id', 'is_profile']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
};
