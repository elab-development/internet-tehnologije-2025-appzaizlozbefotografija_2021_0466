<?php

namespace App\Models;

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

    public function prijave()
    {
        return $this->hasMany(Prijava::class, 'korisnik_id');
    }
}