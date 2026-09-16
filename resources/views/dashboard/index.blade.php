@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div style="width: 100%; max-width: 1000px; margin: 0 auto;">
        
        <!-- HEADER DASHBOARD -->
        <div style="margin-bottom: 24px; border-bottom: 2px solid #c5d99b; padding-bottom: 12px;">
            <h2 style="margin: 0; color: #2e4600; font-size: 22px; font-weight: bold; text-transform: uppercase;">Dashboard Overview</h2>
            <p style="margin: 4px 0 0 0; font-size: 14px; color: #666;">Ringkasan statistik data karyawan dan status pengiriman slip gaji.</p>
        </div>

        @if (session('status'))
            <div style="background-color: #c5d99b; color: #2e4600; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; font-weight: 600; border: 1px solid #b3cb86;">
                {{ session('status') }}
            </div>
        @endif

        <!-- CARDS RINGKASAN STATISTIK (3 KOLOM) -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; margin-bottom: 30px;">
            
            <!-- TOTAL KARYAWAN -->
            <div style="background: #ffffff; padding: 20px; border-radius: 8px; border: 1px solid #dcdcdc; box-shadow: 0 4px 12px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 16px;">
                <div style="width: 50px; height: 50px; background-color: #eaf2e8; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                    👥
                </div>
                <div>
                    <span style="display: block; font-size: 13px; color: #666; font-weight: 600; text-transform: uppercase;">Total Karyawan</span>
                    <strong style="font-size: 24px; color: #2e4600;">{{ $jumlahKaryawan ?? 0 }}</strong>
                </div>
            </div>

            <!-- TOTAL SLIP GAJI -->
            <div style="background: #ffffff; padding: 20px; border-radius: 8px; border: 1px solid #dcdcdc; box-shadow: 0 4px 12px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 16px;">
                <div style="width: 50px; height: 50px; background-color: #eaf2e8; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                    💰
                </div>
                <div>
                    <span style="display: block; font-size: 13px; color: #666; font-weight: 600; text-transform: uppercase;">Jumlah Slip Gaji</span>
                    <strong style="font-size: 24px; color: #61885c;">{{ $jumlahSlip ?? 0 }}</strong>
                </div>
            </div>

            <!-- SLIP TERKIRIM -->
            <div style="background: #ffffff; padding: 20px; border-radius: 8px; border: 1px solid #dcdcdc; box-shadow: 0 4px 12px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 16px;">
                <div style="width: 50px; height: 50px; background-color: #eaf2e8; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                    ✉️
                </div>
                <div>
                    <span style="display: block; font-size: 13px; color: #666; font-weight: 600; text-transform: uppercase;">Slip Terkirim</span>
                    <strong style="font-size: 24px; color: #198754;">{{ $totalDikirim ?? 0 }} <span style="font-size: 14px; font-weight: normal; color: #777;">/ {{ $jumlahSlip ?? 0 }}</span></strong>
                </div>
            </div>

        </div>

        <!-- QUICK ACTIONS & DETAIL SLIP GAJI TERAKHIR -->
        <div style="display: flex; gap: 20px; flex-wrap: wrap;">
            
            <!-- TOMBOL AKSES CEPAT -->
            <div style="flex: 1; min-width: 280px; background: #ffffff; padding: 24px; border-radius: 8px; border: 1px solid #dcdcdc; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                <h3 style="margin-top: 0; font-size: 16px; color: #2e4600; font-weight: bold; margin-bottom: 16px;">⚡ Akses Cepat</h3>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="{{ route('karyawan.index') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; background-color: #61885c; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 14px; transition: background 0.2s;">
                        <span>👥 Data Karyawan</span>
                        <span>&rarr;</span>
                    </a>
                    <a href="{{ route('gaji.index') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; background-color: #f9fbf8; color: #2e4600; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 14px; border: 1px solid #c5d99b;">
                        <span>💰 Data Gaji</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- SLIP GAJI TERAKHIR -->
            <div style="flex: 1.5; min-width: 320px; background: #ffffff; padding: 24px; border-radius: 8px; border: 1px solid #dcdcdc; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                <h3 style="margin-top: 0; font-size: 16px; color: #2e4600; font-weight: bold; margin-bottom: 16px;">📄 Slip Gaji Terbaru</h3>
                @if (isset($terbaru) && $terbaru)
                    <ul style="list-style: none; padding: 0; margin: 0 0 16px 0; font-size: 14px;">
                        <li style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee;">
                            <span style="color: #666;">Nama Karyawan</span>
                            <strong>{{ $terbaru->karyawan->nama ?? '-' }}</strong>
                        </li>
                        <li style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee;">
                            <span style="color: #666;">Periode Gaji</span>
                            <span>
                                {{ $terbaru->periode_awal ? \Carbon\Carbon::parse($terbaru->periode_awal)->translatedFormat('d M Y') : '-' }} - 
                                {{ $terbaru->periode_akhir ? \Carbon\Carbon::parse($terbaru->periode_akhir)->translatedFormat('d M Y') : '-' }}
                            </span>
                        </li>
                        <li style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee;">
                            <span style="color: #666;">Gaji Pokok</span>
                            <span>Rp {{ number_format($terbaru->gaji_pokok ?? 0, 0, ',', '.') }}</span>
                        </li>
                        <li style="display: flex; justify-content: space-between; padding: 8px 0; font-weight: bold; color: #2e4600; font-size: 15px;">
                            <span>Gaji Bersih</span>
                            <span>Rp {{ number_format($terbaru->gaji_bersih ?? 0, 0, ',', '.') }}</span>
                        </li>
                    </ul>
                    <a href="{{ route('gaji.slip', $terbaru) }}" style="display: inline-block; color: #61885c; text-decoration: none; font-weight: 600; font-size: 13px;">Lihat Rincian Slip Ini &rarr;</a>
                @else
                    <p style="color: #888; margin: 0; font-size: 14px;">Belum ada data transaksi slip gaji.</p>
                @endif
            </div>

        </div>

    </div>
@endsection