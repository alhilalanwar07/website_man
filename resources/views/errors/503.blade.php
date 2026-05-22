<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sedang Pemeliharaan — MAN 2 Kolaka</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-slate-950 min-h-screen flex flex-col items-center justify-center p-6 relative overflow-hidden">

    {{-- Background blobs --}}
    <div class="absolute top-[-10%] left-[-5%] w-[28rem] h-[28rem] rounded-full bg-amber-600/10 blur-[110px] pointer-events-none"></div>
    <div class="absolute bottom-[-15%] right-[-10%] w-[30rem] h-[30rem] rounded-full bg-emerald-600/10 blur-[120px] pointer-events-none"></div>

    {{-- Card --}}
    <div class="relative z-10 w-full max-w-lg text-center">

        {{-- Badge --}}
        <span class="inline-flex px-4 py-1.5 rounded-full border border-white/10 bg-white/5 text-xs font-bold uppercase tracking-[0.3em] text-emerald-400 mb-8">
            MAN 2 Kolaka
        </span>

        {{-- Icon --}}
        <div class="w-20 h-20 rounded-3xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center mx-auto mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </div>

        {{-- Code --}}
        <div class="text-[7rem] sm:text-[9rem] font-black leading-none tracking-tighter text-transparent bg-clip-text bg-gradient-to-br from-amber-400 via-yellow-400 to-amber-600 select-none mb-4">
            503
        </div>

        {{-- Message --}}
        <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight mb-3">
            Website Sedang Pemeliharaan
        </h1>
        <p class="text-slate-400 text-base leading-relaxed max-w-sm mx-auto mb-10">
            Kami sedang melakukan pembaruan sistem untuk meningkatkan layanan. Mohon kembali beberapa saat lagi.
        </p>

        {{-- Status indicator --}}
        <div class="inline-flex items-center gap-3 px-5 py-3 rounded-2xl border border-white/10 bg-white/5 text-sm font-semibold text-slate-300 mb-10">
            <span class="relative flex h-2.5 w-2.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span>
            </span>
            Sistem sedang offline sementara
        </div>

        @if(!empty($exception) && $exception->getMessage())
        <div class="mb-8 px-5 py-3 rounded-2xl border border-white/10 bg-white/5 text-xs text-slate-500 font-mono text-left break-all">
            {{ $exception->getMessage() }}
        </div>
        @endif

        {{-- Divider --}}
        <div class="pt-8 border-t border-white/10">
            <p class="text-xs text-slate-500 font-medium">
                MAN 2 Kolaka &mdash; Madrasah Aliyah Negeri &copy; {{ date('Y') }}
            </p>
        </div>
    </div>

</body>
</html>
