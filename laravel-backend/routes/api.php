<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\IzlozbaController;
use App\Http\Controllers\FotografijaController;
use App\Http\Controllers\PrijavaController;
use App\Http\Controllers\KorisnikController;
use App\Http\Controllers\AutentifikacijaController;



Route::post('/registracija', [AutentifikacijaController::class, 'registracija']);
Route::post('/prijava', [AutentifikacijaController::class, 'prijava']);
Route::post('/zaboravljena-lozinka', [AutentifikacijaController::class, 'resetujLozinku']);

Route::get('/izlozbe', [IzlozbaController::class, 'index']);
Route::get('/izlozbe/{id}', [IzlozbaController::class, 'show']);
Route::get('/izlozbe/{id}/fotografije', [IzlozbaController::class, 'fotografije']);
Route::get('/izlozbe/{id}/prijave', [IzlozbaController::class, 'prijave']);

Route::get('/fotografije', [FotografijaController::class, 'index']);
Route::get('/fotografije/{id}', [FotografijaController::class, 'show']);

Route::get('/test', fn () => response()->json(['radi' => true]));

Route::middleware('auth:sanctum')->group(function () {

Route::post('/odjava', [AutentifikacijaController::class, 'odjava']);
Route::get('/korisnik', [AutentifikacijaController::class, 'korisnik']);

Route::get('/zasticeno', function (Request $request) {
    return response()->json([
            'poruka' => 'Pristup uspešan. Dobrodošao, ' .
                $request->user()->ime . ' ' . $request->user()->prezime
    ]);
});

Route::post('/izlozbe', [IzlozbaController::class, 'store']);
Route::put('/izlozbe/{id}', [IzlozbaController::class, 'update']);
Route::delete('/izlozbe/{id}', [IzlozbaController::class, 'destroy']);

Route::post('/fotografije', [FotografijaController::class, 'store']);
Route::delete('/fotografije/{id}', [FotografijaController::class, 'destroy']);
 
Route::get('/prijave', [PrijavaController::class, 'index']); 
Route::post('/prijave', [PrijavaController::class, 'store']);
Route::delete('/prijave/{id}', [PrijavaController::class, 'destroy']);

Route::put('/izlozbe/{id}/prijave/datum', [PrijavaController::class, 'azurirajDatumeZaIzlozbu']);

Route::get('/korisnici', [KorisnikController::class, 'index']);
Route::post('/korisnici', [KorisnikController::class, 'store']);
Route::put('/korisnici/{id}', [KorisnikController::class, 'update']);
Route::delete('/korisnici/{id}', [KorisnikController::class, 'destroy']);

Route::get('/korisnici/{id}/izlozbe', [KorisnikController::class, 'izlozbe']);
});
