<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Contrat;
use App\Models\Engin;
use App\Models\Maintenance;
use App\Services\EnginStatutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Ce service touche directement la base (relations contrats/maintenances),
 * d'où la présence dans tests/Feature malgré son nom "Service".
 */
class EnginStatutServiceTest extends TestCase
{
    use RefreshDatabase;

    private function makeEngin(): Engin
    {
        return Engin::create([
            'designation' => 'Engin Test',
            'categorie' => 'Test',
            'tarif_horaire' => 10000,
            'statut' => 'disponible',
        ]);
    }

    private function makeClient(): Client
    {
        return Client::create(['nom' => 'Client Test', 'type_client' => 'entreprise']);
    }

    public function test_engin_sans_contrat_ni_maintenance_est_disponible(): void
    {
        $engin = $this->makeEngin();

        EnginStatutService::synchroniser($engin);

        $this->assertSame('disponible', $engin->fresh()->statut);
    }

    public function test_engin_avec_contrat_actif_passe_en_location(): void
    {
        $engin = $this->makeEngin();
        $client = $this->makeClient();

        Contrat::create([
            'client_id' => $client->id,
            'engin_id' => $engin->id,
            'date_debut' => now()->subDay(),
            'tarif_horaire' => 10000,
            'statut' => 'en_cours',
        ]);

        EnginStatutService::synchroniser($engin);

        $this->assertSame('en_location', $engin->fresh()->statut);
    }

    public function test_panne_est_prioritaire_sur_location(): void
    {
        $engin = $this->makeEngin();
        $client = $this->makeClient();

        Contrat::create([
            'client_id' => $client->id,
            'engin_id' => $engin->id,
            'date_debut' => now()->subDay(),
            'tarif_horaire' => 10000,
            'statut' => 'en_cours',
        ]);

        Maintenance::create([
            'engin_id' => $engin->id,
            'type' => 'panne',
            'date_debut' => now(),
            'statut' => 'en_cours',
        ]);

        EnginStatutService::synchroniser($engin);

        $this->assertSame('en_panne', $engin->fresh()->statut);
    }

    public function test_retour_en_location_apres_panne_terminee(): void
    {
        $engin = $this->makeEngin();
        $client = $this->makeClient();

        Contrat::create([
            'client_id' => $client->id,
            'engin_id' => $engin->id,
            'date_debut' => now()->subDay(),
            'tarif_horaire' => 10000,
            'statut' => 'en_cours',
        ]);

        $maintenance = Maintenance::create([
            'engin_id' => $engin->id,
            'type' => 'panne',
            'date_debut' => now(),
            'statut' => 'en_cours',
        ]);

        EnginStatutService::synchroniser($engin);
        $this->assertSame('en_panne', $engin->fresh()->statut);

        $maintenance->update(['statut' => 'terminee']);
        EnginStatutService::synchroniser($engin->fresh());

        $this->assertSame('en_location', $engin->fresh()->statut);
    }

    public function test_hors_service_nest_jamais_modifie_automatiquement(): void
    {
        $engin = $this->makeEngin();
        $engin->update(['statut' => 'hors_service']);

        Maintenance::create([
            'engin_id' => $engin->id,
            'type' => 'entretien_preventif',
            'date_debut' => now(),
            'statut' => 'en_cours',
        ]);

        EnginStatutService::synchroniser($engin->fresh());

        $this->assertSame('hors_service', $engin->fresh()->statut);
    }
}
