<?php

namespace App\Http\Controllers;

use App\Models\Entreprise;
use App\Models\Facture;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class FacturePdfController extends Controller
{
    private const STATUTS = [
        'brouillon' => 'Brouillon',
        'emise' => 'Émise',
        'partiellement_payee' => 'Partiellement payée',
        'payee' => 'Payée',
        'en_retard' => 'En retard',
    ];

    public function show(Request $request, Facture $facture)
    {
        abort_unless($request->user()->can('factures.view'), 403);

        $facture->load('contrat.client', 'contrat.engin');

        $pdf = Pdf::loadView('pdf.facture', [
            'facture' => $facture,
            'entreprise' => Entreprise::first(),
            'statuts' => self::STATUTS,
        ]);

        return $pdf->stream("facture-{$facture->numero}.pdf");
    }
}
