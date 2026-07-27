<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Paiement extends Model
{
    use LogsActivity;

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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['facture_id', 'date_paiement', 'montant', 'mode_paiement', 'reference'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('paiement');
    }
}
