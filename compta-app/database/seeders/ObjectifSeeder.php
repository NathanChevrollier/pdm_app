<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ObjectifSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
}
