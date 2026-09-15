@extends('layouts.app')

@section('title', 'Data Karyawan')

@section('content')
    <div class="page-header-row" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h2>Data Karyawan</h2>
            <p class="page-intro">Kelola data karyawan dan slip gaji</p>
        </div>
        <a href="{{ route('karyawan.create') }}" class="btn" style="background-color: #198754; color: white; padding: 10px 16px; text-decoration: none; border-radius: 6px; font-weight: 600;">+ Tambah Karyawan</a>
    </div>

    {{-- Alert Pesan Sukses --}}
    @if (session('status'))
        <div style="background-color: #d1e7dd; color: #0f5132; padding: 12px; border-radius: 6px; margin-bottom: 16px;">
            {{ session('status') }}
        </div>
    @endif

    {{-- Alert Pesan Error --}}
    @if ($errors->any())
        <div style="background-color: #f8d7da; color: #842029; padding: 12px; border-radius: 6px; margin-bottom: 16px;">
            {{ $errors->first() }}
        </div>
    @endif

    <div style="margin-bottom: 16px;">
        <form action="{{ route('karyawan.index') }}" method="GET" style="display: flex; gap: 8px;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau NIK..." style="padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; width: 250px;">
            <button type="submit" class="btn small" style="padding: 8px 16px; background-color: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">Cari</button>
        </form>
    </div>

    <div class="card" style="overflow-x: auto;">
        @if ($karyawans->isEmpty())
            <p style="padding: 16px;">Belum ada data karyawan.</p>
        @else
            <table class="data-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #dee2e6; text-align: left;">
                        <th style="padding: 12px;">NIK</th>
                        <th style="padding: 12px;">Nama</th>
                        <th style="padding: 12px;">Jabatan</th>
                        <th style="padding: 12px;">Gaji Pokok</th>
                        <th style="padding: 12px;">Lembur</th>
                        <th style="padding: 12px;">Pinjaman</th>
                        <th style="padding: 12px;">Gaji Bersih</th>
                        <th style="padding: 12px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($karyawans as $karyawan)
                        @php
                            $gajiTerbaru = $karyawan->gajis->last();
                        @endphp
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px;">{{ $karyawan->nik }}</td>
                            <td style="padding: 12px; font-weight: 600;">{{ $karyawan->nama }}</td>
                            <td style="padding: 12px;">{{ $karyawan->jabatan }}</td>
                            <td style="padding: 12px;">Rp {{ number_format($karyawan->gaji_pokok ?? 0, 0, ',', '.') }}</td>
                            <td style="padding: 12px;">Rp {{ number_format($karyawan->lembur ?? 0, 0, ',', '.') }}</td>
                            <td style="padding: 12px;">Rp {{ number_format($karyawan->pinjaman_karyawan ?? 0, 0, ',', '.') }}</td>
                            <td style="padding: 12px; color: #0d6efd; font-weight: bold;">
                                Rp {{ number_format(($karyawan->gaji_pokok ?? 0) + ($karyawan->lembur ?? 0) - ($karyawan->pinjaman_karyawan ?? 0), 0, ',', '.') }}
                            </td>
                            <td style="padding: 12px; text-align: center; white-space: nowrap;">
                                <a href="{{ route('karyawan.edit', $karyawan) }}" class="btn small" style="background-color: #0d6efd; color: white; padding: 4px 8px; border-radius: 4px; text-decoration: none; font-size: 12px;">Edit</a>

                                @if ($gajiTerbaru)
                                    <a href="{{ route('gaji.pdf', $gajiTerbaru) }}" class="btn small" style="background-color: #198754; color: white; padding: 4px 8px; border-radius: 4px; text-decoration: none; font-size: 12px;">PDF</a>

                                    <button type="button" class="btn small" style="background-color: #ffc107; color: #000; border: none; padding: 4px 8px; border-radius: 4px; font-size: 12px; cursor: pointer;" onclick="bukaModalKirim('email', '{{ route('gaji.send-email', $gajiTerbaru) }}', '{{ $karyawan->nama }}')">Email</button>

                                    <button type="button" class="btn small" style="background-color: #25d366; color: white; border: none; padding: 4px 8px; border-radius: 4px; font-size: 12px; cursor: pointer;" onclick="bukaModalKirim('wa', '{{ route('gaji.send-wa', $gajiTerbaru) }}', '{{ $karyawan->nama }}')">WA</button>
                                @endif

                                <form action="{{ route('karyawan.destroy', $karyawan) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus karyawan ini?');" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn small" style="background-color: #dc3545; color: white; border: none; padding: 4px 8px; border-radius: 4px; font-size: 12px; cursor: pointer;">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- MODAL POP-UP --}}
    <div id="modalKirim" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
        <div style="background: #fff; padding: 24px; border-radius: 8px; width: 100%; max-width: 400px; box-shadow: 0 4px 10px rgba(0,0,0,0.3);">
            <h3 id="modalTitle" style="margin-top: 0; margin-bottom: 8px;">Kirim Slip Gaji</h3>
            <p id="modalSub" style="font-size: 13px; color: #666; margin-bottom: 16px;">Silakan masukkan tujuan pengiriman slip gaji.</p>
            
            <form id="modalForm" method="POST" action="">
                @csrf
                <div style="margin-bottom: 16px;">
                    <label id="modalLabel" for="modalInput" style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 14px;">Tujuan</label>
                    <input type="text" id="modalInput" name="" required placeholder="" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-size: 14px;">
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 8px;">
                    <button type="button" onclick="tutupModalKirim()" style="padding: 8px 14px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">Batal</button>
                    <button type="submit" id="modalSubmitBtn" style="padding: 8px 14px; background: #0d6efd; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">Kirim Sekarang</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function bukaModalKirim(tipe, urlAction, namaKaryawan) {
            const modal = document.getElementById('modalKirim');
            const form = document.getElementById('modalForm');
            const title = document.getElementById('modalTitle');
            const label = document.getElementById('modalLabel');
            const input = document.getElementById('modalInput');
            const btn = document.getElementById('modalSubmitBtn');

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

        function tutupModalKirim() {
            document.getElementById('modalKirim').style.display = 'none';
        }
    </script>
@endsection