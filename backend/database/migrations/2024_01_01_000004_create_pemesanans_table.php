<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemesanans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pemesanan', 20)->unique();
            $table->string('nama_pemesan', 100);
            $table->string('email_pemesan', 100);
            $table->timestamp('tgl_pemesanan')->useCurrent();
            $table->date('tgl_check_in');
            $table->date('tgl_check_out');
            $table->integer('jumlah_kamar');
            $table->integer('jumlah_malam');
            $table->bigInteger('total_harga');
            $table->enum('metode_pembayaran', ['transfer', 'cash'])->default('transfer');
            $table->text('bukti_transfer')->nullable();
            $table->bigInteger('denda')->default(0);
            $table->enum('status_pemesanan', ['baru', 'check_in', 'check_out'])->default('baru');
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemesanans');
    }
};
