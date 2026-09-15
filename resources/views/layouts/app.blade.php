<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Slip Gaji Karyawan')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <nav class="topnav">
        <span class="brand">Slip Gaji Karyawan</span>
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
        <a href="{{ route('karyawan.index') }}" class="{{ request()->routeIs('karyawan.*') ? 'active' : '' }}">Data Karyawan</a>
        <a href="{{ route('gaji.index') }}" class="{{ request()->routeIs('gaji.*') ? 'active' : '' }}">Data Gaji</a>
        @if (session('admin_id'))
            <span class="topbar-user">{{ \App\Models\User::find(session('admin_id'))->name ?? '' }}</span>
        @endif
        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
            @csrf
            <button type="submit" class="link">Keluar</button>
        </form>
    </nav>

    <main class="app-content">
        <h1 style="font-size:16px; text-transform:uppercase; letter-spacing:.03em; margin-bottom:18px;">@yield('title')</h1>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        @yield('content')
    </main>
</body>
</html>
