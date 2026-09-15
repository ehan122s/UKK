<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Slip Gaji Karyawan')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="guest-body">
    <div class="guest-wrap">
        @yield('content')
    </div>
</body>
</html>
