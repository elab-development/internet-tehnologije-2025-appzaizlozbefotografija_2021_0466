<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fotografija;

class FotografijaController extends Controller
{
    public function index()
    {
        return response()->json(Fotografija::with('izlozba')->get(), 200);
    }

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
            $request->file('slika')->move(public_path('fotografije'), $imeFajla);
            $fotografija->putanja_slike = 'fotografije/' . $imeFajla;
        } else {
            $fotografija->putanja_slike = $validated['url'];
        }

        $fotografija->save();

        return response()->json($fotografija->load('izlozba'), 201);
    }

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