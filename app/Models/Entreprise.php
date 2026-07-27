<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Entreprise extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'nom',
        'ninea',
        'adresse',
        'telephone',
        'email',
        'site_web',
        'devise',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
    }
}
