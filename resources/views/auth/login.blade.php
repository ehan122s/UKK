@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <div class="login-card" style="max-width: 420px; margin: 40px auto; background: #ffffff; padding: 32px; border-radius: 8px; box-shadow: 0 4px 16px rgba(0,0,0,0.06); border: 1px solid #dcdcdc;">
        
        <!-- BRAND & HEADER -->
        <div class="login-brand" style="font-size: 20px; font-weight: bold; color: #2e4600; text-align: center; margin-bottom: 6px; letter-spacing: 0.5px;">
            📋 Slip Gaji Karyawan
        </div>
        <h2 style="font-size: 16px; font-weight: 600; color: #4f734a; text-align: center; margin: 0 0 4px 0; text-transform: uppercase;">Masuk Admin</h2>
        <p class="sub" style="font-size: 13px; color: #666; text-align: center; margin-bottom: 24px;">Silakan masukkan email dan password admin</p>

        @if ($errors->any())
            <div class="alert alert-error" style="background-color: #f8d7da; color: #842029; border: 1px solid #f5c2c7; padding: 10px 14px; border-radius: 4px; font-size: 13px; margin-bottom: 16px;">
                {{ $errors->first() }}
            </div>
        @endif
        @if (session('status'))
            <div class="alert alert-success" style="background-color: #c5d99b; color: #2e4600; border: 1px solid #b3cb86; padding: 10px 14px; border-radius: 4px; font-size: 13px; margin-bottom: 16px; font-weight: 600;">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.attempt') }}">
            @csrf

            <!-- EMAIL -->
            <div class="field" style="margin-bottom: 16px;">
                <label for="email" style="display: block; font-size: 13px; font-weight: 600; color: #2e4600; margin-bottom: 6px;">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required placeholder="nama@perusahaan.com" style="width: 100%; padding: 10px 12px; border: 1px solid #ff781f; border-radius: 4px; font-size: 13px; outline: none; box-sizing: border-box;">
            </div>

            <!-- PASSWORD -->
            <div class="field" style="margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <label for="password" style="margin: 0; font-size: 13px; font-weight: 600; color: #2e4600;">Sandi</label>
                    <a href="javascript:void(0)" onclick="toggleForgotPassword()" style="font-size: 12px; color: #61885c; text-decoration: none; font-weight: 600;">Lupa Password?</a>
                </div>
                <input id="password" type="password" name="password" required placeholder="••••••••" style="width: 100%; padding: 10px 12px; border: 1px solid #ff781f; border-radius: 4px; font-size: 13px; outline: none; box-sizing: border-box;">
            </div>

            <!-- TOMBOL SUBMIT -->
            <button type="submit" class="btn" style="width: 100%; padding: 10px; background-color: #61885c; color: white; border: none; border-radius: 4px; font-size: 14px; font-weight: 600; cursor: pointer; transition: background 0.2s;">
                Login
            </button>
        </form>

        <!-- BOX MODAL RESET PASSWORD (SATU TEMA SERAGAM) -->
        <div id="forgotPasswordBox" style="display: none; margin-top: 20px; padding: 16px; background: #f9fbf8; border: 1px solid #c5d99b; border-radius: 6px; text-align: left;">
            <h4 style="margin: 0 0 8px 0; font-size: 14px; color: #2e4600; font-weight: bold;">Lupa Password Akun?</h4>
            <div id="forgotMessage" style="display: none; font-size: 12px; padding: 8px 12px; margin-bottom: 12px; border-radius: 4px; font-weight: 600;"></div>

            <!-- STEP 1: Minta Kode Token -->
            <div id="step1Box">
                <p style="margin: 0 0 12px 0; font-size: 12px; color: #555;">Masukkan email terdaftar untuk menerima token reset via API Email.</p>
                <div class="field" style="margin-bottom: 12px;">
                    <input id="forgotEmail" type="email" placeholder="email@terdaftar.com" style="width: 100%; padding: 8px 10px; font-size: 13px; border: 1px solid #ff781f; border-radius: 4px; box-sizing: border-box; outline: none;">
                </div>
                <div style="display: flex; gap: 8px;">
                    <button type="button" onclick="sendForgotPasswordApi()" id="btnForgot" style="padding: 8px 14px; background: #61885c; color: white; border: none; border-radius: 4px; font-size: 12px; cursor: pointer; font-weight: 600;">Kirim Token</button>
                    <button type="button" onclick="toggleForgotPassword()" style="padding: 8px 14px; background: #6c757d; color: white; border: none; border-radius: 4px; font-size: 12px; cursor: pointer; font-weight: 600;">Batal</button>
                </div>
            </div>

            <!-- STEP 2: Input Kode Token & Password Baru -->
            <div id="step2Box" style="display: none;">
                <p style="margin: 0 0 12px 0; font-size: 12px; color: #555;">Masukkan kode token dari Gmail beserta password baru Anda.</p>
                
                <div class="field" style="margin-bottom: 8px;">
                    <input id="resetToken" type="text" placeholder="Masukkan 6 Digit Token" style="width: 100%; padding: 8px 10px; font-size: 13px; border: 1px solid #ff781f; border-radius: 4px; box-sizing: border-box; outline: none;">
                </div>
                <div class="field" style="margin-bottom: 8px;">
                    <input id="newPassword" type="password" placeholder="Password Baru" style="width: 100%; padding: 8px 10px; font-size: 13px; border: 1px solid #ff781f; border-radius: 4px; box-sizing: border-box; outline: none;">
                </div>
                <div class="field" style="margin-bottom: 12px;">
                    <input id="confirmPassword" type="password" placeholder="Konfirmasi Password Baru" style="width: 100%; padding: 8px 10px; font-size: 13px; border: 1px solid #ff781f; border-radius: 4px; box-sizing: border-box; outline: none;">
                </div>

                <div style="display: flex; gap: 8px;">
                    <button type="button" onclick="resetPasswordApi()" id="btnReset" style="padding: 8px 14px; background: #4b89dc; color: white; border: none; border-radius: 4px; font-size: 12px; cursor: pointer; font-weight: 600;">Simpan Password</button>
                    <button type="button" onclick="toggleForgotPassword()" style="padding: 8px 14px; background: #6c757d; color: white; border: none; border-radius: 4px; font-size: 12px; cursor: pointer; font-weight: 600;">Batal</button>
                </div>
            </div>
        </div>
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
                    msg.style.background = '#c5d99b';
                    msg.style.color = '#2e4600';
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
                    msg.style.background = '#c5d99b';
                    msg.style.color = '#2e4600';
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