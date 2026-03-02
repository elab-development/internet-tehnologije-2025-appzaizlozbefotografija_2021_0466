<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Korisnik;
use Illuminate\Support\Facades\Hash;

class KorisnikSeeder extends Seeder
{
    public function run(): void
    {
        Korisnik::create([
            'ime' => 'Admin',
            'prezime' => 'AdminP',
            'email' => 'admin@test.com',
            'lozinka' => Hash::make('admin123'),
            'uloga' => 'admin'
        ]);

        Korisnik::create([
            'ime' => 'Fotograf',
            'prezime' => 'FotografP',
            'email' => 'fotograf@test.com',
            'lozinka' => Hash::make('fotograf123'),
            'uloga' => 'fotograf'
        ]);

        Korisnik::create([
            'ime' => 'Posetilac',
            'prezime' => 'PosetilacP',
            'email' => 'posetilac@test.com',
            'lozinka' => Hash::make('posetilac123'),
            'uloga' => 'posetilac'
        ]);
    }
}
