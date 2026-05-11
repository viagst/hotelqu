<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TipeKamar;
use App\Models\Pemesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TipeKamarController extends Controller
{
    public function index(Request $request)
    {
        $query = TipeKamar::withCount('kamars');

        $tipeKamars = $query->get();

        $checkIn = $request->check_in ?? date('Y-m-d');
        $checkOut = $request->check_out ?? date('Y-m-d', strtotime('+1 day'));

        $tipeKamars->each(function ($tipeKamar) use ($checkIn, $checkOut, $request) {
            $booked = $this->getBookedCount($tipeKamar->id, $checkIn, $checkOut);
            $tipeKamar->available = max(0, $tipeKamar->stok - $booked);
            $tipeKamar->is_available = $tipeKamar->available > 0;

            $isAdminRequest = $request->is('admin/*') || $request->is('api/admin/*');

            // Untuk admin, jangan mengubah field stok master.
            if (!$isAdminRequest) {
                $tipeKamar->stok = $tipeKamar->available;
            }

            // Pastikan fasilitas_tipe selalu array untuk frontend.
            $tipeKamar->fasilitas_tipe = is_array($tipeKamar->fasilitas_tipe)
                ? $tipeKamar->fasilitas_tipe
                : (is_string($tipeKamar->fasilitas_tipe)
                    ? (json_decode($tipeKamar->fasilitas_tipe, true) ?: [])
                    : []);
        });


        return response()->json($tipeKamars);
    }

    public function show($id, Request $request)
    {
        $tipeKamar = TipeKamar::with('kamars')->findOrFail($id);
        $checkIn = $request->check_in ?? date('Y-m-d');
        $checkOut = $request->check_out ?? date('Y-m-d', strtotime('+1 day'));
        $booked = $this->getBookedCount($tipeKamar->id, $checkIn, $checkOut);
        $tipeKamar->available = max(0, $tipeKamar->stok - $booked);
        $tipeKamar->is_available = $tipeKamar->available > 0;
        
        $tipeKamar->kamars->each(function ($kamar) use ($checkIn, $checkOut) {
            $isBooked = \App\Models\DetailPemesanan::where('id_kamar', $kamar->id)
                ->whereHas('pemesanan', function ($q) use ($checkIn, $checkOut) {
                    $q->where('status_pemesanan', '!=', 'check_out')
                      ->where('tgl_check_in', '<', $checkOut)
                      ->where('tgl_check_out', '>', $checkIn);
                })->exists();
            $kamar->is_available = !$isBooked && in_array($kamar->status_kamar, ['tersedia', 'bersih']);
        });

        $isAdminRequest = $request->is('admin/*') || $request->is('api/admin/*');

        // Untuk admin (/admin/*) jangan ubah stok master.
        if ($isAdminRequest) {
            $tipeKamar->stok = (int) ($tipeKamar->getOriginal('stok') ?? $tipeKamar->stok);
            return response()->json($tipeKamar);
        }

        $tipeKamar->stok = $tipeKamar->available;


        return response()->json($tipeKamar);

    }

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
        ]);

        $tipeKamars = TipeKamar::all();

        $result = $tipeKamars->map(function ($tipeKamar) use ($request) {
            $booked = $this->getBookedCount($tipeKamar->id, $request->check_in, $request->check_out);
            $available = max(0, $tipeKamar->stok - $booked);

            return [
                'id' => $tipeKamar->id,
                'nama_tipe_kamar' => $tipeKamar->nama_tipe_kamar,
                'harga' => $tipeKamar->harga,
                'deskripsi' => $tipeKamar->deskripsi,
                'foto' => $tipeKamar->foto,
                'fasilitas_tipe' => $tipeKamar->fasilitas_tipe,
                'stok' => $tipeKamar->stok,
                'available' => $available,
                'is_available' => $available > 0,
            ];
        });

        return response()->json($result);
    }

    // Admin CRUD
    public function store(Request $request)
    {
        $request->validate([
            'nama_tipe_kamar' => 'required|string|max:100',
            'harga' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|max:2048',
            'fasilitas_tipe' => 'nullable|array',
            'stok' => 'required|integer|min:0',
        ]);

        $data = $request->except(['foto', '_method']);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('tipe_kamar', 'public');
        }

        // Remove manual JSON encoding since model cast handles it automatically
        // if (isset($data['fasilitas_tipe'])) {
        //     $data['fasilitas_tipe'] = json_encode($data['fasilitas_tipe']);
        // }

        $tipeKamar = TipeKamar::create($data);

        return response()->json([
            'message' => 'Tipe kamar berhasil ditambahkan',
            'data' => $tipeKamar,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $tipeKamar = TipeKamar::findOrFail($id);

        $request->validate([
            'nama_tipe_kamar' => 'sometimes|string|max:100',
            'harga' => 'sometimes|integer|min:0',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|max:2048',
            'fasilitas_tipe' => 'nullable|array',
            'stok' => 'sometimes|integer|min:0',
        ]);

        // Critical: jangan pernah override stok master kecuali user memang mengirim field stok.
        // Di UI saat klik edit biasanya user tidak berniat mengubah stok, tapi tetap terkirim.
        // Agar sesuai requirement, stok master dipakai apa adanya.
        $data = $request->except(['foto', '_method']);
        if (!$request->has('stok')) {
            unset($data['stok']);
        }


        if ($request->hasFile('foto')) {
            if ($tipeKamar->foto) {
                Storage::disk('public')->delete($tipeKamar->foto);
            }
            $data['foto'] = $request->file('foto')->store('tipe_kamar', 'public');
        }

        // Remove manual JSON encoding since model cast handles it automatically
        // if (isset($data['fasilitas_tipe'])) {
        //     $data['fasilitas_tipe'] = json_encode($data['fasilitas_tipe']);
        // }

        $tipeKamar->update($data);

        return response()->json([
            'message' => 'Tipe kamar berhasil diupdate',
            'data' => $tipeKamar->fresh(),
        ]);
    }

    public function destroy($id)
    {
        $tipeKamar = TipeKamar::findOrFail($id);

        if ($tipeKamar->foto) {
            Storage::disk('public')->delete($tipeKamar->foto);
        }

        $tipeKamar->delete();

        return response()->json(['message' => 'Tipe kamar berhasil dihapus']);
    }

    private function getBookedCount($tipeKamarId, $checkIn, $checkOut)
    {
        return Pemesanan::where('status_pemesanan', '!=', 'check_out')
            ->where('tgl_check_in', '<', $checkOut)
            ->where('tgl_check_out', '>', $checkIn)
            ->whereHas('detailPemesanans', function ($q) use ($tipeKamarId) {
                $q->where('id_tipe_kamar', $tipeKamarId);
            })
            ->withCount(['detailPemesanans' => function ($q) use ($tipeKamarId) {
                $q->where('id_tipe_kamar', $tipeKamarId);
            }])
            ->get()
            ->sum('detail_pemesanans_count');
    }
}
