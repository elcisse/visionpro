<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    protected $fillable = [
        'engin_id',
        'type',
        'date_debut',
        'date_fin',
        'cout',
        'description',
        'statut',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'cout' => 'decimal:2',
    ];

    public function engin()
    {
        return $this->belongsTo(Engin::class);
    }

    public function charges()
    {
        return $this->hasMany(Charge::class);
    }
}
