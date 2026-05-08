<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sedang Perbaikan - SMKN 1 Kolaka</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900" rel="stylesheet" />
    
    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-slate-50 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-2xl w-full text-center relative z-10">
        {{-- Background Effects --}}
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[400px] h-[400px] bg-amber-400/20 rounded-full blur-[100px] -z-10"></div>
        
        <div class="bg-white rounded-3xl p-8 sm:p-12 shadow-2xl shadow-slate-200/50 border border-slate-100">
            <div class="w-24 h-24 bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-8 shadow-inner border border-amber-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            
            <h1 class="text-4xl font-black text-slate-900 mb-4 tracking-tight">Website Sedang Diperbarui</h1>
            <p class="text-lg text-slate-600 mb-8 max-w-lg mx-auto">Kami sedang melakukan perbaikan atau pembaruan sistem untuk meningkatkan layanan. Mohon kembali beberapa saat lagi.</p>
            
            <div class="inline-flex items-center gap-3 px-6 py-3 bg-slate-100 rounded-full text-slate-600 text-sm font-semibold">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                </span>
                Sistem Sedang Offline (Mode Perbaikan)
            </div>
        </div>
        
        <p class="text-sm text-slate-400 mt-8 font-medium">SMK Negeri 1 Kolaka &copy; {{ date('Y') }}</p>
    </div>
</body>
</html>
