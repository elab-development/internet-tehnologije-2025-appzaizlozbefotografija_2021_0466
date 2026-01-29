<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prijava extends Model
{
    use HasFactory;

    protected $table = 'prijave';

    protected $fillable = [
        'korisnik_id',
        'izlozba_id',
        'datum_prijave',
        'qr_kod',
    ];

    public function korisnik()
    {
        return $this->belongsTo(Korisnik::class, 'korisnik_id');
    }

    public function izlozba()
    {
        return $this->belongsTo(Izlozba::class, 'izlozba_id');
    }
}