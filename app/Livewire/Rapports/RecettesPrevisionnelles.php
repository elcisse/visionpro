<?php

namespace App\Livewire\Rapports;

use App\Models\Engin;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Recettes prévisionnelles'])]
class RecettesPrevisionnelles extends Component
{
    /**
     * Conversions horaires reprises du tableau tarifaire initial.
     */
    private const HEURES = [
        'jour' => 20,
        'semaine' => 120,
        'mensuel' => 480,
        'annee' => 5760,
    ];

    public function mount(): void
    {
        $this->authorize('engins.view');
    }

    public function render()
    {
        $engins = Engin::orderBy('designation')->get()->map(function (Engin $engin) {
            return [
                'engin' => $engin,
                'total' => (float) $engin->tarif_horaire * self::HEURES['annee'],
            ];
        });

        return view('livewire.rapports.recettes-previsionnelles', [
            'lignes' => $engins,
            'heures' => self::HEURES,
            'totalGeneral' => $engins->sum('total'),
        ]);
    }
}
