<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Izlozba;

class IzlozbaSeeder extends Seeder
{
    public function run(): void
    {
        Izlozba::create([
            'naziv' => 'Priroda Srbije',
            'opis' => 'Izložba fotografija prirodnih pejzaža.',
            'lokacija' => 'Beograd',
            'datum' => now()->addDays(10),
            'dostupna_mesta' => 40
        ]);

        Izlozba::create([
            'naziv' => 'Portreti',
            'opis' => 'Umetnički portreti savremenih autora.',
            'lokacija' => 'Novi Sad',
            'datum' => now()->addDays(20),
            'dostupna_mesta' => 50
        ]);

        Izlozba::create([
            'naziv' => 'Sportovi',
            'opis' => 'Fotografije poznatih sportova i sportista.',
            'lokacija' => 'Niš',
            'datum' => now()->addDays(30),
            'dostupna_mesta' => 70
        ]);
    }
}
