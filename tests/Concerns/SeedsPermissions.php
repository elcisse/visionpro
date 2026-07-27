<?php

namespace Tests\Concerns;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

trait SeedsPermissions
{
    protected function seedRolesAndPermissions(): void
    {
        $this->seed(RolePermissionSeeder::class);
    }

    protected function userWithRole(string $role): User
    {
        $this->seedRolesAndPermissions();

        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }
}
