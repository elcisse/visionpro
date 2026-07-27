<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Contrat;
use App\Models\Engin;
use App\Models\Pointage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsPermissions;
use Tests\TestCase;

class EnginFicheTest extends TestCase
{
    use RefreshDatabase;
    use SeedsPermissions;

    public function test_la_fiche_engin_affiche_le_contrat_et_les_pointages(): void
    {
        $admin = $this->userWithRole('Super Admin');

        $client = Client::create(['nom' => 'Client Fiche', 'type_client' => 'entreprise']);
        $engin = Engin::create([
            'designation' => 'Engin Fiche Test',
            'categorie' => 'Test',
            'tarif_horaire' => 20000,
            'statut' => 'disponible',
        ]);
        $contrat = Contrat::create([
            'client_id' => $client->id,
            'engin_id' => $engin->id,
            'date_debut' => '2026-07-01',
            'lieu_chantier' => 'Chantier XYZ',
            'tarif_horaire' => 20000,
            'statut' => 'en_cours',
        ]);
        Pointage::create(['contrat_id' => $contrat->id, 'date' => '2026-07-01', 'heures_travaillees' => 8]);

        $response = $this->actingAs($admin)->get(route('engins.show', $engin));

        $response->assertOk();
        $response->assertSee('Engin Fiche Test');
        $response->assertSee('Chantier XYZ');
        $response->assertSee($contrat->numero);
    }

    public function test_un_utilisateur_sans_permission_ne_peut_pas_voir_la_fiche(): void
    {
        $this->seedRolesAndPermissions();
        $user = \App\Models\User::factory()->create();

        $engin = Engin::create([
            'designation' => 'Engin Fiche Test',
            'categorie' => 'Test',
            'tarif_horaire' => 20000,
            'statut' => 'disponible',
        ]);

        $this->actingAs($user)
            ->get(route('engins.show', $engin))
            ->assertForbidden();
    }
}
