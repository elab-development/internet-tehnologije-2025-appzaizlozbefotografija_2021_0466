<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prijava;
use App\Models\Izlozba;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PrijavaController extends Controller
{
    // Kreiranje nove prijave (posetilac)
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

                if (($izlozba->dostupnaMesta ?? 0) <= 0) {
                    return ['status' => 422, 'body' => ['poruka' => 'Nema više slobodnih mesta.']];
                }

                $prijava = Prijava::create([
                    'korisnik_id' => auth()->id(),
                    'izlozba_id' => $validated['izlozba_id'],
                    'datum_prijave' => now()->toDateString(),
                    'qr_kod' => (string) Str::uuid(),
                ]);

                $izlozba->decrement('dostupnaMesta');

                return [
                    'status' => 201,
                    'body' => [
                        'poruka' => 'Uspešno rezervisano.',
                        'prijava' => $prijava,
                        'preostaloMesta' => $izlozba->dostupnaMesta,
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

    // Brisanje prijave (admin ili posetilac koji je napravio prijavu)
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

                if ($izlozba) {
                    $izlozba->increment('dostupnaMesta');
                    $preostalo = $izlozba->dostupnaMesta;
                } else {
                    $preostalo = null;
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

    // Ažuriranje datuma svih prijava za izložbu (admin)
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
