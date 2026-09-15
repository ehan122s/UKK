<?php

namespace App\Services;

use App\Models\Gaji;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class WhatsAppService
{
    private string $token;
    private string $phoneNumberId;
    private string $apiVersion;

    public function __construct()
    {
        $this->token = (string) config('services.whatsapp.token');
        $this->phoneNumberId = (string) config('services.whatsapp.phone_number_id');
        $this->apiVersion = (string) config('services.whatsapp.api_version', 'v20.0');
    }

    /**
     * Kirim ringkasan slip gaji sebagai pesan teks WhatsApp
     * lewat WhatsApp Cloud API resmi Meta.
     *
     * @param string $to nomor tujuan format internasional tanpa "+", contoh 6281234567890
     */
    public function sendSlipGaji(string $to, Gaji $gaji): array
    {
        if (! $this->token || ! $this->phoneNumberId) {
            throw new RuntimeException(
                'WHATSAPP_TOKEN / WHATSAPP_PHONE_NUMBER_ID belum diisi di file .env.'
            );
        }

        $pesan = $this->formatPesan($gaji);

        $response = Http::withToken($this->token)
            ->baseUrl("https://graph.facebook.com/{$this->apiVersion}")
            ->post("/{$this->phoneNumberId}/messages", [
                'messaging_product' => 'whatsapp',
                'to'                => $to,
                'type'              => 'text',
                'text'              => [
                    'preview_url' => false,
                    'body'        => $pesan,
                ],
            ]);

        if ($response->failed()) {
            throw new RuntimeException(
                'Gagal mengirim WhatsApp: ' . $response->body()
            );
        }

        return $response->json();
    }

    private function formatPesan(Gaji $gaji): string
    {
        $user = $gaji->karyawan;

        return sprintf(
            "*Slip Gaji Karyawan*\n".
            "Periode %s - %s\n\n".
            "Nama: %s\n".
            "NIK: %s\n".
            "Jabatan: %s\n\n".
            "*Penghasilan*\n".
            "Gaji Pokok: Rp %s\n".
            "Lembur: Rp %s\n".
            "Total Penghasilan: Rp %s\n\n".
            "*Potongan*\n".
            "Pinjaman Karyawan: Rp %s\n\n".
            "*Gaji Bersih: Rp %s*",
            $gaji->periode_awal->translatedFormat('d M Y'),
            $gaji->periode_akhir->translatedFormat('d M Y'),
            $user->nama,
            $user->nik,
            $user->jabatan,
            number_format($gaji->gaji_pokok, 0, ',', '.'),
            number_format($gaji->lembur, 0, ',', '.'),
            number_format($gaji->total_penghasilan, 0, ',', '.'),
            number_format($gaji->pinjaman_karyawan, 0, ',', '.'),
            number_format($gaji->gaji_bersih, 0, ',', '.'),
        );
    }
}
