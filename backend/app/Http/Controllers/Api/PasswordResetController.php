<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user->no_hp) {
            return response()->json([
                'message' => 'Akun ini belum memiliki nomor HP. Hubungi admin untuk reset password.',
            ], 422);
        }

        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Delete old OTPs
        PasswordReset::where('email', $request->email)->delete();

        // Save new OTP
        PasswordReset::create([
            'email' => $request->email,
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);

        // Send OTP via Fonnte WhatsApp
        try {
            $response = Http::withHeaders([
                'Authorization' => config('services.fonnte.token'),
            ])->post('https://api.fonnte.com/send', [
                'target' => $user->no_hp,
                'message' => "🏨 *71 Hotel - Reset Password*\n\nKode OTP Anda: *{$otp}*\n\nKode ini berlaku selama 5 menit.\nJangan bagikan kode ini kepada siapapun.",
                'countryCode' => '62',
            ]);

            if ($response->successful()) {
                return response()->json([
                    'message' => 'OTP berhasil dikirim ke WhatsApp Anda.',
                    'phone_hint' => substr($user->no_hp, 0, 4) . '****' . substr($user->no_hp, -3),
                ]);
            }

            return response()->json([
                'message' => 'Gagal mengirim OTP. Silakan coba lagi.',
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengirim OTP. Silakan coba lagi.',
            ], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        $reset = PasswordReset::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$reset) {
            return response()->json([
                'message' => 'OTP tidak valid atau sudah kadaluarsa.',
            ], 422);
        }

        return response()->json([
            'message' => 'OTP terverifikasi. Silakan buat password baru.',
            'verified' => true,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $reset = PasswordReset::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$reset) {
            return response()->json([
                'message' => 'OTP tidak valid atau sudah kadaluarsa.',
            ], 422);
        }

        $user = User::where('email', $request->email)->first();
        $user->update(['password' => $request->password]);

        // Clean up OTP
        PasswordReset::where('email', $request->email)->delete();

        return response()->json([
            'message' => 'Password berhasil direset. Silakan login dengan password baru.',
        ]);
    }
}
