<?php

namespace App\Events;

use App\Models\Activite;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ActivityLogged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * L'activité enregistrée
     *
     * @var \App\Models\Activite
     */
    public $activity;

    /**
     * Crée une nouvelle instance d'événement
     *
     * @param  \App\Models\Activite  $activity
     * @return void
     */
    public function __construct(Activite $activity)
    {
        $this->activity = $activity;
    }
}
