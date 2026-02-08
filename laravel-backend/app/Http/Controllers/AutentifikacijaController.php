<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Korisnik;
use Illuminate\Support\Facades\Hash;

class AutentifikacijaController extends Controller
{
    public function registracija(Request $request)
    {
        $podaci = $request->validate([
            'ime' => 'required|string|max:255',
            'prezime' => 'required|string|max:255',
            'email' => 'required|email|unique:korisnici,email',
            'lozinka' => 'required|string|min:6|confirmed',
        ],
        
        [
        'email.unique' => 'Email adresa je već registrovana.',
        ]

        );

        $korisnik = Korisnik::create([
            'ime' => $podaci['ime'],
            'prezime' => $podaci['prezime'],
            'email' => $podaci['email'],
            'lozinka' => Hash::make($podaci['lozinka']),
            'uloga' => 'posetilac'
        ]);

        $token = $korisnik->createToken('pristup_token')->plainTextToken;

        return response()->json([
            'korisnik' => [
                'id' => $korisnik->id,
                'ime' => $korisnik->ime,
                'prezime' => $korisnik->prezime,
                'email' => $korisnik->email,
                'uloga' => $korisnik->uloga
            ],
            'token' => $token
        ], 201);
    }

    public function prijava(Request $request)
    {
        $podaci = $request->validate([
            'email' => 'required|email',
            'lozinka' => 'required|string'
        ]);

        $korisnik = Korisnik::where('email', $podaci['email'])->first();

        if (!$korisnik || !Hash::check($podaci['lozinka'], $korisnik->lozinka)) {
            return response()->json(['poruka' => 'Pogrešan email ili lozinka.'], 401);
        }

        $token = $korisnik->createToken('pristup_token')->plainTextToken;

        return response()->json([
            'korisnik' => [
                'id' => $korisnik->id,
                'ime' => $korisnik->ime,
                'prezime' => $korisnik->prezime,
                'email' => $korisnik->email,
                'uloga' => $korisnik->uloga
            ],
            'token' => $token
        ]);
    }

    public function odjava(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['poruka' => 'Uspešno ste se odjavili.']);
    }

    public function korisnik(Request $request)
    {
        return response()->json([
            'korisnik' => $request->user()
        ]);
    }

    public function resetujLozinku(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:korisnici,email',
            'nova_lozinka' => 'required|min:6|confirmed',
        ]);

        $korisnik = Korisnik::where('email', $request->email)->first();
        $korisnik->lozinka = Hash::make($request->nova_lozinka);
        $korisnik->save();

        return response()->json([
            'poruka' => 'Lozinka je uspešno promenjena.'
        ]);
    }
}