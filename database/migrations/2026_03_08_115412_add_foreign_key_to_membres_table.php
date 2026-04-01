<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    public function up(): void
    {
        try {
            Schema::table('membres', function (Blueprint $table) {
                $table->foreign('agent_parrain_id')
                      ->references('id')
                      ->on('agents')
                      ->onDelete('set null');
            });
            
        } catch (\Exception $e) {
            // Si l'erreur est due à une contrainte qui existe déjà
            if (str_contains($e->getMessage(), 'Duplicate key name')) {
                return;
            }
            
            // Si l'erreur est due à des tables/colonnes manquantes
            if (str_contains($e->getMessage(), 'Table') && 
                (str_contains($e->getMessage(), 'doesn\'t exist') || 
                 str_contains($e->getMessage(), "doesn't have a column"))) {
                return;
            }
            
            Log::error('Migration failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function down(): void
    {
        try {
            Schema::table('membres', function (Blueprint $table) {
                $table->dropForeign(['agent_parrain_id']);
            });
            
        } catch (\Exception $e) {
            // Si l'erreur est due à une contrainte inexistante
            if (str_contains($e->getMessage(), 'drop foreign') || 
                str_contains($e->getMessage(), 'foreign key constraint')) {
                return;
            }
   
            Log::error('Migration down failed: ' . $e->getMessage());
            throw $e;
        }
    }
};