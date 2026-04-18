<div wire:poll.10s>
    <div role="status" aria-live="polite" aria-atomic="true" class="sr-only">
        <span wire:loading wire:target="statusFilter,pilihanFilter,search">Memuat daftar siswa untuk penentuan jurusan.</span>
        <span wire:loading wire:target="setBatchTo">Memproses penetapan jurusan massal.</span>
        <span wire:loading wire:target="assignedMajors">Menyimpan penentuan jurusan siswa.</span>
    </div>

    <div wire:loading.flex wire:target="statusFilter,pilihanFilter,search,setBatchTo" class="mb-4 items-center gap-2 rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm font-semibold text-blue-700 dark:border-blue-900 dark:bg-blue-900/30 dark:text-blue-300">
        <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
        Memperbarui data penentuan jurusan...
    </div>

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between shrink-0">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Penentuan Jurusan</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Input manual menetapkan keputusan jurusan akhir siswa berdasarkan hasil tes wawancara.</p>
        </div>
        <div class="w-full lg:w-auto mt-2 sm:mt-0 flex flex-col sm:flex-row gap-3">
            <select wire:model.live="statusFilter" wire:loading.attr="disabled" wire:target="statusFilter,pilihanFilter,search,setBatchTo" class="rounded-xl border-slate-300 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-slate-900 dark:border-slate-700 dark:text-white bg-white disabled:cursor-not-allowed disabled:opacity-60">
                <option value="semua">Semua Status</option>
                <option value="belum_ditentukan">Belum Ditentukan (Prioritas)</option>
                <option value="sudah_ditentukan">Sudah Diputus</option>
            </select>
            
            <select wire:model.live="pilihanFilter" wire:loading.attr="disabled" wire:target="statusFilter,pilihanFilter,search,setBatchTo" class="rounded-xl border-slate-300 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-slate-900 dark:border-slate-700 dark:text-white bg-white w-full sm:w-48 truncate disabled:cursor-not-allowed disabled:opacity-60">
                <option value="">Semua Pilihan (Awal)</option>
                @foreach($majors as $m)
                    <option value="{{ $m->id }}">{{ $m->kode_jurusan ?? $m->nama_jurusan }}</option>
                @endforeach
            </select>

            <div class="relative w-full sm:w-64">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <x-admin.icon name="search" class="h-4 w-4 text-slate-400" />
                </div>
                <input wire:model.live.debounce.300ms="search" wire:loading.attr="disabled" wire:target="statusFilter,pilihanFilter,search,setBatchTo" type="text" class="block w-full rounded-xl border-slate-300 pl-10 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-slate-900 dark:border-slate-700 dark:text-white disabled:cursor-not-allowed disabled:opacity-60" placeholder="Cari nama / NISN...">
            </div>
        </div>
    </div>

    <!-- TOAST NOTIFICATION -->
    @if (session()->has('message'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 4000)" 
             x-transition:enter="transform ease-out duration-300 transition" 
             x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4" 
             x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0" 
             x-transition:leave="transition ease-in duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0 translate-x-4" 
             class="fixed top-8 right-8 z-[100] flex w-full max-w-sm rounded-2xl bg-slate-900 p-4 text-white shadow-2xl dark:bg-white dark:text-slate-900 ring-1 ring-black/5">
            <div class="flex w-full items-center gap-3">
                <div class="flex-shrink-0">
                    <x-admin.icon name="check-circle" class="h-6 w-6 text-emerald-400" />
                </div>
                <div class="flex-1">
                    <p class="text-sm font-bold">{{ session('message') }}</p>
                </div>
                <div class="flex flex-shrink-0">
                    <button @click="show = false" type="button" class="inline-flex rounded-md text-slate-400 hover:text-white focus:outline-none dark:hover:text-slate-600">
                        <x-admin.icon name="x" class="h-5 w-5" />
                    </button>
                </div>
            </div>
        </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 flex flex-col overflow-hidden">
        
        <!-- Header & Quick Override -->
        <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div class="flex items-center gap-2">
                <h3 class="font-bold text-slate-800 dark:text-white text-sm whitespace-nowrap">Daftar Siswa Terverifikasi Berkas</h3>
                @if(count($selectedRows) > 0)
                    <span class="bg-blue-100 text-blue-700 text-[10px] uppercase tracking-wider font-bold px-2 py-0.5 rounded-md">{{ count($selectedRows) }} Dipilih</span>
                @endif
            </div>
            
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 bg-white dark:bg-slate-950 p-3 sm:p-2 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm md:w-auto w-full" x-data="{ majorId: '' }">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wide px-2 shrink-0">Tetapkan yang dipilih ke:</span>
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <select x-model="majorId" class="flex-1 sm:w-48 text-sm font-bold text-slate-700 rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white py-2 px-3 bg-slate-50 cursor-pointer">
                        <option value="">-- Pilih Jurusan --</option>
                        @foreach($majors as $m)
                            <option value="{{ $m->id }}">{{ $m->kode_jurusan ?? $m->nama_jurusan }}</option>
                        @endforeach
                    </select>
                    <button x-show="majorId" @click="$wire.setBatchTo(majorId)" wire:loading.attr="disabled" wire:target="setBatchTo" class="shrink-0 bg-slate-900 text-white hover:bg-slate-800 text-sm px-4 py-2 rounded-lg font-bold transition shadow-sm dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200 whitespace-nowrap disabled:cursor-not-allowed disabled:opacity-60">
                        <span wire:loading.remove wire:target="setBatchTo">Eksekusi</span>
                        <span wire:loading wire:target="setBatchTo">Memproses...</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto" wire:loading.remove wire:target="statusFilter,pilihanFilter,search,setBatchTo">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400 min-w-[700px]">
                <thead class="bg-white sticky top-0 text-[11px] uppercase tracking-wider text-slate-400 font-bold dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 z-10">
                    <tr>
                        <th scope="col" class="px-5 py-4 w-12 text-center">
                            <input wire:model.live="selectAll" wire:loading.attr="disabled" wire:target="statusFilter,pilihanFilter,search,setBatchTo" type="checkbox" class="rounded border-slate-300 text-blue-600 focus:ring-blue-600 cursor-pointer disabled:cursor-not-allowed disabled:opacity-60">
                        </th>
                        <th scope="col" class="px-4 py-4 w-1/3">Identitas Calon Siswa</th>
                        <th scope="col" class="px-4 py-4 w-1/4">Preferensi Jurusan Awal</th>
                        <th scope="col" class="px-5 py-4 w-[350px]">
                            <div class="flex items-center text-blue-600 dark:text-blue-400 gap-2">
                                <x-admin.icon name="academic-cap" class="w-4 h-4" /> KEPUTUSAN FINAL
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($pendaftar as $index => $item)
                        <tr wire:key="penentuan-jurusan-row-{{ $item->id }}" class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition group {{ isset($assignedMajors[$item->id]) && $assignedMajors[$item->id] ? 'bg-blue-50/20' : '' }}">
                            <td class="px-5 py-4 text-center">
                                <input wire:model.live="selectedRows" value="{{ $item->id }}" type="checkbox" class="rounded border-slate-300 text-blue-600 focus:ring-blue-600 cursor-pointer">
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-bold text-base text-slate-900 dark:text-white">{{ $item->nama_lengkap }}</div>
                                <div class="text-[11px] mt-1 text-slate-500 font-medium flex items-center gap-2">
                                    <span class="bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded text-slate-600 dark:text-slate-300">{{ $item->nomor_pendaftaran }}</span>
                                    <span>NISN: {{ $item->nisn ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <ul class="text-xs space-y-1.5 text-slate-500">
                                    <li class="flex items-start gap-1.5">
                                        <span class="font-bold text-slate-400 mt-px">1.</span>
                                        <span class="{{ $item->pilihan_program_1_id ? 'text-slate-700 dark:text-slate-200 font-bold' : '' }} leading-tight">{{ $item->pilihanProgram1?->nama_jurusan ?? 'Belum memilih' }}</span>
                                    </li>
                                    @if($item->pilihan_program_2_id)
                                    <li class="flex items-start gap-1.5 opacity-70">
                                        <span class="font-bold text-slate-400 mt-px">2.</span>
                                        <span class="leading-tight">{{ $item->pilihanProgram2?->nama_jurusan }}</span>
                                    </li>
                                    @endif
                                </ul>
                            </td>
                            <td class="px-5 py-4">
                                <div class="relative w-full">
                                    <select wire:model.live="assignedMajors.{{ $item->id }}" 
                                            class="block w-full border-2 rounded-xl shadow-sm focus:border-blue-500 focus:ring-0 text-sm font-bold transition-all cursor-pointer h-11 pl-4 pr-10
                                            {{ isset($assignedMajors[$item->id]) && $assignedMajors[$item->id] ? 'bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-900/30 dark:border-emerald-800 dark:text-emerald-400 hover:border-emerald-300' : 'bg-white border-slate-200 text-slate-700 dark:bg-slate-950 dark:border-slate-700 dark:text-slate-300 hover:border-slate-300' }}">
                                        <option value="">-- Kosongkan (Belum Diputus) --</option>
                                        @foreach($majors as $m)
                                            <option value="{{ $m->id }}">DITERIMA: {{ $m->nama_jurusan }}</option>
                                        @endforeach
                                    </select>
                                    
                                    <!-- Indikator Loading Auto-Save -->
                                    <div wire:loading wire:target="assignedMajors.{{ $item->id }}" class="absolute right-3 top-1/2 -translate-y-1/2 p-1 bg-white dark:bg-slate-900 rounded-full">
                                        <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    </div>
                                    
                                    <!-- Indikator Sukses Check -->
                                    <div wire:loading.remove wire:target="assignedMajors.{{ $item->id }}" class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none">
                                        @if(isset($assignedMajors[$item->id]) && $assignedMajors[$item->id])
                                            <div class="p-1 bg-emerald-100 rounded-full text-emerald-600 dark:bg-emerald-900/50 dark:text-emerald-400">
                                                <x-admin.icon name="check" class="h-4 w-4" />
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-20 text-center">
                                <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-slate-50 text-slate-300 mb-4 dark:bg-slate-800">
                                    <x-admin.icon name="academic-cap" class="w-8 h-8" />
                                </div>
                                <h3 class="font-black text-slate-900 dark:text-white text-lg">Belum ada siswa di daftar seleksi</h3>
                                <p class="text-sm text-slate-500 mt-2 max-w-md mx-auto leading-relaxed">Siswa yang muncul di sini hanyalah mereka yang telah lulus Verifikasi Berkas. Jika kosong, pastikan Anda telah menyelesaikan tahap verifikasi di menu "Data Pendaftar".</p>
                                <a href="{{ route('admin.ppdb.pendaftar') }}" class="mt-6 inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-slate-900 text-white font-bold transition hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200">
                                    Ke Data Verifikasi <x-admin.icon name="arrow-right" class="w-4 h-4"/>
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div wire:loading.block wire:target="statusFilter,pilihanFilter,search,setBatchTo" class="p-4">
            <x-admin.skeleton.table :columns="4" :rows="6" />
        </div>
    </div>
</div>
