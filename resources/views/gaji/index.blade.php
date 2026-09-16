@extends('layouts.app')

@section('title', 'Data Gaji')

@section('content')
    <div style="width: 100%; background: #ffffff; padding: 24px; border-radius: 8px; box-shadow: 0 4px 16px rgba(0,0,0,0.06); border: 1px solid #dcdcdc; box-sizing: border-box;">

        <!-- HEADER DATA GAJI -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid #c5d99b; padding-bottom: 14px; flex-wrap: wrap; gap: 12px;">
            <div>
                <h2 style="margin: 0; color: #2e4600; font-size: 20px; font-weight: bold; text-transform: uppercase;">Data Gaji Karyawan</h2>
                <p style="margin: 4px 0 0 0; font-size: 13px; color: #555;">Riwayat transaksi dan status pengiriman slip gaji karyawan</p>
            </div>
            <a href="{{ route('gaji.create') }}" style="background-color: #61885c; color: white; padding: 10px 18px; border-radius: 4px; font-weight: 600; text-decoration: none; font-size: 14px; display: inline-block;">
                + Input Gaji
            </a>
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
            <form action="{{ route('gaji.index') }}" method="GET" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama karyawan atau NIK..." style="padding: 8px 12px; border: 1px solid #ff781f; border-radius: 4px; width: 260px; font-size: 13px; outline: none;">

                <button type="submit" style="padding: 8px 18px; background-color: #61885c; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 13px;">Cari</button>
                
                @if(request('search'))
                    <a href="{{ route('gaji.index') }}" style="padding: 8px 12px; background-color: #dc3545; color: white; border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: 600;">Reset</a>
                @endif
            </form>
        </div>

        <!-- TABEL DATA GAJI -->
        <div style="width: 100%; overflow-x: auto;">
            @if ($gajis->isEmpty())
                <p style="padding: 20px; text-align: center; color: #777;">Data riwayat gaji tidak ditemukan.</p>
            @else
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <thead>
                        <tr style="background-color: #c5d99b; color: #2e4600; text-align: left; font-weight: bold;">
                            <th style="padding: 10px;">NAMA KARYAWAN</th>
                            <th style="padding: 10px;">PERIODE GAJI</th>
                            <th style="padding: 10px;">GAJI POKOK</th>
                            <th style="padding: 10px;">LEMBUR</th>
                            <th style="padding: 10px;">PINJAMAN</th>
                            <th style="padding: 10px;">GAJI BERSIH</th>
                            <th style="padding: 10px; text-align: center;">STATUS EMAIL</th>
                            <th style="padding: 10px; text-align: center;">STATUS WA</th>
                            <th style="padding: 10px; text-align: center;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($gajis as $gaji)
                            @php
                                $gajiPokok  = $gaji->gaji_pokok ?? 0;
                                $lembur     = $gaji->lembur ?? 0;
                                $pinjaman   = $gaji->pinjaman_karyawan ?? 0;
                                $gajiBersih = $gaji->gaji_bersih ?? ($gajiPokok + $lembur - $pinjaman);
                            @endphp
                            <tr style="border-bottom: 1px solid #e6eee0;">
                                <td style="padding: 10px; font-weight: bold; color: #222; white-space: nowrap;">
                                    {{ $gaji->karyawan->nama ?? '-' }}
                                    <div style="font-size: 11px; color: #666; font-weight: normal;">NIK: {{ $gaji->karyawan->nik ?? '-' }}</div>
                                </td>
                                <td style="padding: 10px; font-size: 12px; white-space: nowrap;">
                                    @if($gaji->periode_awal && $gaji->periode_akhir)
                                        {{ \Carbon\Carbon::parse($gaji->periode_awal)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($gaji->periode_akhir)->translatedFormat('d M Y') }}
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
                                    @if($gaji->email_sent_at)
                                        <span style="background-color: #c5d99b; color: #2e4600; padding: 3px 8px; border-radius: 10px; font-size: 10px; font-weight: bold;">✔ Terkirim</span>
                                    @else
                                        <span style="color: #999; font-size: 11px;">Belum</span>
                                    @endif
                                </td>
                                <td style="padding: 10px; text-align: center; white-space: nowrap;">
                                    @if($gaji->whatsapp_sent_at)
                                        <span style="background-color: #c5d99b; color: #2e4600; padding: 3px 8px; border-radius: 10px; font-size: 10px; font-weight: bold;">✔ Terkirim</span>
                                    @else
                                        <span style="color: #999; font-size: 11px;">Belum</span>
                                    @endif
                                </td>
                                <td style="padding: 10px; text-align: center; white-space: nowrap;">
                                    <a href="{{ route('gaji.edit', $gaji) }}" style="background-color: #4b89dc; color: white; padding: 4px 8px; border-radius: 3px; text-decoration: none; font-size: 11px; font-weight: 600; margin-right: 2px;">Edit</a>

                                    <a href="{{ route('gaji.pdf', $gaji) }}" style="background-color: #61885c; color: white; padding: 4px 8px; border-radius: 3px; text-decoration: none; font-size: 11px; font-weight: 600; margin-right: 2px;">PDF</a>

                                    <button type="button" style="background-color: #ffc107; color: #000; border: none; padding: 4px 8px; border-radius: 3px; font-size: 11px; cursor: pointer; font-weight: 600; margin-right: 2px;" onclick="bukaModalGaji('email', '{{ route('gaji.send-email', $gaji) }}', '{{ $gaji->karyawan->nama ?? '' }}')">Email</button>

                                    <button type="button" style="background-color: #25d366; color: white; border: none; padding: 4px 8px; border-radius: 3px; font-size: 11px; cursor: pointer; font-weight: 600; margin-right: 2px;" onclick="bukaModalGaji('wa', '{{ route('gaji.send-wa', $gaji) }}', '{{ $gaji->karyawan->nama ?? '' }}')">WA</button>

                                    <form action="{{ route('gaji.destroy', $gaji) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan gaji ini?');" style="display:inline;">
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

    <!-- MODAL POP-UP EMAIL / WA -->
    <div id="modalKirimGaji" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
        <div style="background: #fff; padding: 24px; border-radius: 8px; width: 100%; max-width: 380px; box-shadow: 0 4px 12px rgba(0,0,0,0.25);">
            <h3 id="modalTitleGaji" style="margin-top: 0; margin-bottom: 8px; font-size: 18px; color: #2e4600;">Kirim Slip Gaji</h3>
            <p style="font-size: 13px; color: #666; margin-bottom: 16px;">Silakan masukkan tujuan pengiriman.</p>
            
            <form id="modalFormGaji" method="POST" action="">
                @csrf
                <div style="margin-bottom: 16px;">
                    <label id="modalLabelGaji" for="modalInputGaji" style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 13px;">Tujuan</label>
                    <input type="text" id="modalInputGaji" name="" required style="width: 100%; padding: 10px; border: 1px solid #ff781f; border-radius: 4px; box-sizing: border-box; font-size: 14px; outline: none;">
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 8px;">
                    <button type="button" onclick="tutupModalGaji()" style="padding: 8px 14px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 13px;">Batal</button>
                    <button type="submit" id="modalBtnGaji" style="padding: 8px 14px; background: #61885c; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 13px;">Kirim</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function bukaModalGaji(tipe, urlAction, namaKaryawan) {
            const modal = document.getElementById('modalKirimGaji');
            const form  = document.getElementById('modalFormGaji');
            const title = document.getElementById('modalTitleGaji');
            const label = document.getElementById('modalLabelGaji');
            const input = document.getElementById('modalInputGaji');
            const btn   = document.getElementById('modalBtnGaji');

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

        function tutupModalGaji() {
            document.getElementById('modalKirimGaji').style.display = 'none';
        }
    </script>
@endsection