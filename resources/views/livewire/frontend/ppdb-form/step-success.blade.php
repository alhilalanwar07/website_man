{{-- Success State --}}
<div class="text-center space-y-6">
    <div class="w-20 h-20 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-3xl flex items-center justify-center mx-auto shadow-xl shadow-emerald-500/30">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
    </div>

    <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Pendaftaran Berhasil!</h1>
        <p class="mt-2 text-sm text-slate-500 max-w-md mx-auto">Data pendaftaran Anda telah berhasil disimpan. Simpan nomor pendaftaran berikut untuk keperluan cek status.</p>
    </div>

    <div class="bg-white rounded-2xl border border-emerald-200 shadow-sm p-6 max-w-sm mx-auto">
        <p class="text-xs font-bold text-emerald-600 uppercase tracking-wider mb-2">Nomor Pendaftaran</p>
        <p class="text-3xl font-black text-slate-900 tracking-tight">{{ $submittedNumber }}</p>
    </div>

    @if($submittedDownloadUrl)
        <a href="{{ $submittedDownloadUrl }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-6 py-3 bg-slate-900 text-white text-sm font-bold rounded-xl hover:bg-slate-800 transition shadow-lg shadow-slate-900/20">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Unduh Formulir PDF
        </a>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 max-w-md mx-auto text-left">
        @if($submissionEmailSent)
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-900">Email konfirmasi terkirim</p>
                    <p class="text-xs text-slate-500 mt-0.5">Dikirim ke <strong>{{ $submittedEmail }}</strong>. Cek folder spam jika tidak ditemukan.</p>
                </div>
            </div>
        @else
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-900">Email tidak terkirim</p>
                    <p class="text-xs text-slate-500 mt-0.5">Data tetap tersimpan. Unduh formulir PDF dan hubungi admin jika diperlukan.</p>
                </div>
            </div>
        @endif
    </div>

    <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
        <a href="{{ route('ppdb.status') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white text-sm font-bold rounded-xl hover:bg-blue-700 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            Cek Status Pendaftaran
        </a>
        <a href="{{ route('ppdb.index') }}" class="text-sm font-semibold text-slate-500 hover:text-blue-600 transition">Kembali ke Info PPDB</a>
    </div>
</div>
