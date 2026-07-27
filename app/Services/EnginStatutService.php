<?php

namespace App\Services;

use App\Models\Engin;

class EnginStatutService
{
    /**
     * Recalcule et met à jour le statut d'un engin en fonction de ses
     * maintenances et contrats actifs. Ne touche jamais un engin
     * explicitement mis "hors_service" (décision manuelle).
     */
    public static function synchroniser(Engin $engin): void
    {
        if ($engin->statut === 'hors_service') {
            return;
        }

        if ($engin->maintenances()->where('type', 'panne')->where('statut', '!=', 'terminee')->exists()) {
            $nouveauStatut = 'en_panne';
        } elseif ($engin->maintenances()->where('type', 'entretien_preventif')->where('statut', '!=', 'terminee')->exists()) {
            $nouveauStatut = 'en_entretien';
        } else {
            $today = now()->toDateString();

            $contratActif = $engin->contrats()
                ->where('statut', 'en_cours')
                ->where('date_debut', '<=', $today)
                ->where(function ($query) use ($today) {
                    $query->whereNull('date_fin')->orWhere('date_fin', '>=', $today);
                })
                ->exists();

            $nouveauStatut = $contratActif ? 'en_location' : 'disponible';
        }

        if ($engin->statut !== $nouveauStatut) {
            $engin->update(['statut' => $nouveauStatut]);
        }
    }
}
