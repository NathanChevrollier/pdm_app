<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Pointage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'heure_entree',
        'heure_sortie',
        'duree_minutes',
        'statut',
        'commentaire'
    ];

    protected $casts = [
        'heure_entree' => 'datetime',
        'heure_sortie' => 'datetime',
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calcule la durée du pointage en minutes
     */
    public function calculerDuree()
    {
        if ($this->heure_sortie) {
            $this->duree_minutes = $this->heure_entree->diffInMinutes($this->heure_sortie);
            $this->statut = 'termine';
            return $this->duree_minutes;
        }
        return null;
    }

    /**
     * Formatte la durée en heures et minutes
     */
    public function getDureeFormatteeAttribute()
    {
        if ($this->duree_minutes === null) {
            return 'En cours';
        }
        
        $heures = floor($this->duree_minutes / 60);
        $minutes = $this->duree_minutes % 60;
        
        return sprintf('%02dh%02d', $heures, $minutes);
    }

    /**
     * Vérifie si le pointage est en cours
     */
    public function getEstEnCoursAttribute()
    {
        return $this->statut === 'en_cours';
    }

    /**
     * Vérifie si le pointage est terminé
     */
    public function getEstTermineAttribute()
    {
        return $this->statut === 'termine';
    }

    /**
     * Vérifie si le pointage est incomplet
     */
    public function getEstIncompletAttribute()
    {
        return $this->statut === 'incomplet';
    }

    /**
     * Marque le pointage comme incomplet
     */
    public function marquerIncomplet($commentaire = null)
    {
        $this->statut = 'incomplet';
        if ($commentaire) {
            $this->commentaire = $commentaire;
        }
        $this->save();
    }

    /**
     * Récupère le dernier pointage en cours pour un utilisateur
     */
    public static function getPointageEnCours($userId)
    {
        return self::where('user_id', $userId)
            ->where('statut', 'en_cours')
            ->orderBy('heure_entree', 'desc')
            ->first();
    }

    /**
     * Récupère les pointages d'un utilisateur pour une semaine donnée
     */
    public static function getPointagesSemaine($userId, $debutSemaine, $finSemaine)
    {
        return self::where('user_id', $userId)
            ->whereBetween('heure_entree', [$debutSemaine, $finSemaine])
            ->orderBy('heure_entree', 'desc')
            ->get();
    }

    /**
     * Récupère le temps total de présence d'un utilisateur pour une semaine donnée
     */
    public static function getTempsPresenceSemaine($userId, $debutSemaine, $finSemaine)
    {
        $pointages = self::where('user_id', $userId)
            ->whereBetween('heure_entree', [$debutSemaine, $finSemaine])
            ->where('statut', 'termine')
            ->get();
            
        return $pointages->sum('duree_minutes');
    }
}
