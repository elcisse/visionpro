<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    protected $fillable = [
        'contrat_id',
        'numero',
        'type',
        'periode_debut',
        'periode_fin',
        'heures_facturees',
        'montant',
        'date_echeance',
        'statut',
    ];

    protected $casts = [
        'periode_debut' => 'date',
        'periode_fin' => 'date',
        'date_echeance' => 'date',
        'heures_facturees' => 'decimal:2',
        'montant' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Facture $facture) {
            if (empty($facture->numero)) {
                $facture->numero = 'FACT-'.now()->format('Y').'-'.str_pad((static::whereYear('created_at', now()->year)->count() + 1), 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function contrat()
    {
        return $this->belongsTo(Contrat::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }
}
