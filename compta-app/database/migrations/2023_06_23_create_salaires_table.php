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
        Schema::create('salaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('semaine');  // Format YYYY-WXX (ex: 2023-W25)
            $table->decimal('montant', 10, 2)->default(0);
            $table->decimal('commission', 10, 2)->default(0);
            $table->boolean('est_paye')->default(false);
            $table->timestamp('date_paiement')->nullable();
            $table->timestamps();
            
            // Index pour accélérer les recherches
            $table->index(['user_id', 'semaine']);
            $table->index('est_paye');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salaires');
    }
};
