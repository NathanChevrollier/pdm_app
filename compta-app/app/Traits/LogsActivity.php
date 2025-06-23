<?php

namespace App\Traits;

use App\Services\ActivityLogger;

/**
 * Ce trait permet d'ajouter le suivi d'activité à un modèle
 */
trait LogsActivity
{
    /**
     * Le nom du modèle à utiliser pour les activités
     * 
     * @return string
     */
    public function getActivityName(): string
    {
        // Par défaut, utilise le nom de la classe du modèle
        return class_basename($this);
    }
    
    /**
     * Enregistre la création du modèle
     */
    protected static function bootLogsActivity()
    {
        static::created(function ($model) {
            $activityLogger = app(ActivityLogger::class);
            $activityLogger->log(
                sprintf('%s créé', $model->getActivityName()),
                sprintf('Un nouvel élément a été ajouté à %s', $model->getActivityName()),
                'success',
                'bx-plus-circle',
                'success',
                null,
                $model
            );
        });

        static::updated(function ($model) {
            $activityLogger = app(ActivityLogger::class);
            $activityLogger->log(
                sprintf('%s mis à jour', $model->getActivityName()),
                sprintf('Un élément de %s a été modifié', $model->getActivityName()),
                'info',
                'bx-edit',
                'info',
                null,
                $model
            );
        });

        static::deleted(function ($model) {
            $activityLogger = app(ActivityLogger::class);
            $activityLogger->log(
                sprintf('%s supprimé', $model->getActivityName()),
                sprintf('Un élément de %s a été supprimé', $model->getActivityName()),
                'danger',
                'bx-trash',
                'danger',
                null,
                $model
            );
        });
    }
    
    /**
     * Enregistre une activité personnalisée pour ce modèle
     * 
     * @param string $title
     * @param string $description
     * @param string $type
     * @param string|null $icon
     * @param string|null $color
     * @param string|null $link
     * @return \App\Models\Activite
     */
    public function logActivity(
        string $title,
        string $description,
        string $type = 'info',
        ?string $icon = null,
        ?string $color = null,
        ?string $link = null
    ) {
        return ActivityLogger::log(
            $title,
            $description,
            $type,
            $icon,
            $color,
            $link,
            $this
        );
    }
}
