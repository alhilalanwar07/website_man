@php
    $profil = \Illuminate\Support\Facades\Cache::remember(
        'site:profil:v1',
        now()->addMinutes(10),
        fn () => \App\Models\ProfilSekolah::first()
    );
    $namaSekolah = $profil->nama_sekolah ?? 'MAN 2 Kolaka';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth overflow-x-hidden">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Form Pendaftaran PPDB' }} — {{ $namaSekolah }}</title>
    <meta name="description" content="Formulir pendaftaran peserta didik baru {{ $namaSekolah }}. Isi data lengkap, pilih peminatan, dan unggah berkas secara online.">
    <meta name="robots" content="index,follow">

    @if($profil && $profil->favicon_path)
        <link rel="icon" href="{{ Storage::url($profil->favicon_path) }}">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="antialiased font-sans bg-slate-50 text-slate-900 selection:bg-emerald-500 selection:text-white min-h-screen flex flex-col overflow-x-hidden">
    {{-- Minimal Top Bar --}}
    <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-xl border-b border-slate-200/60">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                @if($profil && $profil->logo_path)
                    <img src="{{ Storage::url($profil->logo_path) }}" class="w-9 h-9 rounded-lg object-contain transition-transform group-hover:scale-110" alt="{{ $namaSekolah }}">
                @else
                    <div class="w-9 h-9 bg-gradient-to-br from-emerald-600 to-indigo-600 rounded-lg flex items-center justify-center text-white shadow-lg shadow-emerald-500/25">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                    </div>
                @endif
                <div>
                    <span class="block text-sm font-extrabold tracking-tight leading-none text-slate-900">MAN 2 KOLAKA</span>
                    <span class="text-[9px] font-semibold tracking-[0.2em] text-emerald-600 uppercase">PPDB Online</span>
                </div>
            </a>
            <div class="flex items-center gap-3">
                <a href="{{ route('ppdb.status') }}" class="text-xs font-semibold text-slate-500 hover:text-emerald-600 transition-colors hidden sm:inline-flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Cek Status
                </a>
                <a href="{{ route('ppdb.index') }}" class="text-xs font-semibold text-slate-500 hover:text-emerald-600 transition-colors inline-flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
                    Info PPDB
                </a>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="flex-1 overflow-x-hidden">
        {{ $slot }}
    </main>

    {{-- Minimal Footer --}}
    <footer class="border-t border-slate-200/60 bg-white py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 text-center">
            <p class="text-xs text-slate-400">&copy; {{ date('Y') }} {{ $namaSekolah }}. Seluruh data dilindungi dan dienkripsi.</p>
        </div>
    </footer>
</body>

</html>
