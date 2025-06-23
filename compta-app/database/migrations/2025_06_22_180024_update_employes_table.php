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
        // Modifier la colonne de statut pour inclure plus d'options
        DB::statement("ALTER TABLE employes MODIFY COLUMN statut ENUM('admin', 'employe', 'manager', 'vendeur', 'comptable', 'rh') DEFAULT 'employe'");
        
        // Modifier la plage de la commission pour aller jusqu'à 100%
        DB::statement('ALTER TABLE employes MODIFY COLUMN commission DECIMAL(5,2) UNSIGNED CHECK (commission BETWEEN 0 AND 100)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revenir à la configuration d'origine
        DB::statement("ALTER TABLE employes MODIFY COLUMN statut ENUM('admin', 'employe') DEFAULT 'employe'");
        DB::statement('ALTER TABLE employes MODIFY COLUMN commission DECIMAL(5,2)');
    }
};
