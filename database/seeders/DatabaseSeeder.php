<?php

namespace Database\Seeders;

use App\Models\Gaji;
use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun admin (satu-satunya yang bisa login)
        User::create([
            'name'     => 'Admin',
            'email'    => 'admin@perusahaan.com',
            'password' => Hash::make('admin123'),
        ]);

        // Data karyawan contoh
        $ayu = Karyawan::create([
            'nama'    => 'Ayu Prastiwi',
            'nik'     => '3273040112',
            'jabatan' => 'Staff Administrasi',
            'email'   => 'ayu@perusahaan.com',
            'no_hp'   => '6281234567890', // ganti dengan nomor yang terdaftar di WhatsApp Cloud API untuk tes nyata
        ]);

        $budi = Karyawan::create([
            'nama'    => 'Budi Santoso',
            'nik'     => '3273040113',
            'jabatan' => 'Staff Gudang',
            'email'   => 'budi@perusahaan.com',
            'no_hp'   => '6281234567891',
        ]);

        Gaji::create([
            'karyawan_id'       => $ayu->id,
            'periode_awal'      => '2025-11-25',
            'periode_akhir'     => '2025-12-25',
            'gaji_pokok'        => 4500000,
            'lembur'            => 350000,
            'pinjaman_karyawan' => 250000,
        ]);

        Gaji::create([
            'karyawan_id'       => $budi->id,
            'periode_awal'      => '2025-11-25',
            'periode_akhir'     => '2025-12-25',
            'gaji_pokok'        => 3800000,
            'lembur'            => 150000,
            'pinjaman_karyawan' => 0,
        ]);
    }
}
