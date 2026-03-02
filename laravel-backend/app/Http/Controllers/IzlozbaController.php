<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Izlozba;
use OpenApi\Attributes as OA;

class IzlozbaController extends Controller
{

    #[OA\Get(path: "/api/izlozbe", summary: "Lista izložbi (sa filterima/paginacijom)", tags: ["Izložbe"],
    responses: [new OA\Response(response: 200, description: "OK")]
)]

    public function index(Request $request)
    {
        $query = Izlozba::query();

        if ($request->filled('naziv')) {
            $query->where('naziv', 'like', '%' . $request->naziv . '%');
        }

        if ($request->filled('datum')) {
            $query->whereDate('datum', $request->datum);
        }

        if ($request->filled('lokacija')) {
            $query->where('lokacija', 'like', '%' . $request->lokacija . '%');
        }

        $izlozbe = $query->paginate(10);

        return response()->json($izlozbe);
    }

    #[OA\Post(path: "/api/izlozbe", summary: "Kreiranje izložbe (admin)", tags: ["Izložbe"],
    security: [["bearerAuth" => []]],
    requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
        required: ["naziv","datum","lokacija"],
        properties: [
            new OA\Property(property: "naziv", type: "string", example: "Izložba 2026"),
            new OA\Property(property: "datum", type: "string", format: "date", example: "2026-03-10"),
            new OA\Property(property: "lokacija", type: "string", example: "Beograd"),
            new OA\Property(property: "opis", type: "string", example: "Opis..."),
            new OA\Property(property: "dostupna_mesta", type: "integer", example: 50),
        ]
    )),
    responses: [new OA\Response(response: 201, description: "Kreirano"), new OA\Response(response: 403, description: "Zabranjeno")]
)]

    public function store(Request $request)
    {
        if (auth()->user()->uloga !== 'admin') {
            return response()->json(['poruka' => 'Nemate dozvolu za ovu akciju.'], 403);
        }

        $validated = $request->validate([
            'naziv' => 'required|string|max:255',
            'datum' => 'required|date',
            'lokacija' => 'required|string|max:255',
            'opis' => 'nullable|string',
            'dostupna_mesta' => 'nullable|integer|min:0'
        ]);

        $izlozba = Izlozba::create($validated);

        return response()->json($izlozba->fresh(), 201);
    }

    #[OA\Get(path: "/api/izlozbe/{id}", summary: "Detalji izložbe", tags: ["Izložbe"],
    parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))],
    responses: [new OA\Response(response: 200, description: "OK"), new OA\Response(response: 404, description: "Nije pronađena")]
)]

    public function show($id)
    {
        $izlozba = Izlozba::find($id);

        if (!$izlozba) {
            return response()->json(['poruka' => 'Izložba nije pronađena.'], 404);
        }

        return response()->json($izlozba);
    }

    #[OA\Put(path: "/api/izlozbe/{id}", summary: "Izmena izložbe (admin)", tags: ["Izložbe"],
    security: [["bearerAuth" => []]],
    parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))],
    requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
        required: ["naziv","datum","lokacija"],
        properties: [
            new OA\Property(property: "naziv", type: "string", example: "Izložba 2026"),
            new OA\Property(property: "datum", type: "string", format: "date", example: "2026-03-10"),
            new OA\Property(property: "lokacija", type: "string", example: "Beograd"),
            new OA\Property(property: "opis", type: "string", example: "Opis..."),
            new OA\Property(property: "dostupna_mesta", type: "integer", example: 50),
        ]
    )),
    responses: [new OA\Response(response: 200, description: "OK"), new OA\Response(response: 404, description: "Nije pronađena")]
)]

    public function update(Request $request, $id)
    {
        if (auth()->user()->uloga !== 'admin') {
            return response()->json(['poruka' => 'Nemate dozvolu za ovu akciju.'], 403);
        }

        $izlozba = Izlozba::find($id);
        if (!$izlozba) {
            return response()->json(['poruka' => 'Izložba nije pronađena.'], 404);
        }

        $validated = $request->validate([
            'naziv' => 'required|string|max:255',
            'datum' => 'required|date',
            'lokacija' => 'required|string|max:255',
            'opis' => 'nullable|string',
            'dostupna_mesta' => 'nullable|integer|min:0'
        ]);

        $izlozba->update($validated);

        return response()->json($izlozba->fresh());
    }

    #[OA\Delete(path: "/api/izlozbe/{id}", summary: "Brisanje izložbe (admin)", tags: ["Izložbe"],
    security: [["bearerAuth" => []]],
    parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))],
    responses: [new OA\Response(response: 200, description: "Obrisano"), new OA\Response(response: 404, description: "Nije pronađena")]
)]

    public function destroy($id)
    {
        if (auth()->user()->uloga !== 'admin') {
            return response()->json(['poruka' => 'Nemate dozvolu za ovu akciju.'], 403);
        }

        $izlozba = Izlozba::find($id);
        if (!$izlozba) {
            return response()->json(['poruka' => 'Izložba nije pronađena.'], 404);
        }

        $izlozba->delete();

        return response()->json(['poruka' => 'Izložba je uspešno obrisana.'], 200);
    }

    #[OA\Get(path: "/api/izlozbe/{id}/fotografije", summary: "Fotografije za izložbu", tags: ["Izložbe"],
    parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))],
    responses: [new OA\Response(response: 200, description: "OK"), new OA\Response(response: 404, description: "Nije pronađena")]
)]

    public function fotografije($id)
    {
        $izlozba = Izlozba::find($id);
        if (!$izlozba) {
            return response()->json(['poruka' => 'Izložba nije pronađena.'], 404);
        }

        return response()->json($izlozba->fotografije);
    }

    #[OA\Get(path: "/api/izlozbe/{id}/prijave", summary: "Prijave za izložbu (sa korisnikom)", tags: ["Izložbe"],
    parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))],
    responses: [new OA\Response(response: 200, description: "OK"), new OA\Response(response: 404, description: "Nije pronađena")]
)]

    public function prijave($id)
    {
        $izlozba = Izlozba::find($id);
        if (!$izlozba) {
            return response()->json(['poruka' => 'Izložba nije pronađena.'], 404);
        }

        return response()->json(
            $izlozba->prijave()->with('korisnik')->get()
        );
    }
}
