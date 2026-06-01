@php
    $profil = \Illuminate\Support\Facades\Cache::remember(
        'site:profil:v1',
        now()->addMinutes(10),
        fn () => \App\Models\ProfilSekolah::first()
    );
    $namaSekolah = $profil->nama_sekolah ?? 'MAN 2 Kolaka';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="overflow-x-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Login - ' . $namaSekolah }}</title>

    @if($profil && $profil->favicon_path)
    <link rel="icon" href="{{ Storage::url($profil->favicon_path) }}">
    @else
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        if (localStorage.getItem('darkMode') === 'true' || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    <style>

    </style>
</head>
<body class=" antialiased font-sans bg-slate-50 dark:bg-slate-950 min-h-screen flex items-center justify-center relative overflow-hidden overflow-x-hidden selection:bg-emerald-500 selection:text-white">
    {{ $slot }}
</body>
</html>
