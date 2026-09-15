@extends('layouts.app')

@section('title', 'Data Gaji')

@section('content')
    <div class="page-header-row">
        <p class="page-intro">Tabel input data gaji per periode. Data ini yang jadi dasar perhitungan slip gaji.</p>
        <a href="{{ route('gaji.create') }}" class="btn small">+ Input Gaji</a>
    </div>

    <div class="card">
        @if ($gajis->isEmpty())
            <p>Belum ada data gaji. Klik "Input Gaji" untuk menambahkan.</p>
        @else
            <table class="data-table">
                <tr>
                    <th>Karyawan</th>
                    <th>Periode</th>
                    <th>Gaji Pokok</th>
                    <th>Lembur</th>
                    <th>Pinjaman</th>
                    <th>Gaji Bersih</th>
                    <th>Status Kirim</th>
                    <th>Aksi</th>
                </tr>
                @foreach ($gajis as $gaji)
                    <tr>
                        <td>{{ $gaji->karyawan->nama }}</td>
                        <td>{{ $gaji->periode_awal->translatedFormat('d M Y') }} – {{ $gaji->periode_akhir->translatedFormat('d M Y') }}</td>
                        <td>Rp {{ number_format($gaji->gaji_pokok, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($gaji->lembur, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($gaji->pinjaman_karyawan, 0, ',', '.') }}</td>
                        <td><strong>Rp {{ number_format($gaji->gaji_bersih, 0, ',', '.') }}</strong></td>
                        <td>
                            @if ($gaji->email_sent_at)<span class="badge badge-ok">Email</span>@endif
                            @if ($gaji->whatsapp_sent_at)<span class="badge badge-ok">WA</span>@endif
                            @if (! $gaji->email_sent_at && ! $gaji->whatsapp_sent_at)<span class="badge">Belum</span>@endif
                        </td>
                        <td class="table-actions">
                            <a href="{{ route('gaji.slip', $gaji) }}">Slip</a>
                            <a href="{{ route('gaji.edit', $gaji) }}">Edit</a>
                            <form method="POST" action="{{ route('gaji.destroy', $gaji) }}" onsubmit="return confirm('Hapus data gaji periode ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="link link-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </table>
        @endif
    </div>
@endsection
