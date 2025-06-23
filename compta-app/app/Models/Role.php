<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    /**
     * Les utilisateurs qui ont ce rôle
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Les permissions associées à ce rôle
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * Vérifie si le rôle a une permission spécifique
     */
    public function hasPermission($permission)
    {
        if (is_string($permission)) {
            return $this->permissions->contains('name', $permission);
        }
        
        return !!$permission->intersect($this->permissions)->count();
    }

    /**
     * Attribue des permissions à ce rôle
     */
    public function givePermissionTo(...$permissions)
    {
        $permissions = Permission::whereIn('name', $permissions)->get();
        
        if ($permissions->count() === 0) {
            return $this;
        }
        
        $this->permissions()->syncWithoutDetaching($permissions);
        
        return $this;
    }
}
