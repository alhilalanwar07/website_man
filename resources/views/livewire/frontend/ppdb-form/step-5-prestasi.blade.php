{{-- Step 5: Prestasi --}}
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Prestasi</h2>
        <p class="mt-1 text-sm text-slate-500">Cantumkan prestasi yang pernah diraih (opsional, maksimal 3). Jika tidak ada, langsung lanjutkan.</p>
    </div>

    <div class="space-y-4">
        @foreach($prestasi as $index => $prestasiItem)
            <div wire:key="prestasi-row-{{ $index }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 bg-amber-50 rounded-lg flex items-center justify-center">
                            <span class="text-xs font-bold text-amber-600">{{ $index + 1 }}</span>
                        </div>
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Prestasi {{ $index + 1 }}</span>
                    </div>
                    @if(count($prestasi) > 1)
                        <button type="button" wire:click="removePrestasiRow({{ $index }})" class="text-xs font-semibold text-red-500 hover:text-red-700 transition-colors inline-flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                            Hapus
                        </button>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Prestasi</label>
                        <input wire:model.blur="prestasi.{{ $index }}.achievement_name" type="text" placeholder="Contoh: Lomba Cerdas Cermat" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                        @error('prestasi.' . $index . '.achievement_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Juara</label>
                        <input wire:model.blur="prestasi.{{ $index }}.achievement_rank" type="text" placeholder="Contoh: Juara 1" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                        @error('prestasi.' . $index . '.achievement_rank') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tingkat</label>
                        <select wire:model="prestasi.{{ $index }}.achievement_level" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition">
                            <option value="">Pilih</option>
                            <option value="Sekolah">Sekolah</option>
                            <option value="Kecamatan">Kecamatan</option>
                            <option value="Kabupaten/Kota">Kabupaten/Kota</option>
                            <option value="Provinsi">Provinsi</option>
                            <option value="Nasional">Nasional</option>
                            <option value="Internasional">Internasional</option>
                        </select>
                        @error('prestasi.' . $index . '.achievement_level') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        @endforeach

        @error('prestasi') <p class="text-xs text-red-500">{{ $message }}</p> @enderror

        @if(count($prestasi) < 3)
            <button type="button" wire:click="addPrestasiRow" wire:loading.attr="disabled" class="w-full rounded-2xl border-2 border-dashed border-slate-200 bg-white py-4 text-sm font-semibold text-slate-500 hover:border-blue-300 hover:text-blue-600 transition-all inline-flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Prestasi
            </button>
        @endif
    </div>

    @include('livewire.frontend.ppdb-form.partials.nav-buttons', ['showPrev' => true, 'showNext' => true])
</div>
