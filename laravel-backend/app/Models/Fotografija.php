<?php

namespace App\Models;

use App\Models\Izlozba;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fotografija extends Model
{
    use HasFactory;

    protected $table = 'fotografije';

    protected $fillable = [
        'izlozba_id',
        'url',
        'naziv',
        'opis',
        'putanja_slike'
    ];

    public function izlozba()
    {
        return $this->belongsTo(Izlozba::class, 'izlozba_id');
    }
}