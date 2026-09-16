@extends('layouts.app')

@section('title', 'Data Karyawan')

@section('content')
    <div style="width: 100%; background: #ffffff; padding: 24px; border-radius: 8px; box-shadow: 0 4px 16px rgba(0,0,0,0.06); border: 1px solid #dcdcdc; box-sizing: border-box;">

        <!-- HEADER -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid #c5d99b; padding-bottom: 14px; flex-wrap: wrap; gap: 12px;">
            <div>
                <h2 style="margin: 0; color: #2e4600; font-size: 20px; font-weight: bold; text-transform: uppercase;">Data Karyawan</h2>
                <p style="margin: 4px 0 0 0; font-size: 13px; color: #555;">Kelola data karyawan dan slip gaji secara efisien</p>
            </div>
            <button type="button" onclick="bukaModalPeriode()" style="background-color: #61885c; color: white; padding: 10px 18px; border: none; border-radius: 4px; font-weight: 600; cursor: pointer; font-size: 14px;">
                + Tambah Karyawan
            </button>
        </div>

        <!-- FORM PENCARIAN -->
        <div style="margin-bottom: 20px; background-color: #f9fbf8; padding: 12px; border-radius: 6px; border: 1px solid #e2ebd8;">
            <form action="{{ route('karyawan.index') }}" method="GET" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau NIK..." style="padding: 8px 12px; border: 1px solid #ff781f; border-radius: 4px; width: 260px; font-size: 13px; outline: none;">

                <button type="submit" style="padding: 8px 18px; background-color: #61885c; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 13px;">Cari</button>
                
                @if(request('search'))
                    <a href="{{ route('karyawan.index') }}" style="padding: 8px 12px; background-color: #dc3545; color: white; border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: 600;">Reset</a>
                @endif
            </form>
        </div>

        <!-- TABEL DATA -->
        <div style="width: 100%; overflow-x: auto;">
            @if ($karyawans->isEmpty())
                <p style="padding: 20px; text-align: center; color: #777;">Data karyawan tidak ditemukan.</p>
            @else
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <thead>
                        <tr style="background-color: #c5d99b; color: #2e4600; text-align: left; font-weight: bold;">
                            <th style="padding: 10px;">NIK</th>
                            <th style="padding: 10px;">NAMA</th>
                            <th style="padding: 10px;">JABATAN</th>
                            <th style="padding: 10px;">PERIODE GAJI</th>
                            <th style="padding: 10px;">GAJI POKOK</th>
                            <th style="padding: 10px;">LEMBUR</th>
                            <th style="padding: 10px;">PINJAMAN</th>
                            <th style="padding: 10px;">GAJI BERSIH</th>
                            <th style="padding: 10px; text-align: center;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($karyawans as $karyawan)
                            @php
                                $gajiTerbaru = $karyawan->gajis->last();
                                $gajiPokok   = $gajiTerbaru->gaji_pokok ?? $karyawan->gaji_pokok ?? 0;
                                $lembur      = $gajiTerbaru->lembur ?? $karyawan->lembur ?? 0;
                                $pinjaman    = $gajiTerbaru->pinjaman_karyawan ?? $karyawan->pinjaman ?? 0;
                                $gajiBersih  = $gajiPokok + $lembur - $pinjaman;

                                $periodeAwal  = $gajiTerbaru->periode_awal ?? null;
                                $periodeAkhir = $gajiTerbaru->periode_akhir ?? null;
                            @endphp
                            <tr style="border-bottom: 1px solid #e6eee0;">
                                <td style="padding: 10px; color: #444; white-space: nowrap;">{{ $karyawan->nik }}</td>
                                <td style="padding: 10px; font-weight: bold; color: #222; white-space: nowrap;">{{ $karyawan->nama }}</td>
                                <td style="padding: 10px; color: #555; white-space: nowrap;">{{ $karyawan->jabatan }}</td>
                                <td style="padding: 10px; font-size: 12px; white-space: nowrap;">
                                    @if($periodeAwal && $periodeAkhir)
                                        {{ \Carbon\Carbon::parse($periodeAwal)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($periodeAkhir)->translatedFormat('d M Y') }}
                                    @else
                                        <span style="color: #999;">-</span>
                                    @endif
                                </td>
                                <td style="padding: 10px; white-space: nowrap;">Rp {{ number_format($gajiPokok, 0, ',', '.') }}</td>
                                <td style="padding: 10px; white-space: nowrap;">Rp {{ number_format($lembur, 0, ',', '.') }}</td>
                                <td style="padding: 10px; white-space: nowrap;">Rp {{ number_format($pinjaman, 0, ',', '.') }}</td>
                                <td style="padding: 10px; color: #2e4600; font-weight: bold; white-space: nowrap;">
                                    Rp {{ number_format($gajiBersih, 0, ',', '.') }}
                                </td>
                                <td style="padding: 10px; text-align: center; white-space: nowrap;">
                                    <a href="{{ route('karyawan.edit', $karyawan) }}" style="background-color: #4b89dc; color: white; padding: 4px 8px; border-radius: 3px; text-decoration: none; font-size: 11px; font-weight: 600;">Edit</a>

                                    @if ($gajiTerbaru)
                                        <a href="{{ route('gaji.pdf', $gajiTerbaru) }}" style="background-color: #61885c; color: white; padding: 4px 8px; border-radius: 3px; text-decoration: none; font-size: 11px; font-weight: 600;">PDF</a>

                                        <button type="button" style="background-color: #ffc107; color: #000; border: none; padding: 4px 8px; border-radius: 3px; font-size: 11px; cursor: pointer; font-weight: 600;" onclick="bukaModalKaryawan('email', '{{ route('gaji.send-email', $gajiTerbaru) }}', '{{ $karyawan->nama }}')">Email</button>

                                        <button type="button" style="background-color: #25d366; color: white; border: none; padding: 4px 8px; border-radius: 3px; font-size: 11px; cursor: pointer; font-weight: 600;" onclick="bukaModalKaryawan('wa', '{{ route('gaji.send-wa', $gajiTerbaru) }}', '{{ $karyawan->nama }}')">WA</button>
                                    @endif

                                    <form action="{{ route('karyawan.destroy', $karyawan) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus karyawan ini?');" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background-color: #dc3545; color: white; border: none; padding: 4px 8px; border-radius: 3px; font-size: 11px; cursor: pointer; font-weight: 600;">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

    </div>

    <!-- MODAL POP-UP PILIH PERIODE -->
    <div id="modalPilihPeriode" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
        <div style="background: #fff; padding: 24px; border-radius: 8px; width: 100%; max-width: 400px; box-shadow: 0 4px 15px rgba(0,0,0,0.25);">
            <h3 style="margin-top: 0; margin-bottom: 6px; font-size: 18px; color: #2e4600; text-align: center;">📅 Pilih Periode Gaji</h3>
            <p style="font-size: 13px; color: #666; margin-bottom: 18px; text-align: center;">Setting bulan & tahun penggajian (Standar Tgl 25 - 25)</p>
            
            <div style="margin-bottom: 14px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 13px;">Bulan Awal Gaji</label>
                <select id="selectBulan" style="width: 100%; padding: 10px; border: 1px solid #ff781f; border-radius: 4px; font-size: 14px; background: #fff; outline: none;">
                    <option value="11">November</option>
                    <option value="12">Desember</option>
                    <option value="1">Januari</option>
                    <option value="2">Februari</option>
                    <option value="3">Maret</option>
                    <option value="4">April</option>
                    <option value="5">Mei</option>
                    <option value="6">Juni</option>
                    <option value="7">Juli</option>
                    <option value="8">Agustus</option>
                    <option value="9">September</option>
                    <option value="10">Oktober</option>
                </select>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 13px;">Tahun</label>
                <input type="number" id="inputTahun" value="2026" min="2020" max="2035" style="width: 100%; padding: 10px; border: 1px solid #ff781f; border-radius: 4px; box-sizing: border-box; font-size: 14px; outline: none;">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 8px;">
                <button type="button" onclick="tutupModalPeriode()" style="padding: 9px 16px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 13px;">Batal</button>
                <button type="button" onclick="lanjutKeFormTambah()" style="padding: 9px 18px; background: #61885c; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 13px;">Lanjutkan &rarr;</button>
            </div>
        </div>
    </div>

    <!-- MODAL POP-UP EMAIL / WA -->
    <div id="modalKirimKaryawan" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
        <div style="background: #fff; padding: 24px; border-radius: 8px; width: 100%; max-width: 380px; box-shadow: 0 4px 12px rgba(0,0,0,0.25);">
            <h3 id="modalTitleKaryawan" style="margin-top: 0; margin-bottom: 8px; font-size: 18px; color: #2e4600;">Kirim Slip Gaji</h3>
            <p style="font-size: 13px; color: #666; margin-bottom: 16px;">Silakan masukkan tujuan pengiriman.</p>
            
            <form id="modalFormKaryawan" method="POST" action="">
                @csrf
                <div style="margin-bottom: 16px;">
                    <label id="modalLabelKaryawan" for="modalInputKaryawan" style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 13px;">Tujuan</label>
                    <input type="text" id="modalInputKaryawan" name="" required style="width: 100%; padding: 10px; border: 1px solid #ff781f; border-radius: 4px; box-sizing: border-box; font-size: 14px; outline: none;">
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 8px;">
                    <button type="button" onclick="tutupModalKaryawan()" style="padding: 8px 14px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 13px;">Batal</button>
                    <button type="submit" id="modalBtnKaryawan" style="padding: 8px 14px; background: #61885c; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 13px;">Kirim</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function bukaModalPeriode() {
            document.getElementById('modalPilihPeriode').style.display = 'flex';
        }

        function tutupModalPeriode() {
            document.getElementById('modalPilihPeriode').style.display = 'none';
        }

        function lanjutKeFormTambah() {
            const bulanAwal = parseInt(document.getElementById('selectBulan').value);
            const tahunAwal = parseInt(document.getElementById('inputTahun').value);

            let bulanAkhir = bulanAwal + 1;
            let tahunAkhir = tahunAwal;

            if (bulanAkhir > 12) {
                bulanAkhir = 1;
                tahunAkhir += 1;
            }

            const strBulanAwal  = String(bulanAwal).padStart(2, '0');
            const strBulanAkhir = String(bulanAkhir).padStart(2, '0');

            const tglAwal  = `${tahunAwal}-${strBulanAwal}-25`;
            const tglAkhir = `${tahunAkhir}-${strBulanAkhir}-25`;

            window.location.href = `{{ route('karyawan.create') }}?periode_awal=${tglAwal}&periode_akhir=${tglAkhir}`;
        }

        function bukaModalKaryawan(tipe, urlAction, namaKaryawan) {
            const modal = document.getElementById('modalKirimKaryawan');
            const form = document.getElementById('modalFormKaryawan');
            const title = document.getElementById('modalTitleKaryawan');
            const label = document.getElementById('modalLabelKaryawan');
            const input = document.getElementById('modalInputKaryawan');
            const btn = document.getElementById('modalBtnKaryawan');

            form.action = urlAction;
            input.value = '';

            if (tipe === 'email') {
                title.innerText = 'Kirim Slip Gaji via Email';
                label.innerText = 'Email Tujuan (' + namaKaryawan + '):';
                input.name = 'target_email';
                input.type = 'email';
                input.placeholder = 'Ketik email tujuan...';
                btn.style.backgroundColor = '#ffc107';
                btn.style.color = '#000';
            } else {
                title.innerText = 'Kirim Slip Gaji via WhatsApp';
                label.innerText = 'No. WA Tujuan (' + namaKaryawan + '):';
                input.name = 'target_wa';
                input.type = 'text';
                input.placeholder = 'Contoh: 0895029355';
                btn.style.backgroundColor = '#25d366';
                btn.style.color = '#fff';
            }

            modal.style.display = 'flex';
            input.focus();
        }

        function tutupModalKaryawan() {
            document.getElementById('modalKirimKaryawan').style.display = 'none';
        }
    </script>
@endsection