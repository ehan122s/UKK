@php
    $gaji = isset($karyawan) ? ($karyawan->gajis instanceof \Illuminate\Support\Collection ? $karyawan->gajis->first() : $karyawan->gajis) : null;
@endphp

<div style="font-family: sans-serif;">
    <!-- NIK -->
    <div style="margin-bottom: 20px;">
        <label for="nik" style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px; color: #333;">NIK</label>
        <input type="text" name="nik" id="nik" value="{{ old('nik', $karyawan->nik ?? '') }}" required style="width: 100%; padding: 10px 14px; border: 1px solid #dcdcdc; border-radius: 6px; font-size: 14px; box-sizing: border-box; background-color: #fafafa;">
        @error('nik') <span style="color: #dc3545; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span> @enderror
    </div>

    <!-- Nama Lengkap -->
    <div style="margin-bottom: 20px;">
        <label for="nama" style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px; color: #333;">Nama Lengkap</label>
        <input type="text" name="nama" id="nama" value="{{ old('nama', $karyawan->nama ?? '') }}" required style="width: 100%; padding: 10px 14px; border: 1px solid #dcdcdc; border-radius: 6px; font-size: 14px; box-sizing: border-box; background-color: #fafafa;">
        @error('nama') <span style="color: #dc3545; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span> @enderror
    </div>

    <!-- Email & No HP / WA (2 Kolom Sejajar) -->
    <div style="display: flex; gap: 16px; margin-bottom: 20px;">
        <div style="flex: 1;">
            <label for="email" style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px; color: #333;">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email', $karyawan->email ?? '') }}" placeholder="contoh@gmail.com" required style="width: 100%; padding: 10px 14px; border: 1px solid #dcdcdc; border-radius: 6px; font-size: 14px; box-sizing: border-box; background-color: #fafafa;">
            @error('email') <span style="color: #dc3545; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span> @enderror
        </div>

        <div style="flex: 1;">
            <label for="no_hp" style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px; color: #333;">No. WhatsApp</label>
            <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp', $karyawan->no_hp ?? '') }}" placeholder="08xxxxxxxxxx" required style="width: 100%; padding: 10px 14px; border: 1px solid #dcdcdc; border-radius: 6px; font-size: 14px; box-sizing: border-box; background-color: #fafafa;">
            @error('no_hp') <span style="color: #dc3545; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span> @enderror
        </div>
    </div>

    <!-- Jabatan -->
    <div style="margin-bottom: 20px;">
        <label for="jabatan" style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px; color: #333;">Jabatan</label>
        <input type="text" name="jabatan" id="jabatan" value="{{ old('jabatan', $karyawan->jabatan ?? '') }}" required style="width: 100%; padding: 10px 14px; border: 1px solid #dcdcdc; border-radius: 6px; font-size: 14px; box-sizing: border-box; background-color: #fafafa;">
        @error('jabatan') <span style="color: #dc3545; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span> @enderror
    </div>

    <!-- Rincian Gaji (3 Kolom Sejajar) -->
    <div style="display: flex; gap: 16px; margin-bottom: 28px;">
        <div style="flex: 1;">
            <label for="gaji_pokok" style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px; color: #333;">Gaji Pokok (Rp)</label>
            <input type="number" name="gaji_pokok" id="gaji_pokok" value="{{ old('gaji_pokok', $gaji->gaji_pokok ?? 0) }}" style="width: 100%; padding: 10px 14px; border: 1px solid #dcdcdc; border-radius: 6px; font-size: 14px; box-sizing: border-box; background-color: #fafafa;">
        </div>

        <div style="flex: 1;">
            <label for="lembur" style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px; color: #333;">Lembur (Rp)</label>
            <input type="number" name="lembur" id="lembur" value="{{ old('lembur', $gaji->lembur ?? 0) }}" style="width: 100%; padding: 10px 14px; border: 1px solid #dcdcdc; border-radius: 6px; font-size: 14px; box-sizing: border-box; background-color: #fafafa;">
        </div>

        <div style="flex: 1;">
            <label for="pinjaman" style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px; color: #333;">Pinjaman (Rp)</label>
            <input type="number" name="pinjaman" id="pinjaman" value="{{ old('pinjaman', $gaji->pinjaman_karyawan ?? 0) }}" style="width: 100%; padding: 10px 14px; border: 1px solid #dcdcdc; border-radius: 6px; font-size: 14px; box-sizing: border-box; background-color: #fafafa;">
        </div>
    </div>

    <!-- Tombol Aksi -->
    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 10px;">
        <a href="{{ route('karyawan.index') }}" style="padding: 10px 20px; background-color: #212529; color: white; text-decoration: none; border-radius: 6px; font-weight: 500; font-size: 14px; display: inline-flex; align-items: center; gap: 6px;">
            <span style="font-size: 16px;">&larr;</span> Kembali
        </a>

        <button type="submit" style="padding: 10px 22px; background-color: #4a6d45; color: white; border: none; border-radius: 6px; font-weight: 500; font-size: 14px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path d="M11 2H9v3h2V2z"/>
                <path d="M1.5 0h11.586a1.5 1.5 0 0 1 1.06.44l1.415 1.414A1.5 1.5 0 0 1 16 2.914V14.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 14.5v-13A1.5 1.5 0 0 1 1.5 0zM1 1.5v13a.5.5 0 0 0 .5.5H2v-4.5A1.5 1.5 0 0 1 3.5 9h9a1.5 1.5 0 0 1 1.5 1.5V15h.5a.5.5 0 0 0 .5-.5V2.914a.5.5 0 0 0-.146-.353l-1.415-1.415A.5.5 0 0 0 13.086 1H12v2.5A1.5 1.5 0 0 1 10.5 5h-5A1.5 1.5 0 0 1 4 3.5V1H1.5a.5.5 0 0 0-.5.5z"/>
            </svg>
            Perbarui Data
        </button>
    </div>
</div>