<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemesanan extends Model
{
    protected $fillable = [
        'nomor_pemesanan', 'nama_pemesan', 'email_pemesan',
        'tgl_pemesanan', 'tgl_check_in', 'tgl_check_out',
        'jumlah_kamar', 'jumlah_malam', 'total_harga',
        'metode_pembayaran', 'bukti_transfer', 'denda',
        'status_pemesanan', 'status_pembayaran', 'id_user',
    ];

    protected function casts(): array
    {
        return [
            'tgl_pemesanan' => 'datetime',
            'tgl_check_in' => 'date',
            'tgl_check_out' => 'date',
            'total_harga' => 'integer',
            'denda' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function detailPemesanans()
    {
        return $this->hasMany(DetailPemesanan::class, 'id_pemesanan');
    }
}
