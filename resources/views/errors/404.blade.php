<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Halaman Tidak Ditemukan — MAN 2 Kolaka</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-slate-950 min-h-screen flex flex-col items-center justify-center p-6 relative overflow-hidden">

    {{-- Background blobs --}}
    <div class="absolute top-[-10%] left-[-5%] w-[28rem] h-[28rem] rounded-full bg-emerald-600/15 blur-[110px] pointer-events-none"></div>
    <div class="absolute bottom-[-15%] right-[-10%] w-[30rem] h-[30rem] rounded-full bg-teal-600/10 blur-[120px] pointer-events-none"></div>

    {{-- Card --}}
    <div class="relative z-10 w-full max-w-lg text-center">

        {{-- Badge --}}
        <span class="inline-flex px-4 py-1.5 rounded-full border border-white/10 bg-white/5 text-xs font-bold uppercase tracking-[0.3em] text-emerald-400 mb-8">
            MAN 2 Kolaka
        </span>

        {{-- Code --}}
        <div class="text-[7rem] sm:text-[9rem] font-black leading-none tracking-tighter text-transparent bg-clip-text bg-gradient-to-br from-emerald-400 via-teal-400 to-emerald-600 select-none mb-4">
            404
        </div>

        {{-- Message --}}
        <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight mb-3">
            Halaman Tidak Ditemukan
        </h1>
        <p class="text-slate-400 text-base leading-relaxed max-w-sm mx-auto mb-10">
            Halaman yang Anda cari mungkin sudah dipindahkan, dihapus, atau alamatnya salah ketik.
        </p>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="/" class="w-full sm:w-auto px-7 py-3.5 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold rounded-2xl transition-all shadow-lg shadow-emerald-600/25 hover:-translate-y-0.5">
                Kembali ke Beranda
            </a>
            <a href="javascript:history.back()" class="w-full sm:w-auto px-7 py-3.5 border border-white/10 bg-white/5 hover:bg-white/10 text-white text-sm font-bold rounded-2xl transition-all">
                Halaman Sebelumnya
            </a>
        </div>

        {{-- Divider --}}
        <div class="mt-12 pt-8 border-t border-white/10">
            <p class="text-xs text-slate-500 font-medium">
                MAN 2 Kolaka &mdash; Madrasah Aliyah Negeri &copy; {{ date('Y') }}
            </p>
        </div>
    </div>

</body>
</html>
