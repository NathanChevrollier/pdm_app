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
        Schema::table('users', function (Blueprint $table) {
            // Vérifier si les colonnes existent déjà avant de les ajouter
            if (!Schema::hasColumn('users', 'prenom')) {
                $table->string('prenom')->nullable()->after('nom');
            }
            
            if (!Schema::hasColumn('users', 'statut')) {
                $table->string('statut')->default('Vendeur')->after('email_verified_at');
            }
            
            if (!Schema::hasColumn('users', 'commission')) {
                $table->decimal('commission', 5, 2)->default(0)->after('statut');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'prenom')) {
                $table->dropColumn('prenom');
            }
            
            if (Schema::hasColumn('users', 'statut')) {
                $table->dropColumn('statut');
            }
            
            if (Schema::hasColumn('users', 'commission')) {
                $table->dropColumn('commission');
            }
        });
    }
};
