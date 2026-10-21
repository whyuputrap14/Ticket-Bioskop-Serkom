<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('showtimes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('film_id')->constrained()->cascadeOnDelete();
            $table->dateTime('starts_at');
            $table->string('hall');
            $table->timestamps();
            $table->unique(['film_id', 'starts_at', 'hall']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('showtimes');
    }
};
