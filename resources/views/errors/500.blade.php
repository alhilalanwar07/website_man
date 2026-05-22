<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terjadi Kesalahan — MAN 2 Kolaka</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-slate-950 min-h-screen flex flex-col items-center justify-center p-6 relative overflow-hidden">

    {{-- Background blobs --}}
    <div class="absolute top-[-10%] left-[-5%] w-[28rem] h-[28rem] rounded-full bg-rose-600/10 blur-[110px] pointer-events-none"></div>
    <div class="absolute bottom-[-15%] right-[-10%] w-[30rem] h-[30rem] rounded-full bg-orange-600/10 blur-[120px] pointer-events-none"></div>

    {{-- Card --}}
    <div class="relative z-10 w-full max-w-lg text-center">

        {{-- Badge --}}
        <span class="inline-flex px-4 py-1.5 rounded-full border border-white/10 bg-white/5 text-xs font-bold uppercase tracking-[0.3em] text-emerald-400 mb-8">
            MAN 2 Kolaka
        </span>

        {{-- Icon --}}
        <div class="w-20 h-20 rounded-3xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center mx-auto mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>

        {{-- Code --}}
        <div class="text-[7rem] sm:text-[9rem] font-black leading-none tracking-tighter text-transparent bg-clip-text bg-gradient-to-br from-rose-400 via-orange-400 to-rose-600 select-none mb-4">
            500
        </div>

        {{-- Message --}}
        <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight mb-3">
            Terjadi Kesalahan Sistem
        </h1>
        <p class="text-slate-400 text-base leading-relaxed max-w-sm mx-auto mb-10">
            Server kami mengalami kendala saat memproses permintaan ini. Tim teknis sudah diberitahu dan sedang menanganinya.
        </p>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="/" class="w-full sm:w-auto px-7 py-3.5 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold rounded-2xl transition-all shadow-lg shadow-emerald-600/25 hover:-translate-y-0.5">
                Kembali ke Beranda
            </a>
            <button onclick="window.location.reload()" class="w-full sm:w-auto px-7 py-3.5 border border-white/10 bg-white/5 hover:bg-white/10 text-white text-sm font-bold rounded-2xl transition-all cursor-pointer">
                Coba Muat Ulang
            </button>
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
