<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\LogsActivity;

class Vehicule extends Model
{
    use HasFactory, LogsActivity;
    
    /**
     * Le nom du modèle pour les activités
     * 
     * @return string
     */
    public function getActivityName(): string
    {
        return 'Véhicule';
    }

    protected $fillable = [
        'nom',
        'prix_achat',
        'prix_vente',
    ];

    protected $casts = [
        'prix_achat' => 'decimal:2',
        'prix_vente' => 'decimal:2',
    ];

    public function commandes(): HasMany
    {
        return $this->hasMany(Commande::class);
    }
}
