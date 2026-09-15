@extends('layouts.app')

@section('content')
<div style="max-width: 1200px; margin: 30px auto; padding: 0 15px; font-family: sans-serif;">
    <div style="margin-bottom: 25px;">
        <h2 style="margin: 0 0 5px 0; font-size: 24px; color: #333;">Data Karyawan</h2>
        <p style="margin: 0; color: #666; font-size: 14px;">Kelola data karyawan dan slip gaji</p>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <form action="{{ route('karyawan.index') }}" method="GET" style="display: flex; gap: 8px;">
            <input type="text" name="search" placeholder="Cari nama atau NIK..." value="{{ request('search') }}" style="padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; width: 250px;">
            <button type="submit" style="padding: 8px 16px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">Cari</button>
        </form>

        <a href="{{ route('karyawan.create') }}" style="padding: 9px 16px; background: #198754; color: white; text-decoration: none; border-radius: 4px; font-weight: bold; font-size: 14px;">+ Tambah Karyawan</a>
    </div>

    @if(session('status'))
        <div style="padding: 12px 16px; background: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; border-radius: 4px; margin-bottom: 20px; font-size: 14px;">
            {{ session('status') }}
        </div>
    @endif

    @if($errors->any())
        <div style="padding: 12px 16px; background: #f8d7da; color: #842029; border: 1px solid #f5c2c7; border-radius: 4px; margin-bottom: 20px; font-size: 14px;">
            {{ $errors->first() }}
        </div>
    @endif

    <div style="overflow-x: auto; background: #fff; border: 1px solid #e0e0e0; border-radius: 6px;">
        <table style="width: 100%; border-collapse: collapse; font-size: 14px; text-align: left;">
            <thead>
                <tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6; color: #495057;">
                    <th style="padding: 12px; text-align: center; width: 40px;">No</th>
                    <th style="padding: 12px;">NIK</th>
                    <th style="padding: 12px;">Nama</th>
                    <th style="padding: 12px;">Email</th>
                    <th style="padding: 12px;">Jabatan</th>
                    <th style="padding: 12px;">Gaji Pokok</th>
                    <th style="padding: 12px;">Lembur</th>
                    <th style="padding: 12px;">Pinjaman</th>
                    <th style="padding: 12px;">Gaji Bersih</th>
                    <th style="padding: 12px; text-align: center; width: 220px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($karyawans as $index => $item)
                    @php
                        $gaji = $item->gajis instanceof \Illuminate\Support\Collection ? $item->gajis->first() : $item->gajis;
                        $gajiId = $gaji->id ?? null;
                        $gajiPokok  = $gaji->gaji_pokok ?? 0;
                        $lembur     = $gaji->lembur ?? 0;
                        $pinjaman   = $gaji->pinjaman_karyawan ?? 0;
                        $gajiBersih = $gajiPokok + $lembur - $pinjaman;
                    @endphp
                    <tr style="border-bottom: 1px solid #e0e0e0;">
                        <td style="padding: 12px; text-align: center; color: #666;">{{ $loop->iteration }}</td>
                        <td style="padding: 12px;">{{ $item->nik }}</td>
                        <td style="padding: 12px; font-weight: 500;">{{ $item->nama }}</td>
                        <td style="padding: 12px; color: #555;">{{ $item->email ?? '-' }}</td>
                        <td style="padding: 12px; color: #555;">{{ $item->jabatan }}</td>
                        <td style="padding: 12px;">Rp {{ number_format($gajiPokok, 0, ',', '.') }}</td>
                        <td style="padding: 12px;">Rp {{ number_format($lembur, 0, ',', '.') }}</td>
                        <td style="padding: 12px;">Rp {{ number_format($pinjaman, 0, ',', '.') }}</td>
                        <td style="padding: 12px; font-weight: bold; color: #0d6efd;">Rp {{ number_format($gajiBersih, 0, ',', '.') }}</td>
                        <td style="padding: 12px; text-align: center;">
                            <div style="display: flex; gap: 4px; justify-content: center; align-items: center;">
                                <a href="{{ route('karyawan.edit', $item->id) }}" style="padding: 5px 8px; background: #0d6efd; color: white; border-radius: 4px; text-decoration: none; font-size: 12px;" title="Edit">Edit</a>

                                @if($gajiId)
                                    <a href="{{ route('gaji.pdf', $gajiId) }}" style="padding: 5px 8px; background: #198754; color: white; border-radius: 4px; text-decoration: none; font-size: 12px;" title="PDF">PDF</a>
                                    
                                    <form action="{{ route('gaji.email', $gajiId) }}" method="POST" style="margin:0;">
                                        @csrf
                                        <button type="submit" style="padding: 5px 8px; background: #ffc107; color: #333; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;" title="Email">Email</button>
                                    </form>

                                    <form action="{{ route('gaji.whatsapp', $gajiId) }}" method="POST" style="margin:0;">
                                        @csrf
                                        <button type="submit" style="padding: 5px 8px; background: #25D366; color: white; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;" title="WA">WA</button>
                                    </form>
                                @endif

                                <form action="{{ route('karyawan.destroy', $item->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Yakin hapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="padding: 5px 8px; background: #dc3545; color: white; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="padding: 20px; text-align: center; color: #888;">Data karyawan belum ada atau tidak ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection