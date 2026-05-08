<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Halaman Tidak Ditemukan - SMKN 1 Kolaka</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900" rel="stylesheet" />
    
    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-slate-50 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-2xl w-full text-center relative z-10">
        {{-- Background Effects --}}
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[400px] h-[400px] bg-blue-400/10 rounded-full blur-[100px] -z-10"></div>
        
        <div class="bg-white rounded-3xl p-8 sm:p-12 shadow-2xl shadow-slate-200/50 border border-slate-100">
            <h1 class="text-8xl font-black text-transparent bg-clip-text bg-gradient-to-br from-blue-600 to-indigo-600 mb-4 tracking-tighter">404</h1>
            
            <h2 class="text-3xl font-black text-slate-900 mb-4 tracking-tight">Halaman Tidak Ditemukan</h2>
            <p class="text-lg text-slate-600 mb-8 max-w-lg mx-auto">Maaf, halaman yang Anda cari mungkin telah dihapus, diubah namanya, atau tidak pernah ada.</p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="/" class="px-8 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-600/20 w-full sm:w-auto">
                    Kembali ke Beranda
                </a>
            </div>
        </div>
        
        <p class="text-sm text-slate-400 mt-8 font-medium">SMK Negeri 1 Kolaka &copy; {{ date('Y') }}</p>
    </div>
</body>
</html>
