<div class="space-y-6">
    <div role="status" aria-live="polite" aria-atomic="true" class="sr-only">
        <span wire:loading wire:target="period">Memuat konfigurasi periode PPDB.</span>
        <span wire:loading wire:target="openActionModal,confirmAction">Memproses perubahan pengaturan PPDB.</span>
        <span wire:loading wire:target="setTab">Memuat tab pengaturan.</span>
    </div>

    <div wire:loading.flex wire:target="period,openActionModal,confirmAction,setTab" class="items-center gap-2 rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm font-semibold text-blue-700 dark:border-blue-900 dark:bg-blue-900/30 dark:text-blue-300">
        <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
        Menyinkronkan pengaturan PPDB...
    </div>

    @php($periodQuery = $selectedPeriodId ? ['periode' => $selectedPeriodId] : [])

    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-blue-500">Pengaturan PPDB V2</p>
            <h1 class="mt-2 text-2xl font-black text-slate-900 dark:text-white">Panel Konfigurasi Berbasis Tab</h1>
            <p class="mt-2 max-w-3xl text-sm text-slate-500 dark:text-slate-400">
                Semua konfigurasi operasional PPDB dipusatkan di halaman ini. Tiap tab mewakili domain data yang berbeda agar panitia lebih cepat melakukan pembaruan.
            </p>
        </div>

        <div class="flex flex-wrap gap-3">
            <select wire:model.live="period" wire:loading.attr="disabled" wire:target="period" class="min-w-[320px] rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950 disabled:cursor-not-allowed disabled:opacity-60">
                @foreach($availablePeriods as $periodOption)
                    <option wire:key="settings-period-option-{{ $periodOption->id }}" value="{{ $periodOption->id }}">{{ $periodOption->full_label }}</option>
                @endforeach
            </select>
            <a href="{{ route('admin.ppdb.dashboard', $periodQuery) }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:border-blue-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">Dashboard</a>
            <a href="{{ route('admin.ppdb.pendaftar', $periodQuery) }}" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-blue-700">Kelola Pendaftar</a>
        </div>
    </div>

    <div wire:loading.block wire:target="period" class="space-y-4">
        <x-admin.skeleton.card-grid :cards="5" />
        <x-admin.skeleton.table :columns="2" :rows="6" />
    </div>

    <div wire:loading.remove wire:target="period">
        @if($activePeriod)
            @php($periodStatusLabel = match ($activePeriod->status) {
                'draft' => 'Draf',
                'published' => 'Dipublikasikan',
                'closed' => 'Ditutup',
                'archived' => 'Diarsipkan',
                default => str($activePeriod->status)->title(),
            })
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Periode</p>
                    <p class="mt-2 text-lg font-black text-slate-900 dark:text-white">{{ $activePeriod->nama_periode }}</p>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $activePeriod->tahun_ajaran }} · {{ $activePeriod->gelombang_label }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Status Frontend</p>
                    <p class="mt-2 text-lg font-black {{ $activePeriod->isRegistrationOpen() ? 'text-emerald-600' : 'text-slate-900 dark:text-white' }}">{{ $activePeriod->isRegistrationOpen() ? 'Registrasi Terbuka' : 'Registrasi Tidak Terbuka' }}</p>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Berdasarkan status periode + rentang tanggal pendaftaran</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Pengumuman</p>
                    <p class="mt-2 text-lg font-black {{ $activePeriod->isAnnouncementPublished() ? 'text-blue-600' : 'text-slate-900 dark:text-white' }}">{{ $activePeriod->isAnnouncementPublished() ? 'Dipublikasikan' : 'Draf' }}</p>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Status hasil seleksi di halaman cek status</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Kontak Aktif</p>
                    <p class="mt-2 text-lg font-black text-slate-900 dark:text-white">{{ $activePeriod->contactPersons->where('is_active', true)->count() }}</p>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">PIC bantuan periode ini</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Kuota Aktif</p>
                    <p class="mt-2 text-lg font-black text-slate-900 dark:text-white">{{ $quotaOverview->count() }} kombinasi</p>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Kombinasi jalur-peminatan aktif</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 xl:grid-cols-[1.2fr_1fr]">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <p class="text-xs font-bold uppercase tracking-[0.25em] text-slate-400">Kontrol Periode</p>
                    <h3 class="mt-2 text-lg font-black text-slate-900 dark:text-white">Aktivasi dan tata kelola periode</h3>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                        Periode aktif default dipakai sebagai acuan bawaan admin/frontend ketika parameter periode tidak dikirim.
                    </p>
                    <div class="mt-4 flex flex-wrap gap-3">
                        <button wire:click="openActionModal('activate-period')" wire:loading.attr="disabled" wire:target="openActionModal" type="button" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-900 disabled:cursor-not-allowed disabled:opacity-60">
                            Jadikan Aktif Bawaan
                        </button>
                        <button
                            wire:click="openActionModal('archive-period')"
                            wire:loading.attr="disabled"
                            wire:target="openActionModal"
                            type="button"
                            class="rounded-xl border border-amber-300 bg-amber-50 px-4 py-2.5 text-sm font-bold text-amber-700 transition hover:bg-amber-100 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            Arsipkan dari Frontend
                        </button>
                        <button
                            wire:click="openActionModal('delete-period')"
                            wire:loading.attr="disabled"
                            wire:target="openActionModal"
                            type="button"
                            class="rounded-xl border border-rose-300 bg-rose-50 px-4 py-2.5 text-sm font-bold text-rose-700 transition hover:bg-rose-100 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            Hapus Periode
                        </button>
                        <span class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-600 dark:border-slate-700 dark:bg-slate-800/60 dark:text-slate-300">
                            Status: {{ $periodStatusLabel }}{{ $activePeriod->is_active ? ' · Aktif' : '' }}
                        </span>
                    </div>
                </div>

                <div class="rounded-2xl border border-dashed border-blue-200 bg-blue-50 p-5 dark:border-blue-900 dark:bg-blue-950/20">
                    <p class="text-xs font-bold uppercase tracking-[0.25em] text-blue-600">Sinkron Dokumen Resmi</p>
                    <ul class="mt-3 space-y-2 text-sm text-slate-700 dark:text-slate-200">
                        <li>Kontak person dipakai pada lampiran PDF formulir.</li>
                        <li>Tanggal penting menyesuaikan timeline periode aktif.</li>
                        <li>Persyaratan berkas tampil di panduan verifikasi.</li>
                        <li>Warna map peminatan menjadi ketentuan cetak resmi.</li>
                    </ul>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex flex-wrap gap-2">
                    @foreach($tabOptions as $tabOption)
                        <button
                            type="button"
                            wire:key="ppdb-settings-tab-{{ $tabOption['key'] }}"
                            wire:click="setTab('{{ $tabOption['key'] }}')"
                            class="inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-sm font-semibold transition {{ $tab === $tabOption['key']
                                ? 'border-blue-600 bg-blue-600 text-white shadow-sm'
                                : 'border-slate-200 bg-white text-slate-600 hover:border-blue-300 hover:text-blue-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200' }}"
                        >
                            <span>{{ $tabOption['label'] }}</span>
                            @if($tabOption['count'] !== null)
                                <span class="rounded-full px-2 py-0.5 text-xs {{ $tab === $tabOption['key'] ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' }}">{{ $tabOption['count'] }}</span>
                            @endif
                        </button>
                    @endforeach
                </div>
                <p class="px-1 pt-3 text-xs text-slate-500 dark:text-slate-400">
                    {{ collect($tabOptions)->firstWhere('key', $tab)['description'] ?? 'Kelola pengaturan sesuai tab yang dipilih.' }}
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                @if($tab === 'periode')
                    <div class="space-y-6">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-700 dark:bg-slate-800/40">
                            <p class="text-sm font-bold text-slate-900 dark:text-white">Buat Periode atau Gelombang Baru</p>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Siapkan tahun ajaran berikutnya tanpa mengubah histori periode lama.</p>

                            <form wire:submit.prevent="openActionModal('create-period')" class="mt-4 space-y-4">
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                                    <div class="xl:col-span-2">
                                        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Nama Periode</label>
                                        <input wire:model.blur="newPeriodForm.nama_periode" type="text" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                                        @error('newPeriodForm.nama_periode') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Tahun Ajaran</label>
                                        <input wire:model.blur="newPeriodForm.tahun_ajaran" type="text" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                                        @error('newPeriodForm.tahun_ajaran') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Status</label>
                                        <select wire:model="newPeriodForm.status" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                                            <option value="draft">Draf</option>
                                            <option value="published">Dipublikasikan</option>
                                            <option value="closed">Ditutup</option>
                                            <option value="archived">Diarsipkan</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Tahun Mulai</label>
                                        <input wire:model.blur="newPeriodForm.tahun_mulai" type="number" min="2020" max="2100" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                                        @error('newPeriodForm.tahun_mulai') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Tahun Selesai</label>
                                        <input wire:model.blur="newPeriodForm.tahun_selesai" type="number" min="2020" max="2101" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                                        @error('newPeriodForm.tahun_selesai') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Nomor Gelombang</label>
                                        <input wire:model.blur="newPeriodForm.gelombang_ke" type="number" min="1" max="20" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Label Gelombang</label>
                                        <input wire:model.blur="newPeriodForm.gelombang_label" type="text" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Mulai Pendaftaran</label>
                                        <input wire:model.blur="newPeriodForm.tanggal_mulai_pendaftaran" type="date" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Selesai Pendaftaran</label>
                                        <input wire:model.blur="newPeriodForm.tanggal_selesai_pendaftaran" type="date" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Tanggal Pengumuman</label>
                                        <input wire:model.blur="newPeriodForm.tanggal_pengumuman" type="date" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Mulai Daftar Ulang</label>
                                        <input wire:model.blur="newPeriodForm.tanggal_mulai_daftar_ulang" type="date" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Selesai Daftar Ulang</label>
                                        <input wire:model.blur="newPeriodForm.tanggal_selesai_daftar_ulang" type="date" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                                    </div>
                                </div>

                                <div>
                                    <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Deskripsi</label>
                                    <textarea wire:model.blur="newPeriodForm.deskripsi" rows="3" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800"></textarea>
                                </div>

                                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                                    <label class="flex items-center justify-between gap-4 rounded-xl border border-slate-200 bg-white px-4 py-3 dark:border-slate-700 dark:bg-slate-900">
                                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Salin jalur, kuota, dan info pendukung dari periode terpilih</span>
                                        <input wire:model="newPeriodForm.clone_template" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    </label>
                                    <label class="flex items-center justify-between gap-4 rounded-xl border border-slate-200 bg-white px-4 py-3 dark:border-slate-700 dark:bg-slate-900">
                                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Jadikan aktif default setelah dibuat</span>
                                        <input wire:model="newPeriodForm.is_active" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    </label>
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit" wire:loading.attr="disabled" wire:target="openActionModal" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60">
                                        <span wire:loading.remove wire:target="openActionModal">Buat Periode Baru</span>
                                        <span wire:loading wire:target="openActionModal">Memproses...</span>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-700 dark:bg-slate-800/40">
                            <p class="text-sm font-bold text-slate-900 dark:text-white">Pengaturan Periode dan Publikasi</p>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Kelola jadwal pendaftaran, pengumuman, dan daftar ulang agar sinkron dengan portal frontend.</p>

                            <form wire:submit.prevent="openActionModal('save-period-settings')" class="mt-4 space-y-4">
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                                    <div class="xl:col-span-2">
                                        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Nama Periode</label>
                                        <input wire:model.blur="periodForm.nama_periode" type="text" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                                        @error('periodForm.nama_periode') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Tahun Ajaran</label>
                                        <input wire:model.blur="periodForm.tahun_ajaran" type="text" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                                        @error('periodForm.tahun_ajaran') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Tahun Mulai</label>
                                        <input wire:model.blur="periodForm.tahun_mulai" type="number" min="2020" max="2100" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                                        @error('periodForm.tahun_mulai') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Tahun Selesai</label>
                                        <input wire:model.blur="periodForm.tahun_selesai" type="number" min="2020" max="2101" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                                        @error('periodForm.tahun_selesai') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Nomor Gelombang</label>
                                        <input wire:model.blur="periodForm.gelombang_ke" type="number" min="1" max="20" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                                        @error('periodForm.gelombang_ke') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Label Gelombang</label>
                                        <input wire:model.blur="periodForm.gelombang_label" type="text" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                                        @error('periodForm.gelombang_label') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Status Pengumuman</label>
                                        <select wire:model="periodForm.status_pengumuman" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                                            <option value="draft">Draf</option>
                                            <option value="published">Dipublikasikan</option>
                                        </select>
                                        @error('periodForm.status_pengumuman') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Status Periode</label>
                                        <select wire:model="periodForm.status" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                                            <option value="draft">Draf</option>
                                            <option value="published">Dipublikasikan</option>
                                            <option value="closed">Ditutup</option>
                                            <option value="archived">Diarsipkan</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Mulai Pendaftaran</label>
                                        <input wire:model.blur="periodForm.tanggal_mulai_pendaftaran" type="date" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                                        @error('periodForm.tanggal_mulai_pendaftaran') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Selesai Pendaftaran</label>
                                        <input wire:model.blur="periodForm.tanggal_selesai_pendaftaran" type="date" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                                        @error('periodForm.tanggal_selesai_pendaftaran') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Tanggal Pengumuman</label>
                                        <input wire:model.blur="periodForm.tanggal_pengumuman" type="date" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                                        @error('periodForm.tanggal_pengumuman') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Mulai Daftar Ulang</label>
                                        <input wire:model.blur="periodForm.tanggal_mulai_daftar_ulang" type="date" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                                        @error('periodForm.tanggal_mulai_daftar_ulang') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Selesai Daftar Ulang</label>
                                        <input wire:model.blur="periodForm.tanggal_selesai_daftar_ulang" type="date" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                                        @error('periodForm.tanggal_selesai_daftar_ulang') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Catatan Pengumuman</label>
                                        <textarea wire:model.blur="periodForm.catatan_pengumuman" rows="4" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800"></textarea>
                                        @error('periodForm.catatan_pengumuman') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Deskripsi Periode</label>
                                        <textarea wire:model.blur="periodForm.deskripsi" rows="4" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800"></textarea>
                                        @error('periodForm.deskripsi') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                                <div class="rounded-2xl border border-emerald-200 bg-emerald-50/70 p-5 dark:border-emerald-900 dark:bg-emerald-950/20">
                                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                        <div class="max-w-2xl">
                                            <p class="text-sm font-bold text-emerald-900 dark:text-emerald-200">Akses Grup WhatsApp Siswa Baru</p>
                                            <p class="mt-1 text-xs leading-relaxed text-emerald-800/80 dark:text-emerald-200/80">
                                                Tautan ini akan ditampilkan kepada siswa yang sudah lulus dan sudah mengirim konfirmasi daftar ulang. Portal siswa menampilkan tombol gabung dan QR code agar instruksinya lebih jelas.
                                            </p>
                                        </div>
                                        @if(!empty($periodForm['student_whatsapp_group_url']))
                                            <a href="{{ str_starts_with($periodForm['student_whatsapp_group_url'], 'http') ? $periodForm['student_whatsapp_group_url'] : 'https://' . ltrim($periodForm['student_whatsapp_group_url'], '/') }}" target="_blank" rel="noopener" class="inline-flex items-center rounded-xl border border-emerald-300 bg-white px-4 py-2 text-xs font-bold text-emerald-700 transition hover:bg-emerald-100 dark:border-emerald-800 dark:bg-slate-900 dark:text-emerald-300">
                                                Cek Tautan Grup
                                            </a>
                                        @endif
                                    </div>
                                    <div class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-2">
                                        <div>
                                            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Label Tombol / Judul Grup</label>
                                            <input wire:model.blur="periodForm.student_whatsapp_group_label" type="text" placeholder="Grup WhatsApp Siswa Baru" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Opsional. Jika kosong, sistem memakai label bawaan yang tetap mudah dipahami siswa.</p>
                                            @error('periodForm.student_whatsapp_group_label') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Tautan Invite Grup WhatsApp</label>
                                            <input wire:model.blur="periodForm.student_whatsapp_group_url" type="text" placeholder="https://chat.whatsapp.com/..." class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Boleh tempel tautan penuh atau domain WhatsApp tanpa `https://`.</p>
                                            @error('periodForm.student_whatsapp_group_url') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                </div>
                                <label class="flex items-center justify-between gap-4 rounded-xl border border-slate-200 bg-white px-4 py-3 dark:border-slate-700 dark:bg-slate-900">
                                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Gunakan sebagai periode aktif bawaan</span>
                                    <input wire:model="periodForm.is_active" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                </label>
                                <div class="flex justify-end">
                                    <button type="submit" wire:loading.attr="disabled" wire:target="openActionModal" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60">
                                        <span wire:loading.remove wire:target="openActionModal">Simpan Pengaturan Periode</span>
                                        <span wire:loading wire:target="openActionModal">Memproses...</span>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-700 dark:bg-slate-800/40">
                            <p class="text-sm font-bold text-slate-900 dark:text-white">Pengaturan NIPD Otomatis</p>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">NIPD akan diberikan otomatis ketika peserta dinyatakan lulus, sudah ditetapkan peminatan, dan daftar ulang diverifikasi.</p>

                            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                                <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 dark:border-slate-700 dark:bg-slate-900">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Tersimpan di Periode</p>
                                    <p class="mt-2 text-2xl font-black text-slate-900 dark:text-white">{{ number_format($configuredLastNipd, 0, ',', '.') }}</p>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Nilai manual saat ini</p>
                                </div>
                                <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 dark:border-slate-700 dark:bg-slate-900">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Terakhir Terpakai</p>
                                    <p class="mt-2 text-2xl font-black text-slate-900 dark:text-white">{{ number_format($lastAssignedNipd, 0, ',', '.') }}</p>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">NIPD terbesar dari data pendaftar</p>
                                </div>
                                <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 dark:border-slate-700 dark:bg-slate-900">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">NIPD Efektif</p>
                                    <p class="mt-2 text-2xl font-black text-slate-900 dark:text-white">{{ number_format($effectiveLastNipd, 0, ',', '.') }}</p>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Acuan nomor terakhir final</p>
                                </div>
                                <div class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 dark:border-blue-900 dark:bg-blue-950/20">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-blue-600">Preview Berikutnya</p>
                                    <p class="mt-2 text-2xl font-black text-blue-700 dark:text-blue-300">{{ number_format($nextNipdPreview, 0, ',', '.') }}</p>
                                    <p class="mt-1 text-xs text-blue-700/80 dark:text-blue-300/80">Teraplikasi saat verifikasi daftar ulang</p>
                                </div>
                            </div>

                            <form wire:submit.prevent="openActionModal('save-nipd-settings')" class="mt-4 space-y-4">
                                <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Set Manual NIPD Terakhir</label>
                                        <input wire:model.blur="nipdSettings.last_nipd" type="number" min="0" step="1" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                                        @error('nipdSettings.last_nipd') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Gunakan nilai terbesar terakhir. Sistem akan melanjutkan ke nomor setelahnya.</p>
                                    </div>
                                    <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                                        <p class="font-semibold text-slate-900 dark:text-white">Distribusi NIPD Periode Ini</p>
                                        <p class="mt-2">Total peserta yang sudah memiliki NIPD: <span class="font-bold">{{ number_format($nipdAssignedCount, 0, ',', '.') }}</span></p>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">NIPD tidak akan dihapus otomatis ketika verifikasi daftar ulang dibatalkan.</p>
                                    </div>
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit" wire:loading.attr="disabled" wire:target="openActionModal" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60">
                                        <span wire:loading.remove wire:target="openActionModal">Simpan Pengaturan NIPD</span>
                                        <span wire:loading wire:target="openActionModal">Memproses...</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @elseif($tab === 'jalur')
                    <form wire:submit.prevent="openActionModal('save-track-settings')" class="space-y-4">
                        <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
                            @forelse($activePeriod->tracks as $track)
                                <div wire:key="track-setting-{{ $track->id }}" class="space-y-4 rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-700 dark:bg-slate-800/40">
                                    <div>
                                        <p class="text-xs font-bold uppercase tracking-[0.25em] text-slate-400">{{ $track->slug }}</p>
                                        <h3 class="mt-2 text-lg font-black text-slate-900 dark:text-white">{{ $track->nama_jalur }}</h3>
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Urutan Tampil</label>
                                        <input wire:model.blur="trackSettings.{{ $track->id }}.urutan" type="number" min="1" max="99" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-900">
                                        @error('trackSettings.' . $track->id . '.urutan') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                    </div>
                                    <label class="flex items-center justify-between gap-4 rounded-xl border border-slate-200 bg-white px-4 py-3 dark:border-slate-700 dark:bg-slate-900">
                                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Tampilkan di portal</span>
                                        <input wire:model="trackSettings.{{ $track->id }}.status_tampil" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    </label>
                                    <label class="flex items-center justify-between gap-4 rounded-xl border border-slate-200 bg-white px-4 py-3 dark:border-slate-700 dark:bg-slate-900">
                                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Butuh verifikasi khusus</span>
                                        <input wire:model="trackSettings.{{ $track->id }}.requires_verification" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    </label>
                                </div>
                            @empty
                                <div class="xl:col-span-3 rounded-xl border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
                                    Belum ada jalur PPDB pada periode ini.
                                </div>
                            @endforelse
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" wire:loading.attr="disabled" wire:target="openActionModal" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60">
                                <span wire:loading.remove wire:target="openActionModal">Simpan Pengaturan Jalur</span>
                                <span wire:loading wire:target="openActionModal">Memproses...</span>
                            </button>
                        </div>
                    </form>
                @elseif($tab === 'kuota')
                    <form wire:submit.prevent="openActionModal('save-quota-settings')" class="space-y-4">
                        <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                            @forelse($activePeriod->quotas as $quota)
                                <div wire:key="quota-setting-{{ $quota->id }}" class="rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-700 dark:bg-slate-800/40">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <p class="text-xs font-bold uppercase tracking-[0.25em] text-slate-400">{{ $quota->track?->nama_jalur ?? 'Tanpa Jalur' }}</p>
                                            <h3 class="mt-2 text-lg font-black text-slate-900 dark:text-white">{{ $quota->programKeahlian?->nama_jurusan ?? 'Program tidak ditemukan' }}</h3>
                                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Terisi {{ $quota->kuota_terisi }} dari {{ $quota->kuota }} kuota</p>
                                        </div>
                                        <label class="flex items-center gap-2 text-xs font-bold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">
                                            Aktif
                                            <input wire:model="quotaSettings.{{ $quota->id }}.status_aktif" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                        </label>
                                    </div>
                                    <div class="mt-4">
                                        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Kuota</label>
                                        <input wire:model.blur="quotaSettings.{{ $quota->id }}.kuota" type="number" min="0" max="500" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-900">
                                        @error('quotaSettings.' . $quota->id . '.kuota') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            @empty
                                <div class="xl:col-span-2 rounded-xl border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
                                    Belum ada data kuota pada periode ini.
                                </div>
                            @endforelse
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" wire:loading.attr="disabled" wire:target="openActionModal" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60">
                                <span wire:loading.remove wire:target="openActionModal">Simpan Pengaturan Kuota</span>
                                <span wire:loading wire:target="openActionModal">Memproses...</span>
                            </button>
                        </div>
                    </form>
                @elseif($tab === 'kontak')
                    <form wire:submit.prevent="openActionModal('save-contact-settings')" class="space-y-4">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-bold text-slate-900 dark:text-white">Kontak Person Panitia</p>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Data ini digunakan pada lampiran PDF dan alur bantuan peserta.</p>
                            </div>
                            <button type="button" wire:click="addContactPersonRow" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-700 transition hover:border-blue-300 hover:text-blue-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                                + Tambah Kontak
                            </button>
                        </div>

                        <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-700">
                            <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-700">
                                <thead class="bg-slate-50 dark:bg-slate-800/60">
                                    <tr>
                                        <th class="px-3 py-3 text-left text-xs font-bold uppercase tracking-[0.2em] text-slate-500">No</th>
                                        <th class="px-3 py-3 text-left text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Nama & Jabatan</th>
                                        <th class="px-3 py-3 text-left text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Kontak</th>
                                        <th class="px-3 py-3 text-left text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Status</th>
                                        <th class="px-3 py-3 text-right text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-700 dark:bg-slate-900">
                                    @foreach($contactPersonSettings as $index => $row)
                                        <tr wire:key="contact-person-row-{{ $row['id'] ?? 'new-' . $index }}">
                                            <td class="px-3 py-3 text-slate-500">{{ $index + 1 }}
                                                <input type="hidden" wire:model="contactPersonSettings.{{ $index }}.id">
                                            </td>
                                            <td class="px-3 py-3 align-top">
                                                <input wire:model.blur="contactPersonSettings.{{ $index }}.nama" type="text" placeholder="Nama kontak" class="mb-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800">
                                                @error('contactPersonSettings.' . $index . '.nama') <p class="mb-2 text-xs text-red-500">{{ $message }}</p> @enderror
                                                <input wire:model.blur="contactPersonSettings.{{ $index }}.jabatan" type="text" placeholder="Jabatan (contoh: Layanan PPDB)" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800">
                                                @error('contactPersonSettings.' . $index . '.jabatan') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                            </td>
                                            <td class="px-3 py-3 align-top">
                                                <input wire:model.blur="contactPersonSettings.{{ $index }}.nomor_telepon" type="text" placeholder="Telepon" class="mb-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800">
                                                <input wire:model.blur="contactPersonSettings.{{ $index }}.nomor_whatsapp" type="text" placeholder="WhatsApp" class="mb-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800">
                                                <input wire:model.blur="contactPersonSettings.{{ $index }}.email" type="email" placeholder="Email" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800">
                                                @error('contactPersonSettings.' . $index . '.nomor_telepon') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                                @error('contactPersonSettings.' . $index . '.email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                            </td>
                                            <td class="px-3 py-3 align-top">
                                                <button type="button" wire:click="setPrimaryContactPerson({{ $index }})" class="mb-3 rounded-lg border px-3 py-1.5 text-xs font-bold transition {{ ($row['is_primary'] ?? false) ? 'border-blue-600 bg-blue-600 text-white' : 'border-slate-200 bg-white text-slate-600 hover:border-blue-300 hover:text-blue-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300' }}">
                                                    {{ ($row['is_primary'] ?? false) ? 'Kontak Utama' : 'Jadikan Utama' }}
                                                </button>
                                                <label class="flex items-center gap-2 text-xs font-semibold text-slate-600 dark:text-slate-300">
                                                    <input wire:model="contactPersonSettings.{{ $index }}.is_active" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                                    Aktif
                                                </label>
                                            </td>
                                            <td class="px-3 py-3 text-right align-top">
                                                <button type="button" wire:click="removeContactPersonRow({{ $index }})" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-700 transition hover:bg-rose-100">
                                                    Hapus
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" wire:loading.attr="disabled" wire:target="openActionModal" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60">
                                <span wire:loading.remove wire:target="openActionModal">Simpan Kontak Person</span>
                                <span wire:loading wire:target="openActionModal">Memproses...</span>
                            </button>
                        </div>
                    </form>
                @elseif($tab === 'tanggal')
                    <form wire:submit.prevent="openActionModal('save-important-date-settings')" class="space-y-4">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-bold text-slate-900 dark:text-white">Timeline Tanggal Penting</p>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Digunakan pada informasi publik dan lampiran formulir resmi.</p>
                            </div>
                            <button type="button" wire:click="addImportantDateRow" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-700 transition hover:border-blue-300 hover:text-blue-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                                + Tambah Tanggal
                            </button>
                        </div>

                        <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-700">
                            <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-700">
                                <thead class="bg-slate-50 dark:bg-slate-800/60">
                                    <tr>
                                        <th class="px-3 py-3 text-left text-xs font-bold uppercase tracking-[0.2em] text-slate-500">No</th>
                                        <th class="px-3 py-3 text-left text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Kegiatan</th>
                                        <th class="px-3 py-3 text-left text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Tanggal</th>
                                        <th class="px-3 py-3 text-left text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Keterangan</th>
                                        <th class="px-3 py-3 text-left text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Status</th>
                                        <th class="px-3 py-3 text-right text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-700 dark:bg-slate-900">
                                    @foreach($importantDateSettings as $index => $row)
                                        <tr wire:key="important-date-row-{{ $row['id'] ?? 'new-' . $index }}">
                                            <td class="px-3 py-3 text-slate-500">{{ $index + 1 }}
                                                <input type="hidden" wire:model="importantDateSettings.{{ $index }}.id">
                                            </td>
                                            <td class="px-3 py-3 align-top">
                                                <input wire:model.blur="importantDateSettings.{{ $index }}.label" type="text" placeholder="Nama kegiatan" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800">
                                                @error('importantDateSettings.' . $index . '.label') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                            </td>
                                            <td class="px-3 py-3 align-top">
                                                <input wire:model.blur="importantDateSettings.{{ $index }}.tanggal_mulai" type="date" class="mb-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800">
                                                <input wire:model.blur="importantDateSettings.{{ $index }}.tanggal_selesai" type="date" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800" placeholder="Opsional">
                                                @error('importantDateSettings.' . $index . '.tanggal_mulai') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                                @error('importantDateSettings.' . $index . '.tanggal_selesai') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                            </td>
                                            <td class="px-3 py-3 align-top">
                                                <input wire:model.blur="importantDateSettings.{{ $index }}.keterangan" type="text" placeholder="Opsional" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800">
                                                @error('importantDateSettings.' . $index . '.keterangan') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                            </td>
                                            <td class="px-3 py-3 align-top">
                                                <label class="flex items-center gap-2 text-xs font-semibold text-slate-600 dark:text-slate-300">
                                                    <input wire:model="importantDateSettings.{{ $index }}.is_active" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                                    Aktif
                                                </label>
                                            </td>
                                            <td class="px-3 py-3 text-right align-top">
                                                <button type="button" wire:click="removeImportantDateRow({{ $index }})" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-700 transition hover:bg-rose-100">
                                                    Hapus
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" wire:loading.attr="disabled" wire:target="openActionModal" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60">
                                <span wire:loading.remove wire:target="openActionModal">Simpan Tanggal Penting</span>
                                <span wire:loading wire:target="openActionModal">Memproses...</span>
                            </button>
                        </div>
                    </form>
                @elseif($tab === 'persyaratan')
                    <form wire:submit.prevent="openActionModal('save-document-requirement-settings')" class="space-y-4">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-bold text-slate-900 dark:text-white">Persyaratan Berkas</p>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Daftar berkas yang harus dibawa saat verifikasi/daftar ulang.</p>
                            </div>
                            <button type="button" wire:click="addDocumentRequirementRow" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-700 transition hover:border-blue-300 hover:text-blue-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                                + Tambah Persyaratan
                            </button>
                        </div>

                        <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-700">
                            <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-700">
                                <thead class="bg-slate-50 dark:bg-slate-800/60">
                                    <tr>
                                        <th class="px-3 py-3 text-left text-xs font-bold uppercase tracking-[0.2em] text-slate-500">No</th>
                                        <th class="px-3 py-3 text-left text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Nama Berkas</th>
                                        <th class="px-3 py-3 text-left text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Keterangan</th>
                                        <th class="px-3 py-3 text-left text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Sifat</th>
                                        <th class="px-3 py-3 text-left text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Status</th>
                                        <th class="px-3 py-3 text-right text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-700 dark:bg-slate-900">
                                    @foreach($documentRequirementSettings as $index => $row)
                                        <tr wire:key="document-requirement-row-{{ $row['id'] ?? 'new-' . $index }}">
                                            <td class="px-3 py-3 text-slate-500">{{ $index + 1 }}
                                                <input type="hidden" wire:model="documentRequirementSettings.{{ $index }}.id">
                                            </td>
                                            <td class="px-3 py-3 align-top">
                                                <input wire:model.blur="documentRequirementSettings.{{ $index }}.nama_berkas" type="text" placeholder="Nama berkas" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800">
                                                @error('documentRequirementSettings.' . $index . '.nama_berkas') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                            </td>
                                            <td class="px-3 py-3 align-top">
                                                <input wire:model.blur="documentRequirementSettings.{{ $index }}.keterangan" type="text" placeholder="Opsional" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800">
                                                @error('documentRequirementSettings.' . $index . '.keterangan') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                            </td>
                                            <td class="px-3 py-3 align-top">
                                                <label class="flex items-center gap-2 text-xs font-semibold text-slate-600 dark:text-slate-300">
                                                    <input wire:model="documentRequirementSettings.{{ $index }}.wajib" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                                    Wajib
                                                </label>
                                            </td>
                                            <td class="px-3 py-3 align-top">
                                                <label class="flex items-center gap-2 text-xs font-semibold text-slate-600 dark:text-slate-300">
                                                    <input wire:model="documentRequirementSettings.{{ $index }}.is_active" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                                    Aktif
                                                </label>
                                            </td>
                                            <td class="px-3 py-3 text-right align-top">
                                                <button type="button" wire:click="removeDocumentRequirementRow({{ $index }})" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-700 transition hover:bg-rose-100">
                                                    Hapus
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" wire:loading.attr="disabled" wire:target="openActionModal" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60">
                                <span wire:loading.remove wire:target="openActionModal">Simpan Persyaratan Berkas</span>
                                <span wire:loading wire:target="openActionModal">Memproses...</span>
                            </button>
                        </div>
                    </form>
                @elseif($tab === 'warna-map')
                    <form wire:submit.prevent="openActionModal('save-map-color-settings')" class="space-y-4">
                        <div>
                            <p class="text-sm font-bold text-slate-900 dark:text-white">Warna Map per Program Keahlian</p>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Aturan ini dipakai sebagai acuan resmi pada lampiran informasi formulir.</p>
                        </div>

                        <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-700">
                            <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-700">
                                <thead class="bg-slate-50 dark:bg-slate-800/60">
                                    <tr>
                                        <th class="px-3 py-3 text-left text-xs font-bold uppercase tracking-[0.2em] text-slate-500">No</th>
                                        <th class="px-3 py-3 text-left text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Program Keahlian</th>
                                        <th class="px-3 py-3 text-left text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Warna Map</th>
                                        <th class="px-3 py-3 text-left text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Keterangan</th>
                                        <th class="px-3 py-3 text-left text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-700 dark:bg-slate-900">
                                    @foreach($mapColorSettings as $index => $row)
                                        <tr wire:key="map-color-row-{{ $row['program_keahlian_id'] ?? $index }}">
                                            <td class="px-3 py-3 text-slate-500">{{ $index + 1 }}</td>
                                            <td class="px-3 py-3 align-top">
                                                {{ $row['nama_jurusan'] ?? '-' }}
                                                <input type="hidden" wire:model="mapColorSettings.{{ $index }}.program_keahlian_id">
                                            </td>
                                            <td class="px-3 py-3 align-top">
                                                <input wire:model.blur="mapColorSettings.{{ $index }}.warna_map" type="text" placeholder="Contoh: Merah" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800">
                                                @error('mapColorSettings.' . $index . '.warna_map') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                            </td>
                                            <td class="px-3 py-3 align-top">
                                                <input wire:model.blur="mapColorSettings.{{ $index }}.keterangan" type="text" placeholder="Opsional" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800">
                                                @error('mapColorSettings.' . $index . '.keterangan') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                            </td>
                                            <td class="px-3 py-3 align-top">
                                                <label class="flex items-center gap-2 text-xs font-semibold text-slate-600 dark:text-slate-300">
                                                    <input wire:model="mapColorSettings.{{ $index }}.is_active" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                                    Aktif
                                                </label>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" wire:loading.attr="disabled" wire:target="openActionModal" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60">
                                <span wire:loading.remove wire:target="openActionModal">Simpan Warna Map Peminatan</span>
                                <span wire:loading wire:target="openActionModal">Memproses...</span>
                            </button>
                        </div>
                    </form>
                                @elseif($tab === 'pamflet')
                    <div class="space-y-6">
                        <div>
                            <p class="text-sm font-bold text-slate-900 dark:text-white">Pamflet Promosi PPDB</p>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                Upload gambar pamflet yang akan ditampilkan sebagai modal popup saat pengunjung membuka halaman beranda dan PPDB.
                                Gambar otomatis dikonversi ke format WebP untuk loading optimal.
                            </p>
                        </div>

                        <div class="grid gap-6 lg:grid-cols-2">
                            {{-- Desktop/Tablet (Landscape) --}}
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-700 dark:bg-slate-800/40">
                                <div class="mb-3 flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-900 dark:text-white">Versi Desktop / Tablet</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">Orientasi landscape, max 5MB</p>
                                    </div>
                                </div>

                                @if($activePeriod->pamflet_desktop)
                                    <div class="mb-3 overflow-hidden rounded-xl border border-slate-200 dark:border-slate-600">
                                        <img src="{{ route('pamflet.show', \App\Http\Controllers\PamfletController::generateToken($activePeriod->id, 'desktop')) }}" alt="Pamflet Desktop" class="w-full object-contain" style="max-height: 280px;">
                                    </div>
                                    <button type="button" wire:click="openActionModal('remove-pamflet-desktop')" class="mb-3 rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-700 transition hover:bg-rose-100">
                                        Hapus Pamflet Desktop
                                    </button>
                                @endif

                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Upload Baru (JPG/PNG/WebP)</label>
                                    <input type="file" wire:model="pamfletDesktopUpload" accept="image/*" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800">
                                    @error('pamfletDesktopUpload') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                    <div wire:loading wire:target="pamfletDesktopUpload" class="mt-2 text-xs text-blue-600">Mengupload...</div>
                                    @if($pamfletDesktopUpload)
                                        <div class="mt-2 overflow-hidden rounded-lg border border-blue-200 bg-blue-50 p-2">
                                            <p class="text-xs font-semibold text-blue-700 mb-1">Preview:</p>
                                            <img src="{{ $pamfletDesktopUpload->temporaryUrl() }}" alt="Preview" class="w-full rounded object-contain" style="max-height: 200px;">
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Mobile (Portrait) --}}
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-700 dark:bg-slate-800/40">
                                <div class="mb-3 flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-900 dark:text-white">Versi Mobile</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">Orientasi portrait, max 5MB</p>
                                    </div>
                                </div>

                                @if($activePeriod->pamflet_mobile)
                                    <div class="mb-3 overflow-hidden rounded-xl border border-slate-200 dark:border-slate-600">
                                        <img src="{{ route('pamflet.show', \App\Http\Controllers\PamfletController::generateToken($activePeriod->id, 'mobile')) }}" alt="Pamflet Mobile" class="w-full object-contain" style="max-height: 280px;">
                                    </div>
                                    <button type="button" wire:click="openActionModal('remove-pamflet-mobile')" class="mb-3 rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-700 transition hover:bg-rose-100">
                                        Hapus Pamflet Mobile
                                    </button>
                                @endif

                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Upload Baru (JPG/PNG/WebP)</label>
                                    <input type="file" wire:model="pamfletMobileUpload" accept="image/*" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800">
                                    @error('pamfletMobileUpload') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                    <div wire:loading wire:target="pamfletMobileUpload" class="mt-2 text-xs text-blue-600">Mengupload...</div>
                                    @if($pamfletMobileUpload)
                                        <div class="mt-2 overflow-hidden rounded-lg border border-emerald-200 bg-emerald-50 p-2">
                                            <p class="text-xs font-semibold text-emerald-700 mb-1">Preview:</p>
                                            <img src="{{ $pamfletMobileUpload->temporaryUrl() }}" alt="Preview" class="w-full rounded object-contain" style="max-height: 200px;">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($pamfletDesktopUpload || $pamfletMobileUpload)
                            <div class="flex justify-end">
                                <button type="button" wire:click="openActionModal('save-pamflet')" wire:loading.attr="disabled" wire:target="openActionModal" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60">
                                    <span wire:loading.remove wire:target="openActionModal">Simpan Pamflet</span>
                                    <span wire:loading wire:target="openActionModal">Memproses...</span>
                                </button>
                            </div>
                        @endif

                        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-900 dark:bg-amber-950/20">
                            <p class="text-xs font-semibold text-amber-800 dark:text-amber-300">
                                💡 Pamflet akan ditampilkan otomatis saat pengunjung membuka halaman beranda dan PPDB.
                                Pengunjung dapat menutup modal dan mencentang "Jangan tampilkan lagi" untuk menyembunyikan pamflet di perangkat tersebut.
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        @else
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-400">
                Belum ada periode PPDB yang dapat dikelola. Buat periode baru terlebih dahulu agar pengaturan operasional bisa diaktifkan.
            </div>
        @endif
    </div>

    @if($showActionModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm" wire:click="closeActionModal">
            <div class="w-full max-w-lg rounded-2xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900" wire:click.stop>
                <div class="flex items-start gap-4 border-b border-slate-100 p-6 dark:border-slate-800">
                    <div @class([
                        'flex h-12 w-12 shrink-0 items-center justify-center rounded-xl',
                        'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300' => $actionModalTone === 'danger',
                        'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300' => $actionModalTone === 'warning',
                        'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' => in_array($actionModalTone, ['primary', 'info'], true),
                    ])>
                        @if($actionModalTone === 'danger')
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        @elseif($actionModalTone === 'warning')
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" /></svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-lg font-black text-slate-900 dark:text-white">{{ $actionModalTitle }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-600 dark:text-slate-300">{{ $actionModalMessage }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 rounded-b-2xl bg-slate-50 p-5 dark:bg-slate-800/60">
                    <button type="button" wire:click="closeActionModal" class="rounded-xl px-5 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-200 dark:text-slate-300 dark:hover:bg-slate-700">
                        Batal
                    </button>
                    <button
                        type="button"
                        wire:click="confirmAction"
                        wire:loading.attr="disabled"
                        wire:target="confirmAction"
                        @class([
                            'rounded-xl px-5 py-2.5 text-sm font-bold text-white transition disabled:cursor-not-allowed disabled:opacity-60',
                            'bg-rose-600 hover:bg-rose-700' => $actionModalTone === 'danger',
                            'bg-amber-600 hover:bg-amber-700' => $actionModalTone === 'warning',
                            'bg-blue-600 hover:bg-blue-700' => in_array($actionModalTone, ['primary', 'info'], true),
                        ])
                    >
                        <span wire:loading.remove wire:target="confirmAction">{{ $actionModalConfirmLabel ?: 'Ya, Lanjutkan' }}</span>
                        <span wire:loading wire:target="confirmAction">Memproses...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
