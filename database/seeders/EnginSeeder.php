<?php

namespace Database\Seeders;

use App\Models\Engin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EnginSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $engins = [
            [
                'designation' => 'Bulldozer D8 GC',
                'categorie' => 'Bulldozer',
                'marque' => 'Caterpillar',
                'modele' => 'D8 GC',
                'tarif_horaire' => 45000,
            ],
            [
                'designation' => 'Pelle excavatrice CAT333-GC',
                'categorie' => 'Pelle excavatrice',
                'marque' => 'Caterpillar',
                'modele' => 'CAT333-GC',
                'tarif_horaire' => 30000,
            ],
            [
                'designation' => 'Pelle excavatrice CAT330-GC',
                'categorie' => 'Pelle excavatrice',
                'marque' => 'Caterpillar',
                'modele' => 'CAT330-GC',
                'tarif_horaire' => 30000,
            ],
            [
                'designation' => 'Gradeur CAT140 K',
                'categorie' => 'Gradeur',
                'marque' => 'Caterpillar',
                'modele' => 'CAT140K',
                'tarif_horaire' => 35000,
            ],
            [
                'designation' => 'Tractopelle CAT426',
                'categorie' => 'Tractopelle',
                'marque' => 'Caterpillar',
                'modele' => 'CAT426',
                'tarif_horaire' => 15000,
            ],
        ];

        foreach ($engins as $engin) {
            Engin::updateOrCreate(
                ['designation' => $engin['designation']],
                $engin
            );
        }
    }
}
