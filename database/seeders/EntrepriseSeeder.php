<?php

namespace Database\Seeders;

use App\Models\Entreprise;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EntrepriseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Entreprise::updateOrCreate(
            ['nom' => 'GEOPARTNERS CONSULTING'],
            [
                'ninea' => '0119729092Y2',
                'adresse' => 'Cité Lobath Fall, rond-point EDK villa N°89, Pikine, Dakar',
                'email' => 'falldiara@hotmail.com',
                'site_web' => 'www.geopartnersconsulting.com',
                'devise' => 'FCFA',
            ]
        );
    }
}
