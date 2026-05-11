<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipe_kamars', function (Blueprint $table) {
            $table->id();
            $table->string('nama_tipe_kamar', 100);
            $table->bigInteger('harga');
            $table->text('deskripsi')->nullable();
            $table->text('foto')->nullable();
            $table->json('fasilitas_tipe')->nullable();
            $table->integer('stok')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipe_kamars');
    }
};
