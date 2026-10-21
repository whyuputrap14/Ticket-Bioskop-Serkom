<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->foreignId('showtime_id')
                ->nullable()
                ->after('film_id')
                ->constrained('showtimes')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropConstrainedForeignId('showtime_id');
        });
    }
};
