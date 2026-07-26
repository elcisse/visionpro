<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Charge extends Model
{
    protected $fillable = [
        'engin_id',
        'maintenance_id',
        'type',
        'date',
        'montant',
        'description',
    ];

    protected $casts = [
        'date' => 'date',
        'montant' => 'decimal:2',
    ];

    public function engin()
    {
        return $this->belongsTo(Engin::class);
    }

    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class);
    }
}
