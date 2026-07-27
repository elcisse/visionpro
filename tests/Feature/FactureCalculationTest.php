<?php

namespace Tests\Feature;

use App\Livewire\Factures\Manager as FacturesManager;
use App\Models\Client;
use App\Models\Contrat;
use App\Models\Engin;
use App\Models\Pointage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Concerns\SeedsPermissions;
use Tests\TestCase;

class FactureCalculationTest extends TestCase
{
    use RefreshDatabase;
    use SeedsPermissions;

    public function test_facture_calcule_heures_et_montant_depuis_les_pointages_de_la_periode(): void
    {
        $admin = $this->userWithRole('Super Admin');

        $client = Client::create(['nom' => 'Client Test', 'type_client' => 'entreprise']);
        $engin = Engin::create([
            'designation' => 'Engin Test',
            'categorie' => 'Test',
            'tarif_horaire' => 45000,
            'statut' => 'disponible',
        ]);
        $contrat = Contrat::create([
            'client_id' => $client->id,
            'engin_id' => $engin->id,
            'date_debut' => '2026-07-01',
            'tarif_horaire' => 45000,
            'statut' => 'en_cours',
        ]);

        Pointage::create(['contrat_id' => $contrat->id, 'date' => '2026-07-01', 'heures_travaillees' => 8]);
        Pointage::create(['contrat_id' => $contrat->id, 'date' => '2026-07-02', 'heures_travaillees' => 6]);
        // Hors période testée, ne doit pas être compté.
        Pointage::create(['contrat_id' => $contrat->id, 'date' => '2026-07-10', 'heures_travaillees' => 10]);

        Livewire::actingAs($admin)
            ->test(FacturesManager::class)
            ->set('contrat_id', $contrat->id)
            ->set('periode_debut', '2026-07-01')
            ->set('periode_fin', '2026-07-05')
            ->assertSet('heures_facturees', fn ($value) => (float) $value === 14.0)
            ->assertSet('montant', fn ($value) => (float) $value === 630000.0)
            ->set('date_echeance', '2026-08-05')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('factures', [
            'contrat_id' => $contrat->id,
            'montant' => 630000,
        ]);
    }
}
