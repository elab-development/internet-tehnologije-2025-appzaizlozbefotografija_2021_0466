<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fotografija;
use OpenApi\Attributes as OA;

class FotografijaController extends Controller
{

#[OA\Get(path: "/api/fotografije", summary: "Lista fotografija", tags: ["Fotografije"],
    responses: [new OA\Response(response: 200, description: "OK")]
)]

    public function index()
    {
        return response()->json(Fotografija::with('izlozba')->get(), 200);
    }

    #[OA\Post(path: "/api/fotografije", summary: "Dodavanje fotografije (fotograf)", tags: ["Fotografije"],
    security: [["bearerAuth" => []]],
    requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
        required: ["naziv","izlozba_id"],
        properties: [
            new OA\Property(property: "naziv", type: "string", example: "Planina"),
            new OA\Property(property: "opis", type: "string", example: "Opis..."),
            new OA\Property(property: "izlozba_id", type: "integer", example: 1),
            new OA\Property(property: "url", type: "string", example: "https://example.com/slika.jpg"),
            
        ]
    )),
    responses: [new OA\Response(response: 201, description: "Kreirano"), new OA\Response(response: 403, description: "Zabranjeno")]
)]

    public function store(Request $request)
    {
        if (!auth()->check() || auth()->user()->uloga !== 'fotograf') {
            return response()->json(['poruka' => 'Samo fotograf može da dodaje fotografije.'], 403);
        }

        $validated = $request->validate([
            'naziv' => 'required|string|max:100',
            'opis' => 'nullable|string|max:500',
            'izlozba_id' => 'required|exists:izlozbe,id',
            'slika' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'url' => 'nullable|url|max:2048',
        ]);

        if (!$request->hasFile('slika') && !$request->filled('url')) {
            return response()->json(['poruka' => 'Morate uneti URL slike ili uploadovati fajl.'], 422);
        }

        $fotografija = new Fotografija();
        $fotografija->naziv = $validated['naziv'];
        $fotografija->opis = $validated['opis'] ?? null;
        $fotografija->izlozba_id = $validated['izlozba_id'];

        if ($request->hasFile('slika')) {
            $imeFajla = time() . '.' . $request->file('slika')->getClientOriginalExtension();
            $request->file('slika')->storeAs('fotografije', $imeFajla, 'public');
            $fotografija->putanja_slike = 'storage/fotografije/' . $imeFajla;
        } else {
            $fotografija->putanja_slike = $validated['url'];
        }

        $fotografija->save();

        return response()->json($fotografija->load('izlozba'), 201);
    }

    #[OA\Get(path: "/api/fotografije/{id}", summary: "Detalji fotografije", tags: ["Fotografije"],
    parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))],
    responses: [new OA\Response(response: 200, description: "OK"), new OA\Response(response: 404, description: "Nije pronađena")]
)]

    public function show($id)
    {
        $fotografija = Fotografija::with('izlozba')->find($id);

        if (!$fotografija) {
            return response()->json(['poruka' => 'Fotografija nije pronađena.'], 404);
        }

        return response()->json($fotografija, 200);
    }

    public function update(Request $request, $id)
    {
        return response()->json(['poruka' => 'Izmena fotografija nije podržana.'], 405);
    }

    #[OA\Delete(path: "/api/fotografije/{id}", summary: "Brisanje fotografije (admin)", tags: ["Fotografije"],
    security: [["bearerAuth" => []]],
    parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))],
    responses: [new OA\Response(response: 200, description: "Obrisano"), new OA\Response(response: 404, description: "Nije pronađena")]
)]

    public function destroy($id)
    {
        if (!auth()->check() || auth()->user()->uloga !== 'admin') {
            return response()->json(['poruka' => 'Samo administrator može da briše fotografije.'], 403);
        }

        $fotografija = Fotografija::find($id);
        if (!$fotografija) {
            return response()->json(['poruka' => 'Fotografija nije pronađena.'], 404);
        }

        $fotografija->delete();

        return response()->json(['poruka' => 'Fotografija je uspešno obrisana.'], 200);
    }
}