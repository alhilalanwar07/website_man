@php
    $profil = \Illuminate\Support\Facades\Cache::remember(
        'site:profil:v1',
        now()->addMinutes(10),
        fn () => \App\Models\ProfilSekolah::first()
    );
    $namaSekolah = $profil->nama_sekolah ?? 'MAN 2 Kolaka';
@endphp

<div class="min-h-screen w-full flex flex-col justify-center items-center py-10 px-4 relative">
    
    <div class="absolute top-10 left-10 w-80 h-80 bg-blue-500 rounded-full mix-blend-multiply filter blur-[80px] opacity-40 dark:opacity-20 animate-blob pointer-events-none"></div>
    <div class="absolute bottom-10 right-10 w-80 h-80 bg-purple-500 rounded-full mix-blend-multiply filter blur-[80px] opacity-40 dark:opacity-20 animate-blob animation-delay-2000 pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">
        
        <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-2xl rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.4)] p-8 sm:p-10 border border-white/50 dark:border-slate-700/50 relative overflow-hidden group">
            
            <div class="absolute inset-0 bg-gradient-to-br from-white/40 to-white/0 dark:from-white/5 dark:to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700 pointer-events-none"></div>

            {{-- Logo & Header --}}
            <div class="text-center mb-10 relative">
                @if($profil && $profil->logo_path)
                    <img src="{{ Storage::url($profil->logo_path) }}" class="w-20 h-20 object-contain mx-auto mb-6 drop-shadow-xl transform transition-all duration-300 hover:scale-110 hover:-rotate-3" alt="{{ $namaSekolah }}">
                @else
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center text-white mx-auto mb-6 shadow-lg shadow-blue-500/40 transform transition-all duration-300 hover:scale-105 hover:-rotate-3 group/logo">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 group-hover/logo:animate-pulse" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                        </svg>
                    </div>
                @endif
                <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Admin Portal</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 font-medium">{{ $namaSekolah }}</p>
            </div>

            {{-- Form --}}
            <form wire:submit="login" class="space-y-6 relative z-10">
                
                {{-- Email --}}
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-bold text-slate-700 dark:text-slate-300">Email Address</label>
                    <div class="relative group/input">
                        <input wire:model="email" type="email" id="email" placeholder="admin@man2kolaka.sch.id"
                            class="peer w-full pl-12 pr-4 py-3.5 rounded-xl border border-slate-200 dark:border-slate-700/60 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all duration-300 shadow-sm relative z-0" autocomplete="username">
                        
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 peer-focus:text-blue-500 transition-colors z-10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" /><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" /></svg>
                        </div>
                    </div>
                    @error('email') <p class="text-red-500 text-xs font-semibold animate-pulse mt-1.5">{{ $message }}</p> @enderror
                </div>

                {{-- Password --}}
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-bold text-slate-700 dark:text-slate-300">Password</label>
                    <div class="relative group/input">
                        <input wire:model="password" type="password" id="password" placeholder="••••••••"
                            class="peer w-full pl-12 pr-4 py-3.5 rounded-xl border border-slate-200 dark:border-slate-700/60 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all duration-300 shadow-sm relative z-0" autocomplete="current-password">
                        
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 peer-focus:text-blue-500 transition-colors z-10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                    </div>
                    @error('password') <p class="text-red-500 text-xs font-semibold animate-pulse mt-1.5">{{ $message }}</p> @enderror
                </div>

                {{-- Remember --}}
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-3 cursor-pointer group/check">
                        <div class="relative flex items-center justify-center">
                            <input wire:model="remember" type="checkbox" class="peer sr-only">
                            <div class="w-5 h-5 border-2 border-slate-300 dark:border-slate-600 rounded bg-white/80 dark:bg-slate-800/80 peer-checked:bg-blue-600 peer-checked:border-blue-600 transition-all duration-200 shadow-sm"></div>
                            <svg class="absolute w-3.5 h-3.5 text-white opacity-0 peer-checked:opacity-100 transition-opacity duration-200 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-slate-600 dark:text-slate-400 group-hover/check:text-slate-900 dark:group-hover/check:text-slate-200 transition-colors">Ingat saya di perangkat ini</span>
                    </label>
                </div>

                {{-- Submit --}}
                <button type="submit" class="w-full mt-4 py-3.5 px-6 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-blue-600/30 transform transition-all duration-300 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:ring-offset-2 dark:focus:ring-offset-slate-900 relative overflow-hidden group/btn text-[15px]">
                    <div class="absolute inset-0 w-full h-full bg-white/20 scale-x-0 group-hover/btn:scale-x-100 origin-left transition-transform duration-500 ease-out"></div>
                    <span wire:loading.remove wire:target="login" class="relative z-10 flex items-center justify-center gap-2">
                        Masuk ke Sistem
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover/btn:translate-x-1" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    <span wire:loading wire:target="login" class="relative z-10 flex items-center justify-center gap-3">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        <span>Mengautentikasi...</span>
                    </span>
                </button>
            </form>
        </div>

        <p class="text-center text-[11px] font-bold text-slate-500/80 dark:text-slate-500 mt-8 tracking-widest uppercase">&copy; {{ date('Y') }} {{ $namaSekolah }}</p>

    </div>

    <style>
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob {
            animation: blob 7s infinite;
        }
        .animation-delay-2000 {
            animation-delay: 2s;
        }
    </style>
</div>