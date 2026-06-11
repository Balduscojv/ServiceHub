<?php

namespace App\Models\Concerns;

use App\Models\Role;

trait HasRoles
{
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function hasRole(string $role): bool
    {
        return $this->roles->contains('name', $role);
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->roles->whereIn('name', $roles)->isNotEmpty();
    }

    public function hasPermission(string $permission): bool
    {
        return $this->roles
            ->flatMap(fn ($role) => $role->permissions)
            ->contains('name', $permission);
    }

    public function assignRole(string $role): void
    {
        $roleModel = Role::where('name', $role)->firstOrFail();
        $this->roles()->syncWithoutDetaching([$roleModel->id]);
    }

    public function syncRoles(array $roles): void
    {
        $ids = Role::whereIn('name', $roles)->pluck('id');
        $this->roles()->sync($ids);
    }
}
