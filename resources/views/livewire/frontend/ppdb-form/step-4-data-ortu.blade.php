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

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Ayah <span class="text-red-500">*</span></label>
            <input wire:model.blur="nama_ayah" type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
            @error('nama_ayah') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
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
                    <option value="SD">SD / Sederajat</option>
                    <option value="SMP">SMP / Sederajat</option>
                    <option value="SMA">SMA / Sederajat</option>
                    <option value="D3">D3</option>
                    <option value="S1">S1</option>
                    <option value="S2">S2</option>
                    <option value="S3">S3</option>
                </select>
                @error('pendidikan_terakhir_ayah') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pekerjaan <span class="text-red-500">*</span></label>
                <input wire:model.blur="pekerjaan_ayah" type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                @error('pekerjaan_ayah') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Penghasilan <span class="text-red-500">*</span></label>
                <select wire:model="penghasilan_ayah" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                    <option value="">Pilih</option>
                    <option value="< Rp 1.000.000">< Rp 1.000.000</option>
                    <option value="Rp 1.000.000 - 3.000.000">Rp 1 - 3 juta</option>
                    <option value="Rp 3.000.000 - 5.000.000">Rp 3 - 5 juta</option>
                    <option value="> Rp 5.000.000">> Rp 5.000.000</option>
                </select>
                @error('penghasilan_ayah') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
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

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Ibu <span class="text-red-500">*</span></label>
            <input wire:model.blur="nama_ibu" type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
            @error('nama_ibu') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
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
                    <option value="SD">SD / Sederajat</option>
                    <option value="SMP">SMP / Sederajat</option>
                    <option value="SMA">SMA / Sederajat</option>
                    <option value="D3">D3</option>
                    <option value="S1">S1</option>
                    <option value="S2">S2</option>
                    <option value="S3">S3</option>
                </select>
                @error('pendidikan_terakhir_ibu') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pekerjaan <span class="text-red-500">*</span></label>
                <input wire:model.blur="pekerjaan_ibu" type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                @error('pekerjaan_ibu') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Penghasilan <span class="text-red-500">*</span></label>
                <select wire:model="penghasilan_ibu" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                    <option value="">Pilih</option>
                    <option value="< Rp 1.000.000">< Rp 1.000.000</option>
                    <option value="Rp 1.000.000 - 3.000.000">Rp 1 - 3 juta</option>
                    <option value="Rp 3.000.000 - 5.000.000">Rp 3 - 5 juta</option>
                    <option value="> Rp 5.000.000">> Rp 5.000.000</option>
                </select>
                @error('penghasilan_ibu') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
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
