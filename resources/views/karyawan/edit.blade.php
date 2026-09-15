@extends('layouts.app')

@section('title', 'Edit Data Karyawan')

@section('content')
    @php
        $gajiTerbaru  = $karyawan->gajis->last();
        $gajiPokok    = $gajiTerbaru->gaji_pokok ?? $karyawan->gaji_pokok ?? 0;
        $lembur       = $gajiTerbaru->lembur ?? $karyawan->lembur ?? 0;
        $pinjaman     = $gajiTerbaru->pinjaman_karyawan ?? $karyawan->pinjaman ?? 0;
        $periodeAwal  = $gajiTerbaru->periode_awal ?? now()->startOfMonth()->toDateString();
        $periodeAkhir = $gajiTerbaru->periode_akhir ?? now()->endOfMonth()->toDateString();
    @endphp

    <div style="max-width: 650px; margin: 20px auto; background: #ffffff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); overflow: hidden;">
        
        <div style="background-color: #61885c; color: white; padding: 18px 24px; font-weight: bold; font-size: 18px; display: flex; align-items: center; gap: 10px;">
            <span>Edit Data Karyawan & Periode Gaji</span>
        </div>

        <form action="{{ route('karyawan.update', $karyawan) }}" method="POST" style="padding: 24px;">
            @csrf
            @method('PUT')

            <!-- NIK -->
            <div style="margin-bottom: 18px;">
                <label for="nik" style="display: block; font-weight: 600; margin-bottom: 6px; color: #333;">NIK</label>
                <input id="nik" type="text" name="nik" value="{{ old('nik', $karyawan->nik) }}" required style="width: 100%; padding: 10px 14px; border: 1px solid #e0e0e0; border-radius: 6px; background-color: #f9f9f9; box-sizing: border-box;">
            </div>

            <!-- Nama Lengkap -->
            <div style="margin-bottom: 18px;">
                <label for="nama" style="display: block; font-weight: 600; margin-bottom: 6px; color: #333;">Nama Lengkap</label>
                <input id="nama" type="text" name="nama" value="{{ old('nama', $karyawan->nama) }}" required style="width: 100%; padding: 10px 14px; border: 1px solid #e0e0e0; border-radius: 6px; background-color: #f9f9f9; box-sizing: border-box;">
            </div>

            <!-- Jabatan -->
            <div style="margin-bottom: 18px;">
                <label for="jabatan" style="display: block; font-weight: 600; margin-bottom: 6px; color: #333;">Jabatan</label>
                <input id="jabatan" type="text" name="jabatan" value="{{ old('jabatan', $karyawan->jabatan) }}" required style="width: 100%; padding: 10px 14px; border: 1px solid #e0e0e0; border-radius: 6px; background-color: #f9f9f9; box-sizing: border-box;">
            </div>

            <!-- Periode Gaji (Dari - Sampai) -->
            <div style="display: flex; gap: 12px; margin-bottom: 18px;">
                <div style="flex: 1;">
                    <label for="periode_awal" style="display: block; font-weight: 600; margin-bottom: 6px; color: #333; font-size: 13px;">Periode Gaji (Dari Tanggal)</label>
                    <input id="periode_awal" type="date" name="periode_awal" value="{{ old('periode_awal', $periodeAwal) }}" required style="width: 100%; padding: 10px 12px; border: 1px solid #e0e0e0; border-radius: 6px; background-color: #f9f9f9; box-sizing: border-box;">
                </div>
                <div style="flex: 1;">
                    <label for="periode_akhir" style="display: block; font-weight: 600; margin-bottom: 6px; color: #333; font-size: 13px;">Periode Gaji (Sampai Tanggal)</label>
                    <input id="periode_akhir" type="date" name="periode_akhir" value="{{ old('periode_akhir', $periodeAkhir) }}" required style="width: 100%; padding: 10px 12px; border: 1px solid #e0e0e0; border-radius: 6px; background-color: #f9f9f9; box-sizing: border-box;">
                </div>
            </div>

            <!-- Gaji Pokok, Lembur, Pinjaman -->
            <div style="display: flex; gap: 12px; margin-bottom: 24px;">
                <div style="flex: 1;">
                    <label for="gaji_pokok" style="display: block; font-weight: 600; margin-bottom: 6px; color: #333; font-size: 13px;">Gaji Pokok (Rp)</label>
                    <input id="gaji_pokok" type="number" min="0" name="gaji_pokok" value="{{ old('gaji_pokok', $gajiPokok) }}" required style="width: 100%; padding: 10px 12px; border: 1px solid #e0e0e0; border-radius: 6px; background-color: #f9f9f9; box-sizing: border-box;">
                </div>
                <div style="flex: 1;">
                    <label for="lembur" style="display: block; font-weight: 600; margin-bottom: 6px; color: #333; font-size: 13px;">Lembur (Rp)</label>
                    <input id="lembur" type="number" min="0" name="lembur" value="{{ old('lembur', $lembur) }}" required style="width: 100%; padding: 10px 12px; border: 1px solid #e0e0e0; border-radius: 6px; background-color: #f9f9f9; box-sizing: border-box;">
                </div>
                <div style="flex: 1;">
                    <label for="pinjaman" style="display: block; font-weight: 600; margin-bottom: 6px; color: #333; font-size: 13px;">Pinjaman (Rp)</label>
                    <input id="pinjaman" type="number" min="0" name="pinjaman" value="{{ old('pinjaman', $pinjaman) }}" required style="width: 100%; padding: 10px 12px; border: 1px solid #e0e0e0; border-radius: 6px; background-color: #f9f9f9; box-sizing: border-box;">
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px;">
                <a href="{{ route('karyawan.index') }}" class="btn" style="background-color: #2b3035; color: white; padding: 10px 18px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 14px;">&larr; Kembali</a>
                <button type="submit" class="btn" style="background-color: #3b6b3e; color: white; padding: 10px 18px; border-radius: 6px; border: none; cursor: pointer; font-weight: 600; font-size: 14px;">💾 Perbarui Data</button>
            </div>
        </form>
    </div>
@endsection