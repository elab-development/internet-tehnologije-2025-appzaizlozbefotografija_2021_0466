<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fotografije', function (Blueprint $table) {
            $table->id();

            $table->foreignId('izlozba_id')
                ->constrained('izlozbe')
                ->cascadeOnDelete();

            $table->string('naziv');
            $table->text('opis')->nullable();
            $table->string('url')->nullable();
            $table->string('putanja_slike');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fotografije');
    }
};
