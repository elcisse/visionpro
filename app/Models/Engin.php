<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Engin extends Model
{
    protected $fillable = [
        'designation',
        'categorie',
        'marque',
        'modele',
        'numero_serie',
        'tarif_horaire',
        'statut',
        'compteur_horaire',
    ];

    protected $casts = [
        'tarif_horaire' => 'decimal:2',
        'compteur_horaire' => 'decimal:2',
    ];

    public function affectations()
    {
        return $this->hasMany(Affectation::class);
    }

    public function contrats()
    {
        return $this->hasMany(Contrat::class);
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }

    public function charges()
    {
        return $this->hasMany(Charge::class);
    }

    public function factures()
    {
        return $this->hasManyThrough(Facture::class, Contrat::class);
    }

    public function recettesTotales(): float
    {
        return (float) $this->factures()->sum('montant');
    }

    public function chargesTotales(): float
    {
        return (float) $this->charges()->sum('montant');
    }

    public function rentabilite(): float
    {
        return $this->recettesTotales() - $this->chargesTotales();
    }
}
