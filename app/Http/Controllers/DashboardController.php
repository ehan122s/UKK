<?php

namespace App\Http\Controllers;

use App\Models\Gaji;
use App\Models\Karyawan;

class DashboardController extends Controller
{
    public function index()
    {
        $jumlahKaryawan = Karyawan::count();
        $jumlahSlip     = Gaji::count();
        
        // Menggunakan latest() tanpa argumen (otomatis mengurutkan berdasarkan created_at desc)
        $terbaru        = Gaji::with('karyawan')->latest()->first();
        
        $totalDikirim   = Gaji::whereNotNull('email_sent_at')
                              ->orWhereNotNull('whatsapp_sent_at')
                              ->count();

        return view('dashboard.index', compact(
            'jumlahKaryawan', 'jumlahSlip', 'terbaru', 'totalDikirim'
        ));
    }
}