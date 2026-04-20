<div wire:poll.10s class="h-full flex flex-col min-h-[calc(100vh-8rem)]">
    <div role="status" aria-live="polite" aria-atomic="true" class="sr-only">
        <span wire:loading wire:target="search,statusFilter,selectSiswa,closeDetail">Memuat data pendaftar.</span>
        <span wire:loading wire:target="verifySelected,verifySingle,rejectSingle,saveSiswaManual,sendSecureDocumentEmailToSelected">Memproses perubahan data pendaftar.</span>
    </div>

    <div wire:loading.flex wire:target="search,statusFilter,selectSiswa,closeDetail,verifySelected,saveSiswaManual,sendSecureDocumentEmailToSelected" class="mb-4 items-center gap-2 rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm font-semibold text-blue-700 dark:border-blue-900 dark:bg-blue-900/30 dark:text-blue-300">
        <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
        Memperbarui data verifikasi pendaftar...
    </div>

    <!-- Header -->
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between shrink-0">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Verifikasi Pendaftar</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Data pendaftar baru yang masuk hari ini. Gunakan panel ini untuk mengecek keabsahan berkas.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <!-- Ekspor Group -->
            <div class="flex rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-sm">
                <button wire:click="exportExcel" wire:loading.attr="disabled" wire:target="exportExcel" class="flex items-center px-4 py-2.5 text-sm font-bold text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/40 border-r border-slate-200 dark:border-slate-700 transition disabled:cursor-not-allowed disabled:opacity-60">
                    <span wire:loading.remove wire:target="exportExcel" class="inline-flex items-center gap-2">
                        <x-admin.icon name="document-text" class="w-4 h-4" /> Excel
                    </span>
                    <span wire:loading wire:target="exportExcel">Menyiapkan...</span>
                </button>
                <button wire:click="exportPdf" wire:loading.attr="disabled" wire:target="exportPdf" class="flex items-center px-4 py-2.5 text-sm font-bold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/40 transition disabled:cursor-not-allowed disabled:opacity-60">
                    <span wire:loading.remove wire:target="exportPdf" class="inline-flex items-center gap-2">
                        <x-admin.icon name="document-text" class="w-4 h-4" /> PDF
                    </span>
                    <span wire:loading wire:target="exportPdf">Menyiapkan...</span>
                </button>
            </div>
            
            <button wire:click="$set('showAddModal', true)" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700 hover:shadow-md">
                <x-admin.icon name="plus" class="w-4 h-4" /> Tambah Offline
            </button>
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

    @if (session()->has('error'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 5000)" 
             x-transition:enter="transform ease-out duration-300 transition" 
             x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4" 
             x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0" 
             x-transition:leave="transition ease-in duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0 translate-x-4" 
             class="fixed top-8 right-8 z-[100] flex w-full max-w-sm rounded-2xl bg-rose-600 p-4 text-white shadow-2xl ring-1 ring-black/10">
            <div class="flex w-full items-center gap-3">
                <div class="flex-shrink-0">
                    <x-admin.icon name="x" class="h-6 w-6 text-rose-100" />
                </div>
                <div class="flex-1">
                    <p class="text-sm font-bold">{{ session('error') }}</p>
                </div>
                <div class="flex flex-shrink-0">
                    <button @click="show = false" type="button" class="inline-flex rounded-md text-rose-100/70 hover:text-white focus:outline-none">
                        <x-admin.icon name="x" class="h-5 w-5" />
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- MASTER-DETAIL LAYOUT -->
    <div class="relative flex-1 overflow-hidden grid grid-cols-1 {{ $selectedSiswaId ? 'lg:grid-cols-12' : 'lg:grid-cols-1' }} gap-6 transition-all duration-300">
        <div wire:loading.flex wire:target="selectSiswa" class="absolute inset-0 z-20 items-stretch justify-end bg-white/70 p-4 backdrop-blur-[1px] dark:bg-slate-950/70 lg:p-0 lg:pl-6">
            <div class="w-full lg:w-2/3">
                <div class="h-full rounded-2xl border border-slate-200 bg-slate-50 p-5 shadow-inner dark:border-slate-700 dark:bg-slate-900/60">
                    <div class="animate-pulse space-y-5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="h-12 w-12 rounded-full bg-slate-200 dark:bg-slate-700"></div>
                                <div class="space-y-2">
                                    <div class="h-4 w-40 rounded bg-slate-200 dark:bg-slate-700"></div>
                                    <div class="h-3 w-56 rounded bg-slate-200 dark:bg-slate-700"></div>
                                </div>
                            </div>
                            <div class="h-9 w-9 rounded-xl bg-slate-200 dark:bg-slate-700"></div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-900">
                            <div class="mb-4 h-3 w-36 rounded bg-slate-200 dark:bg-slate-700"></div>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
                                @for($i = 0; $i < 9; $i++)
                                    <div class="h-16 rounded-xl bg-slate-200 dark:bg-slate-700"></div>
                                @endfor
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-900">
                            <div class="mb-3 h-3 w-32 rounded bg-slate-200 dark:bg-slate-700"></div>
                            <div class="space-y-3">
                                <div class="h-12 rounded-xl bg-slate-200 dark:bg-slate-700"></div>
                                <div class="h-56 rounded-xl bg-slate-200 dark:bg-slate-700"></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div class="h-12 rounded-xl bg-slate-200 dark:bg-slate-700"></div>
                            <div class="col-span-2 h-12 rounded-xl bg-slate-200 dark:bg-slate-700"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- ==========================================
             BAGIAN KIRI: DAFTAR SISWA (MASTER)
        =========================================== -->
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 flex flex-col h-full {{ $selectedSiswaId ? 'lg:col-span-4' : 'lg:col-span-1 border-b' }}">
            
            <!-- Toolbar & Pencarian -->
            <div class="p-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 shrink-0">
                <div class="{{ $selectedSiswaId ? 'flex flex-col gap-3' : 'flex flex-col sm:flex-row justify-between items-center gap-4' }}">
                    <div class="relative w-full">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <x-admin.icon name="search" class="h-4 w-4 text-slate-400" />
                        </div>
                        <input wire:model.live.debounce.300ms="search" wire:loading.attr="disabled" wire:target="search,statusFilter" type="text" class="block w-full rounded-xl border-slate-300 pl-10 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white disabled:cursor-not-allowed disabled:opacity-60" placeholder="Cari nama / NISN...">
                    </div>
                    
                    <div class="flex gap-2 w-full">
                        <select wire:model.live="statusFilter" wire:loading.attr="disabled" wire:target="search,statusFilter" class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white disabled:cursor-not-allowed disabled:opacity-60">
                            <option value="">Semua Berkas</option>
                            <option value="pending">Menunggu Verifikasi</option>
                            <option value="verified">Sah</option>
                            <option value="revision">Perlu Revisi</option>
                        </select>
                    </div>

                    @if(!$selectedSiswaId && count($selectedRows) > 0)
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ count($selectedRows) }} Dipilih</span>
                        <button wire:click="verifySelected" wire:loading.attr="disabled" wire:target="verifySelected" class="px-3 py-1.5 bg-emerald-100 text-emerald-700 font-bold rounded-lg text-xs hover:bg-emerald-200 disabled:cursor-not-allowed disabled:opacity-60">
                            <span wire:loading.remove wire:target="verifySelected">Verifikasi Massal</span>
                            <span wire:loading wire:target="verifySelected">Memproses...</span>
                        </button>
                    </div>
                    @endif
                </div>
            </div>

            <!-- List/Tabel Area -->
            <div class="flex-1 overflow-y-auto">
                <div wire:loading.block wire:target="search,statusFilter,selectAll,page" class="p-4">
                    <x-admin.skeleton.table :columns="6" :rows="8" />
                </div>

                <div wire:loading.remove wire:target="search,statusFilter,selectAll,page">
                    @if(!$selectedSiswaId)
                        <!-- TAMPILAN FULL TABLE JIKA TIDAK ADA YANG DIKLIK -->
                        <table class="w-full text-left text-sm text-slate-500 dark:text-slate-400">
                            <thead class="bg-white sticky top-0 text-xs uppercase text-slate-400 font-bold dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 z-10">
                                <tr>
                                    <th class="p-4 w-10"><input wire:model.live="selectAll" wire:loading.attr="disabled" wire:target="search,statusFilter,selectAll,page" type="checkbox" class="rounded border-slate-300 text-blue-600 focus:ring-blue-600 disabled:cursor-not-allowed disabled:opacity-60"></th>
                                    <th class="px-4 py-3">Nama Pendaftar</th>
                                    <th class="px-4 py-3">Asal Sekolah</th>
                                    <th class="px-4 py-3">No. Whatsapp</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @forelse ($pendaftar as $item)
                                    <tr wire:key="pendaftar-table-{{ $item->id }}" wire:click="selectSiswa({{ $item->id }})" class="hover:bg-blue-50/50 dark:hover:bg-blue-900/20 cursor-pointer transition group">
                                        <td class="p-4" wire:click.stop>
                                            <input wire:model.live="selectedRows" value="{{ $item->id }}" type="checkbox" class="rounded border-slate-300 text-blue-600 focus:ring-blue-600">
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="font-bold text-slate-900 dark:text-white group-hover:text-blue-600">{{ $item->nama_lengkap }}</div>
                                            <div class="text-[11px] mt-0.5 text-slate-500">Reg: {{ $item->nomor_pendaftaran }}</div>
                                        </td>
                                        <td class="px-4 py-3 font-medium">{{ $item->asal_sekolah ?? '-' }}</td>
                                        <td class="px-4 py-3">{{ $item->nomor_hp ?? '-' }}</td>
                                        <td class="px-4 py-3">
                                            @if($item->status_berkas === 'verified')
                                                <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-600 px-2.5 py-1 rounded-full text-xs font-bold border border-emerald-100"><div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div> Sah</span>
                                            @elseif($item->status_berkas === 'revision')
                                                <span class="inline-flex items-center gap-1 bg-rose-50 text-rose-600 px-2.5 py-1 rounded-full text-xs font-bold border border-rose-100"><div class="w-1.5 h-1.5 rounded-full bg-rose-500"></div> Perlu Revisi</span>
                                            @else
                                                <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-600 px-2.5 py-1 rounded-full text-xs font-bold border border-amber-100"><div class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></div> Menunggu Verifikasi</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <button class="text-xs font-bold text-blue-600 bg-blue-50 px-4 py-1.5 rounded-xl hover:bg-blue-600 hover:text-white transition">Buka File &rarr;</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-16 text-center">
                                            <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-slate-50 text-slate-300 mb-4 dark:bg-slate-800">
                                                <x-admin.icon name="document-search" class="w-8 h-8" />
                                            </div>
                                            <p class="font-bold text-slate-900 dark:text-white text-lg">Tidak ada pendaftar</p>
                                            <p class="text-sm text-slate-500 mt-1">Belum ada data pendaftar yang memenuhi pencarian Anda.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    @else
                        <!-- TAMPILAN COMPACT LIST JIKA DETAIL TERBUKA -->
                        <div class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse ($pendaftar as $item)
                            <div wire:key="pendaftar-compact-{{ $item->id }}" wire:click="selectSiswa({{ $item->id }})" class="p-4 cursor-pointer transition {{ $selectedSiswaId === $item->id ? 'bg-blue-50 border-l-4 border-blue-600 dark:bg-blue-900/20' : 'hover:bg-slate-50 border-l-4 border-transparent dark:hover:bg-slate-800/50' }}">
                                <div class="flex justify-between items-start mb-1">
                                    <h4 class="font-bold {{ $selectedSiswaId === $item->id ? 'text-blue-700 dark:text-blue-400' : 'text-slate-900 dark:text-white' }} truncate pr-2">{{ $item->nama_lengkap }}</h4>
                                    
                                    <div class="shrink-0 mt-0.5">
                                        @if($item->status_berkas === 'verified')
                                            <x-admin.icon name="check-circle" class="w-5 h-5 text-emerald-500" />
                                        @elseif($item->status_berkas === 'revision')
                                            <x-admin.icon name="x" class="w-5 h-5 text-rose-500" />
                                        @else
                                            <div class="w-2.5 h-2.5 rounded-full bg-amber-400 shadow-[0_0_8px_rgba(251,191,36,0.8)] mt-1 animate-pulse"></div>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center text-xs text-slate-500 gap-2">
                                    <span>{{ $item->nomor_pendaftaran }}</span>
                                    <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                    <span class="truncate">{{ $item->asal_sekolah ?? '-' }}</span>
                                </div>
                            </div>
                            @empty
                                <div class="p-8 text-center text-sm text-slate-400">Data pendaftar kosong.</div>
                            @endforelse
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Pagination selalu di bawah -->
            @if($pendaftar->hasPages())
                <div class="p-4 border-t border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 shrink-0 shadow-[0_-10px_20px_rgba(0,0,0,0.02)]">
                    {{ $pendaftar->links('components.admin.pagination') }}
                </div>
            @endif
        </div>

        <!-- ==========================================
             BAGIAN KANAN: PREVIEW DOKUMEN (LAYAR LEBAR)
        =========================================== -->
        @if($selectedSiswaId && $this->selectedSiswa)
        <div class="lg:col-span-8 flex flex-col fade-in h-auto max-h-[100%] rounded-2xl border border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-900/50 shadow-inner overflow-hidden relative">
            
            <!-- Header Kartu Detail -->
            <div class="p-5 bg-white border-b border-slate-200 dark:bg-slate-800 dark:border-slate-700 flex justify-between items-center shadow-sm shrink-0">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-500 text-white flex items-center justify-center text-xl font-bold font-serif shadow-md">
                        {{ substr($this->selectedSiswa->nama_lengkap, 0, 1) }}
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-slate-900 dark:text-white leading-tight">{{ $this->selectedSiswa->nama_lengkap }}</h2>
                        <div class="text-sm font-semibold mt-0.5 flex gap-2 items-center">
                            <span class="text-slate-500">{{ $this->selectedSiswa->nomor_hp ?? 'Tidak ada No. HP' }}</span>
                            <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-500 text-[10px] uppercase font-bold">{{ $this->selectedSiswa->asal_sekolah ?? '-' }}</span>
                        </div>
                    </div>
                </div>
                <div>
                    <button wire:click="closeDetail" wire:loading.attr="disabled" wire:target="closeDetail" class="text-slate-400 hover:bg-slate-100 p-2 rounded-xl transition dark:hover:bg-slate-700 disabled:cursor-not-allowed disabled:opacity-60">
                        <x-admin.icon name="x" class="w-6 h-6"/>
                    </button>
                </div>
            </div>

            <!-- Scrollable Content Ruang Kerja PDF/Gambar -->
            <div class="flex-1 overflow-y-auto p-6 flex flex-col gap-6">

                <!-- Ringkasan Informasi Siswa -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden dark:bg-slate-900 dark:border-slate-800">
                    <div class="px-4 py-3 bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-700">
                        <h3 class="text-xs font-black uppercase tracking-widest text-slate-600 dark:text-slate-300">Informasi Siswa</h3>
                    </div>
                    <div class="p-4 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3 text-sm">
                        <div class="rounded-xl border border-slate-200 bg-slate-50/70 px-3 py-2.5 dark:border-slate-700 dark:bg-slate-800/40">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Nomor Pendaftaran</p>
                            <p class="mt-1 font-bold text-slate-900 dark:text-white">{{ $this->selectedSiswa->nomor_pendaftaran ?? '-' }}</p>
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-slate-50/70 px-3 py-2.5 dark:border-slate-700 dark:bg-slate-800/40">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">NISN</p>
                            <p class="mt-1 font-bold text-slate-900 dark:text-white">{{ $this->selectedSiswa->nisn ?? '-' }}</p>
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-slate-50/70 px-3 py-2.5 dark:border-slate-700 dark:bg-slate-800/40">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Jenis Kelamin</p>
                            <p class="mt-1 font-bold text-slate-900 dark:text-white">{{ $this->selectedSiswa->jenis_kelamin ? str($this->selectedSiswa->jenis_kelamin)->title() : '-' }}</p>
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-slate-50/70 px-3 py-2.5 dark:border-slate-700 dark:bg-slate-800/40">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Tempat Lahir</p>
                            <p class="mt-1 font-bold text-slate-900 dark:text-white">{{ $this->selectedSiswa->tempat_lahir ?? '-' }}</p>
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-slate-50/70 px-3 py-2.5 dark:border-slate-700 dark:bg-slate-800/40">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Tanggal Lahir</p>
                            <p class="mt-1 font-bold text-slate-900 dark:text-white">{{ $this->selectedSiswa->tanggal_lahir ? $this->selectedSiswa->tanggal_lahir->format('d M Y') : '-' }}</p>
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-slate-50/70 px-3 py-2.5 dark:border-slate-700 dark:bg-slate-800/40">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Email</p>
                            <p class="mt-1 font-bold text-slate-900 dark:text-white break-all">{{ $this->selectedSiswa->email ?? '-' }}</p>
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-slate-50/70 px-3 py-2.5 dark:border-slate-700 dark:bg-slate-800/40 sm:col-span-2 xl:col-span-1">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Status Pendaftaran</p>
                            <p class="mt-1 font-bold text-slate-900 dark:text-white">{{ $this->selectedSiswa->status_pendaftaran_label }}</p>
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-slate-50/70 px-3 py-2.5 dark:border-slate-700 dark:bg-slate-800/40 sm:col-span-2 xl:col-span-1">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Jalur Pendaftaran</p>
                            <p class="mt-1 font-bold text-slate-900 dark:text-white">{{ $this->selectedSiswa->track?->nama_jalur ?? '-' }}</p>
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-slate-50/70 px-3 py-2.5 dark:border-slate-700 dark:bg-slate-800/40 sm:col-span-2 xl:col-span-1">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Pilihan Program 1</p>
                            <p class="mt-1 font-bold text-slate-900 dark:text-white">{{ $this->selectedSiswa->pilihanProgram1?->nama_jurusan ?? '-' }}</p>
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-slate-50/70 px-3 py-2.5 dark:border-slate-700 dark:bg-slate-800/40 sm:col-span-2">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Catatan Verifikator</p>
                            <p class="mt-1 font-semibold text-slate-700 dark:text-slate-300">{{ $this->selectedSiswa->catatan_verifikator ?? 'Belum ada catatan verifikator.' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Dokumen Formulir Aman -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden dark:bg-slate-900 dark:border-slate-800">
                    <div class="px-4 py-3 bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-700">
                        <h3 class="text-xs font-black uppercase tracking-widest text-slate-600 dark:text-slate-300">Formulir Aman</h3>
                    </div>

                    <div class="p-4 grid grid-cols-1 xl:grid-cols-3 gap-3 text-sm">
                        <div class="rounded-xl border border-slate-200 bg-slate-50/70 px-3 py-2.5 dark:border-slate-700 dark:bg-slate-800/40">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Kode Dokumen</p>
                            <p class="mt-1 font-mono text-xs text-slate-900 dark:text-white break-all">{{ $selectedSecureDocument['document_code'] ?? '-' }}</p>
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-slate-50/70 px-3 py-2.5 dark:border-slate-700 dark:bg-slate-800/40">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Tanda Tangan Digital</p>
                            <p class="mt-1 font-mono text-xs text-slate-900 dark:text-white break-all">{{ $selectedSecureDocument['signature_short'] ?? '-' }}</p>
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-slate-50/70 px-3 py-2.5 dark:border-slate-700 dark:bg-slate-800/40">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Diterbitkan</p>
                            <p class="mt-1 font-semibold text-slate-900 dark:text-white">{{ $selectedSecureDocument['issued_at_human'] ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="px-4 pb-4 flex flex-wrap gap-2">
                        @if($this->selectedFormulirDownloadUrl)
                            <a href="{{ $this->selectedFormulirDownloadUrl }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-4 py-2 text-xs font-black uppercase tracking-wide text-blue-700 hover:bg-blue-100 transition">
                                <x-admin.icon name="document-text" class="w-4 h-4" /> Unduh Formulir Aman
                            </a>
                        @endif

                        @if(!empty($selectedSecureDocument['verification_url']))
                            <a href="{{ $selectedSecureDocument['verification_url'] }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2 text-xs font-black uppercase tracking-wide text-emerald-700 hover:bg-emerald-100 transition">
                                <x-admin.icon name="check-circle" class="w-4 h-4" /> Cek Keaslian
                            </a>
                        @endif

                        <button type="button" wire:click="sendSecureDocumentEmailToSelected" wire:loading.attr="disabled" wire:target="sendSecureDocumentEmailToSelected" class="inline-flex items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-2 text-xs font-black uppercase tracking-wide text-amber-700 hover:bg-amber-100 transition disabled:cursor-not-allowed disabled:opacity-60">
                            <span wire:loading.remove wire:target="sendSecureDocumentEmailToSelected" class="inline-flex items-center gap-2">
                                <x-admin.icon name="mail" class="w-4 h-4" /> Kirim Ulang ke Email
                            </span>
                            <span wire:loading wire:target="sendSecureDocumentEmailToSelected">Mengirim...</span>
                        </button>
                    </div>
                </div>
                
                @if($this->selectedSiswa->documents->isEmpty())
                    <!-- Simulasi Jika Dokumen Kosong -->
                    <div class="text-center py-20 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
                        <div class="inline-flex w-24 h-24 bg-slate-50 rounded-full items-center justify-center mb-4">
                            <x-admin.icon name="document-text" class="w-12 h-12 text-slate-200" />
                        </div>
                        <h3 class="text-lg font-bold text-slate-900">Peserta belum mengunggah berkas apapun.</h3>
                        <p class="text-sm text-slate-500 mt-2 max-w-md mx-auto">Untuk simulasi UI, ini adalah kolom di mana Ijazah/SKL atau identitas yang di-*upload* siswa akan dimunculkan.</p>
                        <div class="mt-8 border-t border-slate-100 pt-8 w-full max-w-2xl mx-auto px-6">
                            <img src="https://placehold.co/1200x800/f8fafc/94a3b8?text=+SIMULASI+SCAN+KERTAS+IJAZAH+" class="w-full h-auto rounded-xl shadow-md border border-slate-200" alt="Ijazah" >
                        </div>
                    </div>
                @else
                    <!-- Mapping Semua Dokumen Asli -->
                    @foreach($this->selectedSiswa->documents as $doc)
                        @php
                            $extension = pathinfo($doc->file_path, PATHINFO_EXTENSION);
                            $url = asset('storage/' . $doc->file_path);
                        @endphp
                        <div wire:key="doc-preview-{{ $doc->id }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden dark:bg-slate-900 dark:border-slate-800">
                            <div class="bg-slate-50 dark:bg-slate-800/80 px-4 py-3 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
                                <h4 class="font-black text-slate-700 dark:text-slate-300 uppercase tracking-widest text-xs flex items-center gap-2">
                                    <x-admin.icon name="document-search" class="w-4 h-4 text-blue-500" /> 
                                    {{ str_replace('_', ' ', $doc->jenis_dokumen) }}
                                </h4>
                                <a href="{{ $url }}" target="_blank" class="text-xs bg-white border border-slate-300 shadow-sm px-3 py-1.5 rounded-lg text-slate-700 font-bold hover:bg-slate-50 transition">Buka Window Baru</a>
                            </div>
                            <div class="bg-slate-100/50 dark:bg-slate-950 p-4 min-h-[500px] flex items-center justify-center">
                                @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'svg', 'webp']))
                                    <!-- Jika Gambar -->
                                    <img src="{{ $url }}" class="max-w-full max-h-[800px] object-contain rounded-xl shadow-sm bg-white" alt="Scan File">
                                @elseif(strtolower($extension) === 'pdf')
                                    <!-- Jika PDF -->
                                    <embed src="{{ $url }}#toolbar=0&navpanes=0" type="application/pdf" class="w-full h-[800px] rounded-xl shadow-sm bg-white" />
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Footer Action Bar (Sticky Bawah) -->
            <div class="p-4 bg-white border-t border-slate-200 dark:bg-slate-800 dark:border-slate-700 shadow-[0_-4px_10px_rgba(0,0,0,0.03)] shrink-0 z-10 flex gap-3 pb-8 lg:pb-4">
                
                @if($this->selectedSiswa->status_berkas === 'verified')
                    <div class="w-full py-3.5 bg-emerald-50 border border-emerald-200 text-emerald-700 font-black rounded-xl flex items-center justify-center gap-2 text-sm shadow-sm dark:bg-emerald-900/30 dark:border-emerald-800">
                        <x-admin.icon name="check-circle" class="w-5 h-5"/> BERKAS TELAH DISAHKAN
                    </div>
                    <!-- Tombol Batal/Tukar untuk UX Aman -->
                    <button wire:click="rejectSingle({{ $this->selectedSiswa->id }})" wire:loading.attr="disabled" wire:target="rejectSingle" class="shrink-0 px-6 py-3 bg-slate-100 text-slate-500 font-bold rounded-xl hover:bg-rose-50 hover:text-rose-600 transition text-sm disabled:opacity-50" title="Batalkan & Tolak">
                        <span wire:loading.remove wire:target="rejectSingle">Tolak/Batal</span>
                        <span wire:loading wire:target="rejectSingle">Memproses...</span>
                    </button>
                    
                @elseif($this->selectedSiswa->status_berkas === 'revision')
                    <div class="w-full py-3.5 bg-rose-50 border border-rose-200 text-rose-700 font-black rounded-xl flex items-center justify-center gap-2 text-sm shadow-sm dark:bg-rose-900/30 dark:border-rose-800">
                        <x-admin.icon name="x" class="w-5 h-5"/> BERKAS DITOLAK (REVISI)
                    </div>
                    <button wire:click="verifySingle({{ $this->selectedSiswa->id }})" wire:loading.attr="disabled" wire:target="verifySingle" class="shrink-0 px-6 py-3 bg-slate-100 text-slate-500 font-bold rounded-xl hover:bg-emerald-50 hover:text-emerald-600 transition text-sm disabled:opacity-50" title="Ralat Menjadi Sah">
                        <span wire:loading.remove wire:target="verifySingle">Ubah SAH</span>
                        <span wire:loading wire:target="verifySingle">Memproses...</span>
                    </button>
                    
                @else
                    <button wire:click="rejectSingle({{ $this->selectedSiswa->id }})" wire:loading.attr="disabled" wire:target="rejectSingle" class="w-1/3 py-3.5 bg-rose-50 text-rose-700 border border-rose-200 font-black rounded-xl hover:bg-rose-100 hover:text-rose-800 transition text-sm flex items-center justify-center gap-2 disabled:opacity-50">
                        <span wire:loading.remove wire:target="rejectSingle" class="flex items-center gap-2"><x-admin.icon name="x" class="w-5 h-5"/> TOLAK BERKAS</span>
                        <span wire:loading wire:target="rejectSingle">Memproses...</span>
                    </button>
                    <button wire:click="verifySingle({{ $this->selectedSiswa->id }})" wire:loading.attr="disabled" wire:target="verifySingle" class="w-2/3 py-3.5 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-black hover:from-emerald-600 hover:to-teal-600 rounded-xl transition shadow-md shadow-emerald-500/20 text-sm flex items-center justify-center gap-2 disabled:opacity-50">
                        <span wire:loading.remove wire:target="verifySingle" class="flex items-center gap-2"><x-admin.icon name="check" class="w-5 h-5"/> VERIFIKASI ASLI & SAH (LULUSKAN)</span>
                        <span wire:loading wire:target="verifySingle">Menyimpan...</span>
                    </button>
                @endif
                
            </div>
            
        </div>
        @endif
        
    </div>

    <!-- Modal Tambah Offline -->
    @if($showAddModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-slate-900/60 p-4 backdrop-blur-sm">
            <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl dark:bg-slate-900 border border-slate-200 dark:border-slate-700">
                <div wire:loading.flex wire:target="saveSiswaManual" class="absolute inset-0 z-20 items-center justify-center rounded-2xl bg-white/80 text-sm font-bold text-blue-600 backdrop-blur-sm dark:bg-slate-900/80 dark:text-blue-300">
                    Menyimpan data pendaftar...
                </div>
                <form wire:submit.prevent="saveSiswaManual">
                    <div class="flex items-center justify-between border-b border-slate-100 p-5 dark:border-slate-800">
                        <h3 class="text-xl font-black text-slate-900 dark:text-white">Form Input Manual</h3>
                        <button type="button" wire:click="$set('showAddModal', false)" wire:loading.attr="disabled" wire:target="saveSiswaManual" class="rounded-full bg-slate-100 p-2 text-slate-500 hover:bg-rose-100 hover:text-rose-600 transition dark:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60">
                            <x-admin.icon name="x" class="h-4 w-4" />
                        </button>
                    </div>
                    <div class="space-y-5 p-6">
                        <div>
                            <label class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-300">Nama Lengkap (Sesuai Ijazah)</label>
                            <input type="text" wire:model="formSiswa.nama_lengkap" wire:loading.attr="disabled" wire:target="saveSiswaManual" class="block w-full rounded-xl border-slate-300 p-3 text-sm focus:border-blue-500 focus:ring-blue-500 outline-none transition bg-slate-50 dark:bg-slate-950 dark:border-slate-700 disabled:cursor-not-allowed disabled:opacity-60" placeholder="Ahmad Budi..." required>
                            @error('formSiswa.nama_lengkap') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-300">NISN</label>
                                <input type="text" wire:model="formSiswa.nisn" wire:loading.attr="disabled" wire:target="saveSiswaManual" class="block w-full rounded-xl border-slate-300 p-3 text-sm focus:border-blue-500 font-mono tracking-widest outline-none transition bg-slate-50 dark:bg-slate-950 dark:border-slate-700 disabled:cursor-not-allowed disabled:opacity-60" placeholder="00123...">
                                @error('formSiswa.nisn') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-300">No. WhatsApp Aktif</label>
                                <input type="text" wire:model="formSiswa.nomor_hp" wire:loading.attr="disabled" wire:target="saveSiswaManual" class="block w-full rounded-xl border-slate-300 p-3 text-sm focus:border-blue-500 outline-none transition bg-slate-50 dark:bg-slate-950 dark:border-slate-700 disabled:cursor-not-allowed disabled:opacity-60" placeholder="08...">
                                @error('formSiswa.nomor_hp') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-300">Email Aktif</label>
                                <input type="email" wire:model="formSiswa.email" wire:loading.attr="disabled" wire:target="saveSiswaManual" class="block w-full rounded-xl border-slate-300 p-3 text-sm focus:border-blue-500 outline-none transition bg-slate-50 dark:bg-slate-950 dark:border-slate-700 disabled:cursor-not-allowed disabled:opacity-60" placeholder="nama@email.com" required>
                                @error('formSiswa.email') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-300">Asal Sekolah</label>
                            <input type="text" wire:model="formSiswa.asal_sekolah" wire:loading.attr="disabled" wire:target="saveSiswaManual" class="block w-full rounded-xl border-slate-300 p-3 text-sm focus:border-blue-500 outline-none transition bg-slate-50 dark:bg-slate-950 dark:border-slate-700 disabled:cursor-not-allowed disabled:opacity-60" placeholder="SMPN ..." required>
                            @error('formSiswa.asal_sekolah') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-300">Jenis Kelamin</label>
                                <select wire:model="formSiswa.jenis_kelamin" wire:loading.attr="disabled" wire:target="saveSiswaManual" class="block w-full rounded-xl border-slate-300 p-3 text-sm focus:border-blue-500 outline-none transition bg-slate-50 dark:bg-slate-950 dark:border-slate-700 disabled:cursor-not-allowed disabled:opacity-60" required>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                                @error('formSiswa.jenis_kelamin') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-300">Tanggal Lahir</label>
                                <input type="date" wire:model="formSiswa.tanggal_lahir" wire:loading.attr="disabled" wire:target="saveSiswaManual" class="block w-full rounded-xl border-slate-300 p-3 text-sm focus:border-blue-500 outline-none transition bg-slate-50 dark:bg-slate-950 dark:border-slate-700 disabled:cursor-not-allowed disabled:opacity-60" required>
                                @error('formSiswa.tanggal_lahir') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-300">Tempat Lahir</label>
                                <input type="text" wire:model="formSiswa.tempat_lahir" wire:loading.attr="disabled" wire:target="saveSiswaManual" class="block w-full rounded-xl border-slate-300 p-3 text-sm focus:border-blue-500 outline-none transition bg-slate-50 dark:bg-slate-950 dark:border-slate-700 disabled:cursor-not-allowed disabled:opacity-60" placeholder="Kolaka" required>
                                @error('formSiswa.tempat_lahir') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-300">Jalur Pendaftaran</label>
                                <select wire:model="formSiswa.track_id" wire:loading.attr="disabled" wire:target="saveSiswaManual" class="block w-full rounded-xl border-slate-300 p-3 text-sm focus:border-blue-500 outline-none transition bg-slate-50 dark:bg-slate-950 dark:border-slate-700 disabled:cursor-not-allowed disabled:opacity-60" required>
                                    <option value="">Pilih Jalur</option>
                                    @foreach($availableTracksForManual as $trackOption)
                                        <option value="{{ $trackOption['id'] }}">{{ $trackOption['label'] }}</option>
                                    @endforeach
                                </select>
                                @error('formSiswa.track_id') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-300">Pilihan Program Keahlian 1</label>
                            <select wire:model="formSiswa.pilihan_program_1_id" wire:loading.attr="disabled" wire:target="saveSiswaManual" class="block w-full rounded-xl border-slate-300 p-3 text-sm focus:border-blue-500 outline-none transition bg-slate-50 dark:bg-slate-950 dark:border-slate-700 disabled:cursor-not-allowed disabled:opacity-60" required>
                                <option value="">Pilih Program</option>
                                @foreach($availableProgramsForManual as $programOption)
                                    <option value="{{ $programOption['id'] }}">{{ $programOption['label'] }}</option>
                                @endforeach
                            </select>
                            @error('formSiswa.pilihan_program_1_id') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-300">Alamat Lengkap</label>
                            <textarea wire:model="formSiswa.alamat_lengkap" wire:loading.attr="disabled" wire:target="saveSiswaManual" rows="3" class="block w-full rounded-xl border-slate-300 p-3 text-sm focus:border-blue-500 outline-none transition bg-slate-50 dark:bg-slate-950 dark:border-slate-700 disabled:cursor-not-allowed disabled:opacity-60" placeholder="Jalan, RT/RW, Kelurahan, Kecamatan" required></textarea>
                            @error('formSiswa.alamat_lengkap') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 p-5 bg-slate-50 rounded-b-2xl dark:bg-slate-800/50">
                        <button type="button" wire:click="$set('showAddModal', false)" wire:loading.attr="disabled" wire:target="saveSiswaManual" class="rounded-xl px-5 py-2.5 font-bold text-slate-600 hover:bg-slate-200 transition text-sm disabled:cursor-not-allowed disabled:opacity-60">Kembali</button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="saveSiswaManual" class="rounded-xl bg-blue-600 px-7 py-2.5 font-bold text-white hover:bg-blue-700 transition shadow-lg shadow-blue-600/30 text-sm disabled:cursor-not-allowed disabled:opacity-60">
                            <span wire:loading.remove wire:target="saveSiswaManual">Simpan Data</span>
                            <span wire:loading wire:target="saveSiswaManual">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
