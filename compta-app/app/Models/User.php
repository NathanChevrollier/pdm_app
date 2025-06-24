<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Traits\LogsActivity;

class User extends Authenticatable
{
    use HasFactory, Notifiable, LogsActivity;
    
    /**
     * Le nom du modèle pour les activités
     * 
     * @return string
     */
    public function getActivityName(): string
    {
        return 'Utilisateur';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'statut',
        'commission',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'commission' => 'decimal:2',
    ];

    /**
     * Les statuts disponibles et leur hiérarchie (du plus élevé au plus bas)
     */
    public static $statutsHierarchie = [
        'admin' => 1,        // Administrateur (niveau le plus élevé)
        'gerant' => 2,       // Gérant
        'co-gerant' => 3,    // Co-gérant
        'manager' => 4,      // Manager
        'vendeur' => 5,      // Vendeur
        'stagiaire' => 6     // Stagiaire (niveau le plus bas)
    ];
    
    /**
     * Vérifie si l'utilisateur est un administrateur.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->statut === 'admin';
    }
    
    /**
     * Vérifie si l'utilisateur a un statut supérieur ou égal à un autre utilisateur
     *
     * @param User $user L'utilisateur à comparer
     * @return bool
     */
    public function hasHigherOrEqualStatutThan(User $user): bool
    {
        $thisLevel = self::$statutsHierarchie[$this->statut] ?? 999;
        $otherLevel = self::$statutsHierarchie[$user->statut] ?? 999;
        
        return $thisLevel <= $otherLevel; // Plus petit = plus élevé dans la hiérarchie
    }

    /**
     * Relation avec les commandes de l'utilisateur.
     * Cette relation récupère les commandes liées à cet utilisateur
     */
    public function commandes()
    {
        return $this->hasMany(Commande::class, 'user_id');
    }
    
    /**
     * Retourne le nom complet de l'utilisateur.
     *
     * @return string
     */
    public function getNomComplet(): string
    {
        return $this->nom . ' ' . $this->prenom;
    }
    
    // Cette section a été nettoyée pour éviter les doublons
    
    /**
     * Obtient le taux de commission en fonction du statut de l'utilisateur.
     *
     * @return float
     */
    public function getTauxCommission(): float
    {
        // Si une commission personnalisée est définie, l'utiliser
        if ($this->commission > 0) {
            return $this->commission / 100; // Convertir en décimal
        }
        
        // Sinon, déterminer le taux en fonction du statut
        switch ($this->statut) {
            case 'admin':
                return 1; // 65% (Patron)
            case 'gerant':
                return 0.65; // 65% (Patron)
            case 'co-gerant':
                return 0.65; // 65% (Co-patron)
            case 'manager':
                return 0.60; // 60% (Manager)
            case 'vendeur':
                return 0.55; // 55% (Vendeur)
            case 'stagiaire':
                return 0.40; // 40% (Recrue)
            default:
                return 0;
        }
    }
    
    /**
     * Obtient le taux de commission par défaut en fonction du statut (en pourcentage)
     *
     * @param string $statut
     * @return int
     */
    public static function getDefaultCommissionRate(string $statut): int
    {
        switch ($statut) {
            case 'admin':
                return 70; // 70% pour patron
            case 'gerant':
                return 70; // 70% pour patron/gérant
            case 'co-gerant':
                return 65; // 65% pour co-gérant
            case 'manager':
                return 60; // 60% pour manager
            case 'vendeur':
                return 55; // 55% pour vendeur
            case 'stagiaire':
                return 40; // 40% pour stagiaire
            default:
                return 0;
        }
    }
    
    /**
     * Vérifie si l'utilisateur a un statut spécifique.
     *
     * @param string|array $statuts
     * @return bool
     */
    public function hasStatut($statuts): bool
    {
        if (!is_array($statuts)) {
            $statuts = [$statuts];
        }
        
        return in_array($this->statut, $statuts);
    }
}
