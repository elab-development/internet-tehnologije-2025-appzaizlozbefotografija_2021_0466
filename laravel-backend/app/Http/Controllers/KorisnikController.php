<?php

namespace App\Http\Controllers;

use App\Models\Korisnik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class KorisnikController extends Controller
{
    private function samoAdmin()
    {
        if (!auth()->check() || auth()->user()->uloga !== 'admin') {
            abort(response()->json(['poruka' => 'Nemate dozvolu za ovu akciju.'], 403));
        }
    }

    // ADMIN: lista korisnika
    public function index()
    {
        $this->samoAdmin();
        return response()->json(Korisnik::all(), 200);
    }

    // ADMIN: dodavanje korisnika (opciono)
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

    // ADMIN: promena uloge / emaila / lozinke
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

    // ADMIN: brisanje korisnika (opciono)
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

    // JAVNO/ZAŠTIĆENO (po tvojoj ruti): izložbe korisnika (preko prijava)
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
