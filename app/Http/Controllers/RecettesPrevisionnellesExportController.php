<?php

namespace App\Http\Controllers;

use App\Models\Engin;
use Illuminate\Http\Request;
use Spatie\SimpleExcel\SimpleExcelWriter;

class RecettesPrevisionnellesExportController extends Controller
{
    private const HEURE_ANNEE = 5760;

    public function export(Request $request)
    {
        abort_unless($request->user()->can('engins.view'), 403);

        $writer = SimpleExcelWriter::streamDownload('recettes-previsionnelles.xlsx');

        $totalGeneral = 0;

        foreach (Engin::orderBy('designation')->get() as $engin) {
            $total = (float) $engin->tarif_horaire * self::HEURE_ANNEE;
            $totalGeneral += $total;

            $writer->addRow([
                'Équipement' => $engin->designation,
                'Jour (h)' => 20,
                'Semaine (h)' => 120,
                'Mensuel (h)' => 480,
                'Année (h)' => self::HEURE_ANNEE,
                'Taux (FCFA/h)' => (float) $engin->tarif_horaire,
                'Total (FCFA/an)' => $total,
            ]);
        }

        $writer->addRow([
            'Équipement' => 'TOTAL RECETTE',
            'Jour (h)' => '',
            'Semaine (h)' => '',
            'Mensuel (h)' => '',
            'Année (h)' => '',
            'Taux (FCFA/h)' => '',
            'Total (FCFA/an)' => $totalGeneral,
        ]);

        $writer->toBrowser();
    }
}
