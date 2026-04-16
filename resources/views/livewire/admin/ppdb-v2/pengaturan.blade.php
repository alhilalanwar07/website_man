<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Pengaturan PPDB</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Konfigurasi jadwal, kuota jurusan, dan operasional pendaftaran.</p>
        </div>
    </div>

    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-800 dark:bg-emerald-900/30 dark:border-emerald-800 dark:text-emerald-400 font-medium">
            <div class="flex items-center gap-2">
                <x-admin.icon name="check-circle" class="w-5 h-5 text-emerald-500 shrink-0" />
                {{ session('message') }}
            </div>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 rounded-xl bg-red-50 border border-red-200 p-4 text-red-800 dark:bg-red-900/30 dark:border-red-800 dark:text-red-400 font-medium">
            {{ session('error') }}
        </div>
    @endif

    <!-- Toggle Pendaftaran -->
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 mb-6">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-800 p-3 rounded-2xl">
                <x-admin.icon name="sliders" class="w-8 h-8 text-blue-600 dark:text-blue-400" />
            </div>
            <div class="flex-1">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h4 class="text-lg font-bold text-slate-900 dark:text-white">Status Formulir Online</h4>
                        <p class="text-sm text-slate-500 mt-1">Jika dimatikan, halaman formulir di website utama akan otomatis ditutup dan disembunyikan dari calon siswa.</p>
                    </div>
                    <div class="flex-shrink-0">
                        <label class="relative flex items-center cursor-pointer p-1">
                            <input wire:model.live="isRegistrationOpen" type="checkbox" class="sr-only peer">
                            <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[6px] after:left-[6px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-blue-600"></div>
                            <span class="ml-3 text-sm font-bold text-slate-900 dark:text-white">{{ $isRegistrationOpen ? 'DIBUKA' : 'DITUTUP' }}</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6">
        <!-- Pengaturan Kuota Jurusan -->
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 overflow-hidden">
            <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50 dark:bg-slate-900/50">
                <h3 class="font-bold text-slate-800 dark:text-white text-sm">Target Kuota Siswa Lulus Per Jurusan</h3>
                <button wire:click="simpanKuota" class="text-sm font-bold text-white bg-blue-600 px-4 py-2 rounded-xl transition hover:bg-blue-700">Simpan Kuota</button>
            </div>
            
            <div class="p-4 overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                    <thead class="bg-slate-50/80 text-xs uppercase text-slate-700 dark:bg-slate-900 dark:text-slate-300 border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th scope="col" class="px-4 py-3">Nama Program Keahlian</th>
                            <th scope="col" class="px-4 py-3 w-48 text-center">Jumlah Saat Ini</th>
                            <th scope="col" class="px-4 py-3 w-48 text-center">Target Kuota</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($programs as $p)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                            <td class="px-4 py-4 font-bold text-slate-900 dark:text-white">{{ $p->nama_jurusan ?? $p->nama_program }}</td>
                            <td class="px-4 py-4 text-center">
                                <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                    {{ \App\Models\PpdbApplication::where('period_id', $period?->id)->where('program_diterima_id', $p->id)->count() }} Siswa Diizinkan
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <input wire:model="quotas.{{ $p->id }}" type="number" class="w-24 text-center rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm p-2.5 bg-white dark:bg-slate-950 dark:border-slate-700 dark:text-white">
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="p-6 text-center text-slate-500">Belum ada jurusan aktif di database.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
