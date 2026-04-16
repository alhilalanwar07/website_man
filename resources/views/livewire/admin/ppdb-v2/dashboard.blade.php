<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Dashboard PPDB</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Ringkasan pendaftaran dan akses cepat penentuan jurusan.</p>
        </div>
        <div class="flex flex-shrink-0 items-center gap-3">
            <a href="{{ route('admin.ppdb.broadcast') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200">
                <x-admin.icon name="megaphone" class="w-4 h-4" />
                Tulis Pengumuman
            </a>
        </div>
    </div>

    <!-- Widget Metrics -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <!-- Pendaftar -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                    <x-admin.icon name="users" class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-slate-500 dark:text-slate-400">Total Pendaftar</h3>
                    <p class="mt-1 text-2xl font-black text-slate-900 dark:text-white">{{ $totalPendaftar }}</p>
                </div>
            </div>
        </div>
        
        <!-- Sudah Diwawancara -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
                    <x-admin.icon name="clipboard-check" class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-slate-500 dark:text-slate-400">Sudah Diwawancara</h3>
                    <p class="mt-1 text-2xl font-black text-slate-900 dark:text-white">{{ $sudahDiwawancara }}</p>
                </div>
            </div>
        </div>

        <!-- Selesai Jurusan -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                    <x-admin.icon name="check-circle" class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-slate-500 dark:text-slate-400">Selesai Penentuan</h3>
                    <p class="mt-1 text-2xl font-black text-slate-900 dark:text-white">{{ $selesaiJurusan }}</p>
                </div>
            </div>
        </div>

        <!-- Daftar Ulang -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-600 dark:bg-violet-900/30 dark:text-violet-400">
                    <x-admin.icon name="document-text" class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-slate-500 dark:text-slate-400">Tuntas Daftar Ulang</h3>
                    <p class="mt-1 text-2xl font-black text-slate-900 dark:text-white">{{ $tuntasDaftarUlang }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Tabel Pendaftar Terbaru -->
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50 dark:bg-slate-900/50">
                <h3 class="font-bold text-slate-800 dark:text-white">Pendaftar Terbaru</h3>
                <a href="{{ route('admin.ppdb.pendaftar') }}" class="text-sm font-bold text-blue-600 transition hover:text-blue-700">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($pendaftarTerbaru as $siswa)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                <td class="px-5 py-3">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $siswa->nama_lengkap }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $siswa->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-xs font-semibold text-blue-700">Baru</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="p-5 text-center text-slate-500">Belum ada pendaftar terbaru.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Status Jurusan -->
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50 dark:bg-slate-900/50">
                <h3 class="font-bold text-slate-800 dark:text-white">Grafik Kuota Jurusan</h3>
                <a href="{{ route('admin.ppdb.penentuan-jurusan') }}" class="text-sm font-bold text-blue-600 transition hover:text-blue-700">Atur Jurusan</a>
            </div>
            <div class="p-5 text-center text-slate-500">
                <p>Belum ada data distribusi jurusan.</p>
            </div>
        </div>
    </div>
</div>
