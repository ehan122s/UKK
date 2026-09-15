@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <p class="page-intro">Ringkasan data karyawan dan gaji.</p>

    <div class="card" style="margin-bottom:18px;">
        <ul class="dash-summary">
            <li><span>Jumlah Karyawan</span><span>{{ $jumlahKaryawan }}</span></li>
            <li><span>Jumlah Slip Gaji</span><span>{{ $jumlahSlip }}</span></li>
            <li><span>Slip Sudah Dikirim</span><span>{{ $totalDikirim }} / {{ $jumlahSlip }}</span></li>
        </ul>

        <div class="quick-actions">
            <a href="{{ route('karyawan.create') }}" class="btn small">+ Tambah Karyawan</a>
            <a href="{{ route('gaji.create') }}" class="btn small btn-outline">+ Input Gaji</a>
            <a href="{{ route('gaji.index') }}" class="btn small btn-outline">Semua Data Gaji</a>
        </div>
    </div>

    @if ($terbaru)
        <div class="card">
            <h2 style="font-size:13px; text-transform:uppercase; letter-spacing:.02em; margin-bottom:12px;">Slip Gaji Terbaru</h2>
            <ul class="info-list">
                <li><span>Karyawan</span><span>{{ $terbaru->karyawan->nama }}</span></li>
                <li><span>Periode</span><span>{{ $terbaru->periode_awal->translatedFormat('d M Y') }} – {{ $terbaru->periode_akhir->translatedFormat('d M Y') }}</span></li>
                <li><span>Total Penghasilan</span><span>Rp {{ number_format($terbaru->total_penghasilan, 0, ',', '.') }}</span></li>
                <li><span>Total Potongan</span><span>Rp {{ number_format($terbaru->total_potongan, 0, ',', '.') }}</span></li>
                <li><span>Gaji Bersih</span><span><strong>Rp {{ number_format($terbaru->gaji_bersih, 0, ',', '.') }}</strong></span></li>
            </ul>
            <a href="{{ route('gaji.slip', $terbaru) }}" class="link">Lihat slip ini →</a>
        </div>
    @endif
@endsection
