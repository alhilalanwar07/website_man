<div class="space-y-6">
    <div
        wire:loading.delay.longer.flex
        wire:target="triggerHolidayCheck,saveHoliday"
        class="items-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700 dark:border-blue-900/50 dark:bg-blue-900/20 dark:text-blue-300"
    >
        <span class="inline-block h-2 w-2 animate-pulse rounded-full bg-blue-500"></span>
        Memproses permintaan...
    </div>

    <div class="sr-only" role="status" aria-live="polite" aria-atomic="true">
        <span wire:loading wire:target="triggerHolidayCheck">Sedang mengirim konfirmasi libur ke Telegram.</span>
        <span wire:loading wire:target="saveHoliday">Sedang menyimpan data libur.</span>
        <span wire:loading wire:target="previousCalendarMonth,nextCalendarMonth,jumpToCurrentCalendarMonth,calendarMonth">Sedang memuat data kalender.</span>
        <span wire:loading wire:target="search,filterStatus,filterType,gotoPage,previousPage,nextPage">Sedang memuat daftar libur.</span>
        <span wire:loading wire:target="editHoliday,toggleHolidayStatus,deleteHoliday">Sedang memperbarui data libur.</span>
    </div>

    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-emerald-500">Kalender Operasional</p>
            <h1 class="mt-2 text-2xl font-black text-slate-900 dark:text-white">Manajemen Hari Libur Sekolah</h1>
            <p class="mt-2 max-w-3xl text-sm text-slate-500 dark:text-slate-400">
                Kelola tanggal libur manual di luar kalender nasional, termasuk libur darurat/kondisional.
                Data ini dipakai saat workflow Telegram meminta konfirmasi libur besok.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">

            <button
                type="button"
                wire:click="triggerHolidayCheck"
                wire:loading.attr="disabled"
                wire:target="triggerHolidayCheck"
                class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60"
            >
                <span wire:loading.remove wire:target="triggerHolidayCheck">Kirim Konfirmasi Besok ke Telegram</span>
                <span wire:loading wire:target="triggerHolidayCheck">Memproses...</span>
            </button>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Form Libur Via Modal</p>
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">Klik tanggal di kalender untuk membuka form tambah libur. Untuk ubah data, gunakan tombol Edit pada tabel.</p>
            </div>

            <button
                type="button"
                wire:click="openHolidayModal"
                wire:loading.attr="disabled"
                wire:target="openHolidayModal"
                class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-700 transition hover:border-blue-300 hover:text-blue-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
            >
                <span wire:loading.remove wire:target="openHolidayModal">Buka Form Libur</span>
                <span wire:loading wire:target="openHolidayModal">Membuka...</span>
            </button>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="flex flex-col gap-4 border-b border-slate-200 bg-gradient-to-r from-emerald-50 via-white to-cyan-50 p-4 md:flex-row md:items-center md:justify-between dark:border-slate-800 dark:from-slate-900 dark:via-slate-900 dark:to-slate-900">
            <div>
                <p class="text-sm font-bold text-slate-800 dark:text-slate-100">Kalender Libur Sekolah</p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Klik tanggal pada kalender untuk membuka Form Libur dalam modal dengan tanggal terpilih. Event API nasional tampil langsung di tanggal terkait.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <button type="button" wire:click="previousCalendarMonth" wire:loading.attr="disabled" wire:target="previousCalendarMonth" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-700 transition hover:border-slate-300 disabled:cursor-not-allowed disabled:opacity-60 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                    <span wire:loading.remove wire:target="previousCalendarMonth">Bulan Sebelumnya</span>
                    <span wire:loading wire:target="previousCalendarMonth">Memuat...</span>
                </button>
                <button type="button" wire:click="jumpToCurrentCalendarMonth" wire:loading.attr="disabled" wire:target="jumpToCurrentCalendarMonth" class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 transition hover:bg-emerald-100 disabled:cursor-not-allowed disabled:opacity-60 dark:border-emerald-900/60 dark:bg-emerald-900/20 dark:text-emerald-300">
                    <span wire:loading.remove wire:target="jumpToCurrentCalendarMonth">Bulan Ini</span>
                    <span wire:loading wire:target="jumpToCurrentCalendarMonth">Memuat...</span>
                </button>
                <button type="button" wire:click="nextCalendarMonth" wire:loading.attr="disabled" wire:target="nextCalendarMonth" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-700 transition hover:border-slate-300 disabled:cursor-not-allowed disabled:opacity-60 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                    <span wire:loading.remove wire:target="nextCalendarMonth">Bulan Berikutnya</span>
                    <span wire:loading wire:target="nextCalendarMonth">Memuat...</span>
                </button>
                <input wire:model.blur="calendarMonth" wire:loading.attr="disabled" wire:target="calendarMonth" type="month" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm disabled:cursor-not-allowed disabled:opacity-60 dark:border-slate-700 dark:bg-slate-800">
            </div>
        </div>

        <div wire:loading.flex wire:target="previousCalendarMonth,nextCalendarMonth,jumpToCurrentCalendarMonth,calendarMonth" class="items-center gap-2 border-b border-slate-200 bg-slate-50 px-4 py-2 text-xs font-medium text-slate-600 dark:border-slate-800 dark:bg-slate-800/40 dark:text-slate-300">
            <span class="inline-block h-2 w-2 animate-pulse rounded-full bg-slate-500"></span>
            Memuat data kalender...
        </div>

        <div class="grid grid-cols-1 gap-3 border-b border-slate-200 p-4 sm:grid-cols-2 xl:grid-cols-4 dark:border-slate-800">
            <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 dark:border-slate-700 dark:bg-slate-800/40">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Periode</p>
                <p class="mt-1 text-sm font-bold text-slate-800 dark:text-slate-100">{{ $calendarStats['month_label'] }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 dark:border-slate-700 dark:bg-slate-800/40">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Tanggal Libur Terdata</p>
                <p class="mt-1 text-sm font-bold text-slate-800 dark:text-slate-100">{{ $calendarStats['holiday_dates_count'] }} tanggal</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 dark:border-slate-700 dark:bg-slate-800/40">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Libur Manual Aktif</p>
                <p class="mt-1 text-sm font-bold text-slate-800 dark:text-slate-100">{{ $calendarStats['active_count'] }} dari {{ $calendarStats['entry_count'] }}</p>
            </div>
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 dark:border-rose-900/60 dark:bg-rose-900/20">
                <p class="text-xs uppercase tracking-[0.2em] text-rose-600 dark:text-rose-300">Event API Nasional</p>
                <p class="mt-1 text-sm font-bold text-rose-700 dark:text-rose-200">{{ $calendarStats['api_event_count'] }} event</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 px-4 py-3 dark:border-slate-800">
            <span class="rounded-full border border-rose-200 bg-rose-50 px-2.5 py-1 text-[11px] font-bold text-rose-700 dark:border-rose-900/60 dark:bg-rose-900/20 dark:text-rose-300">API Nasional</span>
            <span class="rounded-full border border-blue-200 bg-blue-50 px-2.5 py-1 text-[11px] font-bold text-blue-700 dark:border-blue-900/60 dark:bg-blue-900/20 dark:text-blue-300">Manual Tambahan</span>
            <span class="rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1 text-[11px] font-bold text-amber-700 dark:border-amber-900/60 dark:bg-amber-900/20 dark:text-amber-300">Darurat / Kondisional</span>
            <span class="rounded-full border border-slate-300 bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-600 dark:border-slate-700 dark:bg-slate-800/60 dark:text-slate-300">Manual Nonaktif</span>
        </div>

        @php($weekdayLabels = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'])

        <x-admin.skeleton.calendar wire:loading.block wire:target="previousCalendarMonth,nextCalendarMonth,jumpToCurrentCalendarMonth,calendarMonth" :days="42" :key-prefix="'hari-libur-calendar'" />

        <div class="overflow-x-auto p-4" wire:loading.remove wire:target="previousCalendarMonth,nextCalendarMonth,jumpToCurrentCalendarMonth,calendarMonth">
            <table class="min-w-full table-fixed border-collapse text-xs md:text-sm">
                <thead>
                    <tr>
                        @foreach($weekdayLabels as $weekdayLabel)
                            <th wire:key="calendar-head-{{ $weekdayLabel }}" class="border border-slate-200 bg-slate-50 px-2 py-2 text-center font-bold text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                                {{ $weekdayLabel }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($calendarWeeks as $weekIndex => $week)
                        <tr wire:key="calendar-week-{{ $weekIndex }}">
                            @foreach($week as $day)
                                <td wire:key="calendar-day-{{ $day['date_string'] }}" class="h-32 align-top border border-slate-200 p-1.5 dark:border-slate-700">
                                    <button
                                        type="button"
                                        wire:click="setHolidayDateFromCalendar('{{ $day['date_string'] }}')"
                                        @class([
                                            'h-full w-full rounded-lg p-1.5 text-left transition',
                                            'bg-white hover:bg-slate-50 dark:bg-slate-900 dark:hover:bg-slate-800/70' => $day['is_current_month'],
                                            'bg-slate-100 text-slate-400 dark:bg-slate-800/50 dark:text-slate-500' => ! $day['is_current_month'],
                                            'ring-2 ring-emerald-500' => $isHolidayModalOpen && ($holidayForm['holiday_date'] ?? null) === $day['date_string'],
                                        ])
                                    >
                                        <div class="flex items-center justify-between gap-2">
                                            <span @class([
                                                'inline-flex h-6 w-6 items-center justify-center rounded-full text-xs font-bold',
                                                'bg-emerald-600 text-white' => $day['is_today'],
                                                'text-slate-700 dark:text-slate-200' => ! $day['is_today'] && $day['is_current_month'] && ! $day['is_weekend'],
                                                'text-rose-600 dark:text-rose-300' => ! $day['is_today'] && $day['is_current_month'] && $day['is_weekend'],
                                                'text-slate-400 dark:text-slate-500' => ! $day['is_current_month'],
                                            ])>
                                                {{ $day['day_number'] }}
                                            </span>
                                            <div class="flex items-center gap-1">
                                                @if($day['api_event_count'] > 0)
                                                    <span class="rounded-full bg-rose-100 px-1.5 py-0.5 text-[9px] font-bold text-rose-700 dark:bg-rose-900/40 dark:text-rose-300">API {{ $day['api_event_count'] }}</span>
                                                @endif
                                                @if($day['manual_event_count'] > 0)
                                                    <span class="rounded-full bg-emerald-100 px-1.5 py-0.5 text-[9px] font-bold text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">M {{ $day['manual_event_count'] }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        @if($day['holiday_count'] > 0)
                                            <div class="mt-1 space-y-1">
                                                @foreach(array_slice($day['holidays'], 0, 3) as $holiday)
                                                    <p wire:key="calendar-day-holiday-{{ $day['date_string'] }}-{{ $holiday['id'] }}" @class([
                                                        'line-clamp-1 rounded px-1.5 py-0.5 text-[10px] font-medium',
                                                        'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300' => ($holiday['source'] ?? '') === 'api',
                                                        'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300' => ($holiday['source'] ?? '') !== 'api' && ($holiday['type'] ?? '') === 'emergency',
                                                        'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' => ($holiday['source'] ?? '') !== 'api' && ($holiday['type'] ?? '') !== 'emergency',
                                                        'opacity-60' => ! $holiday['is_active'],
                                                    ])>
                                                        @if(($holiday['source'] ?? '') === 'api')
                                                            API: {{ $holiday['name'] }}
                                                        @else
                                                            {{ $holiday['name'] }}
                                                        @endif
                                                    </p>
                                                @endforeach

                                                @if($day['holiday_count'] > 3)
                                                    <p class="text-[10px] text-slate-500 dark:text-slate-400">+{{ $day['holiday_count'] - 3 }} lainnya</p>
                                                @endif
                                            </div>
                                        @endif
                                    </button>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="flex flex-col gap-3 border-b border-slate-200 p-4 md:flex-row md:items-center md:justify-between dark:border-slate-800">
            <p class="text-sm font-bold text-slate-800 dark:text-slate-100">Daftar Libur Manual</p>
            <div class="flex flex-col gap-2 sm:flex-row">
                <input
                    wire:model.live.debounce.300ms="search"
                    wire:loading.attr="disabled"
                    wire:target="search,filterStatus,filterType,gotoPage,previousPage,nextPage"
                    type="text"
                    placeholder="Cari nama/alasan libur"
                    class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800"
                >
                <select wire:model="filterStatus" wire:loading.attr="disabled" wire:target="search,filterStatus,filterType,gotoPage,previousPage,nextPage" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800">
                    <option value="all">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif</option>
                </select>
                <select wire:model="filterType" wire:loading.attr="disabled" wire:target="search,filterStatus,filterType,gotoPage,previousPage,nextPage" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800">
                    <option value="all">Semua Jenis</option>
                    <option value="manual">Manual Tambahan</option>
                    <option value="emergency">Darurat / Kondisional</option>
                </select>
            </div>
        </div>

        <div wire:loading.flex wire:target="search,filterStatus,filterType,gotoPage,previousPage,nextPage" class="items-center gap-2 border-b border-slate-200 bg-slate-50 px-4 py-2 text-xs font-medium text-slate-600 dark:border-slate-800 dark:bg-slate-800/40 dark:text-slate-300">
            <span class="inline-block h-2 w-2 animate-pulse rounded-full bg-slate-500"></span>
            Memuat daftar libur...
        </div>

        <div wire:loading.block wire:target="search,filterStatus,filterType,gotoPage,previousPage,nextPage" class="p-4">
            <x-admin.skeleton.table :columns="5" :rows="6" :key-prefix="'hari-libur-table'" :show-actions="true" />
        </div>

        <div class="overflow-x-auto" wire:loading.remove wire:target="search,filterStatus,filterType,gotoPage,previousPage,nextPage">
            <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-700">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-[0.2em] text-slate-500 dark:bg-slate-800/70 dark:text-slate-300">
                    <tr>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Nama / Alasan</th>
                        <th class="px-4 py-3 text-left">Jenis</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white text-slate-700 dark:divide-slate-800 dark:bg-slate-900 dark:text-slate-200">
                    @forelse($manualHolidays as $holiday)
                        <tr wire:key="manual-holiday-{{ $holiday->id }}">
                            <td class="px-4 py-3 align-top">
                                <p class="font-semibold">{{ $holiday->holiday_date?->translatedFormat('d M Y') }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $holiday->holiday_date?->translatedFormat('l') }}</p>
                            </td>
                            <td class="px-4 py-3 align-top">
                                <p class="font-semibold">{{ $holiday->name }}</p>
                                @if($holiday->description)
                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $holiday->description }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 align-top">
                                <span @class([
                                    'inline-flex rounded-full px-2.5 py-1 text-xs font-bold',
                                    'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300' => $holiday->type === 'emergency',
                                    'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' => $holiday->type !== 'emergency',
                                ])>
                                    {{ $holiday->type === 'emergency' ? 'Darurat / Kondisional' : 'Manual Tambahan' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 align-top">
                                <span @class([
                                    'inline-flex rounded-full px-2.5 py-1 text-xs font-bold',
                                    'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' => $holiday->is_active,
                                    'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200' => ! $holiday->is_active,
                                ])>
                                    {{ $holiday->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 align-top">
                                <div class="flex justify-end gap-2">
                                    <button type="button" wire:click="editHoliday({{ $holiday->id }})" wire:loading.attr="disabled" wire:target="editHoliday({{ $holiday->id }})" class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-700 transition hover:border-blue-300 disabled:cursor-not-allowed disabled:opacity-60 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                                        <span wire:loading.remove wire:target="editHoliday({{ $holiday->id }})">Edit</span>
                                        <span wire:loading wire:target="editHoliday({{ $holiday->id }})">Memuat...</span>
                                    </button>
                                    <button type="button" wire:click="toggleHolidayStatus({{ $holiday->id }})" wire:loading.attr="disabled" wire:target="toggleHolidayStatus({{ $holiday->id }})" class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-700 transition hover:border-emerald-300 disabled:cursor-not-allowed disabled:opacity-60 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                                        <span wire:loading.remove wire:target="toggleHolidayStatus({{ $holiday->id }})">{{ $holiday->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</span>
                                        <span wire:loading wire:target="toggleHolidayStatus({{ $holiday->id }})">Memproses...</span>
                                    </button>
                                    <button type="button" wire:click="deleteHoliday({{ $holiday->id }})" wire:loading.attr="disabled" wire:target="deleteHoliday({{ $holiday->id }})" onclick="return confirm('Hapus data libur manual ini?')" class="rounded-lg border border-rose-300 bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-700 transition hover:bg-rose-100 disabled:cursor-not-allowed disabled:opacity-60">
                                        <span wire:loading.remove wire:target="deleteHoliday({{ $holiday->id }})">Hapus</span>
                                        <span wire:loading wire:target="deleteHoliday({{ $holiday->id }})">Menghapus...</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500 dark:text-slate-400">
                                Belum ada data libur manual. Tambahkan jika ada kebijakan libur di luar kalender nasional.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($manualHolidays->hasPages())
            <div class="border-t border-slate-200 px-4 py-3 dark:border-slate-800" wire:loading.remove wire:target="search,filterStatus,filterType,gotoPage,previousPage,nextPage">
                {{ $manualHolidays->links() }}
            </div>
        @endif
    </div>

    @if($isHolidayModalOpen)
        <div wire:transition="holiday-modal-backdrop" class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm" wire:click="closeHolidayModal" aria-hidden="true"></div>

        <div wire:transition="holiday-modal-shell" class="fixed inset-0 z-50 flex items-center justify-center p-4" wire:keydown.escape.window="closeHolidayModal" role="dialog" aria-modal="true" aria-labelledby="holiday-modal-title">
            <div wire:transition="holiday-modal-panel" class="w-full max-w-3xl rounded-2xl border border-slate-200 bg-white shadow-2xl dark:border-slate-800 dark:bg-slate-900" wire:click.stop x-data x-init="$nextTick(() => { if ($refs.holidayNameField) { $refs.holidayNameField.focus(); } })">
                <div class="flex items-center justify-between gap-3 border-b border-slate-200 px-5 py-4 dark:border-slate-800">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Form Libur</p>
                        <h3 id="holiday-modal-title" class="mt-1 text-base font-black text-slate-900 dark:text-white">{{ $editingHolidayId ? 'Perbarui Data Libur' : 'Tambah Data Libur Baru' }}</h3>
                    </div>

                    <button type="button" wire:click="closeHolidayModal" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                        <span class="sr-only">Tutup</span>
                        &times;
                    </button>
                </div>

                <form wire:submit.prevent="saveHoliday" class="space-y-4 px-5 py-4" wire:key="holiday-form-modal">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Tanggal Libur</label>
                            <input wire:model.blur="holidayForm.holiday_date" type="date" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                            @error('holidayForm.holiday_date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div class="xl:col-span-2">
                            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Nama Libur / Keterangan Singkat</label>
                            <input x-ref="holidayNameField" wire:model.blur="holidayForm.name" type="text" placeholder="Contoh: Libur Kondisional Karena Banjir" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                            @error('holidayForm.name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Jenis</label>
                            <select wire:model="holidayForm.type" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                                <option value="manual">Manual Tambahan</option>
                                <option value="emergency">Darurat / Kondisional</option>
                            </select>
                            @error('holidayForm.type') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Alasan Detail (opsional)</label>
                        <textarea wire:model.blur="holidayForm.description" rows="3" placeholder="Jelaskan konteks libur agar jejak keputusan lebih jelas" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800"></textarea>
                        @error('holidayForm.description') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 pt-4 dark:border-slate-800">
                        <label class="flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 dark:border-slate-700 dark:bg-slate-800/40 dark:text-slate-300">
                            <input wire:model.live="holidayForm.is_active" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            Libur ini aktif dan ikut diproses workflow
                        </label>

                        <div class="flex items-center gap-2">
                            <button type="button" wire:click="closeHolidayModal" class="rounded-lg border border-slate-200 bg-white px-3.5 py-2 text-sm font-bold text-slate-700 transition hover:border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                                Batal
                            </button>

                            <button type="submit" wire:loading.attr="disabled" wire:target="saveHoliday" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60">
                                <span wire:loading.remove wire:target="saveHoliday">{{ $editingHolidayId ? 'Perbarui Libur' : 'Simpan Libur' }}</span>
                                <span wire:loading wire:target="saveHoliday">Menyimpan...</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
