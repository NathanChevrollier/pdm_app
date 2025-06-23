<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modifier la colonne statut pour accepter toutes les valeurs nécessaires
        Schema::table('users', function (Blueprint $table) {
            // Supprimer la contrainte enum sur la colonne statut
            if (DB::getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE users MODIFY statut VARCHAR(20) NOT NULL DEFAULT "vendeur"');
            } else {
                // Pour les autres bases de données
                $table->string('statut', 20)->default('vendeur')->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restaurer la contrainte enum si nécessaire
        Schema::table('users', function (Blueprint $table) {
            if (DB::getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE users MODIFY statut ENUM("admin", "gerant", "co-gerant", "vendeur", "stagiaire") NOT NULL DEFAULT "vendeur"');
            } else {
                // Pour les autres bases de données
                $table->string('statut')->default('vendeur')->change();
            }
        });
    }
};
