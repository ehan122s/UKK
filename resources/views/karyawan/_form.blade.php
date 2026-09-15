@csrf

<div class="field-underline" style="margin-bottom: 16px;">
    <label for="nik" style="display: block; margin-bottom: 6px; font-weight: 600;">NIK</label>
    <input id="nik" type="text" name="nik" value="{{ old('nik', $karyawan->nik ?? '') }}" required style="width: 100%; padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px;">
</div>

<div class="field-underline" style="margin-bottom: 16px;">
    <label for="nama" style="display: block; margin-bottom: 6px; font-weight: 600;">Nama Lengkap</label>
    <input id="nama" type="text" name="nama" value="{{ old('nama', $karyawan->nama ?? '') }}" required style="width: 100%; padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px;">
</div>

<div class="field-underline" style="margin-bottom: 16px;">
    <label for="jabatan" style="display: block; margin-bottom: 6px; font-weight: 600;">Jabatan</label>
    <input id="jabatan" type="text" name="jabatan" value="{{ old('jabatan', $karyawan->jabatan ?? '') }}" required style="width: 100%; padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px;">
</div>

<div style="display: flex; gap: 12px; margin-bottom: 16px;">
    <div style="flex: 1;">
        <label for="gaji_pokok" style="display: block; margin-bottom: 6px; font-weight: 600;">Gaji Pokok (Rp)</label>
        <input id="gaji_pokok" type="number" min="0" name="gaji_pokok" value="{{ old('gaji_pokok', $karyawan->gaji_pokok ?? 0) }}" required style="width: 100%; padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px;">
    </div>
    <div style="flex: 1;">
        <label for="lembur" style="display: block; margin-bottom: 6px; font-weight: 600;">Lembur (Rp)</label>
        <input id="lembur" type="number" min="0" name="lembur" value="{{ old('lembur', $karyawan->lembur ?? 0) }}" required style="width: 100%; padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px;">
    </div>
    <div style="flex: 1;">
        <label for="pinjaman_karyawan" style="display: block; margin-bottom: 6px; font-weight: 600;">Pinjaman (Rp)</label>
        <input id="pinjaman_karyawan" type="number" min="0" name="pinjaman_karyawan" value="{{ old('pinjaman_karyawan', $karyawan->pinjaman_karyawan ?? 0) }}" required style="width: 100%; padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px;">
    </div>
</div>

<div style="margin-top: 24px; display: flex; gap: 8px;">
    <a href="{{ route('karyawan.index') }}" class="btn" style="background-color: #212529; color: white; padding: 10px 18px; border-radius: 6px; text-decoration: none;">&larr; Kembali</a>
    <button type="submit" class="btn" style="background-color: #2e7d32; color: white; padding: 10px 18px; border-radius: 6px; border: none; cursor: pointer; font-weight: 600;">
        {{ isset($karyawan) && $karyawan->exists ? 'Perbarui Data' : 'Simpan Data' }}
    </button>
</div>