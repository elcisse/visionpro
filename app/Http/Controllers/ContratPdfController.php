<?php

namespace App\Http\Controllers;

use App\Models\Contrat;
use App\Models\Entreprise;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ContratPdfController extends Controller
{
    public function show(Request $request, Contrat $contrat)
    {
        abort_unless($request->user()->can('contrats.view'), 403);

        $contrat->load('client', 'engin');

        $pdf = Pdf::loadView('pdf.contrat', [
            'contrat' => $contrat,
            'entreprise' => Entreprise::first(),
        ]);

        return $pdf->stream("contrat-{$contrat->numero}.pdf");
    }
}
