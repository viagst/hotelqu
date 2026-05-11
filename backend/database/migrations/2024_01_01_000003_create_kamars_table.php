<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kamars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tipe_kamar')->constrained('tipe_kamars')->onDelete('cascade');
            $table->string('nomor_kamar', 10);
            $table->enum('status_kamar', ['tersedia', 'kotor', 'perbaikan'])->default('tersedia');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kamars');
    }
};
