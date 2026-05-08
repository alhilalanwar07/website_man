<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Error - SMKN 1 Kolaka</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900" rel="stylesheet" />
    
    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-slate-50 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-2xl w-full text-center relative z-10">
        {{-- Background Effects --}}
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[400px] h-[400px] bg-red-400/10 rounded-full blur-[100px] -z-10"></div>
        
        <div class="bg-white rounded-3xl p-8 sm:p-12 shadow-2xl shadow-slate-200/50 border border-slate-100">
            <div class="w-24 h-24 bg-red-50 rounded-2xl flex items-center justify-center mx-auto mb-8 shadow-inner border border-red-100 relative overflow-hidden">
                <div class="absolute inset-0 bg-red-500/10 animate-pulse"></div>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-red-500 relative z-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            
            <h1 class="text-4xl font-black text-slate-900 mb-4 tracking-tight">Oops! Terjadi Kesalahan</h1>
            <p class="text-lg text-slate-600 mb-8 max-w-lg mx-auto">Sistem kami saat ini mengalami kendala atau tidak dapat merespon permintaan Anda. Tim kami telah diberitahu dan sedang memperbaikinya.</p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="/" class="px-8 py-3 bg-slate-900 text-white font-bold rounded-xl hover:bg-slate-800 transition shadow-lg shadow-slate-900/20 w-full sm:w-auto">
                    Kembali ke Beranda
                </a>
                <button onclick="window.location.reload()" class="px-8 py-3 bg-white text-slate-700 font-bold rounded-xl border border-slate-200 hover:bg-slate-50 transition w-full sm:w-auto">
                    Coba Muat Ulang
                </button>
            </div>
        </div>
        
        <p class="text-sm text-slate-400 mt-8 font-medium">SMK Negeri 1 Kolaka &copy; {{ date('Y') }}</p>
    </div>
</body>
</html>
