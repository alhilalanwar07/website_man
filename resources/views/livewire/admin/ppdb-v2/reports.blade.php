<div class="h-full flex flex-col min-h-[calc(100vh-8rem)]">
    <div role="status" aria-live="polite" aria-atomic="true" class="sr-only">
        <span wire:loading wire:target="search,statusFilter,programFilter,trackFilter">Memuat data laporan.</span>
        <span wire:loading wire:target="exportAssessmentExcel,exportApplicantDataExcel,exportApplicantDataPdf">Menyiapkan file laporan untuk diunduh.</span>
    </div>

    <div wire:loading.flex wire:target="search,statusFilter,programFilter,trackFilter" class="mb-4 items-center gap-2 rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm font-semibold text-blue-700 dark:border-blue-900 dark:bg-blue-900/30 dark:text-blue-300">
        <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
        Memuat data laporan...
    </div>

    <!-- Header -->
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between shrink-0">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Laporan PPDB</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola dan ekspor laporan data pendaftar dalam berbagai format penilaian.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <!-- Export Group -->
            <div class="flex flex-col gap-2 sm:flex-row rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-sm">
                <button wire:click="exportAssessmentExcel" wire:loading.attr="disabled" wire:target="exportAssessmentExcel" class="flex items-center px-4 py-2.5 text-sm font-bold text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/40 border-b sm:border-b-0 sm:border-r border-slate-200 dark:border-slate-700 transition disabled:cursor-not-allowed disabled:opacity-60">
                    <span wire:loading.remove wire:target="exportAssessmentExcel" class="inline-flex items-center gap-2">
                        <x-admin.icon name="document-text" class="w-4 h-4" /> Ekspor Penilaian
                    </span>
                    <span wire:loading wire:target="exportAssessmentExcel">Menyiapkan...</span>
                </button>
                <button wire:click="exportApplicantDataExcel" wire:loading.attr="disabled" wire:target="exportApplicantDataExcel" class="flex items-center px-4 py-2.5 text-sm font-bold text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/40 border-b sm:border-b-0 sm:border-r border-slate-200 dark:border-slate-700 transition disabled:cursor-not-allowed disabled:opacity-60">
                    <span wire:loading.remove wire:target="exportApplicantDataExcel" class="inline-flex items-center gap-2">
                        <x-admin.icon name="document-text" class="w-4 h-4" /> Ekspor Excel
                    </span>
                    <span wire:loading wire:target="exportApplicantDataExcel">Menyiapkan...</span>
                </button>
                <button wire:click="exportApplicantDataPdf" wire:loading.attr="disabled" wire:target="exportApplicantDataPdf" class="flex items-center px-4 py-2.5 text-sm font-bold text-red-600 hover:bg-red-50 dark:hover:bg-red-900/40 transition disabled:cursor-not-allowed disabled:opacity-60">
                    <span wire:loading.remove wire:target="exportApplicantDataPdf" class="inline-flex items-center gap-2">
                        <x-admin.icon name="document-text" class="w-4 h-4" /> Ekspor PDF
                    </span>
                    <span wire:loading wire:target="exportApplicantDataPdf">Menyiapkan...</span>
                </button>
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

    <!-- Filters -->
    <div class="mb-6 rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 overflow-hidden">
        <div class="flex flex-col gap-4 border-b border-slate-100 bg-slate-50/50 p-4 dark:border-slate-800 dark:bg-slate-900/50">
            <div class="grid w-full gap-3 lg:grid-cols-[minmax(0,1.4fr)_220px_220px_220px]">
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <x-admin.icon name="search" class="h-4 w-4 text-slate-400" />
                    </div>
                    <input wire:model.live.debounce.300ms="search" wire:loading.attr="disabled" wire:target="search,statusFilter,programFilter,trackFilter" type="text" class="block w-full rounded-xl border-slate-300 pl-10 text-sm focus:border-blue-500 focus:ring-blue-500 disabled:cursor-not-allowed disabled:opacity-60" placeholder="Cari nama, no daftar, atau NISN...">
                </div>
                <div>
                    <select wire:model.live="trackFilter" wire:loading.attr="disabled" wire:target="search,statusFilter,programFilter,trackFilter" class="block w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 disabled:cursor-not-allowed disabled:opacity-60">
                        <option value="">Semua Jalur</option>
                        @foreach($trackFilterOptions as $trackOption)
                            <option value="{{ $trackOption->id }}">{{ $trackOption->nama_jalur }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select wire:model.live="programFilter" wire:loading.attr="disabled" wire:target="search,statusFilter,programFilter,trackFilter" class="block w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 disabled:cursor-not-allowed disabled:opacity-60">
                        <option value="">Semua Program</option>
                        @foreach($programFilterOptions as $programOption)
                            <option value="{{ $programOption->id }}">{{ $programOption->nama_jurusan }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button wire:click="resetFilters" wire:loading.attr="disabled" wire:target="resetFilters" class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 disabled:cursor-not-allowed disabled:opacity-60">
                        <x-admin.icon name="x" class="h-3.5 w-3.5" /> Atur Ulang
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 gap-3 p-4 md:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-800/40">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Total Peserta</p>
                <p class="mt-2 text-2xl font-black text-slate-900 dark:text-white">{{ $countTotal }}</p>
            </div>
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-3 dark:border-blue-900 dark:bg-blue-950/20">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-600">Periode Aktif</p>
                <p class="mt-2 text-lg font-black text-slate-900 dark:text-white">{{ $period?->tahun_ajaran ?? '-' }}</p>
            </div>
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 dark:border-emerald-900 dark:bg-emerald-950/20">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-600">Status</p>
                <p class="mt-2 text-lg font-black text-slate-900 dark:text-white">{{ $period?->is_active ? 'Aktif' : 'Non-Aktif' }}</p>
            </div>
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-3 dark:border-amber-900 dark:bg-amber-950/20">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-amber-600">Nama Periode</p>
                <p class="mt-2 text-sm font-bold text-slate-900 dark:text-white truncate">{{ $period?->nama_periode ?? 'Tidak ada' }}</p>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 flex flex-col flex-1">
        <div wire:loading.block wire:target="search,statusFilter,programFilter,trackFilter" class="p-4">
            <x-admin.skeleton.table :columns="6" :rows="8" />
        </div>

        <div wire:loading.remove wire:target="search,statusFilter,programFilter,trackFilter" class="overflow-x-auto flex-1">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                <thead class="border-b border-slate-200 bg-slate-50/80 text-xs uppercase text-slate-700 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 sticky top-0 z-10">
                    <tr>
                        <th scope="col" class="w-10 px-4 py-3 text-center">#</th>
                        <th scope="col" class="px-4 py-3">Nama Pendaftar</th>
                        <th scope="col" class="px-4 py-3">No. Pendaftaran</th>
                        <th scope="col" class="px-4 py-3">NISN</th>
                        <th scope="col" class="px-4 py-3">Jalur</th>
                        <th scope="col" class="px-4 py-3">Program Diterima</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($applicants as $index => $applicant)
                        <tr wire:key="report-row-{{ $applicant->id }}" class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                            <td class="px-4 py-3 text-center font-medium text-slate-400">{{ $applicants->firstItem() + $index }}</td>
                            <td class="px-4 py-3 font-bold text-slate-900 dark:text-white">{{ $applicant->nama_lengkap }}</td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $applicant->nomor_pendaftaran }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-slate-600 dark:text-slate-300">{{ $applicant->nisn ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm">{{ $applicant->track?->nama_jalur ?? '-' }}</td>
                            <td class="px-4 py-3 font-semibold text-blue-600 dark:text-blue-400">{{ $applicant->programDiterima?->nama_jurusan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center">
                                <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400 dark:bg-slate-800">
                                    <x-admin.icon name="document-text" class="h-6 w-6" />
                                </div>
                                <h3 class="font-medium text-slate-900 dark:text-white">Tidak ada data yang cocok</h3>
                                <p class="mx-auto mt-1 max-w-sm text-sm text-slate-500">Coba ubah filter pencarian atau periode aktif.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-100 bg-white px-4 py-3 dark:border-slate-800 dark:bg-slate-900">
            {{ $applicants->links() }}
        </div>
    </div>
</div>
