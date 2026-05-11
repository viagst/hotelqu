<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipeKamar extends Model
{
    protected $fillable = [
        'nama_tipe_kamar', 'harga', 'deskripsi', 'foto', 'fasilitas_tipe', 'stok',
    ];

    protected function casts(): array
    {
        return [
            'fasilitas_tipe' => 'array',
            'harga' => 'integer',
            'stok' => 'integer',
        ];
    }

    public function getFasilitasTipeAttribute($value)
    {
        if (!$value) return [];
        if (is_array($value)) return $value;
        
        try {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function kamars()
    {
        return $this->hasMany(Kamar::class, 'id_tipe_kamar');
    }

    public function detailPemesanans()
    {
        return $this->hasMany(DetailPemesanan::class, 'id_tipe_kamar');
    }
}
