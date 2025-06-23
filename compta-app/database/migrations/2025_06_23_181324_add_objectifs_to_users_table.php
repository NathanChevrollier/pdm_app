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
            $table->decimal('objectif_ventes', 10, 2)->nullable()->default(0)->comment('Objectif de ventes hebdomadaires en euros');
            $table->integer('objectif_vehicules')->nullable()->default(0)->comment('Objectif de nombre de véhicules à vendre par semaine');
            $table->decimal('objectif_commission', 10, 2)->nullable()->default(0)->comment('Objectif de commission hebdomadaire en euros');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['objectif_ventes', 'objectif_vehicules', 'objectif_commission']);
        });
    }
};
