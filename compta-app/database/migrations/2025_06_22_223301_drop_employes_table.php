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
        Schema::dropIfExists('employes');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cette migration supprime définitivement la table employes
        // La recréation de la table n'est pas implémentée car nous migrons vers la table users
    }
};
