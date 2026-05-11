<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPemesanan extends Model
{
    protected $fillable = [
        'id_pemesanan', 'id_tipe_kamar', 'id_kamar',
        'nama_tamu_spesifik', 'harga_kamar_instance',
    ];

    protected function casts(): array
    {
        return [
            'harga_kamar_instance' => 'integer',
        ];
    }

    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class, 'id_pemesanan');
    }

    public function tipeKamar()
    {
        return $this->belongsTo(TipeKamar::class, 'id_tipe_kamar');
    }

    public function kamar()
    {
        return $this->belongsTo(Kamar::class, 'id_kamar');
    }
}
