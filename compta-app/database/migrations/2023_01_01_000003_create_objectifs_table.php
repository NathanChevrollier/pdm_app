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
        Schema::create('objectifs', function (Blueprint $table) {
            $table->id();
            $table->decimal('objectif_ventes', 15, 2)->default(0)->comment('Objectif global de ventes en euros');
            $table->integer('objectif_vehicules')->default(0)->comment('Objectif global de véhicules vendus');
            $table->decimal('objectif_commission', 15, 2)->default(0)->comment('Objectif global de commissions en euros');
            $table->decimal('objectif_benefice', 15, 2)->default(100000)->comment('Objectif global de bénéfice en euros');
            $table->boolean('is_active')->default(true)->comment('Si les objectifs sont actifs');
            $table->timestamps();
        });

        // Insérer un objectif par défaut
        DB::table('objectifs')->insert([
            'objectif_ventes' => 1000000.00,
            'objectif_vehicules' => 50,
            'objectif_commission' => 100000.00,
            'objectif_benefice' => 200000.00,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('objectifs');
    }
};
