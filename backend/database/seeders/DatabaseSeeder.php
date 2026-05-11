<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\TipeKamar;
use App\Models\Kamar;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin
        User::create([
            'nama_user' => 'Administrator',
            'email' => 'admin@71hotel.com',
            'no_hp' => '081234567890',
            'password' => 'password',
            'role' => 'admin',
        ]);

        // Create receptionist
        User::create([
            'nama_user' => 'Resepsionis',
            'email' => 'resepsionis@71hotel.com',
            'no_hp' => '081234567891',
            'password' => 'password',
            'role' => 'resepsionis',
        ]);

        // Create guest
        User::create([
            'nama_user' => 'Tamu Demo',
            'email' => 'tamu@gmail.com',
            'no_hp' => '081234567892',
            'password' => 'password',
            'role' => 'tamu',
        ]);

        // Create Room Types
        $standard = TipeKamar::create([
            'nama_tipe_kamar' => 'Standard Room',
            'harga' => 250000,
            'deskripsi' => 'Kamar standar yang nyaman dengan fasilitas lengkap untuk istirahat Anda. Dilengkapi dengan tempat tidur single/double, AC, TV LED, dan kamar mandi dalam.',
            'fasilitas_tipe' => ['AC', 'TV LED 32"', 'WiFi Gratis', 'Kamar Mandi Dalam', 'Air Panas', 'Handuk', 'Toiletries'],
            'stok' => 10,
        ]);

        $deluxe = TipeKamar::create([
            'nama_tipe_kamar' => 'Deluxe Room',
            'harga' => 450000,
            'deskripsi' => 'Kamar deluxe yang luas dan elegan dengan pemandangan kota. Dilengkapi dengan tempat tidur king size, sofa, meja kerja, dan minibar.',
            'fasilitas_tipe' => ['AC', 'TV LED 43"', 'WiFi Gratis', 'Kamar Mandi Dalam', 'Air Panas', 'Bathtub', 'Minibar', 'Sofa', 'Meja Kerja', 'Safe Box', 'Handuk', 'Toiletries', 'Coffee Maker'],
            'stok' => 6,
        ]);

        $suite = TipeKamar::create([
            'nama_tipe_kamar' => 'Suite Room',
            'harga' => 850000,
            'deskripsi' => 'Suite mewah dengan ruang tamu terpisah dan pemandangan panoramic. Pengalaman menginap premium dengan layanan butler dan amenities eksklusif.',
            'fasilitas_tipe' => ['AC', 'TV LED 55"', 'WiFi Premium', 'Kamar Mandi Mewah', 'Jacuzzi', 'Bathtub', 'Minibar Premium', 'Ruang Tamu', 'Sofa Set', 'Meja Kerja', 'Safe Box', 'Handuk Premium', 'Toiletries Premium', 'Nespresso Machine', 'Butler Service', 'Balkon Privat'],
            'stok' => 3,
        ]);

        $family = TipeKamar::create([
            'nama_tipe_kamar' => 'Family Room',
            'harga' => 650000,
            'deskripsi' => 'Kamar keluarga yang luas dengan dua tempat tidur dan ruang bermain anak. Cocok untuk liburan keluarga yang menyenangkan.',
            'fasilitas_tipe' => ['AC', 'TV LED 43"', 'WiFi Gratis', 'Kamar Mandi Dalam', 'Air Panas', '2 Double Bed', 'Minibar', 'Meja Kerja', 'Safe Box', 'Handuk', 'Toiletries', 'Extra Bed Available'],
            'stok' => 5,
        ]);

        // Create rooms for each type
        $rooms = [
            [$standard->id, ['101', '102', '103', '104', '105', '201', '202', '203', '204', '205']],
            [$deluxe->id, ['301', '302', '303', '304', '305', '306']],
            [$suite->id, ['401', '402', '403']],
            [$family->id, ['501', '502', '503', '504', '505']],
        ];

        foreach ($rooms as [$tipeId, $nomorKamars]) {
            foreach ($nomorKamars as $nomor) {
                Kamar::create([
                    'id_tipe_kamar' => $tipeId,
                    'nomor_kamar' => $nomor,
                    'status_kamar' => 'tersedia',
                ]);
            }
        }
    }
}
