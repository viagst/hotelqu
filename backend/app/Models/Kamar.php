<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kamar extends Model
{
    protected $fillable = [
        'id_tipe_kamar', 'nomor_kamar', 'status_kamar',
    ];

    public function tipeKamar()
    {
        return $this->belongsTo(TipeKamar::class, 'id_tipe_kamar');
    }

    public function detailPemesanans()
    {
        return $this->hasMany(DetailPemesanan::class, 'id_kamar');
    }
}
