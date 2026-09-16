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
        $totalDikirim   = Gaji::whereNotNull('email_sent_at')
                              ->orWhereNotNull('whatsapp_sent_at')
                              ->count();
        
        $terbaru = Gaji::with('karyawan')->latest()->first();

        // Objek karyawan baru untuk form input di dashboard
        $karyawan = new Karyawan();

        return view('dashboard.index', compact('jumlahKaryawan', 'jumlahSlip', 'totalDikirim', 'terbaru', 'karyawan'));
    }
}