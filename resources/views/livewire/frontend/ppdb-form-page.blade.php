<div>
    @php($periodQuery = $selectedPeriodId ? ['periode' => $selectedPeriodId] : [])

    <section class="relative overflow-hidden bg-slate-950 text-white py-24 noise">
        <div class="absolute inset-0 bg-mesh-hero opacity-70"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 items-start">
                <div class="lg:col-span-3">
                    <span class="inline-flex px-4 py-1.5 rounded-full glass text-xs font-bold uppercase tracking-[0.3em] text-blue-300 mb-6">Form Pendaftaran</span>
                    <h1 class="text-4xl md:text-6xl font-black tracking-tight leading-[0.95]">Lengkapi <span class="text-gradient">Data Pendaftaran</span></h1>
                    <p class="mt-6 text-lg text-slate-300 max-w-2xl leading-relaxed">Halaman ini disiapkan khusus untuk pengisian formulir online. Sebelum mengirim, pastikan data identitas, pilihan jurusan, dan berkas sudah lengkap agar proses verifikasi berjalan lancar.</p>
                    <div class="mt-8 max-w-xl rounded-[24px] border border-white/10 bg-white/5 p-4 backdrop-blur">
                        <label class="text-xs font-bold uppercase tracking-[0.25em] text-slate-300">Pilih Tahun Ajaran / Gelombang</label>
                        <select wire:model.live="selectedPeriod" class="mt-3 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-white outline-none">
                            @foreach($availablePeriods as $periodOption)
                                <option wire:key="form-period-option-{{ $periodOption->id }}" value="{{ $periodOption->id }}">{{ $periodOption->full_label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-wrap gap-4 mt-10">
                        <a href="{{ route('ppdb.index', $periodQuery) }}" class="px-7 py-4 rounded-2xl glass font-bold hover:bg-white/10 transition">Kembali ke Info PPDB</a>
                        <a href="{{ route('ppdb.status') }}" class="px-7 py-4 rounded-2xl bg-white text-slate-900 font-bold hover:-translate-y-1 transition">Cek Status</a>
                    </div>
                </div>
                <div class="lg:col-span-2 space-y-4">
                    <div class="glass rounded-[28px] p-6">
                        <p class="text-xs uppercase tracking-[0.25em] text-slate-400 font-bold">Periode Dipilih</p>
                        <h2 class="text-2xl font-black mt-3">{{ $period?->nama_periode ?? 'Belum Dibuka' }}</h2>
                        <p class="text-sm text-slate-300 mt-1">{{ $period?->tahun_ajaran ?? 'Menunggu publikasi panitia' }} @if($period?->gelombang_label) · {{ $period->gelombang_label }} @endif</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="glass rounded-[24px] p-5">
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-400 font-bold">Jalur</p>
                            <p class="text-3xl font-black mt-2">{{ $period?->tracks?->count() ?? 0 }}</p>
                        </div>
                        <div class="glass rounded-[24px] p-5">
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-400 font-bold">Pendaftar</p>
                            <p class="text-3xl font-black mt-2">{{ $applicationsCount }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('livewire.frontend.partials.ppdb-journey-nav', [
        'active' => 'form',
        'periodQuery' => $periodQuery,
    ])

    @if($period)
    <section class="py-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 xl:grid-cols-5 gap-8">
            <div class="xl:col-span-2 space-y-6">
                <div class="bg-white rounded-[28px] border border-slate-100 shadow-sm p-7">
                    <p class="text-xs font-bold uppercase tracking-[0.25em] text-blue-500 mb-4">Checklist Wajib</p>
                    <ul class="space-y-3 text-sm text-slate-700">
                        <li><span class="font-bold text-slate-900">1.</span> Isi identitas siswa sesuai dokumen resmi.</li>
                        <li><span class="font-bold text-slate-900">2.</span> Gunakan nomor HP dan email aktif agar panitia mudah menghubungi.</li>
                        <li><span class="font-bold text-slate-900">3.</span> Pilih jurusan utama dan cadangan dengan cermat.</li>
                        <li><span class="font-bold text-slate-900">4.</span> Upload berkas yang terbaca jelas.</li>
                        <li><span class="font-bold text-slate-900">5.</span> Simpan nomor pendaftaran setelah formulir terkirim.</li>
                    </ul>
                    <div class="mt-5 rounded-2xl bg-blue-50 border border-blue-100 px-4 py-3 text-xs text-blue-700 font-semibold">
                        Estimasi pengisian 8-12 menit jika data dan dokumen sudah siap.
                    </div>
                </div>
                <div class="bg-slate-900 text-white rounded-[28px] p-7 noise relative overflow-hidden">
                    <div class="absolute inset-0 bg-mesh-hero opacity-45"></div>
                    <div class="relative">
                        <p class="text-xs font-bold uppercase tracking-[0.25em] text-blue-300 mb-4">Dokumen yang Disiapkan</p>
                        <ul class="space-y-3 text-sm text-slate-300">
                            <li>Kartu Keluarga (JPG/PNG/PDF)</li>
                            <li>Akta Kelahiran (JPG/PNG/PDF)</li>
                            <li>Rapor atau rekap nilai (JPG/PNG/PDF)</li>
                            <li>Pas foto terbaru (JPG/PNG)</li>
                            <li>SKL bila sudah tersedia (opsional)</li>
                        </ul>
                        <p class="mt-4 text-xs text-slate-400">Ukuran maksimal per berkas 4 MB.</p>
                    </div>
                </div>
                <div class="rounded-[28px] border border-slate-200 bg-white p-6">
                    <p class="text-xs uppercase tracking-[0.25em] text-slate-400 font-bold">Setelah Kirim Form</p>
                    <p class="mt-3 text-sm text-slate-700 leading-relaxed">Buka halaman cek status secara berkala. Jika panitia memberi status Perlu Revisi, ikuti catatan verifikator yang tampil di halaman status.</p>
                </div>
                @if($period && ! $period->isRegistrationOpen())
                <div class="rounded-[28px] border border-amber-200 bg-amber-50 p-6">
                    <p class="text-xs uppercase tracking-[0.25em] text-amber-600 font-bold">Pendaftaran Belum Dibuka</p>
                    <p class="text-sm text-amber-700 mt-3">Periode yang dipilih dapat dilihat, tetapi saat ini belum menerima pendaftaran baru. Anda bisa memilih gelombang lain yang sedang aktif.</p>
                </div>
                @endif
                @if($submittedNumber)
                <div class="rounded-[28px] border border-emerald-200 bg-emerald-50 p-6">
                    <p class="text-xs uppercase tracking-[0.25em] text-emerald-600 font-bold">Pendaftaran Berhasil</p>
                    <h4 class="text-2xl font-black text-emerald-800 mt-3">{{ $submittedNumber }}</h4>
                    <p class="text-sm text-emerald-700 mt-3">Nomor pendaftaran Anda berhasil dibuat. Simpan nomor ini untuk cek status dan pengumuman resmi.</p>
                    @if($submittedDownloadUrl)
                    <a href="{{ $submittedDownloadUrl }}" target="_blank" rel="noopener" class="mt-4 inline-flex rounded-xl bg-emerald-700 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-emerald-800">Unduh Formulir PDF</a>
                    @endif
                    <div class="mt-3 rounded-xl border border-emerald-200 bg-white/70 px-3 py-2 text-xs text-emerald-900">
                        @if($submissionEmailSent)
                            Konfirmasi pendaftaran juga sudah dikirim ke email aktif Anda: <span class="font-bold">{{ $submittedEmail }}</span>
                        @else
                            Data pendaftaran tetap tersimpan, tetapi email konfirmasi belum terkirim. Silakan unduh formulir PDF dan hubungi admin bila diperlukan.
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <div class="xl:col-span-3 bg-white rounded-[32px] border border-slate-100 shadow-sm p-6 md:p-8">
                <div class="mb-8">
                    <p class="text-xs uppercase tracking-[0.25em] text-slate-400 font-bold">Form Online</p>
                    <h3 class="text-2xl md:text-3xl font-black text-slate-900 mt-3">Daftar PPDB Online</h3>
                    <p class="mt-3 text-sm text-slate-500 leading-relaxed">Kolom bertanda <span class="font-bold text-rose-600">*</span> wajib diisi. Untuk user baru, isi dari atas ke bawah agar tidak ada data terlewat.</p>
                </div>

                <form wire:submit="submitApplication" class="space-y-8">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Bagian 1</p>
                        <p class="mt-1 text-sm font-semibold text-slate-800">Data Calon Peserta Didik</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Lengkap *</label>
                            <input wire:model.blur="nama_lengkap" type="text" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                            <p class="mt-1 text-xs text-slate-500">Gunakan nama sesuai ijazah atau kartu keluarga.</p>
                            @error('nama_lengkap') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">NISN</label>
                            <input wire:model.blur="nisn" type="text" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                            @error('nisn') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">NIK</label>
                            <input wire:model.blur="nik" type="text" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                            @error('nik') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Tempat Lahir *</label>
                            <input wire:model.blur="tempat_lahir" type="text" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                            @error('tempat_lahir') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal Lahir *</label>
                            <input wire:model.blur="tanggal_lahir" type="date" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                            @error('tanggal_lahir') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Jenis Kelamin *</label>
                            <select wire:model="jenis_kelamin" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Agama</label>
                            <input wire:model.blur="agama" type="text" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Nomor HP Aktif *</label>
                            <input wire:model.blur="nomor_hp" type="text" placeholder="08xxxx atau 62xxxx" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                            <p class="mt-1 text-xs text-slate-500">Wajib aktif dan tidak boleh sama dengan pendaftar lain.</p>
                            @error('nomor_hp') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Email Aktif *</label>
                            <input wire:model.blur="email" type="email" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                            <p class="mt-1 text-xs text-slate-500">Wajib aktif karena dipakai untuk notifikasi pendaftaran.</p>
                            @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Alamat Rumah *</label>
                            <textarea wire:model.blur="alamat_lengkap" rows="3" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10"></textarea>
                            @error('alamat_lengkap') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">RT / RW</label>
                            <input wire:model.blur="rt_rw" type="text" placeholder="Contoh: 004/003" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                            @error('rt_rw') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Kelurahan</label>
                            <input wire:model.blur="kelurahan" type="text" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                            @error('kelurahan') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Kecamatan</label>
                            <input wire:model.blur="kecamatan" type="text" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                            @error('kecamatan') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Anak Ke-</label>
                            <input wire:model.blur="anak_ke" type="number" min="1" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                            @error('anak_ke') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Jumlah Saudara</label>
                            <input wire:model.blur="jumlah_saudara" type="number" min="0" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                            @error('jumlah_saudara') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Tinggi Badan (cm)</label>
                            <input wire:model.blur="tinggi_badan" type="number" min="30" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                            @error('tinggi_badan') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Berat Badan (kg)</label>
                            <input wire:model.blur="berat_badan" type="number" min="2" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                            @error('berat_badan') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Golongan Darah</label>
                            <input wire:model.blur="gol_darah" type="text" placeholder="A / B / AB / O" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                            @error('gol_darah') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Ukuran Seragam</label>
                            <input wire:model.blur="ukuran_seragam" type="text" placeholder="Contoh: M" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                            @error('ukuran_seragam') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Bagian 2</p>
                        <p class="mt-1 text-sm font-semibold text-slate-800">Asal Sekolah dan Pilihan Jurusan</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Asal Sekolah *</label>
                            <input wire:model.blur="asal_sekolah" type="text" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                            @error('asal_sekolah') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Alamat Sekolah Asal</label>
                            <input wire:model.blur="alamat_sekolah" type="text" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                            @error('alamat_sekolah') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Nilai Rata-rata</label>
                            <input wire:model.blur="nilai_rata_rata" type="number" step="0.01" min="0" max="100" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                            @error('nilai_rata_rata') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Jalur Pendaftaran *</label>
                            <select wire:model="track_id" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                                <option value="">Pilih Jalur</option>
                                @foreach($period->tracks as $track)
                                <option wire:key="form-track-option-{{ $track->id }}" value="{{ $track->id }}">{{ $track->nama_jalur }}</option>
                                @endforeach
                            </select>
                            @error('track_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Pilihan Jurusan 1 *</label>
                            <select wire:model="pilihan_program_1_id" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                                <option value="">Pilih Jurusan</option>
                                @foreach($programs as $program)
                                <option wire:key="form-program1-option-{{ $program->id }}" value="{{ $program->id }}">{{ $program->nama_jurusan }}</option>
                                @endforeach
                            </select>
                            @error('pilihan_program_1_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Pilihan Jurusan 2</label>
                            <select wire:model="pilihan_program_2_id" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                                <option value="">Tidak memilih cadangan</option>
                                @foreach($programs as $program)
                                <option wire:key="form-program2-option-{{ $program->id }}" value="{{ $program->id }}">{{ $program->nama_jurusan }}</option>
                                @endforeach
                            </select>
                            @error('pilihan_program_2_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Bagian 3</p>
                        <p class="mt-1 text-sm font-semibold text-slate-800">Prestasi Yang Pernah Diraih (Maksimal 3)</p>
                    </div>
                    <div class="space-y-3">
                        @foreach($prestasi as $index => $prestasiItem)
                        <div wire:key="prestasi-row-{{ $index }}" class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Prestasi</label>
                                    <input wire:model.blur="prestasi.{{ $index }}.achievement_name" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                                    @error('prestasi.' . $index . '.achievement_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Juara</label>
                                    <input wire:model.blur="prestasi.{{ $index }}.achievement_rank" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                                    @error('prestasi.' . $index . '.achievement_rank') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Tingkat</label>
                                    <input wire:model.blur="prestasi.{{ $index }}.achievement_level" type="text" placeholder="Sekolah / Kecamatan / Kabupaten / Provinsi" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                                    @error('prestasi.' . $index . '.achievement_level') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="mt-3 flex justify-end">
                                @if(count($prestasi) > 1)
                                <button type="button" wire:click="removePrestasiRow({{ $index }})" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-700 transition hover:bg-rose-100">Hapus Baris</button>
                                @endif
                            </div>
                        </div>
                        @endforeach

                        @error('prestasi') <p class="text-xs text-red-500">{{ $message }}</p> @enderror

                        <button type="button" wire:click="addPrestasiRow" wire:loading.attr="disabled" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-60">
                            Tambah Prestasi
                        </button>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Bagian 4</p>
                        <p class="mt-1 text-sm font-semibold text-slate-800">Data Orang Tua / Wali</p>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="rounded-2xl border border-slate-200 bg-white p-4 space-y-3">
                            <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Data Ayah</p>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Ayah</label>
                                <input wire:model.blur="nama_ayah" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Tempat/Tanggal Lahir Ayah</label>
                                <input wire:model.blur="tempat_tanggal_lahir_ayah" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Pendidikan Terakhir Ayah</label>
                                <input wire:model.blur="pendidikan_terakhir_ayah" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Pekerjaan Ayah</label>
                                <input wire:model.blur="pekerjaan_ayah" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Penghasilan Ayah</label>
                                <input wire:model.blur="penghasilan_ayah" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Alamat Ayah</label>
                                <textarea wire:model.blur="alamat_ayah" rows="2" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10"></textarea>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Kelurahan Ayah</label>
                                    <input wire:model.blur="kelurahan_ayah" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Kecamatan Ayah</label>
                                    <input wire:model.blur="kecamatan_ayah" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">No. HP/WA Ayah</label>
                                <input wire:model.blur="nomor_hp_ayah" type="text" placeholder="08xxxx atau 62xxxx" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                                @error('nomor_hp_ayah') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-white p-4 space-y-3">
                            <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Data Ibu</p>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Ibu</label>
                                <input wire:model.blur="nama_ibu" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Tempat/Tanggal Lahir Ibu</label>
                                <input wire:model.blur="tempat_tanggal_lahir_ibu" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Pendidikan Terakhir Ibu</label>
                                <input wire:model.blur="pendidikan_terakhir_ibu" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Pekerjaan Ibu</label>
                                <input wire:model.blur="pekerjaan_ibu" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Penghasilan Ibu</label>
                                <input wire:model.blur="penghasilan_ibu" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Alamat Ibu</label>
                                <textarea wire:model.blur="alamat_ibu" rows="2" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10"></textarea>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Kelurahan Ibu</label>
                                    <input wire:model.blur="kelurahan_ibu" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Kecamatan Ibu</label>
                                    <input wire:model.blur="kecamatan_ibu" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">No. HP/WA Ibu</label>
                                <input wire:model.blur="nomor_hp_ibu" type="text" placeholder="08xxxx atau 62xxxx" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                                @error('nomor_hp_ibu') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="lg:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Nomor HP Orang Tua (Utama)</label>
                            <input wire:model.blur="nomor_hp_orang_tua" type="text" placeholder="08xxxx atau 62xxxx" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10">
                            @error('nomor_hp_orang_tua') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Bagian 5</p>
                        <p class="mt-1 text-sm font-semibold text-slate-800">Upload Berkas</p>
                        <p class="mt-1 text-xs text-slate-500">Format: JPG, JPEG, PNG, PDF. Maksimal 4 MB per file.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-2">Kartu Keluarga *</label>
                            <input wire:model="file_kk" type="file" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-sm text-slate-500">
                            @error('file_kk') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-2">Akta Kelahiran *</label>
                            <input wire:model="file_akta" type="file" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-sm text-slate-500">
                            @error('file_akta') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-2">Rapor / Nilai *</label>
                            <input wire:model="file_rapor" type="file" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-sm text-slate-500">
                            @error('file_rapor') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-2">Pas Foto *</label>
                            <input wire:model="file_pas_foto" type="file" accept=".jpg,.jpeg,.png" class="w-full text-sm text-slate-500">
                            @error('file_pas_foto') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-600 mb-2">Surat Keterangan Lulus</label>
                            <input wire:model="file_skl" type="file" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-sm text-slate-500">
                            @error('file_skl') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div wire:loading wire:target="file_kk,file_akta,file_rapor,file_pas_foto,file_skl" class="mt-3 rounded-xl border border-blue-100 bg-blue-50 px-4 py-2 text-xs font-semibold text-blue-700">
                        Sedang mengunggah berkas, mohon tunggu sampai proses selesai.
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Catatan Tambahan</label>
                        <textarea wire:model.blur="catatan_pendaftar" rows="3" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10"></textarea>
                    </div>

                    <label class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
                        <input wire:model="persetujuan_data" type="checkbox" class="mt-0.5 h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span>Saya menyatakan data yang diisi benar dan bersedia mengikuti ketentuan PPDB SMK Negeri 1 Kolaka.</span>
                    </label>
                    @error('persetujuan_data') <p class="text-xs text-red-500">{{ $message }}</p> @enderror

                    <div class="rounded-2xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                        <p class="font-semibold">Jika sudah daftar tapi ingin ubah data, silakan hubungi admin.</p>
                        <a href="{{ route('ppdb.contact') }}" class="mt-1 inline-flex text-xs font-bold text-blue-700 hover:text-blue-800">Buka Halaman Hubungi Admin</a>
                    </div>

                    @error('period') <p class="text-sm text-red-600">{{ $message }}</p> @enderror

                    <div class="flex flex-wrap items-center gap-4 pt-2">
                        <button type="submit" wire:loading.attr="disabled" wire:target="submitApplication" class="px-7 py-4 rounded-2xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition disabled:cursor-not-allowed disabled:opacity-60">
                            <span wire:loading.remove wire:target="submitApplication">Kirim Pendaftaran</span>
                            <span wire:loading wire:target="submitApplication">Mengirim Data...</span>
                        </button>
                        <a href="{{ route('ppdb.status') }}" class="text-sm font-bold text-slate-600 hover:text-blue-600">Sudah punya nomor pendaftaran?</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
    @else
    <section class="py-24 bg-slate-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm p-10">
                <span class="inline-flex px-4 py-1.5 rounded-full bg-amber-50 text-amber-600 text-xs font-bold uppercase tracking-[0.25em] mb-4">Belum Aktif</span>
                <h2 class="text-3xl font-black text-slate-900">Periode PPDB Belum Dipublikasikan</h2>
                <p class="text-slate-500 mt-4 max-w-2xl mx-auto">Panitia belum membuka formulir pendaftaran aktif. Silakan kembali ke halaman informasi PPDB untuk melihat jadwal dan pengumuman terbaru.</p>
                <a href="{{ route('ppdb.index') }}" class="inline-flex mt-8 px-6 py-3 rounded-2xl bg-slate-900 text-white font-bold hover:bg-slate-800 transition">Kembali ke Info PPDB</a>
            </div>
        </div>
    </section>
    @endif
</div>