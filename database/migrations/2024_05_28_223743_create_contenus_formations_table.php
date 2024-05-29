<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contenus_formations', function (Blueprint $table) {
           $table->id();
            $table->integer('NumChap');
            $table->integer('NumUnite');
            $table->text('description');
            $table->integer('NombreHeures');
            $table->foreignId('formation_id')->constrained('formations');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contenus_formations');
    }
};