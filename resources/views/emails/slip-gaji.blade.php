<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"></head>
<body style="font-family:sans-serif;color:#1F2937;">
    <p>Halo {{ $gaji->karyawan->nama }},</p>

    <p>
        Slip gaji Anda untuk periode
        {{ $gaji->periode_awal->translatedFormat('d M Y') }}
        – {{ $gaji->periode_akhir->translatedFormat('d M Y') }}
        sudah tersedia. Silakan lihat lampiran PDF untuk rincian lengkap.
    </p>

    <p><strong>Gaji Bersih: Rp {{ number_format($gaji->gaji_bersih, 0, ',', '.') }}</strong></p>

    <p>Terima kasih.</p>
</body>
</html>
