<?php

namespace Tests\Feature;

use App\Livewire\Engins\Manager as EnginsManager;
use App\Models\Engin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Symfony\Component\HttpKernel\Exception\HttpException;
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

    public function test_ne_peut_pas_supprimer_une_photo_dun_autre_engin(): void
    {
        $admin = $this->userWithRole('Super Admin');
        $engin1 = Engin::create(['designation' => 'Engin 1', 'categorie' => 'Test', 'tarif_horaire' => 10000, 'statut' => 'disponible']);
        $engin2 = Engin::create(['designation' => 'Engin 2', 'categorie' => 'Test', 'tarif_horaire' => 10000, 'statut' => 'disponible']);

        $fake = UploadedFile::fake()->image('photo.jpg');
        $engin2->addMedia($fake->getRealPath())->usingFileName('photo.jpg')->toMediaCollection('photos');
        $media = $engin2->getFirstMedia('photos');

        $this->actingAs($admin);
        $component = new EnginsManager();
        $component->edit($engin1->id);

        try {
            $component->deletePhoto($media->id);
            $this->fail('Expected HttpException was not thrown.');
        } catch (HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }

        $this->assertNotNull($engin2->fresh()->getFirstMedia('photos'));
    }
}
