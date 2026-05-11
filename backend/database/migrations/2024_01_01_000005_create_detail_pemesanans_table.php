<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_pemesanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pemesanan')->constrained('pemesanans')->onDelete('cascade');
            $table->foreignId('id_tipe_kamar')->constrained('tipe_kamars')->onDelete('cascade');
            $table->foreignId('id_kamar')->constrained('kamars')->onDelete('cascade');
            $table->string('nama_tamu_spesifik', 100)->nullable();
            $table->bigInteger('harga_kamar_instance');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pemesanans');
    }
};
