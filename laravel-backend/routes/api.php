<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\IzlozbaController;
use App\Http\Controllers\FotografijaController;
use App\Http\Controllers\PrijavaController;
use App\Http\Controllers\KorisnikController;
use App\Http\Controllers\AutentifikacijaController;

// =====================
// AUTH (javne rute)
// =====================
Route::post('/registracija', [AutentifikacijaController::class, 'registracija']);
Route::post('/prijava', [AutentifikacijaController::class, 'prijava']);
Route::post('/zaboravljena-lozinka', [AutentifikacijaController::class, 'resetujLozinku']);

// =====================
// PUBLIC GET rute
// =====================
Route::get('/izlozbe', [IzlozbaController::class, 'index']);
Route::get('/izlozbe/{id}', [IzlozbaController::class, 'show']);
Route::get('/izlozbe/{id}/fotografije', [IzlozbaController::class, 'fotografije']);
Route::get('/izlozbe/{id}/prijave', [IzlozbaController::class, 'prijave']);

Route::get('/fotografije', [FotografijaController::class, 'index']);
Route::get('/fotografije/{id}', [FotografijaController::class, 'show']);

Route::get('/test', fn() => response()->json(['radi' => true]));

// =====================
// PROTECTED (auth:sanctum)
// =====================
Route::middleware('auth:sanctum')->group(function () {

    // Auth helper rute
    Route::post('/odjava', [AutentifikacijaController::class, 'odjava']);
    Route::get('/korisnik', [AutentifikacijaController::class, 'korisnik']);

    Route::get('/zasticeno', function (Request $request) {
        return response()->json([
            'poruka' => 'Pristup uspešan. Dobrodošao, ' .
                $request->user()->ime . ' ' . $request->user()->prezime
        ]);
    });

    // Izlozbe CRUD (admin proverava u kontroleru)
    Route::post('/izlozbe', [IzlozbaController::class, 'store']);
    Route::put('/izlozbe/{id}', [IzlozbaController::class, 'update']);
    Route::delete('/izlozbe/{id}', [IzlozbaController::class, 'destroy']);

    // Fotografije (store fotograf; destroy admin+fotograf proverava u kontroleru)
    Route::post('/fotografije', [FotografijaController::class, 'store']);
    Route::delete('/fotografije/{id}', [FotografijaController::class, 'destroy']);

    // Prijave (posetilac; delete admin ili vlasnik proverava u kontroleru)
    Route::post('/prijave', [PrijavaController::class, 'store']);
    Route::delete('/prijave/{id}', [PrijavaController::class, 'destroy']);

    // Admin pomocno
    Route::put('/izlozbe/{id}/prijave/datum', [PrijavaController::class, 'azurirajDatumeZaIzlozbu']);

    // Korisnici (ADMIN only - provera u KorisnikController)
    Route::get('/korisnici', [KorisnikController::class, 'index']);
    Route::post('/korisnici', [KorisnikController::class, 'store']);
    Route::put('/korisnici/{id}', [KorisnikController::class, 'update']);
    Route::delete('/korisnici/{id}', [KorisnikController::class, 'destroy']);

    // Izlozbe korisnika (preko prijava)
    Route::get('/korisnici/{id}/izlozbe', [KorisnikController::class, 'izlozbe']);
});


