<?php

use Illuminate\Support\Facades\Storage;


Route::get('/slika/{path}', function ($path) {
    abort_unless(Storage::disk('public')->exists($path), 404);

    $fullPath = Storage::disk('public')->path($path);

    return response()->file($fullPath, [
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->where('path', '.*');