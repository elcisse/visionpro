<?php

namespace Tests\Feature;

use App\Livewire\Engins\Manager as EnginsManager;
use App\Models\Engin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Concerns\SeedsPermissions;
use Tests\TestCase;

class EnginsCrudTest extends TestCase
{
    use RefreshDatabase;
    use SeedsPermissions;

    public function test_peut_creer_un_engin(): void
    {
        $admin = $this->userWithRole('Super Admin');

        Livewire::actingAs($admin)
            ->test(EnginsManager::class)
            ->call('create')
            ->set('designation', 'Bulldozer D9')
            ->set('categorie', 'Bulldozer')
            ->set('tarif_horaire', '50000')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('engins', [
            'designation' => 'Bulldozer D9',
            'statut' => 'disponible',
        ]);
    }

    public function test_ne_peut_pas_creer_un_engin_sans_designation(): void
    {
        $admin = $this->userWithRole('Super Admin');

        Livewire::actingAs($admin)
            ->test(EnginsManager::class)
            ->call('create')
            ->set('categorie', 'Bulldozer')
            ->set('tarif_horaire', '50000')
            ->call('save')
            ->assertHasErrors(['designation' => 'required']);
    }

    public function test_peut_modifier_un_engin(): void
    {
        $admin = $this->userWithRole('Super Admin');
        $engin = Engin::create([
            'designation' => 'Ancien nom',
            'categorie' => 'Test',
            'tarif_horaire' => 10000,
            'statut' => 'disponible',
        ]);

        Livewire::actingAs($admin)
            ->test(EnginsManager::class)
            ->call('edit', $engin->id)
            ->set('designation', 'Nouveau nom')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('Nouveau nom', $engin->fresh()->designation);
    }

    public function test_peut_supprimer_un_engin(): void
    {
        $admin = $this->userWithRole('Super Admin');
        $engin = Engin::create([
            'designation' => 'À supprimer',
            'categorie' => 'Test',
            'tarif_horaire' => 10000,
            'statut' => 'disponible',
        ]);

        Livewire::actingAs($admin)
            ->test(EnginsManager::class)
            ->call('delete', $engin->id);

        $this->assertDatabaseMissing('engins', ['id' => $engin->id]);
    }
}
