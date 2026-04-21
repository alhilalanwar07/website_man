<div>
    <section class="relative overflow-hidden bg-slate-950 text-white py-24 noise">
        <div class="absolute inset-0 bg-mesh-hero opacity-70"></div>
        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="inline-flex px-4 py-1.5 rounded-full glass text-xs font-bold uppercase tracking-[0.3em] text-blue-300 mb-6">Portal PPDB</span>
            <h1 class="text-4xl lg:text-6xl font-black tracking-tight leading-tight">Cek Status <span class="text-gradient">Verifikasi</span></h1>
            <p class="text-slate-300 max-w-2xl mx-auto mt-4">Masukkan nomor pendaftaran atau NISN dan tanggal lahir untuk melihat status verifikasi gabungan, detail status operasional, dan hasil sementara PPDB.</p>
        </div>
    </section>

    @include('livewire.frontend.partials.ppdb-journey-nav', [
        'active' => 'status',
    ])

    <section class="py-20 bg-slate-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Langkah 1</p>
                    <p class="mt-2 text-sm font-semibold text-slate-800">Masukkan nomor pendaftaran atau NISN</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Langkah 2</p>
                    <p class="mt-2 text-sm font-semibold text-slate-800">Cocokkan tanggal lahir</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Langkah 3</p>
                    <p class="mt-2 text-sm font-semibold text-slate-800">Baca status verifikasi dan catatan panitia</p>
                </div>
            </div>
            <div class="bg-white rounded-[28px] border border-slate-100 shadow-sm p-8 md:p-10">
                <form wire:submit="search" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Nomor Pendaftaran / NISN</label>
                        <input wire:model.blur="nomor_pendaftaran" type="text" placeholder="PPDB-2026-0001 atau 0012345678" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 outline-none">
                        <p class="mt-1 text-xs text-slate-500">Gunakan nomor yang tersimpan setelah pendaftaran selesai.</p>
                        @error('nomor_pendaftaran') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal Lahir</label>
                        <input wire:model.blur="tanggal_lahir" type="date" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 outline-none">
                        @error('tanggal_lahir') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-3 flex flex-wrap items-center gap-3">
                        <button type="submit" wire:loading.attr="disabled" wire:target="search" class="px-6 py-3 bg-slate-900 text-white text-sm font-bold rounded-2xl hover:bg-slate-800 transition disabled:cursor-not-allowed disabled:opacity-60">
                            <span wire:loading.remove wire:target="search">Cek Status</span>
                            <span wire:loading wire:target="search">Mencari Data...</span>
                        </button>
                        @if($searched)
                        <button type="button" wire:click="resetSearch" class="px-6 py-3 rounded-2xl border border-slate-300 bg-white text-sm font-bold text-slate-700 hover:bg-slate-100 transition">Cari Data Lain</button>
                        @endif
                    </div>
                </form>
            </div>

            @if($searched)
                @if($result)
                @php
                    $selectionBadge = match ($result->hasil_seleksi) {
                        'passed' => 'bg-emerald-50 text-emerald-700',
                        'reserve' => 'bg-amber-50 text-amber-700',
                        'failed' => 'bg-red-50 text-red-700',
                        default => 'bg-slate-100 text-slate-700',
                    };
                @endphp
                <div class="mt-8 bg-white rounded-[28px] border border-slate-100 shadow-sm p-8 space-y-8">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.25em] text-slate-400">Data Pendaftar</p>
                            <h2 class="text-2xl font-black text-slate-900 mt-2">{{ $result->nama_lengkap }}</h2>
                            <p class="text-sm text-slate-500 mt-1">{{ $result->nomor_pendaftaran }} · {{ $result->asal_sekolah }}</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <span class="px-3 py-1.5 rounded-full text-xs font-bold {{ $result->verification_status_key === 'approved' ? 'bg-emerald-50 text-emerald-700' : ($result->verification_status_key === 'rejected' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700') }}">Verifikasi: {{ $result->verification_status_label }}</span>
                            <span class="px-3 py-1.5 rounded-full text-xs font-bold {{ $selectionBadge }}">Hasil: {{ $result->hasil_seleksi_label }}</span>
                            <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700">Pendaftaran: {{ $result->status_pendaftaran_label }}</span>
                            <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700">Berkas: {{ $result->status_berkas_label }}</span>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Arti Status</p>
                        <p class="text-sm text-slate-700 leading-relaxed">Dalam Proses berarti data pendaftaran atau berkas masih ditinjau. Terverifikasi / Diterima berarti tahapan verifikasi sudah siap lanjut. Ditolak berarti ada penolakan pada salah satu tahap verifikasi. Status Pendaftaran dan Berkas tetap ditampilkan sebagai detail operasional.</p>
                    </div>

                    <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-600 mb-2">Aksi Mandiri Pendaftar</p>
                        <p class="text-sm text-slate-700 leading-relaxed">Anda bisa mengunduh ulang formulir PDF pendaftaran dan mengirim ulang email konfirmasi jika dibutuhkan.</p>
                        <div class="mt-3 flex flex-wrap items-center gap-3">
                            @if($resultDownloadUrl)
                            <a href="{{ $resultDownloadUrl }}" target="_blank" rel="noopener" class="inline-flex rounded-xl bg-blue-700 px-4 py-2 text-xs font-bold text-white transition hover:bg-blue-800">Unduh Formulir PDF</a>
                            @endif

                            @if(filter_var((string) $result->email, FILTER_VALIDATE_EMAIL))
                            <button type="button" wire:click="resendConfirmationEmail" wire:loading.attr="disabled" wire:target="resendConfirmationEmail" class="inline-flex rounded-xl border border-blue-300 bg-white px-4 py-2 text-xs font-bold text-blue-700 transition hover:bg-blue-100 disabled:cursor-not-allowed disabled:opacity-60">
                                <span wire:loading.remove wire:target="resendConfirmationEmail">Kirim Ulang Email Konfirmasi</span>
                                <span wire:loading wire:target="resendConfirmationEmail">Mengirim Ulang...</span>
                            </button>
                            @endif
                        </div>

                        @if(filter_var((string) $result->email, FILTER_VALIDATE_EMAIL))
                        <p class="mt-2 text-xs text-slate-600">Email tujuan: {{ $result->email }}</p>
                        @else
                        <p class="mt-2 text-xs text-amber-700">Email pendaftar tidak tersedia atau tidak valid, sehingga kirim ulang email tidak bisa dilakukan otomatis.</p>
                        @endif

                        @if($actionFeedbackMessage)
                        <div class="mt-3 rounded-xl border px-3 py-2 text-xs {{ $actionFeedbackType === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-rose-200 bg-rose-50 text-rose-700' }}">
                            {{ $actionFeedbackMessage }}
                        </div>
                        @endif
                    </div>

                    @if($result->period?->isAnnouncementPublished())
                    <div class="rounded-3xl border border-emerald-100 bg-emerald-50 p-6">
                        <p class="text-xs font-bold uppercase tracking-[0.25em] text-emerald-600">Pengumuman Resmi</p>
                        <h3 class="text-xl font-black text-slate-900 mt-3">{{ $result->hasil_seleksi === 'passed' ? 'Selamat, Anda dinyatakan lulus.' : ($result->hasil_seleksi === 'reserve' ? 'Anda masuk daftar cadangan.' : ($result->hasil_seleksi === 'failed' ? 'Anda belum dinyatakan lulus.' : 'Hasil masih diproses.')) }}</h3>
                        <p class="text-sm text-slate-600 mt-2 leading-relaxed">{{ $result->period->catatan_pengumuman ?: 'Panitia telah mempublikasikan hasil resmi PPDB.' }}</p>
                        @if($result->hasil_seleksi === 'passed')
                        <button type="button" wire:click="continueToReRegistration" wire:loading.attr="disabled" wire:target="continueToReRegistration" class="inline-flex mt-4 px-5 py-3 rounded-2xl bg-slate-900 text-white text-sm font-bold hover:bg-slate-800 transition disabled:cursor-not-allowed disabled:opacity-60">
                            <span wire:loading.remove wire:target="continueToReRegistration">Lanjut ke Daftar Ulang</span>
                            <span wire:loading wire:target="continueToReRegistration">Membuka Halaman...</span>
                        </button>
                        @endif
                    </div>
                    @else
                    <div class="rounded-3xl border border-amber-100 bg-amber-50 p-6">
                        <p class="text-xs font-bold uppercase tracking-[0.25em] text-amber-600">Menunggu Pengumuman</p>
                        <p class="text-sm text-slate-700 leading-relaxed mt-2">Status administrasi Anda sudah tercatat, tetapi hasil resmi PPDB belum dipublikasikan panitia. Pantau halaman ini secara berkala hingga tanggal pengumuman.</p>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="rounded-3xl bg-slate-50 p-6">
                            <p class="text-xs font-bold uppercase tracking-[0.25em] text-slate-400 mb-3">Pilihan Jurusan</p>
                            <div class="space-y-2 text-sm text-slate-700">
                                <p><span class="font-semibold">Pilihan 1:</span> {{ $result->pilihanProgram1->nama_jurusan }}</p>
                                <p><span class="font-semibold">Pilihan 2:</span> {{ $result->pilihanProgram2->nama_jurusan ?? '-' }}</p>
                                <p><span class="font-semibold">Pilihan 3:</span> {{ $result->pilihanProgram3->nama_jurusan ?? '-' }}</p>
                                <p><span class="font-semibold">Jalur:</span> {{ $result->track->nama_jalur }}</p>
                            </div>
                        </div>
                        <div class="rounded-3xl bg-slate-50 p-6">
                            <p class="text-xs font-bold uppercase tracking-[0.25em] text-slate-400 mb-3">Hasil Seleksi</p>
                            <div class="space-y-2 text-sm text-slate-700">
                                <p><span class="font-semibold">Skor:</span> {{ $result->period?->isAnnouncementPublished() ? number_format((float) ($result->skor_seleksi ?? 0), 2) : 'Belum ditampilkan' }}</p>
                                <p><span class="font-semibold">Ranking Jalur:</span> {{ $result->period?->isAnnouncementPublished() ? ($result->ranking_jalur ?? '-') : '-' }}</p>
                                <p><span class="font-semibold">Ranking Program:</span> {{ $result->period?->isAnnouncementPublished() ? ($result->ranking_program ?? '-') : '-' }}</p>
                                <p><span class="font-semibold">Program Diterima:</span> {{ $result->period?->isAnnouncementPublished() ? ($result->programDiterima->nama_jurusan ?? 'Belum ditetapkan') : 'Menunggu pengumuman resmi' }}</p>
                                <p><span class="font-semibold">Daftar Ulang:</span> {{ $result->status_daftar_ulang_label }}</p>
                            </div>
                        </div>
                        <div class="rounded-3xl bg-slate-50 p-6">
                            <p class="text-xs font-bold uppercase tracking-[0.25em] text-slate-400 mb-3">Catatan Panitia</p>
                            <p class="text-sm text-slate-700 leading-relaxed">{{ $result->period?->isAnnouncementPublished() ? ($result->selection_notes ?: ($result->catatan_verifikator ?: 'Belum ada catatan dari panitia. Silakan cek secara berkala.')) : ($result->catatan_verifikator ?: 'Belum ada catatan dari panitia. Silakan cek secara berkala.') }}</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.25em] text-slate-400 mb-4">Dokumen Terkirim</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($result->documents as $document)
                            <div wire:key="status-document-{{ $document->id }}" class="rounded-2xl border border-slate-200 p-4 flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ $document->jenis_dokumen }}</p>
                                    <p class="text-xs text-slate-500 mt-1">{{ $document->status_verifikasi_label }}</p>
                                </div>
                                <a href="{{ Storage::url($document->file_path) }}" target="_blank" class="text-sm font-bold text-blue-600 hover:text-blue-700">Lihat</a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @else
                <div class="mt-8 rounded-[28px] bg-white border border-red-100 p-8 text-center text-red-600 font-semibold shadow-sm">
                    Data pendaftaran tidak ditemukan. Periksa kembali nomor pendaftaran atau NISN dan tanggal lahir.
                    <div class="mt-4 flex flex-wrap items-center justify-center gap-3">
                        <a href="{{ route('ppdb.form') }}" class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white hover:bg-slate-800 transition">Buka Form Pendaftaran</a>
                        <a href="{{ route('ppdb.index') }}" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100 transition">Lihat Info PPDB</a>
                    </div>
                </div>
                @endif
            @endif
        </div>
    </section>
</div>
