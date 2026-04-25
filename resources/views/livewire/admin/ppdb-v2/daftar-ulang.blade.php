<div>
    <div role="status" aria-live="polite" aria-atomic="true" class="sr-only">
        <span wire:loading wire:target="search,statusFilter,programFilter">Memuat daftar daftar ulang.</span>
        <span wire:loading wire:target="toggleStatus,bulkVerifySelected">Memproses status daftar ulang.</span>
    </div>

    <div wire:loading.flex wire:target="search,statusFilter,programFilter,toggleStatus,bulkVerifySelected" class="mb-4 items-center gap-2 rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm font-semibold text-blue-700 dark:border-blue-900 dark:bg-blue-900/30 dark:text-blue-300">
        <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
        Memuat pembaruan daftar ulang...
    </div>

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Verifikasi Daftar Ulang</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Kelola siswa yang sudah lulus, sudah memilih jurusan akhir, dan sedang menjalani proses daftar ulang.</p>
        </div>
        <div class="flex flex-shrink-0 items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-2 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="text-sm">
                <span class="text-slate-500">Progres:</span>
                <span class="ml-1 font-bold text-emerald-600">{{ $countSelesai }}</span>
                <span class="mx-0.5 text-slate-400">/</span>
                <span class="font-bold text-slate-800 dark:text-white">{{ $countTotal }}</span>
            </div>
            <div class="h-2 w-24 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                <div class="h-full rounded-full bg-emerald-500" style="width: {{ $countTotal > 0 ? ($countSelesai / $countTotal * 100) : 0 }}%"></div>
            </div>
        </div>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Menunggu Konfirmasi</p>
            <p class="mt-2 text-2xl font-black text-slate-900 dark:text-white">{{ $countPending }}</p>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Belum ada konfirmasi dari halaman siswa.</p>
        </div>
        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4 shadow-sm dark:border-blue-900 dark:bg-blue-950/20">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-600">Menunggu Verifikasi</p>
            <p class="mt-2 text-2xl font-black text-slate-900 dark:text-white">{{ $countSubmitted }}</p>
            <p class="mt-1 text-xs text-slate-600 dark:text-slate-300">Sudah daftar ulang, tinggal diverifikasi admin.</p>
        </div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm dark:border-emerald-900 dark:bg-emerald-950/20">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-600">Terverifikasi</p>
            <p class="mt-2 text-2xl font-black text-slate-900 dark:text-white">{{ $countSelesai }}</p>
            <p class="mt-1 text-xs text-slate-600 dark:text-slate-300">Sudah final dan siap lanjut administrasi sekolah.</p>
        </div>
    </div>

    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="mb-4 rounded-xl border border-violet-200 bg-violet-50 p-4 text-sm font-medium text-violet-800 shadow-sm transition-all dark:border-violet-800 dark:bg-violet-900/30 dark:text-violet-400">
            <div class="flex items-center gap-2">
                <x-admin.icon name="check-circle" class="h-5 w-5 shrink-0 text-violet-500" />
                {{ session('message') }}
            </div>
        </div>
    @endif

    @php
        $exportQuery = array_filter([
            'period_id' => $period?->id,
            'scope' => 're-registration',
            'registration_status' => $statusFilter !== '' && $statusFilter !== 'unfinished' ? $statusFilter : null,
            'program_id' => $programFilter !== '' ? $programFilter : null,
            'search' => $search !== '' ? $search : null,
        ], fn ($value) => $value !== null && $value !== '');
    @endphp

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="flex flex-col gap-4 border-b border-slate-100 bg-slate-50/50 p-4 dark:border-slate-800 dark:bg-slate-900/50">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                <div class="grid w-full gap-3 lg:grid-cols-[minmax(0,1.4fr)_220px] xl:max-w-3xl">
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <x-admin.icon name="search" class="h-4 w-4 text-slate-400" />
                        </div>
                        <input wire:model.live.debounce.300ms="search" wire:loading.attr="disabled" wire:target="search,statusFilter,programFilter" type="text" class="block w-full rounded-xl border-slate-300 pl-10 text-sm focus:border-blue-500 focus:ring-blue-500 disabled:cursor-not-allowed disabled:opacity-60" placeholder="Cari nama, no daftar, atau NIPD...">
                    </div>
                    <div>
                        <select wire:model.live="programFilter" wire:loading.attr="disabled" wire:target="search,statusFilter,programFilter" class="block w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 disabled:cursor-not-allowed disabled:opacity-60">
                            <option value="">Semua Jurusan</option>
                            @foreach($programFilterOptions as $programOption)
                                <option value="{{ $programOption->id }}">{{ $programOption->nama_jurusan }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('admin.ppdb.export', array_merge($exportQuery, ['format' => 'xls'])) }}" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-700 transition hover:border-blue-300 hover:text-blue-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                        Export Excel
                    </a>
                    <a href="{{ route('admin.ppdb.export', array_merge($exportQuery, ['format' => 'pdf'])) }}" target="_blank" rel="noopener" class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-900">
                        Export PDF
                    </a>
                </div>
            </div>

            <div class="flex flex-wrap rounded-xl border border-slate-300 bg-slate-100 p-1 dark:border-slate-700 dark:bg-slate-950">
                <button wire:click="$set('statusFilter', '')" wire:loading.attr="disabled" wire:target="statusFilter,programFilter,search" class="px-4 py-1.5 text-xs font-bold rounded-lg transition disabled:cursor-not-allowed disabled:opacity-60 {{ $statusFilter === '' ? 'bg-white shadow relative text-slate-900 dark:bg-slate-800 dark:text-white' : 'text-slate-500 hover:text-slate-700' }}">Semua</button>
                <button wire:click="$set('statusFilter', 'pending')" wire:loading.attr="disabled" wire:target="statusFilter,programFilter,search" class="px-4 py-1.5 text-xs font-bold rounded-lg transition disabled:cursor-not-allowed disabled:opacity-60 {{ $statusFilter === 'pending' ? 'bg-white shadow relative text-slate-900 dark:bg-slate-800 dark:text-white' : 'text-slate-500 hover:text-slate-700' }}">Belum Konfirmasi</button>
                <button wire:click="$set('statusFilter', 'submitted')" wire:loading.attr="disabled" wire:target="statusFilter,programFilter,search" class="px-4 py-1.5 text-xs font-bold rounded-lg transition disabled:cursor-not-allowed disabled:opacity-60 {{ $statusFilter === 'submitted' ? 'bg-white shadow relative text-slate-900 dark:bg-slate-800 dark:text-white' : 'text-slate-500 hover:text-slate-700' }}">Menunggu Verifikasi</button>
                <button wire:click="$set('statusFilter', 'verified')" wire:loading.attr="disabled" wire:target="statusFilter,programFilter,search" class="px-4 py-1.5 text-xs font-bold rounded-lg transition disabled:cursor-not-allowed disabled:opacity-60 {{ $statusFilter === 'verified' ? 'bg-white shadow relative text-slate-900 dark:bg-slate-800 dark:text-white' : 'text-slate-500 hover:text-slate-700' }}">Terverifikasi</button>
                <button wire:click="$set('statusFilter', 'rejected')" wire:loading.attr="disabled" wire:target="statusFilter,programFilter,search" class="px-4 py-1.5 text-xs font-bold rounded-lg transition disabled:cursor-not-allowed disabled:opacity-60 {{ $statusFilter === 'rejected' ? 'bg-white shadow relative text-slate-900 dark:bg-slate-800 dark:text-white' : 'text-slate-500 hover:text-slate-700' }}">Ditolak</button>
                <button wire:click="$set('statusFilter', 'unfinished')" wire:loading.attr="disabled" wire:target="statusFilter,programFilter,search" class="px-4 py-1.5 text-xs font-bold rounded-lg transition disabled:cursor-not-allowed disabled:opacity-60 {{ $statusFilter === 'unfinished' ? 'bg-white shadow relative text-slate-900 dark:bg-slate-800 dark:text-white' : 'text-slate-500 hover:text-slate-700' }}">Belum Final</button>
            </div>

            @if(count($selectedIds) > 0)
                <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 dark:border-emerald-900 dark:bg-emerald-950/20">
                    <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-300">{{ count($selectedIds) }} siswa dipilih untuk verifikasi massal.</p>
                    <button wire:click="bulkVerifySelected" wire:loading.attr="disabled" wire:target="bulkVerifySelected" class="inline-flex items-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60">
                        <span wire:loading.remove wire:target="bulkVerifySelected">Verifikasi Terpilih</span>
                        <span wire:loading wire:target="bulkVerifySelected">Memproses...</span>
                    </button>
                </div>
            @endif
        </div>

        <div class="overflow-x-auto" wire:loading.remove wire:target="search,statusFilter,programFilter,toggleStatus,bulkVerifySelected">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                <thead class="border-b border-slate-200 bg-slate-50/80 text-xs uppercase text-slate-700 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300">
                    <tr>
                        <th scope="col" class="w-10 px-4 py-3 text-center">
                            <input wire:model.live="selectPage" type="checkbox" class="rounded border-slate-300 text-blue-600 focus:ring-blue-600">
                        </th>
                        <th scope="col" class="w-10 px-4 py-3 text-center">#</th>
                        <th scope="col" class="px-4 py-3">Data Siswa Diterima</th>
                        <th scope="col" class="px-4 py-3">Diterima di Jurusan</th>
                        <th scope="col" class="px-4 py-3">Status Daftar Ulang</th>
                        <th scope="col" class="px-4 py-3 text-center">Aksi Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($pendaftar as $index => $item)
                        @php
                            $isSelesai = $item->status_daftar_ulang === 'verified';
                        @endphp
                        <tr wire:key="daftar-ulang-row-{{ $item->id }}" class="{{ $isSelesai ? 'bg-emerald-50/10 dark:bg-emerald-900/10' : 'hover:bg-slate-50 dark:hover:bg-slate-800/50' }} transition">
                            <td class="px-4 py-3 text-center">
                                <input wire:model.live="selectedIds" value="{{ $item->id }}" type="checkbox" class="rounded border-slate-300 text-blue-600 focus:ring-blue-600">
                            </td>
                            <td class="px-4 py-3 text-center font-medium text-slate-400">{{ $pendaftar->firstItem() + $index }}</td>
                            <td class="px-4 py-3">
                                <div class="font-bold {{ $isSelesai ? 'text-slate-500 line-through' : 'text-slate-900 dark:text-white' }}">{{ $item->nama_lengkap }}</div>
                                <div class="mt-0.5 text-[11px] text-slate-500">Nomor: {{ $item->nomor_pendaftaran }} • NIPD: {{ $item->nipd ?: '-' }} • Asal: {{ $item->asal_sekolah }}</div>
                            </td>
                            <td class="px-4 py-3 font-semibold text-slate-700 dark:text-slate-300">
                                {{ $item->programDiterima?->nama_jurusan ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $item->status_daftar_ulang === 'verified' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : ($item->status_daftar_ulang === 'submitted' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' : ($item->status_daftar_ulang === 'rejected' ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300')) }}">
                                    {{ $item->status_daftar_ulang_label }}
                                </div>
                                <p class="mt-1 text-[11px] text-slate-500">
                                    {{ $item->daftar_ulang_at?->format('d M Y H:i') ?? 'Belum ada waktu konfirmasi' }}
                                </p>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button wire:click="toggleStatus({{ $item->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="toggleStatus({{ $item->id }})"
                                        class="inline-flex items-center gap-2 rounded-xl border px-5 py-2 text-sm font-bold shadow-sm transition disabled:cursor-not-allowed disabled:opacity-60 {{ $isSelesai ? 'border-emerald-200 bg-emerald-100 text-emerald-700 hover:bg-emerald-200 dark:border-emerald-800/50 dark:bg-emerald-900/40 dark:text-emerald-400 hover:dark:bg-emerald-900/60' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200' }}">
                                    @if($isSelesai)
                                        <span wire:loading.remove wire:target="toggleStatus({{ $item->id }})" class="inline-flex items-center gap-2">
                                            <x-admin.icon name="check" class="h-4 w-4" /> BATALKAN
                                        </span>
                                    @else
                                        <span wire:loading.remove wire:target="toggleStatus({{ $item->id }})" class="inline-flex items-center gap-2">
                                            <div class="h-4 w-4 rounded-sm border-2 border-slate-400"></div> TANDAI TERVERIFIKASI
                                        </span>
                                    @endif
                                    <span wire:loading wire:target="toggleStatus({{ $item->id }})">Memproses...</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                    <x-admin.icon name="check-circle" class="h-6 w-6" />
                                </div>
                                <h3 class="font-medium text-slate-900 dark:text-white">Tidak ada data daftar ulang yang cocok</h3>
                                <p class="mx-auto mt-1 max-w-sm text-sm text-slate-500">Coba ubah filter status, jurusan, atau kata pencarian. Siswa tetap harus sudah lulus dan sudah ditentukan jurusannya agar muncul di halaman ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-100 bg-white px-4 py-3 dark:border-slate-800 dark:bg-slate-900">
            {{ $pendaftar->links() }}
        </div>

        <div wire:loading.block wire:target="search,statusFilter,programFilter,toggleStatus,bulkVerifySelected" class="p-4">
            <x-admin.skeleton.table :columns="6" :rows="6" />
        </div>
    </div>
</div>
