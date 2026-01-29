<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Izlozba extends Model
{
    use HasFactory;

    protected $table = 'izlozbe';

    protected $fillable = [
        'naziv',
        'opis',
        'lokacija',
        'datum',
        'dostupnaMesta'
    ];

    public function prijave()
{
    return $this->hasMany(Prijava::class, 'izlozba_id');
}

   
public function fotografije()
{
    return $this->hasMany(\App\Models\Fotografija::class);
}


}