<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    // Tahap 1: Send 6-character token via Resend API
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.exists' => 'Email tidak terdaftar di sistem kami.',
        ]);

        // Generate token 6 karakter acak
        $token = Str::random(6);

        // Simpan/update token ke tabel password_resets
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'token'      => $token,
                'created_at' => Carbon::now(),
            ]
        );

        // Kirim email via HTTP API (Port 443 HTTPS)
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('RESEND_API_KEY'),
            'Content-Type'  => 'application/json',
        ])->post('https://api.resend.com/emails', [
            'from'    => config('mail.from.name') . ' <' . config('mail.from.address') . '>',
            'to'      => [$request->email],
            'subject' => 'Kode Reset Password Akun',
            'html'    => "<p>Kode token reset password Anda adalah: <b>{$token}</b></p>",
        ]);

        if ($response->successful()) {
            return response()->json([
                'status'  => true,
                'message' => 'Kode token reset password berhasil dikirim ke Gmail Anda!',
            ], 200);
        }

        return response()->json([
            'status'  => false,
            'message' => 'Gagal mengirim email: ' . $response->body(),
        ], 500);
    }

    // Tahap 2: Validate token & update password baru
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email', 'exists:users,email'],
            'token'    => ['required', 'string'],
            'password' => ['required', 'min:6', 'confirmed'],
        ], [
            'email.exists'       => 'Email tidak terdaftar.',
            'token.required'     => 'Kode token wajib diisi.',
            'password.required'  => 'Password baru wajib diisi.',
            'password.min'       => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Cek kecocokan token di database
        $resetRecord = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$resetRecord) {
            return response()->json([
                'status'  => false,
                'message' => 'Kode token salah atau tidak valid!',
            ], 422);
        }

        // Update password user
        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password),
        ]);

        // Hapus token yang sudah terpakai
        DB::table('password_resets')->where('email', $request->email)->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Password berhasil diperbarui! Silakan login kembali.',
        ], 200);
    }
}