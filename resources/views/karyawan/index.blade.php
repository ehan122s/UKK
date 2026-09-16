@extends('layouts.app')

@section('title', 'Data Karyawan')

@section('content')
    <div style="width: 100%; background: #ffffff; padding: 24px; border-radius: 8px; box-shadow: 0 4px 16px rgba(0,0,0,0.06); border: 1px solid #dcdcdc; box-sizing: border-box;">

        <!-- HEADER -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid #c5d99b; padding-bottom: 14px; flex-wrap: wrap; gap: 12px;">
            <div>
                <h2 style="margin: 0; color: #2e4600; font-size: 20px; font-weight: bold; text-transform: uppercase;">Data Karyawan</h2>
                <p style="margin: 4px 0 0 0; font-size: 13px; color: #555;">Kelola data master karyawan dan transaksi penggajian</p>
            </div>
            <!-- Tombol Tambah Karyawan Baru Utama -->
            <button type="button" onclick="bukaModalPeriode(null)" style="background-color: #61885c; color: white; padding: 10px 18px; border: none; border-radius: 4px; font-weight: 600; cursor: pointer; font-size: 14px;">
                + Tambah Karyawan Baru
            </button>
        </div>

        @if (session('status'))
            <div style="background-color: #c5d99b; color: #2e4600; padding: 12px 16px; border-radius: 4px; margin-bottom: 20px; font-weight: 600; font-size: 14px; border: 1px solid #b3cb86;">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div style="background-color: #f8d7da; color: #842029; padding: 12px 16px; border-radius: 4px; margin-bottom: 20px; font-size: 14px; border: 1px solid #f5c2c7;">
                {{ $errors->first() }}
            </div>
        @endif

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

        <!-- TABEL DATA KARYAWAN -->
        <div style="width: 100%; overflow-x: auto;">
            @if ($karyawans->isEmpty())
                <p style="padding: 20px; text-align: center; color: #777;">Data karyawan tidak ditemukan.</p>
            @else
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <thead>
                        <tr style="background-color: #c5d99b; color: #2e4600; text-align: left; font-weight: bold;">
                            <th style="padding: 12px 10px; width: 12%;">NIK</th>
                            <th style="padding: 12px 10px; width: 33%;">NAMA & KONTAK KARYAWAN</th>
                            <th style="padding: 12px 10px; width: 15%;">JABATAN</th>
                            <th style="padding: 12px 10px; width: 18%;">GAJI POKOK ACUAN</th>
                            <th style="padding: 12px 10px; width: 12%;">RIWAYAT SLIP</th>
                            <th style="padding: 12px 10px; text-align: center; width: 10%;">AKSI & PENGGAJIAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($karyawans as $karyawan)
                            @php
                                $gajiTerbaru = $karyawan->gajis->last();
                                $gajiPokok   = $gajiTerbaru->gaji_pokok ?? $karyawan->gaji_pokok ?? 0;
                                $totalSlip   = $karyawan->gajis ? $karyawan->gajis->count() : 0;
                            @endphp
                            <tr style="border-bottom: 1px solid #e6eee0;">
                                <!-- NIK -->
                                <td style="padding: 14px 10px; font-weight: bold; color: #2e4600; vertical-align: middle;">
                                    {{ $karyawan->nik }}
                                </td>

                                <!-- NAMA & KONTAK -->
                                <td style="padding: 14px 10px; vertical-align: middle;">
                                    <div style="font-weight: bold; font-size: 14px; color: #111; margin-bottom: 3px;">
                                        {{ $karyawan->nama }}
                                    </div>
                                    <div style="font-size: 12px; color: #555; display: flex; flex-direction: column; gap: 2px;">
                                        @if(!empty($karyawan->email))
                                            <span>✉ {{ $karyawan->email }}</span>
                                        @endif
                                        @if(!empty($karyawan->no_hp) || !empty($karyawan->telepon))
                                            <span>💬 {{ $karyawan->no_hp ?? $karyawan->telepon }}</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- JABATAN BADGE -->
                                <td style="padding: 14px 10px; vertical-align: middle;">
                                    <span style="background-color: #f9fbf8; color: #2e4600; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 600; border: 1px solid #c5d99b; display: inline-block;">
                                        {{ $karyawan->jabatan }}
                                    </span>
                                </td>

                                <!-- GAJI POKOK ACUAN -->
                                <td style="padding: 14px 10px; font-weight: bold; color: #2e4600; font-size: 14px; vertical-align: middle;">
                                    Rp {{ number_format($gajiPokok, 0, ',', '.') }}
                                </td>

                                <!-- RIWAYAT SLIP -->
                                <td style="padding: 14px 10px; color: #444; font-size: 12px; vertical-align: middle;">
                                    <div style="display: flex; align-items: center; gap: 4px; font-weight: 600;">
                                        📋 {{ $totalSlip }} Slip Tersimpan
                                    </div>
                                </td>

                                <!-- AKSI & PENGGAJIAN -->
                                <td style="padding: 14px 10px; text-align: center; vertical-align: middle; white-space: nowrap;">
                                    <div style="display: flex; justify-content: center; gap: 6px;">
                                        <!-- Tombol + Tambah Slip Gaji untuk Karyawan Ini -->
                                        <button type="button" onclick="bukaModalPeriode({{ $karyawan->id }})" title="Tambah Slip Gaji untuk {{ $karyawan->nama }}" style="width: 32px; height: 32px; background-color: #61885c; color: white; border: none; border-radius: 4px; font-weight: bold; font-size: 16px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                            +
                                        </button>

                                        <!-- Tombol Edit Karyawan -->
                                        <a href="{{ route('karyawan.edit', $karyawan) }}" title="Edit Data Karyawan" style="width: 32px; height: 32px; background-color: #4b89dc; color: white; border-radius: 4px; font-size: 14px; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                                            ✏️
                                        </a>

                                        <!-- Tombol Hapus Karyawan -->
                                        <form action="{{ route('karyawan.destroy', $karyawan) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus karyawan ini beserta seluruh riwayat gajinya?');" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus Karyawan" style="width: 32px; height: 32px; background-color: #dc3545; color: white; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
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
            
            <!-- Hidden Input untuk Menyimpan ID Karyawan Jika Memilih Tambah Slip Per Orang -->
            <input type="hidden" id="selectedKaryawanId" value="">

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
                <button type="button" onclick="lanjutKeForm()" style="padding: 9px 18px; background: #61885c; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 13px;">Lanjutkan &rarr;</button>
            </div>
        </div>
    </div>

    <script>
        function bukaModalPeriode(karyawanId = null) {
            document.getElementById('selectedKaryawanId').value = karyawanId ? karyawanId : '';
            document.getElementById('modalPilihPeriode').style.display = 'flex';
        }

        function tutupModalPeriode() {
            document.getElementById('modalPilihPeriode').style.display = 'none';
        }

        function lanjutKeForm() {
            const karyawanId = document.getElementById('selectedKaryawanId').value;
            const bulanAwal  = parseInt(document.getElementById('selectBulan').value);
            const tahunAwal  = parseInt(document.getElementById('inputTahun').value);

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

            // Jika karyawanId ada, berarti dari tombol '+' (Input Slip Gaji Karyawan Tersebut)
            if (karyawanId) {
                window.location.href = `{{ route('gaji.create') }}?karyawan_id=${karyawanId}&periode_awal=${tglAwal}&periode_akhir=${tglAkhir}`;
            } else {
                // Jika null, berarti dari tombol '+ Tambah Karyawan Baru'
                window.location.href = `{{ route('karyawan.create') }}?periode_awal=${tglAwal}&periode_akhir=${tglAkhir}`;
            }
        }
    </script>
@endsection