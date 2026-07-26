<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    protected $fillable = [
        'facture_id',
        'date_paiement',
        'montant',
        'mode_paiement',
        'reference',
        'commentaire',
    ];

    protected $casts = [
        'date_paiement' => 'date',
        'montant' => 'decimal:2',
    ];

    public function facture()
    {
        return $this->belongsTo(Facture::class);
    }
}
