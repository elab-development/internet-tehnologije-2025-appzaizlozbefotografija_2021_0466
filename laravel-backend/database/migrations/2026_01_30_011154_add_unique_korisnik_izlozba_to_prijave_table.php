<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prijave', function (Blueprint $table) {
            $table->unique(
                ['korisnik_id', 'izlozba_id'],
                'prijave_korisnik_izlozba_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('prijave', function (Blueprint $table) {
            $table->dropUnique('prijave_korisnik_izlozba_unique');
        });
    }
};
