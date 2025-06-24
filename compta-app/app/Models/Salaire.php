<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salaire extends Model
{
    use HasFactory;

    protected $table = 'salaires';

    protected $fillable = [
        'user_id',
        'montant_base',
        'commission',
        'deductions',
        'taxes',
        'montant_final',
        'periode_debut',
        'periode_fin',
        'est_paye',
        'date_paiement'
    ];

    protected $casts = [
        'est_paye' => 'boolean',
        'date_paiement' => 'datetime',
    ];

    /**
     * Récupère l'employé associé à ce salaire
     */
    public function employe()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
