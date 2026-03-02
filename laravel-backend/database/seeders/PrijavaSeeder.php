<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Prijava;
use App\Models\Izlozba;
use App\Models\Korisnik; 

class PrijavaSeeder extends Seeder
{
    public function run(): void
    {
        // uzmi posetioce (ili sve korisnike ako nema filtriranja)
        $posetioci = Korisnik::where('uloga', 'posetilac')->get();
        if ($posetioci->isEmpty()) {
            $posetioci = Korisnik::all();
        }

        $izlozbe = Izlozba::all();

        if ($posetioci->isEmpty() || $izlozbe->isEmpty()) {
            return;
        }

        // napravi npr. 10 prijava raspoređenih
        $broj = 10;

        for ($i = 0; $i < $broj; $i++) {
            $korisnik = $posetioci[$i % $posetioci->count()];
            $izlozba = $izlozbe[$i % $izlozbe->count()];

            // ako nema mesta, preskoči
            if (($izlozba->dostupna_mesta ?? 0) <= 0) {
                continue;
            }

            // spreči duplikate (isti korisnik ista izložba) ako želiš
            $vecPostoji = Prijava::where('korisnik_id', $korisnik->id)
                ->where('izlozba_id', $izlozba->id)
                ->exists();
            if ($vecPostoji) continue;

            Prijava::create([
                'korisnik_id' => $korisnik->id,
                'izlozba_id' => $izlozba->id,
                'datum_prijave' => Carbon::now()->subDays(rand(0, 20)), // datetime
                'qr_kod' => (string) Str::uuid(),
            ]);

            // smanji dostupna mesta kao u store()
            $izlozba->decrement('dostupna_mesta');
        }
    }
}