<?php

namespace App\Http\Controllers;

use App\Models\Gaji;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class SlipGajiController extends Controller
{
    public function show(Request $request, Gaji $gaji)
    {
        $verified = $request->session()->get('verified_gaji_id') === $gaji->id;

        if (! $verified && ! $request->session()->has("captcha_gaji_{$gaji->id}")) {
            $this->putNewCaptcha($request, $gaji->id);
        }

        return view('slip-gaji.show', [
            'gaji'     => $gaji->load('karyawan'),
            'verified' => $verified,
            'question' => $verified ? null : $this->captchaQuestion($request, $gaji->id),
        ]);
    }

    public function verifyCaptcha(Request $request, Gaji $gaji)
    {
        $request->validate(['jawaban' => ['required', 'integer']]);

        $key = "captcha_gaji_{$gaji->id}";
        $captcha = $request->session()->get($key);

        $benar = $captcha && (int) $request->input('jawaban') === $captcha['answer'];

        if (! $benar) {
            $this->putNewCaptcha($request, $gaji->id);

            return back()->withErrors(['jawaban' => 'Jawaban captcha salah, coba lagi.']);
        }

        $request->session()->forget($key);
        $request->session()->put('verified_gaji_id', $gaji->id);

        return redirect()->route('gaji.slip', $gaji)->with('status', 'Verifikasi berhasil.');
    }

    public function refreshCaptcha(Request $request, Gaji $gaji)
    {
        $this->putNewCaptcha($request, $gaji->id);

        return response()->json([
            'question' => $this->captchaQuestion($request, $gaji->id),
        ]);
    }

    public function downloadPdf(Request $request, Gaji $gaji)
    {
        $gaji->load('karyawan');
        $karyawan = $gaji->karyawan;

        // Penanganan format tanggal agar tidak error
        $periodeAkhir = $gaji->periode_akhir instanceof Carbon 
            ? $gaji->periode_akhir->format('Y-m') 
            : Carbon::parse($gaji->periode_akhir)->format('Y-m');

        if (! ($gaji->periode_awal instanceof Carbon)) {
            $gaji->periode_awal = Carbon::parse($gaji->periode_awal);
        }
        if (! ($gaji->periode_akhir instanceof Carbon)) {
            $gaji->periode_akhir = Carbon::parse($gaji->periode_akhir);
        }

        $pdf = Pdf::loadView('slip-gaji.pdf', compact('gaji', 'karyawan'));

        return $pdf->download('slip-gaji-' . $karyawan->nik . '-' . $periodeAkhir . '.pdf');
    }

    // Mengirim Email Slip Gaji langsung ke Gmail Karyawan via Resend API
    public function sendEmail(Request $request, Gaji $gaji)
    {
        $gaji->load('karyawan');
        $karyawan = $gaji->karyawan;

        if (! $karyawan || ! $karyawan->email) {
            return back()->withErrors(['email' => 'Email karyawan belum diisi.']);
        }

        try {
            // Generate PDF untuk lampiran email
            $pdf = Pdf::loadView('slip-gaji.pdf', compact('gaji', 'karyawan'));
            $pdfBase64 = base64_encode($pdf->output());

            // Tembak Resend API (Port 443 HTTPS - Langsung ke Inbox Gmail)
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('RESEND_API_KEY'),
                'Content-Type'  => 'application/json',
            ])->post('https://api.resend.com/emails', [
                'from'    => config('mail.from.name', 'Sistem Penggajian') . ' <' . config('mail.from.address', 'onboarding@resend.dev') . '>',
                'to'      => [$karyawan->email],
                'subject' => 'Slip Gaji Karyawan - ' . $karyawan->nama,
                'html'    => "<p>Halo <b>{$karyawan->nama}</b>,</p><p>Berikut terlampir file Slip Gaji Anda.</p>",
                'attachments' => [
                    [
                        'filename' => 'slip-gaji-' . $karyawan->nik . '.pdf',
                        'content'  => $pdfBase64,
                    ]
                ]
            ]);

            if (! $response->successful()) {
                return back()->withErrors(['email' => 'Gagal kirim via Resend API: ' . $response->body()]);
            }

            // Update timestamp email terkirim
            $gaji->update(['email_sent_at' => now()]);

            return back()->with('status', 'Slip gaji berhasil dikirim ke email ' . $karyawan->email);
        } catch (\Throwable $e) {
            return back()->withErrors(['email' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function sendWhatsapp(Request $request, Gaji $gaji)
    {
        $gaji->load('karyawan');
        $karyawan = $gaji->karyawan;

        if (! $karyawan->no_hp) {
            return back()->withErrors(['no_hp' => 'Nomor WhatsApp karyawan belum diisi.']);
        }

        // Format Nomor HP ke format internasional 62
        $noHp = preg_replace('/[^0-9]/', '', $karyawan->no_hp);
        if (str_starts_with($noHp, '0')) {
            $noHp = '62' . substr($noHp, 1);
        }

        $gajiPokok  = $gaji->gaji_pokok ?? 0;
        $lembur     = $gaji->lembur ?? 0;
        $pinjaman   = $gaji->pinjaman_karyawan ?? 0;
        $gajiBersih = $gajiPokok + $lembur - $pinjaman;

        $pesan = "*SLIP GAJI KARYAWAN*\n\n" .
                 "Nama: " . $karyawan->nama . "\n" .
                 "NIK: " . $karyawan->nik . "\n" .
                 "Jabatan: " . $karyawan->jabatan . "\n\n" .
                 "Gaji Pokok: Rp " . number_format($gajiPokok, 0, ',', '.') . "\n" .
                 "Lembur: Rp " . number_format($lembur, 0, ',', '.') . "\n" .
                 "Pinjaman: Rp " . number_format($pinjaman, 0, ',', '.') . "\n" .
                 "*Total Gaji Bersih: Rp " . number_format($gajiBersih, 0, ',', '.') . "*\n\n" .
                 "Pesan ini dikirim otomatis oleh Sistem Penggajian.";

        // Kirim via Fonnte API
        $token = env('FONNTE_TOKEN');

        if ($token) {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target'  => $noHp,
                'message' => $pesan,
            ]);

            if (! $response->successful()) {
                return back()->withErrors(['whatsapp' => 'Gagal mengirim WA via Fonnte API: ' . $response->body()]);
            }
        } else {
            return redirect()->away("https://wa.me/" . $noHp . "?text=" . urlencode($pesan));
        }

        $gaji->update(['whatsapp_sent_at' => now()]);

        return back()->with('status', 'Slip gaji berhasil dikirim via WhatsApp ke ' . $karyawan->nama);
    }

    private function putNewCaptcha(Request $request, int $gajiId): void
    {
        $a = random_int(1, 20);
        $b = random_int(1, 20);
        $op = random_int(0, 1) === 0 ? '+' : '-';

        if ($op === '-' && $b > $a) {
            [$a, $b] = [$b, $a];
        }

        $answer = $op === '+' ? $a + $b : $a - $b;

        $request->session()->put("captcha_gaji_{$gajiId}", [
            'a' => $a, 'b' => $b, 'op' => $op, 'answer' => $answer,
        ]);
    }

    private function captchaQuestion(Request $request, int $gajiId): string
    {
        $c = $request->session()->get("captcha_gaji_{$gajiId}");

        return "{$c['a']} {$c['op']} {$c['b']} = ?";
    }
}