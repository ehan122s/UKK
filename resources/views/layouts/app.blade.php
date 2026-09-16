<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Slip Gaji Karyawan')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        html, body {
            width: 100%;
            height: 100%;
            overflow-x: hidden; /* Menghilangkan scrollbar horizontal di seluruh window */
            background-color: #f6f9f4;
            color: #2e4600;
        }

        /* WRAPPER FLEXBOX UTAMA */
        .app-wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* SIDEBAR FIXED DI KIRI */
        .sidebar {
            width: 250px;
            background-color: #61885c;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0,0,0,0.08);
        }

        .sidebar-brand {
            padding: 22px 16px;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: center;
            background-color: #4f734a;
            border-bottom: 2px solid #ff781f;
            color: #ffffff;
        }

        .sidebar-user {
            padding: 12px 20px;
            background-color: #557950;
            font-size: 13px;
            color: #e2f0c8;
            font-weight: 600;
            border-bottom: 1px solid rgba(255,255,255,0.15);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sidebar-menu {
            list-style: none;
            padding: 15px 0;
            margin: 0;
            flex-grow: 1;
        }

        .sidebar-menu li a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 24px;
            color: #f0f7e6;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .sidebar-menu li a:hover {
            background-color: #53774e;
            color: #ffffff;
        }

        .sidebar-menu li.active a {
            background-color: #c5d99b;
            color: #2e4600;
            border-left: 5px solid #ff781f;
            font-weight: bold;
        }

        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid rgba(255,255,255,0.2);
            background-color: #4f734a;
        }

        .sidebar-footer button {
            width: 100%;
            padding: 9px;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            font-size: 13px;
        }

        .sidebar-footer button:hover {
            background-color: #bb2d3b;
        }

        /* AREA KONTEN UTAMA (DIPENDEKKAN DENGAN MARGIN-LEFT TERUKUR DARI SIDEBAR) */
        .app-content {
            margin-left: 250px !important;
            width: calc(100% - 250px) !important;
            max-width: calc(100% - 250px) !important;
            padding: 24px 28px !important;
            box-sizing: border-box !important;
            min-height: 100vh;
        }
    </style>
</head>
<body>

    <div class="app-wrapper">

        <!-- SIDEBAR SAMPING KIRI -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                📋 Slip Gaji Karyawan
            </div>

            @if (session('admin_id'))
                <div class="sidebar-user">
                    <span>👤</span> {{ \App\Models\User::find(session('admin_id'))->name ?? 'Admin' }}
                </div>
            @endif

            <ul class="sidebar-menu">
                <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}">
                        <span>📊</span> Dashboard
                    </a>
                </li>
                <li class="{{ request()->routeIs('karyawan.*') ? 'active' : '' }}">
                    <a href="{{ route('karyawan.index') }}">
                        <span>👥</span> Data Karyawan
                    </a>
                </li>
                <li class="{{ request()->routeIs('gaji.*') ? 'active' : '' }}">
                    <a href="{{ route('gaji.index') }}">
                        <span>💰</span> Data Gaji
                    </a>
                </li>
            </ul>

            <div class="sidebar-footer">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">🚪 Keluar</button>
                </form>
            </div>
        </aside>

        <!-- KONTEN UTAMA (KANAN) -->
        <main class="app-content">
            @if (session('status'))
                <div style="background-color: #c5d99b; color: #2e4600; border: 1px solid #b3cb86; padding: 12px; border-radius: 6px; margin-bottom: 16px; font-weight: 600;">
                    {{ session('status') }}
                </div>
            @endif
            @if ($errors->any())
                <div style="background-color: #f8d7da; color: #842029; border: 1px solid #f5c2c7; padding: 12px; border-radius: 6px; margin-bottom: 16px;">
                    {{ $errors->first() }}
                </div>
            @endif

            @yield('content')
        </main>

    </div>

</body>
</html>