{{-- Step 2: Data Pribadi Calon Peserta Didik --}}
<div class="space-y-6">
    {{-- Header --}}
    <div>
        <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Data Pribadi</h2>
        <p class="mt-1 text-sm text-slate-500">Isi data identitas sesuai dokumen resmi (Akta/KK/Ijazah). Kolom bertanda <span class="text-red-500 font-bold">*</span> wajib diisi.</p>
    </div>

    {{-- Card: Identitas Utama --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-4">
        <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
            <div class="w-7 h-7 bg-blue-50 rounded-lg flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Identitas</span>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
            <input wire:model.blur="nama_lengkap" type="text" placeholder="Sesuai ijazah/akta kelahiran" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
            @error('nama_lengkap') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">NISN <span class="text-red-500">*</span></label>
                <input wire:model.live.debounce.300ms="nisn" inputmode="numeric" pattern="[0-9]*" type="text" maxlength="10" placeholder="10 digit" @class([
                    'w-full rounded-xl border bg-slate-50/50 px-4 py-3 text-sm outline-none focus:ring-2 focus:bg-white transition',
                    'border-slate-200 focus:border-blue-500 focus:ring-blue-500/10' => ! $nisnRealtimeWarning,
                    'border-red-300 bg-red-50/40 text-red-800 focus:border-red-500 focus:ring-red-500/10' => $nisnRealtimeWarning,
                ])>
                @if($nisnRealtimeWarning)
                    <p class="text-xs text-red-600 mt-1 font-semibold">{{ $nisnRealtimeWarning }}</p>
                @endif
                @error('nisn') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">NIK <span class="text-red-500">*</span></label>
                <input wire:model.blur="nik" type="text" maxlength="20" placeholder="16 digit" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                @error('nik') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tempat Lahir <span class="text-red-500">*</span></label>
                <input wire:model.blur="tempat_lahir" type="text" placeholder="Contoh: Kolaka" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                @error('tempat_lahir') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal Lahir <span class="text-red-500">*</span></label>
                <input wire:model.blur="tanggal_lahir" type="date" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                @error('tanggal_lahir') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nomor KK</label>
                <input wire:model.blur="no_kk" type="text" maxlength="16" placeholder="16 digit" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                @error('no_kk') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">No Registrasi Akta Lahir</label>
                <input wire:model.blur="no_registrasi_akta_lahir" type="text" placeholder="Nomor registrasi akta" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                @error('no_registrasi_akta_lahir') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jenis Kelamin <span class="text-red-500">*</span></label>
                <select wire:model="jenis_kelamin" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Agama <span class="text-red-500">*</span></label>
                <select wire:model="agama" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                    <option value="Islam">Islam</option>
                    <option value="Kristen">Kristen</option>
                    <option value="Katolik">Katolik</option>
                    <option value="Hindu">Hindu</option>
                    <option value="Buddha">Buddha</option>
                    <option value="Konghucu">Konghucu</option>
                    <option value="Kepercayaan kepada Tuhan YME">Kepercayaan kepada Tuhan YME</option>
                </select>
                @error('agama') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kewarganegaraan <span class="text-red-500">*</span></label>
                <select wire:model="kewarganegaraan" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                    <option value="WNI">Indonesia (WNI)</option>
                    <option value="WNA">Asing (WNA)</option>
                </select>
                @error('kewarganegaraan') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kebutuhan Khusus</label>
                <select wire:model="kebutuhan_khusus" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                    <option value="Tidak">Tidak</option>
                    <option value="Netra (A)">Netra (A)</option>
                    <option value="Rungu (B)">Rungu (B)</option>
                    <option value="Grahita ringan (C)">Grahita ringan (C)</option>
                    <option value="Grahita sedang (C1)">Grahita sedang (C1)</option>
                    <option value="Daksa ringan (D)">Daksa ringan (D)</option>
                    <option value="Daksa sedang (D1)">Daksa sedang (D1)</option>
                    <option value="Laras (E)">Laras (E)</option>
                    <option value="Wicara (F)">Wicara (F)</option>
                    <option value="Tuna ganda (G)">Tuna ganda (G)</option>
                    <option value="Hiper aktif (H)">Hiper aktif (H)</option>
                    <option value="Cerdas Istimewa (I)">Cerdas Istimewa (I)</option>
                    <option value="Bakat Istimewa (J)">Bakat Istimewa (J)</option>
                    <option value="Kesulitan Belajar (K)">Kesulitan Belajar (K)</option>
                    <option value="Narkoba (N)">Narkoba (N)</option>
                    <option value="Indigo (O)">Indigo (O)</option>
                    <option value="Down Sindrome (P)">Down Sindrome (P)</option>
                    <option value="Autis (Q)">Autis (Q)</option>
                </select>
                @error('kebutuhan_khusus') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        @if($kewarganegaraan === 'WNA')
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Negara <span class="text-red-500">*</span></label>
                <input wire:model.blur="negara_asal" type="text" placeholder="Contoh: Malaysia" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                @error('negara_asal') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        @endif
    </div>

    {{-- Card: Kontak --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-4">
        <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
            <div class="w-7 h-7 bg-emerald-50 rounded-lg flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>
            </div>
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kontak & Alamat</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Telepon Rumah</label>
                <input wire:model.blur="nomor_telepon_rumah" type="text" placeholder="0405xxxxxx" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                @error('nomor_telepon_rumah') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nomor HP Aktif <span class="text-red-500">*</span></label>
                <input wire:model.blur="nomor_hp" type="text" placeholder="08xxxxxxxxxx" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                <p class="mt-1 text-[11px] text-slate-400">Format: 08xxx atau 62xxx. Wajib aktif.</p>
                @error('nomor_hp') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email Aktif <span class="text-red-500">*</span></label>
                <input wire:model.blur="email" type="email" placeholder="email@contoh.com" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                <p class="mt-1 text-[11px] text-slate-400">Digunakan untuk notifikasi status pendaftaran.</p>
                @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat Rumah <span class="text-red-500">*</span></label>
            <textarea wire:model.blur="alamat_lengkap" rows="2" placeholder="Jalan, nomor rumah, dusun/lingkungan..." class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition resize-none"></textarea>
            @error('alamat_lengkap') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-5 gap-3">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">RT</label>
                <input wire:model.blur="rt" type="text" placeholder="004" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                @error('rt') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">RW</label>
                <input wire:model.blur="rw" type="text" placeholder="003" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                @error('rw') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kelurahan <span class="text-red-500">*</span></label>
                <input wire:model.blur="kelurahan" type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                @error('kelurahan') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kecamatan <span class="text-red-500">*</span></label>
                <input wire:model.blur="kecamatan" type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                @error('kecamatan') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kode Pos</label>
                <input wire:model.blur="kode_pos" type="text" placeholder="93511" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                @error('kode_pos') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Dusun</label>
            <input wire:model.blur="nama_dusun" type="text" placeholder="Contoh: Dusun Cempaka" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
            @error('nama_dusun') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Lintang</label>
                <input wire:model.blur="lintang" type="text" placeholder="-4.03971" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                @error('lintang') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Bujur</label>
                <input wire:model.blur="bujur" type="text" placeholder="121.59383" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                @error('bujur') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    {{-- Card: Data Fisik --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-4">
        <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
            <div class="w-7 h-7 bg-purple-50 rounded-lg flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-purple-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-6"/></svg>
            </div>
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Data Fisik & Keluarga</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Anak Ke- <span class="text-red-500">*</span></label>
                <input wire:model.blur="anak_ke" type="number" min="1" max="20" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                @error('anak_ke') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jml Saudara <span class="text-red-500">*</span></label>
                <input wire:model.blur="jumlah_saudara" type="number" min="0" max="20" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                @error('jumlah_saudara') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">TB (cm) <span class="text-red-500">*</span></label>
                <input wire:model.blur="tinggi_badan" type="number" min="30" max="250" placeholder="165" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                @error('tinggi_badan') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">BB (kg) <span class="text-red-500">*</span></label>
                <input wire:model.blur="berat_badan" type="number" min="2" max="300" placeholder="55" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                @error('berat_badan') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Gol. Darah <span class="text-red-500">*</span></label>
                <select wire:model="gol_darah" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                    <option value="">Pilih</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="AB">AB</option>
                    <option value="O">O</option>
                </select>
                @error('gol_darah') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Ukuran Seragam <span class="text-red-500">*</span></label>
                <select wire:model="ukuran_seragam" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                    <option value="">Pilih</option>
                    <option value="XS">XS</option>
                    <option value="S">S</option>
                    <option value="M">M</option>
                    <option value="L">L</option>
                    <option value="XL">XL</option>
                    <option value="XXL">XXL</option>
                    <option value="3XL">3XL</option>
                </select>
                @error('ukuran_seragam') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Lingkar Kepala (cm)</label>
                <input wire:model.blur="lingkar_kepala" type="number" min="20" max="100" placeholder="54" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                @error('lingkar_kepala') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tempat Tinggal</label>
                <select wire:model="tempat_tinggal" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                    <option value="">Pilih</option>
                    <option value="Bersama orang tua">Bersama orang tua</option>
                    <option value="Wali">Wali</option>
                    <option value="Kos">Kos</option>
                    <option value="Asrama">Asrama</option>
                    <option value="Panti Asuhan">Panti Asuhan</option>
                </select>
                @error('tempat_tinggal') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Moda Transportasi</label>
                <select wire:model="moda_transportasi" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                    <option value="">Pilih</option>
                    <option value="Jalan kaki">Jalan kaki</option>
                    <option value="Kendaraan pribadi">Kendaraan pribadi</option>
                    <option value="Kendaraan umum">Kendaraan umum</option>
                    <option value="Jemputan sekolah">Jemputan sekolah</option>
                    <option value="Kereta api">Kereta api</option>
                    <option value="Ojek">Ojek</option>
                    <option value="Andong/Bendi/Sado/Dokar/Delman/Beca">Andong/Bendi/Sado/Dokar/Delman/Beca</option>
                    <option value="Perahu penyebrangan/Rakit/Getek">Perahu penyebrangan/Rakit/Getek</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
                @error('moda_transportasi') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jarak Rumah</label>
                <select wire:model="jarak_tempat_tinggal_kategori" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                    <option value="">Pilih</option>
                    <option value="kurang_dari_1km">Kurang dari 1 km</option>
                    <option value="lebih_dari_1km">Lebih dari 1 km</option>
                </select>
                @error('jarak_tempat_tinggal_kategori') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jarak (km)</label>
                <input wire:model.blur="jarak_tempat_tinggal_km" type="number" min="0" step="0.01" placeholder="2.50" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                @error('jarak_tempat_tinggal_km') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Waktu Tempuh</label>
                <div class="grid grid-cols-2 gap-2">
                    <input wire:model.blur="waktu_tempuh_jam" type="number" min="0" max="24" placeholder="Jam" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                    <input wire:model.blur="waktu_tempuh_menit" type="number" min="0" max="59" placeholder="Menit" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                </div>
                @error('waktu_tempuh_jam') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                @error('waktu_tempuh_menit') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    {{-- Card: Bantuan & Kartu Sosial --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-4">
        <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
            <div class="w-7 h-7 bg-amber-50 rounded-lg flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
            </div>
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Bantuan & Kartu Sosial</span>
        </div>

        {{-- KIP Section --}}
        <div class="space-y-3">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Apakah punya KIP (Kartu Indonesia Pintar)?</label>
                <div class="flex items-center gap-5 pt-2">
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input wire:model.live="punya_kip" type="radio" value="1" class="text-blue-600 focus:ring-blue-500">
                        Ya
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input wire:model.live="punya_kip" type="radio" value="0" class="text-blue-600 focus:ring-blue-500">
                        Tidak
                    </label>
                </div>
                @error('punya_kip') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            @if($punya_kip === '1')
                <div class="rounded-xl border border-blue-100 bg-blue-50/40 p-4 space-y-3">
                    <p class="text-xs font-semibold text-blue-700">📋 Lengkapi data KIP</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">No. KIP</label>
                            <input wire:model.blur="nomor_kip" type="text" placeholder="Nomor Kartu KIP" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 transition">
                            @error('nomor_kip') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama di KIP</label>
                            <input wire:model.blur="nama_di_kip" type="text" placeholder="Nama sesuai di kartu" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 transition">
                            @error('nama_di_kip') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div>
                        @include('livewire.frontend.ppdb-form.partials.upload-field', [
                            'wireModel' => 'file_kip',
                            'label' => 'Upload Foto/Scan KIP',
                            'required' => false,
                            'accept' => '.jpg,.jpeg,.png,.pdf',
                            'hint' => 'JPG/PNG/PDF, maks 2 MB',
                            'fileVar' => $file_kip ?? null,
                            'isPdf' => false,
                        ])
                    </div>
                </div>
            @endif
        </div>

        {{-- Kartu Kesejahteraan Section --}}
        <div class="space-y-3">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Punya Kartu Kesejahteraan Lainnya?</label>
                <select wire:model.live="jenis_kesejahteraan" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                    <option value="">Tidak punya / Pilih jenis kartu</option>
                    <option value="PKH">PKH (Program Keluarga Harapan)</option>
                    <option value="PIP">PIP (Program Indonesia Pintar)</option>
                    <option value="Kartu Perlindungan Sosial">Kartu Perlindungan Sosial</option>
                    <option value="Kartu Keluarga Sejahtera">Kartu Keluarga Sejahtera</option>
                    <option value="Kartu Kesehatan">Kartu Kesehatan</option>
                </select>
                @error('jenis_kesejahteraan') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            @if($jenis_kesejahteraan !== '')
                <div class="rounded-xl border border-amber-100 bg-amber-50/40 p-4 space-y-3">
                    <p class="text-xs font-semibold text-amber-700">📋 Lengkapi data kartu {{ $jenis_kesejahteraan }}</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">No. Kartu</label>
                            <input wire:model.blur="nomor_kartu_kesejahteraan" type="text" placeholder="Nomor kartu" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 transition">
                            @error('nomor_kartu_kesejahteraan') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama di Kartu</label>
                            <input wire:model.blur="nama_di_kartu_kesejahteraan" type="text" placeholder="Nama sesuai di kartu" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 transition">
                            @error('nama_di_kartu_kesejahteraan') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div>
                        @include('livewire.frontend.ppdb-form.partials.upload-field', [
                            'wireModel' => 'file_kesejahteraan',
                            'label' => 'Upload Foto/Scan Kartu',
                            'required' => false,
                            'accept' => '.jpg,.jpeg,.png,.pdf',
                            'hint' => 'JPG/PNG/PDF, maks 2 MB',
                            'fileVar' => $file_kesejahteraan ?? null,
                            'isPdf' => false,
                        ])
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Navigation --}}
    @include('livewire.frontend.ppdb-form.partials.nav-buttons', ['showPrev' => true, 'showNext' => true])
</div>
