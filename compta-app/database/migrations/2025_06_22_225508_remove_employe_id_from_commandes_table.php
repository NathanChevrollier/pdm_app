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
        Schema::table('commandes', function (Blueprint $table) {
            // Supprimer la clé étrangère avant de supprimer la colonne
            $table->dropForeign(['employe_id']);
            $table->dropColumn('employe_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            // Recréer la colonne et la clé étrangère si on revient en arrière
            $table->foreignId('employe_id')->nullable()->after('nom_client');
            $table->foreign('employe_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
