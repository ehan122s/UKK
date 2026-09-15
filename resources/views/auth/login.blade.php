@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <div class="login-card">
        <div class="login-brand">Slip Gaji Karyawan</div>
        <h2>Masuk</h2>
        <p class="sub">Silahkan masukkan email dan password admin</p>

        @if ($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login.attempt') }}">
            @csrf

            <div class="field">
                <label for="email">Email</label>
                <input id="email" type="text" name="email" value="{{ old('email') }}" placeholder="nama@perusahaan.com">
            </div>

            <div class="field">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <label for="password" style="margin: 0;">Sandi</label>
                    <a href="javascript:void(0)" onclick="toggleForgotPassword()" style="font-size: 12px; color: #4a6d45; text-decoration: none; font-weight: 500;">Lupa Password?</a>
                </div>
                <input id="password" type="password" name="password" placeholder="••••••••">
            </div>

            <button type="submit" class="btn">Login</button>
        </form>

        <!-- Box Form Reset Password Modal -->
        <div id="forgotPasswordBox" style="display: none; margin-top: 20px; padding: 15px; background: #f8f9fa; border: 1px solid #e0e0e0; border-radius: 6px; text-align: left;">
            <h4 style="margin: 0 0 8px 0; font-size: 14px; color: #333;">Lupa Password Akun?</h4>
            <div id="forgotMessage" style="display: none; font-size: 12px; padding: 8px; margin-bottom: 10px; border-radius: 4px;"></div>

            <!-- STEP 1: Minta Kode Token -->
            <div id="step1Box">
                <p style="margin: 0 0 12px 0; font-size: 12px; color: #666;">Masukkan email terdaftar untuk menerima token reset via API Email.</p>
                <div class="field" style="margin-bottom: 10px;">
                    <input id="forgotEmail" type="email" placeholder="email@terdaftar.com" style="width: 100%; padding: 8px; font-size: 13px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                </div>
                <div style="display: flex; gap: 8px;">
                    <button type="button" onclick="sendForgotPasswordApi()" id="btnForgot" style="padding: 7px 12px; background: #4a6d45; color: white; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">Kirim Token</button>
                    <button type="button" onclick="toggleForgotPassword()" style="padding: 7px 12px; background: #6c757d; color: white; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">Batal</button>
                </div>
            </div>

            <!-- STEP 2: Input Kode Token & Password Baru (Sembunyi Dulu) -->
            <div id="step2Box" style="display: none;">
                <p style="margin: 0 0 12px 0; font-size: 12px; color: #666;">Masukkan kode token dari Gmail beserta password baru Anda.</p>
                
                <div class="field" style="margin-bottom: 8px;">
                    <input id="resetToken" type="text" placeholder="Masukkan 6 Digit Token" style="width: 100%; padding: 8px; font-size: 13px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                </div>
                <div class="field" style="margin-bottom: 8px;">
                    <input id="newPassword" type="password" placeholder="Password Baru" style="width: 100%; padding: 8px; font-size: 13px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                </div>
                <div class="field" style="margin-bottom: 10px;">
                    <input id="confirmPassword" type="password" placeholder="Konfirmasi Password Baru" style="width: 100%; padding: 8px; font-size: 13px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                </div>

                <div style="display: flex; gap: 8px;">
                    <button type="button" onclick="resetPasswordApi()" id="btnReset" style="padding: 7px 12px; background: #0d6efd; color: white; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">Simpan Password</button>
                    <button type="button" onclick="toggleForgotPassword()" style="padding: 7px 12px; background: #6c757d; color: white; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">Batal</button>
                </div>
            </div>
        </div>

        <p class="hint" style="margin-top: 20px;">Demo: admin@perusahaan.com / admin123</p>
    </div>

    <script>
        function toggleForgotPassword() {
            const box = document.getElementById('forgotPasswordBox');
            box.style.display = box.style.display === 'none' ? 'block' : 'none';
        }

        // STEP 1: Kirim Email Token
        function sendForgotPasswordApi() {
            const email = document.getElementById('forgotEmail').value;
            const btn = document.getElementById('btnForgot');
            const msg = document.getElementById('forgotMessage');

            if (!email) {
                msg.style.display = 'block';
                msg.style.background = '#f8d7da';
                msg.style.color = '#842029';
                msg.innerText = 'Silakan isi email terlebih dahulu.';
                return;
            }

            btn.disabled = true;
            btn.innerText = 'Sending...';

            fetch("{{ route('password.email') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ email: email })
            })
            .then(res => res.json())
            .then(data => {
                msg.style.display = 'block';
                if (data.status) {
                    msg.style.background = '#d1e7dd';
                    msg.style.color = '#0f5132';
                    msg.innerText = data.message;

                    // Switch dari Step 1 ke Step 2
                    document.getElementById('step1Box').style.display = 'none';
                    document.getElementById('step2Box').style.display = 'block';
                } else {
                    msg.style.background = '#f8d7da';
                    msg.style.color = '#842029';
                    msg.innerText = data.message || 'Gagal mengirim token.';
                }
            })
            .catch(err => {
                msg.style.display = 'block';
                msg.style.background = '#f8d7da';
                msg.style.color = '#842029';
                msg.innerText = 'Terjadi kesalahan sistem / API error.';
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerText = 'Kirim Token';
            });
        }

        // STEP 2: Ubah Password Baru
        function resetPasswordApi() {
            const email = document.getElementById('forgotEmail').value;
            const token = document.getElementById('resetToken').value;
            const password = document.getElementById('newPassword').value;
            const passwordConfirmation = document.getElementById('confirmPassword').value;

            const btn = document.getElementById('btnReset');
            const msg = document.getElementById('forgotMessage');

            if (!token || !password || !passwordConfirmation) {
                msg.style.display = 'block';
                msg.style.background = '#f8d7da';
                msg.style.color = '#842029';
                msg.innerText = 'Semua field wajib diisi.';
                return;
            }

            btn.disabled = true;
            btn.innerText = 'Memproses...';

            fetch("{{ route('password.update') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    email: email,
                    token: token,
                    password: password,
                    password_confirmation: passwordConfirmation
                })
            })
            .then(res => res.json())
            .then(data => {
                msg.style.display = 'block';
                if (data.status) {
                    msg.style.background = '#d1e7dd';
                    msg.style.color = '#0f5132';
                    msg.innerText = data.message;

                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    msg.style.background = '#f8d7da';
                    msg.style.color = '#842029';
                    msg.innerText = data.message || 'Gagal mengubah password.';
                }
            })
            .catch(err => {
                msg.style.display = 'block';
                msg.style.background = '#f8d7da';
                msg.style.color = '#842029';
                msg.innerText = 'Terjadi kesalahan sistem.';
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerText = 'Simpan Password';
            });
        }
    </script>
@endsection