<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\LogsActivity;

class Commande extends Model
{
    use HasFactory, LogsActivity;
    
    /**
     * Le nom du modèle pour les activités
     * 
     * @return string
     */
    public function getActivityName(): string
    {
        return 'Commande';
    }
    
    protected $fillable = [
        'nom_client',
        'user_id',
        'vehicule_id',
        'reduction_pourcentage',
        'prix_final',
        'date_commande',
        'statut',
    ];

    protected $casts = [
        'date_commande' => 'datetime',
        'reduction_pourcentage' => 'decimal:2',
        'prix_final' => 'decimal:2',
    ];

    /**
     * Mutateur pour s'assurer que date_commande conserve l'heure
     *
     * @param  string  $value
     * @return void
     */
    public function setDateCommandeAttribute($value)
    {
        $this->attributes['date_commande'] = $value;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Relation de compatibilité pour l'ancien code
     * Cette méthode permet de maintenir la compatibilité avec le code existant
     * qui utilise la relation employe
     */
    public function employe(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function vehicule(): BelongsTo
    {
        return $this->belongsTo(Vehicule::class);
    }
    
    /**
     * Calcule le prix final en appliquant la réduction
     * 
     * @param float $prixVehicule Le prix du véhicule
     * @param float $reduction Le pourcentage de réduction
     * @return float Le prix final après réduction
     */
    public static function calculerPrixFinal(float $prixVehicule, float $reduction): float
    {
        return $prixVehicule * (1 - ($reduction / 100));
    }
}
