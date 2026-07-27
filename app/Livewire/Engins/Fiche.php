<?php

namespace App\Livewire\Engins;

use App\Models\Engin;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Fiche engin'])]
class Fiche extends Component
{
    public Engin $engin;

    public function mount(Engin $engin): void
    {
        $this->authorize('engins.view');

        $this->engin = $engin;
    }

    public function render()
    {
        $engin = $this->engin->load('media');

        $contratEnCours = $engin->contrats()
            ->where('statut', 'en_cours')
            ->with('client')
            ->latest('date_debut')
            ->first();

        $contratsHistorique = $engin->contrats()
            ->with('client')
            ->orderByDesc('date_debut')
            ->get();

        $affectationActuelle = $engin->affectations()
            ->with('chauffeur')
            ->where(function ($query) {
                $query->whereNull('date_fin')->orWhere('date_fin', '>=', now()->toDateString());
            })
            ->orderByDesc('date_debut')
            ->first();

        $affectationsHistorique = $engin->affectations()
            ->with('chauffeur')
            ->orderByDesc('date_debut')
            ->get();

        $pannes = $engin->maintenances()->where('type', 'panne')->orderByDesc('date_debut')->get();
        $entretiens = $engin->maintenances()->where('type', 'entretien_preventif')->orderByDesc('date_debut')->get();

        $pointagesRecents = $engin->pointages()
            ->with(['contrat.client', 'chauffeur'])
            ->orderByDesc('date')
            ->limit(15)
            ->get();

        return view('livewire.engins.fiche', [
            'contratEnCours' => $contratEnCours,
            'contratsHistorique' => $contratsHistorique,
            'affectationActuelle' => $affectationActuelle,
            'affectationsHistorique' => $affectationsHistorique,
            'pannes' => $pannes,
            'entretiens' => $entretiens,
            'pointagesRecents' => $pointagesRecents,
            'recettesTotales' => $engin->recettesTotales(),
            'chargesTotales' => $engin->chargesTotales(),
            'rentabilite' => $engin->rentabilite(),
            'heuresTravaillees' => $engin->heuresTravailleesCumulees(),
        ]);
    }
}
