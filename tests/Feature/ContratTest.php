<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Contrat;
use App\Models\Engin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContratTest extends TestCase
{
    use RefreshDatabase;

    public function test_le_numero_de_contrat_est_genere_automatiquement(): void
    {
        $client = Client::create(['nom' => 'Client Test', 'type_client' => 'entreprise']);
        $engin = Engin::create([
            'designation' => 'Engin Test',
            'categorie' => 'Test',
            'tarif_horaire' => 45000,
            'statut' => 'disponible',
        ]);

        $contrat1 = Contrat::create([
            'client_id' => $client->id,
            'engin_id' => $engin->id,
            'date_debut' => now(),
            'tarif_horaire' => 45000,
        ]);
        $contrat2 = Contrat::create([
            'client_id' => $client->id,
            'engin_id' => $engin->id,
            'date_debut' => now(),
            'tarif_horaire' => 45000,
        ]);

        $annee = now()->format('Y');
        $this->assertSame("CTR-{$annee}-0001", $contrat1->numero);
        $this->assertSame("CTR-{$annee}-0002", $contrat2->numero);
    }

    public function test_le_numero_fourni_explicitement_nest_pas_ecrase(): void
    {
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
            'date_debut' => now(),
            'tarif_horaire' => 45000,
            'numero' => 'CTR-PERSONNALISE',
        ]);

        $this->assertSame('CTR-PERSONNALISE', $contrat->numero);
    }
}
