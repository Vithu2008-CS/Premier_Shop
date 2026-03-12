<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name', 'display_name', 'description', 'is_staff'];

    protected function casts(): array
    {
        return [
            'is_staff' => 'boolean',
        ];
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function hasPermission(string $permission): bool
    {
        return $this->permissions()->where('name', $permission)->exists();
    }

    public function givePermission(string $permission): void
    {
        $perm = Permission::where('name', $permission)->first();
        if ($perm && !$this->permissions()->where('permission_id', $perm->id)->exists()) {
            $this->permissions()->attach($perm->id);
        }
    }
}
