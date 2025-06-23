<?php

namespace App\Models;

use App\Events\ActivityLogged;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Event;

class Activite extends Model
{
    
    /**
     * Le nom du modèle pour les activités
     * 
     * @return string
     */
    public function getActivityName(): string
    {
        return 'Activité';
    }
    /**
     * Les attributs qui sont mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'titre',
        'description',
        'type',
        'icon',
        'color',
        'lien',
        'user_id',
        'metadata',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Les valeurs par défaut des attributs.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'type' => 'info',
        'icon' => 'bx-info-circle',
        'color' => 'primary',
    ];

    /**
     * Relation avec le modèle User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Enregistre une activité dans le journal
     * 
     * @param string $titre
     * @param string $description
     * @param string $type
     * @param string|null $icon
     * @param string|null $color
     * @param string|null $lien
     * @param array|\Illuminate\Database\Eloquent\Model|null $metadata
     * @param int|\App\Models\User|null $user
     * @return Activite
     */
    public static function log(
        string $titre,
        string $description,
        string $type = 'info',
        ?string $icon = null,
        ?string $color = null,
        ?string $lien = null,
        $metadata = null,
        $user = null
    ): self {
        $activityLogger = app(\App\Services\ActivityLogger::class);
        
        return $activityLogger->log(
            $titre,
            $description,
            $type,
            $icon,
            $color,
            $lien,
            $metadata,
            $user
        );
    }
    
    /**
     * Enregistre une activité à partir d'un tableau d'attributs
     * 
     * @param array $attributes
     * @return static
     */
    public static function createFromArray(array $attributes): self
    {
        return static::create($attributes);
    }
}
