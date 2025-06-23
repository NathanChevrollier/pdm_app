<?php

namespace App\Services;

use App\Models\Activite;
use App\Models\User;
use Illuminate\Support\Str;

class ActivityLogger
{
    /**
     * Enregistre une activité dans le journal
     *
     * @param string $title
     * @param string $description
     * @param string $type
     * @param string|null $icon
     * @param string|null $color
     * @param string|null $link
     * @param mixed $subject
     * @param User|int|null $user
     * @return Activite
     */
    public function log(
        string $title,
        string $description,
        string $type = 'info',
        ?string $icon = null,
        ?string $color = null,
        ?string $link = null,
        $subject = null,
        $user = null
    ): Activite {
        // Déterminer l'utilisateur actuel si non spécifié
        if ($user === null) {
            $user = auth()->user();
        } elseif (is_int($user)) {
            // Si un ID utilisateur est fourni, charger l'utilisateur
            $user = User::find($user);
        }
        
        // Si l'utilisateur est toujours null, on ne peut pas enregistrer l'activité
        if (!$user) {
            throw new \InvalidArgumentException('Aucun utilisateur spécifié pour l\'activité');
        }
        
        // Préparer les métadonnées
        $metadata = [];
        
        // Si un sujet est fourni, enregistrer ses informations
        if ($subject) {
            // Limiter les données du sujet pour éviter les problèmes de JSON trop volumineux
            $subjectData = $subject->toArray();
            
            // Ne garder que les champs essentiels pour éviter les erreurs de contrainte
            $essentialFields = ['id', 'nom', 'prenom', 'email', 'statut'];
            $filteredSubject = array_intersect_key($subjectData, array_flip(
                array_filter(array_keys($subjectData), function($key) use ($essentialFields) {
                    return in_array($key, $essentialFields);
                })
            ));
            
            $metadata = [
                'subject_type' => get_class($subject),
                'subject_id' => $subject->id,
                'subject' => $filteredSubject,
            ];
            
            // Générer un lien par défaut si non fourni
            if (!$link) {
                $routeName = Str::plural(Str::kebab(class_basename($subject)));
                if (\Illuminate\Support\Facades\Route::has($routeName . '.show')) {
                    $link = route($routeName . '.show', $subject);
                }
            }
        }
        
        // Déterminer l'icône et la couleur par défaut en fonction du type
        $typeConfig = self::getTypeConfig($type);
        $icon = $icon ?? $typeConfig['icon'];
        $color = $color ?? $typeConfig['color'];
        
        // Créer et retourner l'activité
        return Activite::create([
            'titre' => $title,
            'description' => $description,
            'type' => $type,
            'icon' => $icon,
            'color' => $color,
            'lien' => $link,
            'user_id' => $user ? $user->id : null,
            'metadata' => $metadata,
        ]);
    }
    
    /**
     * Configuration des types d'activités
     * 
     * @param string $type
     * @return array
     */
    protected static function getTypeConfig(string $type): array
    {
        $types = [
            'info' => ['icon' => 'bx-info-circle', 'color' => 'info'],
            'success' => ['icon' => 'bx-check-circle', 'color' => 'success'],
            'warning' => ['icon' => 'bx-error', 'color' => 'warning'],
            'danger' => ['icon' => 'bx-error-circle', 'color' => 'danger'],
            'primary' => ['icon' => 'bx-info-circle', 'color' => 'primary'],
            'secondary' => ['icon' => 'bx-info-circle', 'color' => 'secondary'],
        ];
        
        return $types[$type] ?? $types['info'];
    }
    
    /**
     * Méthode statique pour la compatibilité
     * 
     * @deprecated Utiliser l'injection de dépendance à la place
     */
    public static function logStatic(
        string $title,
        string $description,
        string $type = 'info',
        ?string $icon = null,
        ?string $color = null,
        ?string $link = null,
        $subject = null,
        $user = null
    ): Activite {
        return app(self::class)->log(
            $title,
            $description,
            $type,
            $icon,
            $color,
            $link,
            $subject,
            $user
        );
    }
    
    /**
     * Enregistre la création d'un modèle
     * 
     * @deprecated Utiliser la méthode log() directement
     */
    public static function created($model, ?string $description = null, $user = null): void
    {
        $modelName = $model->getActivityName();
        
        app(self::class)->log(
            "$modelName créé",
            $description ?? "Un nouvel élément a été ajouté à $modelName",
            'success',
            'bx-plus-circle',
            'success',
            null,
            $model,
            $user
        );
    }
    
    /**
     * Enregistre la mise à jour d'un modèle
     * 
     * @deprecated Utiliser la méthode log() directement
     */
    public static function updated($model, ?string $description = null, $user = null): void
    {
        $modelName = $model->getActivityName();
        
        app(self::class)->log(
            "$modelName mis à jour",
            $description ?? "Un élément de $modelName a été modifié",
            'info',
            'bx-edit',
            'info',
            null,
            $model,
            $user
        );
    }
    
    /**
     * Enregistre la suppression d'un modèle
     * 
     * @deprecated Utiliser la méthode log() directement
     */
    public static function deleted($model, ?string $description = null, $user = null): void
    {
        $modelName = $model->getActivityName();
        
        app(self::class)->log(
            "$modelName supprimé",
            $description ?? "Un élément de $modelName a été supprimé",
            'danger',
            'bx-trash',
            'danger',
            null,
            $model, // On garde une référence au modèle supprimé
            $user
        );
    }
}
