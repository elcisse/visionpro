<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entreprise extends Model
{
    protected $fillable = [
        'nom',
        'ninea',
        'adresse',
        'telephone',
        'email',
        'site_web',
        'logo',
        'devise',
    ];
}
