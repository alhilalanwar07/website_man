<div>
    <section class="relative overflow-hidden bg-slate-950 text-white py-24 noise">
        <div class="absolute inset-0 bg-mesh-hero opacity-70"></div>
        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="inline-flex px-4 py-1.5 rounded-full glass text-xs font-bold uppercase tracking-[0.3em] text-blue-300 mb-6">Daftar Ulang</span>
            <h1 class="text-4xl lg:text-6xl font-black tracking-tight leading-tight">Konfirmasi <span class="text-gradient">Daftar Ulang</span></h1>
            <p class="text-slate-300 max-w-2xl mx-auto mt-4">Peserta yang sudah dinyatakan lulus secara resmi dapat mengirim konfirmasi daftar ulang melalui portal ini selama jadwal masih aktif.</p>
        </div>
    </section>

    @include('livewire.frontend.partials.ppdb-journey-nav', [
        'active' => 'reregistration',
    ])

    <section class="py-20 bg-slate-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-[24px] border border-slate-100 p-6 shadow-sm">
                    <p class="text-xs uppercase tracking-[0.25em] text-slate-400 font-bold">Syarat</p>
                    <h3 class="text-lg font-black text-slate-900 mt-3">Sudah Lulus Resmi</h3>
                    <p class="text-sm text-slate-500 mt-2">Daftar ulang hanya tersedia bagi peserta yang hasil resminya sudah berstatus lulus.</p>
                </div>
                <div class="bg-white rounded-[24px] border border-slate-100 p-6 shadow-sm">
                    <p class="text-xs uppercase tracking-[0.25em] text-slate-400 font-bold">Aksi</p>
                    <h3 class="text-lg font-black text-slate-900 mt-3">Kirim Konfirmasi</h3>
                    <p class="text-sm text-slate-500 mt-2">Masukkan nomor pendaftaran atau NISN lalu kirim catatan konfirmasi kehadiran untuk diverifikasi panitia.</p>
                </div>
                <div class="bg-white rounded-[24px] border border-slate-100 p-6 shadow-sm">
                    <p class="text-xs uppercase tracking-[0.25em] text-slate-400 font-bold">Lanjutan</p>
                    <h3 class="text-lg font-black text-slate-900 mt-3">Akses Siswa Baru</h3>
                    <p class="text-sm text-slate-500 mt-2">Setelah konfirmasi terkirim, halaman ini berubah menjadi panduan lanjutan agar siswa tahu langkah berikutnya tanpa bingung.</p>
                </div>
            </div>

            <div class="bg-white rounded-[28px] border border-slate-100 shadow-sm p-8 md:p-10">
                <form wire:submit="search" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Nomor Pendaftaran / NISN</label>
                        <input wire:model.blur="nomor_pendaftaran" type="text" placeholder="PPDB-2026-0001 atau 0012345678" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 outline-none">
                        <p class="mt-1 text-xs text-slate-500">Boleh gunakan nomor pendaftaran atau NISN.</p>
                        @error('nomor_pendaftaran') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal Lahir</label>
                        <input wire:model.blur="tanggal_lahir" type="date" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 outline-none">
                        @error('tanggal_lahir') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-3 flex flex-wrap items-center gap-3">
                        <button type="submit" wire:loading.attr="disabled" wire:target="search" class="px-6 py-3 bg-slate-900 text-white text-sm font-bold rounded-2xl hover:bg-slate-800 transition disabled:cursor-not-allowed disabled:opacity-60">
                            <span wire:loading.remove wire:target="search">Cari Data Lulus</span>
                            <span wire:loading wire:target="search">Mencari Data...</span>
                        </button>
                        @if($searched)
                        <button type="button" wire:click="resetSearch" class="px-6 py-3 rounded-2xl border border-slate-300 bg-white text-sm font-bold text-slate-700 hover:bg-slate-100 transition">Cari Data Lain</button>
                        @endif
                    </div>
                </form>
            </div>

            @if($searched && $result)
            <div class="bg-white rounded-[28px] border border-slate-100 shadow-sm p-8 space-y-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.25em] text-slate-400">Peserta</p>
                        <h2 class="text-2xl font-black text-slate-900 mt-2">{{ $result->nama_lengkap }}</h2>
                        <p class="text-sm text-slate-500 mt-1">{{ $result->nomor_pendaftaran }} · {{ $result->programDiterima?->nama_jurusan ?? 'Belum ada program final' }}</p>
                    </div>
                    <span class="px-3 py-1.5 rounded-full text-xs font-bold {{ $result->status_daftar_ulang === 'verified' ? 'bg-emerald-50 text-emerald-700' : ($result->status_daftar_ulang === 'submitted' ? 'bg-blue-50 text-blue-700' : ($result->status_daftar_ulang === 'rejected' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700')) }}">
                        {{ $result->status_daftar_ulang_label }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="rounded-3xl bg-slate-50 p-6 text-sm text-slate-700 space-y-2">
                        <p><span class="font-semibold">Jalur:</span> {{ $result->track->nama_jalur }}</p>
                        <p><span class="font-semibold">Hasil Seleksi:</span> {{ $result->hasil_seleksi_label }}</p>
                        <p><span class="font-semibold">Jadwal:</span> {{ $result->period->tanggal_mulai_daftar_ulang?->translatedFormat('d M Y') }} - {{ $result->period->tanggal_selesai_daftar_ulang?->translatedFormat('d M Y') }}</p>
                    </div>
                    <div class="rounded-3xl bg-slate-50 p-6 text-sm text-slate-700">
                        <p class="font-semibold mb-2">Catatan Panitia</p>
                        <p class="leading-relaxed">{{ $result->catatan_daftar_ulang ?: 'Belum ada catatan daftar ulang.' }}</p>
                    </div>
                </div>

                @php
                    $canShowGroupAccess = $result->hasPassedSelection() && in_array($result->status_daftar_ulang, ['submitted', 'verified'], true);
                    $canSubmitReRegistration = $result->hasPassedSelection() && $result->period->isAnnouncementPublished() && in_array($result->status_daftar_ulang, ['pending', 'not_available', 'rejected'], true);
                @endphp

                @if($canShowGroupAccess)
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                    <div class="rounded-[28px] border border-emerald-200 bg-emerald-50 p-6">
                        <p class="text-xs font-bold uppercase tracking-[0.25em] text-emerald-600">Langkah Berikutnya</p>
                        <h3 class="mt-3 text-2xl font-black text-slate-900">
                            {{ $result->status_daftar_ulang === 'verified' ? 'Daftar ulang Anda sudah diverifikasi.' : 'Konfirmasi daftar ulang sudah diterima.' }}
                        </h3>
                        <p class="mt-3 text-sm leading-relaxed text-slate-700">
                            {{ $result->status_daftar_ulang === 'verified'
                                ? 'Silakan masuk ke grup siswa baru untuk menerima informasi resmi lanjutan dari panitia dan sekolah.'
                                : 'Sambil menunggu verifikasi panitia, Anda sudah bisa masuk ke grup siswa baru agar tidak tertinggal informasi lanjutan.' }}
                        </p>

                        @if($result->period->hasStudentWhatsappGroup())
                        <div class="mt-5 flex flex-wrap gap-3">
                            <a href="{{ $result->period->student_whatsapp_group_url }}" target="_blank" rel="noopener" class="inline-flex items-center rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-emerald-700">
                                Gabung {{ $result->period->student_whatsapp_group_label }}
                            </a>
                            <p class="self-center text-xs text-slate-600">Gunakan tombol ini atau scan QR code di samping.</p>
                        </div>
                        <ol class="mt-5 space-y-2 text-sm text-slate-700">
                            <li>1. Pastikan nama akun WhatsApp Anda mudah dikenali.</li>
                            <li>2. Klik tombol gabung atau scan QR code.</li>
                            <li>3. Simpan grup agar tidak ketinggalan pengumuman berikutnya.</li>
                        </ol>
                        @else
                        <div class="mt-5 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-4 text-sm text-amber-800">
                            Tautan grup siswa baru belum dipublikasikan admin. Status daftar ulang Anda sudah tercatat, jadi cukup pantau halaman ini secara berkala.
                        </div>
                        @endif
                    </div>

                    <div class="rounded-[28px] border border-slate-200 bg-white p-6">
                        <p class="text-xs font-bold uppercase tracking-[0.25em] text-slate-400">QR Code Grup</p>
                        @if($result->period->student_whatsapp_group_qr_url)
                            <div class="mt-4 rounded-3xl border border-slate-100 bg-slate-50 p-4">
                                <img src="{{ $result->period->student_whatsapp_group_qr_url }}" alt="QR code grup WhatsApp siswa baru" class="mx-auto aspect-square w-full max-w-[240px] rounded-2xl border border-slate-200 bg-white p-3">
                            </div>
                            <p class="mt-4 text-center text-sm font-semibold text-slate-900">{{ $result->period->student_whatsapp_group_label }}</p>
                            <p class="mt-1 text-center text-xs leading-relaxed text-slate-500">Arahkan kamera atau aplikasi WhatsApp ke QR code ini untuk membuka tautan grup dengan cepat.</p>
                        @else
                            <div class="mt-4 flex min-h-[240px] items-center justify-center rounded-3xl border border-dashed border-slate-200 bg-slate-50 px-6 text-center text-sm leading-relaxed text-slate-500">
                                QR code akan tampil otomatis setelah admin mengisi tautan grup WhatsApp siswa baru.
                            </div>
                        @endif
                    </div>
                </div>
                @endif

                @if($canSubmitReRegistration)
                <form wire:submit="submitReRegistration" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Catatan Daftar Ulang</label>
                        <textarea wire:model.blur="catatan_daftar_ulang" rows="4" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 outline-none" placeholder="Tulis konfirmasi kehadiran, rencana kedatangan, atau catatan penting untuk panitia."></textarea>
                        @error('catatan_daftar_ulang') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" wire:loading.attr="disabled" wire:target="submitReRegistration" class="px-6 py-3 bg-emerald-600 text-white text-sm font-bold rounded-2xl hover:bg-emerald-700 transition disabled:cursor-not-allowed disabled:opacity-60">
                        <span wire:loading.remove wire:target="submitReRegistration">Kirim Konfirmasi Daftar Ulang</span>
                        <span wire:loading wire:target="submitReRegistration">Mengirim Konfirmasi...</span>
                    </button>
                </form>
                @endif

                @if($result->status_daftar_ulang === 'rejected')
                <div class="rounded-2xl border border-rose-200 bg-rose-50 p-5 text-sm text-rose-700">
                    Panitia meminta penyesuaian daftar ulang. Periksa catatan panitia di atas, lalu kirim ulang konfirmasi setelah data Anda diperbarui.
                </div>
                @endif

                @if($submitted)
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 text-sm font-semibold text-emerald-700">
                    Konfirmasi daftar ulang sudah diterima. Halaman ini sudah beralih ke panduan lanjutan agar Anda bisa langsung mengikuti informasi siswa baru.
                </div>
                @endif
            </div>
            @elseif($searched)
            <div class="rounded-[28px] bg-white border border-red-100 p-8 text-center text-red-600 font-semibold shadow-sm">
                Data pendaftar tidak ditemukan. Periksa kembali nomor pendaftaran atau NISN dan tanggal lahir.
                <div class="mt-4 flex flex-wrap items-center justify-center gap-3">
                    <a href="{{ route('ppdb.status') }}" class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white hover:bg-slate-800 transition">Buka Cek Status</a>
                    <a href="{{ route('ppdb.form') }}" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100 transition">Buka Form Pendaftaran</a>
                </div>
            </div>
            @endif
        </div>
    </section>
</div>
