<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prijave', function (Blueprint $table) {
            $table->id();

            $table->foreignId('korisnik_id')
                ->constrained('korisnici')
                ->cascadeOnDelete();

            $table->foreignId('izlozba_id')
                ->constrained('izlozbe')
                ->cascadeOnDelete();

            $table->dateTime('datum_prijave');
            $table->string('qr_kod')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prijave');
    }
};
