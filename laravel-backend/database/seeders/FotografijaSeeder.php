<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fotografija;

class FotografijaSeeder extends Seeder
{
    public function run(): void
    {
        Fotografija::create([
            'naziv' => 'Planina',
            'putanja_slike' => 'fotografije/planina.jpg',
            'opis' => 'Kopaonihk',
            'izlozba_id' => 1
        ]);

        Fotografija::create([
            'naziv' => 'Reka',
            'putanja_slike' => 'fotografije/reka.jpg',
            'opis' => 'Dunav',
            'izlozba_id' => 1
        ]);
    }
}
