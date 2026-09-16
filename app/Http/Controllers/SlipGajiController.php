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

        $periodeAkhir = $gaji->periode_akhir instanceof Carbon 
            ? $gaji->periode_akhir->format('Y-m') 
            : Carbon::parse($gaji->periode_akhir)->format('Y-m');

        $pdf = Pdf::loadView('slip-gaji.pdf', compact('gaji', 'karyawan'));

        return $pdf->download('slip-gaji-' . optional($karyawan)->nik . '-' . $periodeAkhir . '.pdf');
    }

    public function sendEmail(Request $request, Gaji $gaji)
    {
        $request->validate([
            'target_email' => ['required', 'email'],
        ], [
            'target_email.required' => 'Masukkan email tujuan pengiriman terlebih dahulu.',
            'target_email.email'    => 'Format email tujuan tidak valid.',
        ]);

        $gaji->load('karyawan');
        $karyawan = $gaji->karyawan;
        $emailTujuan = $request->target_email;

        try {
            $pdf = Pdf::loadView('slip-gaji.pdf', compact('gaji', 'karyawan'));
            $pdfBase64 = base64_encode($pdf->output());

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('RESEND_API_KEY'),
                'Content-Type'  => 'application/json',
            ])->post('https://api.resend.com/emails', [
                'from'    => config('mail.from.name', 'Sistem Penggajian') . ' <' . config('mail.from.address', 'onboarding@resend.dev') . '>',
                'to'      => [$emailTujuan],
                'subject' => 'Slip Gaji Karyawan - ' . optional($karyawan)->nama,
                'html'    => "<p>Halo,</p><p>Berikut terlampir file Slip Gaji untuk <b>" . optional($karyawan)->nama . "</b>.</p>",
                'attachments' => [
                    [
                        'filename' => 'slip-gaji-' . optional($karyawan)->nik . '.pdf',
                        'content'  => $pdfBase64,
                    ]
                ]
            ]);

            if (! $response->successful()) {
                return back()->withErrors(['email' => 'Gagal kirim via Resend API: ' . $response->body()]);
            }

            $gaji->update(['email_sent_at' => now()]);

            return back()->with('status', 'Slip gaji berhasil dikirim ke ' . $emailTujuan);
        } catch (\Throwable $e) {
            return back()->withErrors(['email' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function sendWhatsapp(Request $request, Gaji $gaji)
    {
        $request->validate([
            'target_wa' => ['required', 'string'],
        ], [
            'target_wa.required' => 'Masukkan nomor WhatsApp tujuan terlebih dahulu.',
        ]);

        $gaji->load('karyawan');
        $karyawan = $gaji->karyawan;

        $noHp = preg_replace('/[^0-9]/', '', $request->target_wa);
        if (str_starts_with($noHp, '0')) {
            $noHp = '62' . substr($noHp, 1);
        }

        $gajiPokok  = $gaji->gaji_pokok ?? $karyawan->gaji_pokok ?? 0;
        $lembur     = $gaji->lembur ?? $karyawan->lembur ?? 0;
        $pinjaman   = $gaji->pinjaman_karyawan ?? $karyawan->pinjaman ?? 0;
        $gajiBersih = $gajiPokok + $lembur - $pinjaman;

        $pesan = "*SLIP GAJI KARYAWAN*\n\n" .
                 "Nama: " . optional($karyawan)->nama . "\n" .
                 "NIK: " . optional($karyawan)->nik . "\n" .
                 "Jabatan: " . optional($karyawan)->jabatan . "\n\n" .
                 "Gaji Pokok: Rp " . number_format($gajiPokok, 0, ',', '.') . "\n" .
                 "Lembur: Rp " . number_format($lembur, 0, ',', '.') . "\n" .
                 "Pinjaman: Rp " . number_format($pinjaman, 0, ',', '.') . "\n" .
                 "*Total Gaji Bersih: Rp " . number_format($gajiBersih, 0, ',', '.') . "*\n\n" .
                 "Pesan ini dikirim otomatis oleh Sistem Penggajian.";

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

        return back()->with('status', 'Slip gaji berhasil dikirim via WhatsApp ke ' . $noHp);
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