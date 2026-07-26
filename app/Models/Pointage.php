<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pointage extends Model
{
    protected $fillable = [
        'contrat_id',
        'chauffeur_id',
        'date',
        'heures_travaillees',
        'en_panne',
        'heures_panne',
        'commentaire',
    ];

    protected $casts = [
        'date' => 'date',
        'heures_travaillees' => 'decimal:2',
        'en_panne' => 'boolean',
        'heures_panne' => 'decimal:2',
    ];

    public function contrat()
    {
        return $this->belongsTo(Contrat::class);
    }

    public function chauffeur()
    {
        return $this->belongsTo(Chauffeur::class);
    }
}
