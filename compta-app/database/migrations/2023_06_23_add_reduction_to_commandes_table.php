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
            $table->decimal('reduction_pourcentage', 5, 2)->default(0)->after('vehicule_id');
            $table->decimal('prix_final', 10, 2)->nullable()->after('reduction_pourcentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->dropColumn('reduction_pourcentage');
            $table->dropColumn('prix_final');
        });
    }
};
