<?php

namespace App\Http\Controllers;

use App\Models\Gaji;
use App\Models\Karyawan;
use Illuminate\Http\Request;

class GajiController extends Controller
{
    public function index()
    {
        // Diurutkan berdasarkan data terbaru (created_at) agar aman dari error kolom
        $gajis = Gaji::with('karyawan')->latest()->get();

        return view('gaji.index', compact('gajis'));
    }

    public function create()
    {
        $karyawans = Karyawan::orderBy('nama')->get();
        $gaji = new Gaji(); // Kirim objek kosong agar _form.blade.php tidak error null

        return view('gaji.create', compact('karyawans', 'gaji'));
    }

    public function store(Request $request)
    {
        Gaji::create($this->validated($request));

        return redirect()->route('gaji.index')->with('status', 'Data gaji berhasil ditambahkan.');
    }

    public function edit(Gaji $gaji)
    {
        $karyawans = Karyawan::orderBy('nama')->get();

        return view('gaji.edit', compact('gaji', 'karyawans'));
    }

    public function update(Request $request, Gaji $gaji)
    {
        $gaji->update($this->validated($request));

        return redirect()->route('gaji.index')->with('status', 'Data gaji berhasil diperbarui.');
    }

    public function destroy(Gaji $gaji)
    {
        $gaji->delete();

        return redirect()->route('gaji.index')->with('status', 'Data gaji berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'karyawan_id'       => ['required', 'exists:karyawans,id'],
            'periode_awal'      => ['required', 'date'],
            'periode_akhir'     => ['required', 'date', 'after_or_equal:periode_awal'],
            'gaji_pokok'        => ['required', 'integer', 'min:0'],
            'lembur'            => ['required', 'integer', 'min:0'],
            'pinjaman_karyawan' => ['required', 'integer', 'min:0'],
        ]);
    }
}