<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('izlozbe', function (Blueprint $table) {
            $table->id();
            $table->string('naziv');
            $table->text('opis')->nullable();
            $table->string('lokacija');
            $table->dateTime('datum');
            $table->unsignedInteger('dostupna_mesta');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('izlozbe');
    }
};
