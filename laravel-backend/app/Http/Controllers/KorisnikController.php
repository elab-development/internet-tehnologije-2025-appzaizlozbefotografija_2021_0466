<?php

namespace App\Http\Controllers;

use App\Models\Korisnik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

class KorisnikController extends Controller
{
    private function samoAdmin()
    {
        if (!auth()->check() || auth()->user()->uloga !== 'admin') {
            abort(response()->json(['poruka' => 'Nemate dozvolu za ovu akciju.'], 403));
        }
    }

    #[OA\Get(path: "/api/korisnici", summary: "Lista korisnika (admin)", tags: ["Korisnici"],
    security: [["bearerAuth" => []]],
    responses: [new OA\Response(response: 200, description: "OK"), new OA\Response(response: 403, description: "Zabranjeno")]
)]

    public function index()
    {
        $this->samoAdmin();
        return response()->json(Korisnik::all(), 200);
    }

    #[OA\Post(path: "/api/korisnici", summary: "Kreiranje korisnika (admin)", tags: ["Korisnici"],
    security: [["bearerAuth" => []]],
    requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
        required: ["ime","prezime","email","lozinka","uloga"],
        properties: [
            new OA\Property(property: "ime", type: "string", example: "Ana"),
            new OA\Property(property: "prezime", type: "string", example: "Anić"),
            new OA\Property(property: "email", type: "string", example: "ana@test.com"),
            new OA\Property(property: "lozinka", type: "string", example: "ana12345"),
            new OA\Property(property: "uloga", type: "string", example: "fotograf"),
        ]
    )),
    responses: [new OA\Response(response: 201, description: "Kreirano"), new OA\Response(response: 403, description: "Zabranjeno")]
)]

    public function store(Request $request)
    {
        $this->samoAdmin();

        $validated = $request->validate([
            'ime' => 'required|string|max:50',
            'prezime' => 'required|string|max:50',
            'email' => 'required|email|unique:korisnici,email',
            'lozinka' => 'required|string|min:6',
            'uloga' => 'required|in:admin,fotograf,posetilac',
        ]);

        $korisnik = Korisnik::create([
            'ime' => $validated['ime'],
            'prezime' => $validated['prezime'],
            'email' => $validated['email'],
            'lozinka' => Hash::make($validated['lozinka']),
            'uloga' => $validated['uloga'],
        ]);

        return response()->json($korisnik, 201);
    }

    #[OA\Put(path: "/api/korisnici/{id}", summary: "Izmena korisnika (admin)", tags: ["Korisnici"],
    security: [["bearerAuth" => []]],
    parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))],
    requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
        properties: [
            new OA\Property(property: "ime", type: "string", example: "Ana"),
            new OA\Property(property: "prezime", type: "string", example: "Anić"),
            new OA\Property(property: "email", type: "string", example: "ana@test.com"),
            new OA\Property(property: "lozinka", type: "string", example: "nova12345"),
            new OA\Property(property: "uloga", type: "string", example: "admin"),
        ]
    )),
    responses: [new OA\Response(response: 200, description: "OK"), new OA\Response(response: 404, description: "Nije pronađen")]
)]

    public function update(Request $request, $id)
    {
        $this->samoAdmin();

        $korisnik = Korisnik::find($id);
        if (!$korisnik) {
            return response()->json(['poruka' => 'Korisnik nije pronađen.'], 404);
        }

        $validated = $request->validate([
            'ime' => 'sometimes|string|max:50',
            'prezime' => 'sometimes|string|max:50',
            'email' => 'sometimes|email|unique:korisnici,email,' . $id,
            'lozinka' => 'sometimes|string|min:6',
            'uloga' => 'sometimes|in:admin,fotograf,posetilac',
        ]);

        if (isset($validated['lozinka'])) {
            $validated['lozinka'] = Hash::make($validated['lozinka']);
        }

        $korisnik->update($validated);

        return response()->json($korisnik, 200);
    }

    #[OA\Delete(path: "/api/korisnici/{id}", summary: "Brisanje korisnika (admin)", tags: ["Korisnici"],
    security: [["bearerAuth" => []]],
    parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))],
    responses: [new OA\Response(response: 200, description: "Obrisan"), new OA\Response(response: 404, description: "Nije pronađen")]
)]

    public function destroy($id)
    {
        $this->samoAdmin();

        $korisnik = Korisnik::find($id);
        if (!$korisnik) {
            return response()->json(['poruka' => 'Korisnik nije pronađen.'], 404);
        }

        $korisnik->delete();
        return response()->json(['poruka' => 'Korisnik obrisan.'], 200);
    }

    #[OA\Get(path: "/api/korisnici/{id}/izlozbe", summary: "Izložbe na koje je korisnik prijavljen", tags: ["Korisnici"],
    security: [["bearerAuth" => []]],
    parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))],
    responses: [new OA\Response(response: 200, description: "OK"), new OA\Response(response: 404, description: "Nije pronađen")]
)]

    public function izlozbe($id)
    {
        $korisnik = Korisnik::find($id);

        if (!$korisnik) {
            return response()->json(['poruka' => 'Korisnik nije pronađen.'], 404);
        }

        $izlozbe = $korisnik->prijave()
            ->with('izlozba')
            ->get()
            ->pluck('izlozba')
            ->unique('id')
            ->values();

        return response()->json($izlozbe, 200);
    }
}
