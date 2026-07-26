<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'type_client',
        'nom',
        'telephone',
        'email',
        'adresse',
        'ninea',
        'statut',
    ];

    public function contrats()
    {
        return $this->hasMany(Contrat::class);
    }
}
