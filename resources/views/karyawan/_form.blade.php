@csrf

@php
    use Illuminate\Support\Carbon;

    $gajiTerbaru  = isset($karyawan) && $karyawan->gajis ? $karyawan->gajis->last() : null;
    $gajiPokok    = old('gaji_pokok', $gajiTerbaru->gaji_pokok ?? $karyawan->gaji_pokok ?? 0);
    $lembur       = old('lembur', $gajiTerbaru->lembur ?? $karyawan->lembur ?? 0);
    $pinjaman     = old('pinjaman_karyawan', $gajiTerbaru->pinjaman_karyawan ?? $karyawan->pinjaman ?? 0);
    
    // Ambil tanggal dari URL query param (hasil pop-up) atau default 25 Nov 2026 - 25 Des 2026
    $periodeAwal  = request('periode_awal', old('periode_awal', $gajiTerbaru->periode_awal ?? '2026-11-25'));
    $periodeAkhir = request('periode_akhir', old('periode_akhir', $gajiTerbaru->periode_akhir ?? '2026-12-25'));

    // Format Bahasa Indonesia: 25 November 2026 - 25 Desember 2026
    $labelAwal  = Carbon::parse($periodeAwal)->translatedFormat('d F Y');
    $labelAkhir = Carbon::parse($periodeAkhir)->translatedFormat('d F Y');

    // Captcha Acak
    $num1 = rand(1, 10);
    $num2 = rand(1, 10);
@endphp

<!-- INPUT HIDDEN UNTUK DISIMPAN KE DATABASE -->
<input type="hidden" name="periode_awal" value="{{ $periodeAwal }}">
<input type="hidden" name="periode_akhir" value="{{ $periodeAkhir }}">

<!-- CARD CONTAINER SLIP GAJI CENTER -->
<div style="width: 100%; max-width: 700px; margin: 0 auto; background: #ffffff; padding: 28px; border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border: 1px solid #dcdcdc; box-sizing: border-box;">

    <!-- HEADER SLIP GAJI DENGAN TEKS PERIODE MOCKUP -->
    <div style="text-align: center; margin-bottom: 24px;">
        <h3 style="margin: 0; font-size: 18px; font-weight: bold; letter-spacing: 0.5px; color: #111;">SLIP GAJI KARYAWAN</h3>
        <div style="margin-top: 6px; font-size: 14px; font-weight: 600; color: #333; letter-spacing: 0.3px;">
            PERIODE {{ $labelAwal }} - {{ $labelAkhir }}
        </div>
    </div>

    <!-- DATA KARYAWAN -->
    <div style="max-width: 380px; margin-bottom: 20px;">
        <div style="display: flex; align-items: center; margin-bottom: 8px;">
            <label for="nama" style="width: 100px; font-weight: 600; font-size: 13px; color: #222;">NAMA</label>
            <span style="margin-right: 10px; font-weight: bold;">:</span>
            <input id="nama" type="text" name="nama" value="{{ old('nama', $karyawan->nama ?? '') }}" required style="flex: 1; padding: 6px 10px; border: 1px solid #ff781f; border-radius: 3px; outline: none; font-size: 13px;">
        </div>
        <div style="display: flex; align-items: center; margin-bottom: 8px;">
            <label for="nik" style="width: 100px; font-weight: 600; font-size: 13px; color: #222;">NIK</label>
            <span style="margin-right: 10px; font-weight: bold;">:</span>
            <input id="nik" type="text" name="nik" value="{{ old('nik', $karyawan->nik ?? '') }}" required style="flex: 1; padding: 6px 10px; border: 1px solid #ff781f; border-radius: 3px; outline: none; font-size: 13px;">
        </div>
        <div style="display: flex; align-items: center; margin-bottom: 8px;">
            <label for="jabatan" style="width: 100px; font-weight: 600; font-size: 13px; color: #222;">JABATAN</label>
            <span style="margin-right: 10px; font-weight: bold;">:</span>
            <input id="jabatan" type="text" name="jabatan" value="{{ old('jabatan', $karyawan->jabatan ?? '') }}" required style="flex: 1; padding: 6px 10px; border: 1px solid #ff781f; border-radius: 3px; outline: none; font-size: 13px;">
        </div>
    </div>

    <!-- HEADER HIJAU: PENGHASILAN & POTONGAN -->
    <div style="display: flex; background-color: #c5d99b; border-radius: 2px; font-weight: bold; font-size: 13px; margin-bottom: 14px; color: #273b00;">
        <div style="flex: 1; padding: 8px 12px; text-align: center;">PENGHASILAN</div>
        <div style="flex: 1; padding: 8px 12px; text-align: center;">POTONGAN</div>
    </div>

    <!-- TABEL INPUT ANGKA -->
    <div style="display: flex; gap: 24px; margin-bottom: 16px;">
        <!-- Penghasilan -->
        <div style="flex: 1;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <label for="gaji_pokok" style="font-size: 13px; color: #333;">Gaji Pokok</label>
                <input id="gaji_pokok" type="number" min="0" name="gaji_pokok" value="{{ $gajiPokok }}" required oninput="hitungTotalSlip()" style="width: 58%; padding: 5px 8px; border: 1px solid #ff781f; border-radius: 3px; outline: none; font-size: 13px;">
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
                <label for="lembur" style="font-size: 13px; color: #333;">Lembur</label>
                <input id="lembur" type="number" min="0" name="lembur" value="{{ $lembur }}" required oninput="hitungTotalSlip()" style="width: 58%; padding: 5px 8px; border: 1px solid #ff781f; border-radius: 3px; outline: none; font-size: 13px;">
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <label style="font-size: 13px; font-weight: 600; color: #111;">Total Penghasilan</label>
                <input id="total_penghasilan" type="text" readonly style="width: 58%; padding: 5px 8px; border: 1px solid #ff781f; border-radius: 3px; background-color: #fbfbfb; font-weight: bold; outline: none; font-size: 13px;">
            </div>
        </div>

        <!-- Potongan -->
        <div style="flex: 1;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <label for="pinjaman_karyawan" style="font-size: 13px; color: #333;">Pinjaman Karyawan</label>
                <input id="pinjaman_karyawan" type="number" min="0" name="pinjaman_karyawan" value="{{ $pinjaman }}" required oninput="hitungTotalSlip()" style="width: 58%; padding: 5px 8px; border: 1px solid #ff781f; border-radius: 3px; outline: none; font-size: 13px;">
            </div>
            <div style="height: 31px; margin-bottom: 14px;"></div>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <label style="font-size: 13px; font-weight: 600; color: #111;">Total Potongan</label>
                <input id="total_potongan" type="text" readonly style="width: 58%; padding: 5px 8px; border: 1px solid #ff781f; border-radius: 3px; background-color: #fbfbfb; font-weight: bold; outline: none; font-size: 13px;">
            </div>
        </div>
    </div>

    <!-- STRIP GAJI BERSIH -->
    <div style="display: flex; align-items: center; background-color: #c5d99b; padding: 6px 14px; border-radius: 2px; margin-bottom: 20px;">
        <div style="flex: 1; font-weight: bold; text-align: center; font-size: 14px; color: #273b00;">Gaji Bersih</div>
        <div style="flex: 1; text-align: right;">
            <input id="gaji_bersih" type="text" readonly style="width: 58%; padding: 5px 8px; border: 1px solid #ff781f; border-radius: 3px; background-color: #ffffff; font-weight: bold; outline: none; font-size: 13px;">
        </div>
    </div>

    <!-- CAPTCHA DINAMIS -->
    <div style="max-width: 280px; margin-bottom: 20px;">
        <div style="display: flex; align-items: center; background-color: #cfcfcf; padding: 5px 10px; border-radius: 2px; margin-bottom: 6px;">
            <span id="captcha_label" style="font-size: 13px; font-weight: bold; flex: 1; color: #333;">Captcha : {{ $num1 }}*{{ $num2 }}</span>
            <button type="button" style="background: none; border: none; cursor: pointer; font-size: 14px;" onclick="generateRandomCaptcha()">🔄</button>
        </div>
        <input type="number" name="captcha" id="captcha_input" placeholder="Masukkan captcha..." required style="width: 100%; padding: 6px 10px; border: 1px solid #ff781f; border-radius: 3px; box-sizing: border-box; outline: none; font-size: 13px;">
        
        <input type="hidden" name="captcha_expected" id="captcha_expected" value="{{ $num1 * $num2 }}">
    </div>

    <!-- TOMBOL AKSI -->
    <div style="display: flex; gap: 10px; max-width: 280px;">
        <button type="submit" class="btn" style="flex: 1; background-color: #4b89dc; color: white; padding: 9px; border: none; border-radius: 2px; font-weight: 600; cursor: pointer; font-size: 13px;">
            Submit
        </button>
        <a href="{{ route('karyawan.index') }}" class="btn" style="background-color: #6c757d; color: white; padding: 9px 14px; border-radius: 2px; text-decoration: none; font-size: 13px; font-weight: 600;">
            Kembali
        </a>
    </div>

</div>

<script>
    function hitungTotalSlip() {
        const gajiPokok = parseFloat(document.getElementById('gaji_pokok').value) || 0;
        const lembur    = parseFloat(document.getElementById('lembur').value) || 0;
        const pinjaman  = parseFloat(document.getElementById('pinjaman_karyawan').value) || 0;

        const totalPenghasilan = gajiPokok + lembur;
        const totalPotongan    = pinjaman;
        const gajiBersih       = totalPenghasilan - totalPotongan;

        document.getElementById('total_penghasilan').value = 'Rp ' + totalPenghasilan.toLocaleString('id-ID');
        document.getElementById('total_potongan').value    = 'Rp ' + totalPotongan.toLocaleString('id-ID');
        document.getElementById('gaji_bersih').value       = 'Rp ' + gajiBersih.toLocaleString('id-ID');
    }

    function generateRandomCaptcha() {
        const n1 = Math.floor(Math.random() * 9) + 1;
        const n2 = Math.floor(Math.random() * 9) + 1;
        const hasil = n1 * n2;

        document.getElementById('captcha_label').innerText = 'Captcha : ' + n1 + '*' + n2;
        document.getElementById('captcha_expected').value = hasil;
        document.getElementById('captcha_input').value = '';
    }

    document.addEventListener('DOMContentLoaded', hitungTotalSlip);
</script>