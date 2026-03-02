<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Korisnik;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

class AutentifikacijaController extends Controller
{
    #[OA\Post(
        path: "/api/registracija",
        summary: "Registracija korisnika (posetilac)",
        tags: ["Autentifikacija"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["ime","prezime","email","lozinka","lozinka_confirmation"],
                properties: [
                    new OA\Property(property: "ime", type: "string", example: "Pera"),
                    new OA\Property(property: "prezime", type: "string", example: "Perić"),
                    new OA\Property(property: "email", type: "string", example: "pera@test.com"),
                    new OA\Property(property: "lozinka", type: "string", example: "pera123"),
                    new OA\Property(property: "lozinka_confirmation", type: "string", example: "pera123"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Korisnik registrovan"),
            new OA\Response(response: 422, description: "Validaciona greška"),
        ]
    )]

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


    #[OA\Post(
        path: "/api/prijava",
        summary: "Prijava korisnika",
        tags: ["Autentifikacija"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email","lozinka"],
                properties: [
                    new OA\Property(property: "email", type: "string", example: "pera@test.com"),
                    new OA\Property(property: "lozinka", type: "string", example: "pera123"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Ulogovan"),
            new OA\Response(response: 401, description: "Pogrešan email ili lozinka"),
            new OA\Response(response: 422, description: "Validaciona greška"),
        ]
    )]

    public function prijava(Request $request)
    {
        $podaci = $request->validate([
            'email' => 'required|email',
            'lozinka' => 'required|string'
        ]);

        $korisnik = Korisnik::where('email', $podaci['email'])->first();

        if (!$korisnik || !Hash::check($podaci['lozinka'], $korisnik->lozinka)) {
            return response()->json(['poruka' => 'Email ili lozinka nisu ispravni.'], 401);
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

    #[OA\Post(
        path: "/api/odjava",
        summary: "Odjava korisnika",
        tags: ["Autentifikacija"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Odjavljen"),
            new OA\Response(response: 401, description: "Niste autentifikovani"),
        ]
    )]

    public function odjava(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['poruka' => 'Uspešno ste se odjavili.']);
    }

    #[OA\Get(
        path: "/api/korisnik",
        summary: "Vraća ulogovanog korisnika",
        tags: ["Autentifikacija"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "OK"),
            new OA\Response(response: 401, description: "Niste autentifikovani"),
        ]
    )]

    public function korisnik(Request $request)
    {
        return response()->json([
            'korisnik' => $request->user()
        ]);
    }


    #[OA\Post(
        path: "/api/zaboravljena-lozinka",
        summary: "Reset lozinke (direktna promena)",
        tags: ["Autentifikacija"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email","nova_lozinka","nova_lozinka_confirmation"],
                properties: [
                    new OA\Property(property: "email", type: "string", example: "pera@test.com"),
                    new OA\Property(property: "nova_lozinka", type: "string", example: "nova12345"),
                    new OA\Property(property: "nova_lozinka_confirmation", type: "string", example: "nova12345"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Lozinka promenjena"),
            new OA\Response(response: 422, description: "Validaciona greška"),
        ]
    )]

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