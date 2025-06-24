<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Objectif extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'objectif_ventes',
        'objectif_vehicules',
        'objectif_commission',
        'objectif_benefice',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'objectif_ventes' => 'decimal:2',
        'objectif_commission' => 'decimal:2',
        'objectif_benefice' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Récupère les objectifs actifs ou crée un enregistrement par défaut si aucun n'existe
     *
     * @return Objectif
     */
    public static function getActiveObjectifs(): self
    {
        $objectifs = self::where('is_active', true)->first();
        
        if (!$objectifs) {
            $objectifs = self::create([
                'objectif_ventes' => 500000, // 500 000 € par défaut
                'objectif_vehicules' => 20,   // 20 véhicules par défaut
                'objectif_commission' => 50000, // 50 000 € par défaut
                'objectif_benefice' => 100000, // 100 000 € par défaut
                'is_active' => true,
            ]);
        }
        
        return $objectifs;
    }
}
