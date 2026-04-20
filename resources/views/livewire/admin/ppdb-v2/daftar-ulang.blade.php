<div>
    <div role="status" aria-live="polite" aria-atomic="true" class="sr-only">
        <span wire:loading wire:target="search,statusFilter">Memuat daftar daftar ulang.</span>
        <span wire:loading wire:target="toggleStatus">Memproses status daftar ulang.</span>
    </div>

    <div wire:loading.flex wire:target="search,statusFilter,toggleStatus" class="mb-4 items-center gap-2 rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm font-semibold text-blue-700 dark:border-blue-900 dark:bg-blue-900/30 dark:text-blue-300">
        <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
        Memuat pembaruan daftar ulang...
    </div>

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Verifikasi Daftar Ulang</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola data siswa yang sudah lulus dan wajib melakukan daftar ulang administrasi/finansial.</p>
        </div>
        <div class="flex flex-shrink-0 items-center gap-3 bg-white px-4 py-2 rounded-2xl shadow-sm border border-slate-200 dark:bg-slate-900 dark:border-slate-800">
            <div class="text-sm">
                <span class="text-slate-500">Progres:</span>
                <span class="font-bold text-emerald-600 ml-1">{{ $countSelesai }}</span> 
                <span class="text-slate-400 mx-0.5">/</span>
                <span class="font-bold text-slate-800 dark:text-white">{{ $countTotal }}</span>
            </div>
            <div class="w-24 h-2 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $countTotal > 0 ? ($countSelesai / $countTotal * 100) : 0 }}%"></div>
            </div>
        </div>
    </div>

    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" stroke-width="2" class="mb-4 rounded-xl bg-violet-50 border border-violet-200 p-4 text-violet-800 dark:bg-violet-900/30 dark:border-violet-800 dark:text-violet-400 text-sm font-medium transition-all shadow-sm">
            <div class="flex items-center gap-2">
                <x-admin.icon name="check-circle" class="w-5 h-5 text-violet-500 shrink-0" />
                {{ session('message') }}
            </div>
        </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 overflow-hidden">
        
        <div class="p-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="relative w-full sm:w-72">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <x-admin.icon name="search" class="h-4 w-4 text-slate-400" />
                </div>
                <input wire:model.live.debounce.300ms="search" wire:loading.attr="disabled" wire:target="search,statusFilter" type="text" class="block w-full rounded-xl border-slate-300 pl-10 text-sm focus:border-blue-500 focus:ring-blue-500 disabled:cursor-not-allowed disabled:opacity-60" placeholder="Cari nama siswa...">
            </div>
            
            <div class="flex rounded-xl overflow-hidden border border-slate-300 dark:border-slate-700 bg-slate-100 dark:bg-slate-950 p-1">
                <button wire:click="$set('statusFilter', '')" wire:loading.attr="disabled" wire:target="statusFilter" class="px-4 py-1.5 text-xs font-bold rounded-lg transition disabled:cursor-not-allowed disabled:opacity-60 {{ $statusFilter === '' ? 'bg-white shadow relative text-slate-900 dark:bg-slate-800 dark:text-white' : 'text-slate-500 hover:text-slate-700' }}">Semua</button>
                <button wire:click="$set('statusFilter', 'belum')" wire:loading.attr="disabled" wire:target="statusFilter" class="px-4 py-1.5 text-xs font-bold rounded-lg transition disabled:cursor-not-allowed disabled:opacity-60 {{ $statusFilter === 'belum' ? 'bg-white shadow relative text-slate-900 dark:bg-slate-800 dark:text-white' : 'text-slate-500 hover:text-slate-700' }}">Belum Selesai</button>
                <button wire:click="$set('statusFilter', 'selesai')" wire:loading.attr="disabled" wire:target="statusFilter" class="px-4 py-1.5 text-xs font-bold rounded-lg transition disabled:cursor-not-allowed disabled:opacity-60 {{ $statusFilter === 'selesai' ? 'bg-white shadow relative text-slate-900 dark:bg-slate-800 dark:text-white' : 'text-slate-500 hover:text-slate-700' }}">Sudah Selesai</button>
            </div>
        </div>

        <div class="overflow-x-auto" wire:loading.remove wire:target="search,statusFilter,toggleStatus">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                <thead class="bg-slate-50/80 text-xs uppercase text-slate-700 dark:bg-slate-900 dark:text-slate-300 border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th scope="col" class="px-4 py-3 w-10 text-center">#</th>
                        <th scope="col" class="px-4 py-3">Data Siswa Diterima</th>
                        <th scope="col" class="px-4 py-3">Diterima di Jurusan</th>
                        <th scope="col" class="px-4 py-3 text-center">Konfirmasi Lunas / Berkas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($pendaftar as $index => $item)
                        @php
                            $isSelesai = $item->status_daftar_ulang === 'verified';
                        @endphp
                        <tr wire:key="daftar-ulang-row-{{ $item->id }}" class="{{ $isSelesai ? 'bg-emerald-50/10 dark:bg-emerald-900/10' : 'hover:bg-slate-50 dark:hover:bg-slate-800/50' }} transition">
                            <td class="px-4 py-3 text-center text-slate-400 font-medium">{{ $index + 1 }}</td>
                            <td class="px-4 py-3">
                                <div class="font-bold {{ $isSelesai ? 'text-slate-500 line-through' : 'text-slate-900 dark:text-white' }}">{{ $item->nama_lengkap }}</div>
                                <div class="text-[11px] mt-0.5 text-slate-500">Nomor: {{ $item->nomor_pendaftaran }} • Asal: {{ $item->asal_sekolah }}</div>
                            </td>
                            <td class="px-4 py-3 font-semibold text-slate-700 dark:text-slate-300">
                                {{ $item->programDiterima?->nama_program ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button wire:click="toggleStatus({{ $item->id }})" 
                                        wire:loading.attr="disabled"
                                        wire:target="toggleStatus({{ $item->id }})"
                                        class="inline-flex items-center gap-2 rounded-xl border px-5 py-2 text-sm font-bold shadow-sm transition disabled:cursor-not-allowed disabled:opacity-60 {{ $isSelesai ? 'border-emerald-200 bg-emerald-100 text-emerald-700 hover:bg-emerald-200 dark:border-emerald-800/50 dark:bg-emerald-900/40 dark:text-emerald-400 hover:dark:bg-emerald-900/60' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200' }}">
                                    @if($isSelesai)
                                        <span wire:loading.remove wire:target="toggleStatus({{ $item->id }})" class="inline-flex items-center gap-2">
                                            <x-admin.icon name="check" class="w-4 h-4" /> BATALKAN
                                        </span>
                                    @else
                                        <span wire:loading.remove wire:target="toggleStatus({{ $item->id }})" class="inline-flex items-center gap-2">
                                            <div class="h-4 w-4 border-2 border-slate-400 rounded-sm"></div> TANDAI SELESAI
                                        </span>
                                    @endif
                                    <span wire:loading wire:target="toggleStatus({{ $item->id }})">Memproses...</span>
                                </button>
                                
                                @if($isSelesai)
                                    <div class="mt-2" title="Waktu Daftar Ulang: {{ $item->daftar_ulang_at?->format('d M Y H:i') }}">
                                        <span class="text-[11px] font-bold text-emerald-600 bg-emerald-100/50 px-2 py-1 rounded inline-flex items-center gap-1">
                                            <x-admin.icon name="printer" class="w-3 h-3" /> Cetak Bukti (Segera Tersedia)
                                        </span>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-12 text-center">
                                <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400 mb-3">
                                    <x-admin.icon name="check-circle" class="w-6 h-6" />
                                </div>
                                <h3 class="font-medium text-slate-900 dark:text-white">Tidak ada data pendaftar yang lulus</h3>
                                <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">Siswa harus ditentukan jurusannya terlebih dahulu sebelum bisa melakukan konfirmasi daftar ulang di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div wire:loading.block wire:target="search,statusFilter,toggleStatus" class="p-4">
            <x-admin.skeleton.table :columns="4" :rows="6" />
        </div>
    </div>
</div>
