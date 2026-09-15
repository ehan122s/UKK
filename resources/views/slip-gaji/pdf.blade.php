<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; color:#1F2937; font-size:12px; }
    h1 { font-size:16px; margin:0 0 2px; }
    .period { color:#6B7280; margin:0 0 16px; }
    
    /* Tabel Info Karyawan (Pengganti Flexbox) */
    .table-info { width:100%; margin-bottom:18px; border-collapse:collapse; }
    .table-info td { padding:3px 0; border:none; text-align:left; }
    .table-info td.label { width:80px; color:#6B7280; }
    .table-info td.colon { width:15px; text-align:center; color:#6B7280; }
    .table-info td.value { font-weight:bold; color:#1F2937; }

    /* Tabel Rincian Gaji & Potongan */
    table.data-table { width:100%; border-collapse:collapse; margin-bottom:14px; }
    table.data-table td, table.data-table th { padding:6px 0; border-bottom:1px solid #D1D5DB; text-align:left; }
    table.data-table td:last-child, table.data-table th:last-child { text-align:right; }
    table.data-table tr.total td { font-weight:bold; border-top:1px solid #1F2937; border-bottom:none; }
    
    /* Box Gaji Bersih (Pengganti Flexbox) */
    .net-box {
        width: 100%;
        background: #EFF6FF;
        border: 1px solid #BFDBFE;
        font-weight: bold;
        color: #1D4ED8;
    }
    .net-box td { padding: 10px 14px; border: none; }
    .net-box td:last-child { text-align: right; }
</style>
</head>
<body>
    <h1>Slip Gaji Karyawan</h1>
    <p class="period">
        Periode {{ $gaji->periode_awal->translatedFormat('d M Y') }}
        – {{ $gaji->periode_akhir->translatedFormat('d M Y') }}
    </p>

    <!-- Informasi Karyawan Pakai Tabel Rapi -->
    <table class="table-info">
        <tr>
            <td class="label">Nama</td>
            <td class="colon">:</td>
            <td class="value">{{ $karyawan->nama }}</td>
        </tr>
        <tr>
            <td class="label">NIK</td>
            <td class="colon">:</td>
            <td class="value">{{ $karyawan->nik }}</td>
        </tr>
        <tr>
            <td class="label">Jabatan</td>
            <td class="colon">:</td>
            <td class="value">{{ $karyawan->jabatan }}</td>
        </tr>
    </table>

    <table class="data-table">
        <tr><th>Penghasilan</th><th>Jumlah</th></tr>
        <tr><td>Gaji Pokok</td><td>Rp {{ number_format($gaji->gaji_pokok, 0, ',', '.') }}</td></tr>
        <tr><td>Lembur</td><td>Rp {{ number_format($gaji->lembur, 0, ',', '.') }}</td></tr>
        <tr class="total"><td>Total Penghasilan</td><td>Rp {{ number_format($gaji->total_penghasilan, 0, ',', '.') }}</td></tr>
    </table>

    <table class="data-table">
        <tr><th>Potongan</th><th>Jumlah</th></tr>
        <tr><td>Pinjaman Karyawan</td><td>Rp {{ number_format($gaji->pinjaman_karyawan, 0, ',', '.') }}</td></tr>
        <tr class="total"><td>Total Potongan</td><td>Rp {{ number_format($gaji->total_potongan, 0, ',', '.') }}</td></tr>
    </table>

    <!-- Box Gaji Bersih Pakai Tabel -->
    <table class="net-box">
        <tr>
            <td>Gaji Bersih</td>
            <td>Rp {{ number_format($gaji->gaji_bersih, 0, ',', '.') }}</td>
        </tr>
    </table>
</body>
</html>