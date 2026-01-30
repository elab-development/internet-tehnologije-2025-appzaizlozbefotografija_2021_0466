<?php

namespace App\Models;

use App\Models\Prijava;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Korisnik extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'korisnici';

    protected $fillable = [
        'ime',
        'prezime',
        'email',
        'lozinka',
        'uloga',
    ];

    protected $hidden = [
        'lozinka',
        'remember_token',
    ];

    // Laravel auth koristi password polje po default-u, a kod tebe je "lozinka"
    public function getAuthPassword()
    {
        return $this->lozinka;
    }

    public function prijave()
    {
        return $this->hasMany(Prijava::class, 'korisnik_id');
    }
}