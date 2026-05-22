<div>
    {{-- ============================================ --}}
    {{-- HERO --}}
    {{-- ============================================ --}}
    <section class="relative py-20 sm:py-24 lg:py-28 overflow-hidden bg-slate-950 noise">
        <div class="absolute inset-0">
            <div class="absolute top-[-20%] left-[-10%] w-[250px] sm:w-[400px] h-[250px] sm:h-[400px] bg-emerald-600/20 rounded-full blur-[80px] sm:blur-[100px] animate-blob"></div>
            <div class="absolute bottom-[-10%] right-[-5%] w-[200px] sm:w-[300px] h-[200px] sm:h-[300px] bg-teal-600/15 rounded-full blur-[60px] sm:blur-[80px] animate-blob" style="animation-delay:2s"></div>
            <div class="absolute top-[40%] right-[15%] w-[150px] sm:w-[200px] h-[150px] sm:h-[200px] bg-emerald-500/10 rounded-full blur-[60px] sm:blur-[80px] animate-float-slow"></div>
        </div>
        <div class="absolute inset-0 opacity-[0.03]" style="background-image: linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 60px 60px;"></div>
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-[20%] left-[8%] w-16 h-16 border border-white/10 rounded-2xl rotate-12 animate-float hidden sm:block"></div>
            <div class="absolute bottom-[20%] right-[10%] w-12 h-12 border border-emerald-400/20 rounded-full animate-float-reverse hidden sm:block"></div>
            <div class="absolute top-[30%] right-[30%] w-3 h-3 bg-emerald-400/40 rounded-full animate-float" style="animation-delay:1s"></div>
        </div>
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 text-xs font-bold text-emerald-300 glass rounded-full uppercase tracking-wider mb-4 sm:mb-6 animate-fade-up">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                Tentang Kami
            </span>
            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-black tracking-tight text-white mb-4 sm:mb-6 leading-[0.95] animate-fade-up delay-100">
                Profil <span class="text-gradient">Madrasah</span>
            </h1>
            <p class="text-base sm:text-lg text-slate-400 max-w-2xl animate-fade-up delay-200">Mengenal lebih dekat MAN 2 Kolaka, visi misi, serta komitmen kami dalam membangun generasi unggul.</p>
        </div>
    </section>

    @if($profil)
    {{-- ============================================ --}}
    {{-- SAMBUTAN KEPSEK --}}
    {{-- ============================================ --}}
    @if($profil->teks_sambutan_kepsek)
    <section class="py-16 sm:py-24 lg:py-28 bg-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-[400px] sm:w-[600px] h-[400px] sm:h-[600px] bg-gradient-to-bl from-emerald-50 to-teal-50 rounded-full blur-[100px] sm:blur-[140px] -translate-y-1/3 translate-x-1/3 opacity-70"></div>
        <div class="absolute bottom-0 left-0 w-[300px] h-[300px] bg-gradient-to-tr from-slate-50 to-emerald-50 rounded-full blur-[80px] translate-y-1/3 -translate-x-1/3 opacity-60"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-10 sm:gap-12 lg:gap-20 items-start">

                {{-- Photo Column (2/5) --}}
                <div class="lg:col-span-2 relative">
                    {{-- Decorative borders --}}
                    <div class="absolute -top-3 -left-3 w-full h-full rounded-3xl border-2 border-emerald-200/30 hidden sm:block"></div>
                    <div class="absolute -bottom-3 -right-3 w-full h-full rounded-3xl border-2 border-teal-200/20 hidden sm:block"></div>

                    {{-- Photo --}}
                    <div class="relative z-10 rounded-3xl overflow-hidden shadow-2xl shadow-slate-900/10 mx-auto max-w-xs sm:max-w-sm lg:max-w-none">
                        @if($profil->foto_kepsek)
                            <img src="{{ Storage::url($profil->foto_kepsek) }}" class="w-full aspect-[3/4] object-cover object-top" alt="Kepala Madrasah {{ $profil->nama_kepsek }}">
                        @else
                            <div class="w-full aspect-[3/4] bg-gradient-to-br from-slate-700 via-blue-800 to-indigo-900 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-24 sm:w-28 h-24 sm:h-28 text-white/8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="0.8"><circle cx="12" cy="8" r="4"/><path d="M5.5 21a7.5 7.5 0 0 1 13 0"/></svg>
                            </div>
                        @endif

                        {{-- Name plate overlay --}}
                        @if($profil->nama_kepsek)
                        <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent pt-14 sm:pt-16 pb-4 sm:pb-5 px-4 sm:px-5">
                            <h3 class="text-white font-black text-base sm:text-lg tracking-wide uppercase">{{ $profil->nama_kepsek }}</h3>
                            <p class="text-white/70 text-[10px] sm:text-xs font-semibold tracking-widest uppercase mt-0.5">Kepala Madrasah</p>
                        </div>
                        @endif
                    </div>

                    {{-- Akreditasi badge --}}
                    @if($profil->akreditasi)
                    <div class="absolute -bottom-4 sm:-bottom-5 -right-2 sm:-right-5 z-20 animate-float">
                        <div class="bg-white rounded-xl sm:rounded-2xl px-3.5 sm:px-5 py-2.5 sm:py-3.5 shadow-xl shadow-slate-900/8 border border-slate-100">
                            <div class="flex items-center gap-2 sm:gap-3">
                                <div class="w-9 h-9 sm:w-11 sm:h-11 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-lg sm:rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/25">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                                </div>
                                <div>
                                    <span class="block text-xl sm:text-2xl font-black text-slate-900 leading-none">{{ $profil->akreditasi }}</span>
                                    <span class="text-[8px] sm:text-[9px] font-bold text-slate-400 uppercase tracking-[0.15em]">Akreditasi</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Content Column (3/5) --}}
                <div class="lg:col-span-3">
                    <span class="inline-flex items-center gap-2 px-4 py-1.5 text-xs font-bold text-emerald-600 bg-emerald-50 rounded-full uppercase tracking-wider mb-4 sm:mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
                        Sambutan Kepala Madrasah
                    </span>

                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight text-slate-900 mb-6 sm:mb-8 leading-[1.1]">
                        Selamat Datang di<br><span class="text-gradient">{{ $profil->nama_sekolah ?: 'MAN 2 Kolaka' }}</span>
                    </h2>

                    <div class="prose prose-slate max-w-none text-slate-600 leading-relaxed text-sm sm:text-base">
                        {!! nl2br(e($profil->teks_sambutan_kepsek)) !!}
                    </div>

                    {{-- Signature --}}
                    @if($profil->nama_kepsek)
                    <div class="mt-8 sm:mt-10 flex items-center gap-4">
                        <div class="w-12 h-[2px] bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full"></div>
                        <div>
                            <p class="text-sm font-black text-slate-800 uppercase tracking-wide">{{ $profil->nama_kepsek }}</p>
                            <p class="text-xs text-slate-400 font-medium">Kepala Madrasah {{ $profil->nama_sekolah ?: 'MAN 2 Kolaka' }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- ============================================ --}}
    {{-- VISI & MISI --}}
    {{-- ============================================ --}}
    <section class="py-16 sm:py-24 lg:py-28 bg-slate-50 relative overflow-hidden">
        <div class="absolute bottom-0 left-0 w-[300px] sm:w-[400px] h-[300px] sm:h-[400px] bg-emerald-50 rounded-full blur-[80px] sm:blur-[120px] translate-y-1/2 -translate-x-1/2"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-10 sm:mb-16">
                <span class="inline-block px-4 py-1.5 text-xs font-bold text-teal-600 bg-teal-50 rounded-full uppercase tracking-wider mb-4">Visi & Misi</span>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight text-slate-900">Arah & <span class="text-gradient">Tujuan Kami</span></h2>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
                <div class="bg-white p-6 sm:p-8 lg:p-10 rounded-[20px] sm:rounded-[28px] border border-slate-100 card-hover">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-emerald-400 to-teal-600 rounded-xl sm:rounded-2xl flex items-center justify-center mb-4 sm:mb-6 shadow-lg shadow-emerald-500/20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-black text-slate-900 mb-3 sm:mb-4">Visi</h3>
                    <div class="text-slate-600 leading-relaxed text-sm sm:text-base">
                        {!! nl2br(e($profil->visi_teks ?? 'Belum diatur')) !!}
                    </div>
                </div>
                <div class="bg-white p-6 sm:p-8 lg:p-10 rounded-[20px] sm:rounded-[28px] border border-slate-100 card-hover">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-teal-400 to-emerald-600 rounded-xl sm:rounded-2xl flex items-center justify-center mb-4 sm:mb-6 shadow-lg shadow-teal-500/20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" /></svg>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-black text-slate-900 mb-3 sm:mb-4">Misi</h3>
                    <div class="text-slate-600 leading-relaxed text-sm sm:text-base">
                        {!! nl2br(e($profil->misi_teks ?? 'Belum diatur')) !!}
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- INFORMASI SEKOLAH --}}
    {{-- ============================================ --}}
    <section class="py-16 sm:py-24 lg:py-28 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10 sm:mb-14">
                <span class="inline-block px-4 py-1.5 text-xs font-bold text-emerald-600 bg-emerald-50 rounded-full uppercase tracking-wider mb-4">Informasi</span>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight text-slate-900">Data <span class="text-gradient">Madrasah</span></h2>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                @if($profil->npsn)
                <div class="bg-slate-50 rounded-[16px] sm:rounded-[24px] p-5 sm:p-7 card-hover border border-slate-100">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 bg-gradient-to-br from-emerald-400 to-teal-600 rounded-lg sm:rounded-xl flex items-center justify-center mb-3 sm:mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" /></svg>
                    </div>
                    <span class="text-[9px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-wider">NPSN</span>
                    <p class="text-base sm:text-lg font-black text-slate-900 mt-1">{{ $profil->npsn }}</p>
                </div>
                @endif
                @if($profil->alamat_lengkap)
                <div class="bg-slate-50 rounded-[16px] sm:rounded-[24px] p-5 sm:p-7 card-hover border border-slate-100">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 bg-gradient-to-br from-teal-400 to-emerald-600 rounded-lg sm:rounded-xl flex items-center justify-center mb-3 sm:mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /></svg>
                    </div>
                    <span class="text-[9px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-wider">Alamat</span>
                    <p class="text-xs sm:text-sm font-bold text-slate-900 mt-1 leading-relaxed line-clamp-3">{{ $profil->alamat_lengkap }}</p>
                </div>
                @endif
                @if($profil->nomor_telepon)
                <div class="bg-slate-50 rounded-[16px] sm:rounded-[24px] p-5 sm:p-7 card-hover border border-slate-100">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-lg sm:rounded-xl flex items-center justify-center mb-3 sm:mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                    </div>
                    <span class="text-[9px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-wider">Telepon</span>
                    <p class="text-base sm:text-lg font-black text-slate-900 mt-1">{{ $profil->nomor_telepon }}</p>
                </div>
                @endif
                @if($profil->email_resmi)
                <div class="bg-slate-50 rounded-[16px] sm:rounded-[24px] p-5 sm:p-7 card-hover border border-slate-100">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 bg-gradient-to-br from-emerald-400 to-teal-600 rounded-lg sm:rounded-xl flex items-center justify-center mb-3 sm:mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                    </div>
                    <span class="text-[9px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-wider">Email</span>
                    <p class="text-xs sm:text-sm font-black text-slate-900 mt-1 break-all">{{ $profil->email_resmi }}</p>
                </div>
                @endif
            </div>
        </div>
    </section>
    @else
    <section class="py-16 sm:py-28 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-slate-400 text-base sm:text-lg">Profil madrasah belum diatur. Silakan lengkapi melalui panel admin.</p>
        </div>
    </section>
    @endif

    {{-- ============================================ --}}
    {{-- TENAGA PENGAJAR --}}
    {{-- ============================================ --}}
    @if($pegawai->count() > 0)
    <section class="py-16 sm:py-24 lg:py-28 bg-slate-50 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-[300px] sm:w-[400px] h-[300px] sm:h-[400px] bg-blue-50 rounded-full blur-[80px] sm:blur-[120px] -translate-y-1/2 translate-x-1/2"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-10 sm:mb-16">
                <span class="inline-block px-4 py-1.5 text-xs font-bold text-purple-600 bg-purple-50 rounded-full uppercase tracking-wider mb-4">Tim Kami</span>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight text-slate-900">Tenaga Pendidik & <span class="text-gradient">Kependidikan</span></h2>
                <p class="text-slate-500 mt-3 sm:mt-4 max-w-xl mx-auto text-sm sm:text-base">Tim profesional yang berdedikasi untuk masa depan siswa.</p>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 sm:gap-6">
                @foreach($pegawai as $p)
                <div class="group bg-white rounded-[16px] sm:rounded-[24px] border border-slate-100 overflow-hidden text-center card-hover" wire:key="pegawai-{{ $p->id }}">
                    <div class="aspect-square bg-slate-100 overflow-hidden">
                        @if($p->foto_profil)
                            <img src="{{ Storage::url($p->foto_profil) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="{{ $p->nama_lengkap }}">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-slate-50 to-slate-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 sm:w-16 sm:h-16 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            </div>
                        @endif
                    </div>
                    <div class="p-3 sm:p-5">
                        <h4 class="font-bold text-slate-900 text-xs sm:text-sm line-clamp-1">{{ $p->nama_lengkap }}</h4>
                        <p class="text-[10px] sm:text-xs text-slate-400 mt-0.5 sm:mt-1 line-clamp-1">{{ $p->jabatan }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif
</div>
