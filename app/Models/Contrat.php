<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Contrat extends Model
{
    use LogsActivity;

    protected $fillable = [
        'client_id',
        'engin_id',
        'numero',
        'date_debut',
        'date_fin',
        'lieu_chantier',
        'document_pdf',
        'tarif_horaire',
        'statut',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'tarif_horaire' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Contrat $contrat) {
            if (empty($contrat->numero)) {
                $contrat->numero = 'CTR-'.now()->format('Y').'-'.str_pad((static::whereYear('created_at', now()->year)->count() + 1), 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function engin()
    {
        return $this->belongsTo(Engin::class);
    }

    public function pointages()
    {
        return $this->hasMany(Pointage::class);
    }

    public function factures()
    {
        return $this->hasMany(Facture::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['numero', 'client_id', 'engin_id', 'date_debut', 'date_fin', 'lieu_chantier', 'tarif_horaire', 'statut'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('contrat');
    }
}
