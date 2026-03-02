<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prijava;
use App\Models\Izlozba;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class PrijavaController extends Controller
{
    
    #[OA\Get(path: "/api/prijave", summary: "Lista prijava (admin)", tags: ["Prijave"],
    security: [["bearerAuth" => []]],
    responses: [new OA\Response(response: 200, description: "OK"), new OA\Response(response: 403, description: "Zabranjeno")]
)]

    public function index()
    {
        if (!auth()->check()) {
            return response()->json(['poruka' => 'Niste autentifikovani.'], 401);
        }

        if (auth()->user()->uloga !== 'admin') {
            return response()->json(['poruka' => 'Nemate dozvolu za ovu akciju.'], 403);
        }

        $prijave = Prijava::with(['korisnik', 'izlozba'])
            ->orderByDesc('id')
            ->get();

        return response()->json($prijave, 200);
    }

    #[OA\Post(path: "/api/prijave", summary: "Rezervacija mesta na izložbi (posetilac)", tags: ["Prijave"],
    security: [["bearerAuth" => []]],
    requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
        required: ["izlozba_id"],
        properties: [new OA\Property(property: "izlozba_id", type: "integer", example: 1)]
    )),
    responses: [
        new OA\Response(response: 201, description: "Rezervisano"),
        new OA\Response(response: 422, description: "Nema mesta / validacija"),
        new OA\Response(response: 403, description: "Samo posetilac"),
    ]
)]

    public function store(Request $request)
    {
        if (!auth()->check() || auth()->user()->uloga !== 'posetilac') {
            return response()->json(['poruka' => 'Samo posetilac može da se prijavi.'], 403);
        }

        $validated = $request->validate([
            'izlozba_id' => 'required|exists:izlozbe,id',
        ]);

        try {
            $rezultat = DB::transaction(function () use ($validated) {
                $izlozba = Izlozba::where('id', $validated['izlozba_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if (($izlozba->dostupna_mesta ?? 0) <= 0) {
                    return ['status' => 422, 'body' => ['poruka' => 'Nema više slobodnih mesta.']];
                }

                $prijava = Prijava::create([
                    'korisnik_id' => auth()->id(),
                    'izlozba_id' => $validated['izlozba_id'],
                    'datum_prijave' => now(),
                    'qr_kod' => (string) Str::uuid(),
                ]);

                $izlozba->decrement('dostupna_mesta');

                return [
                    'status' => 201,
                    'body' => [
                        'poruka' => 'Uspešno rezervisano.',
                        'prijava' => $prijava,
                        'preostaloMesta' => $izlozba->dostupna_mesta,
                    ]
                ];
            });

            return response()->json($rezultat['body'], $rezultat['status']);
        } catch (\Throwable $e) {
            return response()->json([
                'poruka' => 'Došlo je do greške pri rezervaciji. Pokušajte ponovo.'
            ], 500);
        }
    }

    #[OA\Delete(path: "/api/prijave/{id}", summary: "Brisanje prijave (admin ili vlasnik)", tags: ["Prijave"],
    security: [["bearerAuth" => []]],
    parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))],
    responses: [new OA\Response(response: 200, description: "Obrisano"), new OA\Response(response: 404, description: "Nije pronađena")]
)]

    public function destroy($id)
    {
        if (!auth()->check()) {
            return response()->json(['poruka' => 'Niste autentifikovani.'], 401);
        }

        $prijava = Prijava::find($id);
        if (!$prijava) {
            return response()->json(['poruka' => 'Prijava nije pronađena.'], 404);
        }

        $uloga = auth()->user()->uloga;

        
        if ($uloga !== 'admin' && $prijava->korisnik_id !== auth()->id()) {
            return response()->json(['poruka' => 'Nemate dozvolu za brisanje.'], 403);
        }

        try {
            $rez = DB::transaction(function () use ($prijava) {
                $izlozba = Izlozba::where('id', $prijava->izlozba_id)
                    ->lockForUpdate()
                    ->first();

                $preostalo = null;

                if ($izlozba) {
                    $izlozba->increment('dostupna_mesta');
                    $preostalo = $izlozba->dostupna_mesta;
                }

                $prijava->delete();

                return [
                    'status' => 200,
                    'body' => [
                        'poruka' => 'Prijava obrisana.',
                        'preostaloMesta' => $preostalo,
                    ],
                ];
            });

            return response()->json($rez['body'], $rez['status']);
        } catch (\Throwable $e) {
            return response()->json([
                'poruka' => 'Došlo je do greške pri brisanju. Pokušajte ponovo.'
            ], 500);
        }
    }

    #[OA\Put(path: "/api/izlozbe/{id}/prijave/datum", summary: "Ažuriranje datuma prijave za izložbu (admin)", tags: ["Prijave"],
    security: [["bearerAuth" => []]],
    parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))],
    requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
        required: ["datum_prijave"],
        properties: [new OA\Property(property: "datum_prijave", type: "string", format: "date", example: "2026-03-10")]
    )),
    responses: [new OA\Response(response: 200, description: "OK"), new OA\Response(response: 403, description: "Zabranjeno")]
)]
    
    public function azurirajDatumeZaIzlozbu(Request $request, $id)
    {
        if (!auth()->check() || auth()->user()->uloga !== 'admin') {
            return response()->json(['poruka' => 'Nemate dozvolu za ovu akciju.'], 403);
        }

        $request->validate([
            'datum_prijave' => 'required|date',
        ]);

        $brojAzuriranih = Prijava::where('izlozba_id', $id)
            ->update(['datum_prijave' => $request->datum_prijave]);

        return response()->json([
            'poruka' => "Ažurirano $brojAzuriranih prijava za izložbu sa ID $id.",
        ], 200);
    }
}