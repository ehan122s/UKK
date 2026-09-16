<?php

namespace App\Http\Controllers;

use App\Models\Gaji;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\SlipGajiMail;

class GajiController extends Controller
{
    public function index(Request $request)
    {
        $query = Gaji::with('karyawan');

        // Pencarian berdasarkan nama karyawan atau NIK
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('karyawan', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $gajis = $query->latest()->get();

        // Mengirimkan $karyawans agar tidak error "Undefined variable $karyawans" di view
        $karyawans = Karyawan::orderBy('nama')->get();

        return view('gaji.index', compact('gajis', 'karyawans'));
    }

    public function create(Request $request)
{
    $karyawans = Karyawan::orderBy('nama')->get();
    
    // Buat objek gaji baru dan isi default value dari URL Query string jika ada
    $gaji = new Gaji();
    $gaji->karyawan_id  = $request->query('karyawan_id');
    $gaji->periode_awal  = $request->query('periode_awal');
    $gaji->periode_akhir = $request->query('periode_akhir');

    return view('gaji.create', compact('karyawans', 'gaji'));
}

    public function store(Request $request)
    {
        $data = $this->validated($request);

        // Kalkulasi otomatis Gaji Bersih = (Gaji Pokok + Lembur) - Pinjaman
        $data['gaji_bersih'] = ($data['gaji_pokok'] + $data['lembur']) - $data['pinjaman_karyawan'];

        Gaji::create($data);

        return redirect()->route('gaji.index')->with('status', 'Data gaji berhasil ditambahkan.');
    }

    public function edit(Gaji $gaji)
    {
        $karyawans = Karyawan::orderBy('nama')->get();

        return view('gaji.edit', compact('gaji', 'karyawans'));
    }

    public function update(Request $request, Gaji $gaji)
    {
        $data = $this->validated($request);

        // Kalkulasi otomatis Gaji Bersih saat update
        $data['gaji_bersih'] = ($data['gaji_pokok'] + $data['lembur']) - $data['pinjaman_karyawan'];

        $gaji->update($data);

        return redirect()->route('gaji.index')->with('status', 'Data gaji berhasil diperbarui.');
    }

    public function destroy(Gaji $gaji)
    {
        $gaji->delete();

        return redirect()->route('gaji.index')->with('status', 'Data gaji berhasil dihapus.');
    }

    // --- FITUR KELUARAN (PDF, EMAIL, WHATSAPP) ---

    public function pdf(Gaji $gaji)
    {
        $gaji->load('karyawan');
        
        // Menggunakan DomPDF atau render view khusus slip
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('gaji.pdf', compact('gaji'));
            return $pdf->download('Slip_Gaji_' . ($gaji->karyawan->nama ?? 'Karyawan') . '.pdf');
        }

        return view('gaji.slip', compact('gaji'));
    }

    public function sendEmail(Request $request, Gaji $gaji)
    {
        $request->validate(['target_email' => 'required|email']);
        $emailTujuan = $request->input('target_email');

        try {
            if (class_exists(SlipGajiMail::class)) {
                Mail::to($emailTujuan)->send(new SlipGajiMail($gaji));
            }
            
            $gaji->update(['email_sent_at' => now()]);
            return back()->with('status', 'Slip gaji berhasil dikirim ke email ' . $emailTujuan);
        } catch (\Exception $e) {
            return back()->withErrors(['msg' => 'Gagal mengirim email: ' . $e->getMessage()]);
        }
    }

    public function sendWa(Request $request, Gaji $gaji)
    {
        $request->validate(['target_wa' => 'required|string']);
        $target = $request->input('target_wa');
        $karyawan = $gaji->karyawan;

        $pesan = "Halo " . ($karyawan->nama ?? 'Karyawan') . ",\n\n";
        $pesan .= "Berikut adalah Rincian Slip Gaji Anda:\n";
        $pesan .= "Periode: {$gaji->periode_awal} s/d {$gaji->periode_akhir}\n";
        $pesan .= "Gaji Pokok: Rp " . number_format($gaji->gaji_pokok, 0, ',', '.') . "\n";
        $pesan .= "Lembur: Rp " . number_format($gaji->lembur, 0, ',', '.') . "\n";
        $pesan .= "Pinjaman: Rp " . number_format($gaji->pinjaman_karyawan, 0, ',', '.') . "\n";
        $pesan .= "-----------------------------------\n";
        $pesan .= "GAJI BERSIH: Rp " . number_format($gaji->gaji_bersih ?? (($gaji->gaji_pokok + $gaji->lembur) - $gaji->pinjaman_karyawan), 0, ',', '.') . "\n\n";
        $pesan .= "Terima kasih.";

        try {
            $response = Http::withHeaders([
                'Authorization' => env('FONNTE_TOKEN'),
            ])->post('https://api.fonnte.com/send', [
                'target'  => $target,
                'message' => $pesan,
            ]);

            if ($response->successful()) {
                $gaji->update(['whatsapp_sent_at' => now()]);
                return back()->with('status', 'Slip gaji berhasil dikirim via WhatsApp ke ' . $target);
            }

            return back()->withErrors(['msg' => 'Gagal mengirim pesan WhatsApp via Fonnte API.']);
        } catch (\Exception $e) {
            return back()->withErrors(['msg' => 'Terjadi kesalahan sistem API WA: ' . $e->getMessage()]);
        }
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'karyawan_id'       => ['required', 'exists:karyawans,id'],
            'periode_awal'      => ['required', 'date'],
            'periode_akhir'     => ['required', 'date', 'after_or_equal:periode_awal'],
            'gaji_pokok'        => ['required', 'numeric', 'min:0'],
            'lembur'            => ['required', 'numeric', 'min:0'],
            'pinjaman_karyawan' => ['required', 'numeric', 'min:0'],
        ]);
    }
}