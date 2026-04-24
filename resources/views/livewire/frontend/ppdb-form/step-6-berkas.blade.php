{{-- Step 6: Upload Berkas --}}
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Upload Berkas</h2>
        <p class="mt-1 text-sm text-slate-500">Semua berkas wajib diunggah. Selain pas foto, semua harus berformat <strong class="text-slate-700">PDF</strong>. Maks 4 MB per file (foto maks 2 MB).</p>
    </div>

    {{-- Helper Links --}}
    <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 space-y-2">
        <p class="text-xs font-bold text-blue-800">🔧 Tools Bantu:</p>
        <div class="flex flex-wrap gap-2">
            <a href="https://www.ilovepdf.com/jpg_to_pdf" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-blue-200 rounded-lg text-[11px] font-semibold text-blue-700 hover:bg-blue-50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Gabung Gambar → PDF
            </a>
            <a href="https://www.ilovepdf.com/compress_pdf" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-blue-200 rounded-lg text-[11px] font-semibold text-blue-700 hover:bg-blue-50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="4 14 10 14 10 20"/><polyline points="20 10 14 10 14 4"/><line x1="14" y1="10" x2="21" y2="3"/><line x1="3" y1="21" x2="10" y2="14"/></svg>
                Kompres PDF
            </a>
            <a href="https://tinypng.com" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-blue-200 rounded-lg text-[11px] font-semibold text-blue-700 hover:bg-blue-50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                Kompres Gambar
            </a>
        </div>
    </div>

    {{-- Upload Grid --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

            {{-- 1. Kartu Keluarga (PDF) --}}
            @include('livewire.frontend.ppdb-form.partials.upload-field', [
                'wireModel' => 'file_kk',
                'label' => 'Kartu Keluarga',
                'required' => true,
                'accept' => '.pdf',
                'hint' => 'PDF, maks 4 MB',
                'fileVar' => $file_kk,
                'isPdf' => true,
            ])


            {{-- 5. Pas Foto (Image) --}}
            @include('livewire.frontend.ppdb-form.partials.upload-field', [
                'wireModel' => 'file_pas_foto',
                'label' => 'Pas Foto 3x4 Latar Biru (Pakaian Sekolah)',
                'required' => true,
                'accept' => '.jpg,.jpeg,.png',
                'hint' => 'JPG/PNG, latar biru, maks 2 MB',
                'fileVar' => $file_pas_foto,
                'isPdf' => false,
            ])

            {{-- 6. Ijazah / SKL (PDF) --}}
            @include('livewire.frontend.ppdb-form.partials.upload-field', [
                'wireModel' => 'file_skl',
                'label' => 'Ijazah / SKL',
                'required' => true,
                'accept' => '.pdf',
                'hint' => 'PDF, maks 4 MB',
                'fileVar' => $file_skl,
                'isPdf' => true,
            ])
        </div>

        {{-- Upload indicator --}}
        <div wire:loading wire:target="file_kk,file_pas_foto,file_skl" class="rounded-xl bg-blue-50 border border-blue-100 px-4 py-3 flex items-center gap-2">
            <svg class="animate-spin w-4 h-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            <span class="text-xs font-semibold text-blue-700">Mengunggah berkas, mohon tunggu...</span>
        </div>
    </div>

    {{-- Catatan --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6">
        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Catatan Tambahan <span class="text-xs text-slate-400 font-normal">(opsional)</span></label>
        <textarea wire:model.blur="catatan_pendaftar" rows="3" placeholder="Informasi tambahan yang ingin disampaikan ke panitia..." class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 focus:bg-white transition resize-none"></textarea>
    </div>

    @include('livewire.frontend.ppdb-form.partials.nav-buttons', ['showPrev' => true, 'showNext' => true])
</div>
