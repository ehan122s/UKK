@php
    /** @var \App\Models\Gaji|null $gaji */
    /** @var \Illuminate\Support\Collection $karyawans */
@endphp

<div class="field-underline">
    <label for="karyawan_id">Karyawan</label>
    <select id="karyawan_id" name="karyawan_id"
            style="width:100%; border:none; border-bottom:1px solid var(--line); padding:6px 2px; font-size:13px; font-family:inherit; background:transparent;">
        <option value="">— Pilih karyawan —</option>
        @foreach ($karyawans as $k)
            <option value="{{ $k->id }}" @selected(old('karyawan_id', $gaji->karyawan_id ?? '') == $k->id)>
                {{ $k->nama }} ({{ $k->nik }})
            </option>
        @endforeach
    </select>
</div>

<div class="field-underline">
    <label for="periode_awal">Periode Awal</label>
    <input id="periode_awal" type="date" name="periode_awal"
           value="{{ old('periode_awal', $gaji->periode_awal?->format('Y-m-d') ?? '') }}">
</div>

<div class="field-underline">
    <label for="periode_akhir">Periode Akhir</label>
    <input id="periode_akhir" type="date" name="periode_akhir"
           value="{{ old('periode_akhir', $gaji->periode_akhir?->format('Y-m-d') ?? '') }}">
</div>

<div class="field-underline">
    <label for="gaji_pokok">Gaji Pokok (Rp)</label>
    <input id="gaji_pokok" type="number" min="0" name="gaji_pokok"
           value="{{ old('gaji_pokok', $gaji->gaji_pokok ?? '') }}">
</div>

<div class="field-underline">
    <label for="lembur">Lembur (Rp)</label>
    <input id="lembur" type="number" min="0" name="lembur"
           value="{{ old('lembur', $gaji->lembur ?? '') }}">
</div>

<div class="field-underline">
    <label for="pinjaman_karyawan">Pinjaman Karyawan (Rp)</label>
    <input id="pinjaman_karyawan" type="number" min="0" name="pinjaman_karyawan"
           value="{{ old('pinjaman_karyawan', $gaji->pinjaman_karyawan ?? '') }}">
</div>

<p class="form-note">
    Gaji Bersih akan dihitung otomatis: (Gaji Pokok + Lembur) − Pinjaman Karyawan.
</p>
