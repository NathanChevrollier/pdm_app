<?php

namespace App\Listeners;

use App\Events\ActivityLogged;
use Illuminate\Support\Facades\Log;

class LogActivity
{
    /**
     * Gère l'événement.
     *
     * @param  \App\Events\ActivityLogged  $event
     * @return void
     */
    public function handle(ActivityLogged $event)
    {
        $activity = $event->activity;
        
        // Vous pouvez ajouter ici une logique supplémentaire pour gérer les activités
        // Par exemple, envoyer des notifications, mettre à jour des tableaux de bord en temps réel, etc.
        
        // Exemple de log
        Log::info('Activité enregistrée', [
            'id' => $activity->id,
            'titre' => $activity->titre,
            'type' => $activity->type,
            'user_id' => $activity->user_id,
        ]);
    }
}
