<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chauffeur extends Model
{
    protected $fillable = [
        'nom',
        'prenom',
        'telephone',
        'numero_permis',
        'categorie_permis',
        'statut',
    ];

    public function affectations()
    {
        return $this->hasMany(Affectation::class);
    }

    public function pointages()
    {
        return $this->hasMany(Pointage::class);
    }
}
