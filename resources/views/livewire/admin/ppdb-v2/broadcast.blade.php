<div class="space-y-6">
    <div role="status" aria-live="polite" aria-atomic="true" class="sr-only">
        <span wire:loading wire:target="sendBroadcast">Mengirim broadcast PPDB.</span>
        <span wire:loading wire:target="applyTemplate">Menerapkan template broadcast.</span>
    </div>

    <div wire:loading.flex wire:target="sendBroadcast,applyTemplate" class="items-center gap-2 rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm font-semibold text-blue-700 dark:border-blue-900 dark:bg-blue-900/30 dark:text-blue-300">
        <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
        Menyinkronkan konten broadcast...
    </div>

    <div class="rounded-2xl border border-blue-200 bg-gradient-to-r from-blue-50 via-cyan-50 to-white p-6 shadow-sm dark:border-blue-900 dark:from-blue-950/30 dark:via-slate-900 dark:to-slate-900">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.25em] text-blue-600">PPDB Communication Center</p>
                <h1 class="mt-2 text-2xl font-black text-slate-900 dark:text-white">Broadcast Strategis dan Pengumuman Terpadu</h1>
                <p class="mt-2 max-w-3xl text-sm text-slate-600 dark:text-slate-300">
                    Kelola komunikasi dari tahap verifikasi, penentuan jurusan, pengumuman kelulusan, hingga daftar ulang dari satu layar.
                    Halaman ini dirancang sebagai pusat pesan operasional PPDB agar arahan ke peserta tetap konsisten, cepat, dan terdokumentasi.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach ($integrationMap as $item)
                    <a
                        wire:key="broadcast-link-{{ str($item['menu'])->slug() }}"
                        href="{{ $item['route'] }}"
                        class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-700 transition hover:border-blue-300 hover:text-blue-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200"
                    >
                        {{ $item['menu'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-700 dark:border-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1.35fr_1fr]">
        <div class="space-y-6">
            <div class="relative rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div wire:loading.flex wire:target="sendBroadcast" class="absolute inset-0 z-10 items-center justify-center rounded-2xl bg-white/80 text-sm font-bold text-blue-600 backdrop-blur-sm dark:bg-slate-900/80 dark:text-blue-300">
                    Menyiapkan antrean broadcast...
                </div>

                <div class="mb-4 flex items-center justify-between gap-3">
                    <h3 class="text-lg font-black text-slate-900 dark:text-white">Composer Pesan Massal</h3>
                    <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                        Queue-Ready
                    </span>
                </div>

                <form wire:submit.prevent="sendBroadcast" class="space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-bold text-slate-700 dark:text-slate-300">Target Penerima</label>
                        <select wire:model="targetAudience" wire:loading.attr="disabled" wire:target="sendBroadcast" class="w-full rounded-xl border-slate-300 bg-slate-50 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-950 disabled:cursor-not-allowed disabled:opacity-60">
                            <option value="all">Semua Pendaftar Terdaftar</option>
                            <option value="verified">Siswa Terverifikasi Berkas (Tunggu Ujian)</option>
                            <option value="lulus">Siswa Lulus (Semua Jurusan)</option>
                            <option value="belum_daftar_ulang">Siswa Lulus (Belum Daftar Ulang)</option>
                            <option value="selesai_daftar_ulang">Siswa Lulus (Selesai Daftar Ulang)</option>
                        </select>
                        @error('targetAudience')
                            <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 rounded-xl border border-blue-100 bg-blue-50 px-3 py-2 text-xs text-blue-700 dark:border-blue-900 dark:bg-blue-900/20 dark:text-blue-300">
                            {{ $audienceGuide[$targetAudience] ?? 'Pilih target penerima untuk melihat panduan sasaran komunikasi.' }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-bold text-slate-700 dark:text-slate-300">Jalur Pengiriman</label>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <label class="flex cursor-pointer items-center gap-2 rounded-xl border p-3 transition {{ $channel === 'whatsapp' ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-900/20' : 'border-slate-200 dark:border-slate-700' }}">
                                <input type="radio" wire:model="channel" wire:loading.attr="disabled" wire:target="sendBroadcast" value="whatsapp" class="text-emerald-500 focus:ring-emerald-500 disabled:cursor-not-allowed disabled:opacity-60">
                                <x-admin.icon name="chat" class="h-5 w-5 text-emerald-500" />
                                <span class="text-sm font-bold text-slate-700 dark:text-slate-300">WhatsApp</span>
                            </label>
                            <label class="flex cursor-pointer items-center gap-2 rounded-xl border p-3 transition {{ $channel === 'email' ? 'border-blue-500 bg-blue-50/50 dark:bg-blue-900/20' : 'border-slate-200 dark:border-slate-700' }}">
                                <input type="radio" wire:model="channel" wire:loading.attr="disabled" wire:target="sendBroadcast" value="email" class="text-blue-500 focus:ring-blue-500 disabled:cursor-not-allowed disabled:opacity-60">
                                <x-admin.icon name="mail" class="h-5 w-5 text-blue-500" />
                                <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Email Utama</span>
                            </label>
                        </div>
                        @error('channel')
                            <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                            {{ $channel === 'whatsapp' ? 'Gunakan kalimat singkat, padat, dan mudah ditindaklanjuti oleh peserta.' : 'Gunakan format formal agar cocok untuk arsip administratif peserta.' }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-bold text-slate-700 dark:text-slate-300">Isi Pesan/Pengumuman</label>
                        <textarea wire:model.blur="messageText" wire:loading.attr="disabled" wire:target="sendBroadcast" rows="6" class="w-full rounded-xl border-slate-300 bg-slate-50 text-sm shadow-inner focus:border-blue-500 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-950 disabled:cursor-not-allowed disabled:opacity-60" placeholder="Tulis pengingat jadwal, pengumuman hasil, atau instruksi daftar ulang di sini..."></textarea>
                        @error('messageText')
                            <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Gunakan token personalisasi agar pesan lebih relevan dan mudah dipahami peserta.</p>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-800/40">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Token Personal</p>
                        <div class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2">
                            @foreach ($tokenGuide as $token)
                                <div wire:key="broadcast-token-{{ $token['token'] }}" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs dark:border-slate-700 dark:bg-slate-900">
                                    <p class="font-bold text-slate-800 dark:text-slate-100">{{ $token['token'] }}</p>
                                    <p class="mt-0.5 text-slate-500 dark:text-slate-400">{{ $token['desc'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 dark:border-blue-900 dark:bg-blue-900/20">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-600">Pratinjau Pesan</p>
                        <div class="mt-3 rounded-lg border border-blue-100 bg-white p-3 text-sm leading-relaxed text-slate-700 dark:border-blue-900 dark:bg-slate-900 dark:text-slate-200">
                            {{ $previewText }}
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" wire:loading.attr="disabled" wire:target="sendBroadcast" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-3 text-sm font-bold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200">
                            <span wire:loading.remove wire:target="sendBroadcast" class="inline-flex items-center gap-2">
                                <x-admin.icon name="paper-airplane" class="h-5 w-5" />
                                Arahkan ke Antrean Kirim (Queue)
                            </span>
                            <span wire:loading wire:target="sendBroadcast">Mengirim...</span>
                        </button>
                        <div wire:loading wire:target="sendBroadcast" class="mt-2 w-full text-center text-sm font-bold text-blue-600">
                            Memproses pengiriman...
                        </div>
                    </div>
                </form>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="mb-4 flex items-start justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-black text-slate-900 dark:text-white">Template Operasional PPDB</h3>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Gunakan template siap pakai untuk skenario utama sepanjang siklus PPDB.</p>
                    </div>
                </div>

                <div wire:loading.block wire:target="applyTemplate" class="mb-4">
                    <x-admin.skeleton.card-grid :cards="4" />
                </div>

                <div wire:loading.remove wire:target="applyTemplate" class="grid grid-cols-1 gap-3 md:grid-cols-2">
                    @foreach ($quickTemplates as $template)
                        <button
                            wire:key="broadcast-template-{{ $template['key'] }}"
                            type="button"
                            class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-left transition hover:border-blue-300 hover:bg-blue-50 dark:border-slate-700 dark:bg-slate-800/50 dark:hover:border-blue-700 dark:hover:bg-blue-950/20 disabled:cursor-not-allowed disabled:opacity-60"
                            wire:click="applyTemplate('{{ $template['key'] }}')"
                            wire:loading.attr="disabled"
                            wire:target="applyTemplate"
                        >
                            <p class="text-sm font-black text-slate-800 dark:text-slate-100">{{ $template['title'] }}</p>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Target: {{ str($template['audience'])->replace('_', ' ')->title() }} | Channel: {{ strtoupper($template['channel']) }}</p>
                            <p class="mt-2 line-clamp-2 text-xs text-slate-600 dark:text-slate-300">{{ $template['body'] }}</p>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h3 class="text-base font-black text-slate-900 dark:text-white">Cakupan Kebutuhan Komunikasi PPDB</h3>
                <div class="mt-4 space-y-3">
                    @foreach ($integrationMap as $item)
                        <a wire:key="broadcast-coverage-{{ str($item['menu'])->slug() }}" href="{{ $item['route'] }}" class="block rounded-xl border border-slate-200 bg-slate-50 p-3 transition hover:border-blue-300 hover:bg-blue-50 dark:border-slate-700 dark:bg-slate-800/50 dark:hover:border-blue-700 dark:hover:bg-blue-950/20">
                            <p class="text-sm font-bold text-slate-800 dark:text-slate-100">{{ $item['menu'] }}</p>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $item['goal'] }}</p>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-blue-50 p-6 shadow-sm dark:border-slate-800 dark:bg-blue-900/20">
                <div class="flex items-start gap-3">
                    <div class="mt-0.5 rounded-full bg-blue-100 p-2 dark:bg-blue-800">
                        <x-admin.icon name="information-circle" class="h-5 w-5 text-blue-600 dark:text-blue-300" />
                    </div>
                    <div>
                        <h4 class="font-bold text-blue-900 dark:text-blue-100">Sinkron Otomatis dengan Proses Seleksi</h4>
                        <p class="mt-1 text-sm leading-relaxed text-blue-800/90 dark:text-blue-200/90">
                            Selain blast manual, sistem PPDB telah mendukung notifikasi otomatis pada alur operasional tertentu.
                            Gunakan panel ini untuk komunikasi lanjutan, penguatan instruksi, dan reminder berbasis status peserta.
                        </p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h3 class="text-base font-black text-slate-900 dark:text-white">Checklist Tata Kelola Broadcast</h3>
                <ul class="mt-3 space-y-2">
                    @foreach ($checklistItems as $item)
                        <li wire:key="broadcast-check-{{ str($item)->slug() }}" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-600 dark:border-slate-700 dark:bg-slate-800/50 dark:text-slate-300">
                            {{ $item }}
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="border-b border-slate-100 bg-slate-50/70 p-4 dark:border-slate-800 dark:bg-slate-900/50">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white">Pengumuman Banner di Website PPDB</h3>
                </div>
                <div class="p-5">
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Gunakan kanal ini untuk peserta yang kurang responsif di WA/Email. Konten banner sebaiknya menekankan batas waktu, alur tindakan, dan tautan resmi.
                    </p>
                    <button type="button" class="mt-4 rounded-xl border border-slate-300 px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                        Tambahkan Banner Pengumuman
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
