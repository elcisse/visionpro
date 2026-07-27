<?php

namespace Tests\Feature;

use App\Livewire\Engins\Manager as EnginsManager;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Concerns\SeedsPermissions;
use Tests\TestCase;

class PermissionsTest extends TestCase
{
    use RefreshDatabase;
    use SeedsPermissions;

    public function test_super_admin_bypasses_all_permissions(): void
    {
        $admin = $this->userWithRole('Super Admin');

        Livewire::actingAs($admin)
            ->test(EnginsManager::class)
            ->call('create')
            ->set('designation', 'Engin Test')
            ->set('categorie', 'Test')
            ->set('tarif_horaire', '10000')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('engins', ['designation' => 'Engin Test']);
    }

    public function test_view_only_role_cannot_create_engin(): void
    {
        $direction = $this->userWithRole('Direction');
        $this->actingAs($direction);

        $this->expectException(AuthorizationException::class);

        (new EnginsManager())->create();
    }

    public function test_view_only_role_can_view_engins_list(): void
    {
        $direction = $this->userWithRole('Direction');

        Livewire::actingAs($direction)
            ->test(EnginsManager::class)
            ->assertOk();
    }

    public function test_user_without_any_role_is_forbidden(): void
    {
        $this->seedRolesAndPermissions();
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->expectException(AuthorizationException::class);

        (new EnginsManager())->render();
    }
}
