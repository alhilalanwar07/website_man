{{-- Step 1: Pernyataan Calon Murid Baru --}}
<div class="space-y-6">
    {{-- Header --}}
    <div class="text-center">
        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-blue-500/25">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Pernyataan Calon Murid Baru</h1>
        <p class="mt-2 text-sm text-slate-500 max-w-lg mx-auto">Baca dan pahami seluruh isi pernyataan berikut sebelum melanjutkan proses pendaftaran.</p>
    </div>

    {{-- Pernyataan Card --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-slate-900 to-slate-800 px-6 py-4">
            <h2 class="text-white font-bold text-sm tracking-wide uppercase">Surat Pernyataan</h2>
            <p class="text-slate-300 text-xs mt-0.5">MAN 2 Kolaka — Tahun Pelajaran {{ now()->format('Y') }}/{{ now()->format('Y') + 1 }}</p>
        </div>

        <div class="px-6 py-6 space-y-4 text-sm text-slate-700 leading-relaxed">
            <p>Dengan ini saya menyatakan bahwa saya bertanggung jawab atas <strong class="text-slate-900">kebenaran seluruh data</strong> yang disampaikan dalam formulir pendaftaran ini, dan bersedia untuk:</p>

            <ol class="space-y-3 pl-1">
                <li class="flex gap-3">
                    <span class="flex-shrink-0 w-7 h-7 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center text-xs font-bold">1</span>
                    <span><strong class="text-slate-900">Dicabut haknya</strong> sebagai peserta tes calon murid baru di MAN 2 Kolaka jika memberikan data palsu atau tidak sesuai dengan dokumen resmi.</span>
                </li>
                <li class="flex gap-3">
                    <span class="flex-shrink-0 w-7 h-7 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center text-xs font-bold">2</span>
                    <span><strong class="text-slate-900">Belajar sungguh-sungguh</strong> selama mengikuti pendidikan di MAN 2 Kolaka sampai tamat serta akan mematuhi peraturan dan tata tertib yang ditetapkan oleh madrasah.</span>
                </li>
                <li class="flex gap-3">
                    <span class="flex-shrink-0 w-7 h-7 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center text-xs font-bold">3</span>
                    <span><strong class="text-slate-900">Berakhlak mulia</strong> dimanapun berada serta menjaga nama baik sendiri, keluarga, dan MAN 2 Kolaka.</span>
                </li>
                <li class="flex gap-3">
                    <span class="flex-shrink-0 w-7 h-7 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center text-xs font-bold">4</span>
                    <span><strong class="text-slate-900">Orang Tua / Wali Murid</strong> bersedia bekerja sama dan membantu pihak madrasah dalam mengarahkan calon murid baru untuk rajin dan tekun belajar di rumah dan di sekolah.</span>
                </li>
            </ol>

            <div class="border-t border-slate-100 pt-4 mt-4">
                <p class="text-xs text-slate-500">Dengan mencentang kotak persetujuan di bawah ini, Anda dan orang tua/wali menyatakan telah membaca, memahami, dan menyetujui seluruh isi pernyataan di atas.</p>
            </div>
        </div>
    </div>

    {{-- Additional Info --}}
    <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5">
        <div class="flex gap-3">
            <div class="flex-shrink-0 w-9 h-9 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-blue-900">Sebelum melanjutkan, pastikan Anda sudah menyiapkan:</p>
                <ul class="mt-2 space-y-1 text-xs text-blue-800">
                    <li>• Data diri siswa sesuai ijazah/akta kelahiran</li>
                    <li>• Data orang tua (ayah & ibu)</li>
                    <li>• File scan: KK, Akta, Rapor, Pas Foto, SKL (jika ada)</li>
                    <li>• Ukuran masing-masing file maksimal 4 MB</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Agreement Checkbox --}}
    <label class="flex items-start gap-4 bg-white rounded-2xl border-2 border-slate-200 p-5 cursor-pointer transition-all hover:border-blue-300 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50/50">
        <input wire:model="persetujuan_data" type="checkbox" class="mt-0.5 h-5 w-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500 focus:ring-offset-0">
        <div>
            <span class="text-sm font-semibold text-slate-900">Saya menyetujui seluruh pernyataan di atas</span>
            <p class="text-xs text-slate-500 mt-0.5">Termasuk kesediaan orang tua/wali untuk bekerja sama dengan pihak sekolah.</p>
        </div>
    </label>
    @error('persetujuan_data') <p class="text-xs text-red-500 -mt-3">{{ $message }}</p> @enderror

    {{-- Navigation --}}
    <div class="flex justify-end pt-2">
        <button type="button" wire:click="nextStep" class="inline-flex items-center gap-2 px-8 py-3.5 bg-blue-600 text-white text-sm font-bold rounded-xl hover:bg-blue-700 focus:ring-4 focus:ring-blue-500/20 transition-all shadow-lg shadow-blue-500/25 disabled:opacity-50" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="nextStep">Lanjutkan</span>
            <span wire:loading wire:target="nextStep">Memproses...</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </button>
    </div>
</div>
