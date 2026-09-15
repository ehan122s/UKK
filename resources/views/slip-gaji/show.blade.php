@extends('layouts.app')

@section('title', 'Slip Gaji Karyawan')

@section('content')
    <div class="card">
        <p style="text-align:center; font-size:12px; color:var(--muted); margin:-4px 0 18px;">
            Periode {{ $gaji->periode_awal->translatedFormat('d M Y') }}
            – {{ $gaji->periode_akhir->translatedFormat('d M Y') }}
        </p>

        <ul class="info-list">
            <li><span>Nama</span><span>: {{ $gaji->karyawan->nama }}</span></li>
            <li><span>NIK</span><span>: {{ $gaji->karyawan->nik }}</span></li>
            <li><span>Jabatan</span><span>: {{ $gaji->karyawan->jabatan }}</span></li>
        </ul>

        <div class="ledger-block">
            <div class="band-header"><span>Penghasilan</span><span>Potongan</span></div>
        </div>
        <table class="ledger">
            <tr>
                <td style="width:50%;">Gaji Pokok: Rp {{ number_format($gaji->gaji_pokok, 0, ',', '.') }}</td>
                <td style="width:50%;">Pinjaman Karyawan: Rp {{ number_format($gaji->pinjaman_karyawan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Lembur: Rp {{ number_format($gaji->lembur, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </table>

        <div class="ledger-block">
            <div class="band-header"><span>Total Penghasilan</span><span>Total Potongan</span></div>
        </div>
        <table class="ledger">
            <tr>
                <td style="width:50%;">Rp {{ number_format($gaji->total_penghasilan, 0, ',', '.') }}</td>
                <td style="width:50%;">Rp {{ number_format($gaji->total_potongan, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="net-row">
            <span>Gaji Bersih</span>
            <span>Rp {{ number_format($gaji->gaji_bersih, 0, ',', '.') }}</span>
        </div>

        @if (! $verified)
            {{-- Gerbang captcha berhitung: harus dijawab benar dulu sebelum PDF/Email/WA aktif --}}
            <form method="POST" action="{{ route('gaji.slip.verify', $gaji) }}">
                @csrf
                @error('jawaban')
                    <div class="alert alert-error">{{ $message }}</div>
                @enderror

                <div class="captcha-row">
                    <div>
                        <label style="display:block;font-size:11px;color:var(--muted);margin-bottom:4px;">Captcha</label>
                        <div style="display:flex;gap:8px;align-items:center;">
                            <span class="captcha-code" id="captchaQuestion">{{ $question }}</span>
                            <span class="captcha-refresh" id="captchaRefresh" title="Soal baru">&#x21bb;</span>
                        </div>
                    </div>
                    <div class="field">
                        <label for="jawaban">Jawaban</label>
                        <input id="jawaban" name="jawaban" type="number" placeholder="Hasil hitungan">
                    </div>
                </div>

                <div class="slip-submit">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>

            <script>
                document.getElementById('captchaRefresh')?.addEventListener('click', async () => {
                    const res = await fetch("{{ route('gaji.slip.refresh-captcha', $gaji) }}");
                    const data = await res.json();
                    document.getElementById('captchaQuestion').textContent = data.question;
                });
            </script>
        @else
            <div class="slip-actions-row">
                <a href="{{ route('gaji.slip.pdf', $gaji) }}" class="btn small btn-outline">Cetak PDF</a>

                <form method="POST" action="{{ route('gaji.slip.email', $gaji) }}">
                    @csrf
                    <button type="submit" class="btn small btn-outline">
                        Kirim Email {{ $gaji->email_sent_at ? '(sudah pernah)' : '' }}
                    </button>
                </form>

                <form method="POST" action="{{ route('gaji.slip.whatsapp', $gaji) }}">
                    @csrf
                    <button type="submit" class="btn small btn-outline">
                        Kirim WhatsApp {{ $gaji->whatsapp_sent_at ? '(sudah pernah)' : '' }}
                    </button>
                </form>
            </div>
        @endif
    </div>
@endsection
