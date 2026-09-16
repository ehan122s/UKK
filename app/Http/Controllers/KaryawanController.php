<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $query = Karyawan::with('gajis');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $karyawans = $query->orderBy('nama')->get();

        return view('karyawan.index', compact('karyawans'));
    }

    public function create()
    {
        return view('karyawan.create', ['karyawan' => new Karyawan()]);
    }

    public function store(Request $request)
    {
        if ((int) $request->input('captcha') !== (int) $request->input('captcha_expected')) {
            return back()->withInput()->withErrors(['captcha' => 'Jawaban Captcha salah! Silakan coba lagi.']);
        }

        $validated = $request->validate([
            'nik'               => ['required', 'string', 'max:50', 'unique:karyawans,nik'],
            'nama'              => ['required', 'string', 'max:255'],
            'jabatan'           => ['required', 'string', 'max:255'],
            'gaji_pokok'        => ['required', 'numeric', 'min:0'],
            'lembur'            => ['required', 'numeric', 'min:0'],
            'pinjaman_karyawan' => ['nullable', 'numeric', 'min:0'],
            'periode_awal'      => ['nullable', 'date'],
            'periode_akhir'     => ['nullable', 'date'],
        ]);

        $pinjamanVal  = $request->input('pinjaman_karyawan', 0);
        $periodeAwal  = $request->input('periode_awal') ?: now()->startOfMonth()->toDateString();
        $periodeAkhir = $request->input('periode_akhir') ?: now()->endOfMonth()->toDateString();

        $karyawan = Karyawan::create([
            'nik'               => $validated['nik'],
            'nama'              => $validated['nama'],
            'jabatan'           => $validated['jabatan'],
            'gaji_pokok'        => $validated['gaji_pokok'],
            'lembur'            => $validated['lembur'],
            'pinjaman'          => $pinjamanVal,
            'pinjaman_karyawan' => $pinjamanVal,
        ]);

        $gajiBersih = $validated['gaji_pokok'] + $validated['lembur'] - $pinjamanVal;
        
        $karyawan->gajis()->create([
            'gaji_pokok'        => $validated['gaji_pokok'],
            'lembur'            => $validated['lembur'],
            'pinjaman_karyawan' => $pinjamanVal,
            'gaji_bersih'       => $gajiBersih,
            'periode_awal'      => $periodeAwal,
            'periode_akhir'     => $periodeAkhir,
        ]);

        return redirect()->route('karyawan.index')->with('status', 'Data karyawan dan rincian gaji berhasil ditambahkan.');
    }

    public function edit(Karyawan $karyawan)
    {
        $karyawan->load('gajis');
        return view('karyawan.edit', compact('karyawan'));
    }

    public function update(Request $request, Karyawan $karyawan)
    {
        if ((int) $request->input('captcha') !== (int) $request->input('captcha_expected')) {
            return back()->withInput()->withErrors(['captcha' => 'Jawaban Captcha salah! Silakan coba lagi.']);
        }

        $validated = $request->validate([
            'nik'               => ['required', 'string', 'max:50', 'unique:karyawans,nik,' . $karyawan->id],
            'nama'              => ['required', 'string', 'max:255'],
            'jabatan'           => ['required', 'string', 'max:255'],
            'gaji_pokok'        => ['required', 'numeric', 'min:0'],
            'lembur'            => ['required', 'numeric', 'min:0'],
            'pinjaman_karyawan' => ['nullable', 'numeric', 'min:0'],
            'periode_awal'      => ['nullable', 'date'],
            'periode_akhir'     => ['nullable', 'date'],
        ]);

        $pinjamanVal  = $request->input('pinjaman_karyawan', 0);
        $periodeAwal  = $request->input('periode_awal') ?: now()->startOfMonth()->toDateString();
        $periodeAkhir = $request->input('periode_akhir') ?: now()->endOfMonth()->toDateString();

        $karyawan->update([
            'nik'               => $validated['nik'],
            'nama'              => $validated['nama'],
            'jabatan'           => $validated['jabatan'],
            'gaji_pokok'        => $validated['gaji_pokok'],
            'lembur'            => $validated['lembur'],
            'pinjaman'          => $pinjamanVal,
            'pinjaman_karyawan' => $pinjamanVal,
        ]);

        $gajiBersih = $validated['gaji_pokok'] + $validated['lembur'] - $pinjamanVal;

        if ($karyawan->gajis()->exists()) {
            $gajiTerbaru = $karyawan->gajis->last();
            $gajiTerbaru->update([
                'gaji_pokok'        => $validated['gaji_pokok'],
                'lembur'            => $validated['lembur'],
                'pinjaman_karyawan' => $pinjamanVal,
                'gaji_bersih'       => $gajiBersih,
                'periode_awal'      => $periodeAwal,
                'periode_akhir'     => $periodeAkhir,
            ]);
        } else {
            $karyawan->gajis()->create([
                'gaji_pokok'        => $validated['gaji_pokok'],
                'lembur'            => $validated['lembur'],
                'pinjaman_karyawan' => $pinjamanVal,
                'gaji_bersih'       => $gajiBersih,
                'periode_awal'      => $periodeAwal,
                'periode_akhir'     => $periodeAkhir,
            ]);
        }

        return redirect()->route('karyawan.index')->with('status', 'Data karyawan dan periode gaji berhasil diperbarui.');
    }

    public function destroy(Karyawan $karyawan)
    {
        $karyawan->delete();

        return redirect()->route('karyawan.index')->with('status', 'Data karyawan berhasil dihapus.');
    }
}