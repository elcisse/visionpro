<?php

namespace App\Livewire\Dashboard;

use App\Models\Engin;
use App\Models\Facture;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Tableau de bord'])]
class Show extends Component
{
    /**
     * Heures d'un mois selon la convention du tableau tarifaire initial
     * (jour = 20h, mois = 480h).
     */
    private const HEURES_MOIS = 480;

    public function render()
    {
        $debutMois = now()->startOfMonth();
        $finMois = now()->endOfMonth();

        $recettesReellesMois = (float) Facture::whereBetween('periode_debut', [$debutMois, $finMois])->sum('montant');

        $enginsActifs = Engin::where('statut', '!=', 'hors_service')->get();
        $recettesPrevisionnellesMois = (float) $enginsActifs->sum(fn ($engin) => $engin->tarif_horaire * self::HEURES_MOIS);

        $totalEngins = $enginsActifs->count();
        $enginsEnLocation = $enginsActifs->where('statut', 'en_location')->count();
        $tauxUtilisation = $totalEngins > 0 ? round($enginsEnLocation / $totalEngins * 100) : 0;

        $enginsEnPanne = $enginsActifs->where('statut', 'en_panne')->count();
        $enginsEnEntretien = $enginsActifs->where('statut', 'en_entretien')->count();

        $facturesImpayees = Facture::whereIn('statut', ['emise', 'partiellement_payee', 'en_retard'])
            ->withSum('paiements as paye', 'montant')
            ->get();

        $nombreFacturesImpayees = $facturesImpayees->count();
        $montantRestantDu = (float) $facturesImpayees->sum(fn ($facture) => $facture->montant - ($facture->paye ?? 0));

        $facturesEnRetard = Facture::whereIn('statut', ['emise', 'partiellement_payee'])
            ->whereNotNull('date_echeance')
            ->where('date_echeance', '<', now())
            ->count();

        return view('livewire.dashboard.show', [
            'recettesReellesMois' => $recettesReellesMois,
            'recettesPrevisionnellesMois' => $recettesPrevisionnellesMois,
            'totalEngins' => $totalEngins,
            'enginsEnLocation' => $enginsEnLocation,
            'tauxUtilisation' => $tauxUtilisation,
            'enginsEnPanne' => $enginsEnPanne,
            'enginsEnEntretien' => $enginsEnEntretien,
            'nombreFacturesImpayees' => $nombreFacturesImpayees,
            'montantRestantDu' => $montantRestantDu,
            'facturesEnRetard' => $facturesEnRetard,
        ]);
    }
}
