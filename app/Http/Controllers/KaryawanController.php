<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        // Load relasi gajis dan tambah fitur pencarian
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
        return view('karyawan.create');
    }

   public function store(Request $request)
{
    $data = $this->validated($request);

    $karyawan = Karyawan::create($data);

    $karyawan->gajis()->create([
        'gaji_pokok'        => $request->input('gaji_pokok', 0),
        'lembur'            => $request->input('lembur', 0),
        'pinjaman_karyawan' => $request->input('pinjaman', 0), // Disesuaikan
        'periode_awal'      => now()->startOfMonth()->toDateString(),
        'periode_akhir'     => now()->endOfMonth()->toDateString(),
    ]);

    return redirect()->route('karyawan.index')->with('status', 'Data karyawan dan gaji berhasil ditambahkan.');
}

    public function edit(Karyawan $karyawan)
    {
        $karyawan->load('gajis');
        return view('karyawan.edit', compact('karyawan'));
    }

    public function update(Request $request, Karyawan $karyawan)
{
    $data = $this->validated($request, $karyawan->id);

    $karyawan->update($data);

    $gajiData = [
        'gaji_pokok'        => $request->input('gaji_pokok', 0),
        'lembur'            => $request->input('lembur', 0),
        'pinjaman_karyawan' => $request->input('pinjaman', 0), // Disesuaikan
        'periode_awal'      => now()->startOfMonth()->toDateString(),
        'periode_akhir'     => now()->endOfMonth()->toDateString(),
    ];

    if ($karyawan->gajis()->exists()) {
        $karyawan->gajis()->update($gajiData);
    } else {
        $karyawan->gajis()->create($gajiData);
    }

    return redirect()->route('karyawan.index')->with('status', 'Data karyawan dan gaji berhasil diperbarui.');
}

    public function destroy(Karyawan $karyawan)
    {
        $karyawan->delete(); // otomatis menghapus data gaji terkait (cascade)

        return redirect()->route('karyawan.index')->with('status', 'Data karyawan berhasil dihapus.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'nama'       => ['required', 'string', 'max:255'],
            'nik'        => ['required', 'string', 'max:50', 'unique:karyawans,nik' . ($ignoreId ? ",{$ignoreId}" : '')],
            'jabatan'    => ['required', 'string', 'max:255'],
            'email'      => ['nullable', 'email'],
            'no_hp'      => ['nullable', 'string', 'max:20'],
            'gaji_pokok' => ['nullable', 'numeric'],
            'lembur'     => ['nullable', 'numeric'],
            'pinjaman'   => ['nullable', 'numeric'],
        ]);
    }
}