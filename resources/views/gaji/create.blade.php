@extends('layouts.app')

@section('title', 'Input Data Gaji')

@section('content')
    <div style="width: 100%; max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 8px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); border: 1px solid #dcdcdc; overflow: hidden;">
        
        <!-- HEADER FORM (HIJAU SLIP GAJI) -->
        <div style="background-color: #61885c; color: white; padding: 16px 20px; font-weight: bold; font-size: 16px; display: flex; align-items: center; gap: 8px;">
            <span>➕</span> Generate Slip Gaji Karyawan
        </div>

        <div style="padding: 24px;">
            @if ($errors->any())
                <div style="background-color: #f8d7da; color: #842029; padding: 12px 16px; border-radius: 4px; margin-bottom: 20px; font-size: 13px; border: 1px solid #f5c2c7;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('gaji.store') }}" method="POST">
                @csrf

                <!-- HIDDEN INPUT PERIODE AWAL & AKHIR (DIAMBIL DARI PARAMETER URL / OBJECT GAJI) -->
                <input type="hidden" name="periode_awal" value="{{ old('periode_awal', $gaji->periode_awal ?? request('periode_awal')) }}">
                <input type="hidden" name="periode_akhir" value="{{ old('periode_akhir', $gaji->periode_akhir ?? request('periode_akhir')) }}">

                <!-- DROPDOWN PILIH KARYAWAN -->
                <div style="margin-bottom: 20px;">
                    <label for="karyawan_id" style="display: block; font-weight: 600; color: #2e4600; margin-bottom: 8px; font-size: 13px;">Pilih Karyawan</label>
                    <select name="karyawan_id" id="karyawan_id" onchange="autoFillGaji()" required style="width: 100%; padding: 10px 12px; border: 1px solid #ff781f; border-radius: 4px; font-size: 14px; background-color: #fff; outline: none; cursor: pointer;">
                        <option value="">-- Pilih Karyawan --</option>
                        @foreach ($karyawans as $k)
                            <option value="{{ $k->id }}" 
                                data-gajipokok="{{ $k->gaji_pokok }}" 
                                data-lembur="{{ $k->lembur }}" 
                                data-pinjaman="{{ $k->pinjaman_karyawan ?? $k->pinjaman }}"
                                {{ (old('karyawan_id', $gaji->karyawan_id ?? request('karyawan_id')) == $k->id) ? 'selected' : '' }}>
                                {{ $k->nama }} (NIK: {{ $k->nik }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- FORM ANGKA (AUTO FILL DATA ACUAN KARYAWAN) -->
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-weight: 600; color: #2e4600; margin-bottom: 6px; font-size: 13px;">Gaji Pokok (Rp)</label>
                    <input type="number" name="gaji_pokok" id="gaji_pokok" value="{{ old('gaji_pokok', 0) }}" min="0" required style="width: 100%; padding: 9px 12px; border: 1px solid #ff781f; border-radius: 4px; box-sizing: border-box; font-size: 13px; outline: none;">
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-weight: 600; color: #2e4600; margin-bottom: 6px; font-size: 13px;">Lembur (Rp)</label>
                    <input type="number" name="lembur" id="lembur" value="{{ old('lembur', 0) }}" min="0" required style="width: 100%; padding: 9px 12px; border: 1px solid #ff781f; border-radius: 4px; box-sizing: border-box; font-size: 13px; outline: none;">
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-weight: 600; color: #2e4600; margin-bottom: 6px; font-size: 13px;">Pinjaman Karyawan (Rp)</label>
                    <input type="number" name="pinjaman_karyawan" id="pinjaman_karyawan" value="{{ old('pinjaman_karyawan', 0) }}" min="0" required style="width: 100%; padding: 9px 12px; border: 1px solid #ff781f; border-radius: 4px; box-sizing: border-box; font-size: 13px; outline: none;">
                </div>

                <!-- TOMBOL AKSI -->
                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #eee; padding-top: 16px;">
                    <a href="{{ route('karyawan.index') }}" style="padding: 9px 18px; background-color: #343a40; color: white; text-decoration: none; border-radius: 4px; font-size: 13px; font-weight: 600;">&larr; Batal</a>
                    
                    <button type="submit" style="padding: 9px 20px; background-color: #61885c; color: white; border: none; border-radius: 4px; font-size: 13px; font-weight: 600; cursor: pointer;">
                        💾 Generate Gaji
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function autoFillGaji() {
            const select = document.getElementById('karyawan_id');
            const selectedOption = select.options[select.selectedIndex];

            if (selectedOption.value) {
                document.getElementById('gaji_pokok').value = selectedOption.getAttribute('data-gajipokok') || 0;
                document.getElementById('lembur').value = selectedOption.getAttribute('data-lembur') || 0;
                document.getElementById('pinjaman_karyawan').value = selectedOption.getAttribute('data-pinjaman') || 0;
            }
        }

        // Jalankan autoFillGaji saat halaman pertama kali dimuat jika karyawan sudah terpilih dari URL
        document.addEventListener('DOMContentLoaded', function() {
            autoFillGaji();
        });
    </script>
@endsection