<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanController extends Controller
{
    // Admin: Revenue report by date range
    public function pendapatan(Request $request)
    {
        $request->validate([
            'dari_tanggal' => 'required|date',
            'sampai_tanggal' => 'required|date|after_or_equal:dari_tanggal',
        ]);

        $dari = $request->dari_tanggal;
        $sampai = $request->sampai_tanggal;

        $pemesanans = Pemesanan::with('detailPemesanans.tipeKamar', 'detailPemesanans.kamar', 'user')
            ->whereDate('tgl_pemesanan', '>=', $dari)
            ->whereDate('tgl_pemesanan', '<=', $sampai)
            ->orderBy('tgl_pemesanan', 'desc')
            ->get();

        $totalPendapatan = $pemesanans->sum('total_harga');
        $totalDenda = $pemesanans->sum('denda');
        $totalBooking = $pemesanans->count();

        return response()->json([
            'periode' => [
                'dari' => $dari,
                'sampai' => $sampai,
            ],
            'ringkasan' => [
                'total_pendapatan' => $totalPendapatan,
                'total_denda' => $totalDenda,
                'total_keseluruhan' => $totalPendapatan + $totalDenda,
                'total_booking' => $totalBooking,
            ],
            'data' => $pemesanans,
        ]);
    }

    // Receptionist: Daily check-in/check-out report
    public function harian(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d'));

        $checkIns = Pemesanan::with('detailPemesanans.kamar', 'detailPemesanans.tipeKamar', 'user')
            ->whereDate('tgl_check_in', $tanggal)
            ->orderBy('created_at', 'desc')
            ->get();

        $checkOuts = Pemesanan::with('detailPemesanans.kamar', 'detailPemesanans.tipeKamar', 'user')
            ->whereDate('tgl_check_out', $tanggal)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'tanggal' => $tanggal,
            'check_in' => [
                'total' => $checkIns->count(),
                'data' => $checkIns,
            ],
            'check_out' => [
                'total' => $checkOuts->count(),
                'data' => $checkOuts,
            ],
        ]);
    }

    // Dashboard stats
    public function dashboard()
    {
        $today = Carbon::today();

        $totalBookings = Pemesanan::count();
        $bookingsToday = Pemesanan::whereDate('tgl_pemesanan', $today)->count();
        $checkInsToday = Pemesanan::whereDate('tgl_check_in', $today)->count();
        $checkOutsToday = Pemesanan::whereDate('tgl_check_out', $today)->count();
        $activeBookings = Pemesanan::where('status_pemesanan', 'check_in')->count();

        $revenueThisMonth = Pemesanan::whereMonth('tgl_pemesanan', $today->month)
            ->whereYear('tgl_pemesanan', $today->year)
            ->sum('total_harga');

        $dendaThisMonth = Pemesanan::whereMonth('tgl_pemesanan', $today->month)
            ->whereYear('tgl_pemesanan', $today->year)
            ->sum('denda');

        $recentBookings = Pemesanan::with('user', 'detailPemesanans.tipeKamar')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return response()->json([
            'stats' => [
                'total_bookings' => $totalBookings,
                'bookings_today' => $bookingsToday,
                'check_ins_today' => $checkInsToday,
                'check_outs_today' => $checkOutsToday,
                'active_bookings' => $activeBookings,
                'revenue_this_month' => $revenueThisMonth,
                'denda_this_month' => $dendaThisMonth,
            ],
            'recent_bookings' => $recentBookings,
        ]);
    }
}
