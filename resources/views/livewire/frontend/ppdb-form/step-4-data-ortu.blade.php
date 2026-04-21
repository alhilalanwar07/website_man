{{-- Step 4: Data Orang Tua --}}
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Data Orang Tua / Wali</h2>
        <p class="mt-1 text-sm text-slate-500">Semua kolom bertanda <span class="text-red-500 font-bold">*</span> wajib diisi. Minimal satu nomor HP/WA wajib diisi.</p>
    </div>

    {{-- Card: Data Ayah --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-4">
        <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
            <div class="w-7 h-7 bg-sky-50 rounded-lg flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-sky-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Data Ayah</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Ayah <span class="text-red-500">*</span></label>
                <input wire:model.blur="nama_ayah" type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                @error('nama_ayah') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">NIK Ayah</label>
                <input wire:model.blur="nik_ayah" type="text" maxlength="16" placeholder="16 digit" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                @error('nik_ayah') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tempat Lahir <span class="text-red-500">*</span></label>
                <input wire:model.blur="tempat_lahir_ayah" type="text" placeholder="Contoh: Kolaka" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                @error('tempat_lahir_ayah') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal Lahir <span class="text-red-500">*</span></label>
                <input wire:model.blur="tanggal_lahir_ayah" type="date" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                @error('tanggal_lahir_ayah') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pendidikan Terakhir <span class="text-red-500">*</span></label>
                <select wire:model="pendidikan_terakhir_ayah" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                    <option value="">Pilih</option>
                    <option value="01 Tidak sekolah">01 Tidak sekolah</option>
                    <option value="02 Putus SD">02 Putus SD</option>
                    <option value="03 SD Sederajat">03 SD Sederajat</option>
                    <option value="04 SMP Sederajat">04 SMP Sederajat</option>
                    <option value="05 SMA Sederajat">05 SMA Sederajat</option>
                    <option value="06 D1">06 D1</option>
                    <option value="07 D2">07 D2</option>
                    <option value="08 D3">08 D3</option>
                    <option value="09 D4/S1">09 D4/S1</option>
                    <option value="10 S2">10 S2</option>
                    <option value="11 S3">11 S3</option>
                </select>
                @error('pendidikan_terakhir_ayah') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pekerjaan <span class="text-red-500">*</span></label>
                <select wire:model="pekerjaan_ayah" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                    <option value="">Pilih</option>
                    <option value="01 Tidak bekerja">01 Tidak bekerja</option>
                    <option value="02 Nelayan">02 Nelayan</option>
                    <option value="03 Petani">03 Petani</option>
                    <option value="04 Peternak">04 Peternak</option>
                    <option value="05 PNS/TNI/POLRI">05 PNS/TNI/POLRI</option>
                    <option value="06 Karyawan Swasta">06 Karyawan Swasta</option>
                    <option value="07 Pedagang Kecil">07 Pedagang Kecil</option>
                    <option value="08 Pedagang Besar">08 Pedagang Besar</option>
                    <option value="09 Wiraswasta">09 Wiraswasta</option>
                    <option value="10 Wirausaha">10 Wirausaha</option>
                    <option value="11 Buruh">11 Buruh</option>
                    <option value="12 Pensiunan">12 Pensiunan</option>
                    <option value="13 Meninggal Dunia">13 Meninggal Dunia</option>
                </select>
                @error('pekerjaan_ayah') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Penghasilan <span class="text-red-500">*</span></label>
                <select wire:model="penghasilan_ayah" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                    <option value="">Pilih</option>
                    <option value="01 < Rp.500.000">01 &lt; Rp.500.000</option>
                    <option value="02 Rp.500.000-Rp.999.999">02 Rp.500.000-Rp.999.999</option>
                    <option value="03 Rp.1.000.000-Rp.1.999.999">03 Rp.1.000.000-Rp.1.999.999</option>
                    <option value="04 Rp.2.000.000-Rp.4.999.999">04 Rp.2.000.000-Rp.4.999.999</option>
                    <option value="05 Rp.5.000.000-Rp.20.000.000">05 Rp.5.000.000-Rp.20.000.000</option>
                    <option value="06 > Rp.20.000.000">06 &gt; Rp.20.000.000</option>
                    <option value="07 Tidak Berpenghasilan">07 Tidak Berpenghasilan</option>
                </select>
                @error('penghasilan_ayah') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Berkebutuhan Khusus</label>
            <select wire:model="kebutuhan_khusus_ayah" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                <option value="01 Tidak">01 Tidak</option>
                <option value="02 Netra (A)">02 Netra (A)</option>
                <option value="03 Rungu (B)">03 Rungu (B)</option>
                <option value="04 Grahita ringan (C)">04 Grahita ringan (C)</option>
                <option value="05 Grahita sedang (C1)">05 Grahita sedang (C1)</option>
                <option value="06 Daksa ringan (D)">06 Daksa ringan (D)</option>
                <option value="07 Daksa sedang (D1)">07 Daksa sedang (D1)</option>
                <option value="08 Laras (E)">08 Laras (E)</option>
                <option value="09 Wicara (F)">09 Wicara (F)</option>
                <option value="10 Tuna ganda (G)">10 Tuna ganda (G)</option>
                <option value="11 Hiper aktif (H)">11 Hiper aktif (H)</option>
                <option value="12 Cerdas Istimewa (I)">12 Cerdas Istimewa (I)</option>
                <option value="13 Bakat Istimewa (J)">13 Bakat Istimewa (J)</option>
                <option value="14 Kesulitan Belajar (K)">14 Kesulitan Belajar (K)</option>
                <option value="15 Narkoba (N)">15 Narkoba (N)</option>
                <option value="16 Indigo (O)">16 Indigo (O)</option>
                <option value="17 Down Sindrome (P)">17 Down Sindrome (P)</option>
                <option value="18 Autis (Q)">18 Autis (Q)</option>
            </select>
            @error('kebutuhan_khusus_ayah') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat <span class="text-red-500">*</span></label>
            <textarea wire:model.blur="alamat_ayah" rows="2" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition resize-none"></textarea>
            @error('alamat_ayah') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kelurahan <span class="text-red-500">*</span></label>
                <input wire:model.blur="kelurahan_ayah" type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                @error('kelurahan_ayah') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kecamatan <span class="text-red-500">*</span></label>
                <input wire:model.blur="kecamatan_ayah" type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                @error('kecamatan_ayah') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">No. HP / WA</label>
                <input wire:model.blur="nomor_hp_ayah" type="text" placeholder="08xxx" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                @error('nomor_hp_ayah') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    {{-- Card: Data Ibu --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-4">
        <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
            <div class="w-7 h-7 bg-pink-50 rounded-lg flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-pink-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Data Ibu</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Ibu <span class="text-red-500">*</span></label>
                <input wire:model.blur="nama_ibu" type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                @error('nama_ibu') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">NIK Ibu</label>
                <input wire:model.blur="nik_ibu" type="text" maxlength="16" placeholder="16 digit" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                @error('nik_ibu') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tempat Lahir <span class="text-red-500">*</span></label>
                <input wire:model.blur="tempat_lahir_ibu" type="text" placeholder="Contoh: Kolaka" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                @error('tempat_lahir_ibu') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal Lahir <span class="text-red-500">*</span></label>
                <input wire:model.blur="tanggal_lahir_ibu" type="date" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                @error('tanggal_lahir_ibu') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pendidikan Terakhir <span class="text-red-500">*</span></label>
                <select wire:model="pendidikan_terakhir_ibu" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                    <option value="">Pilih</option>
                    <option value="01 Tidak sekolah">01 Tidak sekolah</option>
                    <option value="02 Putus SD">02 Putus SD</option>
                    <option value="03 SD Sederajat">03 SD Sederajat</option>
                    <option value="04 SMP Sederajat">04 SMP Sederajat</option>
                    <option value="05 SMA Sederajat">05 SMA Sederajat</option>
                    <option value="06 D1">06 D1</option>
                    <option value="07 D2">07 D2</option>
                    <option value="08 D3">08 D3</option>
                    <option value="09 D4/S1">09 D4/S1</option>
                    <option value="10 S2">10 S2</option>
                    <option value="11 S3">11 S3</option>
                </select>
                @error('pendidikan_terakhir_ibu') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pekerjaan <span class="text-red-500">*</span></label>
                <select wire:model="pekerjaan_ibu" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                    <option value="">Pilih</option>
                    <option value="01 Tidak bekerja">01 Tidak bekerja</option>
                    <option value="02 Nelayan">02 Nelayan</option>
                    <option value="03 Petani">03 Petani</option>
                    <option value="04 Peternak">04 Peternak</option>
                    <option value="05 PNS/TNI/POLRI">05 PNS/TNI/POLRI</option>
                    <option value="06 Karyawan Swasta">06 Karyawan Swasta</option>
                    <option value="07 Pedagang Kecil">07 Pedagang Kecil</option>
                    <option value="08 Pedagang Besar">08 Pedagang Besar</option>
                    <option value="09 Wiraswasta">09 Wiraswasta</option>
                    <option value="10 Wirausaha">10 Wirausaha</option>
                    <option value="11 Buruh">11 Buruh</option>
                    <option value="12 Pensiunan">12 Pensiunan</option>
                    <option value="13 Meninggal Dunia">13 Meninggal Dunia</option>
                </select>
                @error('pekerjaan_ibu') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Penghasilan <span class="text-red-500">*</span></label>
                <select wire:model="penghasilan_ibu" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                    <option value="">Pilih</option>
                    <option value="01 < Rp.500.000">01 &lt; Rp.500.000</option>
                    <option value="02 Rp.500.000-Rp.999.999">02 Rp.500.000-Rp.999.999</option>
                    <option value="03 Rp.1.000.000-Rp.1.999.999">03 Rp.1.000.000-Rp.1.999.999</option>
                    <option value="04 Rp.2.000.000-Rp.4.999.999">04 Rp.2.000.000-Rp.4.999.999</option>
                    <option value="05 Rp.5.000.000-Rp.20.000.000">05 Rp.5.000.000-Rp.20.000.000</option>
                    <option value="06 > Rp.20.000.000">06 &gt; Rp.20.000.000</option>
                    <option value="07 Tidak Berpenghasilan">07 Tidak Berpenghasilan</option>
                </select>
                @error('penghasilan_ibu') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Berkebutuhan Khusus</label>
            <select wire:model="kebutuhan_khusus_ibu" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                <option value="01 Tidak">01 Tidak</option>
                <option value="02 Netra (A)">02 Netra (A)</option>
                <option value="03 Rungu (B)">03 Rungu (B)</option>
                <option value="04 Grahita ringan (C)">04 Grahita ringan (C)</option>
                <option value="05 Grahita sedang (C1)">05 Grahita sedang (C1)</option>
                <option value="06 Daksa ringan (D)">06 Daksa ringan (D)</option>
                <option value="07 Daksa sedang (D1)">07 Daksa sedang (D1)</option>
                <option value="08 Laras (E)">08 Laras (E)</option>
                <option value="09 Wicara (F)">09 Wicara (F)</option>
                <option value="10 Tuna ganda (G)">10 Tuna ganda (G)</option>
                <option value="11 Hiper aktif (H)">11 Hiper aktif (H)</option>
                <option value="12 Cerdas Istimewa (I)">12 Cerdas Istimewa (I)</option>
                <option value="13 Bakat Istimewa (J)">13 Bakat Istimewa (J)</option>
                <option value="14 Kesulitan Belajar (K)">14 Kesulitan Belajar (K)</option>
                <option value="15 Narkoba (N)">15 Narkoba (N)</option>
                <option value="16 Indigo (O)">16 Indigo (O)</option>
                <option value="17 Down Sindrome (P)">17 Down Sindrome (P)</option>
                <option value="18 Autis (Q)">18 Autis (Q)</option>
            </select>
            @error('kebutuhan_khusus_ibu') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Alamat Sama Checkbox --}}
        <div class="flex items-center gap-2 py-2">
            <input wire:model.live="alamat_ibu_sama" type="checkbox" id="alamat_ibu_sama" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
            <label for="alamat_ibu_sama" class="text-sm font-medium text-slate-700 cursor-pointer">Alamat sama dengan ayah</label>
        </div>

        <div x-show="!$wire.alamat_ibu_sama" x-cloak class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat <span class="text-red-500">*</span></label>
                <textarea wire:model.blur="alamat_ibu" rows="2" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition resize-none"></textarea>
                @error('alamat_ibu') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kelurahan <span class="text-red-500">*</span></label>
                    <input wire:model.blur="kelurahan_ibu" type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                    @error('kelurahan_ibu') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kecamatan <span class="text-red-500">*</span></label>
                    <input wire:model.blur="kecamatan_ibu" type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                    @error('kecamatan_ibu') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        @if($alamat_ibu_sama)
            <div class="bg-blue-50 rounded-xl px-4 py-3 text-xs text-blue-700 font-medium flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Alamat ibu menggunakan alamat ayah: {{ $alamat_ayah ?: '(belum diisi)' }}
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">No. HP / WA</label>
                <input wire:model.blur="nomor_hp_ibu" type="text" placeholder="08xxx" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                @error('nomor_hp_ibu') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    @include('livewire.frontend.ppdb-form.partials.nav-buttons', ['showPrev' => true, 'showNext' => true])
</div>
