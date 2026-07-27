<?php

namespace Tests\Feature;

use App\Livewire\Paiements\Manager as PaiementsManager;
use App\Models\Client;
use App\Models\Contrat;
use App\Models\Engin;
use App\Models\Facture;
use App\Models\Paiement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Concerns\SeedsPermissions;
use Tests\TestCase;

class PaiementSyncTest extends TestCase
{
    use RefreshDatabase;
    use SeedsPermissions;

    private function makeFacture(): Facture
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
            'date_debut' => '2026-07-01',
            'tarif_horaire' => 45000,
            'statut' => 'en_cours',
        ]);

        return Facture::create([
            'contrat_id' => $contrat->id,
            'type' => 'periodique',
            'periode_debut' => '2026-07-01',
            'periode_fin' => '2026-07-05',
            'heures_facturees' => 10,
            'montant' => 450000,
            'statut' => 'emise',
        ]);
    }

    public function test_solde_restant_diminue_apres_un_paiement_partiel(): void
    {
        $admin = $this->userWithRole('Super Admin');
        $facture = $this->makeFacture();

        $component = Livewire::actingAs($admin)
            ->test(PaiementsManager::class)
            ->set('facture_id', $facture->id);

        $this->assertSame(450000.0, $component->instance()->soldeRestant());

        $component->set('date_paiement', '2026-07-10')
            ->set('montant', '200000')
            ->set('mode_paiement', 'virement')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('partiellement_payee', $facture->fresh()->statut);
    }

    public function test_facture_devient_payee_quand_le_solde_est_soldee(): void
    {
        $admin = $this->userWithRole('Super Admin');
        $facture = $this->makeFacture();

        Paiement::create([
            'facture_id' => $facture->id,
            'date_paiement' => '2026-07-10',
            'montant' => 200000,
            'mode_paiement' => 'virement',
        ]);

        $component = Livewire::actingAs($admin)
            ->test(PaiementsManager::class)
            ->set('facture_id', $facture->id);

        $this->assertSame(250000.0, $component->instance()->soldeRestant());

        $component->set('date_paiement', '2026-07-15')
            ->set('montant', '250000')
            ->set('mode_paiement', 'especes')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('payee', $facture->fresh()->statut);
    }

    public function test_supprimer_un_paiement_repasse_la_facture_en_partiellement_payee(): void
    {
        $admin = $this->userWithRole('Super Admin');
        $facture = $this->makeFacture();

        Paiement::create([
            'facture_id' => $facture->id,
            'date_paiement' => '2026-07-10',
            'montant' => 200000,
            'mode_paiement' => 'virement',
        ]);
        $dernierPaiement = Paiement::create([
            'facture_id' => $facture->id,
            'date_paiement' => '2026-07-15',
            'montant' => 250000,
            'mode_paiement' => 'especes',
        ]);
        $facture->update(['statut' => 'payee']);

        Livewire::actingAs($admin)
            ->test(PaiementsManager::class)
            ->call('delete', $dernierPaiement->id);

        $this->assertSame('partiellement_payee', $facture->fresh()->statut);
    }
}
