{{-- Step 3: Asal Sekolah & Pilihan Jurusan --}}
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Sekolah Asal & Pilihan Jurusan</h2>
        <p class="mt-1 text-sm text-slate-500">Data sekolah SMP/MTs asal dan pilihan program keahlian yang diminati.</p>
    </div>

    {{-- Card: Asal Sekolah --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-4">
        <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
            <div class="w-7 h-7 bg-amber-50 rounded-lg flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
            </div>
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Asal Sekolah</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Sekolah (SMP/MTs) <span class="text-red-500">*</span></label>
                <input wire:model.blur="asal_sekolah" type="text" placeholder="Contoh: SMP Negeri 1 Kolaka" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                @error('asal_sekolah') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat Sekolah <span class="text-red-500">*</span></label>
                <input wire:model.blur="alamat_sekolah" type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                @error('alamat_sekolah') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nilai Rata-rata Rapor <span class="text-red-500">*</span></label>
            <input wire:model.blur="nilai_rata_rata" type="number" step="0.01" min="0" max="100" placeholder="Contoh: 85.50" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition sm:max-w-xs">
            @error('nilai_rata_rata') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- Card: Jalur & Jurusan --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-4">
        <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
            <div class="w-7 h-7 bg-indigo-50 rounded-lg flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-indigo-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 3 21 3 21 8"/><line x1="4" y1="20" x2="21" y2="3"/><polyline points="21 16 21 21 16 21"/><line x1="15" y1="15" x2="21" y2="21"/><line x1="4" y1="4" x2="9" y2="9"/></svg>
            </div>
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Jalur & Pilihan Jurusan</span>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jalur Pendaftaran <span class="text-red-500">*</span></label>
            <select wire:model="track_id" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                <option value="">Pilih Jalur</option>
                @foreach($period->tracks as $track)
                    <option wire:key="track-opt-{{ $track->id }}" value="{{ $track->id }}">{{ $track->nama_jalur }}</option>
                @endforeach
            </select>
            @error('track_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jurusan Pilihan 1 <span class="text-red-500">*</span></label>
                <select wire:model="pilihan_program_1_id" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                    <option value="">Pilih Jurusan</option>
                    @foreach($programs as $program)
                        <option wire:key="prog1-{{ $program->id }}" value="{{ $program->id }}">{{ $program->nama_jurusan }}</option>
                    @endforeach
                </select>
                @error('pilihan_program_1_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jurusan Pilihan 2 <span class="text-red-500">*</span></label>
                <select wire:model="pilihan_program_2_id" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                    <option value="">Pilih Jurusan</option>
                    @foreach($programs as $program)
                        <option wire:key="prog2-{{ $program->id }}" value="{{ $program->id }}">{{ $program->nama_jurusan }}</option>
                    @endforeach
                </select>
                @error('pilihan_program_2_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jurusan Pilihan 3 <span class="text-red-500">*</span></label>
                <select wire:model="pilihan_program_3_id" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                    <option value="">Pilih Jurusan</option>
                    @foreach($programs as $program)
                        <option wire:key="prog3-{{ $program->id }}" value="{{ $program->id }}">{{ $program->nama_jurusan }}</option>
                    @endforeach
                </select>
                @error('pilihan_program_3_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="bg-slate-50 rounded-xl px-4 py-3 text-[11px] text-slate-500">
            <strong class="text-slate-600">Tips:</strong> Pilihan 1 adalah jurusan utama. Pilihan 2 dan 3 menjadi cadangan jika kuota jurusan utama sudah penuh.
        </div>
    </div>

    @include('livewire.frontend.ppdb-form.partials.nav-buttons', ['showPrev' => true, 'showNext' => true])
</div>
