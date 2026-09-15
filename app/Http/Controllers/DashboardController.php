<?php

namespace App\Http\Controllers;

use App\Models\Gaji;
use App\Models\Karyawan;

class DashboardController extends Controller
{
    public function index()
    {
        $jumlahKaryawan = Karyawan::count();
        $jumlahSlip = Gaji::count();
        $terbaru = Gaji::with('karyawan')->latest('periode_akhir')->first();
        $totalDikirim = Gaji::whereNotNull('email_sent_at')->orWhereNotNull('whatsapp_sent_at')->count();

        return view('dashboard.index', compact(
            'jumlahKaryawan', 'jumlahSlip', 'terbaru', 'totalDikirim'
        ));
    }
}
