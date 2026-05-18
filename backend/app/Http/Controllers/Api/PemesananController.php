<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use App\Models\DetailPemesanan;
use App\Models\TipeKamar;
use App\Models\Kamar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PemesananController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'id_tipe_kamar' => 'required|exists:tipe_kamars,id',
            'tgl_check_in' => 'required|date|after_or_equal:today',
            'tgl_check_out' => 'required|date|after:tgl_check_in',
            'selected_rooms' => 'required|array|min:1',
            'selected_rooms.*' => 'exists:kamars,id',
            'nama_tamu' => 'required|array',
            'nama_tamu.*' => 'required|string|max:100',
            'metode_pembayaran' => 'required|in:transfer,cash',
        ]);

        $tipeKamar = TipeKamar::findOrFail($request->id_tipe_kamar);
        $checkIn = $request->tgl_check_in;
        $checkOut = $request->tgl_check_out;
        $jumlahKamar = count($request->selected_rooms);

        // Check availability
        $bookedCount = $this->getBookedCount($tipeKamar->id, $checkIn, $checkOut);
        $available = $tipeKamar->stok - $bookedCount;

        if ($available < $jumlahKamar) {
            return response()->json([
                'message' => 'Maaf, kamar tidak tersedia untuk tanggal yang dipilih. Tersedia: ' . $available . ' kamar.',
            ], 422);
        }

        // Calculate nights and total
        $nights = Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut));
        $totalHarga = $tipeKamar->harga * $jumlahKamar * $nights;

        // Payment method validation
        if ($totalHarga > 500000 && $request->metode_pembayaran === 'cash') {
            return response()->json([
                'message' => 'Total pemesanan di atas Rp 500.000 wajib menggunakan transfer bank.',
            ], 422);
        }

        return DB::transaction(function () use ($request, $tipeKamar, $checkIn, $checkOut, $jumlahKamar, $nights, $totalHarga) {
            // Generate booking number
            $today = Carbon::now()->format('Ymd');
            $count = Pemesanan::whereDate('tgl_pemesanan', Carbon::today())->count() + 1;
            $nomorPemesanan = 'BO-' . $today . str_pad($count, 4, '0', STR_PAD_LEFT);

            $pemesanan = Pemesanan::create([
                'nomor_pemesanan' => $nomorPemesanan,
                'nama_pemesan' => $request->user()->nama_user,
                'email_pemesan' => $request->user()->email,
                'tgl_pemesanan' => Carbon::now(),
                'tgl_check_in' => $checkIn,
                'tgl_check_out' => $checkOut,
                'jumlah_kamar' => $jumlahKamar,
                'jumlah_malam' => $nights,
                'total_harga' => $totalHarga,
                'metode_pembayaran' => $request->metode_pembayaran,
                'status_pemesanan' => 'baru',
                'status_pembayaran' => 'belum_dibayar',
                'id_user' => $request->user()->id,
            ]);

            // Validate and assign selected rooms
            $availableRooms = Kamar::whereIn('id', $request->selected_rooms)
                ->whereIn('status_kamar', ['tersedia', 'bersih'])
                ->whereDoesntHave('detailPemesanans', function ($q) use ($checkIn, $checkOut) {
                    $q->whereHas('pemesanan', function ($pq) use ($checkIn, $checkOut) {
                        $pq->where('status_pemesanan', '!=', 'check_out')
                            ->where('tgl_check_in', '<', $checkOut)
                            ->where('tgl_check_out', '>', $checkIn);
                    });
                })->get();

            if ($availableRooms->count() < count($request->selected_rooms)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'selected_rooms' => 'Beberapa kamar yang dipilih sudah tidak tersedia.'
                ]);
            }

            foreach ($availableRooms as $index => $kamar) {
                DetailPemesanan::create([
                    'id_pemesanan' => $pemesanan->id,
                    'id_tipe_kamar' => $tipeKamar->id,
                    'id_kamar' => $kamar->id,
                    'nama_tamu_spesifik' => $request->nama_tamu[$index] ?? $request->user()->nama_user,
                    'harga_kamar_instance' => $tipeKamar->harga,
                ]);
            }

            return response()->json([
                'message' => 'Pemesanan berhasil dibuat',
                'data' => $pemesanan->load('detailPemesanans.kamar', 'detailPemesanans.tipeKamar'),
            ], 201);
        });
    }

    public function uploadBukti(Request $request, $id)
    {
        $request->validate([
            'bukti_transfer' => 'required|image|max:2048',
        ]);

        $pemesanan = Pemesanan::where('id', $id)
            ->where('id_user', $request->user()->id)
            ->firstOrFail();

        if ($request->hasFile('bukti_transfer')) {
            if ($pemesanan->bukti_transfer) {
                Storage::disk('public')->delete($pemesanan->bukti_transfer);
            }
            $path = $request->file('bukti_transfer')->store('bukti_transfer', 'public');
            $pemesanan->update([
                'bukti_transfer' => $path,
                'status_pembayaran' => 'menunggu_validasi'
            ]);
        }

        return response()->json([
            'message' => 'Bukti transfer berhasil diupload',
            'data' => $pemesanan->fresh(),
        ]);
    }

    public function cekPesanan(Request $request)
    {
        $request->validate([
            'nomor_pemesanan' => 'required|string',
            'email' => 'required|email',
        ]);

        $pemesanan = Pemesanan::with('detailPemesanans.kamar', 'detailPemesanans.tipeKamar', 'user')
            ->where('nomor_pemesanan', $request->nomor_pemesanan)
            ->where('email_pemesan', $request->email)
            ->firstOrFail();

        return response()->json($pemesanan);
    }

    public function invoice($id)
    {
        $pemesanan = Pemesanan::with('detailPemesanans.kamar', 'detailPemesanans.tipeKamar', 'user')
            ->findOrFail($id);

        return response()->json($pemesanan);
    }

    public function validasiPembayaran(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:lunas,belum_dibayar',
            'jumlah_bayar' => 'nullable|numeric|min:0',
            'kembalian' => 'nullable|numeric|min:0',
        ]);

        $pemesanan = Pemesanan::findOrFail($id);
        $updateData = ['status_pembayaran' => $request->status];

        if ($request->status === 'lunas') {
            if ($request->filled('jumlah_bayar')) {
                $updateData['jumlah_bayar'] = $request->jumlah_bayar;
            }
            if ($request->filled('kembalian')) {
                $updateData['kembalian'] = $request->kembalian;
            } elseif ($request->filled('jumlah_bayar')) {
                $totalBayar = $pemesanan->total_harga + ($pemesanan->denda ?? 0);
                $updateData['kembalian'] = max(0, $request->jumlah_bayar - $totalBayar);
            }
        }

        if ($request->status === 'belum_dibayar') {
            if ($pemesanan->bukti_transfer) {
                Storage::disk('public')->delete($pemesanan->bukti_transfer);
            }
            $updateData['bukti_transfer'] = null;
        }

        $pemesanan->update($updateData);

        return response()->json([
            'message' => 'Status pembayaran berhasil diupdate',
            'data' => $pemesanan->fresh()->load('detailPemesanans.kamar', 'detailPemesanans.tipeKamar', 'user'),
        ]);
    }

    public function indexAll(Request $request)
    {
        $query = Pemesanan::with('detailPemesanans.kamar', 'detailPemesanans.tipeKamar', 'user');

        if ($request->has('tgl_check_in')) {
            $query->whereDate('tgl_check_in', $request->tgl_check_in);
        }

        if ($request->has('nama_tamu')) {
            $query->where('nama_pemesan', 'like', '%' . $request->nama_tamu . '%');
        }

        if ($request->has('status')) {
            $query->where('status_pemesanan', $request->status);
        }

        $pemesanans = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($pemesanans);
    }

    public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status_pemesanan' => 'required|in:baru,check_in,check_out',
        'actual_checkout_time' => 'nullable|string',
        'jumlah_bayar' => 'nullable|integer|min:0',
    ]);

    $pemesanan = Pemesanan::findOrFail($id);

    if (
    $request->status_pemesanan === 'check_in' &&
    $pemesanan->metode_pembayaran === 'transfer' &&
    $pemesanan->status_pembayaran !== 'lunas'
)
{
    return response()->json([
        'message' => 'Gagal check-in: Pembayaran transfer belum lunas.',
    ], 422);
}

    $updateData = [
        'status_pemesanan' => $request->status_pemesanan
    ];

    // =========================
    // CHECK OUT
    // =========================
    if ($request->status_pemesanan === 'check_out') {

        $expectedCheckout = Carbon::parse($pemesanan->tgl_check_out)
            ->setHour(12)
            ->setMinute(0)
            ->setSecond(0);

        // actual checkout
        if ($request->filled('actual_checkout_time')) {
            try {
                $actualCheckout = Carbon::parse($request->actual_checkout_time);
            } catch (\Exception $e) {
                $actualCheckout = Carbon::now();
            }
        } else {
            $actualCheckout = Carbon::now();
        }

        // =========================
        // HITUNG DENDA
        // =========================
        $denda = 0;

        if ($actualCheckout->gt($expectedCheckout)) {
            $hoursLate = (int) ceil(
                $expectedCheckout->diffInMinutes($actualCheckout) / 60
            );

            $denda = $hoursLate * 50000;
        }

        $updateData['denda'] = $denda;

        // =========================
        // TOTAL PEMBAYARAN
        // =========================
        $totalBayar = $pemesanan->total_harga + $denda;

        // =========================
        // CASH
        // =========================
        if ($pemesanan->metode_pembayaran === 'cash') {

            $jumlahBayar = $request->jumlah_bayar ?? 0;

            if ($jumlahBayar < $totalBayar) {
                return response()->json([
                    'message' => 'Uang pembayaran kurang.',
                ], 422);
            }

            $kembalian = $jumlahBayar - $totalBayar;

            $updateData['jumlah_bayar'] = $jumlahBayar;
            $updateData['kembalian'] = $kembalian;
            $updateData['status_pembayaran'] = 'lunas';
        }

        // =========================
        // TRANSFER
        // =========================
        if ($pemesanan->metode_pembayaran === 'transfer') {

            if ($denda > 0) {
                $updateData['status_pembayaran'] = 'belum_dibayar';
            }
        }
    }

    $pemesanan->update($updateData);

    // kamar jadi kotor setelah checkout
    if ($request->status_pemesanan === 'check_out') {

        $kamarIds = $pemesanan
            ->detailPemesanans()
            ->pluck('id_kamar');

        \App\Models\Kamar::whereIn('id', $kamarIds)
            ->update([
                'status_kamar' => 'kotor'
            ]);
    }

    return response()->json([
        'message' => 'Status pemesanan berhasil diupdate',
        'data' => $pemesanan->fresh()->load(
            'detailPemesanans.kamar',
            'detailPemesanans.tipeKamar'
        ),
    ]);
}

    public function myBookings(Request $request)
    {
        $pemesanans = Pemesanan::with('detailPemesanans.kamar', 'detailPemesanans.tipeKamar')
            ->where('id_user', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($pemesanans);
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
